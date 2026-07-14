# ScrapeGenius Backend (MySQL + Express)

Replaces the original file/in-memory storage used by the Next.js app:

| Old mechanism | Replaced by |
|---|---|
| `keys.json` (read by `/api/get_keys`) | `api_keys` table |
| `external_usage.json` (obfuscated, read/written by `/api/google_search_api` + `/api/update_usage`) | `usage_logs` table, updated atomically inside a MySQL transaction |
| Calls to `https://api.scrapegenius.com/v1/{signup,login,forget,verification,restricted/*}` | Local `/v1/*` routes backed by the `users` / `auth_tokens` tables |
| `data/map_data/<query>.json` cache written by the Playwright maps scraper | `search_queries` + `maps_results` tables |

It was built by reading the actual compiled route handlers under
`../.next/server/app/api/*/route.js` (Google Search API key rotation/usage-limit
logic, `get_keys`, `update_usage`) rather than guessing, so the daily-limit
behavior (100 requests/day total, 100/day per key, least-used-key-first
selection) matches the original exactly — it's just concurrency-safe now via
row-locked transactions instead of a JSON file.

**Out of scope, on purpose:** the repo also contains Playwright-driven
scrapers (Google Maps, Bing, business-directory/website scraping), a
Google Sheets/Drive-based WHOIS lookup, and Facebook/YouTube scraper stubs.
None of that was asked for and none of it is reimplemented here — `routes/maps.js`
is intentionally a skeleton (usage-limiting + MySQL storage only) with a
`TODO` marking where a real scraper should plug in.

**⚠️ Security note found during analysis:** `json-service.json`, `d-access.json`,
and the compiled `app/api/s_t_d/route.js` bundle all contain a live Google
service-account private key in plaintext (one is hardcoded directly in the
built JS, not just read from a file at runtime). If this build was ever
deployed or committed anywhere, rotate that key.

## 1. Install

```bash
cd backend
npm install
cp .env.example .env
```

Edit `.env`:
- `DB_PASSWORD` — your local MySQL root password (Laragon default is often empty, but check yours)
- `JWT_SECRET` — any long random string
- `GOOGLE_API_FALLBACK_KEY` / `GOOGLE_API_FALLBACK_CX` — a real Google Custom Search key/engine ID, if you have one (used by the seed script)
- `GOOGLE_OAUTH_CLIENT_ID` — only needed if you use `/v1/signuploginwithgoogle`

## 2. Create the database, run migrations, seed data

The migration files are the source of truth; `sql/schema.sql` and `sql/seed.sql`
are a plain-SQL mirror of the same thing for anyone who'd rather not use Knex.

```bash
# create the database (or let it happen automatically — mysql2 doesn't auto-create DBs,
# so run this once):
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS \`google-map-scraper-pro\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

npm run migrate       # applies all 6 tables (idempotent — checks hasTable before creating)
npm run seed          # admin user + 2 placeholder Custom Search keys + 1 maps-scraper pseudo key
```

Seeded admin login: `admin@scrapegenius.com` / `ChangeMe123!` — **change this password immediately.**

The two `google_custom_search` rows seeded in `api_keys` have placeholder
`REPLACE_WITH_REAL_...` values — replace them with real keys via
`PATCH /v1/admin/api-keys/:id` or directly in the database before doing real
searches.

Rollback: `npm run migrate:rollback`. Status: `npm run migrate:status`.

## 3. Run the server

```bash
npm run dev     # nodemon, auto-restart
# or
npm start
```

Server listens on `http://localhost:4000` (configurable via `PORT`). Health check: `GET /health`.

## 4. API surface

| Method | Path | Notes |
|---|---|---|
| POST | `/v1/signup` | creates unverified user, logs a 6-digit code (wire up real email later) |
| POST | `/v1/verification` | `{email, code}` → marks verified |
| POST | `/v1/login` | requires `verified=true`, returns `{token, user}` |
| POST | `/v1/signuploginwithgoogle` | `{idToken}` verified via `google-auth-library` |
| POST | `/v1/forget` | `{email}` → logs a reset token |
| POST | `/v1/reset-password` | `{reset_token, password}` |
| POST | `/v1/logout` | revokes the current session (needs `Authorization: Bearer`) |
| GET | `/v1/restricted/purchasecodeactivation/:code` | local stub — see note below |
| GET | `/v1/restricted/getgooglecode` | active `api_keys`, `{success, data}` |
| GET | `/v1/restricted/users?limit=500` | admin-only |
| POST | `/v1/search/google` | `{query, limit}` → bare array of links, same shape as the original |
| POST | `/v1/search/maps` | skeleton: cache lookup + usage reservation, no scraper wired up |
| POST | `/v1/search/maps/:searchQueryId/results` | for a scraper worker to persist results |
| GET | `/v1/search/maps/cache?query=` | cached maps results lookup |
| GET/POST/PATCH/DELETE | `/v1/admin/api-keys` | manage Custom Search keys |
| GET | `/v1/admin/usage?date=YYYY-MM-DD` | per-key usage for a given day |
| GET | `/v1/admin/search-queries` | recent query log |

All `/v1/search/*` and `/v1/admin/*` routes require `Authorization: Bearer <token>`.

**Purchase code activation** is a stub: the original app validated codes
against Envato's licensing API (a vendor-side check this backend can't
replicate without your own Envato personal token). It currently just checks
the code's format and marks it verified — plug in a real call to Envato's
verify-purchase endpoint if you need that guarantee.

## 5. Frontend integration

Point the Next.js app's API base URL at this server instead of the vendor's:

```diff
- const API_BASE = "https://api.scrapegenius.com/v1"
+ const API_BASE = "http://localhost:4000/v1"
```

The Google Search route also changes shape slightly: instead of `POST
/api/google_search_api` with `{query, limit, tk}` (token in the body), call
`POST http://localhost:4000/v1/search/google` with `{query, limit}` and the
token in the `Authorization: Bearer` header like every other authenticated
route — this was the one deliberate deviation from the original contract,
since passing bearer tokens in a request body instead of a header is worth
fixing while touching this code anyway.

The Next.js app's own internal routes (`/api/get_keys`, `/api/update_usage`,
`/api/google_search_api`) are no longer needed once the frontend calls this
backend directly — `keys.json` and `external_usage.json` can be deleted.

## 6. Concurrency note

`reserveKeyForSearch()` in `src/services/keyUsageService.js` locks the
relevant `api_keys`/`usage_logs` rows (`SELECT ... FOR UPDATE`) inside a
transaction, picks the least-used active key, and **reserves** its requested
quota atomically before any external HTTP call is made. The actual Google
Custom Search calls happen outside that transaction (so a slow external API
never holds a DB lock); if fewer calls succeed than were reserved, the unused
portion is released back via an atomic `GREATEST(count - n, 0)` update. This
is what makes the daily limits safe under concurrent requests, which the
original `external_usage.json` file could not guarantee.
