import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { scrapeDirectoryUrl } from "@/lib/scrapers/directory";

const bodySchema = z.object({
  urls: z.array(z.string().min(1)).min(1).max(10),
});

// Generic directory scraper: works against any listing URL, not just
// IndiaMART/Justdial/Sulekha's dedicated routes.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = bodySchema.parse(await req.json());

    const results = [];
    for (const url of body.urls) {
      results.push(await scrapeDirectoryUrl(url, "GENERIC"));
    }

    await saveScrapedRecords(user.id, "BUSINESS_DIRECTORY", body.urls.join(", "), results);

    return ok({ query: body.urls.join(", "), source: "BUSINESS_DIRECTORY", count: results.length, results });
  });
}
