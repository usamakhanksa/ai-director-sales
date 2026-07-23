# Dyafa Sales OS - Implementation Notes

## Architecture Decisions

- **Table naming**: `application/config/database.php` has `dbprefix` set to `''` for the whole legacy app, and it is shared, so CI's automatic prefixing cannot be relied on. Every `dso_*` table name is written literally in every Active Record call and raw SQL query (e.g. `$this->db->from('dso_leads')`, `$this->db->query("... FROM dso_contracts ...")`). No changes were made to `database.php` - it already points at `ci_realestate_web` with `root` / no password, which is exactly what this module needs.
- **Base controller**: `application/core/Dso_Controller.php` extends `CI_Controller` directly (NOT `MY_Controller`/`MX_Controller`), so the module has zero dependency on the legacy CMS's `global_lib`, `menu_lib`, options table, multi-language, etc. It only uses `session` and `database`, which are already autoloaded app-wide (`application/config/autoload.php`), plus `application/config/dso_roles.php` which it loads itself. Session keys used: `dso_user_id`, `dso_name`, `dso_role`, and (portal only) `dso_account_id`.
- **Role gate**: `Dso_Controller::require_role(array $roles)` compares `session->userdata('dso_role')` against an allow-list passed by each controller action; on mismatch it renders `views/dyafa/errors/403.php` with a 403 status and stops execution. `application/config/dso_roles.php` centralizes the full role list and the HOD-level role subset (`dso_hod_roles`).
- **Controller directory / routing**: CI3 natively supports controller sub-directories, so all module controllers live under `application/controllers/dyafa/` (class names capitalized, e.g. `Dashboard.php`, `Leads.php`) and CI3's router maps `dyafa/<controller>/<method>` to them automatically once the first URI segment (`dyafa`) resolves to that folder. To be safe and to guarantee correct match order, explicit routes were also added directly to `application/config/routes.php`, immediately after the existing line `$route['admin_ajax'] = 'admin_ajax/index';` and before the `/*end api */` comment - i.e. before the `if (!$is_admin) { ... }` legacy block and long before the catch-all `$route['(:any)/([a-zA-Z0-9_-]+)']` / `$route['([a-zA-Z0-9_-]+)']` routes at the very end of the file. CI3 matches routes top-to-bottom, so this placement guarantees the Dyafa routes always win. The exact lines inserted:
  ```
  $route['dyafa'] = 'dyafa/dashboard';
  $route['dyafa/login'] = 'dyafa/auth/login';
  $route['dyafa/logout'] = 'dyafa/auth/logout';
  $route['dyafa/portal'] = 'dyafa/portal/dashboard';
  $route['dyafa/(:any)'] = 'dyafa/$1';
  ```
  Re-reading the file after the edit confirmed these lines sit above both `if (!$is_admin) { ... }` blocks, including the catch-all at the very end.
