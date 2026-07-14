import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import * as cheerio from "cheerio";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import type { NormalizedRecord } from "@/lib/scrapers/types";

const USER_AGENT =
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36";

const bodySchema = z.object({
  query: z.string().min(1).max(500),
  limit: z.number().int().positive().optional(),
});

/**
 * DuckDuckGo's HTML-only endpoint wraps outbound links in
 * `//duckduckgo.com/l/?uddg=<url-encoded-real-url>&rut=...`. `uddg` is
 * already URL-decoded once we read it via URLSearchParams.
 */
function decodeDuckDuckGoHref(href: string): string | undefined {
  if (!href) return undefined;
  const full = href.startsWith("//") ? `https:${href}` : href;
  try {
    const parsed = new URL(full);
    return parsed.searchParams.get("uddg") || full;
  } catch {
    return full;
  }
}

export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = bodySchema.parse(await req.json());
    const limit = Math.min(body.limit ?? 10, 30);

    const url = `https://html.duckduckgo.com/html/?q=${encodeURIComponent(body.query)}`;

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 10_000);

    let html: string;
    try {
      const res = await fetch(url, {
        headers: { "User-Agent": USER_AGENT },
        signal: controller.signal,
      });
      if (!res.ok) {
        clearTimeout(timeout);
        return fail(`DuckDuckGo returned an unexpected status (${res.status}) while fetching search results`, 502);
      }
      html = await res.text();
    } catch {
      clearTimeout(timeout);
      return fail("Failed to reach DuckDuckGo — request timed out or network error", 502);
    }
    clearTimeout(timeout);

    const $ = cheerio.load(html);
    const results: NormalizedRecord[] = [];

    $("a.result__a").each((_, a) => {
      if (results.length >= limit) return;

      const $a = $(a);
      const companyName = $a.text().trim();
      const website = decodeDuckDuckGoHref($a.attr("href") || "");
      const snippet = $a
        .closest(".result, .result__body")
        .find(".result__snippet")
        .first()
        .text()
        .trim();

      if (companyName || website) {
        results.push({
          companyName: companyName || undefined,
          website: website || undefined,
          snippet: snippet || undefined,
        });
      }
    });

    await saveScrapedRecords(user.id, "DUCKDUCKGO", body.query, results);

    return ok({ query: body.query, source: "DUCKDUCKGO", count: results.length, results });
  });
}
