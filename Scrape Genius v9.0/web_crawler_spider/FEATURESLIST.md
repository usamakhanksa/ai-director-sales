# ScrapeGenius Backend — Feature List & Roadmap

## What exists now (this pass: Prisma + Next.js App Router)

Built directly into this project (`app/api/*`, `prisma/`, `lib/`), replacing
file-based and external-service dependencies with `google-map-scraper-pro`
(MySQL, via Prisma):

- **Auth**: `POST /api/auth/signup`, `POST /api/auth/login` — bcrypt password
  hashing, JWT issuance (`lib/jwt.ts`), zod-validated payloads.
- **Per-user Google Custom Search key pool**: `GET /api/get_keys` returns
  only active, non-exhausted keys for the authenticated user, with
  `used_today` / `remaining_today` computed from `usage_logs`.
- **Race-safe usage tracking**: `POST /api/update_usage` increments a key's
  daily count with a single conditional `UPDATE ... WHERE request_count + ?
  <= daily_limit`, so concurrent requests can never blow past the limit —
  replacing the old obfuscated `external_usage.json` file, which had no
  locking at all.
- **Scraped data + dashboard stats**: `POST /api/saved` writes a
  `ScrapedRecord` and upserts the matching `DashboardStat` tile inside one
  Prisma transaction. `GET /api/dashboard/stats` returns them in the
  `{title, records}` shape the original localStorage-based dashboard context
  expected, so the frontend context provider needs no reshaping.
- **Purchase code activation**: `POST /api/purchase-code/activate` — codes
  are seeded unassigned and claimed by whichever account redeems them first
  (idempotent for the claiming account, rejected for anyone else). Code
  `12345` is seeded and verified working end-to-end.
- **Standardized responses**: every route returns `{success, data?, error?}`
  via `lib/api-response.ts`; zod errors, auth errors, and unexpected errors
  all map to the right HTTP status automatically.
- **Realistic seed data**: 1 admin + 2 users, 3 API keys with deliberately
  varied usage (92/100, 10/100, 0/100) so limit-boundary behavior is visible
  immediately, 2 purchase codes (one unclaimed — `12345` — one pre-activated),
  and populated dashboard stats for both users.

All of the above was exercised live against the real local MySQL database
during this build (signup → login → get_keys → update_usage at/over the
limit → saved → dashboard/stats → purchase-code activate, including
rejection paths) — see `API_EXAMPLES.md` for the exact curl commands.

## Also present in this codebase (earlier pass, still standing)

A separate Express + Knex implementation lives in `backend/` (its own
`package.json`, runs on port 4000) covering session-table-backed auth,
global/admin-managed API key pool with least-used-key selection + reserve/
reconcile usage limiting, and a maps-scraping skeleton. **The two
implementations use different, incompatible schemas in the same database
name** — only one should be the system of record. Worth deciding which one
to keep; say the word and I'll retire the other rather than leaving both
half-wired.

## Known gaps / suggested enhancements

- **No route actually calls the Google Custom Search API yet.** `get_keys` /
  `update_usage` manage key rotation and quota, but nothing in this pass
  performs the search itself and feeds usage back automatically — that
  still has to be wired client-side (fetch a key → call
  `googleapis.com/customsearch/v1` → call `update_usage`) or added as a
  server route that does all three in one call (the Express backend's
  `POST /v1/search/google` already does this, using a global key pool
  instead of per-user).
- **Email verification** isn't modeled (no verification-code column this
  round) — `isVerified` is set `true` on signup. Add a verification table/
  columns + a real mail provider if you need it enforced.
- **No refresh/revoke mechanism** — JWTs are stateless with a fixed
  `JWT_EXPIRES_IN`; there's no way to force-logout a compromised token
  before it expires. Add a sessions table if that matters.
- **Purchase code validation is local-only** — it checks the code exists,
  is unclaimed, and isn't expired; it doesn't call out to a real licensing
  provider (e.g. Envato's API). Fine for a self-hosted/internal system, not
  a drop-in replacement for third-party license enforcement.
- **No admin routes yet** in this pass (list/manage users, keys, usage,
  purchase codes) — the Express backend's `routes/admin.js` has a working
  pattern to port over if needed.
- **Maps scraping** is entirely unimplemented here (no route, no
  `ScrapedRecord.source = MAP` producer) — the actual Playwright-driven
  scraper lives in the compiled `.next/server/app/api/google_map_scraper`
  bundle from the original app and was never ported to either backend.
- **No automated tests.** Everything above was verified with live curl
  calls this session, not a repeatable test suite — worth adding integration
  tests (e.g. against a test database) before this goes further.
- **No rate limiting on the Next.js routes** in this pass (unlike the
  Express backend, which has `express-rate-limit` on auth endpoints) —
  Next.js App Router has no built-in middleware chaining per-route, so this
  needs either global `middleware.ts` or a per-route wrapper.
