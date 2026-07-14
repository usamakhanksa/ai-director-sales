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
 * Bing wraps outbound result links in a `bing.com/ck/a?...&u=a1<base64url>&...`
 * redirect. The real URL is base64url-encoded after a 2-char "a1" prefix
 * inside the `u` query param. Falls back to the raw href when it isn't
 * wrapped (Bing doesn't always redirect every result).
 */
function decodeBingHref(href: string): string | undefined {
  if (!href) return undefined;
  const match = href.match(/[?&]u=a1([^&]+)/);
  if (!match) return href;

  let b64 = decodeURIComponent(match[1]).replace(/-/g, "+").replace(/_/g, "/");
  while (b64.length % 4 !== 0) b64 += "=";

  try {
    const decoded = Buffer.from(b64, "base64").toString("utf-8");
    return decoded || href;
  } catch {
    return href;
  }
}

export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = bodySchema.parse(await req.json());
    const limit = Math.min(body.limit ?? 10, 30);

    const url = `https://www.bing.com/search?q=${encodeURIComponent(body.query)}&count=${limit}`;

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
        return fail(`Bing returned an unexpected status (${res.status}) while fetching search results`, 502);
      }
      html = await res.text();
    } catch {
      clearTimeout(timeout);
      return fail("Failed to reach Bing — request timed out or network error", 502);
    }
    clearTimeout(timeout);

    const $ = cheerio.load(html);
    const results: NormalizedRecord[] = [];

    $("li.b_algo").each((_, el) => {
      if (results.length >= limit) return;

      const $el = $(el);
      const $a = $el.find("h2 a").first();
      const companyName = $a.text().trim();
      const website = decodeBingHref($a.attr("href") || "");
      const snippet = $el.find(".b_caption p").first().text().trim();

      if (companyName || website) {
        results.push({
          companyName: companyName || undefined,
          website: website || undefined,
          snippet: snippet || undefined,
        });
      }
    });

    await saveScrapedRecords(user.id, "BING", body.query, results);

    return ok({ query: body.query, source: "BING", count: results.length, results });
  });
}
