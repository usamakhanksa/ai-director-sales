/**
 * Dork Generator API Routes
 * 
 * Endpoints:
 *   POST /v1/dorks/generate — Generate advanced search dorks
 *   GET  /v1/dorks/history — Get user's dork generation history
 */

"use strict";

const express = require("express");
const { generateDorks, saveDorkHistory } = require("../services/dorkGenerator");
const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const auth = requireAuthOrInternal(requireAuth);

const router = express.Router();

/**
 * POST /v1/dorks/generate
 * Generate advanced search dorks based on provided criteria
 */
router.post("/generate", auth, async (req, res, next) => {
  try {
    const { keyword, location, country, intent, platforms, language } = req.body;
    
    // Validate required fields
    if (!keyword || typeof keyword !== 'string') {
      return res.status(400).json({ 
        success: false, 
        error: "keyword is required and must be a string" 
      });
    }

    // Generate dorks
    const options = {
      keyword,
      location: location || '',
      country: country || 'SA',
      intent: intent || 'general',
      platforms: Array.isArray(platforms) ? platforms : [],
      language: language || 'en'
    };

    const dorks = generateDorks(options);

    // Save to history
    await saveDorkHistory(req.user.id, dorks, options);

    res.json({
      success: true,
      data: {
        dorks,
        count: dorks.length,
        options
      }
    });
  } catch (err) {
    next(err);
  }
});

/**
 * GET /v1/dorks/history
 * Get user's dork generation history
 */
router.get("/history", auth, async (req, res, next) => {
  try {
    const limit = Math.min(Number(req.query.limit) || 50, 200);
    const offset = Number(req.query.offset) || 0;

    const history = await db("dork_history")
      .where({ user_id: req.user.id })
      .orderBy("created_at", "desc")
      .limit(limit)
      .offset(offset);

    const total = await db("dork_history")
      .where({ user_id: req.user.id })
      .count("id as count")
      .first();

    res.json({
      success: true,
      data: history.map(record => ({
        id: record.id,
        dorks: JSON.parse(record.dorks || '[]'),
        options: JSON.parse(record.options || '{}'),
        createdAt: record.created_at
      })),
      total: Number(total.count)
    });
  } catch (err) {
    next(err);
  }
});

/**
 * GET /v1/dorks/templates
 * Get available dork templates
 */
router.get("/templates", auth, (req, res) => {
  const { DORK_TEMPLATES } = require("../services/dorkGenerator");
  
  res.json({
    success: true,
    data: {
      categories: Object.keys(DORK_TEMPLATES),
      templates: DORK_TEMPLATES
    }
  });
});

module.exports = router;