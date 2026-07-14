# Scrape Genius Pro — Complete Enhancement Implementation Plan

## Background & Analysis

After a thorough review of the existing codebase, here is what's currently implemented and what's missing:

### ✅ What Exists
- Next.js 14 (App Router) frontend with MUI + Tailwind
- Basic Auth (signup/login/Google OAuth) — both a Prisma-based Next.js pass and an Express+Knex backend
- Dashboard with stat tiles and API key display
- Tool categories in `lib/tools-data.ts` (11 categories, 20+ tools defined as slugs)
- `lib/scrapers/extract.ts` — solid regex extractors for email, phone, social links
- Express backend (`backend/`) with: auth routes, maps skeleton (TODO stub), Google Custom Search, key quota management
- Prisma schema: User, ApiKey, UsageLog, ScrapedRecord, DashboardStat, ApiConnector, CrmConnection, PurchaseCode
- Knex migrations for 6 tables in the Express backend DB

### ❌ What's Missing / Broken
1. **No actual scraping engines** — Google Maps, Facebook, LinkedIn, Haraj, Twitter, classified sites are all stubs or completely absent
2. **No social media module** (Facebook phones/emails, LinkedIn email finder) — `#social-media` anchor in nav has no tools or routes
3. **No Haraj / classified sites module** — not mentioned anywhere in codebase
4. **No AnyWebsite deep-crawler** — website scraper exists as a route shell but no recursive crawl logic
5. **No export engine** — no `.xlsx`, `.csv`, `.html` generation
6. **No multi-keyword campaign manager** — no queue/job system
7. **No RTL/Arabic localization** — no i18n at all
8. **No anti-robot / stealth browser setup** in the codebase
9. **No scheduler / job queue** — scraping is fire-and-forget
10. **No admin panel** — listed in Express routes but partially implemented
11. **No new module routes** for: Facebook, LinkedIn, Haraj, Twitter, OpenSooq, Dubizzle, etc.
12. **Dashboard** is barebones — no animated charts, no live logs, no job progress
13. **DB schema** missing tables for: scrape_jobs, social_results, classified_results, export_queue, job_logs
14. **Two conflicting DB implementations** (Prisma + Knex) — need unified approach

---

## User Review Required

> [!IMPORTANT]
> **Database Unification**: The project has TWO database implementations (Prisma/Next.js on port 3000 + Knex/Express on port 4000) with **incompatible schemas in the same DB name**. This plan will **keep Prisma as the system of record** for the Next.js app and **keep the Express backend as a separate scraping microservice** that writes results back via REST calls (they share the DB but Express only writes to its own tables: `users`, `api_keys`, `usage_logs`, `search_queries`, `maps_results`). Prisma tables stay for auth/stats/dashboard.

> [!WARNING]
> **Real scraping of Facebook, LinkedIn, Twitter requires headless Chrome with stealth plugins**. These scrapers are implemented here using **Playwright + stealth**. They will not work without Node.js + the `playwright-extra` + `puppeteer-extra-plugin-stealth` packages installed. The `installer.bat` will be updated to handle this.

> [!CAUTION]
> **Terms of Service**: Scraping Facebook, LinkedIn, Twitter, and classified sites may violate their ToS. The code includes rate-limiting and random delays to minimize footprint. Users are responsible for compliance.

---

## Open Questions

> [!IMPORTANT]
> 1. **Google Maps Scraper**: Should it use the Google Maps JavaScript API (requires billing) or direct Playwright automation of maps.google.com? **Plan assumes Playwright automation** (no API key needed, matches existing skeleton).
> 2. **Export Storage**: Should exports be saved to the local filesystem (current `data/` pattern) or stored in DB as BLOBs? **Plan assumes local filesystem** in `exports/` folder organized by `keyword/date/`.
> 3. **Anti-captcha Service**: For LinkedIn/Facebook, should we integrate 2captcha/Anti-Captcha API, or only use stealth + delays? **Plan uses stealth + delays only** (no paid captcha service) to keep it self-contained.
> 4. **Multilingual (RTL)**: Full Arabic translation needs translation strings. **Plan will create the i18n framework with English + Arabic placeholders** and a language switcher — actual Arabic copy can be filled in.

