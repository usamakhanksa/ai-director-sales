/**
 * Job Manager Service
 * 
 * Orchestrates the full lifecycle of scraping jobs:
 *   1. createJob()     — writes a QUEUED row to scrape_jobs and enqueues it
 *   2. Worker pool     — up to CONCURRENCY browsers run in parallel (worker_threads)
 *   3. Progress        — workers UPDATE progress/extracted_count via db calls
 *   4. Logging         — workers call logEvent() which inserts into scraper_logs
 *   5. SSE stream      — subscribers (HTTP clients) get pushed new logs in real-time
 *   6. cancelJob()     — sets status=CANCELLED; workers check a flag before each page
 * 
 * The module-to-scraper mapping is defined in SCRAPER_MAP; adding a new module
 * means only adding one entry there — no changes to the job runner loop needed.
 */

"use strict";

const db = require("../config/database");
const { randomDelay } = require("./antiRobotService");

// Maximum concurrent Playwright browser contexts
const CONCURRENCY = Number(process.env.PLAYWRIGHT_CONCURRENCY) || 3;

/**
 * mysql2 auto-parses JSON-typed columns into real JS values, so `job.keywords`/
 * `job.config` already arrive as an array/object, not a JSON string — calling
 * JSON.parse() on them again throws (e.g. JSON.parse(["a"]) stringifies to "a",
 * which isn't valid JSON). Only parse if the driver ever hands back a raw string.
 */
