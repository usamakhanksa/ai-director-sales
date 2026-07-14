export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { prisma } from "@/lib/prisma";

function maskConnector(c: {
  id: number;
  name: string;
  method: string;
  url: string;
  apiKey: string | null;
  authType: string;
  authParam: string | null;
  resultsPath: string | null;
  fieldMap: unknown;
  createdAt: Date;
}) {
  const { apiKey, ...rest } = c;
  return { ...rest, hasApiKey: Boolean(apiKey) };
}

// GET — return one connector's config (API key masked). 404s (not 403) on a
// connector belonging to another user, so we never confirm/deny existence.
export async function GET(
  req: NextRequest,
  { params }: { params: { id: string } },
): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const id = Number(params.id);
    if (!Number.isInteger(id)) return fail("Invalid connector id", 400);

    const connector = await prisma.apiConnector.findUnique({ where: { id } });
    if (!connector || connector.userId !== user.id) {
      return fail("Connector not found", 404);
    }

    return ok(maskConnector(connector));
  });
}

// DELETE — remove a connector, but only if it belongs to the caller.
export async function DELETE(
  req: NextRequest,
  { params }: { params: { id: string } },
): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const id = Number(params.id);
    if (!Number.isInteger(id)) return fail("Invalid connector id", 400);

    const connector = await prisma.apiConnector.findUnique({ where: { id } });
    if (!connector || connector.userId !== user.id) {
      return fail("Connector not found", 404);
    }

    await prisma.apiConnector.delete({ where: { id } });
    return ok({ deleted: true });
  });
}
