const express = require('express');
const router = express.Router();
const { run } = require('../scrapers/linkedinProfileScraper');
const db = require('../config/database');
const { requireAuth } = require('../middleware/auth');
const { requireAuthOrInternal } = require('../middleware/internalAuth');

// Same secret-verified frontend<->backend identity bridge every other
// scrape route uses (see middleware/internalAuth.js) — this route
// previously had no auth at all, so anyone who could reach the backend
// port could kick off a scrape job under no user's identity.
const requireInternalOrAuth = requireAuthOrInternal(requireAuth);

// Middleware to validate session cookie
const validateSessionCookie = (req, res, next) => {
  const { sessionCookieValue } = req.body;

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

// POST route to scrape LinkedIn profile
router.post('/', requireInternalOrAuth, validateSessionCookie, async (req, res) => {
  let jobId;
  try {
    const { profileUrl, sessionCookieValue } = req.body;

    // Validate profile URL
    if (!profileUrl) {
      return res.status(400).json({
        error: 'profileUrl is required'
      });
    }

    // Basic URL validation
    const urlPattern = /^https:\/\/www\.linkedin\.com\/in\/[a-zA-Z0-9_-]+\/?$/;
    if (!urlPattern.test(profileUrl)) {
      return res.status(400).json({
        error: 'Invalid LinkedIn profile URL format'
      });
    }

    // Create a job for tracking — must match the real scrape_jobs schema
    // (backend/migrations/20260714120001_create_scrape_jobs.js): user_id,
    // module, keywords, config, status are all required/typed columns.
    // Knex on MySQL returns the insert id as a plain array, e.g. [42] — not
    // an array of row objects, so destructure it directly (job[0].id was
    // always undefined here).
    const [id] = await db('scrape_jobs').insert({
      user_id: req.user.id,
      module: 'linkedin_profile',
      keywords: JSON.stringify([profileUrl]),
      config: JSON.stringify({ profileUrl }),
      status: 'RUNNING',
      started_at: db.fn.now(),
      created_at: db.fn.now(),
    });
    jobId = id;

    // Prepare config object
    const config = {
      sessionCookieValue: sessionCookieValue
    };

    // Prepare hooks for logging
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

    // Run the scraper
    await run(jobId, [profileUrl], config, hooks);

    // Update job status to completed. The `status` column is a MySQL ENUM
    // restricted to QUEUED/RUNNING/DONE/FAILED/CANCELLED — 'COMPLETED' is
    // not a valid value and this update would previously have failed.
    await db('scrape_jobs')
      .where('id', jobId)
      .update({
        status: 'DONE',
        progress: 100,
        completed_at: db.fn.now(),
      });

    // Fetch the scraped data from the linkedin_profiles table
    const profileData = await db('linkedin_profiles')
      .where('job_id', jobId)
      .orderBy('id', 'desc')
      .first();

    // Return success response with the scraped data
    res.json({
      success: true,
      message: 'LinkedIn profile scraped successfully',
      data: {
        profileUrl,
        jobId,
        result: profileData ? {
          userProfile: {
            fullName: profileData.full_name,
            firstName: profileData.first_name,
            lastName: profileData.last_name,
            title: profileData.title,
            location: profileData.location,
            photo: profileData.photo_url,
            description: profileData.description,
            url: profileData.profile_url
          },
          experiences: profileData.experiences ? JSON.parse(profileData.experiences) : [],
          education: profileData.education ? JSON.parse(profileData.education) : [],
          volunteerExperiences: profileData.volunteer_experiences ? JSON.parse(profileData.volunteer_experiences) : [],
          skills: profileData.skills ? JSON.parse(profileData.skills) : [],
          contactInfo: {
            email: profileData.email,
            phone: profileData.phone
          }
        } : null
      }
    });
  } catch (error) {
    console.error('LinkedIn profile scraping error:', error);

    if (jobId) {
      await db('scrape_jobs')
        .where('id', jobId)
        .update({ status: 'FAILED', error_message: String(error.message || error).slice(0, 1000) })
        .catch(() => {});
    }

    // Handle specific session expired error
    if (error.name === 'SessionExpired') {
      return res.status(401).json({
        error: 'Session expired. Please provide a valid li_at cookie.',
        name: 'SessionExpired'
      });
    }

    res.status(500).json({
      error: error.message || 'Internal server error during LinkedIn scraping'
    });
  }
});

module.exports = router;
