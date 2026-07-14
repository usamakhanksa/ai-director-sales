import { NextRequest, NextResponse } from "next/server";
import bcrypt from "bcryptjs";

import { prisma } from "@/lib/prisma";
import { loginSchema } from "@/lib/validators";
import { signToken } from "@/lib/jwt";
import { ok, fail, withErrorHandling } from "@/lib/api-response";

export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const body = loginSchema.parse(await req.json());

    const user = await prisma.user.findUnique({ where: { email: body.email } });
    if (!user) {
      return fail("Invalid email or password", 401);
    }

    const matches = await bcrypt.compare(body.password, user.passwordHash);
    if (!matches) {
      return fail("Invalid email or password", 401);
    }

    const token = signToken({ sub: user.id, email: user.email, role: user.role });

    return ok({
      token,
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        isVerified: user.isVerified,
      },
    });
  });
}
