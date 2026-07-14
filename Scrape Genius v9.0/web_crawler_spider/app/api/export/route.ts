import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/** GET /api/export?limit=&offset= — export history for the authenticated user. */
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { searchParams } = new URL(req.url);

    const result = await backendFetch(user, "/v1/export", {
      query: {
        limit: searchParams.get("limit") ?? undefined,
        offset: searchParams.get("offset") ?? undefined,
      },
    });

    return NextResponse.json(result.body, { status: result.status });
  });
}

/** POST /api/export {jobId, format} — generates an export file (XLSX/CSV/HTML/TXT) for a job's results. */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { jobId, format } = await req.json();

    const result = await backendFetch(user, `/v1/export/${jobId}`, { method: "POST", body: { format } });
    return NextResponse.json(result.body, { status: result.status });
  });
}
