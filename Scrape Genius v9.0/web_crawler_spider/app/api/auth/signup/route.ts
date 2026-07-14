import { NextRequest, NextResponse } from "next/server";
import bcrypt from "bcryptjs";

import { prisma } from "@/lib/prisma";
import { signupSchema } from "@/lib/validators";
import { ok, fail, withErrorHandling } from "@/lib/api-response";

export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const body = signupSchema.parse(await req.json());

    const existing = await prisma.user.findUnique({ where: { email: body.email } });
    if (existing) {
      return fail("An account with this email already exists", 409);
    }

    const passwordHash = await bcrypt.hash(body.password, 10);

    // No email-verification flow was requested for this schema (no verification
    // code column), so accounts are marked verified immediately on signup.
    const user = await prisma.user.create({
      data: {
        name: body.name,
        email: body.email,
        passwordHash,
        isVerified: true,
      },
      select: { id: true, name: true, email: true, role: true, isVerified: true, createdAt: true },
    });

    return ok(user, 201);
  });
}