---

## Proposed Changes

### Component 1: Database Schema Additions (Prisma + New Knex Migrations)

#### [MODIFY] [schema.prisma](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/prisma/schema.prisma)
- Add `ScrapeJob` model: id, userId, module, keywords[], status (QUEUED/RUNNING/DONE/FAILED), progress, createdAt, completedAt
- Add `SocialResult` model: id, jobId, source (FACEBOOK/LINKEDIN/TWITTER), keyword, name, phone, email, address, title, description, profileUrl, scrapedAt
- Add `ClassifiedResult` model: id, jobId, source (HARAJ/OPENSOOQ/DUBIZZLE/etc), keyword, postTitle, postLink, phone, email, scrapedAt
- Add `ExportRecord` model: id, jobId, userId, format (XLSX/CSV/HTML/TXT), filePath, createdAt
- Add `ScraperLog` model: id, jobId, level (INFO/WARN/ERROR), message, createdAt
- Expand `ScrapeSource` enum: add FACEBOOK, LINKEDIN, TWITTER, HARAJ, OPENSOOQ, DUBIZZLE, MSTAML, CLASSIFIED, GOOGLE_MAPS

#### [NEW] New Knex migrations
- `20260714_create_scrape_jobs.js`
- `20260714_create_social_results.js`
- `20260714_create_classified_results.js`
- `20260714_create_export_records.js`
- `20260714_create_scraper_logs.js`

---

### Component 2: Backend — New Scraping Microservice Routes

#### [MODIFY] [app.js](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/backend/src/app.js)
- Register new routers: `/v1/social`, `/v1/classified`, `/v1/jobs`, `/v1/export`
- Add SSE (Server-Sent Events) endpoint for live log streaming

#### [NEW] `backend/src/routes/jobs.routes.js`
- `GET /v1/jobs` — list all jobs for authenticated user
- `POST /v1/jobs` — create new scrape job (module, keywords, config)
- `GET /v1/jobs/:id` — get job status + progress
- `DELETE /v1/jobs/:id` — cancel/stop a running job
- `GET /v1/jobs/:id/logs` (SSE) — real-time log stream

#### [NEW] `backend/src/routes/social.routes.js`
- `POST /v1/social/facebook` — start Facebook keyword scrape job
- `POST /v1/social/linkedin` — start LinkedIn email finder job
- `POST /v1/social/twitter` — start Twitter/X keyword scrape job
- `GET /v1/social/results/:jobId` — paginated results

#### [NEW] `backend/src/routes/classified.routes.js`
- `POST /v1/classified/haraj` — Haraj SA scraper
- `POST /v1/classified/opensooq` — OpenSooq scraper
- `POST /v1/classified/generic` — generic classified scraper (configurable URL pattern)
- `GET /v1/classified/results/:jobId`

#### [NEW] `backend/src/routes/export.routes.js`
- `POST /v1/export/:jobId` — export results (body: `{ format: "xlsx"|"csv"|"html"|"txt" }`)
- `GET /v1/export/:exportId/download` — stream the file back

#### [MODIFY] [maps.routes.js](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/backend/src/routes/maps.routes.js)
- Replace the TODO stub with a real Playwright-driven Google Maps scraper worker call

---

### Component 3: Backend — Scraping Engine Services

#### [NEW] `backend/src/services/jobManager.js`
- `createJob(userId, module, keywords, config)` — inserts job row, enqueues worker
- `getJobStatus(jobId)` — returns status, progress %, extracted count
- `cancelJob(jobId)` — sets status=CANCELLED, signals worker to stop
- Worker pool: up to 3 concurrent Playwright browser contexts
- Uses Node.js `worker_threads` or `child_process` for isolation

