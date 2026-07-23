/**
 * Google News RSS API Routes
 * 
 * Endpoints:
 *   GET  /v1/scrape/google-news  — Fetch Google News RSS feed for given keyword and language
 */

"use strict";

const express = require("express");
const axios = require("axios");
const xml2js = require("xml2js");
const { z } = require("zod");
const { requireAuth } = require("../middleware/auth");
const { requireAuthOrInternal } = require("../middleware/internalAuth");
const auth = requireAuthOrInternal(requireAuth);

const router = express.Router();

// Validation schema for Google News RSS request
const googleNewsSchema = z.object({
  q: z.string().min(1).max(200), // Search query (raw, not yet URL-encoded)
  hl: z.string().length(2).optional().default("en"), // Language (default: en)
  gl: z.string().length(2).optional().default("US"), // Geographic location (default: US)
  ceid: z.string().optional(), // Specific region edition (e.g., US:en)
});

// Helper function to parse RSS XML to JSON
async function parseRssXml(xmlString) {
  return new Promise((resolve, reject) => {
    xml2js.parseString(xmlString, {
      explicitArray: false,
      ignoreAttrs: false,
      tagNameProcessors: [xml2js.processors.stripPrefix],
    }, (err, result) => {
      if (err) {
        reject(err);
      } else {
        resolve(result);
      }
    });
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// GET /v1/scrape/google-news — Fetch Google News RSS feed
// ─────────────────────────────────────────────────────────────────────────────

router.get("/", auth, async (req, res, next) => {
  try {
    // Validate query parameters
    const validatedParams = googleNewsSchema.safeParse(req.query);
    
    if (!validatedParams.success) {
      return res.status(400).json({
        success: false,
        error: "Invalid parameters",
        details: validatedParams.error.errors,
      });
    }
    
    const { q, hl, gl, ceid } = validatedParams.data;

    // Construct the Google News RSS URL
    let rssUrl = `https://news.google.com/rss/search?q=${encodeURIComponent(q)}`;
    
    // Add language and geographic location parameters
    if (hl) rssUrl += `&hl=${hl}`;
    if (gl) rssUrl += `&gl=${gl}`;
    if (ceid) rssUrl += `&ceid=${ceid}`;
    
    // Add date restriction if provided (e.g., past 24 hours: d, past week: w, past month: m)
    if (req.query.dateRestrict) {
      rssUrl += `&tbs=qdr:${req.query.dateRestrict}`;
    }
    
    // Make request to Google News RSS
    const response = await axios.get(rssUrl, {
      timeout: 10000, // 10 second timeout
      headers: {
        'User-Agent': 'Mozilla/5.0 (compatible; ScrapeGenius/1.0; +https://scrapecenius.com/bot)'
      }
    });
    
    // Parse the RSS XML
    const parsedRss = await parseRssXml(response.data);
    
    // Extract items from RSS feed
    let items = parsedRss.rss.channel.item || [];
    
    // Ensure items is an array (even if only one item)
    if (!Array.isArray(items)) {
      items = [items];
    }
    
    // Transform items to standard format
    const transformedItems = items.map(item => {
      // Parse publication date
      let pubDate = null;
      if (item.pubDate) {
        pubDate = new Date(item.pubDate).toISOString();
      }
      
      // Extract source from news:source element or title
      let source = '';
      if (item.source && typeof item.source === 'object') {
        source = item.source.$.name || '';
      } else if (item.title) {
        // Try to extract source from title format: "Title - Source" or "Source: Title"
        const match = item.title.match(/^(.*?)\s*[-–—]\s*(.+?)$|^([^-]*?):\s*(.*)$/);
        if (match) {
          source = match[2] || match[3] || '';
        }
      }
      
      return {
        title: item.title || '',
        link: item.link || '',
        pubDate: pubDate,
        description: item.description || '',
        source: source,
        guid: item.guid || '',
      };
    });
    
    // Apply pagination if requested
    let paginatedItems = transformedItems;
    if (req.query.limit) {
      const limit = Math.min(parseInt(req.query.limit), 100); // Max 100 items
      const offset = parseInt(req.query.offset) || 0;
      paginatedItems = transformedItems.slice(offset, offset + limit);
    }
    
    res.json({
      success: true,
      data: {
        items: paginatedItems,
        total: transformedItems.length,
        query: q,
        language: hl,
        geographicLocation: gl,
        url: rssUrl,
      },
    });
  } catch (err) {
    console.error("Google News RSS error:", err);
    
    if (err.response) {
      // If Google News returns an error response
      return res.status(err.response.status).json({
        success: false,
        error: `Google News RSS service error: ${err.response.status}`,
        details: err.response.data,
      });
    } else if (err.request) {
      // If request was made but no response received
      return res.status(500).json({
        success: false,
        error: "Unable to connect to Google News RSS service",
      });
    } else {
      // Other errors
      return res.status(500).json({
        success: false,
        error: "Internal server error during Google News RSS processing",
      });
    }
  }
});

module.exports = router;