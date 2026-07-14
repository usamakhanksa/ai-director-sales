import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/** POST /api/social/twitter {keywords, config} — starts a Twitter/X keyword scrape job. */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = await req.json();

    const result = await backendFetch(user, "/v1/social/twitter", { method: "POST", body });
    return NextResponse.json(result.body, { status: result.status });
  });
}