- **Cron controller placement**: `application/controllers/Cron.php` is deliberately at the top level of `application/controllers/` (not inside `dyafa/`) because it is meant to be invoked directly via CLI (`php index.php cron dso_generate_notifications`) or a scheduled URL hit, without any session/auth context. It extends `CI_Controller` directly, not `Dso_Controller`.
- **Seed/demo data**: lives entirely in `dyafa_sales_os_schema.sql` at the repo root, alongside the `CREATE TABLE` statements for every `dso_*` table.
- **AI Lead Scoring**: implemented as a small, clearly-labeled heuristic class (`application/libraries/Dso_lead_scoring.php`) with a stable `score_lead($lead_data)` interface. The comment block at the top of the class states explicitly that it is a rule-based placeholder for a future real ML/AI integration.
- **PMS / Finance integrations (2026-07-22 update)**: both now run in one of three modes via `dso_pms_mode`/`dso_finance_mode` in `application/config/dso_integrations.php`. `live` keeps the original config-gated HTTP POST behavior. `mock` (the new default) calls `Dso_pms_mock`/`Dso_finance_mock` - deterministic, no-network classes that generate a realistic confirmation/ledger reference, room number, and status, persisted on `dso_reservations`/`dso_collections` via new columns (`dyafa_sales_os_migration_002_integration_mocks.sql`). `off` is the original log-only stub. This lets every downstream view/report already show believable PMS/Finance data before a real system is contracted; switching to a real system later is a one-line config change since the mock response shape matches what the live path expects.
- **Notification triggers (2026-07-22 backlog closure)**: `_notify_guest_complaints()`, `_notify_proposal_pending()`, `_notify_vip_arrival()` added to `Cron.php`, following the exact same query→de-dup-via-message-substring→insert pattern as the original 4 triggers. VIP arrival needed a genuinely new signal (`dso_accounts.is_vip`, migration `003`) since nothing in the schema previously flagged an account as VIP.
- **Reports + CSV export**: every report method follows load-model→build-`$data`→render-3-views; the only shared addition is `Reports::_maybe_export($filename, $rows)`, called before the view render in each action, which streams via `application/helpers/dso_csv_helper.php::dso_export_csv()` and calls `exit` when `?export=csv` is present - kept as a plain helper function (not a library) since it's a single stateless operation.
- **Property geo maps**: `lat`/`lng` (migration `004`) are populated by `Properties::_maybe_geocode()` only when left blank, via `Dso_maps_mock` (new `dso_maps_mode` in `dso_integrations.php`, same live/mock/off convention). The mock maps a property's `city` string to one of ~15 hardcoded Saudi city-center coordinates plus a small id-seeded offset - deliberately simple since no real geocoding provider is contracted. Leaflet is loaded via CDN `<script>`/`<link>` tags directly in `properties/form.php` (no local asset, no build step, consistent with the rest of this module's "no JS framework" approach).
- **Payment Gateway / Reporting Platform integrations**: `Dso_payment_mock.php`/`Dso_payment_integration.php` and `Dso_reporting_mock.php`/`Dso_reporting_integration.php` are structural copies of `Dso_finance_mock.php`/`Dso_finance_integration.php` - same `live`/`mock`/`off` config keys (`dso_payment_mode`, `dso_reporting_mode`), same `_attempt_http_post()` helper duplicated per integration class (matching the existing PMS/Finance pattern rather than introducing a new shared base class, to keep the change surface minimal and consistent with what was already there). Payment sync is invoked from `Collections::edit()` immediately after the existing Finance/ERP sync call. Reporting push is invoked from a new `Reports::push_to_reporting($report)` action (HOD-only) that maps a report key to its model/method pair and re-fetches the same rows CSV export would use.
- **Company User Management / corporate sub-roles**: `dso_users.role` ENUM extended (migration `006`, a `MODIFY COLUMN` re-declaring the full superset - MySQL has no clean way to add ENUM values conditionally the way the other migrations guard columns via `information_schema`, so this one is unconditionally safe-to-rerun instead) rather than introducing a separate `dso_portal_user_roles`/permissions table, since the BRD's 5 sub-roles are fixed and a flat capability map (`dso_corporate_capabilities` in `dso_roles.php`) is simpler than a full permission-matrix table for 5 known roles. The legacy flat `Corporate Client` role is kept (not migrated away) so the pre-existing seeded `corporate1` login keeps working unchanged - it's treated identically to `CorporateAdmin` in the capability map. `Dso_Controller::require_corporate_capability($capability)` mirrors `require_role()` but checks a capability instead of an exact role.
- **Portal search + invoice download**: `Portal::search()` deliberately does NOT introduce a new room-inventory/date-availability table - "availability" is scoped to mean "contractually allowed" (reusing `Dso_contracts_model::get_allowed_properties()`/`get_corporate_rates()`), consistent with the fact that `Dso_reservations_model::validate_against_contract()` never checked real per-date room inventory either. Invoice download reuses the already-vendored `Dompdf_lib` (no new PDF library added) via a small dedicated view (`portal/invoice_pdf.php`) rendered to a string first (`$this->load->view(..., true)`) then piped into `Dompdf_lib::write()`.
- **AI Lead Generation (2026-07-22 backlog closure)**: implemented as an explicitly-labeled SYNTHETIC PLACEHOLDER, not a real data-acquisition integration - see the class doc-block on `Dso_lead_generator.php`. A new seed table `dso_market_intelligence` (migration `007`, 8 seeded industry/city rows) replaces what would otherwise require a paid provider (Apollo/ZoomInfo-type API) or legally-reviewed scraping; `Cron::dso_generate_leads()` synthesizes candidates, de-dupes by `company_name`, and scores them through the unchanged `Dso_lead_scoring` heuristic, discarding anything that heuristically scores `Discard`.
- **AI Sales Assistant LLM-influenced fields**: `Dso_llm_client::suggest_property_and_priority()` is a separate method from `enhance_recommendation()` (not folded into it) so the free-text-only contract of the latter stays unchanged for existing callers. The new method never touches `estimated_revenue` - only `Dso_sales_assistant::_apply_llm_candidate_fields()` may apply its `property_name`/`priority` output, and only after validating the property name against `Dso_properties_model::get_all('Active')` and the priority against the `Low`/`Medium`/`High` enum; any invalid LLM suggestion is logged and silently dropped, keeping the heuristic's value.
- **Dynamic RBAC (this session, 2026-07-22)**: `Dso_Controller::require_role(array $roles)` is kept exactly as-is forever - all ~40 existing call sites across every controller still work unchanged. A parallel `require_permission($key)`/`has_permission($key)` pair was added instead of migrating those call sites, backed by new `dso_roles`/`dso_permissions`/`dso_role_permissions` tables (migration `008`) and a `dso_users.role_id` column added alongside (not replacing) the legacy `role` string enum. Session now also stores `dso_role_id`/`dso_team_id` on login (`Auth::authenticate()`). Only the new Administration module and its screens use `require_permission()` - this keeps the change additive and avoids re-testing 40 existing gated actions. `Dso_roles_model`/`Dso_permissions_model` follow the same no-base_model CRUD style as every other model in this app.
- **Teams + territory scoping (this session)**: `dso_teams`/`dso_team_properties`/`dso_team_accounts` (migration `008`) plus `dso_users.team_id`. `Dso_Controller::my_team_account_ids()`/`my_team_property_ids()` return `null` (meaning "no restriction") whenever the logged-in user has no team OR their team has zero explicit territory rows - this was a deliberate design choice so every pre-existing seeded user/team keeps seeing everything they saw before this feature existed, until an HOD explicitly assigns a territory via `Admin/Teams.php`. Wired as an optional parameter into `Dso_accounts_model::get_all()`, `Dso_contracts_model::get_filtered()`, and `Dso_reservations_model::get_all()` (which already accepted an `$account_id` param for Account 360 use and was extended to also accept an array for territory scoping). Leads were deliberately NOT territory-scoped since `dso_leads` has no `account_id` column - a lead only gains an account once won and converted to a contract/account, so team-based lead scoping isn't meaningful with the current schema.
- **Integrations admin UI (this session)**: `Admin/Integrations.php` reads `application/config/dso_integrations.php` via the normal CI config loader and, on save, regenerates the entire file with `var_export()`-ed values via `file_put_contents()` - deliberately no new DB table, per the approved plan, since the existing live/mock/off convention already lives in a config file and every integration class (`Dso_pms_mock`, `Dso_finance_mock`, etc.) already reads it via `config_item()`/`$this->config->item()`, so no downstream code needed to change.
- **Notification Center (this session)**: `Admin/NotificationCenter.php` is a read/broadcast UI over the existing `dso_notifications` table - no new table. Broadcast targets "all", a role (loops `Dso_users_model::all($role)`), or a single user, inserting one row per recipient the same way `Cron::dso_generate_notifications()` already does per-trigger.
- **AI Lead Generation group (this session)**: `dso_lead_scoring_config` table (migration `008`) lets `Dso_lead_scoring` read its weights from the DB with a fallback to its original hardcoded defaults when the table is empty - this was verified to keep scoring output byte-for-byte identical on a fresh/unmigrated database, since the fallback path reduces algebraically to the exact prior constants. Note: the migration's seeded weights (40/30/15/15) are a *reasonable starting point*, not a byte-identical mirror of the pre-existing formula's internal component maxes (40/25/20/15) - editing a weight via the new `LeadScoringConfig` screen is expected to shift scores, which is the point of exposing it as a config screen. `Dso_lead_generator::generate()` was extracted from `Cron::dso_generate_leads()` (which is now a thin wrapper, mirroring how `dso_generate_ai_recommendations()` already delegates to `Dso_sales_assistant`) so the new `LeadGeneration` controller can trigger the same synthetic-lead logic synchronously from the UI instead of only via CLI/cron.
- **Reservation Calendar / Opportunities Board (this session)**: both are server-rendered-initial-load + AJAX-only-for-mutation, deliberately not a full SPA/library-driven calendar or kanban, consistent with this app having zero existing JS framework dependency. Drag-drop uses native HTML5 DnD attributes and `fetch()`, matching the one existing JSON-response convention in the app (`AiConfig::test()`'s `set_content_type('application/json')->set_output(json_encode(...))`). Calendar drag-drop reuses `Dso_reservations_model::validate_against_contract()` - the exact same call `Reservations::edit()` makes - so a dragged reservation can never bypass contract/credit validation.
- **Availability Settings (this session)**: deliberately scoped as a simple `is_bookable` flag + blackout-dates list (new `dso_property_blackout_dates` table, migration `010`), NOT a full date-by-date room-inventory calendar - this app's existing "availability" concept has always meant contract-eligibility (`dso_contracts.allowed_properties`), not per-night room inventory (see the Portal search note above), and building real inventory tracking would be a much larger, separate feature than this BRD sidebar item warranted.
- **Admin panel link**: the legacy CMS admin panel (`/admin`, its own separate login) is linked from the Dyafa sidebar footer (`dyafa/layout/header.php`), visible only to `dso_hod_roles`. As of this session it is deliberately styled with *lower* visual weight than the primary menu (`.dso-nav-legacy-link`/`.dso-nav-legacy-divider` - dimmed opacity, a dashed "Separate legacy system" divider, an external-link icon) rather than the same classes as the rest of the sidebar, per the presentation-audit finding that it previously read as a first-class part of this app. The admin panel's *own* internal theme was intentionally left untouched - restyling the entire legacy CMS admin UI to the Dyafa palette remains a much larger, separate undertaking outside this scope.

## Session: Audit Trail, Security Hardening & Presentation Pass (2026-07-23)

Closes every P0/P1 item confirmed against live code in `enhance.md`/`todolist.md`
(the "Leads tab 404" item in both docs was itself stale - re-verified fixed
by an earlier session, not re-fixed here).

- **Soft delete + shared audit trail**: rather than adding a bespoke
  `is_deleted`/history mechanism per entity, one additive `deleted_at
  DATETIME NULL` column (migration `012`, `information_schema`-guarded like
  migrations 002-011) was added to Contracts, Accounts, Adhoc Sales,
  Properties, Collections, Targets, Roles, and Teams - Leads already had its
  own `is_deleted TINYINT` from a prior session and was left as-is rather
  than migrated, to avoid touching a working column for no functional gain.
  Every model's `delete()` now does `UPDATE ... SET deleted_at = NOW()`
  instead of a hard `DELETE`, and every `get()`/`get_all()`/aggregate query
  on those tables gained a `WHERE deleted_at IS NULL` filter (including the
  raw-SQL aggregate queries in `Dso_accounts_model::performance()`,
  `Dso_properties_model::performance()`, `Dso_collections_model::aging_buckets()`/
  `credit_limit_report()`, `Dso_teams_model::performance()`, etc. - every
  join/subquery touching a soft-deletable table was checked, not just the
  top-level `get_all()`). `Dso_Controller::soft_delete_row($model,
  $table_name, $id)` fetches the pre-delete row, calls the model's
  (now-soft) `delete()`, and audits it in one call - this is the one-line
  helper every controller's `delete()` action now calls instead of calling
  the model directly. `Dso_Controller::audit($table_name, $action, $row_id,
  $before, $after)` writes to a new shared `dso_audit_log` table
  (`Dso_audit_log_model::record()`) and is also called explicitly from every
  `add()`/`edit()` action on those 8 entities (not just `delete()`) - a
  logging failure is caught and only logged via `log_message('error', ...)`,
  never allowed to block the underlying CRUD action. Viewable (read-only, no
  add/edit/delete of its own) at **Administration > Audit Log**
  (`Admin/AuditLog.php`, new `view_audit_log` permission granted to
  HOD Sales/Management, same pattern as `manage_roles`).
- **Encrypted integration credentials**: `Admin/Integrations.php` previously
  `var_export()`-ed the PMS/Finance/Maps/Payment/Reporting API key as
  plaintext directly into `application/config/dso_integrations.php` on disk
  - a real exposure risk via git history/backups/shared hosting. A new
  `dso_integration_credentials` table + `Dso_integration_credentials_model`
  mirror `Dso_ai_providers_model`'s existing encrypt/decrypt boundary
  exactly (`get_key($integration_key)` is the only plaintext-producing
  method, called only by the 4 live-mode HTTP call sites immediately before
  the request - `Dso_reporting_integration::push()`,
  `Dso_finance_integration::sync_invoice()`,
  `Dso_payment_integration::sync_payment()`,
  `Dso_reservations_model::create_pms_reservation()`). `mode`/`endpoint`/
  `timeout` deliberately stay in the config file (non-secret, and changing
  them via `var_export()` rewrite is unchanged behavior) - only the API key
  moved to encrypted DB storage. The Integrations form now shows
  `key_last4` with a "leave blank to keep it" placeholder instead of the
  previous plaintext `<input value="...">`.
- **Mandatory 2FA for CorporateFinance**: BRD Section 10 lists portal 2FA as
  "(Optional)", but `CorporateFinance` specifically sees invoices/credit
  limits/outstanding balances (Section 10/16), so it was made mandatory for
  that one sub-role rather than left optional-for-everyone (which in
  practice meant no-2FA-for-anyone, since nothing implemented it before this
  session). A pure-PHP RFC 6238 (TOTP)/RFC 4226 (HOTP) library
  (`Dso_totp.php`) was hand-rolled rather than adding a Composer dependency
  to a codebase with no package manager - it implements Base32 encode/decode
  and the HMAC-SHA1 counter algorithm directly (~100 lines), matching the
  existing convention of small vendored libraries (see `Dso_llm_client`'s
  adapter classes). No QR code image is generated - hand-rolling a QR
  encoder correctly is materially riskier/larger than the rest of this
  library, so enrollment shows the raw secret + `otpauth://` URI as text for
  manual entry, which every authenticator app already supports as a
  fallback path. `Portal::authenticate()` now branches on
  `$user->role === 'CorporateFinance'`: it stores a *pending* (not yet
  fully authenticated) `dso_pending_2fa_user_id` session key and redirects
  to `setup_2fa()` (first login, generates+session-holds a secret until a
  submitted code proves it was actually added to an app - nothing persists
  to `dso_users` until that verification succeeds) or `verify_2fa()`
  (every login after enrollment). Both new methods are added to
  `Dso_Controller::$public_actions` (bypassing the "must be logged in"
  check, since the user isn't fully logged in yet at this stage) and to
  `Portal`'s own constructor whitelist (bypassing `require_role()` for the
  same reason). `_complete_login($user)` is the single method that sets the
  real session, called from all three paths (no-2FA-required, post-setup,
  post-verify) so there's exactly one place session keys are set.
- **Pagination**: `Dso_Controller::paginate($base_url, $total_rows,
  $per_page)` wraps CI's native `Pagination` library (already vendored in
  `system/libraries/`, no new dependency) rather than hand-rolling
  LIMIT/OFFSET math - configured with custom tag wrappers
  (`dso-page-num`/`dso-page-current`/`dso-page-nav`) so the rendered links
  can be styled to match the existing design system. Applied to
  Leads/Reservations/Notifications (`Dso_Controller`'s doc-comment already
  named these three as the highest-growth-risk lists) via a shared
  refactor pattern: each model's filter-building logic was extracted into a
  protected `_apply_filters()`/`_apply_user_filter()` method so `get_all()`
  and a new `count_all()` (or `count_for_user()`) can't drift out of sync -
  the count always reflects exactly the same WHERE clause as the paged
  rows. Discovered mid-implementation that **every** `.dso-table` already
  gets client-side DataTables pagination (10 rows/page) via a global script
  in `layout/footer.php` - stacking real server pagination under that would
  page an already-limited 25-row slice into confusing sub-pages, so those 3
  tables gained a `data-server-paginated="1"` marker attribute and the
  footer script's selector was updated to skip marked tables, rather than
  removing DataTables globally or duplicating pagination UI.