function parseJsonColumn(value, fallback) {
  if (value == null) return fallback;
  if (typeof value !== "string") return value;
  try {
    return JSON.parse(value);
  } catch {
    return fallback;
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Module → Scraper mapping
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Maps job module strings to their scraper factory functions.
 * Each factory receives (jobId, keywords, config) and resolves when done.
 */
function getScraper(module) {
  const MAP = {
    facebook:           () => require("../scrapers/facebookScraper").run,
    linkedin:           () => require("../scrapers/linkedinScraper").run,
    twitter:            () => require("../scrapers/twitterScraper").run,
    google_maps:        () => require("../scrapers/googleMapsScraper").run,
    website_crawler:    () => require("../scrapers/websiteCrawler").run,
    haraj:              () => require("../scrapers/harajScraper").run,
    classified_generic: () => require("../scrapers/harajScraper").runGeneric,
  };
  const factory = MAP[module];
  if (!factory) throw new Error(`Unknown scraping module: "${module}"`);
  return factory();
}

// ─────────────────────────────────────────────────────────────────────────────
// In-memory structures
// ─────────────────────────────────────────────────────────────────────────────

// Set of jobIds currently being processed by the worker pool
const _activeJobs = new Set();

// SSE subscribers: Map<jobId, Set<responseObject>>
const _sseSubscribers = new Map();

// Cancellation flags: Map<jobId, boolean>
const _cancelFlags = new Map();

// ─────────────────────────────────────────────────────────────────────────────
// SSE (Server-Sent Events) helpers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Registers an HTTP response object to receive SSE events for a job.
 * Call this from the GET /v1/jobs/:id/logs route.
 * 
 * @param {number} jobId
 * @param {import('express').Response} res
 */
function subscribe(jobId, res) {
  if (!_sseSubscribers.has(jobId)) {
    _sseSubscribers.set(jobId, new Set());
  }
  _sseSubscribers.get(jobId).add(res);

  // Remove on client disconnect
  res.on("close", () => {
    const subs = _sseSubscribers.get(jobId);
    if (subs) subs.delete(res);
  });
}

/**
 * Pushes a log event to all SSE subscribers for a job.
 * @param {number} jobId
 * @param {{ level: string, message: string, meta?: object }} event
 */
function _pushEvent(jobId, event) {
  const subs = _sseSubscribers.get(jobId);
  if (!subs || subs.size === 0) return;
  const data = `data: ${JSON.stringify(event)}\n\n`;
  for (const res of subs) {
    try { res.write(data); } catch { /* client gone */ }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Logging
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Inserts a log row into scraper_logs and pushes it to SSE subscribers.
 * 
 * @param {number} jobId
 * @param {"INFO"|"WARN"|"ERROR"|"DEBUG"} level
 * @param {string} message
 * @param {object} [meta]
 */
async function logEvent(jobId, level, message, meta = null) {
  const entry = { level, message, meta, created_at: new Date().toISOString() };
  try {
    await db("scraper_logs").insert({
      job_id: jobId,
      level,
      message: message.substring(0, 2000),
      meta: meta ? JSON.stringify(meta) : null,
    });
  } catch (e) {
    console.warn("[JobManager] Failed to persist log:", e.message);
  }
  _pushEvent(jobId, entry);
  console.log(`[Job ${jobId}] [${level}] ${message}`);
}

// ─────────────────────────────────────────────────────────────────────────────
// Progress
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Updates the job's progress and extracted_count in the DB.
 * @param {number} jobId
 * @param {number} progress       0-100
 * @param {number} extractedCount Total records extracted so far
 */
async function updateProgress(jobId, progress, extractedCount) {
  await db("scrape_jobs").where({ id: jobId }).update({
    progress: Math.min(100, Math.round(progress)),
    extracted_count: extractedCount,
  });
  _pushEvent(jobId, { type: "progress", progress, extractedCount });
}

// ─────────────────────────────────────────────────────────────────────────────
// Job CRUD
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Creates a new scraping job and enqueues it for processing.
 * 
 * @param {object} opts
 * @param {number}   opts.userId   ID of the authenticated user
 * @param {string}   opts.module   Scraping module key (e.g. "facebook")
 * @param {string[]} opts.keywords Array of keyword strings
 * @param {object}   [opts.config] Optional module-specific config
 * @returns {Promise<{ jobId: number }>}
 */
async function createJob({ userId, module, keywords, config = {} }) {
  // Validate module
  getScraper(module); // Throws if invalid

  const [jobId] = await db("scrape_jobs").insert({
    user_id: userId,
    module,
    keywords: JSON.stringify(keywords),
    config: JSON.stringify(config),
    status: "QUEUED",
    progress: 0,
    extracted_count: 0,
  });

  // Async enqueue — does not block the HTTP response
  setImmediate(() => _processQueue());

  return { jobId };
}

/**
 * Returns current status and progress for a job.
 * @param {number} jobId
 */
async function getJobStatus(jobId) {
  const job = await db("scrape_jobs").where({ id: jobId }).first();
  if (!job) return null;
  return {
    id: job.id,
    module: job.module,
    keywords: parseJsonColumn(job.keywords, []),
    status: job.status,
    progress: job.progress,
    extractedCount: job.extracted_count,
    errorMessage: job.error_message,
    startedAt: job.started_at,
    completedAt: job.completed_at,
    createdAt: job.created_at,
  };
}

/**
 * Cancels a running or queued job.
 * @param {number} jobId
 */
async function cancelJob(jobId) {
  _cancelFlags.set(jobId, true);
  await db("scrape_jobs")
    .where({ id: jobId })
    .whereIn("status", ["QUEUED", "RUNNING"])
    .update({ status: "CANCELLED", completed_at: db.fn.now() });
}

/**
 * Returns true if the job has been cancelled (scrapers should check this
 * between page fetches to exit early).
 * @param {number} jobId
 */
function isCancelled(jobId) {
  return !!_cancelFlags.get(jobId);
}

// ─────────────────────────────────────────────────────────────────────────────
// Worker / Queue Runner
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Processes queued jobs up to the concurrency limit.
 * Called on each createJob() and when a job finishes.
 */
async function _processQueue() {
  if (_activeJobs.size >= CONCURRENCY) return; // Pool is full

  // Grab the oldest QUEUED job
  const job = await db("scrape_jobs")
    .where({ status: "QUEUED" })
    .orderBy("created_at", "asc")
    .first();

  if (!job) return; // Nothing to do

  // Claim the job atomically
  const claimed = await db("scrape_jobs")
    .where({ id: job.id, status: "QUEUED" })
    .update({ status: "RUNNING", started_at: db.fn.now() });
  if (!claimed) return; // Another process claimed it first

  const jobId = job.id;
  _activeJobs.add(jobId);

  // Run scraper in async context (does not block other jobs)
  _runJob(job).finally(() => {
    _activeJobs.delete(jobId);
    _cancelFlags.delete(jobId);
    _sseSubscribers.delete(jobId);
    // Try to process next queued job
    setImmediate(() => _processQueue());
  });
}

/**
 * Executes the scraping logic for a single job row.
 * @param {object} job  Full row from scrape_jobs table
 */
async function _runJob(job) {
  const jobId = job.id;

  try {
    const keywords = parseJsonColumn(job.keywords, []);
    const config = parseJsonColumn(job.config, {});

    await logEvent(jobId, "INFO", `Job started: module="${job.module}", keywords=${keywords.length}`);

    const scraper = getScraper(job.module);
    await scraper(jobId, keywords, config, {
      logEvent: (level, msg, meta) => logEvent(jobId, level, msg, meta),
      updateProgress: (pct, count) => updateProgress(jobId, pct, count),
      isCancelled: () => isCancelled(jobId),
    });

    if (isCancelled(jobId)) {
      await logEvent(jobId, "WARN", "Job was cancelled by user.");
      return; // Status already set to CANCELLED
    }

    const finalJob = await db("scrape_jobs").where({ id: jobId }).first();
    await db("scrape_jobs").where({ id: jobId }).update({
      status: "DONE",
      progress: 100,
      completed_at: db.fn.now(),
    });
    await logEvent(jobId, "INFO", `Job complete. Extracted ${finalJob.extracted_count} records.`);
  } catch (err) {
    console.error(`[JobManager] Job ${jobId} failed:`, err);
    await db("scrape_jobs").where({ id: jobId }).update({
      status: "FAILED",
      error_message: String(err.message).substring(0, 1000),
      completed_at: db.fn.now(),
    });
    await logEvent(jobId, "ERROR", `Job failed: ${err.message}`);
  }
}

// Boot the queue on startup (pick up any RUNNING-but-orphaned jobs)
db("scrape_jobs")
  .where({ status: "RUNNING" })
  .update({ status: "QUEUED", started_at: null })
  .then(() => _processQueue())
  .catch((e) => console.warn("[JobManager] Startup queue scan failed:", e.message));

module.exports = {
  createJob,
  getJobStatus,
  cancelJob,
  isCancelled,
  logEvent,
  updateProgress,
  subscribe,
};
