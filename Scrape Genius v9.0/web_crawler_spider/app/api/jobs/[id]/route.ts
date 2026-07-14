import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/** GET /api/jobs/:id — job status, progress, and per-table result counts. */
export async function GET(req: NextRequest, { params }: { params: { id: string } }): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const result = await backendFetch(user, `/v1/jobs/${params.id}`);
    return NextResponse.json(result.body, { status: result.status });
  });
}

/** DELETE /api/jobs/:id — cancel a queued/running job. */
export async function DELETE(req: NextRequest, { params }: { params: { id: string } }): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const result = await backendFetch(user, `/v1/jobs/${params.id}`, { method: "DELETE" });
    return NextResponse.json(result.body, { status: result.status });
  });
}
