/**
 * LinkedIn Email Finder
 * 
 * Strategy (no LinkedIn login required):
 *   1. For each keyword + country, run search engine dork queries across
 *      Google, Bing, and Yahoo (including country-specific TLDs):
 *        site:linkedin.com/in "keyword" email "@gmail" OR "@yahoo" OR "@company"
 *   2. Extract professional emails from SERP snippets (snippets often contain
 *      the email in the meta description / page preview)
 *   3. Apply educated email pattern guessing from name + company data
 *   4. Save results to social_results table
 * 
 * Key Fields Extracted:
 *   Name | Email | Title | Company | LinkedIn URL
 */

"use strict";

const axios = require("axios");
const cheerio = require("cheerio");
const db = require("../config/database");
const { randomDelay, randomUserAgent } = require("../services/antiRobotService");

// ─────────────────────────────────────────────────────────────────────────────
// Regex
// ─────────────────────────────────────────────────────────────────────────────

const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
const ASSET_EXT_RE = /\.(png|jpe?g|svg|webp|gif|ico)$/i;

// ─────────────────────────────────────────────────────────────────────────────
// Search Engine Configurations
// All major search engines + country TLD variants
// ─────────────────────────────────────────────────────────────────────────────

const SEARCH_ENGINES = [
  // Google global + country TLDs
  { name: "google",    url: (q) => `https://www.google.com/search?q=${encodeURIComponent(q)}&num=10` },
  { name: "google-uk", url: (q) => `https://www.google.co.uk/search?q=${encodeURIComponent(q)}&num=10` },
  { name: "google-sa", url: (q) => `https://www.google.com.sa/search?q=${encodeURIComponent(q)}&num=10` },
  { name: "google-ae", url: (q) => `https://www.google.ae/search?q=${encodeURIComponent(q)}&num=10` },
  { name: "google-in", url: (q) => `https://www.google.co.in/search?q=${encodeURIComponent(q)}&num=10` },
  { name: "google-de", url: (q) => `https://www.google.de/search?q=${encodeURIComponent(q)}&num=10` },
  // Bing
  { name: "bing",     url: (q) => `https://www.bing.com/search?q=${encodeURIComponent(q)}&count=10` },
  // Yahoo global + country
  { name: "yahoo",    url: (q) => `https://search.yahoo.com/search?p=${encodeURIComponent(q)}&n=10` },
  { name: "yahoo-uk", url: (q) => `https://uk.search.yahoo.com/search?p=${encodeURIComponent(q)}&n=10` },
  // DuckDuckGo
  { name: "duckduckgo", url: (q) => `https://duckduckgo.com/?q=${encodeURIComponent(q)}&ia=web` },
];

/**
 * Common email domain patterns for professional email guessing.
 * Given firstName + lastName + companyDomain, we generate these permutations.
 */
const EMAIL_PATTERNS = [
  (f, l, d) => `${f}.${l}@${d}`,
  (f, l, d) => `${f}${l}@${d}`,
  (f, l, d) => `${f[0]}${l}@${d}`,
  (f, l, d) => `${f}${l[0]}@${d}`,
  (f, l, d) => `${f}@${d}`,
  (f, l, d) => `${l}${f[0]}@${d}`,
];

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function extractEmails(text) {
  const matches = text.match(EMAIL_RE) || [];
  return [...new Set(matches
    .map((m) => m.toLowerCase().replace(/\.{2,}/g, "."))
    .filter((m) => !ASSET_EXT_RE.test(m) && m.includes("@") && m.length < 100)
  )];
}

/**
 * Extracts LinkedIn profile links from a SERP HTML page.
 * Returns array of { url, snippet, title } objects.
 */
function parseSerpResults(html) {
  const $ = cheerio.load(html);
  const results = [];

  // Parse Google-style results
  $("div.g, div[data-hveid], li.b_algo").each((_, el) => {
    const titleEl = $(el).find("h3, h2").first();
    const linkEl  = $(el).find("a[href]").first();
    const snippetEl = $(el).find("div.VwiC3b, div.b_caption, .snippet, p").first();

    const href = linkEl.attr("href") || "";
    const url = decodeURIComponent(href.replace(/^\/url\?q=/, "").split("&")[0]);

    if (url && /linkedin\.com\/(in|pub)\/[a-zA-Z0-9\-_]+/i.test(url)) {
      results.push({
        url: url.split("?")[0],
        title: titleEl.text().trim(),
        snippet: snippetEl.text().replace(/\s+/g, " ").trim(),
      });
    }
  });

  return results;
}

/**
 * Queries a single search engine and returns parsed LinkedIn profile results.
 */
