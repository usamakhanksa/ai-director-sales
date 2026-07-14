import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { fail, withErrorHandling } from "@/lib/api-response";
import { backendFetch } from "@/lib/backend-client";

export const runtime = "nodejs";

/**
 * POST /api/lead-qualifier
 *   { mode: "classify", text, product? }        — classify a single ad text
 *   { mode: "classify-job", jobId, product?, limit? } — classify a job's unlabeled results
 */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { mode, text, jobId, product, limit } = await req.json();

    if (mode === "classify-job") {
      const result = await backendFetch(user, "/v1/lead-qualifier/classify-job", {
        method: "POST",
        body: { jobId, product, limit },
      });
      return NextResponse.json(result.body, { status: result.status });
    }

    if (mode === "classify" || mode === undefined) {
      if (!text) return fail("text is required", 400);
      const result = await backendFetch(user, "/v1/lead-qualifier/classify", {
        method: "POST",
        body: { text, product },
      });
      return NextResponse.json(result.body, { status: result.status });
    }

    return fail(`Unknown mode "${mode}" — expected "classify" or "classify-job"`, 400);
  });
}
