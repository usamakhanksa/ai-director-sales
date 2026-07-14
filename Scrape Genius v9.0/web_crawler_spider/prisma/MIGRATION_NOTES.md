# Schema ownership and migration workflow

## 1. Knex is authoritative for schema

The Express backend (`backend/`) uses Knex migrations (`backend/migrations/*.js`)
against the same MySQL database that this Next.js app talks to via Prisma.
As of 2026-07-14, **Knex owns table creation for every table it creates**.
`prisma/schema.prisma` has been edited so that Prisma models for those tables
are read-only mirrors (correct field names, types, nullability, defaults,
enums) of what Knex actually creates — Prisma no longer independently defines
or diverges from that shape. Prisma must not be used to create or alter these
tables (no `prisma migrate dev/deploy` against them).

## 2. `prisma/migrations/` is stale and unreliable

The two existing folders:

- `prisma/migrations/20260713095654_init/`
- `prisma/migrations/20260713140000_add_scraper_tools/`

were generated back when Prisma still independently owned tables such as
`users`, `api_keys`, `usage_logs`, `scrape_jobs`, `scraper_logs`, and
`export_records`. Those `migration.sql` files describe a schema that no
longer matches the corrected `schema.prisma` (which now mirrors Knex's actual
columns/enums/indexes for those tables). **Do not run `prisma migrate deploy`
or `prisma migrate dev` using this migrations history against a real
database** — it would try to (re)create tables with the old, incorrect
column set and conflict with what Knex has already created. The folders are
left in place for historical reference only; they are not deleted, but they
must be treated as dead/inapplicable history going forward.

## 3. Correct workflow going forward

Pick one of:

- **Safer:** run `prisma db pull` against the live, Knex-managed database to
  regenerate `schema.prisma` directly from reality, then re-apply any
  Prisma-specific customizations (relation names, comments, `@map`/`@@map`
  aesthetics) on top of the pulled result.
- **Manual sync:** whenever a new Knex migration is added under
  `backend/migrations/`, manually update the corresponding model(s) in
  `schema.prisma` (fields, types, enums, indexes, FKs) to match, in the same
  pull request.

Either way, never run `prisma migrate` commands that would attempt to create
or alter tables that Knex owns.

## 4. Model ownership

### Knex-owned (Prisma model is a read-only mirror of a Knex-created table)

| Prisma model | Table | Knex migration |
|---|---|---|
| `User` | `users` | `20260713120001_create_users_table.js` |
| `ApiKey` | `api_keys` | `20260713120003_create_api_keys_table.js` (shared Google Custom Search key pool — **not** per-user) |
| `UsageLog` | `usage_logs` | `20260713120004_create_usage_logs_table.js` |
| `ScrapeJob` | `scrape_jobs` | `20260714120001_create_scrape_jobs.js` |
| `ScraperLog` | `scraper_logs` | `20260714120006_create_scraper_logs.js` |
| `ExportRecord` | `export_records` | `20260714120005_create_export_records.js` |
| `UserSearchKey` | `user_search_keys` | `20260714160000_create_user_search_keys_table.js` (per-user Google Custom Search keys — added 2026-07-14 to give `app/api/keys/*`, `get_keys`, `update_usage` their own table, since they were previously mistakenly reading/writing the shared `ApiKey`/`api_keys` pool) |
| `UserSearchUsageLog` | `user_search_usage_logs` | `20260714160001_create_user_search_usage_logs_table.js` |

Knex-created tables that currently have **no** Prisma model at all (left
alone — not modeled, not owned by Prisma):
`auth_tokens`, `search_queries`, `maps_results`, `social_results`,
`classified_results`, `maps_job_results`, `dork_history`.

Note: `social_results` (Facebook/LinkedIn/Twitter/Instagram contact scraping,
keyed by `job_id` + `source` + `keyword`) is a different table from Prisma's
`InstagramResult` model below — they were checked column-by-column and are
**not** the same table, so `InstagramResult` was left as Prisma-owned rather
than remapped onto `social_results`.

### Prisma-owned (no Knex equivalent — Prisma remains authoritative)

