# ScrapeGenius API Reference (Complete)

Full, accurate reference of every API route in this codebase — both the Next.js route handlers under `app/api/**/route.ts` and the Express backend (`backend/`) endpoints under `/v1/*` they proxy to. This document supersedes any previous partial version; every route in the repo is listed below with method, auth requirement, request shape, and response shape.

Auth legend:
- **Session (JWT cookie/header)** — `requireAuth()` helper, standard logged-in user.
- **Public API key** — third-party callers authenticate with `x-api-key` header or `?api_key=`.
- **Admin** — session auth + `role === "ADMIN"`.
- **Internal** — service-to-service call validated by an internal secret/API key, not end-user auth.
- **None** — no authentication.

---

## 1. Auth

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| POST | `/api/auth/login` | None | `{email, password}` | `{token, user:{id,name,email,role,isVerified}}`; 401 on bad credentials |
| POST | `/api/auth/signup` | None | `{name, email, password}` | Created user (no password), 201; 409 if email exists |

## 2. Purchase Codes / Licensing

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| POST | `/api/purchase-code/activate` | Session | `{code}` | Links code to current user; 404 invalid, 410 expired, 409 already claimed |
| GET | `/api/admin/purchase-codes` | Admin | — | List of up to 500 codes with owning user |
| POST | `/api/admin/purchase-codes` | Admin | `{expiresAt?}` | New random hex purchase code |

## 3. Saved Records & Dashboard Stats

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| POST | `/api/saved` | Session | `{source, query, data, stat_type}` | `{scraped_record_id, dashboard_stat}` — persists record + bumps stat atomically |
| GET | `/api/dashboard/stats` | Session | — | `[{title, records}]` per-user stat totals |

## 4. Google Custom Search Key Management

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| GET | `/api/keys` | Session | — | User's search keys (masked) + today's usage |
| POST | `/api/keys` | Session | `{googleApiKey, searchEngineId, dailyLimit}` | Created key |
| PATCH | `/api/keys/[id]` | Session (owner) | `{isActive?, dailyLimit?}` | Updated key |
| DELETE | `/api/keys/[id]` | Session (owner) | — | Deletes key |
| GET | `/api/get_keys` | Session | — | Legacy-shape list of active keys with `remaining_today`, filtered to keys with quota left |
| POST | `/api/update_usage` | Session | `{api_key_id, increment_by}` | Atomically increments daily usage; 429 if limit exceeded |

## 5. Public API Keys (for `/api/v1/scrape`)

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| POST | `/api/user/api-keys` | Session | `{name, rateLimit=1000, expiresAt?}` | Raw API key (shown once) |
| GET | `/api/user/api-keys` | Session | — | Active keys, masked |
| DELETE | `/api/user/api-keys?id=` | Session | — | Soft-deletes (deactivates) key |

## 6. Public Scrape API

| Method | Path | Auth | Body / Query | Response |
|---|---|---|---|---|
| POST | `/api/v1/scrape` | Public API key | `{module, keywords, config}` | Forwards to backend module (instagram/google_maps/facebook/linkedin/twitter), logs usage |
| GET | `/api/v1/scrape?jobId=&module=` | Public API key | — | Job status/results from backend, logs usage |

## 7. Job Queue (Express backend proxy)

| Method | Path | Auth | Query/Body | Response |
|---|---|---|---|---|
| GET | `/api/jobs?status=&limit=&offset=` | Session | — | Paginated job list scoped to user (proxies `/v1/jobs`) |
| POST | `/api/jobs` | Session | `{module, keywords, config}` | Creates job (proxies `/v1/jobs`) |
| GET | `/api/jobs/[id]` | Session | — | Job status/progress/result counts |
| DELETE | `/api/jobs/[id]` | Session | — | Cancels job |
| GET | `/api/jobs/[id]/logs` | Session | — | SSE stream (`text/event-stream`) of live job logs |
| GET | `/api/jobs/[id]/results?limit=&offset=&all=` | Session | — | Unified result rows for any module |

## 8. Export

