import { PrismaClient } from "@prisma/client";

// Reuse a single PrismaClient across hot reloads in dev (Next.js dev server
// re-evaluates modules per request otherwise, quickly exhausting MySQL
// connections).
const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

export const prisma =
  globalForPrisma.prisma ??
  new PrismaClient({
    log: process.env.NODE_ENV === "development" ? ["error", "warn"] : ["error"],
  });

if (process.env.NODE_ENV !== "production") {
  globalForPrisma.prisma = prisma;
}
