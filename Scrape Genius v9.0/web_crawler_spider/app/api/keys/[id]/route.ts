export const dynamic = "force-dynamic";
import { NextRequest } from "next/server";
import { z } from "zod";

import { prisma } from "@/lib/prisma";
import { requireAuth, AuthError } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";

const updateSchema = z.object({
  isActive: z.boolean().optional(),
  dailyLimit: z.coerce.number().int().min(1).max(100000).optional(),
});

async function loadOwnedKey(userId: number, idParam: string) {
  const id = Number(idParam);
  const key = await prisma.userSearchKey.findUnique({ where: { id } });
  if (!key || key.userId !== userId) {
    throw new AuthError("API key not found", 404);
  }
  return key;
}

export async function PATCH(req: NextRequest, { params }: { params: { id: string } }) {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const existing = await loadOwnedKey(user.id, params.id);
    const body = updateSchema.parse(await req.json());

    const updated = await prisma.userSearchKey.update({
      where: { id: existing.id },
      data: body,
    });

    return ok({ id: updated.id, isActive: updated.isActive, dailyLimit: updated.dailyLimit });
  });
}

export async function DELETE(req: NextRequest, { params }: { params: { id: string } }) {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const existing = await loadOwnedKey(user.id, params.id);

    await prisma.userSearchKey.delete({ where: { id: existing.id } });

    return ok({ deleted: true });
  });
}
