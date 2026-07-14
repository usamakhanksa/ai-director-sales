/**
 * AI Enhancement Service
 * 
 * Provides AI-powered data extraction and analysis capabilities
 * to enhance the existing scraping functionality.
 */

"use strict";

const axios = require("axios");
const cheerio = require("cheerio");
const db = require("../config/database");

// Configuration for AI services
const AI_CONFIG = {
  // Local Ollama configuration (free option)
  ollama: {
    enabled: process.env.OLLAMA_ENABLED === 'true',
    baseUrl: process.env.OLLAMA_BASE_URL || 'http://localhost:11434',
    model: process.env.OLLAMA_MODEL || 'llama3.2'
  },
  
  // Groq configuration (free tier option)
  groq: {
    enabled: !!process.env.GROQ_API_KEY,
    baseUrl: 'https://api.groq.com/openai/v1',
    model: process.env.GROQ_MODEL || 'llama3-8b-8192'
  },
  
  // Fallback to local processing if no AI service is configured
  fallback: {
    enabled: true
  }
};

/**
 * Extract structured data from HTML using AI
 */
const extractStructuredData = async (htmlContent, schema = {}, context = '') => {
  try {
    const prompt = `
      Extract the following information from the provided HTML:
      ${JSON.stringify(schema)}
      
      Context: ${context}
      
      HTML Content (first 4000 chars):
      ${htmlContent.substring(0, 4000)}
      
      Return ONLY valid JSON with the extracted data following the schema exactly.
      If information is not available, use null for that field.
    `;
    
    const result = await callAIService(prompt);
    
    // Try to parse the result as JSON
    try {
      return JSON.parse(result);
    } catch (e) {
      console.warn('AI service returned non-JSON, attempting fallback parsing');
      return extractWithCheerio(htmlContent, schema);
    }
  } catch (error) {
    console.error('AI extraction failed, falling back to Cheerio:', error.message);
    return extractWithCheerio(htmlContent, schema);
  }
};

/**
 * Extract data using Cheerio as fallback
 */
const extractWithCheerio = (html, schema) => {
  const $ = cheerio.load(html);
  const result = {};
  
  // Common extraction patterns
  Object.keys(schema).forEach(field => {
    switch (field) {
      case 'email':
        result[field] = getEmails($)[0] || null;
        break;
      case 'phone':
        result[field] = getPhones($)[0] || null;
        break;
      case 'name':
        result[field] = getName($) || null;
        break;
      case 'address':
        result[field] = getAddress($) || null;
        break;
      case 'website':
        result[field] = getWebsite($) || null;
        break;
      case 'socialLinks':
        result[field] = getSocialLinks($) || null;
        break;
      default:
        result[field] = null;
    }
  });
  
  return result;
};

/**
 * Extract emails from the page
 */
const getEmails = ($) => {
  const emails = [];
  
  // Look for mailto: links
  $('a[href^="mailto:"]').each((i, elem) => {
    const email = $(elem).attr('href').replace('mailto:', '').split('?')[0];
    if (isValidEmail(email)) emails.push(email);
  });
  
  // Look for text that looks like emails
  const text = $('body').text();
  const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g;
  const foundEmails = text.match(emailRegex) || [];
  
  foundEmails.forEach(email => {
    if (isValidEmail(email) && !emails.includes(email)) {
      emails.push(email);
    }
  });
  
  return [...new Set(emails)]; // Remove duplicates
};

/**
 * Extract phone numbers from the page
 */
const getPhones = ($) => {
  const phones = [];
  
  // Look for tel: links
  $('a[href^="tel:"]').each((i, elem) => {
    const phone = $(elem).attr('href').replace('tel:', '');
    phones.push(cleanPhoneNumber(phone));
  });
  
  // Look for text that looks like phones
  const text = $('body').text();
  const phoneRegex = /[\+]?[\d\s\-\(\)]{10,}/g;
  const foundPhones = text.match(phoneRegex) || [];
  
  foundPhones.forEach(phone => {
    const cleaned = cleanPhoneNumber(phone);
    if (cleaned && !phones.includes(cleaned)) {
      phones.push(cleaned);
    }
  });
  
  return [...new Set(phones)]; // Remove duplicates
};

