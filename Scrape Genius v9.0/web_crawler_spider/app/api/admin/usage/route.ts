export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";

import { prisma } from "@/lib/prisma";
import { requireAuth, requireAdmin } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";

/** GET /api/admin/usage — admin analytics: user/record totals and a per-source breakdown. */
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    requireAdmin(user);

    const [totalUsers, totalRecords, totalApiKeys, bySource] = await Promise.all([
      prisma.user.count(),
      prisma.scrapedRecord.count(),
      prisma.apiKey.count({ where: { isActive: true } }),
      prisma.scrapedRecord.groupBy({ by: ["source"], _count: { _all: true } }),
    ]);

    return ok({
      totalUsers,
      totalRecords,
      totalActiveApiKeys: totalApiKeys,
      bySource: bySource.map((row) => ({ source: row.source, count: row._count._all })),
    });
  });
}
