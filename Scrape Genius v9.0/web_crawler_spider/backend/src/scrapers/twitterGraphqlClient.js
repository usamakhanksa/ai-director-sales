/**
 * X (Twitter) internal GraphQL search client.
 *
 * Why this exists: the previous implementation drove a headless browser to
 * `x.com/search`, which only ever sees X's public/guest surface — and X now
 * gates that surface behind a login wall almost unconditionally, so the DOM
 * scraper got 0 tweets no matter how good its stealth fingerprint was.
 *
 * This talks directly to the same internal GraphQL endpoint the logged-in web
 * app itself calls (`SearchTimeline`), using a real logged-in session's
 * cookies (`auth_token` + `ct0`) plus the public web bearer token every
 * browser session sends. No official API / developer account involved — it's
 * the same request the browser makes, just issued directly over HTTPS.
 *
 * Requires TWITTER_AUTH_TOKEN + TWITTER_CT0 (see backend/.env.example for how
 * to obtain them). Without a valid session, X returns 401/403 for every call —
 * there is no way around authenticating as *some* logged-in account.
 */

"use strict";

const axios = require("axios");
const { HttpsProxyAgent } = require("https-proxy-agent");
const { randomProxy, randomUserAgent } = require("../services/antiRobotService");

// Public bearer token X's own web client embeds in its JS bundle — it's not a
// secret, just an app-level identifier shared by every guest/logged-in web
// session. Overridable in case X rotates it.
const DEFAULT_BEARER =
  "AAAAAAAAAAAAAAAAAAAAANRILgAAAAAAnNwIzUejRCOuH5E6I8xnZz4puTs%3D1Zv7ttfk8LF81IUq16cHjhLTvJu4FA33AGWWjCpTnA";

// GraphQL query id for SearchTimeline. X rotates these opaque ids periodically
// as part of routine frontend deploys, which is the most common cause of a
// clean 404 from an otherwise-correct request — override via env when X ships
// a new one (grab it from the Network tab: any request to
// `/i/api/graphql/<id>/SearchTimeline`).
const SEARCH_QUERY_ID = process.env.TWITTER_SEARCH_QUERY_ID || "gkjsKepM6gl_HmFWoWKfgg";

const SEARCH_FEATURES = {
  rweb_lists_timeline_redesign_enabled: true,
  responsive_web_graphql_exclude_directive_enabled: true,
  verified_phone_label_enabled: false,
  creator_subscriptions_tweet_preview_api_enabled: true,
  responsive_web_graphql_timeline_navigation_enabled: true,
  responsive_web_graphql_skip_user_profile_image_extensions_enabled: false,
  tweetypie_unmention_optimization_enabled: true,
  responsive_web_edit_tweet_api_enabled: true,
  graphql_is_translatable_rweb_tweet_is_translatable_enabled: true,
  view_counts_everywhere_api_enabled: true,
  longform_notetweets_consumption_enabled: true,
  responsive_web_twitter_article_tweet_consumption_enabled: false,
  tweet_awards_web_tipping_enabled: false,
  freedom_of_speech_not_reach_fetch_enabled: true,
  standardized_nudges_misinfo: true,
  tweet_with_visibility_results_prefer_gql_limited_actions_policy_enabled: true,
  longform_notetweets_rich_text_read_enabled: true,
  longform_notetweets_inline_media_enabled: true,
  responsive_web_media_download_video_enabled: false,
  responsive_web_enhance_cards_enabled: false,
  rweb_video_timestamps_enabled: true,
  responsive_web_twitter_article_notetweet_consumption_enabled: true,
  communities_web_enable_tweet_community_results_fetch: true,
  articles_preview_enabled: true,
};

class TwitterAuthError extends Error {
  constructor(message, status) {
    super(message);
    this.name = "TwitterAuthError";
    this.status = status;
  }
}

class TwitterRateLimitError extends Error {
  constructor(message) {
    super(message);
    this.name = "TwitterRateLimitError";
  }
}

function hasSession() {
  return Boolean(process.env.TWITTER_AUTH_TOKEN);
}

