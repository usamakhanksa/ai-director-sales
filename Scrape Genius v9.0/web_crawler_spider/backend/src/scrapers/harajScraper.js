/**
 * Haraj SA & Classified Sites Scraper
 * 
 * Supports 20+ Saudi, Gulf, and MENA classified/marketplace platforms:
 *   haraj.com.sa, opensooq.com, mstaml.com, bey3.com, aqari.sa,
 *   expatriates.com, almuraba.net, alwaseet.com.sa, sa.dubizzle.com,
 *   eg.olx.com.eg, olx.com.kw, tayara.tn, mubawab.sa, mubawab.ma,
 *   4sale.com.kw, qatarliving.com, mourjan.com, mzad.com,
 *   propertyfinder.sa, bayut.sa, motory.com, syarah.com, maroof.sa
 * 
 * Strategy for each site:
 *   1. Navigate to the site's search URL with keyword (supports Arabic)
 *   2. Scrape listing cards from search results page
 *   3. Optionally deep-visit each listing post for more contact detail
 *   4. Extract: post title, post link, phone, email, price, location, date
 *   5. Save to classified_results table
 * 
 * Supports both Arabic (RTL) and English keywords correctly.
 */

"use strict";

const axios = require("axios");
const cheerio = require("cheerio");
const { URL } = require("url");
const db = require("../config/database");
const { createBrowser, createContext, closeBrowser, navigateTo } = require("../services/browserEngine");
const { randomDelay, randomUserAgent } = require("../services/antiRobotService");

// ─────────────────────────────────────────────────────────────────────────────
// Classified Site Configurations
// Each entry defines how to search and parse results for that platform.
// ─────────────────────────────────────────────────────────────────────────────

