# Scrape Genius Pro — Task List

> Rewritten after an audit found the previous version of this file marked
> everything `[x]` when in fact only the backend scraping engine (Phase 2/3
> below) was real — the Prisma schema, all Next.js API/page/component glue,
> and the docs/env updates were either missing or no-op edits. See
> `FEATURESLIST_ENHANCED.md` for the full architecture writeup and honest
> known-gaps list.

## Phase 1: Database
- [x] Backend Knex migrations for scrape_jobs/social_results/classified_results/maps_job_results/export_records/scraper_logs (pre-existing, verified real)
- [~] No new Prisma models added — deliberate decision to keep Express+Knex as sole system of record for job/scraping data, avoiding a schema collision across two DBs (see FEATURESLIST_ENHANCED.md)

## Phase 2: Backend Services (Core Engine) — pre-existing, verified real
- [x] backend/src/services/antiRobotService.js — fixed a live bug this pass: `geolocation: null` in the default context options fails Playwright's schema (expects omitted or a real object), which failed every single job at browser-context creation
- [x] backend/src/services/browserEngine.js
- [x] backend/src/services/jobManager.js — fixed a live bug this pass: `JSON.parse()` on `job.keywords`/`job.config` (already auto-parsed objects, courtesy of mysql2's JSON-column handling) threw uncaught, crashing the whole process on every job; now uses a defensive `parseJsonColumn()` helper inside the try/catch
- [x] backend/src/services/exportService.js
- [x] backend/src/scrapers/facebookScraper.js
- [x] backend/src/scrapers/linkedinScraper.js
- [x] backend/src/scrapers/googleMapsScraper.js — fixed this pass: search URL now forces `?hl=en` (was leaking Arabic Maps UI chrome into parsed address/category fields)
- [x] backend/src/scrapers/websiteCrawler.js
- [x] backend/src/scrapers/harajScraper.js
- [x] backend/src/scrapers/twitterScraper.js
- [x] backend/package.json — added missing cheerio/exceljs/playwright/playwright-extra/puppeteer-extra-plugin-stealth deps + `npm install` + `npx playwright install chromium` (previously undeclared — every scraper would have thrown `Cannot find module`)

## Phase 3: Backend Routes — pre-existing, verified real + newly wired
- [x] backend/src/routes/jobs.routes.js (+ new `GET /:id/results` unified endpoint; also fixed the same redundant-`JSON.parse` bug on `keywords`/log `meta`)
- [x] backend/src/routes/social.routes.js
- [x] backend/src/routes/classified.routes.js
- [x] backend/src/routes/export.routes.js
- [x] backend/src/app.js registers all four
- [x] backend/src/middleware/internalAuth.js (NEW) — server-to-server bridge so Next.js users can call this service at all
- [x] jobs/social/classified/export/maps routes updated to accept the bridge or a native Express Bearer token

## Phase 4: Next.js API Routes — all newly built this pass
- [x] app/api/jobs/route.ts (GET list, POST create)
- [x] app/api/jobs/[id]/route.ts (GET status, DELETE cancel)
- [x] app/api/jobs/[id]/results/route.ts
- [x] app/api/jobs/[id]/logs/route.ts (SSE passthrough)
- [x] app/api/social/facebook/route.ts
- [x] app/api/social/linkedin/route.ts
- [x] app/api/social/twitter/route.ts
- [x] app/api/classified/route.ts
- [x] app/api/export/route.ts (GET history, POST create)
- [x] app/api/export/[id]/download/route.ts
- [x] app/api/admin/users/route.ts, app/api/admin/users/[id]/route.ts (NEW, Prisma-backed, not in original plan)
- [x] app/api/admin/purchase-codes/route.ts (NEW)
- [x] app/api/admin/usage/route.ts (NEW)
- [~] app/api/scrape/google-maps/route.ts — left as-is (pre-existing, independent simple Playwright Maps scraper); the new job-queue-based Maps Pro tool is separate (`/dashboard/tools/google-maps-pro`) rather than replacing it, so the existing working tool isn't disturbed

## Phase 5: Frontend — Lib & Config — all newly built this pass
- [x] lib/i18n.tsx (React context/provider — `.tsx` not `.ts`, needs JSX)
- [x] lib/i18n-strings.ts (EN + real Arabic translations, not placeholders)
- [x] lib/nav-data.ts — added Classified/Jobs/Export/Admin nav items, `adminOnly` flag, `labelKey` for i18n
- [x] lib/tools-data.ts — added social-media + classified categories, google-maps-pro tool
- [x] lib/backend-client.ts (NEW) — server-only proxy helper, never reaches the browser
- [x] lib/download-file.ts (NEW) — authenticated blob download helper (plain `<a href>`/`window.open` can't carry a Bearer token)

## Phase 6: Frontend — Components — all newly built this pass
- [x] components/DashboardShell.tsx — RTL (`dir` attribute), language switcher, live running-jobs badge (polls `/api/jobs?status=RUNNING`), admin-only nav filtering
- [x] components/DashboardShell.module.css — `[dir="rtl"]` overrides, job-badge pulse animation
- [x] components/LiveJobLog.tsx — authenticated SSE reader (manual fetch+stream parse, not EventSource, since EventSource can't send Authorization headers)
- [x] components/MultiKeywordInput.tsx — textarea + CSV/TXT import + removable tag chips
- [x] components/ExportWizard.tsx — format picker, authenticated blob download
- [x] components/ResultsGrid.tsx — MUI DataGrid with built-in filter/column-visibility/density toolbar + client-side dedupe toggle
- [x] components/JobRunnerPanel.tsx (NEW, not in original plan) — shared run/progress/logs/results/export panel reused by all 5 module pages instead of duplicating the same async job-polling logic 5 times

## Phase 7: Frontend — Pages — all newly built this pass
- [x] app/dashboard/tools/social/facebook/page.tsx
- [x] app/dashboard/tools/social/linkedin/page.tsx
- [x] app/dashboard/tools/social/twitter/page.tsx
- [x] app/dashboard/tools/classified/page.tsx (site-selector checkboxes for all 11 real supported sites)
- [x] app/dashboard/tools/google-maps-pro/page.tsx
- [x] app/dashboard/jobs/page.tsx (queue table, cancel, inline results+logs viewer)
- [x] app/dashboard/export/page.tsx (history + re-download)
- [x] app/dashboard/admin/page.tsx (Users/Purchase Codes/Usage tabs, client + server role-gated)
- [~] app/dashboard/page.tsx — left as-is; the existing stats dashboard wasn't broken, so it wasn't rewritten

## Phase 8: Config, ENV, Docs
- [x] .env.example (root) — SCRAPER_BACKEND_URL, INTERNAL_API_SECRET
- [x] backend/.env.example — INTERNAL_API_SECRET, PLAYWRIGHT_*, PROXY_LIST_FILE, RANDOM_DELAY_*, EXPORT_DIR
- [x] .env / backend/.env (real local dev values, matching secret)
- [x] installer.bat — backend Playwright Chromium install step added
- [x] starter.bat — left as-is (already correctly starts both services concurrently)
- [x] backend/package.json — real deps added (see Phase 2)
- [x] FEATURESLIST_ENHANCED.md — rewritten to be accurate, includes known gaps

## Phase 9: Live Verification (this is the part that was skipped last time)
- [x] `npx tsc --noEmit` clean across the whole frontend
- [x] `node --check` on every modified/added backend file
- [x] Both dev servers started and left running (frontend :3000, backend :4000)
- [x] Live smoke test: created a real job through the internal auth bridge, watched it queue → run → complete without crashing
- [x] Live smoke test: Google Maps job for "coffee shop Seattle" extracted real records (business name, phone, website, email, Instagram/Facebook/Twitter) — full chain proven working
- [x] Live smoke test: Haraj job completed cleanly (0 matches — see known gap #6); export endpoint returned a graceful "no results" error instead of crashing
- [x] Arabic keyword text verified to round-trip correctly (UTF-8 bytes checked via `HEX()` in MySQL)
- [x] All 9 new/changed dashboard pages compiled and returned 200 in the live Next.js dev server, no RSC/build errors
- [x] Test data (fake user id 999001, jobs 1-7) cleaned out of the local dev database after verification