function buildClient() {
  const authToken = process.env.TWITTER_AUTH_TOKEN;
  const ct0 = process.env.TWITTER_CT0;
  if (!authToken) {
    throw new TwitterAuthError(
      "TWITTER_AUTH_TOKEN is not set — log into x.com in a browser, copy the `auth_token` and `ct0` cookies " +
        "from DevTools > Application > Cookies, and set TWITTER_AUTH_TOKEN / TWITTER_CT0 in backend/.env.",
      401
    );
  }

  const proxyUrl = randomProxy();
  const headers = {
    authorization: `Bearer ${process.env.TWITTER_BEARER_TOKEN || DEFAULT_BEARER}`,
    "x-twitter-active-user": "yes",
    "x-twitter-auth-type": "OAuth2Session",
    "x-twitter-client-language": "en",
    "user-agent": randomUserAgent(),
    accept: "*/*",
    cookie: `auth_token=${authToken};${ct0 ? ` ct0=${ct0};` : ""}`,
  };
  if (ct0) headers["x-csrf-token"] = ct0;

  return axios.create({
    baseURL: "https://x.com/i/api/graphql",
    headers,
    timeout: 20000,
    httpsAgent: proxyUrl ? new HttpsProxyAgent(proxyUrl) : undefined,
    proxy: false, // disable axios' own proxy handling — we set httpsAgent explicitly above
    validateStatus: () => true, // handle non-2xx ourselves for clearer error messages
  });
}

/**
 * Fetches one page of search results.
 *
 * @param {string} keyword
 * @param {string|null} cursor  Pagination cursor from the previous page's response, if any
 * @param {"Latest"|"Top"} product
 * @returns {Promise<{ tweets: object[], nextCursor: string|null }>}
 */
async function fetchSearchPage(keyword, cursor, product = "Latest") {
  const client = buildClient();

  const variables = {
    rawQuery: keyword,
    count: 20,
    querySource: "typed_query",
    product,
  };
  if (cursor) variables.cursor = cursor;

  const params = {
    variables: JSON.stringify(variables),
    features: JSON.stringify(SEARCH_FEATURES),
  };

  const response = await client.get(`/${SEARCH_QUERY_ID}/SearchTimeline`, { params });

  if (response.status === 401 || response.status === 403) {
    throw new TwitterAuthError(
      `X rejected the session (HTTP ${response.status}) — TWITTER_AUTH_TOKEN/TWITTER_CT0 are likely expired or invalid. ` +
        "Log in again and refresh the cookies.",
      response.status
    );
  }
  if (response.status === 429) {
    throw new TwitterRateLimitError("X rate-limited this session (HTTP 429) — back off and retry later, or rotate to another account.");
  }
  if (response.status === 404) {
    throw new Error(
      `SearchTimeline endpoint returned 404 — X likely rotated its GraphQL query id. ` +
        `Update TWITTER_SEARCH_QUERY_ID (current: ${SEARCH_QUERY_ID}) with the fresh id from a browser's Network tab.`
    );
  }
  if (response.status !== 200) {
    throw new Error(`X search request failed (HTTP ${response.status}): ${JSON.stringify(response.data).slice(0, 500)}`);
  }

  return parseSearchResponse(response.data);
}

function parseSearchResponse(data) {
  const instructions = data?.data?.search_by_raw_query?.search_timeline?.timeline?.instructions || [];
  const addEntries = instructions.find((i) => i.type === "TimelineAddEntries");
  const entries = addEntries?.entries || [];

  const tweets = [];
  let nextCursor = null;

  for (const entry of entries) {
    const entryId = entry.entryId || "";

    if (entryId.startsWith("cursor-bottom-")) {
      nextCursor = entry?.content?.value || nextCursor;
      continue;
    }

    const result = entry?.content?.itemContent?.tweet_results?.result;
    if (!result) continue;

    const tweetData = result.__typename === "TweetWithVisibilityResults" ? result.tweet : result;
    const legacy = tweetData?.legacy;
    const user = tweetData?.core?.user_results?.result?.legacy;
    if (!legacy || !user) continue;

    // Expand t.co shortlinks using the entity metadata X returns alongside the
    // truncated display text, instead of the raw t.co URLs full_text contains.
    const expandedUrls = (legacy.entities?.urls || []).map((u) => u.expanded_url).filter(Boolean);

    tweets.push({
      id: legacy.id_str,
      text: legacy.full_text || "",
      author: user.name || null,
      handle: user.screen_name || null,
      tweetUrl: legacy.id_str && user.screen_name ? `https://twitter.com/${user.screen_name}/status/${legacy.id_str}` : null,
      profileUrl: user.screen_name ? `https://twitter.com/${user.screen_name}` : null,
      createdAt: legacy.created_at || null,
      expandedLinks: expandedUrls,
    });
  }

  return { tweets, nextCursor };
}

module.exports = {
  fetchSearchPage,
  hasSession,
  TwitterAuthError,
  TwitterRateLimitError,
};
