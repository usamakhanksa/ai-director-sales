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
 * Yahoo wraps outbound result links in an `r.search.yahoo.com/...` redirect
 * that embeds the real, URL-encoded destination between `/RU=` and the next
 * `/`. Falls back to the raw href if that marker isn't present.
 */
function decodeYahooHref(href: string): string | undefined {
  if (!href) return undefined;
  const match = href.match(/\/RU=([^/]+)\//);
  if (!match) return href;
  try {
    return decodeURIComponent(match[1]);
  } catch {
    return href;
  }
}

export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = bodySchema.parse(await req.json());
    const limit = Math.min(body.limit ?? 10, 30);

    const url = `https://search.yahoo.com/search?p=${encodeURIComponent(body.query)}`;

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
        return fail(`Yahoo returned an unexpected status (${res.status}) while fetching search results`, 502);
      }
      html = await res.text();
    } catch {
      clearTimeout(timeout);
      return fail("Failed to reach Yahoo — request timed out or network error", 502);
    }
    clearTimeout(timeout);

    const $ = cheerio.load(html);
    const results: NormalizedRecord[] = [];

    $("#web ol.reg > li").each((_, li) => {
      if (results.length >= limit) return;

      const $li = $(li);
      const $container = $li.find(".dd.algo, .algo-sr").first();
      const $scope = $container.length ? $container : $li;

      const companyName = $scope.find("h3.title").first().text().trim();
      const $a = $scope.find(".compTitle a").first();
      const website = decodeYahooHref($a.attr("href") || "");
      const snippet = $scope.find(".compText p, .compText .aAbs").first().text().trim();

      if (companyName || website) {
        results.push({
          companyName: companyName || undefined,
          website: website || undefined,
          snippet: snippet || undefined,
        });
      }
    });

    await saveScrapedRecords(user.id, "YAHOO", body.query, results);

    return ok({ query: body.query, source: "YAHOO", count: results.length, results });
  });
}
