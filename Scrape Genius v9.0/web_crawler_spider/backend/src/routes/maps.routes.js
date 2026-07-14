const express = require("express");

const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const auth = requireAuthOrInternal(requireAuth);
const {
  reserveKeyForSearch,
  releaseUnusedReservation,
  DailyLimitReachedError,
  KeysExhaustedError,
  NoActiveKeysError,
} = require("../services/keyUsageService");

const router = express.Router();

const MAPS_PROVIDER = "maps_scraper";

function mapResultRow(searchQueryId, item) {
  return {
    search_query_id: searchQueryId,
    place_name: item.title || item.place_name || null,
    address: item.address || null,
    phone: item.phone_num || item.phone || null,
    website: item.website || null,
    category: item.category || null,
    rating: item.avg_rating || item.rating || null,
    reviews_count: item.reviews ? parseInt(String(item.reviews).replace(/\D/g, ""), 10) || null : null,
    latitude: item.latitude || null,
    longitude: item.longitude || null,
    raw_json: JSON.stringify(item),
  };
}

/**
 * Skeleton for Google Maps scraping. The original app drives a headless
 * Playwright browser against maps.google.com and caches each query's results
 * to data/map_data/<query>.json. Porting that browser-automation flow is out
 * of scope here; this route wires up the pieces that DO belong in the
 * database - usage limiting and result caching - so a scraper (Playwright,
 * a queue worker, etc.) can plug in at the marked TODO.
 */
router.post("/maps", auth, async (req, res, next) => {
  try {
    const { query } = req.body;
    if (!query) {
      return res.status(400).json({ error: "Query is required" });
    }

    // Serve from cache first, exactly like the old per-query JSON file did.
    const cachedQuery = await db("search_queries")
      .where({ query, search_type: "maps" })
      .orderBy("created_at", "desc")
      .first();
    if (cachedQuery) {
      const cachedResults = await db("maps_results").where({ search_query_id: cachedQuery.id });
      if (cachedResults.length > 0) {
        return res.json({ message: "Cached data retrieved", data: cachedResults });
      }
    }

    let reservation;
    try {
      reservation = await reserveKeyForSearch({ requestedLimit: 1, searchType: "maps", provider: MAPS_PROVIDER });
    } catch (err) {
      if (err instanceof NoActiveKeysError) {
        return res.status(500).json({
          error: "Maps scraping is not provisioned. Seed an api_keys row with provider='maps_scraper' first.",
        });
      }
      if (err instanceof DailyLimitReachedError) return res.status(429).json({ message: err.message });
      if (err instanceof KeysExhaustedError) return res.status(429).json({ error: err.message });
      throw err;
    }

    const [searchQueryId] = await db("search_queries").insert({
      user_id: req.user.id,
      query,
      result_count: 0,
      search_type: "maps",
    });

    // TODO: invoke the real scraper here (e.g. shell out to a Playwright worker)
    // and call POST /v1/search/maps/:searchQueryId/results with what it finds.
    await releaseUnusedReservation(reservation.usageLogId, reservation.reserved);

    res.status(202).json({
      message: "Maps scraping is not implemented in this service yet.",
      search_query_id: searchQueryId,
      next_step: `POST /v1/search/maps/${searchQueryId}/results to store scraped data for this query`,
    });
  } catch (err) {
    next(err);
  }
});

// Lets an external scraper worker persist results for a query started above,
// replacing the old writeFileSync(data/map_data/<query>.json, ...) cache.
router.post("/maps/:searchQueryId/results", auth, async (req, res, next) => {
  try {
    const { searchQueryId } = req.params;
    const { results } = req.body;
    if (!Array.isArray(results)) {
      return res.status(400).json({ error: "results must be an array" });
    }

    const searchQuery = await db("search_queries").where({ id: searchQueryId }).first();
    if (!searchQuery) {
      return res.status(404).json({ error: "search_query_id not found" });
    }

    if (results.length > 0) {
      await db("maps_results").insert(results.map((item) => mapResultRow(searchQueryId, item)));
    }
    await db("search_queries").where({ id: searchQueryId }).update({ result_count: results.length });

    res.status(201).json({ message: "Scraping completed", stored: results.length });
  } catch (err) {
    next(err);
  }
});

router.get("/maps/cache", auth, async (req, res, next) => {
  try {
    const { query } = req.query;
    if (!query) {
      return res.status(400).json({ error: "query is required" });
    }

    const cachedQuery = await db("search_queries")
      .where({ query, search_type: "maps" })
      .orderBy("created_at", "desc")
      .first();
    if (!cachedQuery) {
      return res.status(404).json({ message: `No data found for query: ${query}` });
    }

    const data = await db("maps_results").where({ search_query_id: cachedQuery.id });
    res.json({ message: "Cached data retrieved", data });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