| Prisma model | Table (`@@map`) |
|---|---|
| `PurchaseCode` | `purchase_codes` |
| `ScrapedRecord` | `scraped_records` |
| `DashboardStat` | `dashboard_stats` |
| `ApiConnector` | `api_connectors` |
| `CrmConnection` | `crm_connections` |
| `RefreshToken` | `refresh_tokens` |
| `ApiClientKey` | `api_client_keys` (map added — was missing) |
| `ApiUsageLog` | `api_usage_logs` |
| `InstagramResult` | `instagram_results` |

## 5. Summary of field-level changes made to `schema.prisma`

- **`User` → `users`**: replaced `passwordHash` (was required) with nullable
  `password_hash`; removed `role` (`Role` enum) and `isVerified`; added
  `country`, `verified` (boolean), `verificationCode`,
  `verificationCodeExpiresAt`, `resetToken`, `resetTokenExpiresAt`,
  `purchaseCode`, `purchaseCodeVerified`, `admin`, `googleId` (unique);
  removed the `apiKeys ApiKey[]` back-relation (no FK exists); `updatedAt`
  changed from `@updatedAt` to a plain DB-defaulted column (Knex sets a
  static default, not an on-update trigger).
- **`ApiKey` → `api_keys`**: complete redesign — removed `userId`/`user`
  relation, `googleApiKey`, `searchEngineId`; added `key` (unique), `cx`,
  `provider` (default `google_custom_search`); kept `isActive`,
  `dailyLimit`, `createdAt`; added `updatedAt`.
- **`UsageLog` → `usage_logs`**: removed `requestCount`, `lastResetAt`;
  added `count` (int, default 0), `searchType` (default `search`),
  `createdAt`, `updatedAt`; unique constraint changed from
  `(apiKeyId, date)` to `(apiKeyId, date, searchType)`.
- **`ScrapeJob` → `scrape_jobs`**: `status` changed from free-text `String`
  (default `"pending"`) to the `ScrapeJobStatus` enum
  (`QUEUED|RUNNING|DONE|FAILED|CANCELLED`, default `QUEUED`); removed
  `updatedAt` (column does not exist in the real table); added `exports`
  back-relation to `ExportRecord`; added explicit `@db.VarChar` lengths for
  `module` and `errorMessage`.
- **`ScraperLog` → `scraper_logs`**: `level` changed from free-text `String`
  to the `ScraperLogLevel` enum (`INFO|WARN|ERROR|DEBUG`, default `INFO`);
  added `@db.VarChar(2000)` to `message`; index list corrected to
  `(jobId, createdAt)` + `(level)` to match the Knex indexes.
- **`ExportRecord` → `export_records`**: `format` changed from free-text
  `String` to the `ExportFormat` enum (`XLSX|CSV|HTML|TXT`); added missing
  `fileSize` and `rowCount` columns; added `@db.VarChar(2048)` to `filePath`.
- **New enums added**: `ScrapeJobStatus`, `ScraperLogLevel`, `ExportFormat`
  (mirroring the MySQL `ENUM` columns Knex's `t.enum()` calls create).
- **`ApiClientKey`**: added missing `@@map("api_client_keys")` (previously
  had no explicit table mapping).
- No changes were made to `PurchaseCode`, `ScrapedRecord`, `DashboardStat`,
  `ApiConnector`, `CrmConnection`, `RefreshToken`, `ApiUsageLog`, or
  `InstagramResult` beyond the `ApiClientKey` map noted above — these remain
  Prisma-owned as-is.
- **Added `UserSearchKey`/`UserSearchUsageLog`** (2026-07-14): once `ApiKey`
  was corrected to match Knex's shared-pool `api_keys` table, the per-user
  Google Custom Search key routes (`app/api/keys/route.ts`,
  `app/api/keys/[id]/route.ts`, `app/api/get_keys/route.ts`,
  `app/api/update_usage/route.ts`, `lib/keys.ts`) had nowhere correct to
  read/write. New Knex migrations create `user_search_keys` and
  `user_search_usage_logs`, new Prisma models mirror them, and all five
  files were repointed from `prisma.apiKey`/`prisma.usageLog` to
  `prisma.userSearchKey`/`prisma.userSearchUsageLog`.
