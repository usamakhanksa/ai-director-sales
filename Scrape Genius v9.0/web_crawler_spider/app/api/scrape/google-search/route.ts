import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { pickAvailableKey, incrementKeyUsage, UsageLimitError } from "@/lib/keys";
import type { NormalizedRecord } from "@/lib/scrapers/types";

const USER_AGENT =
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36";

const bodySchema = z.object({
  query: z.string().min(1).max(500),
  limit: z.number().int().positive().optional(),
});

/**
 * Real Google Custom Search API call using the requesting user's own key
 * (picked/charged via lib/keys.ts). Google's API caps `num` at 10 per call,
 * so `limit` beyond that only affects how many of the (max 10) items we keep.
 */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = bodySchema.parse(await req.json());
    const limit = Math.min(body.limit ?? 10, 30);

    const apiKey = await pickAvailableKey(user.id);
    if (!apiKey) {
      return fail(
        "No active Google Custom Search key with quota left today — add one or wait for tomorrow's reset",
        400,
      );
    }

    const url =
      `https://www.googleapis.com/customsearch/v1?key=${encodeURIComponent(apiKey.googleApiKey)}` +
      `&cx=${encodeURIComponent(apiKey.searchEngineId)}` +
      `&q=${encodeURIComponent(body.query)}` +
      `&num=${Math.min(limit, 10)}`;

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 10_000);

    let res: Response;
    try {
      res = await fetch(url, {
        headers: { "User-Agent": USER_AGENT },
        signal: controller.signal,
      });
    } catch {
      clearTimeout(timeout);
      return fail("Failed to reach Google Custom Search API — request timed out or network error", 502);
    }
    clearTimeout(timeout);

    const json: any = await res.json().catch(() => null);

    if (!res.ok) {
      const googleMessage = json?.error?.message as string | undefined;
      return fail(
        "Google Custom Search rejected this key — replace the placeholder key in your account with a real " +
          `Google Custom Search API key + Search Engine ID${googleMessage ? ` (Google said: "${googleMessage}")` : ""}`,
        res.status >= 400 && res.status < 500 ? res.status : 502,
      );
    }

    try {
      await incrementKeyUsage(apiKey.id, apiKey.dailyLimit, 1);
    } catch (err) {
      if (err instanceof UsageLimitError) {
        return fail(err.message, 429);
      }
      throw err;
    }

    const items: any[] = Array.isArray(json?.items) ? json.items : [];
    const results: NormalizedRecord[] = items.slice(0, limit).map((item) => ({
      companyName: item.title,
      website: item.link,
      snippet: item.snippet,
    }));

    await saveScrapedRecords(user.id, "GOOGLE", body.query, results);

    return ok({ query: body.query, source: "GOOGLE", count: results.length, results });
  });
}
