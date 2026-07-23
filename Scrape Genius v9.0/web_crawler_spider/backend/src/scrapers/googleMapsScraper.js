/**
 * Google Maps Scraper (Full Playwright Implementation)
 * 
 * Replaces the TODO stub in maps.routes.js with a complete automation:
 *   1. Navigate to maps.google.com
 *   2. Search for keyword + optional location
 *   3. Scroll the results panel to load all listings
 *   4. Click each listing → extract side panel data
 *   5. Optionally deep-visit the business website to find email + social links
 *   6. Save to maps_job_results table
 * 
 * Key Fields Extracted:
 *   Business Name | Phone | Address | Website | Email
 *   Instagram | Facebook | LinkedIn | Twitter | Rating | Reviews
 */

"use strict";

const cheerio = require("cheerio");
const axios = require("axios");
const db = require("../config/database");
const { createBrowser, createContext, closeBrowser, navigateTo } = require("../services/browserEngine");
const { randomDelay, humanScroll } = require("../services/antiRobotService");
// NEW: Import AI enhancement service
const { extractStructuredData, analyzeReviewSentiment, calculateLeadScore } = require("../services/aiEnhancementService");

// ─────────────────────────────────────────────────────────────────────────────
// Regex patterns
// ─────────────────────────────────────────────────────────────────────────────

