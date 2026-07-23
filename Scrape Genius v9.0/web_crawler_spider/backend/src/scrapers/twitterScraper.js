/**
 * Twitter / X.com Keyword Scraper
 *
 * Strategy (no official API / developer account required):
 *   1. Call X's own internal GraphQL `SearchTimeline` endpoint directly —
 *      the same request the logged-in web app makes — via twitterGraphqlClient.
 *   2. Page through results with the cursor X returns, with no fixed cap
 *      (stops when X stops returning a next cursor, or maxTweets is hit).
 *   3. Apply regex to find phone numbers, emails, and websites in tweet text.
 *   4. Save to social_results table.
 *
 * This replaced an earlier Playwright DOM-scraping approach: X gates its
 * guest/logged-out search surface behind a login wall almost unconditionally,
 * so no amount of stealth-browser tuning got past it. The GraphQL endpoint
 * requires the exact same thing a browser would — a logged-in session's
 * `auth_token`/`ct0` cookies (see backend/.env.example) — but once that's
 * supplied, it's a plain HTTPS call with no DOM/JS rendering involved.
 *
 * Key Fields Extracted:
 *   Tweet URL | Text | Phone | Email | Website | Profile URL | Post Date
 */

"use strict";

const db = require("../config/database");
const { randomDelay } = require("../services/antiRobotService");
const { fetchSearchPage, hasSession, TwitterAuthError, TwitterRateLimitError } = require("./twitterGraphqlClient");

// ─────────────────────────────────────────────────────────────────────────────
// Regex
// ─────────────────────────────────────────────────────────────────────────────

const PHONE_RE = /(?:\+?(?:966|971|965|974|968|973|20|1|44|91)[\s\-]?)?(?:0?5\d[\s\-]?\d{3}[\s\-]?\d{4}|0?[1-9]\d[\s\-]?\d{3}[\s\-]?\d{4}|\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{4})/g;
const EMAIL_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
const EMAIL_TEST_RE = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/;
// Matches bare/http(s) website mentions inside tweet text, excluding twitter/x.com and t.co
// shortener links (those are resolved separately from entity-expanded URLs).
const WEBSITE_RE = /\b(?:https?:\/\/)?(?:www\.)?[a-zA-Z0-9][a-zA-Z0-9-]{0,62}(?:\.[a-zA-Z0-9][a-zA-Z0-9-]{0,62})+(?:\/[^\s]*)?\b/g;
const EXCLUDED_DOMAINS = /^(https?:\/\/)?(www\.)?(twitter|x|t)\.co(m)?/i;

function extractPhones(text) {
  return [...new Set((text.match(PHONE_RE) || []).map((m) => m.trim()).filter((m) => m.replace(/\D/g, "").length >= 7))];
}

function extractEmails(text) {
  return [...new Set((text.match(EMAIL_RE) || []).map((m) => m.toLowerCase()))];
}

function extractWebsites(text, expandedLinks = []) {
  const fromText = (text.match(WEBSITE_RE) || []).filter((m) => !EXCLUDED_DOMAINS.test(m) && !EMAIL_TEST_RE.test(m));
  const all = [...fromText, ...expandedLinks].filter((u) => !EXCLUDED_DOMAINS.test(u));
  return [...new Set(all.map((u) => (u.startsWith("http") ? u : `https://${u}`)))];
}

// ─────────────────────────────────────────────────────────────────────────────
// Main Run Function
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Entry point called by the Job Manager.
 *
 * @param {number}   jobId
 * @param {string[]} keywords    Search terms / hashtags
 * @param {object}   config      { maxTweets: 0 (unlimited) | N, filter: "live"|"top" }
 * @param {object}   hooks       { logEvent, updateProgress, isCancelled }
 */
