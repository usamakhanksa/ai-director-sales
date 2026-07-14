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

// `pageUrl(kw, pageNum, baseUrl)` builds the URL for page N (1-indexed) of results.
// `showPhoneSelector` / `showEmailSelector` are "reveal contact" buttons clicked on the
// listing DETAIL page during deep-scrape (config.deepScrape !== false).
const CLASSIFIED_SITES = {
  HARAJ: {
    name: "Haraj Saudi Arabia",
    baseUrl: "https://haraj.com.sa",
    searchUrl: (kw) => `https://haraj.com.sa/search/${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://haraj.com.sa/search/${encodeURIComponent(kw)}?page=${p}`,
    listingSelector: "article, .post-item, [class*='post'], [class*='listing']",
    titleSelector: "h2, h3, .title, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[class*='phone'], [href^='tel:'], [class*='contact']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('اظهار الرقم'), button:has-text('عرض الرقم'), [class*='show-phone'], [class*='reveal']",
  },
  OPENSOOQ: {
    name: "OpenSooq",
    baseUrl: "https://sa.opensooq.com",
    searchUrl: (kw) => `https://sa.opensooq.com/en/search?term=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://sa.opensooq.com/en/search?term=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: ".listing-item, [class*='listing'], article",
    titleSelector: "h2, h3, .title",
    linkSelector: "a.listing-title, a[href*='/en/post/']",
    phoneSelector: "[href^='tel:'], [class*='phone']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Show'), [class*='show-phone'], [class*='reveal-phone']",
  },
  DUBIZZLE: {
    name: "Dubizzle / OLX",
    baseUrl: "https://sa.dubizzle.com",
    searchUrl: (kw) => `https://sa.dubizzle.com/classifieds/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://sa.dubizzle.com/classifieds/?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "[class*='listing'], [class*='AdTile'], article",
    titleSelector: "[class*='title'], h2, h3",
    linkSelector: "a[href*='/classifieds/']",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Show'), [class*='show-phone'], [class*='call']",
  },
  OLX_KW: {
    name: "OLX Kuwait",
    baseUrl: "https://olx.com.kw",
    searchUrl: (kw) => `https://olx.com.kw/en/search/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://olx.com.kw/en/search/?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "[data-aut-id='itemBox'], .listing-item",
    titleSelector: "[data-aut-id='itemTitle'], h3",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "[data-aut-id='btnChatCall'], button:has-text('Show'), [class*='show-phone']",
  },
  OLX_EG: {
    name: "OLX Egypt",
    baseUrl: "https://eg.olx.com.eg",
    searchUrl: (kw) => `https://eg.olx.com.eg/en/search/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://eg.olx.com.eg/en/search/?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "[data-aut-id='itemBox'], .listing-item",
    titleSelector: "[data-aut-id='itemTitle'], h3",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "[data-aut-id='btnChatCall'], button:has-text('Show'), [class*='show-phone']",
  },
  MUBAWAB_SA: {
    name: "Mubawab Saudi Arabia",
    baseUrl: "https://mubawab.sa",
    searchUrl: (kw) => `https://mubawab.sa/en/search/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://mubawab.sa/en/search/?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "li.listingBox, article, [class*='listing']",
    titleSelector: "h3, .listingTitle",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:'], [class*='phone']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Show'), [class*='show-phone'], [class*='phoneBtn']",
  },
  BAYUT: {
    name: "Bayut Saudi Arabia",
    baseUrl: "https://www.bayut.sa",
    searchUrl: (kw) => `https://www.bayut.sa/en/property/search/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://www.bayut.sa/en/property/search/?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "article, [class*='listing'], [class*='PropertyCard']",
    titleSelector: "h3, [class*='title'], [class*='heading']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Call'), [class*='phone-btn'], [class*='call-button']",
  },
  PROPERTYFINDER: {
    name: "Property Finder SA",
    baseUrl: "https://www.propertyfinder.sa",
    searchUrl: (kw) => `https://www.propertyfinder.sa/en/search?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://www.propertyfinder.sa/en/search?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "article, [class*='card'], [class*='listing']",
    titleSelector: "h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Call'), [class*='phone-btn'], [class*='reveal']",
  },
  SYARAH: {
    name: "Syarah Cars Saudi Arabia",
    baseUrl: "https://syarah.com",
    searchUrl: (kw) => `https://syarah.com/search?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://syarah.com/search?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "[class*='car-card'], article, [class*='listing']",
    titleSelector: "h2, h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('اظهار'), button:has-text('Show'), [class*='show-phone']",
  },
  EXPATRIATES: {
    name: "Expatriates Saudi Arabia",
    baseUrl: "https://www.expatriates.com",
    searchUrl: (kw) => `https://www.expatriates.com/classifieds/saudi-arabia/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://www.expatriates.com/classifieds/saudi-arabia/?q=${encodeURIComponent(kw)}&p=${p}`,
    listingSelector: ".listing, article, [class*='ad']",
    titleSelector: "h2, h3, .listing-title",
    linkSelector: "a[href*='/classifieds/']",
    phoneSelector: "[href^='tel:'], [class*='phone']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showEmailSelector: "a[href*='mailto:'], button:has-text('Email'), [class*='reply']",
  },
  FORSALE_KW: {
    name: "4Sale Kuwait",
    baseUrl: "https://4sale.com.kw",
    searchUrl: (kw) => `https://4sale.com.kw/en/search?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p) => `https://4sale.com.kw/en/search?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "article, [class*='card'], [class*='listing']",
    titleSelector: "h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Show'), [class*='show-phone']",
  },
  CLASSIFIED_GENERIC: {
    name: "Generic Classified",
    baseUrl: null,
    searchUrl: (kw, baseUrl) => `${baseUrl}/?q=${encodeURIComponent(kw)}`,
    pageUrl: (kw, p, baseUrl) => `${baseUrl}/?q=${encodeURIComponent(kw)}&page=${p}`,
    listingSelector: "article, [class*='listing'], [class*='ad'], [class*='item'], [class*='post']",
    titleSelector: "h2, h3, [class*='title']",
    linkSelector: "a[href]",
    phoneSelector: "[href^='tel:']",
    parsePhone: (el, $) => $(el).find("[href^='tel:']").attr("href")?.replace("tel:", "") || null,
    showPhoneSelector: "button:has-text('Show'), button:has-text('Call'), [class*='show-phone'], [class*='reveal']",
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
 * Scrapes a single search-results page already loaded into `page` and
 * returns the parsed listing objects (title/link/phone/email/price/location).
 */
function parseListingsFromHtml(html, site, customBaseUrl) {
  const $ = cheerio.load(html);
  const items = [];

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
    const priceMatch = text.match(/[\d,]+\s*(?:ريال|SAR|جنيه|EGP|دينار|KWD|QAR|AED|\$|€|£)/i);
    const price = priceMatch ? priceMatch[0].trim() : null;

    // Location extraction (generic — looks for city names)
    const locationMatch = text.match(/(?:الرياض|جدة|مكة|Riyadh|Jeddah|Mecca|Kuwait City|Dubai|Cairo|Doha)[^،,\n]*/i);
    const location = locationMatch ? locationMatch[0].trim().substring(0, 200) : null;

    if (title || postLink) {
      items.push({
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

  return items;
}

/**
 * Scrolls the page to the bottom repeatedly to trigger lazy-loaded /
 * infinite-scroll content until the document height stops growing.
 */
async function autoScroll(page, maxSteps = 8) {
  let lastHeight = 0;
  for (let i = 0; i < maxSteps; i++) {
    const height = await page.evaluate(() => document.body.scrollHeight);
    if (height === lastHeight) break;
    lastHeight = height;
    await page.evaluate(() => window.scrollBy(0, document.body.scrollHeight));
    await randomDelay(800, 1500);
  }
}

/**
 * Clicks a "Load more" style button repeatedly (if present) so subsequent
 * scrapes see the fully expanded listing set.
 */
async function clickLoadMore(page, maxClicks = 10) {
  for (let i = 0; i < maxClicks; i++) {
    const btn = await page
      .locator("button:has-text('Load more'), button:has-text('عرض المزيد'), button:has-text('تحميل المزيد'), [class*='load-more']")
      .first();
    if (!(await btn.count().catch(() => 0))) break;
    try {
      await btn.click({ timeout: 3000 });
    } catch {
      break;
    }
    await randomDelay(1200, 2200);
  }
}

/**
 * Visits an individual listing's detail page, clicks any "show phone" /
 * "show email" reveal buttons, and extracts every phone/email found in the
 * fully rendered DOM (much more reliable than parsing the search-result card).
 */
async function deepScrapeListing(page, site, listingUrl) {
  const contact = { phone: null, email: null, allPhones: [], allEmails: [] };
  if (!listingUrl) return contact;

  try {
    await navigateTo(page, listingUrl);
    await randomDelay(1000, 2000);

    // Attempt to click any "reveal phone / email" buttons.
    for (const selectorKey of ["showPhoneSelector", "showEmailSelector"]) {
      const selector = site[selectorKey];
      if (!selector) continue;
      try {
        const locator = page.locator(selector).first();
        if (await locator.count().catch(() => 0)) {
          await locator.click({ timeout: 3000 });
          await randomDelay(800, 1500);
        }
      } catch {
        // Button not present / not clickable — safe to ignore and fall back to regex extraction.
      }
    }

    const html = await page.content();
    const $ = cheerio.load(html);
    const bodyText = $("body").text().replace(/\s+/g, " ");

    const phones = extractPhones(bodyText);
    const telLinks = $("[href^='tel:']")
      .map((_, el) => $(el).attr("href").replace("tel:", "").trim())
      .get();
    const emails = extractEmails(bodyText);
    const mailtoLinks = $("[href^='mailto:']")
      .map((_, el) => $(el).attr("href").replace("mailto:", "").split("?")[0].trim())
      .get();

    const allPhones = [...new Set([...telLinks, ...phones])];
    const allEmails = [...new Set([...mailtoLinks, ...emails])];

    contact.phone = allPhones[0] || null;
    contact.email = allEmails[0] || null;
    contact.allPhones = allPhones;
    contact.allEmails = allEmails;
  } catch (err) {
    console.error(`[HarajScraper] Deep-scrape failed for ${listingUrl}: ${err.message}`);
  }

  return contact;
}

/**
 * Scrapes a single classified site for a given keyword using Playwright,
 * walking every results page (no artificial limit) and optionally
 * deep-visiting each listing to reveal phone/email contact details.
 *
 * config.maxPages   — 0/"0" = unlimited (default 0). Stops early once a page
 *                      yields zero new listings.
 * config.deepScrape — false disables per-listing detail visits (faster, but
 *                      phone/email accuracy relies on the search-card text only).
 */
async function scrapeSite(page, siteKey, keyword, config = {}) {
  const site = CLASSIFIED_SITES[siteKey] || CLASSIFIED_SITES.CLASSIFIED_GENERIC;
  const customBaseUrl = config.customBaseUrl || site.baseUrl;

  if (!customBaseUrl && siteKey === "CLASSIFIED_GENERIC") {
    throw new Error("Generic classified scraper requires config.customBaseUrl");
  }

  const maxPages = config.maxPages === 0 || config.maxPages === "0" || config.maxPages == null
    ? Infinity
    : Number(config.maxPages);
  const deepScrape = config.deepScrape !== false;

  const seenLinks = new Set();
  const results = [];

  try {
    for (let pageNum = 1; pageNum <= maxPages; pageNum++) {
      const url = pageNum === 1
        ? site.searchUrl(keyword, customBaseUrl)
        : (site.pageUrl ? site.pageUrl(keyword, pageNum, customBaseUrl) : null);

      if (!url) break; // Site has no pagination strategy — single page only.

      await navigateTo(page, url);
      await randomDelay(1500, 3000);

      // Trigger lazy-loading via scroll + "load more" clicks before parsing.
      await autoScroll(page);
      await clickLoadMore(page);

      const html = await page.content();
      const pageItems = parseListingsFromHtml(html, site, customBaseUrl);

      // De-dupe against everything seen so far across pages.
      const freshItems = pageItems.filter((item) => {
        const key = item.postLink || item.postTitle;
        if (!key || seenLinks.has(key)) return false;
        seenLinks.add(key);
        return true;
      });

      if (freshItems.length === 0) break; // No new listings → end of pagination.

      results.push(...freshItems);
    }

    // Deep-visit each listing's own page to reliably capture phone/email
    // that are often hidden behind "show number" buttons on the card view.
    if (deepScrape) {
      for (const item of results) {
        if (!item.postLink) continue;
        const contact = await deepScrapeListing(page, site, item.postLink);
        if (contact.phone) item.phone = contact.phone;
        if (contact.email) item.email = contact.email;
        item.rawData.allPhones = [...new Set([...(item.rawData.allPhones || []), ...contact.allPhones])];
        item.rawData.allEmails = [...new Set([...(item.rawData.allEmails || []), ...contact.allEmails])];
        await randomDelay(1000, 2000);
      }
    }
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
 * @param {object}   config      {
 *                                 sites: ["HARAJ", "OPENSOOQ"],
 *                                 maxPerKeyword: 0,   // 0 = unlimited results stored per keyword/site
 *                                 maxPages: 0,        // 0 = unlimited pagination (walks until a page yields nothing new)
 *                                 deepScrape: true,   // visit each listing's own page to reveal phone/email
 *                               }
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
