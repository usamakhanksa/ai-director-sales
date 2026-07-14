export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";

import { prisma } from "@/lib/prisma";
import { requireAuth, requireAdmin } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";

/** GET /api/admin/users — list users for the admin panel (this app's own Prisma users, not the Express backend's). */
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    requireAdmin(user);

    const users = await prisma.user.findMany({
      select: { id: true, name: true, email: true, role: true, isVerified: true, createdAt: true },
      orderBy: { id: "desc" },
      take: 500,
    });

    return ok(users);
  });
}
