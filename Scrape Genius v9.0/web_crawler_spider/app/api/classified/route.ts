import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { fail, withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/**
 * POST /api/classified {site: "haraj"|"generic", keywords, config}
 * Routes to the Express Haraj scraper or the generic classified-site scraper.
 */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { site, keywords, config } = await req.json();

    const path = site === "generic" ? "/v1/classified/generic" : "/v1/classified/haraj";
    if (site !== "generic" && site !== "haraj" && site !== undefined) {
      return fail(`Unknown site "${site}" — expected "haraj" or "generic"`, 400);
    }

    const result = await backendFetch(user, path, { method: "POST", body: { keywords, config } });
    return NextResponse.json(result.body, { status: result.status });
  });
}
