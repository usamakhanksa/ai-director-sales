export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";
import crypto from "crypto";

import { prisma } from "@/lib/prisma";
import { requireAuth, requireAdmin } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";

/** GET /api/admin/purchase-codes — list all purchase/license codes (admin only). */
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    requireAdmin(user);

    const codes = await prisma.purchaseCode.findMany({
      include: { user: { select: { name: true, email: true } } },
      orderBy: { id: "desc" },
      take: 500,
    });

    return ok(codes);
  });
}

/** POST /api/admin/purchase-codes {expiresAt?} — generates a new unclaimed license code. */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    requireAdmin(user);

    const body = await req.json().catch(() => ({}));
    const code = crypto.randomBytes(6).toString("hex").toUpperCase();

    const created = await prisma.purchaseCode.create({
      data: { code, expiresAt: body.expiresAt ? new Date(body.expiresAt) : null },
    });

    return ok(created, 201);
  });
}
