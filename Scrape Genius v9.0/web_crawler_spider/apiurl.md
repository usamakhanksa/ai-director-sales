# ScrapeGenius API & Scraping Strategy Overview

This document provides a comprehensive review of all APIs, their endpoints, the target websites they scrape, and the exact strategies employed. As requested, all data constraints (limits) are now fully unlocked for unlimited pagination (`limit` constraints like `.max(25)` have been successfully removed globally).

## 1. Next.js Frontend APIs (`/api/*`)

These APIs handle rapid, synchronous scraping tasks directly from the Next.js server, often using lightweight HTTP requests and HTML parsing to return data instantly.

### Search Engine Scrapers
* **Google Search** (`/api/scrape/google-search`)
  * **Target:** `google.com/search`
  * **Strategy:** Lightweight HTTP GET requests using Axios with rotating User-Agents. Parses the HTML DOM using Cheerio to extract SERP titles, links, and snippets.
  * **Limit:** Unlimited (Pagination enabled).
* **Bing Search** (`/api/scrape/bing-search`)
  * **Target:** `bing.com/search`
  * **Strategy:** Axios + Cheerio. Extracts natural search results and snippets.
  * **Limit:** Unlimited.
* **Yahoo Search** (`/api/scrape/yahoo-search`)
  * **Target:** `search.yahoo.com/search`
  * **Strategy:** Axios + Cheerio.
  * **Limit:** Unlimited.
* **DuckDuckGo Search** (`/api/scrape/duckduckgo-search`)
  * **Target:** `duckduckgo.com/html`
  * **Strategy:** Axios + Cheerio using the non-JS HTML fallback version of DuckDuckGo.
  * **Limit:** Unlimited.

### Directory Scrapers (B2B/Indian Markets)
* **IndiaMart** (`/api/scrape/indiamart`)
  * **Target:** `dir.indiamart.com`
  * **Strategy:** Axios + Cheerio. Parses supplier directories for company names, contact links, and product info.
* **JustDial** (`/api/scrape/justdial`)
  * **Target:** `justdial.com`
  * **Strategy:** Axios + Cheerio. Scrapes local business listings.
* **Sulekha** (`/api/scrape/sulekha`)
  * **Target:** `sulekha.com`
  * **Strategy:** Axios + Cheerio. 
* **Business Directory Generic** (`/api/scrape/business-directory`)
  * **Target:** Various standard directory structures.
  * **Strategy:** HTML DOM Parsing looking for standard schema.org business data.

### Utility Scrapers
* **WHOIS Lookups** (`/api/scrape/whois`)
  * **Target:** WHOIS TCP port 43 servers.
  * **Strategy:** Uses the native Node.js `net` TCP module to directly ping WHOIS registrars to extract domain ownership, registration dates, and admin contacts.
* **Website Data Center** (`/api/scrape/website-data-center`)
  * **Target:** Any user-provided URL.
  * **Strategy:** Fetches the page and runs regex patterns to harvest emails, phones, and social media handles.
  * **Limit:** Unlocked (previously max 25 pages, now unlimited).
* **Document & Image Scrapers** (`/api/scrape/document-data-scraper`, `/api/scrape/image-data-scraper`)
  * **Target:** Direct file URLs or Web Pages.
  * **Strategy:** Parses the DOM specifically for `<img>`, `<a>` (with document extensions like `.pdf`, `.docx`), and extracts metadata.

---

## 2. Express Backend APIs (`/v1/*`)

The Express backend handles long-running, asynchronous background scraping jobs using headless browser automation via Playwright.

### Job Management APIs
* **Create Job:** `POST /v1/jobs`
* **List Jobs:** `GET /v1/jobs?status=&limit=` (Limit upgraded to 1000/unlimited pagination).
* **Job Progress:** `GET /v1/jobs/:id`
* **Live Network Logs:** `GET /v1/jobs/:id/logs` (Uses SSE - Server Sent Events. Updated to show live network links fetched by crawlers).
* **Job Results:** `GET /v1/jobs/:id/results?limit=` (Pagination upgraded to unlimited).

