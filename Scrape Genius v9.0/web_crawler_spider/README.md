# Scrape Genius v9.0 - Web Crawler Spider

## Enterprise Web Scraping & Lead Generation Platform

Scrape Genius is a full-stack scraping and lead-generation platform combining a Next.js frontend/API layer with an Express + Playwright backend job engine. It covers search-engine scraping, social media scraping, business directories, classified ads, Google Maps, domain intelligence, file/image OCR extraction, CRM sync, custom API connectors, AI-assisted lead scoring, and a full job queue with SSE progress and multi-format export.

## 🚀 Key Features

### Search & Discovery
- **Search Engine Scrapers** — Google, Bing, Yahoo, DuckDuckGo (direct HTTP+Cheerio, per-engine)
- **Multi-Engine Unified Search** — one query fanned out across all engines with per-engine status and quota reporting
- **Google Custom Search key management** — bring your own Google CSE key(s), per-key daily quota tracking
- **Search Dorks Generator** — builds targeted dork queries (keyword/location/intent/platform/language) with history and templates

### Social Media Scraping
- **Instagram** — profile/keyword scraping via backend job queue
- **Facebook** — SERP dorking + stealth Playwright deep-visit of discovered pages
- **LinkedIn** — profile scraper (experience, education, skills, contact info) + SERP-dork email discovery
- **Twitter/X** — stealth Playwright search scrolling + tweet body extraction

### Business Data & Directories
- **B2B Directory Scrapers** — IndiaMart, JustDial, Sulekha, and generic business directories
- **Google Maps Business Extractor** — Playwright deep crawl of Maps listings + secondary website enrichment pass
- **Classified Ads Scraper** — Haraj plus 20+ MENA classifieds/marketplaces (OpenSooq, Dubizzle, OLX, PropertyFinder, etc.), full RTL Arabic support
- **CRM Connections** — JustDial/IndiaMART seller-dashboard login sync (AES-encrypted credentials, Playwright-driven sync)

### Web & File Data Extraction
- **Deep Website Crawler** — BFS crawl with automatic Axios→Playwright fallback for JS-heavy SPAs
- **Website Data Scraper / Data Center** — batch URL scraping and keyword-driven discovery + extraction
- **WHOIS Domain Lookup** — raw TCP WHOIS with IANA registrar-referral chasing
- **Document Scraper** — `.txt`/`.csv`/`.docx` upload with email/phone extraction (Papa Parse, mammoth)
- **Image Scraper (OCR)** — Tesseract.js OCR over uploaded images with email/phone extraction
- **Contact Scraper** — direct email/phone extraction from a URL or raw text

### AI & Enrichment
- **AI Enrichment** — heuristic lead scoring (0–100) and review sentiment/pain-point analysis
- **AI Lead Qualifier** — classifies text or entire job result sets against a target product
- **Professional Contact Finder** — Hunter.io domain search / company lookup for verified professional emails
- **Zero-Cost AI Scraper** — fetches clean Markdown for any URL via the free `r.jina.ai` reader (no API key) and extracts emails, phones, and company names in Arabic and English

### News & Verification
- **Google News RSS Scraper** — structured `{title, link, pubDate, source}` results for any keyword + language, no API key required
- **Email Verifier** — Zod syntax check + free MX-record DNS lookup, disposable/free-provider detection, typo-correction suggestions — no paid API

### Automation
- **Webhooks** — register a URL + event types (`JOB_STARTED`, `JOB_COMPLETED`, `JOB_FAILED`, `EXPORT_READY`, `SCRAPE_DATA_AVAILABLE`) to integrate with your own systems (registration is live; event dispatch is a planned next step — see [Implementation.md](Implementation.md))

### Extensibility
- **Custom API Connectors** — register any third-party HTTP API (query substitution, auth type, JSON path mapping) and run it like a built-in scraper
- **Public Scrape API** (`/api/v1/scrape`) — rate-limited, API-key authenticated endpoint for third-party integrations

### Job Queue, Export & Dashboard
- **Async Job Queue** — create/list/cancel jobs, live SSE progress logs, paginated unified results
- **Export Manager** — generate and download results as XLSX, CSV, HTML, or TXT
- **Dashboard Stats** — per-user running totals by source
- **Purchase Code Activation** — license/seat activation via redeemable codes

### Administration & Security
- **Admin Panel** — user management (role/verification), purchase code generation, platform usage analytics
- **JWT Authentication** — session tokens for the web app; separate long-lived API keys for the public API
- **Encrypted Secrets** — CRM credentials stored AES-encrypted, never returned in plaintext
- **Per-user Rate Limiting & Quotas** — both for Google CSE keys and public API keys

### Anti-Detection (backend scrapers)
- Rotating User-Agents on lightweight HTTP scrapers
- Stealth Playwright automation for JS-rendered targets (Facebook, Twitter, Google Maps, SPA crawler)
- Configurable timeouts and adaptive scroll/delay behavior

## 🛠️ Tech Stack

- **Frontend**: Next.js 13 (App Router), React 18, TypeScript 5, Tailwind CSS, Preline UI, FontAwesome
- **Frontend API layer**: Next.js route handlers (`app/api/**/route.ts`), Zod validation, Cheerio HTML parsing
- **Backend job engine**: Node.js, Express, Playwright/Puppeteer (in `backend/`)
- **Database**: MySQL, Prisma ORM
- **Auth/Security**: JWT (jsonwebtoken), bcrypt/bcryptjs, AES encryption for stored secrets
- **File/Data processing**: Tesseract.js (OCR), Mammoth (docx), PapaParse (CSV), libphonenumber-js

## 📋 Prerequisites