#### [NEW] `backend/src/services/browserEngine.js`
- `createStealthBrowser(proxy?)` — launches Playwright Chromium with:
  - `playwright-extra` + `puppeteer-extra-plugin-stealth`
  - Random viewport, user-agent rotation
  - Random delays (1.5s–4.5s between actions)
  - Optional proxy injection
- `createBrowserContext(browser, options)` — isolated context per job

#### [NEW] `backend/src/scrapers/facebookScraper.js`
- Strategy: Multi-engine approach (Google/Bing/DuckDuckGo dorking: `site:facebook.com "keyword"`)
- For each SERP link: fetch the Facebook profile/page (stealth Playwright)
- Extract: phone numbers (regex on page text), emails, address from About section
- Handle infinite scroll for posts
- Output: `{ name, profileUrl, phone, email, address, title, description }`

#### [NEW] `backend/src/scrapers/linkedinScraper.js`
- Strategy: Search engine dorking (`site:linkedin.com/in "keyword" email`)
- Cross-search Google + Bing + Yahoo (all country domains)
- Extract email patterns from SERP snippets (no LinkedIn login required)
- Pattern guessing: `firstname.lastname@company.com`
- Output: `{ name, linkedinUrl, email, title, company }`

#### [NEW] `backend/src/scrapers/googleMapsScraper.js`
- **Full implementation** replacing the existing TODO stub
- Playwright automation of `maps.google.com`
- Search query → scroll results panel → click each listing → extract side-panel data
- Extract: Business Name, Phone, Address, Website, Rating, Reviews
- Deep-visit each website link to extract: Email, Instagram, Facebook, LinkedIn, Twitter
- Output: Full record with social profiles

#### [NEW] `backend/src/scrapers/websiteCrawler.js`
- Recursive crawler (BFS up to depth 3, max 100 pages per domain)
- Respects `robots.txt` (configurable)
- Uses `axios` + `cheerio` for static pages, Playwright for JS-heavy pages
- Email extraction using `lib/scrapers/extract.ts` patterns
- Output: All emails, phones, social links found across the domain

#### [NEW] `backend/src/scrapers/harajScraper.js`
- Targets: haraj.com.sa, opensooq.com, mstaml.com, bey3.com, aqari.sa, expatriates.com, almuraba.net, alwaseet.com.sa, sa.dubizzle.com, eg.olx.com.eg, olx.com.kw, tayara.tn, mubawab.sa, mubawab.ma, 4sale.com.kw, qatarliving.com, mourjan.com, mzad.com, propertyfinder.sa, bayut.sa, motory.com, syarah.com, maroof.sa
- Keyword search → parse listing cards → extract post title, link, phone, email
- Handles Arabic RTL content correctly (Unicode phone patterns)
- Phone extraction: Saudi (+966), Egypt (+20), Kuwait (+965), Qatar (+974) formats

#### [NEW] `backend/src/scrapers/twitterScraper.js`
- Twitter/X keyword search via web scraping (no API required)
- Extract tweet links, phone numbers, email patterns from tweet text and comments
- Handle pagination / infinite scroll
- Output: `{ tweetUrl, text, phone, email, author, date }`

#### [NEW] `backend/src/services/exportService.js`
- `exportToXLSX(jobId, results)` — using `exceljs`: separate sheets per keyword
- `exportToCSV(jobId, results)` — using `csv-writer`
- `exportToHTML(jobId, results)` — styled HTML report with table
- `exportToTXT(jobId, results)` — plain text, one record per line
- Saves to `exports/{jobId}/{keyword}/results.{ext}`
- Returns file path for download

#### [NEW] `backend/src/services/antiRobotService.js`
- User-agent pool (50+ real browser UAs rotated randomly)
- Random mouse movement simulation helpers
- Human-like typing simulation (random delays between keystrokes)
- `randomDelay(min, max)` — sleep utility
- Proxy rotation helper (reads from `proxies.txt` if present)

---

### Component 4: Next.js API Routes (Frontend-Facing)

