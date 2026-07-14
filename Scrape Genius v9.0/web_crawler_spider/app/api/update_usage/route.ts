import { NextRequest, NextResponse } from "next/server";

import { prisma } from "@/lib/prisma";
import { requireAuth, AuthError } from "@/lib/auth";
import { updateUsageSchema } from "@/lib/validators";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { incrementKeyUsage, todayUtcDateOnly, UsageLimitError } from "@/lib/keys";

// Replaces the old obfuscated external_usage.json read/sum/rewrite cycle.
// The increment is a single conditional UPDATE guarded by the key's
// daily_limit, so concurrent requests can never push a key's count past its
// limit - unlike the old file, which had no locking at all.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = updateUsageSchema.parse(await req.json());

    const apiKey = await prisma.userSearchKey.findUnique({ where: { id: body.api_key_id } });
    if (!apiKey || apiKey.userId !== user.id) {
      throw new AuthError("API key not found", 404);
    }
    if (!apiKey.isActive) {
      return fail("API key is inactive", 400);
    }

    try {
      await incrementKeyUsage(apiKey.id, apiKey.dailyLimit, body.increment_by);
    } catch (err) {
      if (err instanceof UsageLimitError) {
        return fail(err.message, 429);
      }
      throw err;
    }

    const today = todayUtcDateOnly();
    const updated = await prisma.userSearchUsageLog.findUnique({
      where: { userSearchKeyId_date: { userSearchKeyId: apiKey.id, date: today } },
    });

    return ok({
      api_key_id: apiKey.id,
      date: today.toISOString().split("T")[0],
      request_count: updated?.requestCount ?? 0,
      daily_limit: apiKey.dailyLimit,
    });
  });
}
