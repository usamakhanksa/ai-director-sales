import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import type { ScrapeSource } from "@prisma/client";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { ENGINE_IDS, runMultiSearch, type EngineId } from "@/lib/search-engines";

const ENGINE_TO_SOURCE: Record<EngineId, ScrapeSource> = {
  google: "GOOGLE",
  bing: "BING",
  duckduckgo: "DUCKDUCKGO",
  yahoo: "YAHOO",
};

const querySchema = z.object({
  q: z.string().min(1).max(500),
  engines: z.array(z.enum(ENGINE_IDS)).min(1).default([...ENGINE_IDS]),
  page: z.coerce.number().int().positive().max(20).optional(),
  limit: z.coerce.number().int().positive().max(30).optional(),
  lang: z.string().max(10).optional(),
  safeSearch: z.coerce.boolean().optional(),
});

function parseEngines(raw: string | null): string[] | undefined {
  if (!raw) return undefined;
  return raw
    .split(",")
    .map((e) => e.trim().toLowerCase())
    .filter(Boolean);
}

async function handle(req: NextRequest, input: Record<string, unknown>): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const parsed = querySchema.parse(input);

    const { query, results, engines } = await runMultiSearch(
      parsed.q,
      parsed.engines,
      { page: parsed.page, limit: parsed.limit, lang: parsed.lang, safeSearch: parsed.safeSearch },
      user.id,
    );

    await Promise.all(
      engines
        .filter((e) => e.results.length > 0)
        .map((e) =>
          saveScrapedRecords(user.id, ENGINE_TO_SOURCE[e.engine], query, e.results).catch((err) =>
            console.error(`Failed to persist ${e.engine} results:`, err),
          ),
        ),
    );

    if (results.length === 0 && engines.every((e) => e.mode === "failed")) {
      return fail("All requested search engines failed to return results — try again shortly", 502);
    }

    return ok({
      query,
      results,
      meta: {
        quotaRemaining: Object.fromEntries(engines.map((e) => [e.engine, e.quotaRemaining])),
        engineStatus: Object.fromEntries(engines.map((e) => [e.engine, { mode: e.mode, error: e.error, count: e.results.length }])),
      },
    });
  });
}

export async function GET(req: NextRequest): Promise<NextResponse> {
  const { searchParams } = new URL(req.url);
  return handle(req, {
    q: searchParams.get("q") || "",
    engines: parseEngines(searchParams.get("engines")),
    page: searchParams.get("page") ?? undefined,
    limit: searchParams.get("limit") ?? undefined,
    lang: searchParams.get("lang") ?? undefined,
    safeSearch: searchParams.get("safeSearch") ?? undefined,
  });
}

export async function POST(req: NextRequest): Promise<NextResponse> {
  const body = await req.json().catch(() => ({}));
  return handle(req, body);
}
