import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { chromium } from "playwright";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import type { NormalizedRecord } from "@/lib/scrapers/types";

export const runtime = "nodejs";

const bodySchema = z.object({
  query: z.string().min(1).max(300),
  limit: z.number().int().positive().optional(),
});

const OVERALL_TIMEOUT_MS = 45_000;
const MAX_SCROLL_ATTEMPTS = 8;

interface RawCard {
  name: string | null;
  href: string | null;
  innerText: string;
}

/**
 * Google Maps' feed card only exposes name/rating/category/address/open-status
 * in its collapsed list view — phone and website live behind a per-listing
 * click-through we deliberately skip here for speed/reliability across many
 * results. `innerText` lines (verified against the live site): name, name
 * (duplicated), rating, "<category> · [accessibility note ·] <address>",
 * optional tagline, then an open/closed status line.
 */
function parseCard(card: RawCard): NormalizedRecord {
  const lines = card.innerText
    .split("\n")
    .map((l) => l.trim())
    .filter(Boolean);

  const companyName = card.name || lines[0] || undefined;
  const rating = lines.find((l) => /^\d(\.\d)?$/.test(l));
  const detailLine = lines.find((l) => l.includes("·"));
  const segments = detailLine ? detailLine.split("·").map((s) => s.trim()).filter(Boolean) : [];
  const category = segments[0];
  const address = segments.length > 1 ? segments[segments.length - 1] : undefined;
  const reviewMatch = card.innerText.match(/\((\d[\d,]*)\)/);

  return {
    companyName,
    address,
    category,
    rating: rating ? Number(rating) : undefined,
    reviewCount: reviewMatch ? Number(reviewMatch[1].replace(/,/g, "")) : undefined,
    mapsUrl: card.href || undefined,
  };
}

async function scrapeGoogleMaps(query: string, limit: number): Promise<NormalizedRecord[]> {
  const browser = await chromium.launch({ headless: true });
  try {
    const page = await browser.newPage({
      userAgent:
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36",
    });

    // Force English UI text so parsing (category/address separators, "Open"/
    // "Closed" status) doesn't depend on the caller's locale.
    await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}?hl=en`, {
      waitUntil: "domcontentloaded",
      timeout: 30_000,
    });

    const feed = page.locator('div[role="feed"]');
    if ((await feed.count()) === 0) {
      // No results feed at all (zero results, or Google served a single-place
      // page directly) — return whatever, possibly zero, articles exist.
      await page.waitForTimeout(2000);
    }

    let count = await page.locator('div[role="feed"] div[role="article"]').count();
    for (let i = 0; i < MAX_SCROLL_ATTEMPTS && count < limit; i++) {
      await page.evaluate(() => {
        const el = document.querySelector('div[role="feed"]');
        if (el) el.scrollTop = el.scrollHeight;
      });
      await page.waitForTimeout(1200);
      const next = await page.locator('div[role="feed"] div[role="article"]').count();
      if (next === count) break; // No new cards loaded — reached the end of results.
      count = next;
    }

    const cards: RawCard[] = await page
      .locator('div[role="feed"] div[role="article"]')
      .evaluateAll((els) =>
        els.map((el) => ({
          name: el.querySelector("a.hfpxzc")?.getAttribute("aria-label") ?? null,
          href: el.querySelector("a.hfpxzc")?.getAttribute("href") ?? null,
          innerText: (el as HTMLElement).innerText,
        })),
      );

    return cards.slice(0, limit).map(parseCard);
  } finally {
    await browser.close();
  }
}

function withOverallTimeout<T>(promise: Promise<T>, ms: number): Promise<T> {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => reject(new Error("Google Maps scrape timed out")), ms);
    promise.then(
      (v) => {
        clearTimeout(timer);
        resolve(v);
      },
      (e) => {
        clearTimeout(timer);
        reject(e);
      },
    );
  });
}

// Headless-browser scrape of public Google Maps search results (business
// name, rating, category, address, maps URL). Deliberately does not click
// into each listing for phone/website — see parseCard's doc comment.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = bodySchema.parse(await req.json());
    const limit = Math.min(body.limit ?? 15, 40);

    let results: NormalizedRecord[];
    try {
      results = await withOverallTimeout(scrapeGoogleMaps(body.query, limit), OVERALL_TIMEOUT_MS);
    } catch (err: unknown) {
      const reason = err instanceof Error ? err.message : "Unknown error";
      return fail(`Google Maps scrape failed: ${reason}`, 502);
    }

    await saveScrapedRecords(user.id, "MAP", body.query, results);

    return ok({ query: body.query, source: "MAP", count: results.length, results });
  });
}
