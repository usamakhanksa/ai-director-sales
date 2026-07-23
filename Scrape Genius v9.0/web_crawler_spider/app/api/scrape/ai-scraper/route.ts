import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { multilingualZeroCostAIScraper } from "@/lib/scrapers/zero-cost-ai-scraper";

const aiScraperSchema = z.object({
  url: z.string().url({ message: "url must be a valid URL" }),
});

// Zero-cost AI scraper: fetches clean Markdown for `url` via https://r.jina.ai
// (no API key required) and extracts emails, phone numbers, and company
// names — supports both Arabic (RTL) and English content.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const { url } = aiScraperSchema.parse(await req.json());

    const extracted = await multilingualZeroCostAIScraper(url);

    await saveScrapedRecords(user.id, "WEBSITE", url, [
      {
        website: url,
        title: extracted.title,
        description: extracted.description,
        emails: extracted.emails,
        phones: extracted.phones,
        companies: extracted.companies,
      },
    ]);

    return ok({ url, ...extracted });
  });
}
