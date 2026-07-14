/**
 * Classified API Routes
 * 
 * Endpoints:
 *   POST /v1/classified/haraj   — Start Haraj scraper
 *   POST /v1/classified/generic — Start Generic classified scraper
 *   GET  /v1/classified/results/:jobId — Get paginated results
 */

"use strict";

const express = require("express");
const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const auth = requireAuthOrInternal(requireAuth);
const { createJob } = require("../services/jobManager");

const router = express.Router();

router.post("/haraj", auth, async (req, res, next) => {
  try {
    const { keywords, config } = req.body;
    if (!Array.isArray(keywords) || keywords.length === 0) {
      return res.status(400).json({ success: false, error: "keywords array is required" });
    }

    // Default to HARAJ if not specified in config
    const finalConfig = config || {};
    if (!finalConfig.sites || finalConfig.sites.length === 0) {
      finalConfig.sites = ["HARAJ"];
    }

    const { jobId } = await createJob({
      userId: req.user.id,
      module: "haraj",
      keywords,
      config: finalConfig,
    });

    res.status(202).json({ success: true, data: { jobId } });
  } catch (err) {
    next(err);
  }
});

router.post("/generic", auth, async (req, res, next) => {
  try {
    const { keywords, config } = req.body;
    if (!Array.isArray(keywords) || keywords.length === 0) {
      return res.status(400).json({ success: false, error: "keywords array is required" });
    }
    if (!config || !config.customBaseUrl) {
      return res.status(400).json({ success: false, error: "config.customBaseUrl is required for generic scraper" });
    }

    const { jobId } = await createJob({
      userId: req.user.id,
      module: "classified_generic",
      keywords,
      config,
    });

    res.status(202).json({ success: true, data: { jobId } });
  } catch (err) {
    next(err);
  }
});

router.get("/results/:jobId", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.params.jobId);
    const limit = Math.min(Number(req.query.limit) || 50, 200);
    const offset = Number(req.query.offset) || 0;

    // Verify job belongs to user
    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) {
      return res.status(404).json({ success: false, error: "Job not found" });
    }

    const results = await db("classified_results")
      .where({ job_id: jobId })
      .orderBy("id", "asc")
      .limit(limit)
      .offset(offset);

    const total = await db("classified_results").where({ job_id: jobId }).count("id as count").first();

    res.json({
      success: true,
      data: results,
      total: Number(total.count),
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
