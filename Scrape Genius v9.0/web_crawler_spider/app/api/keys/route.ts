export const dynamic = "force-dynamic";
import { NextRequest } from "next/server";
import { z } from "zod";

import { prisma } from "@/lib/prisma";
import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { todayUtcDateOnly } from "@/lib/keys";

const createSchema = z.object({
  googleApiKey: z.string().trim().min(10, "Google API key looks too short"),
  searchEngineId: z.string().trim().min(4, "Search engine ID (cx) looks too short"),
  dailyLimit: z.coerce.number().int().min(1).max(100000).default(100),
});

function mask(key: string) {
  return key.length <= 8 ? "••••" : `${key.slice(0, 4)}••••${key.slice(-4)}`;
}

// GET: list this user's Google Custom Search keys with today's usage.
export async function GET(req: NextRequest) {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const today = todayUtcDateOnly();

    const keys = await prisma.userSearchKey.findMany({
      where: { userId: user.id },
      include: { usageLogs: { where: { date: today } } },
      orderBy: { id: "asc" },
    });

    const data = keys.map((k) => ({
      id: k.id,
      googleApiKey: mask(k.googleApiKey),
      searchEngineId: k.searchEngineId,
      dailyLimit: k.dailyLimit,
      usedToday: k.usageLogs[0]?.requestCount ?? 0,
      isActive: k.isActive,
      createdAt: k.createdAt,
    }));

    return ok(data);
  });
}

// POST: add a new Google Custom Search key for this user.
export async function POST(req: NextRequest) {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = createSchema.parse(await req.json());

    const created = await prisma.userSearchKey.create({
      data: {
        userId: user.id,
        googleApiKey: body.googleApiKey,
        searchEngineId: body.searchEngineId,
        dailyLimit: body.dailyLimit,
      },
    });

    return ok({ id: created.id, googleApiKey: mask(created.googleApiKey), searchEngineId: created.searchEngineId, dailyLimit: created.dailyLimit, isActive: created.isActive }, 201);
  });
}
