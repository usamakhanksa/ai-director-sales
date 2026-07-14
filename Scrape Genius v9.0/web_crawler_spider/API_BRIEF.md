# Scrape Genius — Public/Developer API Brief

This is a separate document from `apiurl.md`. `apiurl.md` catalogues the *internal* endpoints the
dashboard itself calls (`/api/scrape/*` in Next.js, `/v1/jobs` etc. in Express). This brief specifies the
**public-facing, API-key-authenticated developer API** that Phase 3/4 of [TASK_SYSTEM.md](TASK_SYSTEM.md)
build on top of that internal engine (tasks P3-4, P3-6, P3-7, P3-9).

Nothing here exists yet — this is the target contract to implement against, not a description of current
behavior.

## 1. Design goals

- One documented, versioned surface (`/api/v1/*`) instead of the dashboard's internal `/api/scrape/*` routes.
- API-key auth, independent of the dashboard's session JWT — so third parties can integrate without a
  browser session.
- Every long-running scrape is a **job**: submit → poll or webhook → fetch result. No endpoint blocks for
  the duration of a scrape.
- Same internal engine (job queue, Playwright scrapers, exporters) as the dashboard — this API is a new
  front door, not a new backend.

## 2. Auth

- `Authorization: Bearer sg_live_<key>` header on every request.
- Keys are issued from `/dashboard/settings/api-keys` (already exists) and carry a plan tier
  (Free/Pro/Enterprise — ties to P4-3 billing) that determines rate limit and concurrent-job cap.
- Rate limit enforced by the Redis-backed limiter from TASK_SYSTEM.md P3-3; responses include
  `X-RateLimit-Limit` / `X-RateLimit-Remaining` / `X-RateLimit-Reset` headers.

## 3. Core resource: Job

```
POST /api/v1/jobs
```
Body:
```json
{
  "module": "google_maps",          // one of: google_maps, facebook, linkedin, twitter, classified, custom
  "keywords": ["coffee shop Seattle"],
  "config": { "limit": 100, "country": "US" },
  "output_mapping": { "phone": "primary_phone", "email": "primary_email" },  // optional, see §6
  "webhook_url": "https://example.com/hooks/scrapegenius",                  // optional, see §5
  "schedule": "0 9 * * 1"                                                    // optional cron, see §7
}
```
Response `202 Accepted`:
```json
{ "job_id": "job_9f2a...", "status": "queued", "created_at": "2026-07-14T09:00:00Z" }
```

```
GET /api/v1/jobs/{job_id}
```
Returns status (`queued|running|completed|failed|cancelled`), progress percentage, and result summary
counts. Mirrors the existing `scrape_jobs` row shape used internally.

```
GET /api/v1/jobs/{job_id}/results?format=json|csv|xlsx
```
Streams the export. `format=json` returns paginated results inline (`?page=`, `?per_page=`); file formats
redirect to a signed, expiring `EXPORT_DIR` URL (same mechanism as the dashboard's Export Manager).

```
DELETE /api/v1/jobs/{job_id}
```
Cancels a queued/running job (wraps the existing `jobManager` cancellation path).

```
GET /api/v1/jobs
```
List/filter the caller's jobs (`?module=`, `?status=`, `?since=`).

## 4. Custom scrape (`module: "custom"`)

For the Custom API Connector tool (`/dashboard/tools/custom-api`, `custom-api-connector` in
`lib/tools-data.ts`). Body adds:
```json
{
  "module": "custom",
  "target_url": "https://example.com/listings",
  "schema": { "title": "h2.title", "price": ".price", "link": "a@href" }
}
```
Selectors are CSS by default; `@attr` suffix extracts an attribute instead of text content. Runs through
the generic Playwright/cheerio engine with the same anti-detection stack (proxy rotation, fingerprinting)
as every other module — see TASK_SYSTEM.md Phase 1.

## 5. Webhooks

On `completed` or `failed`, if `webhook_url` was set:
```
POST <webhook_url>
X-ScrapeGenius-Signature: sha256=<hmac of body using the account's webhook secret>
Content-Type: application/json

{ "job_id": "job_9f2a...", "status": "completed", "result_count": 214, "results_url": "https://.../export/job_9f2a....xlsx" }
```
3 retries with exponential backoff (1m/5m/15m) on non-2xx response; failures after that are logged and
visible in the dashboard's job detail view, not silently dropped.

## 6. Output mapping

`output_mapping` in the job body renames/reshapes scraped fields before export — same feature as the
dashboard's "Custom Data Mapping" (TASK_SYSTEM.md P3-9). Applied in `exportService.js` right before file
generation, so it's shared between the API and dashboard-triggered jobs rather than reimplemented per
surface.

## 7. Scheduling

`schedule` (standard 5-field cron) turns a one-off job spec into a recurring template. Requires the
BullMQ-backed queue (TASK_SYSTEM.md P3-2) — each fire creates a new `job_id` linked back to the template
via `parent_schedule_id`.

## 8. Errors

Standard shape:
```json
{ "error": { "code": "rate_limited", "message": "Plan limit of 100 jobs/day reached", "retry_after": 3600 } }
```
Codes: `unauthorized`, `invalid_module`, `invalid_config`, `rate_limited`, `quota_exceeded`, `job_not_found`,
`internal_error`.

## 9. Versioning & docs

- URL-prefixed versioning: `/api/v1/`, `/api/v2/` — no header-based version negotiation.
- Full machine-readable contract published as OpenAPI 3.0 at `/api/v1/openapi.json`, rendered via Swagger
  UI at `/docs` (TASK_SYSTEM.md P3-6). This markdown brief is the human-readable source of truth that the
  OpenAPI spec should be generated/kept in sync with — not the other way around.
- SDKs (Node/Python/PHP) wrap this contract 1:1; no SDK-only behavior.

## 10. Relationship to existing internal APIs

| Existing (internal, session-JWT) | New (public, API-key) |
|---|---|
| `POST /api/social/facebook`, `/api/classified`, `POST /v1/jobs` (Express, internal-secret bridged) | `POST /api/v1/jobs` with `module: "facebook"` / `"classified"` / etc. |
| Dashboard SSE log stream | Same SSE stream, plus webhook as an alternative for non-browser clients |
| `/dashboard/export` re-download | `GET /api/v1/jobs/{id}/results` |

The public API is a thin, authenticated wrapper around the same `jobManager`/scraper/export pipeline
documented in `FEATURESLIST_ENHANCED.md` — it does not introduce a second scraping engine.
