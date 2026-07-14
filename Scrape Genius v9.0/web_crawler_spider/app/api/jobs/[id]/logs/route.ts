import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { AuthError } from "@/lib/auth";
import { backendFetchRaw } from "@/lib/backend-client";

export const runtime = "nodejs";

/** GET /api/jobs/:id/logs — SSE passthrough of the Express job manager's live log stream. */
export async function GET(req: NextRequest, { params }: { params: { id: string } }): Promise<NextResponse> {
  let user;
  try {
    user = await requireAuth(req);
  } catch (err) {
    if (err instanceof AuthError) {
      return NextResponse.json({ success: false, error: err.message }, { status: err.status });
    }
    return NextResponse.json({ success: false, error: "Internal Server Error" }, { status: 500 });
  }

  const upstream = await backendFetchRaw(user, `/v1/jobs/${params.id}/logs`);

  return new NextResponse(upstream.body, {
    status: upstream.status,
    headers: {
      "Content-Type": "text/event-stream",
      "Cache-Control": "no-cache, no-transform",
      Connection: "keep-alive",
    },
  });
}