| Method | Path | Auth | Query/Body | Response |
|---|---|---|---|---|
| GET | `/api/export?limit=&offset=` | Session | — | Export history |
| POST | `/api/export` | Session | `{jobId, format}` | Generates export (XLSX/CSV/HTML/TXT) |
| GET | `/api/export/[id]/download` | Session | — | Streams generated file (passthrough Content-Type/Content-Disposition) |

## 9. Admin

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| GET | `/api/admin/users` | Admin | — | Up to 500 users (id, name, email, role, isVerified, createdAt) |
| PATCH | `/api/admin/users/[id]` | Admin | `{role?, isVerified?}` | Updated user |
| GET | `/api/admin/usage` | Admin | — | `{totalUsers, totalRecords, totalActiveApiKeys, bySource}` |
| GET / POST | `/api/admin/purchase-codes` | Admin | see §2 | — |

## 10. Custom API Connectors

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| GET | `/api/api-connectors` | Session | — | User's connectors (API key masked) |
| POST | `/api/api-connectors` | Session | `{name, method, url, apiKey?, authType, authParam?, resultsPath?, fieldMap?}` | Created connector |
| GET | `/api/api-connectors/[id]` | Session (owner) | — | One connector (masked); 404 for others' |
| DELETE | `/api/api-connectors/[id]` | Session (owner) | — | Removes connector |
| POST | `/api/api-connectors/[id]/run` | Session (owner) | `{query}` | Executes saved connector call, maps fields, persists results (source `CUSTOM_API`) |

## 11. CRM Connections (JustDial / IndiaMART)

`[provider]` ∈ `justdial`, `indiamart`

| Method | Path | Auth | Body | Response |
|---|---|---|---|---|
| POST | `/api/crm/[provider]` | Session | `{loginId, secret}` | Upserts connection; secret AES-encrypted at rest |
| GET | `/api/crm/[provider]` | Session | — | Connection status (never returns decrypted secret) |
| DELETE | `/api/crm/[provider]` | Session | — | Removes connection |
| POST | `/api/crm/[provider]/sync` | Session | — | Best-effort Playwright login + scrape using saved creds; updates `lastSyncedAt`/`lastStatus`; never 500s on scrape failure |

## 12. Search Engine Scrapers (direct, synchronous)

| Method | Path | Auth | Body | Notes |
|---|---|---|---|---|
| POST | `/api/scrape/google-search` | Session | `{query, limit?}` | Uses user's own Google CSE key + quota; source `GOOGLE` |
| POST | `/api/scrape/bing-search` | Session | `{query, limit?}` | HTTP+Cheerio, decodes Bing redirect wrapper; source `BING` |
| POST | `/api/scrape/yahoo-search` | Session | `{query, limit?}` | HTTP+Cheerio, decodes Yahoo redirect; source `YAHOO` |
| POST | `/api/scrape/duckduckgo-search` | Session | `{query, limit?}` | HTTP+Cheerio HTML fallback UI; source `DUCKDUCKGO` |
| POST | `/api/scrape/google-maps` | Session | `{query, limit?≤40}` | Playwright headless scroll+scrape of Maps feed; 45s timeout; source `MAP` |
| GET / POST | `/api/search` | Session | `{q, engines[]=all, page?, limit?≤30, lang?, safeSearch?}` | Unified multi-engine search across google/bing/duckduckgo/yahoo; 502 if all engines fail |

## 13. Directory Scrapers

| Method | Path | Auth | Body | Source tag |
|---|---|---|---|---|
| POST | `/api/scrape/indiamart` | Session | `{urls[]≤10}` | `INDIAMART` |
| POST | `/api/scrape/justdial` | Session | `{urls[]≤10}` | `JUSTDIAL` |
| POST | `/api/scrape/sulekha` | Session | `{urls[]≤10}` | `SULEKHA` |
| POST | `/api/scrape/business-directory` | Session | `{urls[]≤10}` | `BUSINESS_DIRECTORY` (generic) |

## 14. Utility / Contact / File Scrapers