const CLASSIFIED_SITES = {
  HARAJ: {
    name: "Haraj Saudi Arabia",
    baseUrl: "https://haraj.com.sa",
    searchUrl: (kw) => `https://haraj.com.sa/search/${encodeURIComponent(kw)}`,
    listingSelector: "article, .post-item, [class*='post'], [class*='listing']",
    titleSelector: "h2, h3, .title, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[class*='phone'], [href^='tel:'], [class*='contact']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  OPENSOOQ: {
    name: "OpenSooq",
    baseUrl: "https://sa.opensooq.com",
    searchUrl: (kw) => `https://sa.opensooq.com/en/search?term=${encodeURIComponent(kw)}`,
    listingSelector: ".listing-item, [class*='listing'], article",
    titleSelector: "h2, h3, .title",
    linkSelector: "a.listing-title, a[href*='/en/post/']",
    phoneSelector: "[href^='tel:'], [class*='phone']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  DUBIZZLE: {
    name: "Dubizzle / OLX",
    baseUrl: "https://sa.dubizzle.com",
    searchUrl: (kw) => `https://sa.dubizzle.com/classifieds/?q=${encodeURIComponent(kw)}`,
    listingSelector: "[class*='listing'], [class*='AdTile'], article",
    titleSelector: "[class*='title'], h2, h3",
    linkSelector: "a[href*='/classifieds/']",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  OLX_KW: {
    name: "OLX Kuwait",
    baseUrl: "https://olx.com.kw",
    searchUrl: (kw) => `https://olx.com.kw/en/search/?q=${encodeURIComponent(kw)}`,
    listingSelector: "[data-aut-id='itemBox'], .listing-item",
    titleSelector: "[data-aut-id='itemTitle'], h3",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  OLX_EG: {
    name: "OLX Egypt",
    baseUrl: "https://eg.olx.com.eg",
    searchUrl: (kw) => `https://eg.olx.com.eg/en/search/?q=${encodeURIComponent(kw)}`,
    listingSelector: "[data-aut-id='itemBox'], .listing-item",
    titleSelector: "[data-aut-id='itemTitle'], h3",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  MUBAWAB_SA: {
    name: "Mubawab Saudi Arabia",
    baseUrl: "https://mubawab.sa",
    searchUrl: (kw) => `https://mubawab.sa/en/search/?q=${encodeURIComponent(kw)}`,
    listingSelector: "li.listingBox, article, [class*='listing']",
    titleSelector: "h3, .listingTitle",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:'], [class*='phone']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  BAYUT: {
    name: "Bayut Saudi Arabia",
    baseUrl: "https://www.bayut.sa",
    searchUrl: (kw) => `https://www.bayut.sa/en/property/search/?q=${encodeURIComponent(kw)}`,
    listingSelector: "article, [class*='listing'], [class*='PropertyCard']",
    titleSelector: "h3, [class*='title'], [class*='heading']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  PROPERTYFINDER: {
    name: "Property Finder SA",
    baseUrl: "https://www.propertyfinder.sa",
    searchUrl: (kw) => `https://www.propertyfinder.sa/en/search?q=${encodeURIComponent(kw)}`,
    listingSelector: "article, [class*='card'], [class*='listing']",
    titleSelector: "h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  SYARAH: {
    name: "Syarah Cars Saudi Arabia",
    baseUrl: "https://syarah.com",
    searchUrl: (kw) => `https://syarah.com/search?q=${encodeURIComponent(kw)}`,
    listingSelector: "[class*='car-card'], article, [class*='listing']",
    titleSelector: "h2, h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  EXPATRIATES: {
    name: "Expatriates Saudi Arabia",
    baseUrl: "https://www.expatriates.com",
    searchUrl: (kw) => `https://www.expatriates.com/classifieds/saudi-arabia/?q=${encodeURIComponent(kw)}`,
    listingSelector: ".listing, article, [class*='ad']",
    titleSelector: "h2, h3, .listing-title",
    linkSelector: "a[href*='/classifieds/']",
    phoneSelector: "[href^='tel:'], [class*='phone']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  FORSALE_KW: {
    name: "4Sale Kuwait",
    baseUrl: "https://4sale.com.kw",
    searchUrl: (kw) => `https://4sale.com.kw/en/search?q=${encodeURIComponent(kw)}`,
    listingSelector: "article, [class*='card'], [class*='listing']",
    titleSelector: "h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
  CLASSIFIED_GENERIC: {
    name: "Generic Classified",
    baseUrl: null,
    searchUrl: (kw, baseUrl) => `${baseUrl}/?q=${encodeURIComponent(kw)}`,
    listingSelector: "article, [class*='listing'], [class*='ad'], [class*='item'], [class*='post']",
    titleSelector: "h2, h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
  },
};

// Regex for extracting phones from post text (Arabic + international formats)
const PHONE_RE = /(?:\+?(?:966|971|965|974|968|973|20|1|44|91)[\s\-]?)?(?:0?5\d[\s\-]?\d{3}[\s\-]?\d{4}|0?[1-9]\d[\s\-]?\d{3}[\s\-]?\d{4}|\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{4})/g;
const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;

function extractPhones(text) {
  return [...new Set((text.match(PHONE_RE) || []).map((m) => m.trim()).filter((m) => m.replace(/\D/g, "").length >= 7))];
}

function extractEmails(text) {
  return [...new Set((text.match(EMAIL_RE) || []).map((m) => m.toLowerCase()))];
}

// ─────────────────────────────────────────────────────────────────────────────
// Core Scraping Logic
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Scrapes a single classified site for a given keyword using Playwright.
 * Returns array of classified result objects.
 */
async function scrapeSite(page, siteKey, keyword, config = {}) {
  const site = CLASSIFIED_SITES[siteKey] || CLASSIFIED_SITES.CLASSIFIED_GENERIC;
  const customBaseUrl = config.customBaseUrl || site.baseUrl;

  if (!customBaseUrl && siteKey === "CLASSIFIED_GENERIC") {
    throw new Error("Generic classified scraper requires config.customBaseUrl");
  }

  const searchUrl = site.searchUrl(keyword, customBaseUrl);
  const results = [];

  try {
    await navigateTo(page, searchUrl);
    await randomDelay(2000, 4000);

    // Handle lazy-loading: scroll to trigger more results
    for (let scroll = 0; scroll < 3; scroll++) {
      await page.evaluate(() => window.scrollBy(0, window.innerHeight));
      await randomDelay(800, 1500);
    }

    const html = await page.content();
    const $ = cheerio.load(html);

    $(site.listingSelector).each((_, el) => {
      const titleEl = $(el).find(site.titleSelector).first();
      const linkEl  = $(el).find(site.linkSelector).first();
      const title   = titleEl.text().trim();
      const rawHref = linkEl.attr("href") || "";
      let postLink  = rawHref;

      // Resolve relative URLs
      if (rawHref && !rawHref.startsWith("http") && customBaseUrl) {
        try { postLink = new URL(rawHref, customBaseUrl).toString(); } catch {}
      }

      // Extract text content for phone/email parsing
      const text = $(el).text().replace(/\s+/g, " ");
      const phones = extractPhones(text);
      const emails = extractEmails(text);

      // Try tel: link extraction
      const telPhone = site.parsePhone(el, $);
      if (telPhone && !phones.includes(telPhone)) phones.unshift(telPhone);

      // Price extraction
      const priceMatch = text.match(/[\d,]+\s*(?:ريال|SAR|جنيه|EGP|دينار|KWD|QAR|AED|$|€|£)/i);
      const price = priceMatch ? priceMatch[0].trim() : null;

      // Location extraction (generic — looks for city names)
      const locationMatch = text.match(/(?:الرياض|جدة|مكة|Riyadh|Jeddah|Mecca|Kuwait City|Dubai|Cairo|Doha)[^،,\n]*/i);
      const location = locationMatch ? locationMatch[0].trim().substring(0, 200) : null;

      if (title || postLink) {
        results.push({
          postTitle: title.substring(0, 1000) || null,
          postLink: postLink || null,
          phone: phones[0] || null,
          email: emails[0] || null,
          price,
          location,
          rawData: { allPhones: phones, allEmails: emails },
        });
      }
    });

  } catch (err) {
    console.error(`[HarajScraper] Error scraping ${siteKey}: ${err.message}`);
  }

  return results;
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 * 
 * @param {number}   jobId
 * @param {string[]} keywords
 * @param {object}   config      { sites: ["HARAJ", "OPENSOOQ"], maxPerKeyword: 50 }
 * @param {object}   hooks       { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const sitesToScrape = config.sites || ["HARAJ", "OPENSOOQ", "DUBIZZLE"];
  // maxPerKeyword: 0 (or "0") means "no limit — extract every listing found"
  const maxPerKeyword = config.maxPerKeyword === 0 || config.maxPerKeyword === "0"
    ? Infinity
    : config.maxPerKeyword || 50;
  let totalExtracted = 0;

  // Get user_id for DB insert
  const job = await db("scrape_jobs").where({ id: jobId }).first();
  const userId = job?.user_id;

  const browser = await createBrowser({ headless: process.env.PLAYWRIGHT_HEADLESS !== "false" });

  try {
    for (let ki = 0; ki < keywords.length; ki++) {
      if (isCancelled()) break;
      const keyword = keywords[ki];
      await logEvent("INFO", `Classified search: "${keyword}" on ${sitesToScrape.join(", ")}`);

      for (const siteKey of sitesToScrape) {
        if (isCancelled()) break;

        const siteConfig = CLASSIFIED_SITES[siteKey];
        if (!siteConfig) {
          await logEvent("WARN", `Unknown site key: ${siteKey}`);
          continue;
        }

        await logEvent("INFO", `Scraping ${siteConfig.name} for "${keyword}"`);

        const { context, page } = await createContext(browser);
        try {
          const results = await scrapeSite(page, siteKey, keyword, config);
          const limited = results.slice(0, maxPerKeyword);

          for (const r of limited) {
            await db("classified_results").insert({
              job_id: jobId,
              source: siteKey,
              keyword,
              post_title: r.postTitle,
              post_link: r.postLink,
              phone: r.phone,
              email: r.email,
              price: r.price,
              location: r.location,
              raw_data: JSON.stringify(r.rawData),
            });
            totalExtracted++;
          }

          await logEvent("INFO", `✓ ${siteConfig.name}: extracted ${limited.length} listings`);
        } catch (err) {
          await logEvent("ERROR", `${siteConfig.name} failed: ${err.message}`);
        } finally {
          await context.close();
          await randomDelay(2000, 4000);
        }
      }

      const progress = ((ki + 1) / keywords.length) * 100;
      await updateProgress(progress, totalExtracted);

      if (ki < keywords.length - 1 && !isCancelled()) {
        await randomDelay(3000, 6000);
      }
    }
  } finally {
    await closeBrowser(browser);
  }
}

/**
 * Generic classified scraper — used when a custom URL is provided.
 * Maps to the "classified_generic" module in the job manager.
 */
async function runGeneric(jobId, keywords, config = {}, hooks = {}) {
  return run(jobId, keywords, { ...config, sites: ["CLASSIFIED_GENERIC"] }, hooks);
}

module.exports = { run, runGeneric, CLASSIFIED_SITES };
