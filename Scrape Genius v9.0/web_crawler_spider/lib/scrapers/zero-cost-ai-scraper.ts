import { z } from "zod";
import { extractEmails, extractPhones } from "./extract";

// Schema for extracted data validation
const ExtractedDataSchema = z.object({
  emails: z.array(z.string()),
  phones: z.array(z.string()),
  companies: z.array(z.string()),
  title: z.string().optional(),
  description: z.string().optional(),
});

export type ExtractedData = z.infer<typeof ExtractedDataSchema>;

const COMPANY_TERMS_EN = ["Inc", "LLC", "Ltd", "Corp", "Company", "Co", "GmbH", "SA", "SRL", "SARL"];
const COMPANY_TERMS_AR = ["شركة", "مؤسسة", "مكتب"];

const EMPTY_RESULT: ExtractedData = { emails: [], phones: [], companies: [], title: undefined, description: undefined };

/** Fetches a URL via Jina AI's reader proxy (r.jina.ai), which returns clean Markdown instead of raw HTML — no API key required. */
async function fetchCleanMarkdown(url: string): Promise<string> {
  const response = await fetch(`https://r.jina.ai/${encodeURIComponent(url)}`);
  if (!response.ok) {
    throw new Error(`Jina AI reader failed: ${response.status} ${response.statusText}`);
  }
  return response.text();
}

function extractCompanyNames(text: string, includeArabic: boolean): string[] {
  const companies: string[] = [];

  for (const term of COMPANY_TERMS_EN) {
    const pattern = new RegExp(`[A-Z][\\w\\s&]{2,20}?\\s${term}\\b`, "g");
    for (const match of text.match(pattern) ?? []) {
      const cleaned = match.trim();
      if (cleaned.length > 3 && !companies.includes(cleaned)) companies.push(cleaned);
    }
  }

  if (includeArabic) {
    for (const term of COMPANY_TERMS_AR) {
      const pattern = new RegExp(`${term}\\s+[^\\n.,،;]{2,40}`, "g");
      for (const match of text.match(pattern) ?? []) {
        const cleaned = match.trim();
        if (cleaned.length > 3 && !companies.includes(cleaned)) companies.push(cleaned);
      }
    }
  }

  return companies;
}

function extractTitle(markdown: string, includeArabic: boolean): string | undefined {
  const h1Match = markdown.match(/^#\s+(.+)$/m);
  if (h1Match) return h1Match[1].trim();

  const sentencePattern = includeArabic
    ? /(?:^|\n)([A-Z؀-ۿ][^.!?\n]{10,80})(?:[.!?]|\n)/
    : /(?:^|\n)([A-Z][^.!?\n]{10,80})(?:[.!?]|\n)/;
  const sentenceMatch = markdown.match(sentencePattern);
  return sentenceMatch ? sentenceMatch[1].trim() : undefined;
}

function extractDescription(markdown: string): string | undefined {
  // [\s\S] instead of the dotAll ("s") flag — keeps this compatible with the project's ES2017 build target.
  const match = markdown.match(/(?:^|\n\n)([\s\S]{50,300}?)(?:\n\n|$)/);
  return match ? match[1].replace(/\s+/g, " ").trim() : undefined;
}

/**
 * Zero-cost AI scraper utility: fetches clean Markdown for any URL via
 * https://r.jina.ai (no API key needed) and extracts emails, phone numbers,
 * and company names using the shared regex extractors. Never throws — on
 * any failure it returns an empty result so callers can treat it as a
 * best-effort enrichment step.
 */
export async function zeroCostAIScraper(url: string): Promise<ExtractedData> {
  try {
    const markdown = await fetchCleanMarkdown(url);

    const result = ExtractedDataSchema.parse({
      emails: extractEmails(markdown),
      phones: extractPhones(markdown),
      companies: extractCompanyNames(markdown, false),
      title: extractTitle(markdown, false),
      description: extractDescription(markdown),
    });

    return result;
  } catch (error) {
    console.error("Zero-cost AI scraper error:", error);
    return EMPTY_RESULT;
  }
}

/** Same as `zeroCostAIScraper`, but company-name and title extraction also match Arabic script (RTL). */
export async function multilingualZeroCostAIScraper(url: string): Promise<ExtractedData> {
  try {
    const markdown = await fetchCleanMarkdown(url);

    const result = ExtractedDataSchema.parse({
      emails: extractEmails(markdown),
      phones: extractPhones(markdown),
      companies: extractCompanyNames(markdown, true),
      title: extractTitle(markdown, true),
      description: extractDescription(markdown),
    });

    return result;
  } catch (error) {
    console.error("Multilingual zero-cost AI scraper error:", error);
    return EMPTY_RESULT;
  }
}