| Method | Path | Auth | Body | Notes |
|---|---|---|---|---|
| POST | `/api/scrape/whois` | Session | `{domain}` | Raw TCP WHOIS (IANA referral chase); source `WHOIS` |
| POST | `/api/scrape/website-data-center` | Session | `{keyword, country?, limit=10}` | Self-contained DDG search + page extraction; source `WEBSITE` |
| POST | `/api/scrape/website-data-scraper` | Session | `{urls[]≤20}` | Batch fetch + extraction, returns `results` + `failed[]`; source `WEBSITE` |
| POST | `/api/scrape/contact-scraper` | Session | `{type:"EMAIL"|"PHONE", url? \| text?}` | Extracts emails/phones; source = type |
| POST | `/api/scrape/document-data-scraper` | Session | multipart `file` (.txt/.csv/.docx, ≤20MB) | Text extraction + email/phone harvest; source `DOCUMENT` |
| POST | `/api/scrape/image-data-scraper` | Session | multipart `file` (image, ≤15MB) | Tesseract OCR + email/phone harvest; source `IMAGE` |

## 15. Social Media

| Method | Path | Auth | Body | Notes |
|---|---|---|---|---|
| POST | `/api/social/instagram` | Session (JWT) | `{keywords[], config?}` | Proxies to backend `/api/social/instagram` |
| GET | `/api/social/instagram?jobId=` | Session (JWT) | — | Proxies to backend `/api/social/instagram/:jobId` |
| POST | `/api/social/facebook` | Session | `{keywords, config}` | Creates backend scrape job `/v1/social/facebook` |
| POST | `/api/social/twitter` | Session | `{keywords, config}` | Creates backend scrape job `/v1/social/twitter` |
| POST | `/api/social/linkedin` | Internal API key | `{profileUrl, sessionCookieValue}` | Validates LinkedIn URL, forwards to scraper backend `/api/linkedin-profile`; returns full profile (experience, education, skills, contact info) |
| GET | `/api/social/linkedin` | — | — | 405 Method Not Allowed (POST only) |

## 16. Classified Ads

| Method | Path | Auth | Body | Notes |
|---|---|---|---|---|
| POST | `/api/classified` | Session | `{site:"haraj"|"generic", keywords, config}` | Routes to backend `/v1/classified/haraj` or `/v1/classified/generic`; 400 on unknown site |

## 17. Search Dorks

| Method | Path | Auth | Body/Query | Notes |
|---|---|---|---|---|
| POST | `/api/dorks` | Session | `{keyword, location, country, intent, platforms, language}` | Generates dork queries via backend `/v1/dorks/generate` |
| GET | `/api/dorks?action=history\|templates&limit=&offset=` | Session | — | Default `templates`; history via `/v1/dorks/history` |

## 18. AI Features

| Method | Path | Auth | Body | Notes |
|---|---|---|---|---|
| POST | `/api/ai-enrichment` | Session | `{email?, phone?, website?, socialLinks?, reviews?}` | Local heuristic lead scoring (0-100) + review sentiment analysis, no external AI call |
| POST | `/api/lead-qualifier` | Session | `{mode:"classify"\|"classify-job", text?, jobId?, product?, limit?}` | Routes to backend `/v1/lead-qualifier/classify[-job]` |

## 19. Professional Contact Finder

| Method | Path | Auth | Body | Notes |
|---|---|---|---|---|
| POST | `/api/professional-contacts` | None (zod-validated) | `{firstName?, lastName?, domain?, keyword?, company?}` (domain or company required) | Hunter.io domain-search (if `HUNTER_API_KEY` set) or demo fallback; returns `{success, count, results[]}` |
| GET | `/api/professional-contacts` | None | — | Usage/help info only, not a functional search |

---

## 20. Express Backend (`/v1/*`) — long-running job architecture

The Express backend (in `backend/`) runs Playwright-based scrapers as async jobs; the Next.js routes above proxy to it.

### Job Management
- `POST /v1/jobs` — create job
- `GET /v1/jobs?status=&limit=&offset=` — list jobs
- `GET /v1/jobs/:id` — job progress/status
- `DELETE /v1/jobs/:id` — cancel job
- `GET /v1/jobs/:id/logs` — SSE live logs
- `GET /v1/jobs/:id/results?limit=&offset=&all=` — unified results

