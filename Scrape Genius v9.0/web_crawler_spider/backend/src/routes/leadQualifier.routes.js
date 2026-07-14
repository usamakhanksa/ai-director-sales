/**
 * AI Lead Qualifier Routes
 *
 * Classifies scraped classified-ad text as LEAD (someone requesting/buying
 * the product we sell) vs. NOT_LEAD (selling, unrelated, noise) using an
 * LLM prompt (Ollama/Groq — see aiEnhancementService).
 *
 * Endpoints:
 *   POST /v1/lead-qualifier/classify        — classify a single ad text
 *   POST /v1/lead-qualifier/classify-job     — classify every unlabeled
 *                                              result of a classified/haraj job
 */

"use strict";

const express = require("express");
const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const { classifyLeadIntent } = require("../services/aiEnhancementService");

const auth = requireAuthOrInternal(requireAuth);
const router = express.Router();

router.post("/classify", auth, async (req, res, next) => {
  try {
    const { text, product } = req.body;
    if (!text || typeof text !== "string" || !text.trim()) {
      return res.status(400).json({ success: false, error: "text is required" });
    }

    const result = await classifyLeadIntent(text, product);
    res.json({ success: true, data: result });
  } catch (err) {
    next(err);
  }
});

router.post("/classify-job", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.body.jobId);
    const product = req.body.product;
    const limit = Math.min(Number(req.body.limit) || 50, 200);
    if (!jobId) {
      return res.status(400).json({ success: false, error: "jobId is required" });
    }

    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) {
      return res.status(404).json({ success: false, error: "Job not found" });
    }

    const rows = await db("classified_results")
      .where({ job_id: jobId })
      .whereNull("ai_label")
      .orderBy("id", "asc")
      .limit(limit);

    const classified = [];
    for (const row of rows) {
      const text = [row.post_title, row.location, row.price].filter(Boolean).join(" — ");
      const result = await classifyLeadIntent(text, product);
      await db("classified_results")
        .where({ id: row.id })
        .update({ ai_is_lead: result.isLead, ai_label: result.label, ai_classified_at: db.fn.now() });
      classified.push({ id: row.id, ...result });
    }

    res.json({ success: true, data: { jobId, classifiedCount: classified.length, results: classified } });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
