import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import * as cheerio from "cheerio";

import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { extractFromHtml } from "@/lib/scrapers/extract";

const USER_AGENT =
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36";

const websiteDataCenterSchema = z.object({
  keyword: z.string().min(1).max(200),
  country: z.string().max(100).optional(),
  limit: z.number().int().min(1).optional().default(10),
});

function normalizeUrl(raw: string): string {
  const trimmed = raw.trim();
  if (/^https?:\/\//i.test(trimmed)) return trimmed;
  return `https://${trimmed}`;
}

async function fetchWithTimeout(url: string): Promise<string> {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 10_000);
  try {
    const res = await fetch(url, {
      headers: { "User-Agent": USER_AGENT },
      signal: controller.signal,
    });
    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }
    return await res.text();
  } finally {
    clearTimeout(timeout);
  }
}

/**
 * Self-contained DuckDuckGo HTML search — deliberately not imported from a
 * dedicated duckduckgo-search route since that route is being built
 * concurrently by another agent and may not exist yet.
 */
async function searchDuckDuckGo(query: string, limit: number): Promise<string[]> {
  const searchUrl = `https://html.duckduckgo.com/html/?q=${encodeURIComponent(query)}`;
  const html = await fetchWithTimeout(searchUrl);
  const $ = cheerio.load(html);

  const urls: string[] = [];
  $("a.result__a").each((_, el) => {
    if (urls.length >= limit) return;
    const href = $(el).attr("href");
    if (!href) return;
    try {
      const full = href.startsWith("//") ? `https:${href}` : href;
      const parsed = new URL(full);
      const uddg = parsed.searchParams.get("uddg");
      if (uddg) {
        urls.push(decodeURIComponent(uddg));
      } else if (/^https?:\/\//i.test(href)) {
        urls.push(href);
      }
    } catch {
      // Skip malformed result links.
    }
  });

  return urls.slice(0, limit);
}

// Keyword + country lead search: runs a DuckDuckGo HTML search, then fetches
// and extracts contact/meta data from each top result. Per-URL failures are
// skipped silently (the batch still returns whatever succeeded).
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = websiteDataCenterSchema.parse(await req.json());

    const query = body.country ? `${body.keyword} ${body.country}` : body.keyword;
    const urls = await searchDuckDuckGo(query, body.limit);

    const results: ReturnType<typeof extractFromHtml>[] = [];
    for (const url of urls) {
      try {
        const normalized = normalizeUrl(url);
        const html = await fetchWithTimeout(normalized);
        results.push(extractFromHtml(html, normalized));
      } catch {
        // Skip pages that fail to fetch/parse; keep the rest of the batch.
      }
    }

    await saveScrapedRecords(user.id, "WEBSITE", body.keyword, results, "Website Data Center Records Scraped");

    return ok({
      query: body.keyword,
      source: "WEBSITE",
      count: results.length,
      results,
    });
  });
}
