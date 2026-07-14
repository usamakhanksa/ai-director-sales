import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/** GET /api/jobs?status=&limit=&offset= — proxies to the Express job manager, scoped to the authenticated user. */
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { searchParams } = new URL(req.url);

    const result = await backendFetch(user, "/v1/jobs", {
      query: {
        status: searchParams.get("status") ?? undefined,
        limit: searchParams.get("limit") ?? undefined,
        offset: searchParams.get("offset") ?? undefined,
      },
    });

    return NextResponse.json(result.body, { status: result.status });
  });
}

/** POST /api/jobs {module, keywords, config} — creates a new scraping job. */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = await req.json();

    const result = await backendFetch(user, "/v1/jobs", { method: "POST", body });
    return NextResponse.json(result.body, { status: result.status });
  });
}
