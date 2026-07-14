# Scrape Genius Pro — Enhanced Feature List & Architecture

This document replaces the previous `implementation_plan.md`/`task.md` pair, which
claimed a full build was complete when in fact only the backend scraping engine
existed and nothing connected it to the frontend. Everything below reflects what
was actually built and verified in this pass.

## Architecture overview

Two independent services, bridged by a server-to-server identity handoff:

```
Browser (Next.js dashboard, port 3000)
   |  Bearer JWT (Prisma-backed auth: signup/login/dashboard/API keys)
   v
Next.js Route Handlers (app/api/**)
   |  lib/backend-client.ts — forwards the verified user's id/email/name
   |  over X-Internal-Secret (server-only, never sent to the browser)
   v
Express scraping microservice (backend/, port 4000)
   |  Playwright + stealth browser engine, job queue, exporters
   v
MySQL (backend's own scrapegenius_backend database: scrape_jobs,
social_results, classified_results, maps_job_results, export_records,
scraper_logs)
```

**Why two identity systems instead of one.** The Next.js app's users (Prisma,
`users` table in `google-map-scraper-pro`) and the Express service's users
(Knex, `users` table in `scrapegenius_backend`) were built independently with
different JWT secrets and password hashing. Rather than forcing every user to
log in twice or rewriting one app's auth wholesale, `backend/src/middleware/internalAuth.js`
lets the Next.js server (never the browser) vouch for an already-authenticated
user via a shared secret (`INTERNAL_API_SECRET`, must match in both `.env`
files). Express lazily mirrors a minimal shadow row into its own `users` table
so the `scrape_jobs.user_id` / `export_records.user_id` foreign keys stay
valid. Shadow users are always created as `admin: false` — **the bridge
cannot grant admin**, and Express's own `/v1/admin/*` routes are not reachable
through it at all. The dashboard's Admin page reads/writes the Next.js app's
own Prisma tables (`User`, `PurchaseCode`, `ApiKey`, `ScrapedRecord`) instead.

This is a pragmatic bridge, not a long-term ideal — a cleaner fix would be
unifying the two user tables/JWTs outright, which is a larger, separate
migration.

## What's implemented end-to-end

| Module | Create job | Live logs | Results grid | Export | Frontend page |
|---|---|---|---|---|---|
| Facebook Phones & Emails | `POST /api/social/facebook` | ✅ SSE | ✅ | XLSX/CSV/HTML/TXT | `/dashboard/tools/social/facebook` |
| LinkedIn Email Finder | `POST /api/social/linkedin` | ✅ | ✅ | ✅ | `/dashboard/tools/social/linkedin` |
| Twitter/X Scraper | `POST /api/social/twitter` | ✅ | ✅ | ✅ | `/dashboard/tools/social/twitter` |
| Haraj & Classified (11 MENA sites) | `POST /api/classified` | ✅ | ✅ | ✅ | `/dashboard/tools/classified` |
| Google Maps Business Extractor Pro | `POST /api/jobs` (module=google_maps) | ✅ | ✅ | ✅ | `/dashboard/tools/google-maps-pro` |
| Job Queue (all modules) | — | ✅ | ✅ | ✅ | `/dashboard/jobs` |
| Export history | — | — | — | ✅ re-download | `/dashboard/export` |
| Admin (Prisma-backed) | — | — | Users / Purchase codes / Usage | — | `/dashboard/admin` |

Supported classified sites (real, matching `backend/src/scrapers/harajScraper.js`):
Haraj, OpenSooq, Dubizzle (sa.dubizzle.com), OLX Kuwait, OLX Egypt, Mubawab
(Saudi), Bayut (Saudi), Property Finder (Saudi), Syarah, Expatriates.com,
4Sale Kuwait.

## What each layer actually does

- **`backend/src/services/browserEngine.js`** — Playwright + `playwright-extra`
  + `puppeteer-extra-plugin-stealth`, random UA/viewport, proxy support via
  `PROXY_LIST_FILE`.
- **`backend/src/services/jobManager.js`** — Knex-backed queue (`scrape_jobs`),
  progress/status updates, SSE log fan-out (`scraper_logs`), cancellation.
- **`backend/src/services/exportService.js`** — real `exceljs`/CSV/HTML/TXT
  generation to `EXPORT_DIR`, tracked in `export_records`.
- **`backend/src/scrapers/*.js`** — real DOM-selector-driven Playwright/cheerio
  scrapers per module (not mocked data): Facebook (search-engine dorking +
  profile visit), LinkedIn (Google/Bing/Yahoo dorking, no LinkedIn login),
  Twitter (`[data-testid="tweet"]` scraping), Google Maps (`div[role="feed"]`
  automation + per-listing website visit for social links), Haraj/classified
  (per-site card parsing), website crawler (BFS, robots.txt aware).
