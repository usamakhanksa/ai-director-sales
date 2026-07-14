import { NextRequest, NextResponse } from "next/server";
// papaparse ships no type declarations and no @types/papaparse is installed,
// so it's pulled in via require (typed as any) instead of an ES import to
// avoid a "cannot find type declarations" build failure.
// eslint-disable-next-line @typescript-eslint/no-var-requires
const Papa: any = require("papaparse");
import mammoth from "mammoth";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { extractEmails, extractPhones } from "@/lib/scrapers/extract";

const MAX_FILE_BYTES = 20 * 1024 * 1024; // 20MB

/**
 * Accepts an uploaded .txt/.csv/.docx file, pulls out its plain text, and
 * runs the shared email/phone extractors over it. Persists the result via
 * saveScrapedRecords like every other scraper route so it shows up on the
 * dashboard and in scraped_records.
 */
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);

    const form = await req.formData();
    const file = form.get("file");

    if (!(file instanceof Blob)) {
      return fail("Missing required 'file' field in multipart/form-data body", 400);
    }

    const filename = (file as any).name ?? "upload";
    if (file.size > MAX_FILE_BYTES) {
      return fail("File too large — max 20MB", 400);
    }

    const ext = filename.toLowerCase().slice(filename.lastIndexOf("."));
    let text: string;

    if (ext === ".txt") {
      text = await file.text();
    } else if (ext === ".csv") {
      const raw = await file.text();
      const parsed = Papa.parse(raw, { header: false });
      text = parsed.data.map((row: unknown) => (Array.isArray(row) ? row.join(" ") : String(row))).join("\n");
    } else if (ext === ".docx") {
      const buffer = Buffer.from(await file.arrayBuffer());
      const result = await mammoth.extractRawText({ buffer });
      text = result.value;
    } else {
      return fail("Unsupported file type — use .txt, .csv, or .docx", 400);
    }

    const emails = extractEmails(text);
    const phones = extractPhones(text);

    await saveScrapedRecords(user.id, "DOCUMENT", filename, [{ emails, phones }]);

    return ok({ query: filename, source: "DOCUMENT", emails, phones });
  });
}
