import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";
import { saveScrapedRecords } from "@/lib/records";

// This is a GET route that reads the Authorization header (via requireAuth)
// and query params — it must never be statically prerendered at build time.
export const dynamic = "force-dynamic";

interface GoogleNewsItem {
  title: string;
  link: string;
  pubDate: string | null;
  description: string;
  source: string;
  guid: string;
}

interface GoogleNewsData {
  items: GoogleNewsItem[];
  total: number;
  query: string;
  language: string;
  geographicLocation: string;
  url: string;
}

// Proxies to the Express backend's public Google News RSS reader
// (backend/src/routes/google-news.routes.js) — no API key required, since
// Google News RSS is a public feed.
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);

    const { searchParams } = new URL(req.url);
    const q = searchParams.get("q");
    if (!q) {
      return fail("Query parameter 'q' is required", 400);
    }

    const result = await backendFetch<GoogleNewsData>(user, "/v1/scrape/google-news", {
      query: {
        q,
        hl: searchParams.get("hl") ?? undefined,
        gl: searchParams.get("gl") ?? undefined,
        ceid: searchParams.get("ceid") ?? undefined,
        dateRestrict: searchParams.get("dateRestrict") ?? undefined,
        limit: searchParams.get("limit") ?? undefined,
        offset: searchParams.get("offset") ?? undefined,
      },
    });

    if (!result.ok || !result.body?.success || !result.body.data) {
      return fail(result.body?.error ?? "Google News RSS request failed", result.status || 502);
    }

    const data = result.body.data;

    await saveScrapedRecords(
      user.id,
      "NEWS_RSS",
      q,
      data.items.map((item) => ({
        title: item.title,
        link: item.link,
        pubDate: item.pubDate,
        source: item.source,
      })),
      undefined,
    );

    return ok(data);
  });
}
