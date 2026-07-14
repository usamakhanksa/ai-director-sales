/**
 * Jobs API Routes
 * 
 * Manages the scraping job queue and provides real-time status via SSE.
 * 
 * Endpoints:
 *   GET  /v1/jobs            — list all jobs for the authenticated user
 *   POST /v1/jobs            — create a new scraping job
 *   GET  /v1/jobs/:id        — get job status + progress
 *   DELETE /v1/jobs/:id      — cancel a running/queued job
 *   GET  /v1/jobs/:id/logs   — SSE stream of real-time scraper logs
 */

"use strict";

const express = require("express");
const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const auth = requireAuthOrInternal(requireAuth);
const { createJob, getJobStatus, cancelJob, subscribe } = require("../services/jobManager");

const router = express.Router();

// ─────────────────────────────────────────────────────────────────────────────
// GET /v1/jobs — List all jobs for the authenticated user
// ─────────────────────────────────────────────────────────────────────────────

router.get("/", auth, async (req, res, next) => {
  try {
    const limit = Math.min(Number(req.query.limit) || 1000, 1000);
    const offset = Number(req.query.offset) || 0;
    const status = req.query.status; // Optional filter: QUEUED|RUNNING|DONE|FAILED|CANCELLED

    let query = db("scrape_jobs")
      .where({ user_id: req.user.id })
      .orderBy("created_at", "desc")
      .limit(limit)
      .offset(offset);

    if (status) query = query.where({ status: status.toUpperCase() });

    const jobs = await query;
    const total = await db("scrape_jobs").where({ user_id: req.user.id }).count("id as count").first();

    res.json({
      success: true,
      data: jobs.map((j) => ({
        id: j.id,
        module: j.module,
        // mysql2 already parses JSON-typed columns into real arrays/objects —
        // j.keywords is not a JSON string here, so it's used as-is.
        keywords: j.keywords || [],
        status: j.status,
        progress: j.progress,
        extractedCount: j.extracted_count,
        errorMessage: j.error_message,
        startedAt: j.started_at,
        completedAt: j.completed_at,
        createdAt: j.created_at,
      })),
      total: Number(total.count),
    });
  } catch (err) {
    next(err);
  }
});

// ─────────────────────────────────────────────────────────────────────────────
// POST /v1/jobs — Create a new scraping job
// ─────────────────────────────────────────────────────────────────────────────

router.post("/", auth, async (req, res, next) => {
  try {
    const { module, keywords, config } = req.body;

    // Validate required fields
    if (!module) {
      return res.status(400).json({ success: false, error: "module is required" });
    }
    if (!Array.isArray(keywords) || keywords.length === 0) {
      return res.status(400).json({ success: false, error: "keywords must be a non-empty array" });
    }
    if (keywords.length > 100) {
      return res.status(400).json({ success: false, error: "Maximum 100 keywords per job" });
    }

    // Validate module
    const VALID_MODULES = ["facebook", "linkedin", "twitter", "google_maps", "website_crawler", "haraj", "classified_generic"];
    if (!VALID_MODULES.includes(module)) {
      return res.status(400).json({
        success: false,
        error: `Invalid module. Allowed: ${VALID_MODULES.join(", ")}`,
      });
    }

    const { jobId } = await createJob({
      userId: req.user.id,
      module,
      keywords: keywords.map((k) => String(k).trim()).filter(Boolean),
      config: config || {},
    });

    res.status(202).json({
      success: true,
      data: { jobId },
      message: `Job ${jobId} queued. Monitor at GET /v1/jobs/${jobId} or stream logs at GET /v1/jobs/${jobId}/logs`,
    });
  } catch (err) {
    if (err.message.includes("Unknown scraping module")) {
      return res.status(400).json({ success: false, error: err.message });
    }
    next(err);
  }
});

// ─────────────────────────────────────────────────────────────────────────────
// GET /v1/jobs/:id — Get job status and progress
// ─────────────────────────────────────────────────────────────────────────────

router.get("/:id", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.params.id);
    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) return res.status(404).json({ success: false, error: "Job not found" });

    const status = await getJobStatus(jobId);

    // Include result counts per table
    const [socialCount, classifiedCount, mapsCount] = await Promise.all([
      db("social_results").where({ job_id: jobId }).count("id as count").first(),
      db("classified_results").where({ job_id: jobId }).count("id as count").first(),
      db("maps_job_results").where({ job_id: jobId }).count("id as count").first(),
    ]);

    res.json({
      success: true,
      data: {
        ...status,
        resultCounts: {
          social: Number(socialCount.count),
          classified: Number(classifiedCount.count),
          maps: Number(mapsCount.count),
        },
      },
    });
  } catch (err) {
    next(err);
  }
});

