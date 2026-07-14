import { NextRequest } from "next/server";
import { prisma } from "./prisma";
import { verifyToken } from "./jwt";
import { Role, type User } from "@prisma/client";

export class AuthError extends Error {
  status: number;
  constructor(message: string, status = 401) {
    super(message);
    this.status = status;
  }
}

/** Reads and validates the Bearer token, returning the authenticated user or throwing AuthError. */
export async function requireAuth(req: NextRequest): Promise<User> {
  const header = req.headers.get("authorization") || "";
  const [scheme, token] = header.split(" ");

  if (scheme !== "Bearer" || !token) {
    throw new AuthError("Missing or invalid Authorization header", 401);
  }

  let payload;
  try {
    payload = verifyToken(token);
  } catch {
    throw new AuthError("Invalid or expired token", 401);
  }

  const user = await prisma.user.findUnique({ where: { id: payload.sub } });
  if (!user) {
    throw new AuthError("User not found", 401);
  }

  return user;
}

export function requireAdmin(user: User): void {
  if (user.role !== Role.ADMIN) {
    throw new AuthError("Admin access required", 403);
  }
}