async function run(jobId, keywords, config = {}, hooks = {}) {
  const {
    logEvent = () => {},
    updateProgress = () => {},
    isCancelled = () => false,
  } = hooks;

  // maxTweets <= 0 (or omitted) means "no limit" — paginate until X stops
  // returning a next cursor instead of stopping at an arbitrary count.
  const maxTweets = config.maxTweets > 0 ? config.maxTweets : Infinity;
  const product = (config.filter || "live").toLowerCase() === "top" ? "Top" : "Latest";
  // Safety ceiling on pages fetched per keyword, so a misbehaving/unlimited
  // run can't loop forever — comfortably above any real search feed's depth.
  const HARD_PAGE_CEILING = 500;
  let totalExtracted = 0;

  if (!hasSession()) {
    await logEvent(
      "ERROR",
      "TWITTER_AUTH_TOKEN is not configured — X requires a logged-in session for search (guest access is blocked). " +
        "Set TWITTER_AUTH_TOKEN and TWITTER_CT0 in backend/.env (see backend/.env.example for how to obtain them)."
    );
    return;
  }

  for (let ki = 0; ki < keywords.length; ki++) {
    if (isCancelled()) break;
    const keyword = keywords[ki];
    await logEvent("INFO", `Twitter search: "${keyword}" (product=${product})`);

    const seenTweets = new Set();
    let cursor = null;
    let page = 0;
    let stop = false;

    while (page < HARD_PAGE_CEILING && !isCancelled() && !stop) {
      let tweets;
      let nextCursor;

      try {
        ({ tweets, nextCursor } = await fetchSearchPage(keyword, cursor, product));
      } catch (err) {
        if (err instanceof TwitterAuthError) {
          await logEvent("ERROR", `Twitter auth failed for "${keyword}": ${err.message}`);
          stop = true;
          break;
        }
        if (err instanceof TwitterRateLimitError) {
          await logEvent("WARN", `Twitter rate-limited on "${keyword}" after ${seenTweets.size} tweets: ${err.message}`);
          stop = true;
          break;
        }
        await logEvent("ERROR", `Twitter search request failed for "${keyword}": ${err.message}`);
        stop = true;
        break;
      }

      let newThisPage = 0;
      for (const tweet of tweets) {
        if (!tweet.tweetUrl || seenTweets.has(tweet.tweetUrl)) continue;
        seenTweets.add(tweet.tweetUrl);
        newThisPage++;
        if (seenTweets.size > maxTweets) break;

        const phones = extractPhones(tweet.text);
        const emails = extractEmails(tweet.text);
        const websites = extractWebsites(tweet.text, tweet.expandedLinks);

        await db("social_results").insert({
          job_id: jobId,
          source: "TWITTER",
          keyword,
          name: tweet.author,
          phone: phones[0] || null,
          email: emails[0] || null,
          profile_url: tweet.profileUrl || tweet.tweetUrl,
          tweet_text: tweet.text.substring(0, 2000),
          post_date: tweet.createdAt ? new Date(tweet.createdAt) : null,
          raw_data: JSON.stringify({
            tweetUrl: tweet.tweetUrl,
            handle: tweet.handle,
            allPhones: phones,
            allEmails: emails,
            allWebsites: websites,
          }),
        });

        totalExtracted++;
      }

      await logEvent("INFO", `Page ${page + 1}: collected ${seenTweets.size} tweets (+${newThisPage})`);

      if (seenTweets.size >= maxTweets) break;
      if (!nextCursor || newThisPage === 0) {
        await logEvent("INFO", `Feed exhausted for "${keyword}" after ${seenTweets.size} tweets.`);
        break;
      }

      cursor = nextCursor;
      page++;
      // X's own web client fires these requests as fast as the user scrolls;
      // a short randomized pause is enough to avoid looking scripted without
      // needlessly slowing down a large "no limit" run.
      await randomDelay(1200, 2800);
    }

    await logEvent("INFO", `✓ Twitter "${keyword}": ${seenTweets.size} tweets extracted`);

    const progress = ((ki + 1) / keywords.length) * 100;
    await updateProgress(progress, totalExtracted);
  }
}

module.exports = { run };
