import * as cheerio from "cheerio";
import { parsePhoneNumberFromString } from "libphonenumber-js";

// Deliberately excludes common asset extensions (png/jpg/svg/webp/gif) so
// email regex hits on image filenames like "logo@2x.png" don't get treated
// as addresses.
const EMAIL_RE = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g;
const ASSET_EMAIL_RE = /\.(png|jpe?g|svg|webp|gif|bmp|ico)$/i;

const PHONE_RE = /(?:\+?\d[\d\s().-]{7,15}\d)/g;

// Indian GST number format: 2-digit state code + 10-char PAN + 1 entity code
// + 'Z' + 1 checksum char.
const GST_RE = /\b\d{2}[A-Z]{5}\d{4}[A-Z]{1}\d[Z]{1}[A-Z\d]{1}\b/g;

const SOCIAL_DOMAINS: Record<string, RegExp> = {
  facebook: /(?:facebook|fb)\.com\/[\w.\-/]+/i,
  instagram: /instagram\.com\/[\w.\-/]+/i,
  linkedin: /linkedin\.com\/[\w.\-/]+/i,
  twitter: /(?:twitter|x)\.com\/[\w.\-/]+/i,
  youtube: /youtube\.com\/[\w.\-/@]+/i,
};

export function extractEmails(text: string): string[] {
  const matches = text.match(EMAIL_RE) ?? [];
  return dedupe(matches.filter((m) => !ASSET_EMAIL_RE.test(m)).map((m) => m.toLowerCase()));
}

export function extractPhones(text: string, defaultCountry: string = "IN"): string[] {
  const candidates = text.match(PHONE_RE) ?? [];
  const valid: string[] = [];
  for (const raw of candidates) {
    const digits = raw.replace(/\D/g, "");
    if (digits.length < 8 || digits.length > 15) continue;
    const parsed = parsePhoneNumberFromString(raw, defaultCountry as any);
    if (parsed?.isValid()) {
      valid.push(parsed.formatInternational());
    }
  }
  return dedupe(valid);
}

export function extractGstNumbers(text: string): string[] {
  return dedupe(text.match(GST_RE) ?? []);
}

/** Pulls phone numbers out of `tel:` links specifically (a much stronger signal than loose digit-run matching). */
export function extractTelLinks(html: string): string[] {
  const matches = [...html.matchAll(/href=["']tel:([^"']+)["']/gi)].map((m) => m[1]);
  return dedupe(matches);
}

export function extractWhatsapp(text: string): string | undefined {
  const waLink = text.match(/(?:wa\.me|api\.whatsapp\.com\/send\?phone=)\/?(\+?\d{8,15})/i);
  if (waLink) return `+${waLink[1].replace(/\D/g, "")}`;
  return undefined;
}

export function extractSocialLinks(text: string): Record<string, string> {
  const links: Record<string, string> = {};
  for (const [platform, re] of Object.entries(SOCIAL_DOMAINS)) {
    const match = text.match(re);
    if (match) links[platform] = match[0].startsWith("http") ? match[0] : `https://${match[0]}`;
  }
  return links;
}

export interface PageMeta {
  title?: string;
  metaTitle?: string;
  metaKeywords?: string;
  metaDescription?: string;
  bodyText: string;
  html: string;
}

/** Parses raw HTML once and returns both the plain-text body (for regex extraction) and meta tags. */
export function parseHtml(html: string): PageMeta {
  const $ = cheerio.load(html);
  return {
    title: $("title").first().text().trim() || undefined,
    metaTitle: $('meta[property="og:title"]').attr("content") || $("title").first().text().trim() || undefined,
    metaKeywords: $('meta[name="keywords"]').attr("content")?.trim() || undefined,
    metaDescription:
      $('meta[name="description"]').attr("content")?.trim() ||
      $('meta[property="og:description"]').attr("content")?.trim() ||
      undefined,
    // cheerio's .text() otherwise includes <script>/<style>/<noscript>
    // contents too (they're text nodes in its DOM model) — that pulled huge
    // inline-JS/JSON numeric blobs into phone/GST extraction and produced a
    // flood of coincidentally-"valid" fake phone numbers. Strip them first.
    bodyText: $("body").clone().find("script, style, noscript").remove().end().text().replace(/\s+/g, " ").trim(),
    html,
  };
}

/** Runs every extractor over a fetched page and returns one normalized record. */
export function extractFromHtml(html: string, sourceUrl: string) {
  const meta = parseHtml(html);
  // Emails: safe to also scan raw HTML (mailto: hrefs are a legitimate
  // signal). Phones: raw HTML/inline JS is full of asset hashes, version
  // strings, and tracking IDs that coincidentally match a loose phone regex
  // and pass libphonenumber's validity check — restrict digit-run matching
  // to visible body text, and pull `tel:` links separately as a much
  // stronger signal.
  const emails = extractEmails(`${meta.bodyText} ${html}`);
  const phones = dedupe([...extractTelLinks(html).map((t) => t.replace(/[^\d+]/g, "")), ...extractPhones(meta.bodyText)]);
  const gst = extractGstNumbers(meta.bodyText);
  const social = extractSocialLinks(html);
  const whatsapp = extractWhatsapp(html);

  return {
    companyName: meta.title,
    website: sourceUrl,
    email: emails[0],
    phone: phones[0],
    gstNumber: gst[0],
    whatsapp,
    socialLinks: social,
    metaTitle: meta.metaTitle,
    metaKeywords: meta.metaKeywords,
    metaDescription: meta.metaDescription,
    allEmails: emails,
    allPhones: phones,
  };
}

function dedupe<T>(arr: T[]): T[] {
  return Array.from(new Set(arr));
}
