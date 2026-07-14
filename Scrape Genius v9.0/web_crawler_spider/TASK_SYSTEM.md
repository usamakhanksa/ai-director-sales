# Scrape Genius — Enhancement Task System

Derived from the roadmap in `FEATURESLIST_ENHANCED.md` + the anti-detection/premium-feature/architecture
brainstorm supplied alongside it. This is the execution backlog: every item is scoped, ordered by
dependency, and tagged with the layer it touches so work can be picked up independently.

Companion doc: [API_BRIEF.md](API_BRIEF.md) — the API surface referenced by several tasks below (P2-4, P3-1).

Status legend: `[ ]` not started · `[~]` in progress · `[x]` done.

---

## Phase 0 — Prerequisites (do first, unblocks everything else)

- [ ] **P0-1** Add `PROXY_PROVIDER`, `CAPTCHA_SOLVER_PROVIDER`, `REDIS_URL` to `.env.example` (root + `backend/`).
- [ ] **P0-2** Stand up Redis locally/staging (required by P1-7, P3-2, P3-3).
- [ ] **P0-3** Pick and provision one proxy vendor (BrightData/Oxylabs/Smartproxy/IPRoyal) and one CAPTCHA
      vendor (2Captcha/CapSolver/Anti-Captcha) — needed before P1-1 and P1-6 can be built against a real API.

## Phase 1 — Core Scraping Engine & Anti-Detection

- [ ] **P1-1** Rotating residential proxy pool — replace `PROXY_LIST_FILE` static rotation in
      `backend/src/services/browserEngine.js` with a provider API client; sticky-session support keyed by
      `job_id` for multi-page crawls; automatic retry on 403/429 with proxy swap.
- [ ] **P1-2** Fingerprint randomization — integrate `fingerprint-injector` (or equivalent) into
      `browserEngine.js` context creation: WebGL/canvas/audio/font/timezone/hardware-concurrency spoofing
      per session, on top of existing `puppeteer-extra-plugin-stealth`.
- [ ] **P1-3** TLS fingerprint parity — evaluate `curl-impersonate` wrapper or patched Chromium build for
      the plain-HTTP (cheerio) fallback path so it isn't flagged by JA3/TLS fingerprinting.
- [ ] **P1-4** Human-like interaction — integrate `ghost-cursor` for mouse movement/scroll/typing jitter in
      any scraper that clicks/types (Facebook profile visit, LinkedIn dorking, Google Maps feed scroll).
- [ ] **P1-5** Network-level bandwidth hack — `page.route()` request interception to abort images/fonts/
      analytics domains in all Playwright scrapers; measure and document the speed/bandwidth win.
- [ ] **P1-6** CAPTCHA solving integration — new `backend/src/services/captchaSolver.js` adapter (pluggable
      by provider), auto-detect reCAPTCHA v2/v3 / hCaptcha / Cloudflare Turnstile, pause job → solve → inject
      token → resume via `jobManager`.
- [ ] **P1-7** Adaptive rate limiting — per-domain delay config + exponential backoff on 429/503, tracked in
      Redis (falls out of P3-2) so limits are shared across worker processes, not just in-process.
- [ ] **P1-8** Session persistence — save/load Playwright storage state (cookies/localStorage) per user per
      module in `EXPORT_DIR`-adjacent storage, so LinkedIn/Facebook scrapers can resume logged-in sessions.
- [ ] **P1-9** AI/LLM fallback parsing — when a scraper's primary selector set returns zero matches, fall
      back to a cheap LLM extraction call on the raw HTML snippet; log every fallback trigger so selector
      drift is visible (feeds P1-10).
- [ ] **P1-10** Automated selector health checks — daily CI/cron canary job per supported site (Haraj,
      OpenSooq, Dubizzle, etc. + Facebook/LinkedIn/Twitter/Google Maps), alert if match rate < threshold.

## Phase 2 — New Data Sources & Modules

Each row follows the existing module pattern (`backend/src/scrapers/*.js` + route + frontend page under
`app/dashboard/tools/`) established by Facebook/LinkedIn/Twitter/Google Maps/Haraj.

- [ ] **P2-1** Instagram scraper (public profiles, posts, comments, hashtags, location search).
- [ ] **P2-2** TikTok scraper (video metadata, music, hashtag trends).
- [ ] **P2-3** YouTube scraper (channel videos, comments, search results).
- [ ] **P2-4** Amazon / eBay / Etsy product scraper (price, reviews, seller info). Exposed via the
      user-facing API from [API_BRIEF.md](API_BRIEF.md).
- [ ] **P2-5** Google Reviews / Yelp / Trustpilot scraper (ratings, review text, owner replies).
- [ ] **P2-6** Real-estate portal scraper (Zillow, Realtor.com, Rightmove).
- [ ] **P2-7** Job board scraper (LinkedIn Jobs, Indeed, Glassdoor — public listings only).
- [ ] **P2-8** News/RSS keyword aggregator.
- [ ] **P2-9** (Optional, lower priority) OSINT/alternative-source module — scope and legal review before
      any build work.