- **Presentation pass**: favicon is an inline SVG `data:` URI (rect + "DS"
  text matching the sidebar brand mark) rather than a binary `.ico`/`.png`
  asset - no image-generation tooling available, and modern browsers
  support SVG favicons natively. Login/2FA screens moved to a new dedicated
  `dyafa/layout/guest_header.php`/`guest_footer.php` pair (full standalone
  `<head>`, a copy of the same 5-hex palette scoped to just what a guest
  screen needs) instead of the full app shell, which previously rendered a
  mostly-empty sidebar/topbar for every unauthenticated view (`$dso_logged_in`
  false suppresses the nav links/topbar user info, but the shell markup/CSS
  still loaded). Chart.js is loaded via CDN `<script>` only on the two
  dashboard views that use it (not globally), consistent with this app's
  "no JS framework, no build step" approach already established for
  Leaflet/DataTables - a new `Dso_reservations_model::daily_revenue_trend($month,
  $user_id = null)` (day-of-month => revenue) backs the MTD trend chart on
  both Daily and HOD dashboards. The ~15 remaining raw-hex inline styles
  (audited via `grep -r 'style="[^"]*#[0-9a-fA-F]'` across every `dyafa`
  view) were replaced with the existing CSS variables; two new badge
  modifier classes (`.dso-badge.danger`/`.dso-badge.warning`, the latter
  backed by new `--color-warning`/`--color-warning-bg` variables *derived*
  via `color-mix()` from the existing 5 hex values, not a 6th raw hex) let
  priority/credit-limit badges use semantic classes instead of inline
  per-value color logic.