/**
 * Extract business name
 */
const getName = ($) => {
  // Try common selectors for business names
  const selectors = [
    'h1', 'title', '[class*="name"]', '[id*="name"]',
    '[class*="title"]', '[id*="title"]', '.brand', '.logo'
  ];
  
  for (const selector of selectors) {
    const element = $(selector).first();
    if (element.length && element.text().trim()) {
      return element.text().trim();
    }
  }
  
  return null;
};

/**
 * Extract address
 */
const getAddress = ($) => {
  // Try common selectors for addresses
  const selectors = [
    '[class*="address"]', '[id*="address"]', 
    '[class*="location"]', '[id*="location"]',
    '.contact-info', '.info-box'
  ];
  
  for (const selector of selectors) {
    const element = $(selector).first();
    if (element.length && element.text().trim()) {
      return element.text().trim();
    }
  }
  
  return null;
};

/**
 * Extract website URL
 */
const getWebsite = ($) => {
  const links = $('a[href]').map((i, elem) => $(elem).attr('href')).get();
  
  // Look for external links that might be the main website
  const websiteRegex = /^https?:\/\/(?!.*\b(google|facebook|linkedin|twitter|instagram)\b)[^\/]+/;
  
  for (const link of links) {
    if (websiteRegex.test(link)) {
      return link;
    }
  }
  
  return null;
};

/**
 * Extract social media links
 */
const getSocialLinks = ($) => {
  const socials = {};
  
  const socialPatterns = {
    facebook: /facebook\.com\/([a-zA-Z0-9._-]+)/i,
    instagram: /instagram\.com\/([a-zA-Z0-9._-]+)/i,
    twitter: /(?:twitter\.com|x\.com)\/([a-zA-Z0-9._-]+)/i,
    linkedin: /linkedin\.com\/(?:in|company)\/([a-zA-Z0-9._-]+)/i,
    youtube: /youtube\.com\/(?:c|channel|user)\/([a-zA-Z0-9._-]+)/i
  };
  
  const links = $('a[href]').map((i, elem) => $(elem).attr('href')).get();
  
  for (const link of links) {
    for (const [platform, pattern] of Object.entries(socialPatterns)) {
      const match = link.match(pattern);
      if (match) {
        socials[platform] = link;
        break;
      }
    }
  }
  
  return socials;
};

/**
 * Validate email format
 */
const isValidEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

/**
 * Clean phone number
 */
const cleanPhoneNumber = (phone) => {
  // Remove all non-digit characters except +
  const cleaned = phone.replace(/[^\d\+]/g, '');
  
  // Basic validation - should have at least 7 digits
  if (cleaned.replace(/\+/g, '').length >= 7) {
    return cleaned;
  }
  
  return null;
};

/**
 * Analyze review sentiment for pain points
 */
const analyzeReviewSentiment = async (reviews) => {
  if (!reviews || reviews.length === 0) {
    return { overall_sentiment: 'neutral', pain_points: [], suggestions: [] };
  }
  
  const prompt = `
    Analyze these customer reviews and identify:
    1. Overall sentiment (positive/negative/neutral)
    2. Pain points or complaints
    3. Suggestions for improvement
    
    Reviews:
    ${reviews.slice(0, 10).join('\n')}  // Limit to first 10 reviews
    
    Return as JSON with format:
    {
      "overall_sentiment": "...",
      "pain_points": [...],
      "suggestions": [...],
      "severity_score": 1-10
    }
  `;
  
  try {
    const result = await callAIService(prompt);
    return JSON.parse(result);
  } catch (error) {
    console.error('Review sentiment analysis failed:', error.message);
    return { overall_sentiment: 'neutral', pain_points: [], suggestions: [], severity_score: 5 };
  }
};

/**
 * Calculate lead score based on scraped data
 */
