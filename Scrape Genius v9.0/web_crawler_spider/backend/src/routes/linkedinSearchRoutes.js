const express = require('express');
const router = express.Router();
const { run } = require('../scrapers/linkedinSearchScraper');
const db = require('../config/database');
const { requireAuth } = require('../middleware/auth');
const { requireAuthOrInternal } = require('../middleware/internalAuth');

const requireInternalOrAuth = requireAuthOrInternal(requireAuth);

// Middleware to validate session cookie
const validateSessionCookie = (req, res, next) => {
  const { sessionCookieValue } = req.method === 'GET' ? req.query : req.body;

  if (!sessionCookieValue) {
    return res.status(400).json({
      error: 'sessionCookieValue is required'
    });
  }

  // Basic validation of cookie format (li_at cookie is typically long alphanumeric)
  if (typeof sessionCookieValue !== 'string' || sessionCookieValue.length < 10) {
    return res.status(400).json({
      error: 'Invalid sessionCookieValue format'
    });
  }

  next();
};

async function runSearch(userId, { keyword, sessionCookieValue, limit }, res) {
  let jobId;
  try {
    if (!keyword) {
      return res.status(400).json({ error: 'keyword is required' });
    }

    // Must match the real scrape_jobs schema (user_id/module/keywords/status
    // are required, typed columns) — see linkedinProfileRoutes.js for the
    // same fix applied there. Knex on MySQL returns the insert id as a
    // plain array, e.g. [42] (job[0].id was always undefined before).
    const [id] = await db('scrape_jobs').insert({
      user_id: userId,
      module: 'linkedin_search',
      keywords: JSON.stringify([keyword]),
      config: JSON.stringify({ keyword, limit }),
      status: 'RUNNING',
      started_at: db.fn.now(),
      created_at: db.fn.now(),
    });
    jobId = id;

    const config = { sessionCookieValue };

    const hooks = {
      logEvent: async (level, message) => {
        await db('scraper_logs').insert({
          job_id: jobId,
          level,
          message,
          created_at: new Date().toISOString()
        });
      },
      updateProgress: async (progress, count) => {
        await db('scrape_jobs')
          .where('id', jobId)
          .update({
            progress: Math.round(progress),
            extracted_count: count,
          });
      },
      isCancelled: () => false
    };

    await run(jobId, keyword, config, hooks, limit);

    // 'DONE' is the real scrape_jobs.status enum value — 'COMPLETED' isn't.
    await db('scrape_jobs')
      .where('id', jobId)
      .update({
        status: 'DONE',
        progress: 100,
        completed_at: db.fn.now(),
      });

    const searchResults = await db('linkedin_search_results')
      .where('job_id', jobId)
      .select('*');

    res.json({
      success: true,
      message: `LinkedIn search completed for keyword: "${keyword}"`,
      data: {
        keyword,
        jobId,
        count: searchResults.length,
        results: searchResults.map(result => ({
          fullName: result.full_name,
          title: result.title,
          location: result.location,
          profileUrl: result.profile_url,
          email: result.email,
          phone: result.phone,
          description: result.description
        }))
      }
    });
  } catch (error) {
    console.error('LinkedIn search error:', error);

    if (jobId) {
      await db('scrape_jobs')
        .where('id', jobId)
        .update({ status: 'FAILED', error_message: String(error.message || error).slice(0, 1000) })
        .catch(() => {});
    }

    if (error.name === 'SessionExpired') {
      return res.status(401).json({
        error: 'Session expired. Please provide a valid li_at cookie.',
        name: 'SessionExpired'
      });
    }

    res.status(500).json({
      error: error.message || 'Internal server error during LinkedIn search'
    });
  }
}

// POST route to search LinkedIn profiles
router.post('/', requireInternalOrAuth, validateSessionCookie, async (req, res) => {
  const { keyword, sessionCookieValue, limit = 10 } = req.body;
  await runSearch(req.user.id, { keyword, sessionCookieValue, limit }, res);
});

// GET route to search LinkedIn profiles
router.get('/', requireInternalOrAuth, validateSessionCookie, async (req, res) => {
  const { keyword, sessionCookieValue, limit } = req.query;
  await runSearch(req.user.id, { keyword, sessionCookieValue, limit: limit ? parseInt(limit, 10) : 10 }, res);
});

module.exports = router;
