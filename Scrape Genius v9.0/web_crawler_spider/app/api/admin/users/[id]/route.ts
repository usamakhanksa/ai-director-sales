export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { prisma } from "@/lib/prisma";
import { requireAuth, requireAdmin } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";

const patchSchema = z.object({
  role: z.enum(["ADMIN", "USER"]).optional(),
  isVerified: z.boolean().optional(),
});

/** PATCH /api/admin/users/:id {role?, isVerified?} — admin-only user management. */
export async function PATCH(req: NextRequest, { params }: { params: { id: string } }): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const admin = await requireAuth(req);
    requireAdmin(admin);

    const body = patchSchema.parse(await req.json());
    if (Object.keys(body).length === 0) {
      return fail("No valid fields to update", 400);
    }

    const targetId = Number(params.id);
    const existing = await prisma.user.findUnique({ where: { id: targetId } });
    if (!existing) return fail("User not found", 404);

    const updated = await prisma.user.update({
      where: { id: targetId },
      data: body,
      select: { id: true, name: true, email: true, role: true, isVerified: true },
    });

    return ok(updated);
  });
}
