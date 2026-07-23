import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const dynamic = "force-dynamic";

interface LinkedInSearchRequest {
  keyword: string;
  sessionCookieValue: string;
  limit?: number;
}

interface LinkedInSearchData {
  keyword: string;
  jobId: number;
  count: number;
  results: Array<{
    fullName: string;
    title: string;
    location: string;
    profileUrl: string;
    email?: string;
    phone?: string;
    description?: string;
  }>;
}

// POST /api/social/linkedin/search {keyword, sessionCookieValue, limit?} —
// keyword-searches LinkedIn using the caller's own session cookie. Proxies
// to the Express backend's /api/linkedin-search.
//
// This previously used `validateApiKey()` from lib/api-utils.ts, a
// browser-only check (`typeof window !== "undefined"`) that always returns
// `valid: false` when run server-side — so every call to this route
// unconditionally 401'd. It also had a duplicate, dead second copy of this
// module concatenated below the real implementation (with colliding
// top-level exports) that returned hardcoded fake profiles instead of
// calling the backend at all; that has been removed.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body: LinkedInSearchRequest = await req.json();

    if (!body.keyword || !body.sessionCookieValue) {
      return fail("Missing required fields: keyword and sessionCookieValue", 400);
    }

    const result = await backendFetch<LinkedInSearchData>(user, "/api/linkedin-search", {
      method: "POST",
      body: { keyword: body.keyword, sessionCookieValue: body.sessionCookieValue, limit: body.limit || 10 },
    });

    if (!result.ok || !result.body?.success || !result.body.data) {
      return fail(result.body?.error ?? "Failed to search LinkedIn profiles", result.status || 502);
    }

    const data = result.body.data;
    return ok({
      results: data.results,
      count: data.count,
      message: result.body.message ?? "Search completed successfully",
    });
  });
}

export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { searchParams } = new URL(req.url);
    const keyword = searchParams.get("keyword");
    const sessionCookieValue = searchParams.get("sessionCookieValue");

    if (!keyword || !sessionCookieValue) {
      return fail("Missing required query parameters: keyword and sessionCookieValue", 400);
    }

    const result = await backendFetch<LinkedInSearchData>(user, "/api/linkedin-search", {
      query: {
        keyword,
        sessionCookieValue,
        limit: searchParams.get("limit") ?? undefined,
      },
    });

    if (!result.ok || !result.body?.success || !result.body.data) {
      return fail(result.body?.error ?? "Failed to search LinkedIn profiles", result.status || 502);
    }

    const data = result.body.data;
    return ok({
      results: data.results,
      count: data.count,
      message: result.body.message ?? "Search completed successfully",
    });
  });
}