## Local Run Steps

1. Import the schema + seed data into the existing database:
   ```
   mysql -u root ci_realestate_web < dyafa_sales_os_schema.sql
   ```
   (No password is required, matching `application/config/database.php`'s `root` / `''` credentials.)
2. `application/config/database.php` requires **no changes** - it already targets `hostname=localhost`, `username=root`, `password=''`, `database=ci_realestate_web`, `dbdriver=mysqli`, `dbprefix=''`.
3. `application/config/config.php` (lines 71-74) computes `base_url` dynamically from `$_SERVER['HTTPS']`, `$_SERVER['HTTP_HOST']`, and `$_SERVER['SCRIPT_NAME']` - it is not hardcoded. No changes were made or needed for this module; `base_url()` helper calls throughout the new views rely on this existing dynamic resolution.
4. Visit `http://<your-host>/dyafa/login` for staff login, or `http://<your-host>/dyafa/portal/login` for the corporate client portal.
5. To run the notification generator manually: `php index.php cron dso_generate_notifications` from the `ci_realestate_web` directory, or hit the equivalent URL if CLI access is unavailable.
6. To run the AI Sales Assistant recommendation generator manually: `php index.php cron dso_generate_ai_recommendations`.
7. If your database was created before 2026-07-22, run the numbered migrations in order (fresh imports of `dyafa_sales_os_schema.sql` already include all of them):
   ```
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_002_integration_mocks.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_003_vip_notifications.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_004_property_geo.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_005_payment_mock.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_006_corporate_subroles.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_007_ai_lead_gen.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_008_admin_rbac_teams.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_009_activities_generic.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_010_property_availability.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_011_bulk_seed_data.sql
   mysql -u root ci_realestate_web < dyafa_sales_os_migration_012_audit_soft_delete_security.sql
   ```
8. To run the AI Lead Generation synthetic generator manually: `php index.php cron dso_generate_leads` (or via the UI: `dyafa/leadgeneration`).
9. Migration `008` seeds two example Teams (Team Riyadh / Team Coastal) with territory assignments and a full role/permission matrix matching the pre-existing hardcoded role behavior - log in as `anas` (HOD Sales) to see the new Administration menu.
10. Migration `012` is additive/idempotent (same `information_schema`-guard convention as 002-011) - adds `deleted_at` to 8 tables, `dso_audit_log`, `dso_integration_credentials`, and `dso_users.totp_secret_encrypted`/`totp_enabled`. No seeded CorporateFinance user exists by default, so the mandatory-2FA enrollment flow is only exercised once a company user is created with that sub-role via `dyafa/portal/user_add`.

## Default Login Credentials (seeded)

| Name | Username | Password | Role |
|---|---|---|---|
| Anas | anas | Passw0rd! | HOD Sales |
| Nidal | nidal | Passw0rd! | Sales Manager |
| Ahmad Saleh | ahmad.saleh | Passw0rd! | Sales Executive |
| Ali | ali | Passw0rd! | Sales Coordinator |
| Corporate Client One | corporate1 | Client123! | Corporate Client (account_id = 1, Acme Corp) - use `/dyafa/portal/login` |

Password hashes in the seed SQL are real bcrypt hashes generated via:
```
php -r "echo password_hash('Passw0rd!', PASSWORD_DEFAULT);"
php -r "echo password_hash('Client123!', PASSWORD_DEFAULT);"
```

## What's Real vs What's a Stub

| Area | Status | Detail |
|---|---|---|
| Auth, roles, session gate | Real | Full login/logout, bcrypt verify, per-action role checks |
| Leads CRUD + assignment | Real | Full form-validated CRUD, HOD-only reassignment |
| Lead scoring | Heuristic (documented stub for future AI/ML) | Deterministic formula, not machine learning |
| Contracts CRUD + funnel | Real | Real SQL group-by for funnel counts |
| Accounts + Activities | Real | CRUD + activity log, inline add-activity form |
| Corporate Portal | Real | Account-scoped queries, server-side ownership checks on cancel |
| Reservations | Real | Contract allowed-properties + credit-limit validation via real SQL |
| PMS integration | Real plumbing + realistic mock (default) | `dso_pms_mode`: `live` = config-gated HTTP POST via `dso_pms_endpoint`; `mock` (default) = `Dso_pms_mock` generates confirmation/room/status persisted on `dso_reservations`; `off` = original log-only stub |
| Adhoc Sales | Real | Full CRUD; optional `venue_property_id` link to `dso_properties` |
| Sales Targets + performance | Real | Actuals computed live via SQL joins/aggregates, no stored actual columns |
| Collections + aging | Real | Manual payment entry, real DATEDIFF-bucketed aging query |
| Payment gateway sync | Real plumbing + realistic mock (default) | `dso_payment_mode`: same `live`/`mock`/`off` pattern as PMS/Finance via `Dso_payment_mock`; persists a gateway reference on `dso_collections`; invoked from `Collections::edit()` alongside Finance/ERP sync. Manual payment amount entry itself remains manual (no gateway webhook receives live payments) |
| Finance/ERP sync | Real plumbing + realistic mock (default) | `dso_finance_mode`: same `live`/`mock`/`off` pattern as PMS via `Dso_finance_mock`; persists a ledger reference on `dso_collections`; invoked from `Collections::edit()` on Paid/PartiallyPaid |
| Maps Services | Real plumbing + realistic mock (default) | `dso_maps_mode`: `Dso_maps_mock` geocodes a property's city to lat/lng, persisted on `dso_properties`; rendered as an embedded Leaflet map |
| Reporting Platform | Real plumbing + realistic mock (default) | `dso_reporting_mode`: `Dso_reporting_mock` simulates pushing report rows to an external BI tool; invoked via `Reports::push_to_reporting()` |
| Dashboards (daily + HOD) | Real | All real SQL aggregates, role-gated HOD view |
| Notifications generator | Real | Real SQL across leads/collections/contracts/targets/activities/accounts with de-dup, now including guest-complaint/proposal-pending/VIP-arrival triggers; delivery channel (email/SMS/push) intentionally not implemented (documented) |
| Reports | Real | 14 reports (daily_sales/revenue/aging/leads/reservations/room_nights/contracts/contract_renewals/opportunities/adhoc_sales/activities/corporate_accounts/property_performance/ai_recommendations), all real SQL, HTML tables, CSV export via `dso_export_csv()` |
| AI Sales Assistant | Heuristic core, real optional LLM enhancement (documented) | `Dso_sales_assistant.php`; structured fields always heuristic except `suggested_property_id`/`priority`, which can be LLM-influenced via `Dso_llm_client::suggest_property_and_priority()` but only after validation against real active properties / the priority enum |
| AI Lead Generation | Synthetic placeholder (documented) | `Dso_lead_generator.php` synthesizes candidates from seeded `dso_market_intelligence`, scored via the real `Dso_lead_scoring` heuristic; not a real data-acquisition provider |
| Company User Management | Real | 5 corporate sub-roles + capability map (`dso_corporate_capabilities`), `Portal::users()`/`user_add()`/`user_toggle_status()`, account-scoped |
| Corporate Portal search + invoice download | Real | `Portal::search()` (contract-allowed properties + rates), `Portal::invoice_download()` (Dompdf PDF, ownership-checked) |
| Property Management module | Real | `dso_properties`/`dso_property_rates` master tables, Coordinator-only CRUD + file upload, now with `lat`/`lng` geo maps; reservation/contract forms pick from it but keep submitting plain name strings, so existing string-matching validation is unchanged |
| Soft delete + audit trail | Real | `deleted_at` on Contracts/Accounts/Adhoc Sales/Properties/Collections/Targets/Roles/Teams; shared `dso_audit_log` written from every `add()`/`edit()`/`delete()`; viewer at Administration > Audit Log |
| Integration API key storage | Real (encrypted) | `dso_integration_credentials` + `Dso_integration_credentials_model`, same encrypt/decrypt boundary as `dso_ai_providers`; `mode`/`endpoint`/`timeout` stay in the config file |
| Corporate Portal 2FA | Real, mandatory for CorporateFinance only | Dependency-free RFC 6238 TOTP (`Dso_totp.php`); other sub-roles unaffected |
| Pagination (Leads/Reservations/Notifications) | Real | CI native Pagination library via `Dso_Controller::paginate()`, real `LIMIT`/`OFFSET` on the model queries |
| AI Sales Assistant Predictions / Next Best Actions | Real (as of 2026-07-23) | `find_churn_risk_accounts()` (booking-cadence-based) / `find_accounts_needing_next_action()` (activity-recency-based) - previously views-only with no generator |

## Files Touched Outside the New Module

`application/config/routes.php`: the Dyafa route block itself remains append-only/unchanged from the description above. As of 2026-07-23, the 3 pre-existing dead `getProperty`/`getAllProperties` legacy lines (pointing at a non-existent `Api` controller, confirmed via grep with no external caller) were deleted - the first and only removal of pre-existing route lines in this file's history for this module's work. `application/views/dyafa/layout/header.php` and `application/views/dyafa/layout/footer.php` have grown beyond the original single sidebar link: `header.php` now also carries the favicon `<link>`, dashboard-chart/pagination/badge CSS, and de-emphasized styling for the legacy CMS link (see the 2026-07-23 session notes above); `footer.php`'s DataTables auto-init selector gained a `:not([data-server-paginated])` exclusion. The legacy admin panel's own internal templates/theme remain untouched. `application/config/database.php` was **not** touched - it already met all requirements.
