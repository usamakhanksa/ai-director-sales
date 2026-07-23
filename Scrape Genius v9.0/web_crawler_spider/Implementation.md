# ScrapeGenius Platform Production Enhancements — Implementation Guide

## Overview
This document tracks four high-ROI, zero/low-cost enhancements added to ScrapeGenius, and the state of every file they touch. Unlike the initial draft of this document, every claim below has been verified against the actual codebase (typecheck, `prisma validate`/`migrate status`, Node syntax check, and route wiring) — nothing here is aspirational.

---

## 1. Zero-Cost AI Scraper Module

**Status: fully working, refactored for consistency and type-safety.**

### What it does
Fetches clean Markdown for any URL via [https://r.jina.ai](https://r.jina.ai) (a free reader proxy, no API key) and extracts emails, phone numbers, and company names. Supports Arabic (RTL) and English content.

### Files
- [lib/scrapers/zero-cost-ai-scraper.ts](lib/scrapers/zero-cost-ai-scraper.ts) — `zeroCostAIScraper(url)` and `multilingualZeroCostAIScraper(url)`.
- [app/api/scrape/ai-scraper/route.ts](app/api/scrape/ai-scraper/route.ts) — **new** authenticated API route exposing it (the original draft implemented the utility but never wired a route to it).

### Fixes applied
- The first draft duplicated regex logic and had two `tsc` errors (a dotAll `/s` regex flag incompatible with this project's `es2017` build target, and a `.match(...) || []` union that TypeScript widened to `never[]` in a couple of branches). Rewrote it to reuse the existing `extractEmails`/`extractPhones` helpers from [lib/scrapers/extract.ts](lib/scrapers/extract.ts) (the same extractors every other scraper route uses) and replaced the `s` flag with an equivalent `[\s\S]` pattern.
- `npx tsc --noEmit` now passes clean.

### API
`POST /api/scrape/ai-scraper` (session auth required)
```json
{ "url": "https://example.com" }
```
Response: `{ success: true, data: { url, emails: [], phones: [], companies: [], title?, description? } }`. Results are persisted via `saveScrapedRecords` under source `WEBSITE`.

### Dashboard
Sidebar → Tools → **Zero-Cost AI Scraper** (`/dashboard/tools/ai-scraper`), or Live Tools catalog under "AI Enrichment". Uses the generic tool-runner page ([app/dashboard/tools/[slug]/page.tsx](app/dashboard/tools/[slug]/page.tsx)), which now supports a single-URL form (`fieldKind: "url"`).

---

## 2. Email Verification API

**Status: fully working, no changes needed beyond wiring a dashboard entry point.**

### What it does
`POST /api/verify/email` — Zod syntax validation + a free MX-record DNS lookup (Node's built-in `dns` module, `resolveMx` with an `resolve4` fallback), disposable/free-provider domain detection, and typo-correction suggestions. No paid API involved.

### Files
- [app/api/verify/email/route.ts](app/api/verify/email/route.ts) — unchanged, already correct (public endpoint, no auth required by design).

### Request / Response
```json
{ "email": "user@example.com" }
```
```json
{
  "success": true,
  "data": {
    "email": "user@example.com",
    "isValidSyntax": true,
    "hasMxRecord": true,
    "isDisposable": false,
    "isFreeProvider": true,
    "deliverable": true,
    "suggestions": []
  }
}
```

### Dashboard
Sidebar → Tools → **Email Verifier** (`/dashboard/tools/email-verifier`). New `fieldKind: "email"` added to the generic tool-runner page/`lib/tools-data.ts`.

---

## 3. Webhooks (register + list)

**Status: the API route existed from the first draft, but its Prisma model did not — this was a real bug (every call would have thrown "Unknown model Webhook"). Fixed.**

### What was broken
`app/api/webhooks/register/route.ts` called `prisma.webhook.findFirst`/`create`/`findMany`, but `prisma/schema.prisma` had no `Webhook` model at all. The Prisma client would not have exposed `prisma.webhook`, so every request to this route would have failed at runtime.

### Fix applied
Added the model (mirroring the existing `ApiConnector`/`CrmConnection` Prisma-owned-table convention — no Knex migration backs this table, Prisma migrations do):

```prisma
model Webhook {
  id        Int      @id @default(autoincrement())
  userId    Int      @map("user_id")
  user      User     @relation(fields: [userId], references: [id], onDelete: Cascade)
  url       String   @db.VarChar(2048)
  events    Json
  isActive  Boolean  @default(true) @map("is_active")
  createdAt DateTime @default(now()) @map("created_at")
  updatedAt DateTime @default(now()) @map("updated_at")

  @@index([userId])
  @@map("webhooks")
}
```
(`events` is `Json` rather than a native array — MySQL has no array column type, so this follows the same pattern as `ApiConnector.fieldMap`.) Added `webhooks Webhook[]` to `User`.

Ran, in order:
1. `npx prisma validate` — schema valid.
2. `npx prisma db push` — created the `webhooks` table + FK in the dev database (non-interactive; `prisma migrate dev` requires a TTY this environment doesn't have).
3. Hand-authored `prisma/migrations/20260714180000_add_webhook_model/migration.sql` (matching the exact DDL `db push` applied) and ran `prisma migrate resolve --applied` so the migration history stays consistent with what other environments will run via `prisma migrate deploy`.
4. `npx prisma migrate status` — confirms "Database schema is up to date."
5. `npx prisma generate` — regenerated the client so `prisma.webhook` now exists.

### Files
- [prisma/schema.prisma](prisma/schema.prisma) — added `Webhook` model + `User.webhooks` relation.
- [prisma/migrations/20260714180000_add_webhook_model/migration.sql](prisma/migrations/20260714180000_add_webhook_model/migration.sql) — new.
- [app/api/webhooks/register/route.ts](app/api/webhooks/register/route.ts) — unchanged, now actually functional.
- [app/dashboard/settings/webhooks/page.tsx](app/dashboard/settings/webhooks/page.tsx) — **new** UI: register a webhook URL + select events, list existing webhooks.

### API
- `POST /api/webhooks/register` — `{ url, events: [...], isActive? }` → creates a webhook (409 if the same URL is already registered for this user).
- `GET /api/webhooks/register` — lists the current user's webhooks.

Event types: `JOB_STARTED`, `JOB_COMPLETED`, `JOB_FAILED`, `EXPORT_READY`, `SCRAPE_DATA_AVAILABLE`.

> Note: this milestone only covers **registering** webhooks. Nothing in the codebase yet *fires* them (no dispatcher hooked into the job-completion/export-ready code paths). That's a natural next step — see Future Enhancements.

### Dashboard
Sidebar → Settings → **Webhooks** (`/dashboard/settings/webhooks`).

---

## 4. Google News RSS Scraper

**Status: backend route existed from the first draft but was never mounted (dead code) and had no frontend proxy or dashboard entry. Fixed.**

### What was broken
`backend/src/routes/google-news.routes.js` was fully written but never `require()`'d or `app.use()`'d in `backend/src/app.js` — so `GET /v1/scrape/google-news` would have 404'd via the `notFoundHandler`. Also `xml2js` and `zod` were used by the route but not listed in `backend/package.json`'s own dependencies (they happened to resolve via the workspace root, but that's not guaranteed in every install layout).

### Fixes applied
- Mounted the route in [backend/src/app.js](backend/src/app.js):
  ```js
  const googleNewsRoutes = require("./routes/google-news.routes");
  ...
  app.use("/v1/scrape/google-news", googleNewsRoutes);
  ```
- Added `xml2js` and `zod` to [backend/package.json](backend/package.json) dependencies explicitly.
- Fixed a small bug in the route: the `q` search param was being `encodeURIComponent`'d inside the Zod schema's `.transform()`, which meant the response's `query` field echoed back the URL-encoded string instead of the raw search term. Moved the encoding to where the RSS URL is built instead.
- Verified with `node --check` (syntax) on both `app.js` and `google-news.routes.js` — no way to run a live DB-backed integration test in this environment, but the route logic (axios fetch → `xml2js.parseString` with `stripPrefix` → normalize `{title, link, pubDate, description, source, guid}` → optional `limit`/`offset` pagination) is unchanged from the original correct implementation.

### Files
- [backend/src/routes/google-news.routes.js](backend/src/routes/google-news.routes.js) — bug fix only (query-encoding).
- [backend/src/app.js](backend/src/app.js) — added require + mount.
- [backend/package.json](backend/package.json) — added `xml2js`, `zod` deps.
- [app/api/scrape/google-news/route.ts](app/api/scrape/google-news/route.ts) — **new** Next.js proxy route (session-authenticated, uses `lib/backend-client.ts`'s `backendFetch` to bridge identity to the Express backend, then persists results via `saveScrapedRecords` under source `NEWS_RSS`, which already existed in the `ScrapeSource` enum).

### API
- Backend: `GET /v1/scrape/google-news?q=&hl=&gl=&ceid=&dateRestrict=&limit=&offset=` (internal-secret or session auth via `requireAuthOrInternal`)
- Frontend proxy: `GET /api/scrape/google-news?q=&hl=&gl=&ceid=&dateRestrict=&limit=&offset=` (session auth)

### Dashboard
Sidebar → Tools → **Google News Scraper** (`/dashboard/tools/google-news`). The generic tool-runner page ([app/dashboard/tools/[slug]/page.tsx](app/dashboard/tools/[slug]/page.tsx)) gained a `method: "GET"` option on `ToolRunConfig` so tools that read via query string (like this one) don't need a dedicated custom page.

---

## Cross-Cutting Fixes Made While Wiring These In

- **`lib/tools-data.ts`**: added `ToolFieldKind` values `"url"` and `"email"`, and a `method?: "GET" | "POST"` option on `ToolRunConfig`.
- **`app/dashboard/tools/[slug]/page.tsx`** (the shared tool-runner): added form fields + submit handling for `"url"`/`"email"` field kinds, GET-vs-POST request building, and result-row mapping for the AI scraper (emails/phones/companies) and Google News (`data.items`) response shapes.
- **`lib/nav-data.ts`** / **`lib/i18n-strings.ts`**: added sidebar entries (with EN/AR translations) for all four features — Zero-Cost AI Scraper, Google News Scraper, and Email Verifier under Tools; Webhooks under Settings.

## Verification performed
- `npx prisma validate` — passes.
- `npx prisma migrate status` — "Database schema is up to date."
- `npx prisma generate` — regenerates client with `Webhook` model included.
- `npx tsc --noEmit -p .` — passes with zero errors across the whole Next.js app (including all new/changed files).
- `node --check` on `backend/src/app.js` and `backend/src/routes/google-news.routes.js` — valid syntax.
- `npx next lint` — the only errors reported are pre-existing issues in files this work did not touch (unescaped quotes in a few unrelated pages, an `@typescript-eslint/no-var-requires` config issue in `document-data-scraper`, a `module` variable warning in `v1/scrape`). None of the new files introduced lint errors.

## What was NOT implemented (explicitly out of scope for this pass, listed for transparency)
- **Webhook dispatch**: registering a webhook does not yet cause any event to actually POST to it. No dispatcher exists in the job/export code paths.
- **Team/workspace management, billing/monetization tiers, scheduled exports** — not implemented; see Future Enhancements below (unchanged from the original proposal, still aspirational).
- **Python/Celery worker architecture** — not implemented; still aspirational.
- **Freemium proxy/anti-bot API integration** (SerpApi/ZenRows/ScrapingBee) — not implemented; still aspirational.

## Future Enhancements (aspirational — not built)
- Webhook dispatcher: hook `ScrapeJob` completion/failure and `ExportRecord` creation to fan out matching active webhooks with an HMAC-signed payload and retry/backoff.
- Celery + Redis Python worker tier to offload heavy Playwright scrapes and AI calls from the Node event loop.
- Tiered rate limiting / usage-based billing on top of the existing `ApiClientKey`/`UserSearchKey` quota system.
- SerpApi/ZenRows/ScrapingBee as an optional proxy layer in front of the direct-HTTP search scrapers for higher reliability against anti-bot defenses.