// ─────────────────────────────────────────────────────────────────────────────
// GET /v1/jobs/:id/results — Unified result rows regardless of module
// ─────────────────────────────────────────────────────────────────────────────

const RESULT_TABLE_BY_MODULE = {
  facebook: "social_results",
  linkedin: "social_results",
  twitter: "social_results",
  haraj: "classified_results",
  classified_generic: "classified_results",
  google_maps: "maps_job_results",
};

router.get("/:id/results", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.params.id);
    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) return res.status(404).json({ success: false, error: "Job not found" });

    const table = RESULT_TABLE_BY_MODULE[job.module];
    if (!table) {
      return res.status(400).json({ success: false, error: `Unknown result table for module "${job.module}"` });
    }

    // limit=0 (or omitted "all" flag) means "no limit — return every row"
    const rawLimit = req.query.limit;
    const showAll = req.query.all === "true" || rawLimit === "0";
    const limit = showAll ? null : Math.min(Number(rawLimit) || 100, 1000);
    const offset = showAll ? 0 : Number(req.query.offset) || 0;

    let rowsQuery = db(table).where({ job_id: jobId }).orderBy("id", "asc");
    if (!showAll) rowsQuery = rowsQuery.limit(limit).offset(offset);

    const [results, total] = await Promise.all([
      rowsQuery,
      db(table).where({ job_id: jobId }).count("id as count").first(),
    ]);

    res.json({ success: true, data: results, total: Number(total.count), module: job.module });
  } catch (err) {
    next(err);
  }
});

// ─────────────────────────────────────────────────────────────────────────────
// DELETE /v1/jobs/:id — Cancel a job
// ─────────────────────────────────────────────────────────────────────────────

router.delete("/:id", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.params.id);
    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) return res.status(404).json({ success: false, error: "Job not found" });

    if (!["QUEUED", "RUNNING"].includes(job.status)) {
      return res.status(400).json({ success: false, error: `Cannot cancel a ${job.status} job` });
    }

    await cancelJob(jobId);
    res.json({ success: true, message: `Job ${jobId} cancellation requested` });
  } catch (err) {
    next(err);
  }
});

// ─────────────────────────────────────────────────────────────────────────────
// GET /v1/jobs/:id/logs — SSE real-time log stream
// ─────────────────────────────────────────────────────────────────────────────

router.get("/:id/logs", auth, async (req, res, next) => {
  try {
    const jobId = Number(req.params.id);
    const job = await db("scrape_jobs").where({ id: jobId, user_id: req.user.id }).first();
    if (!job) return res.status(404).json({ success: false, error: "Job not found" });

    // SSE headers
    res.writeHead(200, {
      "Content-Type": "text/event-stream",
      "Cache-Control": "no-cache",
      Connection: "keep-alive",
      "X-Accel-Buffering": "no", // Disable nginx buffering
    });

    // Send historical logs first (last 200 entries)
    const historicalLogs = await db("scraper_logs")
      .where({ job_id: jobId })
      .orderBy("created_at", "asc")
      .limit(200);

    for (const log of historicalLogs) {
      res.write(`data: ${JSON.stringify({
        level: log.level,
        message: log.message,
        // mysql2 already parses the JSON-typed `meta` column into an object.
        meta: log.meta ?? null,
        created_at: log.created_at,
      })}\n\n`);
    }

    // Send current job status
    res.write(`data: ${JSON.stringify({ type: "status", status: job.status, progress: job.progress })}\n\n`);

    // If job is still active, subscribe to live events
    if (["QUEUED", "RUNNING"].includes(job.status)) {
      subscribe(jobId, res);
    } else {
      // Job already done — close stream after sending history
      setTimeout(() => {
        res.write("data: {\"type\":\"stream_end\"}\n\n");
        res.end();
      }, 500);
    }

    // Keep-alive ping every 20 seconds
    const pingInterval = setInterval(() => {
      try { res.write(": ping\n\n"); } catch { clearInterval(pingInterval); }
    }, 20000);

    req.on("close", () => clearInterval(pingInterval));
  } catch (err) {
    next(err);
  }
});

module.exports = router;