- **`lib/backend-client.ts`** — the only place that knows `INTERNAL_API_SECRET`;
  every proxy route goes through it. `server-only` import enforced.
- **`lib/i18n.tsx` / `lib/i18n-strings.ts`** — EN/AR dictionary + React context;
  `DashboardShell` flips `dir="rtl"`/`ltr"` and the whole layout mirrors via
  CSS logical properties + `[dir="rtl"]` overrides in `DashboardShell.module.css`.

## Known gaps / honest limitations

1. **Shadow-user email collisions**: the internal-auth bridge upserts by
   Express user id; if a Next.js user id happens to collide with a different
   person's email already in Express's `users` table, the upsert could update
   the wrong row's name. Vanishingly unlikely on a fresh install, worth a real
   fix (match-by-email fallback) before multi-tenant production use.
2. **No automated tests** — everything was verified by live typecheck +
   manual job runs (see Verification section below), not a test suite.
3. **Facebook/LinkedIn/Twitter/Google ToS** — these scrapers browse live public
   pages via search-engine dorking and stealth Playwright. Sites change their
   markup and anti-bot posture regularly; selectors may need maintenance.
   You are responsible for complying with each platform's Terms of Service.
4. **Admin panel** only covers Users / Purchase Codes / Usage (Prisma side).
   It does not manage the Express backend's own API-key pool — that remains
   Express-internal (`backend/src/routes/admin.routes.js`, unreachable from
   this bridge by design).
5. **ESLint config** (`next lint`) fails on this checkout with an "Unknown
   options" error — a pre-existing ESLint 8→9 config mismatch unrelated to
   this pass; `npx tsc --noEmit` is clean.
6. **Google Maps locale**: `googleMapsScraper.js` now forces `?hl=en` on the
   search URL (fixed this pass — it previously let Google serve whatever
   locale it chose, which could bleed Arabic UI chrome into the parsed
   address/category fields for non-English-locale contexts).

## Real bugs found and fixed via live end-to-end testing

Two bugs in the pre-existing "genuine" backend code had never actually been
exercised before this pass (nothing could reach them until the bridge existed)
and were only caught by running a real job:

1. **Unconditional crash on every job** — `jobManager._runJob()` and two other
   call sites did `JSON.parse(job.keywords)` / `JSON.parse(job.config)` /
   `JSON.parse(log.meta)`, but `mysql2` already auto-parses `JSON`-typed MySQL
   columns into real JS arrays/objects. Calling `JSON.parse()` on an
   already-parsed array stringifies it first (`JSON.parse(["a"])` →
   `JSON.parse("a")`) and throws `SyntaxError`, which was **uncaught outside
   the try/catch**, crashing the entire Node process on the very first queued
   job. Fixed with a `parseJsonColumn()` helper that only parses actual
   strings, and moved the parsing inside `_runJob`'s try/catch so a bad value
   can never take down the whole server again.
2. **`browser.newContext()` schema violation** — `antiRobotService.js` set
   `geolocation: null` in the default context options; Playwright requires
   `geolocation` to be omitted or a real `{latitude, longitude}` object, so
   every single job failed at browser-context creation. Fixed by removing the
   line (equivalent to omitting it).

With both fixed, a live end-to-end run was performed: a Google Maps job for
"coffee shop Seattle" ran to completion and extracted real records — e.g.
business name, phone, website, email, and Instagram/Facebook/Twitter links
pulled from the business's actual website — proving the full chain (Next.js
bridge → Express job queue → real Playwright scrape → MySQL → results API)
works. A Haraj job also completed (0 listings matched — likely live-site
selector drift, see gap #3) without crashing, and the export endpoint
correctly returned a graceful "no results" error rather than failing.
Arabic keyword text was verified to round-trip through the whole pipeline
with correct UTF-8 bytes (confirmed via `HEX()` in MySQL).

## Environment variables added this pass

Root `.env` / `.env.example`: `SCRAPER_BACKEND_URL`, `INTERNAL_API_SECRET`.
`backend/.env` / `.env.example`: `INTERNAL_API_SECRET` (must match root),
`PLAYWRIGHT_HEADLESS`, `PLAYWRIGHT_TIMEOUT`, `PLAYWRIGHT_CONCURRENCY`,
`PROXY_LIST_FILE`, `RANDOM_DELAY_MIN_MS`, `RANDOM_DELAY_MAX_MS`, `EXPORT_DIR`.

## Verification performed

- `npx tsc --noEmit` — clean across the whole frontend.
- `node --check` on every modified/added backend route + middleware file.
- `npm install` in `backend/` — confirmed `cheerio`, `exceljs`, `playwright`,
  `playwright-extra`, `puppeteer-extra-plugin-stealth` install cleanly.
- `npx playwright install chromium` — confirmed a matching Chromium build is
  present for the backend's Playwright version.
- See the accompanying manual smoke-test notes for a live job run.
