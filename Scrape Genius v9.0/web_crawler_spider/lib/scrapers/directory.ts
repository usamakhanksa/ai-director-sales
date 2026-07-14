import * as cheerio from "cheerio";
import { chromium } from "playwright";

import { extractFromHtml } from "@/lib/scrapers/extract";
import type { NormalizedRecord } from "@/lib/scrapers/types";

export type DirectoryProvider = "INDIAMART" | "JUSTDIAL" | "SULEKHA" | "GENERIC";

const USER_AGENT =
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36";

// Best-effort company-name selectors per directory, tried in order; falls
// back to extractFromHtml's <title>-based guess if none match. These are
// heuristics, not verified against a real live listing page for each site.
const NAME_SELECTORS: Record<DirectoryProvider, string[]> = {
  INDIAMART: ["h1", ".compname", ".company-name", ".fs20"],
  JUSTDIAL: ["h1", ".jcn", ".resultbox_title", ".title-bold"],
  SULEKHA: ["h1", ".company-name", ".biz-name"],
  GENERIC: ["h1"],
};

function normalizeUrl(raw: string): string {
  const trimmed = raw.trim();
  if (/^https?:\/\//i.test(trimmed)) return trimmed;
  return `https://${trimmed}`;
}

async function fetchWithTimeout(url: string, timeoutMs = 10_000): Promise<string> {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), timeoutMs);
  try {
    const res = await fetch(url, { headers: { "User-Agent": USER_AGENT }, signal: controller.signal });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.text();
  } finally {
    clearTimeout(timer);
  }
}

async function fetchWithPlaywright(url: string, timeoutMs = 20_000): Promise<string> {
  const browser = await chromium.launch({ headless: true });
  try {
    const page = await browser.newPage({ userAgent: USER_AGENT });
    await page.goto(url, { waitUntil: "domcontentloaded", timeout: timeoutMs });
    await page.waitForTimeout(1500);
    return await page.content();
  } finally {
    await browser.close();
  }
}

/** A short/blocked-looking response (captcha page, bot-block interstitial) rather than real content. */
function looksBlocked(html: string): boolean {
  if (html.length < 2000) return true;
  const lower = html.toLowerCase();
  return /captcha|access denied|are you a human|attention required/.test(lower);
}

/**
 * Fetches a directory listing URL and extracts contact/meta data. Tries a
 * plain HTTP fetch first (fast); if that fails or comes back looking
 * blocked/too-short, falls back to a real headless-browser render.
 */
export async function scrapeDirectoryUrl(
  rawUrl: string,
  provider: DirectoryProvider,
): Promise<NormalizedRecord & { warning?: string }> {
  const url = normalizeUrl(rawUrl);
  let html: string;
  let warning: string | undefined;

  try {
    html = await fetchWithTimeout(url);
    if (looksBlocked(html)) {
      warning = "Plain fetch looked blocked/too short — retried with a headless browser";
      html = await fetchWithPlaywright(url);
    }
  } catch {
    warning = "Plain fetch failed — retried with a headless browser";
    html = await fetchWithPlaywright(url);
  }

  const base = extractFromHtml(html, url);
  const $ = cheerio.load(html);

  let companyName = base.companyName;
  for (const selector of NAME_SELECTORS[provider]) {
    const text = $(selector).first().text().trim();
    if (text) {
      companyName = text;
      break;
    }
  }

  const result: NormalizedRecord & { warning?: string } = { ...base, companyName };
  if (looksBlocked(html)) {
    result.warning = "Page still looks blocked/short after fallback — data may be incomplete";
  } else if (warning) {
    result.warning = warning;
  }

  return result;
}
