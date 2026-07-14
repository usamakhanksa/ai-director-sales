import { NextRequest, NextResponse } from "next/server";

import { requireAuth, AuthError } from "@/lib/auth";
import { backendFetchRaw } from "@/lib/backend-client";

export const runtime = "nodejs";

/** GET /api/export/:id/download — streams the generated export file back to the browser. */
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

  const upstream = await backendFetchRaw(user, `/v1/export/${params.id}/download`);

  const headers = new Headers();
  const contentType = upstream.headers.get("content-type");
  const contentDisposition = upstream.headers.get("content-disposition");
  if (contentType) headers.set("Content-Type", contentType);
  if (contentDisposition) headers.set("Content-Disposition", contentDisposition);

  return new NextResponse(upstream.body, { status: upstream.status, headers });
}
