import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { extractEmails, extractPhones, parseHtml } from "@/lib/scrapers/extract";

const USER_AGENT =
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36";

const contactScraperSchema = z
  .object({
    type: z.enum(["EMAIL", "PHONE"]),
    url: z.string().min(1).optional(),
    text: z.string().min(1).optional(),
  })
  .refine((v) => Boolean(v.url) !== Boolean(v.text), {
    message: "Provide exactly one of url or text",
  });

function normalizeUrl(raw: string): string {
  const trimmed = raw.trim();
  if (/^https?:\/\//i.test(trimmed)) return trimmed;
  return `https://${trimmed}`;
}

async function fetchBodyText(rawUrl: string): Promise<string> {
  const url = normalizeUrl(rawUrl);
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 10_000);
  try {
    const res = await fetch(url, {
      headers: { "User-Agent": USER_AGENT },
      signal: controller.signal,
    });
    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }
    const html = await res.text();
    return parseHtml(html).bodyText;
  } finally {
    clearTimeout(timeout);
  }
}

// Shared route behind both the "Email Scraper" and "Phone number scraper"
// tools — `type` selects which extractor runs over either a fetched page's
// visible text or raw pasted text.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = contactScraperSchema.parse(await req.json());

    let sourceText: string;
    if (body.url) {
      try {
        sourceText = await fetchBodyText(body.url);
      } catch (err: unknown) {
        const reason = err instanceof Error ? err.message : "Unknown error";
        return fail(`Failed to fetch url: ${reason}`, 502);
      }
    } else {
      sourceText = body.text!;
    }

    const results = body.type === "EMAIL" ? extractEmails(sourceText) : extractPhones(sourceText);
    const query = body.url ?? body.text!.slice(0, 80);

    await saveScrapedRecords(
      user.id,
      body.type,
      query,
      results.map((v) => ({ value: v })),
      undefined,
    );

    return ok({
      query,
      source: body.type,
      count: results.length,
      results,
    });
  });
}
