const express = require("express");
const router = express.Router();
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const db = require("../config/database");
const { scrapeInstagramProfiles } = require("../scrapers/instagramScraper");
const { createJob } = require("../services/jobManager");

// Same secret-verified frontend↔backend identity bridge used by jobs.routes.js
// and classified.routes.js (see middleware/internalAuth.js). The previous
// local implementation here trusted an unauthenticated `x-internal-user-id`
// header with no secret check, and didn't even match the header name
// (`X-User-Id`) the Next.js bridge actually sends.
const requireInternalOrAuth = requireAuthOrInternal(requireAuth);

// Get all social media scraping jobs for the authenticated user
router.get("/jobs", requireInternalOrAuth, async (req, res) => {
  try {
    const jobs = await db("scrape_jobs")
      .where({ user_id: req.user.id })
      .whereIn("module", ["instagram", "facebook", "linkedin", "twitter"])
      .orderBy("created_at", "desc")
      .limit(50);

    res.json({ success: true, jobs });
  } catch (err) {
    console.error("Error fetching social jobs:", err);
    res.status(500).json({ error: "Failed to fetch jobs" });
  }
});

// Start a Facebook / LinkedIn / Twitter contact-scraping job. These modules
// are already registered in jobManager's SCRAPER_MAP and write their results
// to social_results, so job creation just delegates to the generic queue —
// polling/results/logs are handled by GET /v1/jobs/:id, /:id/results, /:id/logs.
function makeSocialJobRoute(moduleName) {
  return async (req, res, next) => {
    try {
      const { keywords, config } = req.body;
      if (!Array.isArray(keywords) || keywords.length === 0) {
        return res.status(400).json({ success: false, error: "keywords array is required" });
      }

      const { jobId } = await createJob({
        userId: req.user.id,
        module: moduleName,
        keywords: keywords.map((k) => String(k).trim()).filter(Boolean),
        config: config || {},
      });

      res.status(202).json({ success: true, data: { jobId } });
    } catch (err) {
      next(err);
    }
  };
}

router.post("/facebook", requireInternalOrAuth, makeSocialJobRoute("facebook"));
router.post("/linkedin", requireInternalOrAuth, makeSocialJobRoute("linkedin"));
router.post("/twitter", requireInternalOrAuth, makeSocialJobRoute("twitter"));

// Start a new Instagram scraping job
router.post("/instagram", requireInternalOrAuth, async (req, res) => {
  const { keywords, config = {} } = req.body;
  
  if (!keywords || !Array.isArray(keywords) || keywords.length === 0) {
    return res.status(400).json({ error: "Keywords array is required" });
  }

  try {
    // Create a new scraping job
    const [jobId] = await db("scrape_jobs").insert({
      user_id: req.user.id,
      module: "instagram",
      keywords: JSON.stringify(keywords),
      config: JSON.stringify(config),
      status: "QUEUED",
      created_at: db.fn.now()
    });

    // Respond immediately with job ID
    res.json({ success: true, jobId });

    // Process the job asynchronously
    processInstagramJob(jobId, keywords, config).catch(console.error);
  } catch (err) {
    console.error("Error starting Instagram job:", err);
    res.status(500).json({ error: "Failed to start job" });
  }
});

// Process Instagram job asynchronously
async function processInstagramJob(jobId, usernames, config) {
  try {
    // Update job status to running
    await db("scrape_jobs").where({ id: jobId }).update({
      status: "RUNNING",
      started_at: db.fn.now(),
      progress: 0
    });

    let completed = 0;
    const total = usernames.length;

    // Update progress callback
    const onProgress = async (current, total, username, error = null) => {
      const progress = Math.round((current / total) * 100);
      
      // Update job progress
      await db("scrape_jobs").where({ id: jobId }).update({
        progress,
        extracted_count: current
      });
      
      // Log the progress
      await db("scraper_logs").insert({
        job_id: jobId,
        level: error ? "error" : "info",
        message: error ? `Failed to scrape ${username}: ${error}` : `Scraped ${username}`,
        meta: JSON.stringify({ username, current, total }),
        created_at: db.fn.now()
      });
    };

    // Perform the scraping
    const results = await scrapeInstagramProfiles(usernames, onProgress);

    // Save results to database
    for (const result of results) {
      await db("instagram_results").insert({
        job_id: jobId,
        username: result.username,
        data: JSON.stringify(result),
        created_at: db.fn.now()
      });
    }

    // Update job status to completed
    await db("scrape_jobs").where({ id: jobId }).update({
      status: "DONE",
      progress: 100,
      completed_at: db.fn.now(),
      extracted_count: results.length
    });

    console.log(`Instagram job ${jobId} completed successfully with ${results.length} results`);
  } catch (err) {
    console.error(`Error processing Instagram job ${jobId}:`, err);
    
    // Update job status to failed
    await db("scrape_jobs").where({ id: jobId }).update({
      status: "FAILED",
      error_message: err.message.substring(0, 1000), // Limit error message length
      completed_at: db.fn.now()
    });

    // Log the error
    await db("scraper_logs").insert({
      job_id: jobId,
      level: "error",
      message: `Job failed: ${err.message}`,
      meta: JSON.stringify({ stack: err.stack }),
      created_at: db.fn.now()
    });
  }
}

// Get Instagram job results
router.get("/instagram/:jobId", requireInternalOrAuth, async (req, res) => {
  const { jobId } = req.params;

  try {
    // Verify the job belongs to the user
    const job = await db("scrape_jobs")
      .where({ id: jobId, user_id: req.user.id, module: "instagram" })
      .first();

    if (!job) {
      return res.status(404).json({ error: "Job not found or unauthorized" });
    }

    // Get the results for this job
    const results = await db("instagram_results")
      .where({ job_id: jobId })
      .select("username", "data", "created_at");

    res.json({ 
      success: true, 
      job,
      results: results.map(r => ({
        ...JSON.parse(r.data),
        createdAt: r.created_at
      }))
    });
  } catch (err) {
    console.error("Error fetching Instagram results:", err);
    res.status(500).json({ error: "Failed to fetch results" });
  }
});

module.exports = router;