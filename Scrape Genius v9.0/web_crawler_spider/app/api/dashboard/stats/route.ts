export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";

import { prisma } from "@/lib/prisma";
import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";

// Replaces the client-side localStorage `totalRecords` array with a
// database-backed source of truth, in the same {title, records} shape the
// dashboard context already expects.
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);

    const stats = await prisma.dashboardStat.findMany({
      where: { userId: user.id },
      orderBy: { statType: "asc" },
    });

    const totalRecords = stats.map((s) => ({
      title: s.statType,
      records: s.recordCount,
    }));

    return ok(totalRecords);
  });
}
