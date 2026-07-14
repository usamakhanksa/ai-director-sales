import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { prisma } from "@/lib/prisma";
import { saveScrapedRecords } from "@/lib/records";
import type { NormalizedRecord } from "@/lib/scrapers/types";

const runSchema = z.object({
  query: z.string().min(1).max(500),
});

/** Resolves a dot-path like "contact.phone" against a plain object/array tree. */
function getPath(obj: unknown, path: string): unknown {
  if (!path) return obj;
  return path
    .split(".")
    .reduce<unknown>((acc, key) => (acc && typeof acc === "object" ? (acc as Record<string, unknown>)[key] : undefined), obj);
}

/** Builds the target URL: substitutes {query} and appends the API key for authType "query". */
function buildUrl(rawUrl: string, query: string, authType: string, authParam: string | null, apiKey: string | null): string {
  let url = rawUrl.includes("{query}") ? rawUrl.replaceAll("{query}", encodeURIComponent(query)) : rawUrl;

  if (authType === "query" && authParam && apiKey) {
    const separator = url.includes("?") ? "&" : "?";
    url = `${url}${separator}${encodeURIComponent(authParam)}=${encodeURIComponent(apiKey)}`;
  }

  return url;
}

function buildHeaders(authType: string, authParam: string | null, apiKey: string | null): Record<string, string> {
  const headers: Record<string, string> = { Accept: "application/json" };
  if (authType === "header" && authParam && apiKey) {
    headers[authParam] = apiKey;
  } else if (authType === "bearer" && apiKey) {
    headers["Authorization"] = `Bearer ${apiKey}`;
  }
  return headers;
}

function mapRecord(item: unknown, fieldMap: Record<string, string> | null): NormalizedRecord {
  if (!fieldMap) {
    return (item && typeof item === "object" ? (item as Record<string, unknown>) : { value: item }) as NormalizedRecord;
  }
  const record: Record<string, unknown> = {};
  for (const [normalizedField, path] of Object.entries(fieldMap)) {
    record[normalizedField] = getPath(item, path);
  }
  return record as NormalizedRecord;
}

// POST — runs a saved connector against its configured third-party API for
// `query`, normalizes the results per `fieldMap`, and persists them the same
// way every other scraper route does (dashboard stats + scraped_records row).
export async function POST(
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

    const { query } = runSchema.parse(await req.json());

    const url = buildUrl(connector.url, query, connector.authType, connector.authParam, connector.apiKey);
    const headers = buildHeaders(connector.authType, connector.authParam, connector.apiKey);

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 15_000);

    let res: Response;
    try {
      if (connector.method === "POST") {
        headers["Content-Type"] = "application/json";
        res = await fetch(url, {
          method: "POST",
          headers,
          body: JSON.stringify({ query }),
          signal: controller.signal,
        });
      } else {
        res = await fetch(url, { method: "GET", headers, signal: controller.signal });
      }
    } catch (err: unknown) {
      const reason = err instanceof Error ? err.message : "Unknown error";
      return fail(`Failed to reach the connector's API: ${reason}`, 502);
    } finally {
      clearTimeout(timeout);
    }

    if (!res.ok) {
      const hint = res.status === 401 || res.status === 403 ? " — check the API key" : "";
      return fail(`The connector's API returned ${res.status}${hint}`, 502);
    }

    let payload: unknown;
    try {
      payload = await res.json();
    } catch {
      return fail("Response wasn't valid JSON", 502);
    }

    const resolved = connector.resultsPath ? getPath(payload, connector.resultsPath) : payload;
    const items: unknown[] = Array.isArray(resolved) ? resolved : resolved != null ? [resolved] : [];

    const fieldMap = (connector.fieldMap as Record<string, string> | null) ?? null;
    const results = items.map((item) => mapRecord(item, fieldMap));

    await saveScrapedRecords(user.id, "CUSTOM_API", query, results, `${connector.name} Records Scraped`);

    return ok({ query, source: "CUSTOM_API", count: results.length, results });
  });
}
