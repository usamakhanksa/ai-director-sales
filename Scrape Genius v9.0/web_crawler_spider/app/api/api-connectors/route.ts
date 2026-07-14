export const dynamic = "force-dynamic";
import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";
import { prisma } from "@/lib/prisma";

// Generic connector: lets a user point ScrapeGenius at ANY third-party JSON
// API (their own key, their own URL) instead of needing bespoke code per
// API. Saved here; actually invoked via /api/api-connectors/[id]/run.
const createConnectorSchema = z
  .object({
    name: z.string().min(1).max(191),
    method: z.enum(["GET", "POST"]).optional().default("GET"),
    url: z.string().url().max(2048),
    apiKey: z.string().max(500).optional(),
    authType: z.enum(["none", "query", "header", "bearer"]).optional().default("none"),
    authParam: z.string().min(1).max(100).optional(),
    resultsPath: z.string().max(200).optional(),
    fieldMap: z.record(z.string()).optional(),
  })
  .refine((v) => v.authType !== "query" && v.authType !== "header" ? true : Boolean(v.authParam), {
    message: "authParam is required when authType is 'query' or 'header'",
    path: ["authParam"],
  });

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

// GET — list the authenticated user's saved connectors (API key masked).
export async function GET(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const connectors = await prisma.apiConnector.findMany({
      where: { userId: user.id },
      orderBy: { createdAt: "desc" },
    });
    return ok(connectors.map(maskConnector));
  });
}

// POST — create a new connector for the authenticated user.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = createConnectorSchema.parse(await req.json());

    const connector = await prisma.apiConnector.create({
      data: {
        userId: user.id,
        name: body.name,
        method: body.method,
        url: body.url,
        apiKey: body.apiKey,
        authType: body.authType,
        authParam: body.authParam,
        resultsPath: body.resultsPath,
        fieldMap: body.fieldMap,
      },
    });

    return ok(maskConnector(connector), 201);
  });
}
