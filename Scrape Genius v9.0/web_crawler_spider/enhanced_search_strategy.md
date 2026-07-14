# ScrapeGenius Pro — Enhanced Search Strategy Implementation Plan

## Overview
This plan enhances the existing ScrapeGenius platform with advanced search strategies, AI-powered parsing, and enhanced data extraction capabilities as requested in the user query.

## Phase 1: Advanced Search URL Patterns & Dorking Engine

### 1.1 Enhanced Google Search Dorks
Implement advanced dork patterns for email and phone harvesting:

```javascript
// Google Search Dork Templates
const DORK_TEMPLATES = {
  // Email harvesting
  BUSINESS_EMAIL: 'site:facebook.com OR site:linkedin.com "{keyword}" ("gmail.com" OR "yahoo.com" OR "email me at")',
  GENERIC_EMAIL: 'site:{domain} ("contact" OR "about") ("email" OR "@")',
  
  // Phone/WhatsApp harvesting
  PHONE_NUMBERS: 'site:instagram.com OR site:facebook.com "{keyword}" ("whatsapp" OR "wa.me" OR "{country_code}")',
  
  // Professional networks
  LINKEDIN_PROFILES: 'site:linkedin.com/in/ "{keyword}" "{location}" ("gmail.com" OR "email")',
  COMPANY_DM: 'site:linkedin.com/company/ "{company}" ("ceo" OR "founder" OR "manager")',
  
  // File harvesting
  PDF_RESUMES: 'site:linkedin.com/in/ "{keyword}" filetype:pdf',
  
  // Directory bypass
  CONTACT_PAGES: 'inurl:contact OR inurl:about "{keyword}" ("gmail.com" OR "phone")',
  
  // Index of exposed files
  EXPOSED_FILES: 'intitle:"index of" "contacts.csv" OR "leads.xlsx" "{keyword}"',
  WP_UPLOADS: 'inurl:"/wp-content/uploads/" "{keyword}" filetype:pdf'
};
```

### 1.2 MENA/Arabic Classifieds Support
Enhance harajScraper.js and add support for multiple Arabic platforms:

```javascript
// MENA Classifieds Search Patterns
const MENA_CLASSIFIEDS = {
  haraj: {
    baseUrl: 'https://haraj.com.sa',
    searchPattern: '/search/{keyword}?city={city}',
    dork: 'site:haraj.com.sa "{keyword}" ("جوال" OR "واتساب" OR "whatsapp")'
  },
  opensooq: {
    baseUrl: 'https://sa.opensooq.com',
    searchPattern: '/ar/search/{keyword}',
    dork: 'site:opensooq.com "{arabic_keyword}" ("جوال" OR "واتساب")'
  },
  propertyfinder: {
    baseUrl: 'https://propertyfinder.sa',
    searchPattern: '/en/search?{params}',
    dork: 'site:propertyfinder.sa "{keyword}" "for rent" "{location}"'
  }
};
```

### 1.3 Google Maps Coordinate Lock Hack
Enhance googleMapsScraper.js with coordinate-based targeting:

```javascript
// Coordinate-based targeting for Google Maps
const COORDINATE_TARGETS = {
  RIYADH: { lat: 24.7136, lng: 46.6753, zoom: 12 },
  JEDDAH: { lat: 21.5433, lng: 39.1728, zoom: 12 },
  MECCA: { lat: 21.4225, lng: 39.8262, zoom: 12 },
  MEDINA: { lat: 24.5247, lng: 39.5692, zoom: 12 }
};

// Enhanced search URL: https://www.google.com/maps/search/{URL_ENCODED_KEYWORD}/{lat},{lng},{zoom}z
```

## Phase 2: AI-Powered Scraping Enhancements

### 2.1 AI HTML-to-JSON Parser
Create an AI-powered parser service that can extract structured data from complex HTML:

```javascript
// AI Parser Service (using local Ollama or Groq API)
const aiParser = {
  extractStructuredData: async (htmlContent, extractionSchema) => {
    const prompt = `Extract the following fields from this HTML: ${JSON.stringify(extractionSchema)}.
    Return ONLY valid JSON with the extracted data. HTML: ${htmlContent.substring(0, 4000)}`;
    
    // Call local Ollama or external API
    const response = await callAIService(prompt);
    return JSON.parse(response);
  },
  
  generateSelectors: async (htmlSample, targetField) => {
    // Generate robust CSS selectors for a target field
    const prompt = `Given this HTML sample, provide a resilient CSS selector for the ${targetField}.
    The selector should work even if class names change. HTML: ${htmlSample}`;
    
    return await callAIService(prompt);
  }
};
```

### 2.2 AI Sentiment Analysis for Review Mining
Add sentiment analysis to Google Maps reviews to identify pain points:

```javascript
// AI Review Sentiment Analysis
const reviewAnalyzer = {
  analyzePainPoints: async (reviews) => {
    const prompt = `Analyze these business reviews and identify operational failures or pain points.
    Rate severity 1-10 and suggest sales pitch. Reviews: ${reviews.join('; ')}`;
    
    return await callAIService(prompt);
  }
};
```

