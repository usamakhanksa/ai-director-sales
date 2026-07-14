export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";

import { prisma } from "@/lib/prisma";
import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";
import { todayUtcDateOnly } from "@/lib/keys";

// Replaces the old keys.json-backed /api/get_keys: returns this user's
// active Google Custom Search keys that still have headroom today.
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const today = todayUtcDateOnly();

    const apiKeys = await prisma.userSearchKey.findMany({
      where: { userId: user.id, isActive: true },
      include: { usageLogs: { where: { date: today } } },
      orderBy: { id: "asc" },
    });

    const available = apiKeys
      .map((k) => {
        const usedToday = k.usageLogs[0]?.requestCount ?? 0;
        return {
          id: k.id,
          google_api_key: k.googleApiKey,
          search_engine_id: k.searchEngineId,
          daily_limit: k.dailyLimit,
          used_today: usedToday,
          remaining_today: Math.max(0, k.dailyLimit - usedToday),
        };
      })
      .filter((k) => k.remaining_today > 0);

    return ok(available);
  });
}
