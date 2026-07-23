import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const dynamic = "force-dynamic";

interface LinkedInScrapeRequest {
  profileUrl: string;
  sessionCookieValue: string;
}

const LINKEDIN_PROFILE_RE = /^https:\/\/www\.linkedin\.com\/in\/[a-zA-Z0-9-_/]+\/?$/;

// POST /api/social/linkedin {profileUrl, sessionCookieValue} — scrapes a
// single public LinkedIn profile using the caller's own session cookie.
// Proxies to the Express backend's /api/linkedin-profile, bridging identity
// the same way every other social scraper route does (lib/backend-client.ts).
//
// This previously used `validateApiKey()` from lib/api-utils.ts, which only
// works in a browser (`typeof window !== "undefined"`) — since Next.js
// route handlers only ever run server-side, that check always returned
// `valid: false`, so every request to this route unconditionally 401'd.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body: LinkedInScrapeRequest = await req.json();

    if (!body.profileUrl || !body.sessionCookieValue) {
      return fail("Missing required fields: profileUrl and sessionCookieValue", 400);
    }

    if (!LINKEDIN_PROFILE_RE.test(body.profileUrl)) {
      return fail("Invalid LinkedIn profile URL format", 400);
    }

    const result = await backendFetch(user, "/api/linkedin-profile", {
      method: "POST",
      body: { profileUrl: body.profileUrl, sessionCookieValue: body.sessionCookieValue },
    });

    if (!result.ok || !result.body?.success) {
      return fail((result.body as any)?.error ?? "Failed to scrape LinkedIn profile", result.status || 502);
    }

    return ok({
      result: (result.body.data as any)?.result,
      message: result.body.message ?? "Profile scraped successfully",
    });
  });
}

// Handle other HTTP methods
export async function GET() {
  return NextResponse.json(
    { error: "Method not allowed. Use POST to scrape a LinkedIn profile." },
    { status: 405 }
  );
}