## Phase 3: Enhanced Scraping Infrastructure

### 3.1 Playwright Resource Blocking
Add resource blocking to improve scraping speed:

```javascript
// In browserEngine.js
const setupResourceBlocking = async (page) => {
  await page.route('**/*', route => {
    const resourceType = route.request().resourceType();
    if (['image', 'media', 'font', 'stylesheet', 'tracking'].includes(resourceType)) {
      route.abort();
    } else {
      route.continue();
    }
  });
};
```

### 3.2 Smart Retry & Exponential Backoff
Implement robust retry mechanisms:

```javascript
// Enhanced axios with retry logic
const axiosRetry = require('axios-retry');
axiosRetry(axios, { 
  retries: 3, 
  retryDelay: axiosRetry.exponentialDelay,
  retryCondition: (error) => error.response?.status === 429 || error.response?.status === 403
});
```

### 3.3 Caching Layer
Add Redis or local caching for frequently scraped URLs:

```javascript
// Simple caching implementation
const cache = new Map();
const CACHE_DURATION = 7 * 24 * 60 * 60 * 1000; // 7 days

const getCachedResult = (key) => {
  const cached = cache.get(key);
  if (cached && Date.now() - cached.timestamp < CACHE_DURATION) {
    return cached.data;
  }
  return null;
};
```

## Phase 4: New Features Implementation

### 4.1 Dork Generator Service
Create a new endpoint for generating search dorks:

```javascript
// backend/src/routes/dorkGenerator.routes.js
router.post('/generate', auth, async (req, res) => {
  const { keyword, location, country, intent } = req.body;
  
  const dorks = generateDorks(keyword, location, country, intent);
  res.json({ success: true, data: { dorks } });
});
```

### 4.2 Enhanced Social Media Scrapers
Update existing scrapers with advanced dorking:

```javascript
// Enhanced LinkedIn scraper using SERP dorking
const enhancedLinkedInScrape = async (keywords, config) => {
  const dorks = keywords.map(k => 
    `site:linkedin.com/in "${k}" "${config.location}" ("${config.intent}")`
  );
  
  // Use multiple search engines for redundancy
  const searchEngines = ['google', 'bing', 'yahoo'];
  // ... implementation
};
```

### 4.3 AI Lead Scoring System
Add lead scoring based on scraped data:

```javascript
// Lead scoring algorithm
const calculateLeadScore = (scrapedData) => {
  let score = 0;
  
  // Generic email detection (high intent)
  if (scrapedData.email && /@(gmail|yahoo|hotmail)/.test(scrapedData.email)) {
    score += 25;
  }
  
  // Missing website (high opportunity)
  if (!scrapedData.website) {
    score += 20;
  }
  
  // Negative reviews/pain points
  if (scrapedData.reviewSentiment && scrapedData.reviewSentiment.score < 3) {
    score += 30;
  }
  
  return Math.min(score, 100);
};
```

## Phase 5: Frontend Integration

### 5.1 Enhanced Dashboard UI
Add new UI elements for advanced search options:

- Dork generator panel
- AI scoring indicators
- Advanced filtering options
- Search history and saved searches

### 5.2 New Tool Categories
Add new tool categories in the tools-data.ts:

```javascript
{
  id: "advanced-search",
  label: "Advanced Search Dorking",
  tools: [
    {
      slug: "dork-generator",
      title: "Search Dork Generator",
      description: "Generate advanced search queries for maximum data extraction using Google dorks, Bing searches, and social media patterns.",
      iconSrc: `${ICON_BASE}/search.png`,
      run: { customPage: "/dashboard/tools/dork-generator" }
    },
    {
      slug: "ai-enhanced-scraper",
      title: "AI-Powered Data Extraction",
      description: "Use AI to intelligently extract data from complex websites that traditional scrapers can't handle.",
      iconSrc: `${ICON_BASE}/ai.png`,
      run: { customPage: "/dashboard/tools/ai-scraper" }
    }
  ]
}
```

## Phase 6: Implementation Steps

### Week 1: Core Infrastructure
1. Update googleMapsScraper.js with coordinate locking
2. Enhance harajScraper.js with MENA-specific patterns
3. Add resource blocking to browserEngine.js
4. Implement caching layer

### Week 2: AI Integration
1. Create aiParser service
2. Add review sentiment analysis
3. Implement lead scoring system
4. Add dork generation endpoint

### Week 3: Frontend Integration
1. Add new tool categories
2. Create dork generator UI
3. Enhance existing scrapers with new dork patterns
4. Add AI scoring indicators to results

### Week 4: Testing & Optimization
1. Performance testing
2. Accuracy validation
3. User acceptance testing
4. Documentation updates

## Expected Outcomes
- 40-60% improvement in scraping speed with resource blocking
- 25-30% improvement in data extraction accuracy with AI
- Access to previously unreachable data sources via advanced dorks
- Enhanced lead qualification with AI scoring
- Better performance in Arabic/MENA markets