#### [NEW] `app/api/jobs/route.ts` — proxy to Express backend job manager
#### [NEW] `app/api/social/facebook/route.ts` — Facebook scraper endpoint
#### [NEW] `app/api/social/linkedin/route.ts` — LinkedIn email finder endpoint
#### [NEW] `app/api/social/twitter/route.ts` — Twitter scraper endpoint
#### [NEW] `app/api/classified/route.ts` — Classified sites scraper endpoint
#### [NEW] `app/api/scrape/google-maps/route.ts` — Google Maps scraper (replaces stub)
#### [NEW] `app/api/export/route.ts` — Export generation endpoint

---

### Component 5: Frontend — Enhanced Dashboard & New UI Modules

#### [MODIFY] [DashboardShell.tsx](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/components/DashboardShell.tsx)
- Add full RTL support via `dir` attribute toggle
- Add language switcher (EN ↔ AR) in topbar
- Add active job indicator (animated pulse badge in sidebar)
- Expand sidebar nav with all new modules

#### [MODIFY] [DashboardShell.module.css](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/components/DashboardShell.module.css)
- Add RTL sidebar flip styles
- Add animated gradient accents
- Add job-status indicator styles

#### [MODIFY] `app/dashboard/page.tsx`
- Replace static tiles with animated CountUp numbers
- Add live jobs table (running, queued, completed)
- Add module usage breakdown with animated bar chart (using ApexCharts/ECharts already installed)
- Add recent extractions feed with source icons

#### [MODIFY] [nav-data.ts](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/lib/nav-data.ts)
- Add: Social Media Scraper, Classified & Haraj, Export Manager, Job Queue, Admin (conditional on role)

#### [MODIFY] [tools-data.ts](file:///c:/laragon/www/ai-director-sales/Scrape%20Genius%20v9.0/web_crawler_spider/lib/tools-data.ts)
- Add **Social Media Scraper** category:
  - Facebook Phones & Emails Extractor (multi-keyword)
  - LinkedIn Email Finder (Google/Bing/Yahoo dorking)
  - Twitter/X Comment & Profile Scraper
- Add **Classified & Saudi Market** category:
  - Haraj SA Scraper
  - OpenSooq Scraper
  - Dubizzle / OLX Scraper
  - Generic Classified Scraper (20+ sites)
- Add **AnyWebsite Deep Crawler** (upgrade of existing website scraper)

#### [NEW] `app/dashboard/tools/social/facebook/page.tsx`
- Multi-keyword input (one per line or CSV import)
- Search engine selector (Google dorking / Bing / DuckDuckGo)
- Country/region filter
- Run button → shows live progress with extracted count
- Results table: Name | Phone | Email | Address | Title | Profile Link
- Export button (XLSX / HTML / CSV)

#### [NEW] `app/dashboard/tools/social/linkedin/page.tsx`
- Keyword input + country selector (all country TLDs)
- Search engine multi-select (Google, Bing, Yahoo)
- Results table: Name | Email | Title | Company | LinkedIn URL
- Export (XLSX / CSV)

#### [NEW] `app/dashboard/tools/social/twitter/page.tsx`
- Keyword + hashtag input
- Date range filter
- Results: Tweet link | Text | Phone | Email | Author

#### [NEW] `app/dashboard/tools/classified/page.tsx`
- Site selector (checkboxes for all 20+ Saudi/MENA classified sites)
- Multi-keyword input
- Results: Post Title | Link | Phone | Email | Source Site
- Export XLSX / HTML

#### [NEW] `app/dashboard/tools/google-maps/page.tsx`
- Query + location input
- Results with all fields including social profiles
- Map preview integration

#### [NEW] `app/dashboard/jobs/page.tsx`
- Job queue table: Module | Keywords | Status | Progress | Started | Actions
- Real-time progress via SSE
- Cancel, retry, export actions per job

#### [NEW] `app/dashboard/export/page.tsx`
- Export history table
- Re-download exports
- Format conversion

#### [NEW] `lib/i18n.ts` — i18n system
- `useTranslation()` hook
- Language context (EN/AR)
- Translation map for all UI strings