### Social Media Scrapers
- **Facebook** (`facebookScraper.js`) — SERP dorking (`site:facebook.com "keyword" (phone OR email)`) + stealth Playwright deep-visit of discovered profiles/pages.
- **LinkedIn** (`linkedinScraper.js`) — SERP dorking across Google/Bing/Yahoo, reads email from SERP snippet, falls back to Name+domain guessing.
- **Twitter/X** (`twitterScraper.js`) — Playwright stealth load of `/search?q=...&f=live`, human-like infinite scroll, regex extraction from tweet bodies.

### Google Maps Scraper (`googleMapsScraper.js`)
- Playwright deep crawl of Maps search results panel with forced lazy-load scrolling; parses side-panel details per listing.
- Secondary pass deep-visits each business website via Axios to harvest email + social links.

### Classified Ads Scraper (`harajScraper.js`)
- Covers 20+ platforms (haraj.com.sa, opensooq.com, dubizzle.com, olx.com.eg, propertyfinder.sa, etc.), full RTL Arabic keyword support, optional deep-visit per listing for seller phone/price/location.

### Deep Website Crawler (`websiteCrawler.js`)
- BFS crawl from seed URLs; fast Axios fetch first, falls back to Playwright when the text-to-HTML ratio indicates a JS-heavy SPA; recursively follows internal links to a configured depth, harvesting emails/phones/social links.

### Lead Qualifier / Dorks (backend)
- `POST /v1/lead-qualifier/classify` / `/classify-job` — text or job-scoped classification.
- `POST /v1/dorks/generate`, `GET /v1/dorks/templates`, `GET /v1/dorks/history` — dork query generation and history (internal-secret + `X-User-ID` header auth).

### Export (backend)
- `GET /v1/export?limit=&offset=` — export history
- `POST /v1/export/:jobId` — generate export file (XLSX/CSV/HTML/TXT)
- `GET /v1/export/:id/download` — stream generated file

---

## 20a. Zero-Cost AI Scraper, Webhooks, Email Verification, Google News (added post-audit)

See [Implementation.md](Implementation.md) for the full build/verification log for these four features.

| Method | Path | Auth | Body / Query | Response |
|---|---|---|---|---|
| POST | `/api/scrape/ai-scraper` | Session | `{url}` | Fetches clean Markdown via `https://r.jina.ai` (no key needed), extracts `{emails[], phones[], companies[], title?, description?}` (Arabic + English); persists source `WEBSITE` |
| POST | `/api/webhooks/register` | Session | `{url, events: [JOB_STARTED\|JOB_COMPLETED\|JOB_FAILED\|EXPORT_READY\|SCRAPE_DATA_AVAILABLE], isActive?}` | Registers a webhook; 409 if URL already registered for this user. **Registration only — nothing yet dispatches events to it.** |
| GET | `/api/webhooks/register` | Session | — | Lists the user's webhooks |
| POST | `/api/verify/email` | None | `{email}` | Zod syntax check + free MX-record DNS lookup (Node `dns` module) + disposable/free-provider detection + typo suggestions; no auth, no paid API |
| GET | `/api/scrape/google-news` | Session | `?q=&hl=&gl=&ceid=&dateRestrict=&limit=&offset=` | Proxies to backend `/v1/scrape/google-news`; parses Google News RSS via `xml2js`; returns `{items:[{title,link,pubDate,description,source,guid}], total, query, language, geographicLocation, url}`; persists source `NEWS_RSS` |

Backend: `GET /v1/scrape/google-news` (mounted in `backend/src/app.js`, auth via `requireAuthOrInternal`).

## 21. Data Limits & Configuration

- Frontend `<input>` max constraints have been raised/removed where the underlying route accepts larger volumes.
- Zod validation limits are enforced per-route as documented above (not globally uniform — check each route's table entry for its actual cap, e.g. directory scrapers cap `urls[]` at 10, Google Maps caps `limit` at 40).
- Backend job queries support `limit=0` / `all=true` to stream complete result sets to exporters and UI tables.
- Do not assume a route is unlimited unless stated in its row above — several routes intentionally cap batch size (10/20/40) to protect scrape reliability and target-site rate limits.