- [ ] **P2-10** Custom scraper builder UI — visual selector picker → generates a reusable Playwright
      template; depends on P4-3 (visual builder) being scoped first, largest single item in this phase.

## Phase 3 — Platform Architecture & Reliability

- [ ] **P3-1** Unify identity/database layer — retire the Prisma/Knex dual-user "shadow user" bridge
      (`backend/src/middleware/internalAuth.js`) described in `FEATURESLIST_ENHANCED.md`; migrate
      `scrape_jobs`/`scraper_logs`/etc. into the Prisma schema. **Largest architectural change in this
      backlog — do as its own migration project, not inline with feature work.**
- [ ] **P3-2** Redis + BullMQ job queue — replace Knex-backed `scrape_jobs` polling with BullMQ; priority
      queues, delayed retries, concurrency limits, real-time progress via queue events instead of DB polling.
- [ ] **P3-3** Global rate limiting — Redis-backed limiter (`@upstash/ratelimit` or `express-rate-limit` +
      Redis store) on all Next.js route handlers and Express endpoints.
- [ ] **P3-4** Webhook delivery — `webhook_url` field on job creation; signed POST on completion/failure.
      (Full contract in [API_BRIEF.md](API_BRIEF.md) §5.)
- [ ] **P3-5** Scheduled/recurring scrapes — `schedule` (cron string) field on jobs; BullMQ repeatable jobs
      (depends on P3-2).
- [ ] **P3-6** OpenAPI 3.0 spec + Swagger UI at `/docs`, generated from the contract in
      [API_BRIEF.md](API_BRIEF.md).
- [ ] **P3-7** User-facing API keys + tiered rate limits for external developers (builds on P3-3).
- [ ] **P3-8** Data enrichment/validation pipeline — post-scrape hooks: email MX validation, E.164 phone
      normalization, optional Clearbit/Hunter.io company enrichment.
- [ ] **P3-9** Custom data mapping — user-defined output field mapping applied in
      `backend/src/services/exportService.js` before file generation.

## Phase 4 — Admin, Billing & UX

- [ ] **P4-1** Express-side admin panel — API key pool, proxy list, cross-user job queue visibility
      (separate from the existing Prisma-backed `/dashboard/admin`).
- [ ] **P4-2** System health dashboard — active jobs, proxy health, success rate, latency, sourced from P3-2
      queue metrics.
- [ ] **P4-3** Billing/subscription integration (Stripe or Paddle), plan tiers (Free/Pro/Enterprise), gates
      P3-7's rate-limit tiers.
- [ ] **P4-4** Audit logs — every admin/user action, for SOC2 readiness.
- [ ] **P4-5** Job builder wizard (step-by-step + preview) in the dashboard.
- [ ] **P4-6** Live result preview while a job is still running (SSE-driven, extends existing log stream).
- [ ] **P4-7** Bulk keyword import (CSV upload → batch job).
- [ ] **P4-8** Mobile-responsive pass on `DashboardShell` + all tool pages.
- [ ] **P4-9** Direct export integrations — Google Sheets/Airtable (OAuth), S3/Dropbox/FTP/WebDAV push.

## Phase 5 — Security, Compliance & DevOps

- [ ] **P5-1** JWT refresh tokens + session revocation (`refresh_tokens` table in Prisma, short-lived access
      token + 7-day refresh token).
- [ ] **P5-2** Email verification flow (`is_verified` + `verification_token` on `User`, Resend/SendGrid).
- [ ] **P5-3** GDPR/CCPA "Scrub PII" export toggle — hash/redact personal emails/phones on export.
- [ ] **P5-4** Automated test suite — Jest + Supertest (Express API, real MySQL via testcontainers),
      Playwright end-to-end (frontend + dry-run scraping mode). Addresses gap #2 in
      `FEATURESLIST_ENHANCED.md`.
- [ ] **P5-5** Fix the ESLint 8→9 config mismatch (gap #5 in `FEATURESLIST_ENHANCED.md`).
- [ ] **P5-6** CI/CD pipeline — GitHub Actions running `tsc --noEmit`, `next lint`, and the P5-4 test suite
      on every PR.

---

## Sequencing notes

- P3-1 (identity unification) and P3-2 (BullMQ) are the two highest-leverage architectural changes — every
  later phase gets simpler once these land, but neither is required to start Phase 1 or Phase 2 work.
- CAPTCHA (P1-6) and proxy rotation (P1-1) should land before adding new scraper modules (Phase 2) — new
  modules will immediately need both.
- Billing (P4-3) blocks nothing else technically but should land before P3-7 (external API keys) is opened
  to real customers.
- Known gaps already on record in `FEATURESLIST_ENHANCED.md` (shadow-user email collision, no test suite,
  ESLint mismatch, Google Maps locale) are folded into P3-1, P5-4, P5-5 respectively so they aren't tracked
  in two places.
