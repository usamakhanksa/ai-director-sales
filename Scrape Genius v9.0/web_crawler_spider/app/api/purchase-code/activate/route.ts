import { NextRequest, NextResponse } from "next/server";

import { prisma } from "@/lib/prisma";
import { requireAuth } from "@/lib/auth";
import { purchaseCodeActivateSchema } from "@/lib/validators";
import { ok, fail, withErrorHandling } from "@/lib/api-response";

// Replaces the vendor-hosted /restricted/purchasecodeactivation/:code check
// with a local one: codes are pre-seeded (e.g. "12345") unassigned, and get
// claimed by whichever authenticated user redeems them first.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = purchaseCodeActivateSchema.parse(await req.json());

    const purchaseCode = await prisma.purchaseCode.findUnique({ where: { code: body.code } });
    if (!purchaseCode) {
      return fail("Invalid purchase code", 404);
    }

    if (purchaseCode.expiresAt && purchaseCode.expiresAt < new Date()) {
      return fail("This purchase code has expired", 410);
    }

    if (purchaseCode.isActive) {
      if (purchaseCode.userId === user.id) {
        return ok({ message: "Purchase code already active on this account", purchaseCode });
      }
      return fail("This purchase code has already been claimed by another account", 409);
    }

    const activated = await prisma.purchaseCode.update({
      where: { id: purchaseCode.id },
      data: { userId: user.id, isActive: true, activatedAt: new Date() },
    });

    return ok({ message: "Purchase code activated", purchaseCode: activated });
  });
}