const calculateLeadScore = (scrapedData) => {
  let score = 0;
  const factors = [];
  
  // Check for generic email (high intent)
  if (scrapedData.email && /@(gmail|yahoo|hotmail|outlook)/i.test(scrapedData.email)) {
    score += 25;
    factors.push('Uses generic email provider (high conversion potential)');
  }
  
  // Check for missing website (opportunity)
  if (!scrapedData.website) {
    score += 20;
    factors.push('No website detected (potential PMS candidate)');
  }
  
  // Check for social media presence
  if (scrapedData.socialLinks && Object.keys(scrapedData.socialLinks).length > 0) {
    score += 10;
    factors.push('Active on social media');
  }
  
  // Check for business size indicators
  if (scrapedData.phone && scrapedData.phone.length > 10) {
    score += 10;
    factors.push('Multiple phone numbers detected');
  }
  
  // Check for review sentiment if available
  if (scrapedData.reviewAnalysis && scrapedData.reviewAnalysis.severity_score > 7) {
    score += 15;
    factors.push('Negative reviews indicate operational issues');
  }
  
  // Cap score at 100
  score = Math.min(score, 100);
  
  return {
    score,
    factors,
    rating: score >= 70 ? 'Hot Lead' : score >= 40 ? 'Warm Lead' : 'Cold Lead'
  };
};

/**
 * Classify whether a scraped classified-ad text represents someone
 * REQUESTING/BUYING a product or service (a lead) vs. selling/unrelated noise.
 *
 * @param {string} text     Raw ad text (Arabic and/or English).
 * @param {string} product  What we sell, e.g. "hotel management software / PMS".
 */
const classifyLeadIntent = async (text, product = "hotel management software / PMS") => {
  const snippet = (text || "").slice(0, 600).trim();
  if (!snippet) {
    return { isLead: false, label: "NOT_LEAD", reason: "Empty ad text" };
  }

  const prompt = `You are a lead classifier for a company that sells: ${product}.
A classified ad text will be provided. It may be in English, Arabic, or mixed.
Determine if the poster is LOOKING TO BUY or REQUESTING the product/service above.
They are NOT a lead if they are selling, offering, advertising, or the ad is unrelated.
Respond with EXACTLY one word: "LEAD" if they are a potential lead, otherwise "NOT_LEAD".
Do not include any other text.

Ad text:
"""
${snippet}
"""`;

  try {
    const result = await callAIService(prompt);
    const upper = (result || "").trim().toUpperCase();
    const label = upper.includes("NOT_LEAD") ? "NOT_LEAD" : upper.includes("LEAD") ? "LEAD" : "NOT_LEAD";
    return { isLead: label === "LEAD", label };
  } catch (error) {
    console.error("Lead intent classification failed:", error.message);
    return { isLead: false, label: "NOT_LEAD", reason: "AI service unavailable" };
  }
};

/**
 * Call the appropriate AI service
 */
const callAIService = async (prompt) => {
  // Try Ollama first (local, free)
  if (AI_CONFIG.ollama.enabled) {
    try {
      const response = await axios.post(`${AI_CONFIG.ollama.baseUrl}/api/generate`, {
        model: AI_CONFIG.ollama.model,
        prompt: prompt,
        stream: false
      }, {
        timeout: 30000
      });
      
      return response.data.response;
    } catch (error) {
      console.log('Ollama unavailable, trying next option');
    }
  }
  
  // Try Groq (free tier)
  if (AI_CONFIG.groq.enabled) {
    try {
      const response = await axios.post(`${AI_CONFIG.groq.baseUrl}/chat/completions`, {
        model: AI_CONFIG.groq.model,
        messages: [{ role: 'user', content: prompt }],
        temperature: 0.1,
        max_tokens: 1000
      }, {
        headers: {
          'Authorization': `Bearer ${process.env.GROQ_API_KEY}`,
          'Content-Type': 'application/json'
        },
        timeout: 30000
      });
      
      return response.data.choices[0].message.content;
    } catch (error) {
      console.log('Groq unavailable, using fallback');
    }
  }
  
  // Fallback to basic processing
  if (AI_CONFIG.fallback.enabled) {
    console.log('Using fallback processing');
    return 'Fallback processing - AI service unavailable';
  }
  
  throw new Error('No AI services available');
};

module.exports = {
  extractStructuredData,
  analyzeReviewSentiment,
  calculateLeadScore,
  classifyLeadIntent,
  AI_CONFIG
};