- Node.js 18+
- MySQL 8.0+
- Playwright browsers (`npx playwright install`)
- Optional external API keys: Google Custom Search (`googleApiKey`/`searchEngineId`), Hunter.io (`HUNTER_API_KEY`) for professional contact search

## 🚀 Quick Start

1. Clone the repository
```bash
git clone <repository-url>
cd web_crawler_spider
```

2. Install dependencies (also installs `backend/` deps via `postinstall`)
```bash
npm install
```

3. Install Playwright browsers
```bash
npx playwright install
```

4. Install enhanced anti-detection dependencies (optional, one-shot script)
```bash
npm run install-enhanced
```

5. Configure environment variables
```bash
cp .env.example .env
# Edit .env with your configuration
```

6. Set up the database
```bash
npx prisma migrate dev
```

7. Start the development servers
```bash
npm run dev          # Next.js frontend only
npm run dev:backend  # Express backend only
npm run dev:all      # both, concurrently
```

Or run the full one-shot bootstrap:
```bash
npm run setup
```

## 🔧 Configuration

### Environment Variables
```env
# Database
DATABASE_URL="mysql://user:password@localhost:3306/scrapedb"

# JWT Configuration
JWT_SECRET=your_jwt_secret
JWT_REFRESH_SECRET=your_refresh_secret

# Application Settings
NEXT_PUBLIC_APP_NAME="Scrape Genius"
NODE_ENV=development

# Backend Configuration
BACKEND_URL=http://localhost:3001
SCRAPER_BACKEND_URL=http://localhost:3001

# Internal service-to-service auth
INTERNAL_API_SECRET=your_internal_secret

# Optional third-party integrations
HUNTER_API_KEY=your_hunter_io_key

# Anti-Detection Settings
PLAYWRIGHT_TIMEOUT=30000
RANDOM_DELAY_MIN_MS=1500
RANDOM_DELAY_MAX_MS=4500
PROXY_LIST_FILE=proxies.txt
```

## 📖 API Documentation

See **[apiurl.md](apiurl.md)** for the complete, route-by-route API reference — every endpoint under `app/api/**`, its method, auth requirement, request/response shape, and the corresponding Express backend (`/v1/*`) endpoints it proxies to. Summary of the major groups:

| Area | Base path |
|---|---|
| Auth | `/api/auth/login`, `/api/auth/signup` |
| Purchase codes | `/api/purchase-code/activate`, `/api/admin/purchase-codes` |
| Saved records & stats | `/api/saved`, `/api/dashboard/stats` |
| Google CSE key management | `/api/keys`, `/api/get_keys`, `/api/update_usage` |
| Public API keys | `/api/user/api-keys` |
| Public scrape API | `/api/v1/scrape` |
| Job queue | `/api/jobs`, `/api/jobs/[id]`, `/api/jobs/[id]/logs`, `/api/jobs/[id]/results` |
| Export | `/api/export`, `/api/export/[id]/download` |
| Admin | `/api/admin/users`, `/api/admin/usage` |
| Custom API connectors | `/api/api-connectors` |
| CRM connections | `/api/crm/[provider]`, `/api/crm/[provider]/sync` |
| Search engine scrapers | `/api/scrape/google-search`, `bing-search`, `yahoo-search`, `duckduckgo-search`, `google-maps` |
| Unified search | `/api/search` |
| Directory scrapers | `/api/scrape/indiamart`, `justdial`, `sulekha`, `business-directory` |
| Utility/file scrapers | `/api/scrape/whois`, `website-data-center`, `website-data-scraper`, `contact-scraper`, `document-data-scraper`, `image-data-scraper` |
| Social media | `/api/social/instagram`, `facebook`, `twitter`, `linkedin` |
| Classified ads | `/api/classified` |
| Search dorks | `/api/dorks` |
| AI features | `/api/ai-enrichment`, `/api/lead-qualifier`, `/api/scrape/ai-scraper` |
| Professional contacts | `/api/professional-contacts` |
| Google News | `/api/scrape/google-news` |
| Email verification | `/api/verify/email` (no auth required) |
| Webhooks | `/api/webhooks/register` |

Protected routes require `Authorization: Bearer <token>` (session) unless documented otherwise; the public scrape API uses `x-api-key` instead.

## 🧪 Running Tests

```bash
# Run unit tests
npm test

# Run integration tests
npm run test:integration

# Run E2E tests
npm run test:e2e
```

## 🚀 Deployment

### Production Build
```bash
npm run build
npm start
```

### Docker Support
```bash
docker-compose up -d
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐛 Issues & Support

Please submit issues and feature requests through the GitHub issue tracker.

## 🆕 What's New in v9.0

- **Full feature audit**: every API route documented in [apiurl.md](apiurl.md), no hidden/undocumented endpoints
- **Expanded scraper coverage**: search engines, directories, classifieds, Google Maps, WHOIS, documents, images, contact scraper
- **AI-assisted lead workflow**: heuristic lead scoring, sentiment analysis, AI lead qualifier, professional contact finder
- **CRM integrations**: JustDial/IndiaMART login sync with encrypted credential storage
- **Custom API Connectors**: bring your own third-party API into the platform without code changes
- **Job Queue System**: asynchronous job processing with SSE progress and unified paginated results
- **Export Manager**: XLSX/CSV/HTML/TXT export with per-job history
- **Admin Panel**: user management, usage analytics, purchase-code licensing
- **Unified sidebar navigation**: every tool and settings page reachable from the dashboard sidebar

---

Built with ❤️ for ethical web scraping and data extraction.

For documentation on all enhanced features, see [ENHANCED_FEATURES.md](ENHANCED_FEATURES.md).
