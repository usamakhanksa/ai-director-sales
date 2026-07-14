import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { prisma } from "@/lib/prisma";
import { encryptSecret } from "@/lib/crypto";

const PROVIDERS = ["justdial", "indiamart"] as const;

const bodySchema = z.object({
  loginId: z.string().min(1).max(191),
  secret: z.string().min(1).max(500),
});

function resolveProvider(raw: string): "JUSTDIAL" | "INDIAMART" | null {
  const lower = raw.toLowerCase();
  return PROVIDERS.includes(lower as (typeof PROVIDERS)[number]) ? (lower.toUpperCase() as "JUSTDIAL" | "INDIAMART") : null;
}

// POST — save (upsert) this user's login for their own Justdial/IndiaMART
// seller dashboard. `secret` is encrypted before it touches the database.
export async function POST(
  req: NextRequest,
  { params }: { params: { provider: string } },
): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const provider = resolveProvider(params.provider);
    if (!provider) return fail("Unknown CRM provider — use 'justdial' or 'indiamart'", 400);

    const body = bodySchema.parse(await req.json());

    const connection = await prisma.crmConnection.upsert({
      where: { userId_provider: { userId: user.id, provider } },
      update: { loginId: body.loginId, secret: encryptSecret(body.secret) },
      create: { userId: user.id, provider, loginId: body.loginId, secret: encryptSecret(body.secret) },
    });

    return ok({
      connected: true,
      provider,
      loginId: connection.loginId,
      lastSyncedAt: connection.lastSyncedAt,
      lastStatus: connection.lastStatus,
    });
  });
}

// GET — connection status for this provider (never returns the decrypted secret).
export async function GET(
  req: NextRequest,
  { params }: { params: { provider: string } },
): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const provider = resolveProvider(params.provider);
    if (!provider) return fail("Unknown CRM provider — use 'justdial' or 'indiamart'", 400);

    const connection = await prisma.crmConnection.findUnique({
      where: { userId_provider: { userId: user.id, provider } },
    });

    if (!connection) return ok({ connected: false, provider });

    return ok({
      connected: true,
      provider,
      loginId: connection.loginId,
      lastSyncedAt: connection.lastSyncedAt,
      lastStatus: connection.lastStatus,
    });
  });
}

// DELETE — remove this user's saved connection for the provider.
export async function DELETE(
  req: NextRequest,
  { params }: { params: { provider: string } },
): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const provider = resolveProvider(params.provider);
    if (!provider) return fail("Unknown CRM provider — use 'justdial' or 'indiamart'", 400);

    await prisma.crmConnection.deleteMany({ where: { userId: user.id, provider } });
    return ok({ deleted: true });
  });
}
