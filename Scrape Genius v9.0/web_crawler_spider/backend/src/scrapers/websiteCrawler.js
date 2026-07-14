/**
 * AnyWebsite Deep Crawler
 * 
 * Recursively crawls a domain (BFS up to configurable depth / page limit)
 * and harvests all email addresses, phone numbers, and social links found.
 * 
 * Strategy:
 *   1. Fetch the seed URL(s) with axios (fast, lightweight for static pages)
 *   2. Parse all internal links (same domain) and queue them
 *   3. For JS-heavy pages (detected by low text:HTML ratio), fall back to Playwright
 *   4. Run extractors on each page's HTML
 *   5. Save accumulated results to scraped_records (reuses existing table)
 * 
 * Key Fields Extracted:
 *   All email addresses | Phone numbers | Social links | Domain-wide dedup
 */

"use strict";

const axios = require("axios");
const cheerio = require("cheerio");
const { URL } = require("url");
const db = require("../config/database");
const { createBrowser, createContext, closeBrowser } = require("../services/browserEngine");
const { randomDelay, randomUserAgent } = require("../services/antiRobotService");

// ─────────────────────────────────────────────────────────────────────────────
// Regex patterns
// ─────────────────────────────────────────────────────────────────────────────

const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
const PHONE_RE = /(?:\+?[\d\s\-().]{8,20}\d)/g;
const ASSET_EXT_RE = /\.(png|jpe?g|svg|webp|gif|ico|bmp|pdf|zip|mp4|mp3|avi|mov|woff|woff2|ttf|css|js)$/i;
const SOCIAL_RE = {
  instagram: /instagram\.com\/([a-zA-Z0-9_.]+)/i,
  facebook:  /facebook\.com\/(?!share|sharer|dialog)([a-zA-Z0-9_./-]+)/i,
  linkedin:  /linkedin\.com\/(?:company|in)\/([a-zA-Z0-9_.-]+)/i,
  twitter:   /(?:twitter|x)\.com\/([a-zA-Z0-9_]+)/i,
};

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function extractEmails(text) {
  return [...new Set((text.match(EMAIL_RE) || [])
    .map((m) => m.toLowerCase())
    .filter((m) => !ASSET_EXT_RE.test(m))
  )];
}

function extractPhones(text) {
  const raw = text.match(PHONE_RE) || [];
  return [...new Set(raw
    .map((m) => m.trim())
    .filter((m) => m.replace(/\D/g, "").length >= 7)
  )];
}

function extractSocials(html) {
  const result = {};
  for (const [platform, re] of Object.entries(SOCIAL_RE)) {
    const m = html.match(re);
    if (m) result[platform] = m[0].startsWith("http") ? m[0] : `https://${m[0]}`;
  }
  return result;
}

/**
 * Extracts all internal links from a parsed HTML page.
 * Only returns same-domain URLs, stripped of fragments and querystrings
 * (configurable), and excluding assets.
 */
function extractInternalLinks($, baseUrl, pageUrl) {
  const base = new URL(baseUrl);
  const links = new Set();

  $("a[href]").each((_, el) => {
    try {
      const raw = $( el).attr("href");
      if (!raw || raw.startsWith("mailto:") || raw.startsWith("tel:") || raw.startsWith("javascript:")) return;

      const resolved = new URL(raw, pageUrl);
      if (resolved.hostname !== base.hostname) return;
      if (ASSET_EXT_RE.test(resolved.pathname)) return;

      // Normalize: remove fragment
      resolved.hash = "";
      links.add(resolved.toString());
    } catch {}
  });

  return [...links];
}

/**
 * Fetches a URL with axios (fast path for static HTML).
 * Returns { html, isJsHeavy } where isJsHeavy suggests a Playwright fallback.
 */
async function fetchStatic(url) {
  const { data: html, headers } = await axios.get(url, {
    timeout: 15000,
    headers: {
      "User-Agent": randomUserAgent(),
      Accept: "text/html,application/xhtml+xml",
      "Accept-Language": "en-US,en;q=0.9",
    },
    maxRedirects: 5,
  });

  // Heuristic: if the page has very little text relative to HTML size,
  // it's probably JS-rendered and needs Playwright.
  const $ = cheerio.load(html);
  const textLen = $("body").text().replace(/\s+/g, " ").trim().length;
  const isJsHeavy = textLen < 200 && html.length > 5000;

  return { html, isJsHeavy };
}

/**
 * Fetches a URL using Playwright for JS-heavy pages.
 */
