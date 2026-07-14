import { prisma } from "@/lib/prisma";
import type { UserSearchKey } from "@prisma/client";

export function todayUtcDateOnly(): Date {
  const now = new Date();
  return new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate()));
}

export class UsageLimitError extends Error {
  constructor() {
    super("Daily usage limit reached for this API key");
  }
}

/**
 * Atomically increments today's usage for an API key, guarded by its
 * daily_limit in the same conditional UPDATE used by POST /api/update_usage.
 * Shared so scraper routes that call Google directly can charge usage
 * in-process instead of looping back through the API over HTTP.
 */
export async function incrementKeyUsage(userSearchKeyId: number, dailyLimit: number, incrementBy = 1) {
  const today = todayUtcDateOnly();

  await prisma.userSearchUsageLog.upsert({
    where: { userSearchKeyId_date: { userSearchKeyId, date: today } },
    update: {},
    create: { userSearchKeyId, date: today, requestCount: 0 },
  });

  const affectedRows = await prisma.$executeRaw`
    UPDATE user_search_usage_logs
    SET request_count = request_count + ${incrementBy}
    WHERE user_search_key_id = ${userSearchKeyId}
      AND date = ${today}
      AND request_count + ${incrementBy} <= ${dailyLimit}
  `;

  if (affectedRows === 0) {
    throw new UsageLimitError();
  }
}

/** Returns this user's active API keys that still have quota left today, cheapest-used first. */
export async function pickAvailableKey(userId: number): Promise<UserSearchKey | null> {
  const today = todayUtcDateOnly();
  const keys = await prisma.userSearchKey.findMany({
    where: { userId, isActive: true },
    include: { usageLogs: { where: { date: today } } },
    orderBy: { id: "asc" },
  });

  const withHeadroom = keys
    .map((k) => ({ key: k, usedToday: k.usageLogs[0]?.requestCount ?? 0 }))
    .filter(({ key, usedToday }) => usedToday < key.dailyLimit)
    .sort((a, b) => a.usedToday - b.usedToday);

  return withHeadroom[0]?.key ?? null;
}
