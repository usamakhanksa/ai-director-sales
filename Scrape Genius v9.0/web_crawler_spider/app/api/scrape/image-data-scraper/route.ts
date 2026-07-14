import { NextRequest, NextResponse } from "next/server";
import { createWorker } from "tesseract.js";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";
import { extractEmails, extractPhones } from "@/lib/scrapers/extract";

const MAX_FILE_BYTES = 15 * 1024 * 1024; // 15MB

/**
 * Accepts an uploaded image, OCRs it with tesseract.js, then runs the shared
 * email/phone extractors over the recognized text. Persists the result via
 * saveScrapedRecords like every other scraper route.
 *
 * tesseract.js@5.x API: createWorker(lang) spawns a worker that is already
 * initialized with the given language (no separate load()/loadLanguage()/
 * initialize() calls needed like in tesseract.js@2.x).
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
      return fail("File too large — max 15MB", 400);
    }

    const buffer = Buffer.from(await file.arrayBuffer());

    const worker = await createWorker("eng");
    let text: string;
    try {
      const result = await worker.recognize(buffer);
      text = result.data.text;
    } finally {
      await worker.terminate();
    }

    const emails = extractEmails(text);
    const phones = extractPhones(text);

    await saveScrapedRecords(user.id, "IMAGE", filename, [{ ocrText: text, emails, phones }]);

    return ok({ query: filename, source: "IMAGE", ocrText: text, emails, phones });
  });
}