const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
const ASSET_EXT_RE = /\.(png|jpe?g|svg|webp|gif|ico)$/i;
const SOCIAL_RE = {
  instagram: /(?:instagram\.com|instagr\.am)\/([a-zA-Z0-9_.]+)/i,
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

function extractSocials(html) {
  const result = {};
  for (const [platform, re] of Object.entries(SOCIAL_RE)) {
    const m = html.match(re);
    if (m) result[platform] = m[0].startsWith("http") ? m[0] : `https://${m[0]}`;
  }
  return result;
}

/**
 * NEW: Enhanced website scraping with AI
 */
async function scrapeBusinessWebsiteWithAI(websiteUrl) {
  const out = { 
    email: null, 
    instagram: null, 
    facebook: null, 
    linkedin: null, 
    twitter: null,
    additional_emails: [],
    phone_numbers: [],
    business_description: null,
    services: []
  };
  
  try {
    const { data: html } = await axios.get(websiteUrl, {
      timeout: 12000,
      headers: { "User-Agent": "Mozilla/5.0 (compatible; ScrapeGeniusPro/9.0; +https://scrapegeniuspro.com)" },
      maxRedirects: 5,
    });
    
    // Extract basic data with existing method
    const emails = extractEmails(html);
    const socials = extractSocials(html);
    
    // NEW: Use AI to extract additional structured data
    const aiExtraction = await extractStructuredData(html, {
      email: "primary email address",
      additional_emails: "array of additional email addresses",
      phone_numbers: "array of phone numbers",
      business_description: "brief description of the business",
      services: "array of services offered"
    }, "Extract business contact information from website HTML");
    
    out.email = emails[0] || aiExtraction.email || null;
    out.additional_emails = [...new Set([...emails.slice(1), ...(aiExtraction.additional_emails || [])])];
    out.phone_numbers = aiExtraction.phone_numbers || [];
    out.business_description = aiExtraction.business_description || null;
    out.services = aiExtraction.services || [];
    
    Object.assign(out, socials);
  } catch (error) {
    console.error(`Error scraping website ${websiteUrl}:`, error.message);
  }
  
  return out;
}

/**
 * Dismisses Google's cookie/GDPR consent interstitial if it appears.
 * Every scrape job starts a brand-new browser context (no cookies), so this
 * page shows up frequently — especially from EU-region IPs/proxies — and
 * blocks the results feed from ever rendering if left unhandled.
 */
async function dismissConsentDialog(page, logEvent = () => {}) {
  const CONSENT_BUTTON_SELECTORS = [
    'button:has-text("Accept all")',
    'button:has-text("I agree")',
    'form[action*="consent.google.com"] button',
    'button[aria-label="Accept all"]',
    'button[aria-label="I agree"]',
    '#L2AGLb', // Google's stable "Accept all" button id on many consent pages
  ];

  if (/consent\.google\.com/.test(page.url())) {
    await logEvent("DEBUG", "Consent interstitial detected — attempting dismissal");
  }

  for (const selector of CONSENT_BUTTON_SELECTORS) {
    try {
      const btn = page.locator(selector).first();
      if (await btn.isVisible({ timeout: 1500 })) {
        await btn.click({ timeout: 3000 });
        await randomDelay(1000, 2000);
        return true;
      }
    } catch {
      // Selector not present — try the next one
    }
  }
  return false;
}

/**
 * Scrolls the Maps results panel to load all listings (up to maxListings).
 * Returns array of listing elements.
 */
async function collectListingElements(page, maxListings = 60, logEvent = () => {}) {
  // The results panel selector (Google Maps DOM). Google occasionally tweaks
  // markup, so we try a couple of known variants rather than a single one.
  const PANEL_SELECTORS = ['div[role="feed"]', 'div[aria-label][role="feed"]'];

  await dismissConsentDialog(page, logEvent);

  let panelSelector = null;
  for (const selector of PANEL_SELECTORS) {
    try {
      await page.waitForSelector(selector, { timeout: 15000 });
      panelSelector = selector;
      break;
    } catch {
      // try next selector
    }
  }

  if (!panelSelector) {
    // Still nothing — maybe the consent dialog appeared after the initial
    // check, or Google served a block/CAPTCHA page. Log enough to diagnose.
    const url = page.url();
    const title = await page.title().catch(() => "");
    await logEvent(
      "WARN",
      `Results feed never appeared (url="${url}", title="${title}"). ` +
        `Possibly a consent/CAPTCHA page or a changed Maps selector.`
    );
    return [];
  }

  const listings = new Set();
  let prevCount = 0;
  let stallCount = 0;

  while (listings.size < maxListings && stallCount < 4) {
    // Collect all listing link hrefs currently in the DOM
    const hrefs = await page.$$eval(
      `${panelSelector} a[href*="/maps/place/"]`,
      (els) => els.map((el) => el.getAttribute("href"))
    );
    hrefs.forEach((h) => h && listings.add(h));

    if (listings.size === prevCount) {
      stallCount++;
    } else {
      stallCount = 0;
    }
    prevCount = listings.size;

    // Scroll panel down
    await page.evaluate((selector) => {
      const panel = document.querySelector(selector);
      if (panel) panel.scrollTop += 800;
    }, panelSelector);

    await randomDelay(800, 1800);
  }

  return [...listings];
}

/**
 * NEW: Extract and analyze Google Maps reviews with AI
 */
async function extractAndAnalyzeReviews(page, businessName) {
  try {
    // Attempt to find and click on reviews tab if available
    const reviewSelectors = [
      'button[aria-label*="review"]',
      'button[data-value*="review"]',
      'span:contains("review")',
      'button:contains("Review")',
      'button:contains("Reviews")'
    ];
    
    for (const selector of reviewSelectors) {
      try {
        const reviewButton = await page.$(selector);
        if (reviewButton) {
          await reviewButton.click();
          await randomDelay(2000, 3000);
          break;
        }
      } catch (e) {
        continue;
      }
    }
    
    // Extract review text
    const reviewTexts = await page.evaluate(() => {
      const reviewElements = document.querySelectorAll('span[jsan="7.n"]');
      return Array.from(reviewElements).slice(0, 10).map(el => el.textContent.trim()).filter(text => text);
    });
    
    if (reviewTexts.length > 0) {
      // Analyze with AI
      const sentimentAnalysis = await analyzeReviewSentiment(reviewTexts);
      return {
        reviews: reviewTexts,
        sentiment: sentimentAnalysis
      };
    }
    
    return { reviews: [], sentiment: null };
  } catch (error) {
    console.error(`Error extracting reviews for ${businessName}:`, error.message);
    return { reviews: [], sentiment: null };
  }
}

/**
 * NEW: Enhanced listing scraping with AI analysis
 */
async function scrapeListingPageWithAI(page, href) {
  const listingData = await scrapeListingPage(page, href); // Original scraping
  
  // NEW: Additional AI analysis if deepVisit is enabled
  if (listingData.website) {
    try {
      const websiteAnalysis = await scrapeBusinessWebsiteWithAI(listingData.website);
      
      // Combine original data with AI-enhanced data
      return {
        ...listingData,
        ...websiteAnalysis
      };
    } catch (error) {
      console.error(`AI analysis failed for ${listingData.businessName}:`, error.message);
      return listingData; // Return original data if AI fails
    }
  }
  
  return listingData;
}

// ORIGINAL scrapeListingPage function (unchanged for compatibility)
async function scrapeListingPage(page, href) {
  const detailUrl = /^https?:\/\//i.test(href) ? href : `https://www.google.com${href}`;
  await page.goto(detailUrl, { waitUntil: "networkidle", timeout: 30000 });

  // Wait for the side panel to load
  await page.waitForSelector('div[role="main"]', { timeout: 10000 });

  // Extract all text content from the side panel
  const textContent = await page.evaluate(() => {
    const mainDiv = document.querySelector('div[role="main"]');
    return mainDiv ? mainDiv.innerText : "";
  });

  // Business name: usually the first h1
  const nameMatch = textContent.match(/^(.+?)\n/);
  const businessName = nameMatch ? nameMatch[1].trim() : "";

  // Phone: look for common phone patterns
  const phoneMatch = textContent.match(/\+?\d[\d\s\-\(\)]{8,}\d/);
  const phone = phoneMatch ? phoneMatch[0].trim() : null;

  // Address: typically after the name, before the rating
  const addressMatch = textContent.match(/^[^\n]*\n([^\n]*\d[^\n]*)/m);
  let address = addressMatch ? addressMatch[1].trim() : null;
  if (address && address === businessName) {
    // Address extraction failed, try alternate method
    const addressRegex = /(?:Address|Location)[:\s]+([^\n]+)/i;
    const altAddressMatch = textContent.match(addressRegex);
    address = altAddressMatch ? altAddressMatch[1].trim() : null;
  }

  // Website: look for "Website" label followed by URL
  const websiteMatch = textContent.match(/Website[:\s]+(https?:\/\/[^\s\n]+)/i);
  const website = websiteMatch ? websiteMatch[1].trim() : null;

  // Rating and reviews count
  const ratingMatch = textContent.match(/(\d+\.?\d*)\s*stars?/i);
  const rating = ratingMatch ? parseFloat(ratingMatch[1]) : null;

  const reviewsMatch = textContent.match(/(\d+)\s*reviews?/i);
  const reviewsCount = reviewsMatch ? parseInt(reviewsMatch[1], 10) : null;

  // Category (usually near the rating)
  const categoryMatch = textContent.match(/(?:·\s*)([A-Za-z\s]+?)(?:\s*·|$)/);
  const category = categoryMatch ? categoryMatch[1].trim() : null;

  // Coordinates from URL
  const coordMatch = href.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*),/);
  const latitude = coordMatch ? parseFloat(coordMatch[1]) : null;
  const longitude = coordMatch ? parseFloat(coordMatch[2]) : null;

  return {
    businessName,
    phone,
    address,
    website,
    rating,
    reviewsCount,
    category,
    latitude,
    longitude,
  };
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 * 
 * @param {number}   jobId
 * @param {string[]} keywords    Each keyword can be "query location" e.g. "restaurant Riyadh"
 * @param {object}   config      { maxPerKeyword: 60, deepVisit: true }
 * @param {object}   hooks       { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  // maxPerKeyword: 0 (or "0") means "no limit — collect every listing found"
  // NEW: Enable AI enhancement if configured
  const enableAI = config.enableAI !== false; // Default: enable AI enhancement
  // maxPerKeyword: 0 (or "0") means "no limit — collect every listing found"
  const maxPerKeyword = config.maxPerKeyword === 0 || config.maxPerKeyword === "0"
    ? Infinity
    : config.maxPerKeyword || 1000;
  const deepVisit = config.deepVisit !== false; // Default: visit website for email+social
  let totalExtracted = 0;

  const browser = await createBrowser({ headless: process.env.PLAYWRIGHT_HEADLESS !== "false" });

  try {
    for (let ki = 0; ki < keywords.length; ki++) {
      if (isCancelled()) break;
      const keyword = keywords[ki];
      await logEvent("INFO", `Google Maps search: "${keyword}" (${ki + 1}/${keywords.length})`);

      const { context, page } = await createContext(browser);

      try {
        // Navigate to Google Maps with the search query. Forcing hl=en keeps
        // listing text (category/address separators) in a parseable format —
        // without it Google may serve a locale based on IP/context, and the
        // address/category parsing below assumes English UI chrome.
        const mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(keyword)}/?hl=en`;
        await navigateTo(page, mapsUrl);
        await randomDelay(2000, 4000);

        // Scroll to collect listing hrefs
        const listingHrefs = await collectListingElements(page, maxPerKeyword, logEvent);
        await logEvent("INFO", `Collected ${listingHrefs.length} listing URLs for "${keyword}"`);

        for (let li = 0; li < listingHrefs.length; li++) {
          if (isCancelled()) break;
          const href = listingHrefs[li];

          const listingData = await scrapeListingPage(page, href);
          await randomDelay(1500, 3000);

          // Deep-visit website to get email + social links
          let websiteData = { 
            email: null, 
            instagram: null, 
            facebook: null, 
            linkedin: null, 
            twitter: null,
            additional_emails: [],
            phone_numbers: [],
            business_description: null,
            services: []
          };
          
          if (deepVisit && listingData.website) {
            await logEvent("DEBUG", `Deep-visiting website: ${listingData.website}`);
            // NEW: Use AI-enhanced website scraping if enabled
            websiteData = enableAI 
              ? await scrapeBusinessWebsiteWithAI(listingData.website)
              : await scrapeBusinessWebsite(listingData.website);
          }

          // NEW: Calculate lead score if AI is enabled
          let leadScore = null;
          if (enableAI) {
            const scrapedData = {
              ...listingData,
              ...websiteData
            };
            leadScore = calculateLeadScore(scrapedData);
          }

          // Save to maps_job_results
          await db("maps_job_results").insert({
            job_id: jobId,
            keyword,
            business_name: listingData.businessName,
            phone: listingData.phone,
            address: listingData.address,
            website: listingData.website,
            email: websiteData.email,
            rating: listingData.rating,
            reviews_count: listingData.reviewsCount,
            category: listingData.category,
            latitude: listingData.latitude,
            longitude: listingData.longitude,
            instagram: websiteData.instagram,
            facebook: websiteData.facebook,
            linkedin: websiteData.linkedin,
            twitter: websiteData.twitter,
            // NEW: Add AI-enhanced fields
            additional_emails: JSON.stringify(websiteData.additional_emails || []),
            business_description: websiteData.business_description,
            services: JSON.stringify(websiteData.services || []),
            lead_score: leadScore ? leadScore.score : null,
            lead_rating: leadScore ? leadScore.rating : null,
            raw_data: JSON.stringify({ listing: listingData, website: websiteData }),
          });

          totalExtracted++;
          const websiteLog = listingData.website ? ` | Link: ${listingData.website}` : "";
          await logEvent("INFO", `✓ Maps record ${totalExtracted}: ${listingData.businessName || href}${websiteLog}`);

          const progress = ((ki + (li + 1) / listingHrefs.length) / keywords.length) * 100;
          await updateProgress(progress, totalExtracted);
        }
      } finally {
        await context.close();
      }

      if (ki < keywords.length - 1 && !isCancelled()) {
        await randomDelay(4000, 8000);
      }
    }
  } finally {
    await closeBrowser(browser);
  }
}

module.exports = { run };
