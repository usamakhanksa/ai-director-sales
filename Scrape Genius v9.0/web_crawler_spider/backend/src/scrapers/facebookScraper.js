/**
 * Facebook Phones & Emails Scraper
 * 
 * Strategy (no Facebook login required):
 *   1. For each keyword, run Google/Bing/DuckDuckGo dorking queries:
 *      site:facebook.com "keyword" (phone OR email OR contact)
 *   2. Collect Facebook profile/page URLs from SERP results
 *   3. Deep-visit each Facebook URL with stealth Playwright
 *   4. Extract: name, phone, email, address, title, description, profile link
 *   5. Save each result to social_results table
 * 
 * Key Fields Extracted:
 *   Phone | Email | Address | Title | Description | Profile Link
 */

"use strict";

const axios = require("axios");
const cheerio = require("cheerio");
const db = require("../config/database");
const { createBrowser, createContext, closeBrowser, navigateTo } = require("../services/browserEngine");
const { randomDelay, randomUserAgent } = require("../services/antiRobotService");

// ─────────────────────────────────────────────────────────────────────────────
// Regex patterns for extracting contact data
// ─────────────────────────────────────────────────────────────────────────────

const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
// Broad phone pattern — covers international formats including Gulf/Arab numbers
const PHONE_RE = /(?:\+?[\d\s\-().]{8,20}\d)/g;
const ASSET_EXT_RE = /\.(png|jpe?g|svg|webp|gif|ico|bmp)$/i;

// Search engine dork templates
const DORK_ENGINES = [
  { name: "google", url: (q) => `https://www.google.com/search?q=${encodeURIComponent(q)}&num=10` },
  { name: "bing",   url: (q) => `https://www.bing.com/search?q=${encodeURIComponent(q)}&count=10` },
  { name: "duckduckgo", url: (q) => `https://duckduckgo.com/?q=${encodeURIComponent(q)}&ia=web` },
];

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function extractEmails(text) {
  const matches = text.match(EMAIL_RE) || [];
  return [...new Set(matches
    .map((m) => m.toLowerCase())
    .filter((m) => !ASSET_EXT_RE.test(m))
  )];
}

function extractPhones(text) {
  const raw = text.match(PHONE_RE) || [];
  return [...new Set(raw
    .map((m) => m.replace(/\s+/g, " ").trim())
    .filter((m) => m.replace(/\D/g, "").length >= 7)
  )];
}

/**
 * Scrapes a search engine SERP for Facebook profile links using cheerio.
 * Keeps it lightweight (no browser needed for SERP scraping).
 */
async function fetchFacebookUrlsFromSerp(keyword, engine, maxResults = 10) {
  const dorkQuery = `site:facebook.com "${keyword}" (phone OR email OR contact OR mobile)`;
  const serpUrl = engine.url(dorkQuery);

  const headers = {
    "User-Agent": randomUserAgent(),
    "Accept": "text/html,application/xhtml+xml",
    "Accept-Language": "en-US,en;q=0.9",
  };

  let html;
  try {
    const { data } = await axios.get(serpUrl, { headers, timeout: 15000 });
    html = data;
  } catch (err) {
    return [];
  }

  const $ = cheerio.load(html);
  const fbUrls = new Set();

  // Extract all hrefs that point to facebook.com
  $("a[href]").each((_, el) => {
    const href = $(el).attr("href") || "";
    // Google wraps links: /url?q=https://facebook.com/...
    const decoded = decodeURIComponent(href.replace(/^\/url\?q=/, "").split("&")[0]);
    if (/facebook\.com\/(pages?|profile|people|groups?|[^/]+\/)/i.test(decoded)) {
      const clean = decoded.split("?")[0].split("#")[0];
      if (clean.length < 200) fbUrls.add(clean);
    }
  });

  return [...fbUrls].slice(0, maxResults);
}

/**
 * Deep-visits a Facebook profile/page URL with a stealth Playwright browser
 * and extracts all contact data visible on the page.
 */