async function fetchWithBrowser(browser, url) {
  const { context, page } = await createContext(browser);
  try {
    await page.goto(url, { waitUntil: "domcontentloaded", timeout: 20000 });
    await randomDelay(1000, 2500);
    return await page.content();
  } finally {
    await context.close();
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 * keywords[] here are treated as URLs to crawl.
 * 
 * @param {number}   jobId
 * @param {string[]} keywords    Array of seed URLs to crawl
 * @param {object}   config      { maxPages: 100, maxDepth: 3, respectRobots: false }
 * @param {object}   hooks       { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const maxPages  = config.maxPages || 100;
  const maxDepth  = config.maxDepth || 3;
  const seedUrls  = keywords; // In this module, keywords = URLs to crawl
  let totalExtracted = 0;

  // Lazy-create browser only if JS-heavy pages are detected
  let browser = null;

  // Master dedup set for emails across all domains in this job
  const globalEmailDedup = new Set();

  for (let si = 0; si < seedUrls.length; si++) {
    if (isCancelled()) break;
    const seedUrl = seedUrls[si];

    let baseOrigin;
    try {
      baseOrigin = new URL(seedUrl).origin;
    } catch {
      await logEvent("WARN", `Invalid URL: ${seedUrl}`);
      continue;
    }

    await logEvent("INFO", `Crawling: ${seedUrl} (${si + 1}/${seedUrls.length})`);

    // BFS queue: [url, depth]
    const queue = [[seedUrl, 0]];
    const visited = new Set([seedUrl]);
    let pageCount = 0;

    // Accumulated results for this domain
    const domainEmails   = new Set();
    const domainPhones   = new Set();
    const domainSocials  = {};

    while (queue.length > 0 && pageCount < maxPages && !isCancelled()) {
      const [currentUrl, depth] = queue.shift();
      pageCount++;

      await logEvent("INFO", `[${pageCount}/${maxPages}] Fetching: ${currentUrl}`);

      let html;
      try {
        const { html: staticHtml, isJsHeavy } = await fetchStatic(currentUrl);
        if (isJsHeavy) {
          await logEvent("DEBUG", `JS-heavy page detected, switching to Playwright: ${currentUrl}`);
          if (!browser) {
            browser = await createBrowser({ headless: process.env.PLAYWRIGHT_HEADLESS !== "false" });
          }
          html = await fetchWithBrowser(browser, currentUrl);
        } else {
          html = staticHtml;
        }
      } catch (err) {
        await logEvent("WARN", `Failed to fetch ${currentUrl}: ${err.message}`);
        continue;
      }

      const $ = cheerio.load(html);
      const bodyText = $("body").text().replace(/\s+/g, " ");

      // Extract data
      const pageEmails = extractEmails(bodyText + " " + html);
      const pagePhones = extractPhones(bodyText);
      const pageSocials = extractSocials(html);

      pageEmails.forEach((e) => { if (!globalEmailDedup.has(e)) domainEmails.add(e); });
      pagePhones.forEach((p) => domainPhones.add(p));
      Object.assign(domainSocials, pageSocials);

      if (pageEmails.length > 0 || pagePhones.length > 0) {
        await logEvent("INFO", `Page ${pageCount}: found ${pageEmails.length} emails, ${pagePhones.length} phones`);
      }

      // Enqueue internal links if within depth limit
      if (depth < maxDepth) {
        const links = extractInternalLinks($, baseOrigin, currentUrl);
        for (const link of links) {
          if (!visited.has(link)) {
            visited.add(link);
            queue.push([link, depth + 1]);
          }
        }
      }

      await randomDelay(500, 1500);
    }

    // Save consolidated domain results to DB
    const emails  = [...domainEmails];
    const phones  = [...domainPhones];
    emails.forEach((e) => globalEmailDedup.add(e));

    if (emails.length > 0 || phones.length > 0) {
      // Save as ScrapedRecord (reuses existing table for website data)
      await db("scraped_records").insert({
        user_id: (await db("scrape_jobs").where({ id: jobId }).first()).user_id,
        query: seedUrl,
        source: "WEBSITE",
        data: JSON.stringify({
          domain: baseOrigin,
          emails,
          phones,
          socials: domainSocials,
          pagesScanned: pageCount,
        }),
      });

      totalExtracted += emails.length + phones.length;
      await logEvent("INFO", `Domain complete: ${emails.length} emails, ${phones.length} phones from ${pageCount} pages`);
    } else {
      await logEvent("WARN", `No contacts found for: ${seedUrl}`);
    }

    const progress = ((si + 1) / seedUrls.length) * 100;
    await updateProgress(progress, totalExtracted);
  }

  if (browser) await closeBrowser(browser);
}

module.exports = { run };
