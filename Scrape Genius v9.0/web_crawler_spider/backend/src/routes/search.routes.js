const express = require("express");

const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { fetchGoogleSearchLinks } = require("../services/googleSearchService");
const {
  reserveKeyForSearch,
  releaseUnusedReservation,
  DailyLimitReachedError,
  KeysExhaustedError,
  NoActiveKeysError,
} = require("../services/keyUsageService");

const router = express.Router();

// Replaces POST /api/google_search_api. `limit` is the number of 10-result
// pages to fetch, matching the original client contract.
router.post("/google", requireAuth, async (req, res, next) => {
  try {
    const { query, limit } = req.body;
    if (!query) {
      return res.status(400).json({ error: "Query is required" });
    }
    const pages = Math.max(1, Number(limit) || 1);

    let reservation;
    try {
      reservation = await reserveKeyForSearch({ requestedLimit: pages, searchType: "search" });
    } catch (err) {
      if (err instanceof NoActiveKeysError) return res.status(500).json({ error: err.message });
      if (err instanceof DailyLimitReachedError) return res.status(429).json({ message: err.message });
      if (err instanceof KeysExhaustedError) return res.status(429).json({ error: err.message });
      throw err;
    }

    const { links, callsMade } = await fetchGoogleSearchLinks({
      key: reservation.key,
      cx: reservation.cx,
      query,
      pages: reservation.reserved,
    });

    if (callsMade < reservation.reserved) {
      await releaseUnusedReservation(reservation.usageLogId, reservation.reserved - callsMade);
    }

    await db("search_queries").insert({
      user_id: req.user.id,
      query,
      result_count: links.length,
      search_type: "search",
    });

    res.status(200).json(links);
  } catch (err) {
    next(err);
  }
});

module.exports = router;