#### [NEW] `lib/i18n-strings.ts`
- English + Arabic translation strings for all UI labels

#### [NEW] `components/LiveJobLog.tsx`
- SSE-connected real-time log viewer component
- Colored by level (INFO=blue, WARN=yellow, ERROR=red)
- Auto-scroll with pause-on-hover

#### [NEW] `components/MultiKeywordInput.tsx`
- Textarea + CSV file import
- Tag-style display of entered keywords
- Bulk delete

#### [NEW] `components/ExportWizard.tsx`
- Modal with format selection (XLSX/CSV/HTML/TXT)
- Column selection (which fields to include)
- One-click export + download

#### [NEW] `components/ResultsGrid.tsx`
- Enhanced results table using `@mui/x-data-grid` (already installed)
- Sorting, filtering, column visibility toggle
- Row selection for bulk export
- Deduplication toggle

---

### Component 6: Admin Panel

#### [NEW] `app/dashboard/admin/page.tsx`
- User management: list, activate/deactivate, role change
- API key pool management: add/edit/delete Google keys
- Usage analytics: daily/weekly/monthly charts
- Purchase code management: generate, list, revoke
- System health: backend status, DB connection, scraper queue depth

---

### Component 7: Enhanced ENV, Config & Installer

#### [MODIFY] `.env.example` — add all new variables
```
# Playwright / Browser Engine
PLAYWRIGHT_HEADLESS=true
PLAYWRIGHT_TIMEOUT=30000
PLAYWRIGHT_CONCURRENCY=3
PROXY_LIST_FILE=proxies.txt

# Export
EXPORT_DIR=./exports
MAX_EXPORT_AGE_DAYS=30

# Classified Sites
HARAJ_BASE_URL=https://haraj.com.sa
OPENSOOQ_BASE_URL=https://sa.opensooq.com

# Anti-Robot
RANDOM_DELAY_MIN_MS=1500
RANDOM_DELAY_MAX_MS=4500

# i18n
DEFAULT_LANGUAGE=en
```

#### [MODIFY] `installer.bat`
- Install `playwright-extra`, `puppeteer-extra-plugin-stealth`, `exceljs`, `node-fetch`, `p-limit`
- Run `npx playwright install chromium`
- Run all Knex migrations

#### [MODIFY] `starter.bat`
- Start both Next.js (port 3000) and Express backend (port 4000) concurrently

---

### Component 8: Enhanced Features Documentation

#### [NEW] `FEATURESLIST_ENHANCED.md`
- Complete feature matrix for all modules
- Architecture diagram (text-based)
- API reference for all endpoints
- Export format specifications

---

## Verification Plan

### Automated Tests
- Lint: `npm run lint` in root
- Type check: `npx tsc --noEmit` in root
- Backend unit test: `node backend/src/scripts/test-scrapers.js` (smoke test each scraper)

### Manual Verification
- Start both servers with updated `starter.bat`
- Login → Dashboard → verify stat tiles load
- Create a Facebook scrape job with keyword "real estate agent Dubai" → verify job shows in queue
- Create a Haraj scrape job with keyword "سيارة" → verify Arabic results parse correctly
- Run Google Maps with keyword "restaurant Riyadh" → verify results with social links
- Export a result set to XLSX → verify file downloads and opens correctly
- Switch language to Arabic → verify RTL layout flips correctly
- Admin panel: add a new API key, verify it appears in key pool

---

## Files To Create (Total Count: ~45 new files, ~15 modified files)

| Layer | New | Modified |
|-------|-----|----------|
| DB Schema/Migrations | 5 | 2 |
| Backend Routes | 4 | 3 |
| Backend Services/Scrapers | 8 | 1 |
| Next.js API Routes | 7 | 0 |
| Frontend Pages | 7 | 3 |
| Frontend Components | 5 | 2 |
| Lib / Config | 3 | 3 |
| Docs / ENV / Scripts | 3 | 3 |
| **Total** | **42** | **17** |
