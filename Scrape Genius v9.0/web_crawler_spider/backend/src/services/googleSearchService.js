const axios = require("axios");

const CUSTOM_SEARCH_ENDPOINT = "https://www.googleapis.com/customsearch/v1";

/**
 * Replicates the original google_search_api pagination loop: up to `pages`
 * calls of 10 results each (start=1,11,21,...), stopping early on the first
 * failed call. Returns the deduped link list and how many calls actually
 * succeeded, so the caller can reconcile reserved-but-unused usage quota.
 */
async function fetchGoogleSearchLinks({ key, cx, query, pages }) {
  const links = [];
  let callsMade = 0;

  for (let i = 0; i < pages; i++) {
    const start = 10 * i + 1;
    try {
      const { data } = await axios.get(CUSTOM_SEARCH_ENDPOINT, {
        params: { key, cx, q: query, start, num: 10 },
      });
      callsMade += 1;
      const pageLinks = (data.items || []).map((item) => item.link).filter(Boolean);
      links.push(...pageLinks);
    } catch (err) {
      console.error("❌ Google Custom Search request failed:", err.message);
      break;
    }
  }

  return { links, callsMade };
}

module.exports = { fetchGoogleSearchLinks };
