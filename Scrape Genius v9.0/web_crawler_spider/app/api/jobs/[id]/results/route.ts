import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/** GET /api/jobs/:id/results?limit=&offset= — unified result rows for any module. */
export async function GET(req: NextRequest, { params }: { params: { id: string } }): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { searchParams } = new URL(req.url);

    const result = await backendFetch(user, `/v1/jobs/${params.id}/results`, {
      query: {
        limit: searchParams.get("limit") ?? undefined,
        offset: searchParams.get("offset") ?? undefined,
        all: searchParams.get("all") ?? undefined,
      },
    });

    return NextResponse.json(result.body, { status: result.status });
  });
}
