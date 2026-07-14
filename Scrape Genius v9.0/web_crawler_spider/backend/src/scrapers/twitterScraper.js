/**
 * Twitter / X.com Keyword Scraper
 * 
 * Strategy (no Twitter API or login required):
 *   1. Use Twitter's public search URL: https://twitter.com/search?q=keyword&f=live
 *   2. Playwright stealth browser loads the page (requires JS execution)
 *   3. Scroll to load tweets (infinite scroll)
 *   4. Extract tweet text, author, URL, date
 *   5. Apply regex to find phone numbers and email patterns in tweet text
 *   6. Save to social_results table
 * 
 * Note: Twitter/X actively fights scrapers. This implementation uses:
 *   - Stealth browser with randomised fingerprint
 *   - Human-like scrolling with random pauses
 *   - Random delay between pages
 * 
 * Key Fields Extracted:
 *   Tweet URL | Text | Phone | Email | Author | Post Date
 */

"use strict";

const db = require("../config/database");
const { createBrowser, createContext, closeBrowser, navigateTo } = require("../services/browserEngine");
const { randomDelay, humanScroll } = require("../services/antiRobotService");

// ─────────────────────────────────────────────────────────────────────────────
// Regex
// ─────────────────────────────────────────────────────────────────────────────

const PHONE_RE = /(?:\+?(?:966|971|965|974|968|973|20|1|44|91)[\s\-]?)?(?:0?5\d[\s\-]?\d{3}[\s\-]?\d{4}|0?[1-9]\d[\s\-]?\d{3}[\s\-]?\d{4}|\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{4})/g;
const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;

function extractPhones(text) {
  return [...new Set((text.match(PHONE_RE) || []).map((m) => m.trim()).filter((m) => m.replace(/\D/g, "").length >= 7))];
}

function extractEmails(text) {
  return [...new Set((text.match(EMAIL_RE) || []).map((m) => m.toLowerCase()))];
}

// ─────────────────────────────────────────────────────────────────────────────
// Tweet Extraction from DOM
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Extracts tweet data from Twitter's DOM after the page has loaded.
 * Twitter uses heavy JS rendering so we work with Playwright's page API.
 */
async function extractTweetsFromPage(page) {
  return await page.$$eval(
    'article[data-testid="tweet"], [data-testid="cellInnerDiv"] article',
    (articles) => articles.map((article) => {
      const textEl     = article.querySelector('[data-testid="tweetText"]');
      const timeEl     = article.querySelector("time");
      const authorEl   = article.querySelector('[data-testid="User-Name"]');
      const linkEl     = article.querySelector('a[href*="/status/"]');

      const text    = textEl?.innerText?.replace(/\s+/g, " ").trim() || "";
      const date    = timeEl?.dateTime || null;
      const author  = authorEl?.innerText?.split("\n")[0]?.trim() || null;
      let tweetUrl  = linkEl?.getAttribute("href") || null;
      if (tweetUrl && !tweetUrl.startsWith("http")) {
        tweetUrl = `https://twitter.com${tweetUrl}`;
      }

      return { text, date, author, tweetUrl };
    })
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 * 
 * @param {number}   jobId
 * @param {string[]} keywords    Search terms / hashtags
 * @param {object}   config      { maxTweets: 200, filter: "live"|"top" }
 * @param {object}   hooks       { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  const maxTweets = config.maxTweets || 200;
  const filter    = config.filter || "live"; // "live" = most recent, "top" = popular
  let totalExtracted = 0;

  const browser = await createBrowser({ headless: process.env.PLAYWRIGHT_HEADLESS !== "false" });

  try {
    for (let ki = 0; ki < keywords.length; ki++) {
      if (isCancelled()) break;
      const keyword = keywords[ki];
      await logEvent("INFO", `Twitter search: "${keyword}" (filter=${filter})`);

      const { context, page } = await createContext(browser);
      const seenTweets = new Set();

      try {
        // Build Twitter/X search URL
        const searchUrl = `https://twitter.com/search?q=${encodeURIComponent(keyword)}&f=${filter}&src=typed_query`;
        await navigateTo(page, searchUrl);

        // Wait for tweet feed to load
        try {
          await page.waitForSelector('[data-testid="tweet"]', { timeout: 20000 });
        } catch {
          await logEvent("WARN", `No tweets loaded for "${keyword}" (may require login or rate-limited)`);
          continue;
        }

        await randomDelay(2000, 4000);

        let scrollAttempts = 0;
        const maxScrolls = Math.ceil(maxTweets / 10);

        while (scrollAttempts < maxScrolls && !isCancelled()) {
          const tweets = await extractTweetsFromPage(page);

          for (const tweet of tweets) {
            if (!tweet.tweetUrl || seenTweets.has(tweet.tweetUrl)) continue;
            seenTweets.add(tweet.tweetUrl);
            if (seenTweets.size > maxTweets) break;

            const phones = extractPhones(tweet.text);
            const emails = extractEmails(tweet.text);

            // Only save tweets that have contact data or are being fully collected
            await db("social_results").insert({
              job_id: jobId,
              source: "TWITTER",
              keyword,
              name: tweet.author,
              phone: phones[0] || null,
              email: emails[0] || null,
              profile_url: tweet.tweetUrl,
              tweet_text: tweet.text.substring(0, 2000),
              post_date: tweet.date ? new Date(tweet.date) : null,
              raw_data: JSON.stringify({ allPhones: phones, allEmails: emails }),
            });

            totalExtracted++;
          }

          await logEvent("INFO", `Scroll ${scrollAttempts + 1}: collected ${seenTweets.size} tweets`);

          if (seenTweets.size >= maxTweets) break;

          // Human-like scrolling
          await humanScroll(page, 1200);
          await randomDelay(1500, 3000);
          scrollAttempts++;
        }

        await logEvent("INFO", `✓ Twitter "${keyword}": ${seenTweets.size} tweets extracted`);

      } catch (err) {
        await logEvent("ERROR", `Twitter scraping failed for "${keyword}": ${err.message}`);
      } finally {
        await context.close();
        await randomDelay(3000, 6000);
      }

      const progress = ((ki + 1) / keywords.length) * 100;
      await updateProgress(progress, totalExtracted);
    }
  } finally {
    await closeBrowser(browser);
  }
}

module.exports = { run };