async function querySearchEngine(engine, keyword, country = "") {
  const dork = country
    ? `site:linkedin.com/in "${keyword}" "${country}"`
    : `site:linkedin.com/in "${keyword}" email OR contact`;

  const url = engine.url(dork);
  try {
    const { data } = await axios.get(url, {
      headers: {
        "User-Agent": randomUserAgent(),
        "Accept": "text/html,application/xhtml+xml",
        "Accept-Language": "en-US,en;q=0.9",
        "Referer": "https://www.google.com/",
      },
      timeout: 15000,
    });
    return parseSerpResults(data);
  } catch (err) {
    return [];
  }
}

/**
 * Extracts name parts and company from a LinkedIn title/snippet.
 * Returns { firstName, lastName, title, company }.
 */
function parseLinkedInSnippet(title, snippet) {
  // LinkedIn titles often look like: "John Smith - Senior Dev at Acme | LinkedIn"
  const namePart = title.replace(/\s*[|–-].*$/, "").trim();
  const parts = namePart.split(/\s+/).filter(Boolean);
  const firstName = (parts[0] || "").toLowerCase().replace(/[^a-z]/g, "");
  const lastName  = (parts.slice(-1)[0] || "").toLowerCase().replace(/[^a-z]/g, "");

  // Extract job title
  const titleMatch = title.match(/[-–]\s*(.+?)\s*(at|@|\||$)/i);
  const jobTitle = titleMatch ? titleMatch[1].trim() : null;

  // Extract company
  const companyMatch = title.match(/(?:at|@)\s+([^|]+?)(?:\s*\||$)/i);
  const company = companyMatch ? companyMatch[1].trim() : null;

  // Try to find a company domain from the snippet
  const domainMatch = snippet.match(/(?:www\.)?([a-zA-Z0-9\-]+\.[a-z]{2,})/);
  const domain = domainMatch ? domainMatch[1].replace(/^www\./, "") : null;

  return { firstName, lastName, jobTitle, company, domain };
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 * 
 * @param {number}   jobId
 * @param {string[]} keywords
 * @param {object}   config       { engines: ["google","bing","yahoo"], country: "Saudi Arabia" }
 * @param {object}   hooks        { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const selectedEngineNames = config.engines || ["google", "bing", "yahoo"];
  const engines = SEARCH_ENGINES.filter((e) =>
    selectedEngineNames.some((n) => e.name.startsWith(n))
  );
  const country = config.country || "";
  let totalExtracted = 0;

  for (let ki = 0; ki < keywords.length; ki++) {
    if (isCancelled()) break;
    const keyword = keywords[ki];
    await logEvent("INFO", `LinkedIn search: "${keyword}" (${ki + 1}/${keywords.length})`);

    const seen = new Set(); // Deduplicate profile URLs

    for (const engine of engines) {
      if (isCancelled()) break;
      await logEvent("DEBUG", `Querying ${engine.name} for "${keyword}"`);

      const serpResults = await querySearchEngine(engine, keyword, country);
      await logEvent("INFO", `${engine.name} returned ${serpResults.length} LinkedIn profiles`);

      for (const result of serpResults) {
        if (isCancelled()) break;
        if (seen.has(result.url)) continue;
        seen.add(result.url);

        const { firstName, lastName, jobTitle, company, domain } = parseLinkedInSnippet(
          result.title,
          result.snippet
        );

        // Extract any emails directly visible in the snippet
        const snippetEmails = extractEmails(result.snippet + " " + result.title);

        // Generate pattern-based email guesses if we have name + domain
        const guessedEmails = [];
        if (firstName && lastName && domain) {
          for (const pat of EMAIL_PATTERNS) {
            guessedEmails.push(pat(firstName, lastName, domain));
          }
        }

        const bestEmail = snippetEmails[0] || guessedEmails[0] || null;

        await db("social_results").insert({
          job_id: jobId,
          source: "LINKEDIN",
          keyword,
          name: result.title.replace(/\s*[|–-].*$/, "").trim().substring(0, 500),
          email: bestEmail,
          title: jobTitle?.substring(0, 500),
          description: company?.substring(0, 500),
          profile_url: result.url,
          raw_data: JSON.stringify({
            serpEngine: engine.name,
            snippet: result.snippet,
            snippetEmails,
            guessedEmails: guessedEmails.slice(0, 3),
            domain,
          }),
        });

        totalExtracted++;
        await logEvent("INFO", `✓ LinkedIn profile saved: ${result.url}`);
      }

      await randomDelay(2000, 4500); // Polite delay between engine requests
    }

    const progress = ((ki + 1) / keywords.length) * 100;
    await updateProgress(progress, totalExtracted);

    if (ki < keywords.length - 1 && !isCancelled()) {
      await randomDelay(3000, 6000);
    }
  }
}

module.exports = { run };