### Social Media Scrapers
* **Facebook Scraper** (`backend/src/scrapers/facebookScraper.js`)
  * **Target:** `facebook.com`
  * **Strategy:** **SERP Dorking + Playwright Deep-Visit.** Since Facebook requires login for search, the scraper first uses search engines with dorks (e.g., `site:facebook.com "keyword" (phone OR email)`). It then launches a stealth Playwright browser to deep-visit the discovered Facebook profiles/pages to extract the phone, email, and description.
* **LinkedIn Scraper** (`backend/src/scrapers/linkedinScraper.js`)
  * **Target:** `linkedin.com/in` and `linkedin.com/company`
  * **Strategy:** **SERP Dorking + Pattern Guessing.** Uses Google/Bing/Yahoo with queries like `site:linkedin.com/in "keyword" email "@gmail.com"`. It reads the email directly from the SERP snippet (bypassing LinkedIn login blocks) and uses educated guessing based on Name + Company domain.
* **Twitter / X Scraper** (`backend/src/scrapers/twitterScraper.js`)
  * **Target:** `twitter.com/search`
  * **Strategy:** **Playwright Stealth.** Directly loads the Twitter public search page (`/search?q=...&f=live`). Uses Playwright to execute JS, perform human-like infinite scrolling, and extracts tweets. Regex is then applied to the tweet bodies to harvest phones and emails.

### Google Maps Scraper
* **Google Maps** (`backend/src/scrapers/googleMapsScraper.js`)
  * **Target:** `maps.google.com` and `Business Websites`
  * **Strategy:** **Playwright + Deep Crawl.** Navigates to Maps search. Automatically scrolls the results panel injecting Javascript to force lazy-loading until all listings are found. Clicks each listing to parse the side-panel for Name, Phone, Rating, and Website.
  * **Secondary Strategy:** Deep-visits the discovered business website using Axios to harvest the business Email and Social Media links (Instagram, Facebook, etc).
  * **Limit:** Unlocked (previously max 60 per keyword, now default 1000 or Infinity).

### Classified Ads Scraper
* **Haraj & MENA Classifieds** (`backend/src/scrapers/harajScraper.js`)
  * **Target:** 20+ Platforms including `haraj.com.sa`, `opensooq.com`, `dubizzle.com`, `olx.com.eg`, `propertyfinder.sa`, etc.
  * **Strategy:** Uses specific site routing logic to navigate to search result pages. Scrapes listing cards and optionally deep-visits individual posts to extract seller phone numbers, prices, and locations. Fully supports RTL Arabic keywords.

### Deep Website Crawler (Live Website Data)
* **AnyWebsite Deep Crawler** (`backend/src/scrapers/websiteCrawler.js`)
  * **Target:** Any provided seed URLs.
  * **Strategy:** **BFS (Breadth-First Search) Hybrid.** Fast-fetches the initial URL using Axios. It analyzes the text-to-HTML ratio. If the page is a modern JS-heavy SPA (React/Vue), it automatically falls back to spinning up a Playwright instance to execute the JS. It recursively crawls internal links up to a specified depth, extracting all emails, phones, and social links it encounters. 

---

## 3. Data Limits & Configuration Review
Following the complete code review, all limiting factors preventing massive data scraping have been resolved:
* **UI/Frontend:** The `<input>` maximum constraints (`max="25"`, `max="40"`) have been removed from the Next.js UI component forms.
* **API Validation:** Zod schema limits (e.g. `.max(25)`) have been stripped out of the API route definitions. DataTables AJAX can now request sizes of 1000, 4000, or unlimited without throwing validation errors.
* **Database Queries:** Backend SQL queries now properly respect the `limit=0` or `all=true` flags to stream out complete record sets to the CSV exporters and UI tables.
* **Scraper Engines:** Hardcoded fallback caps (like 60 places per keyword on Google Maps) have been pushed to 1000/Infinity to guarantee deep extraction runs continuously until completion.
