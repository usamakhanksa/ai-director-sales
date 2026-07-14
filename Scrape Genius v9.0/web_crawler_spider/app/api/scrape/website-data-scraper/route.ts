import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { extractFromHtml } from "@/lib/scrapers/extract";

const USER_AGENT =
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36";

const websiteDataScraperSchema = z.object({
  urls: z.array(z.string().min(1)).min(1).max(20),
});

/** Prepends https:// to bare domains/paths so fetch() doesn't reject them. */
function normalizeUrl(raw: string): string {
  const trimmed = raw.trim();
  if (/^https?:\/\//i.test(trimmed)) return trimmed;
  return `https://${trimmed}`;
}

async function fetchHtml(url: string): Promise<string> {
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

// Batch website scraper: given a list of URLs, fetches each page and pulls
// out contact/meta data via the shared extractFromHtml toolkit. Bad URLs are
// skipped (and reported in `failed`) rather than aborting the whole batch.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = websiteDataScraperSchema.parse(await req.json());

    const results: ReturnType<typeof extractFromHtml>[] = [];
    const failed: { url: string; reason: string }[] = [];

    for (const rawUrl of body.urls) {
      const url = normalizeUrl(rawUrl);
      try {
        const html = await fetchHtml(url);
        results.push(extractFromHtml(html, url));
      } catch (err: unknown) {
        const reason = err instanceof Error ? err.message : "Unknown error";
        failed.push({ url, reason });
      }
    }

    await saveScrapedRecords(user.id, "WEBSITE", body.urls.join(", "), results);

    return ok({
      query: body.urls.join(", "),
      source: "WEBSITE",
      count: results.length,
      results,
      failed,
    });
  });
}