async function scrapeFacebookProfile(page, url) {
  const result = {
    profileUrl: url,
    name: null,
    title: null,
    description: null,
    phone: null,
    email: null,
    address: null,
    rawData: {},
  };

  try {
    await navigateTo(page, url);
    await randomDelay(1500, 3000);

    // Try to navigate to the "About" section for contact info
    const aboutUrl = url.replace(/\/?$/, "/about");
    await navigateTo(page, aboutUrl);
    await randomDelay(1000, 2500);

    const html = await page.content();
    const $ = cheerio.load(html);
    const bodyText = $("body").text().replace(/\s+/g, " ").trim();

    // Extract page title / name
    result.name = $("h1").first().text().trim() ||
                  $('meta[property="og:title"]').attr("content") || null;

    // Extract meta description
    result.description = $('meta[property="og:description"]').attr("content") ||
                         $('meta[name="description"]').attr("content") || null;

    // Extract phones and emails from body text
    const emails = extractEmails(bodyText);
    const phones = extractPhones(bodyText);
    result.email = emails[0] || null;
    result.phone = phones[0] || null;
    result.rawData = { allEmails: emails, allPhones: phones };

    // Try to extract address from structured data
    const ldJson = $('script[type="application/ld+json"]').text();
    if (ldJson) {
      try {
        const ld = JSON.parse(ldJson);
        const addr = ld.address || (Array.isArray(ld) && ld[0]?.address);
        if (addr) {
          result.address = [addr.streetAddress, addr.addressLocality, addr.addressCountry]
            .filter(Boolean).join(", ");
        }
        result.title = ld.jobTitle || ld.description || result.description;
      } catch {}
    }

  } catch (err) {
    result.rawData.error = err.message;
  }

  return result;
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 * 
 * @param {number}   jobId
 * @param {string[]} keywords
 * @param {object}   config       { engines: ["google","bing"], maxPerKeyword: 20 }
 * @param {object}   hooks        { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const engines = (config.engines || ["google", "bing"]).map((name) =>
    DORK_ENGINES.find((e) => e.name === name)
  ).filter(Boolean);

  // maxPerKeyword: 0 (or "0") means "no limit — visit every profile found"
  const maxPerKeyword = config.maxPerKeyword === 0 || config.maxPerKeyword === "0"
    ? Infinity
    : config.maxPerKeyword || 20;
  let totalExtracted = 0;
  const totalSteps = keywords.length;

  const browser = await createBrowser({ headless: process.env.PLAYWRIGHT_HEADLESS !== "false" });

  try {
    for (let ki = 0; ki < keywords.length; ki++) {
      if (isCancelled()) break;
      const keyword = keywords[ki];

      await logEvent("INFO", `Processing keyword: "${keyword}" (${ki + 1}/${totalSteps})`);

      // Collect Facebook URLs from all configured engines
      const fbUrls = new Set();
      for (const engine of engines) {
        if (isCancelled()) break;
        await logEvent("DEBUG", `SERP dork on ${engine.name} for: "${keyword}"`);
        const urls = await fetchFacebookUrlsFromSerp(keyword, engine, maxPerKeyword);
        urls.forEach((u) => fbUrls.add(u));
        await randomDelay(2000, 4000); // Polite delay between SERP calls
      }

      await logEvent("INFO", `Found ${fbUrls.size} Facebook URLs for "${keyword}"`);

      // Deep-visit each Facebook profile
      const urlList = [...fbUrls].slice(0, maxPerKeyword);
      for (let ui = 0; ui < urlList.length; ui++) {
        if (isCancelled()) break;
        const url = urlList[ui];

        const { context, page } = await createContext(browser);
        try {
          await logEvent("DEBUG", `Visiting: ${url}`);
          const extracted = await scrapeFacebookProfile(page, url);

          // Save to DB
          await db("social_results").insert({
            job_id: jobId,
            source: "FACEBOOK",
            keyword,
            name: extracted.name,
            phone: extracted.phone,
            email: extracted.email,
            address: extracted.address,
            title: extracted.title,
            description: extracted.description?.substring(0, 1000),
            profile_url: url,
            raw_data: JSON.stringify(extracted.rawData),
          });

          totalExtracted++;
          await logEvent("INFO", `✓ Extracted record ${totalExtracted} — ${extracted.name || url}`);
        } catch (err) {
          await logEvent("WARN", `Failed to scrape ${url}: ${err.message}`);
        } finally {
          await context.close();
          await randomDelay(1500, 3500);
        }

        // Update progress
        const overallProgress = ((ki / totalSteps) + (ui + 1) / (urlList.length * totalSteps)) * 100;
        await updateProgress(overallProgress, totalExtracted);
      }

      // Inter-keyword delay
      if (ki < keywords.length - 1 && !isCancelled()) {
        await randomDelay(3000, 7000);
      }
    }
  } finally {
    await closeBrowser(browser);
  }
}

module.exports = { run };
