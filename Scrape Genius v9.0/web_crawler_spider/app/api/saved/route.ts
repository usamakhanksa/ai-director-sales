import { NextRequest, NextResponse } from "next/server";

import { requireAuth } from "@/lib/auth";
import { savedSchema } from "@/lib/validators";
import { ok, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";

// Replaces the old Google-Drive-upload version of /api/saved: persists
// scraped rows to scraped_records and bumps the matching dashboard_stats
// tile in one atomic transaction, instead of writing to an external service
// with no relation back to per-user stats.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = savedSchema.parse(await req.json());

    const { record, stat } = await saveScrapedRecords(
      user.id,
      body.source,
      body.query,
      body.data,
      body.stat_type,
    );

    return ok({ scraped_record_id: record.id, dashboard_stat: stat }, 201);
  });
}
