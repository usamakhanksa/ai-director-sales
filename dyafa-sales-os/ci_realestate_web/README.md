# Dyafa Sales OS

Hospitality Sales Management Platform for Dyafa Hospitality Services Company — Corporate Sales CRM, Contract Management, Corporate Self-Service Portal, Reservations, Adhoc Sales, Targets, Collections, Property Management, AI Sales Assistant, Dashboards, Reports and Notifications.

This module is built on top of an existing legacy CodeIgniter 3 (HMVC/wiredesignz) real-estate CMS codebase, sharing its database and framework but living entirely in its own `dso_*` tables and `application/*/dyafa/` folders. See `implementation.md` for architecture decisions and `tasklist.md` for a build-status checklist.

## 1. Features

### Auth & Roles
Session-based login for 8 staff roles (HOD Sales, Sales Manager, Sales Executive, Sales Coordinator, Reservation Team, Finance Team, Management, Corporate Client) plus 5 Corporate Portal sub-roles (CorporateAdmin, CorporateHR, CorporateFinance, CorporateTravelCoordinator, CorporateProjectManager — see Company User Management below). Per-action role gate (`Dso_Controller::require_role()`) for staff, per-capability gate (`Dso_Controller::require_corporate_capability()`) for portal sub-roles. Separate login flow for Corporate Clients at the self-service portal.

### Lead Management
Full CRUD, auto-assignment of new leads to HOD Sales, HOD-only reassignment. Deterministic lead-scoring heuristic (revenue / room-nights / priority / source weighted formula → Hot / High / Medium / Low / Discard + suggested next action) — a documented placeholder for a future real AI/ML scoring service.

### AI Lead Generation
`Cron::dso_generate_leads()` + `application/libraries/Dso_lead_generator.php` synthesize candidate leads from seeded `dso_market_intelligence` signals (industry/city/company-size/avg revenue/avg room nights/signal strength), scored through the existing lead-scoring heuristic and inserted with `source = 'AI Generated'`, de-duplicated by company name. **This is explicitly synthetic placeholder data** — sourcing 500 genuinely real qualified leads (per the BRD's target) requires a paid data provider (Apollo/ZoomInfo-type API) or legally-reviewed scraping, a business/budget decision not yet made. Run manually via `php index.php cron dso_generate_leads`.

### Company User Management
Each corporate account can have multiple portal logins across 5 sub-roles (Administrator/HR/Finance/Travel Coordinator/Project Manager), all scoped to the same `account_id`. Capabilities per sub-role (create/modify/cancel reservation, view statement, manage users) are centralized in `dso_corporate_capabilities` (`application/config/dso_roles.php`) and enforced via `Dso_Controller::require_corporate_capability()`. CorporateAdmin manages users at **Company Users** (`/dyafa/portal/users`) inside the portal — including editing an existing user's name/email/sub-role or resetting their password (`Portal::user_edit()`), not just deactivate-and-recreate.

### Contract Management
Full CRUD, funnel view grouped by status, allowed-properties + corporate-rates per contract, credit limit/days/payment terms, expiry tracking.

### Corporate Accounts & Activities
Full CRUD on corporate accounts; per-account activity log (Call / Meeting / Visit / FollowUp / Reservation / Collection / Complaint / Opportunity).

### Property Management
Master property list (`dso_properties` + `dso_property_rates`) managed by the **Sales Coordinator** role: add/edit/delete properties, upload a map/photo and an info document, maintain a per-property standard rate list. All other roles get read-only access. Reservation and contract forms pick properties from this master list via dropdown/checkboxes, while still storing plain property-name strings — so the existing allowed-properties matching logic is untouched.

Real geo maps: `lat`/`lng` columns on `dso_properties`, auto-populated via `Dso_maps_mock` (`dso_maps_mode` in `application/config/dso_integrations.php`, same live/mock/off convention as PMS/Finance) when left blank on the form — a deterministic city-based geocode, no real geocoding provider contracted yet. Rendered as an embedded Leaflet map on the property form and an OpenStreetMap link on the list.

### Reservation Management
CRUD with real validation: allowed-properties check against the account's linked contract, and credit-limit check against outstanding collections + the new reservation amount. Property selection is now a dropdown sourced from the Property Management module.

### Adhoc Sales Management
Full CRUD for Wedding / Birthday / MeetingRoom / Event / Catering / Conference / Retreat / GroupBooking / CoffeeBreak, with an optional venue/property link and a funnel-style status set (Inquiry → ProposalSent → Negotiation → Confirmed → Completed, plus Cancelled/Lost).

### Sales Targets & Performance
Monthly targets across revenue, room nights, reservations, collections, adhoc revenue, meetings, visits, calls, new leads, new contracts. Actuals are computed live via SQL aggregates (never stored), with %-achievement banding (Outstanding / Excellent / Good / Average / Needs Attention).

### Collections Management
Manual payment recording with auto status derivation (Pending / PartiallyPaid / Paid / Overdue), aging report bucketed 0-30 / 31-60 / 61-90 / 90+ days via real SQL. Recording a Paid/PartiallyPaid payment now also fires the Finance/ERP sync **and** the Payment Gateway sync extension points (see Integrations below).

### Corporate Self-Service Portal
Login for Corporate Client / corporate sub-role users, all data scoped to their own `account_id`: **Search Hotels** (availability against the account's contract-allowed properties and corporate/standard rates), booking, view/cancel reservations (capability-gated per sub-role), download an HTML statement or a per-invoice PDF, view outstanding balance, credit limit and contract expiry.

### Dashboards
- **Daily Sales Dashboard** — today's/MTD revenue, reservations, activities, new leads, personal target achievement, for the logged-in user.
- **HOD Sales Dashboard** — team ranking by revenue/achievement, lead conversion rate per owner, top 5 accounts by revenue, outstanding collections, contracts expiring within 30 days. Restricted to HOD-level roles.

### AI Sales Assistant
A deterministic, documented rule-based heuristic (`application/libraries/Dso_sales_assistant.php`) computes every structured field of a recommendation — never trusted to an LLM. Detects:
- Accounts with no reservation in 45+ days → recommends a meeting + long-stay package, with a suggested property, an estimated revenue figure (trailing average of past bookings, or a documented fallback), and a priority level.
- Contracts expiring within 30 days (reusing the existing contract-expiry query) → recommends starting renewal discussions.

**Optional real LLM enhancement.** The heuristic's own free-text `suggested_action`/`reason` draft can be refined by a genuinely connected LLM, configured at **AI Config** (`/dyafa/aiconfig`, HOD-only). Supported providers: OpenAI, Anthropic (Claude), Google Gemini, Groq, OpenRouter, Ollama (local), Mistral, DeepSeek, xAI (Grok), Azure OpenAI, and Cohere — add one, pick a model, optionally set temperature/max_tokens/advanced JSON params, "Test Connection", then "Set Default". Only the single default, enabled provider is ever called.

Architecture: **heuristic gathers, LLM enhances, always falls back safely.** `account_id`, `type`, and `estimated_revenue` are always the heuristic's own values — the LLM never invents a revenue figure. `suggested_action`/`reason` are candidates for LLM enhancement via `enhance_recommendation()`, and the heuristic's own text is what's sent to the model as the baseline draft to refine. **`suggested_property_id`/`priority` can also be LLM-influenced** via the separate `Dso_llm_client::suggest_property_and_priority()` call, but only ever applied after `Dso_sales_assistant::_apply_llm_candidate_fields()` validates the property against the real active property list and the priority against the Low/Medium/High enum — any mismatch is logged and the heuristic's own value is kept untouched. If no provider is configured, the default provider is disabled, the call times out (8s), or anything errors — the original heuristic values are used untouched and the failure is only visible in the PHP error log. Cron and the manual generator have no awareness an LLM was even attempted.

API keys are encrypted at rest in `dso_ai_providers.api_key_encrypted` using CI's Encryption library (never stored or displayed in plaintext — only a `key_last4` mask is ever shown in the UI). **Required manual setup step:** set a strong, random `$config['encryption_key']` in `application/config/config.php` before saving any provider — see the `TODO` comment at that line; nothing can be encrypted/decrypted with a blank key.

Recommendations are generated and stored (not computed live) either by the scheduled job `Cron::dso_generate_ai_recommendations()` or by clicking **Generate Now** on the AI Assistant page (HOD-only) — both call the exact same `Dso_sales_assistant::generate_all_recommendations()`, with de-duplication, and are reviewed/actioned/dismissed at **AI Assistant** in the nav.

Two further detection passes generate the **Predictions** and **Next Best Actions** sub-views (previously empty-state-only views with no generator):
- **Predictions** — `find_churn_risk_accounts()`: accounts with 2+ past reservations that are now overdue for their next stay relative to their own historical average gap between checkouts (a real prediction computed from the account's own booking history, not a fabricated number; accounts with fewer than 2 reservations are skipped rather than assigned a fake cadence).
- **Next Best Actions** — `find_accounts_needing_next_action()`: active accounts with no logged sales activity (call/meeting/visit/follow-up) in 21+ days — a shorter, activity-based signal distinct from the 45-day reservation-based `InactiveAccount` detector, so the two don't just duplicate each other.

### Notifications
Real SQL-driven generator (`Cron::dso_generate_notifications()`) for new leads, payment-pending, contract-expiring, target-achieved, guest complaints (from `dso_activities.activity_type = 'Complaint'`), proposal-pending-approval (leads/adhoc sales at `ProposalSent`), and VIP guest arrivals (`dso_accounts.is_vip` + a reservation checking in today), with de-duplication. In-app only — email/SMS/push delivery is intentionally out of scope.

### Reports
14 reports covering Daily Sales, Revenue, Collections Aging, Leads, Reservations, Room Nights, Contract, Contract Renewal, Opportunities, Adhoc Sales, Activities, Corporate Accounts, Property Performance, and AI Recommendation — all real SQL, HTML output, each with a CSV export (`?export=csv`) via the shared `dso_export_csv()` helper. All 14 also offer **Push to Reporting Platform** (HOD-only, see Integrations below) — previously only 7 were wired.

### PMS, Finance/ERP, Payment Gateway & Reporting Platform Integrations
Config-gated real HTTP integration points (`application/config/dso_integrations.php`, ships blank/disabled, `live`/`mock`/`off` per integration):
- **PMS** — `Dso_reservations_model::create_pms_reservation()` attempts a POST to `dso_pms_endpoint` when configured; falls back to a local-only stub (log + in-app notification) when blank or on any failure. Fires automatically on every reservation insert.
- **Finance/ERP** — `Dso_finance_integration::sync_invoice()` follows the same pattern against `dso_finance_endpoint`, invoked from Collections whenever a payment update results in Paid/PartiallyPaid status.
- **Payment Gateway** — `Dso_payment_integration::sync_payment()` follows the same pattern against `dso_payment_endpoint`, invoked alongside Finance/ERP sync; persists `dso_collections.payment_reference`/`payment_synced_at`.
- **Reporting Platform** — `Dso_reporting_integration::push()` follows the same pattern against `dso_reporting_endpoint`; invoked from **Push to Reporting Platform** on supported reports.
- **Maps Services** — see Property Management above (`Dso_maps_mock`, `dso_maps_mode`).

None of these fabricate a fake external response — with no endpoint configured (the shipped default), behavior is identical to the original documented stubs, just with realistic mock reference data attached.

### Administration (Users & Roles, Teams, Integrations, Notification Center, Audit Log)
**Users & Roles** (`/dyafa/admin/users`, `/dyafa/admin/roles`) is real dynamic RBAC — `dso_roles`/`dso_permissions`/`dso_role_permissions` tables, editable per-role permission checkboxes, driven by a new `Dso_Controller::require_permission()` gate that sits *alongside* (not replacing) the original `require_role()` used everywhere else — every pre-existing role check keeps working unchanged. **Teams** (`/dyafa/admin/teams`) group users under an HOD/lead and can optionally be assigned a territory (specific properties/accounts); when a team has an assigned territory, its members' Leads/Contracts/Accounts/Reservations lists are automatically scoped to it — a team with no territory assigned sees everything, exactly as before this feature existed. **Integrations** (`/dyafa/admin/integrations`) is a form over the existing `application/config/dso_integrations.php` for the non-secret `mode`/`endpoint`/`timeout` settings; the API key itself is encrypted at rest in a new `dso_integration_credentials` table (same boundary as the AI provider keys below) — only a `key_last4` mask is ever shown, never the plaintext key. **Notification Center** (`/dyafa/admin/notificationcenter`) is an admin view + broadcast tool over the existing `dso_notifications` table. **Audit Log** (`/dyafa/admin/auditlog`) is a read-only viewer over `dso_audit_log` (see Data Integrity & Audit Trail below).

### Data Integrity & Audit Trail
Contracts, Corporate Accounts, Adhoc Sales, Properties, Collections, Targets, Roles, and Teams (the financially/legally significant entities — Leads already had this) now soft-delete via a `deleted_at` column instead of a hard SQL `DELETE`; nothing is ever un-recoverably lost through the UI. Every `add()`/`edit()`/`delete()` on those same entities also writes a row to a new shared `dso_audit_log` table (`user_id`, `table_name`, `row_id`, `action`, `before_json`, `after_json`, `created_at`) via `Dso_Controller::audit()`/`soft_delete_row()`, viewable at **Administration > Audit Log**.

### Security hardening
- **Encrypted integration credentials** — see Administration above.
- **Mandatory 2FA for Corporate Finance** — BRD Section 10 lists portal 2FA as "(Optional)", but the `CorporateFinance` corporate sub-role specifically sees invoices/credit limits/outstanding balances, so 2FA is now mandatory (not optional) for that one sub-role. First login walks the user through enrollment (`/dyafa/portal/setup_2fa` — shows a secret + `otpauth://` URI for any authenticator app, no QR image library added); every login after that requires a 6-digit TOTP code (`/dyafa/portal/verify_2fa`). Implemented as a small dependency-free RFC 6238 library (`application/libraries/Dso_totp.php`) — no external package. Other corporate sub-roles are unaffected.

### Everything else closed this session
- **Dashboard**: My Performance, Team Performance.
- **Leads**: My Leads / Unassigned Leads / AI Generated Leads scopes, Lead Sources breakdown.
- **AI Lead Generation** (new top-level menu group): Lead Scoring Config (HOD-tunable weights, `/dyafa/leadscoringconfig`), Generate Leads (`/dyafa/leadgeneration`, same engine as the cron job, triggered synchronously from the UI).
- **Contracts**: Active / Pending Approval / Expiring Soon filters.
- **Corporate Accounts**: Account 360° View (contracts + reservations + collections + activities on one page), Performance.
- **Reservations**: Pending / Today's Check-ins / Today's Check-outs filters, plus a drag-drop Reservation Calendar (`/dyafa/reservations/calendar`) that reuses the same contract-validation path as the normal edit form.
- **Adhoc Sales**: drag-drop Opportunities Board (`/dyafa/adhoc/board`), Proposals / Events filters.
- **Activities** (new top-level module, `/dyafa/activities`): My/Team Activities, generic Log Activity (previously only possible from inside an Account).
- **Collections**: Credit Limits, Invoices, Statements (staff-side).
- **Property Management**: Availability Settings (bookable flag + blackout dates — see note below on why this isn't a full room-inventory calendar).
- **AI Sales Assistant**: Predictions / Next Best Actions views and a real Analytics aggregate — see the two new detection passes described above.

**Note on "Availability Settings"**: this app's `dso_contracts.allowed_properties` already governs which properties a corporate account may book (contract-eligibility). There has never been a date-by-date room-inventory table, so Availability Settings is scoped to a simple bookable flag + blackout dates per property, not a full per-night calendar — building true room inventory would be a materially larger feature.

### Presentation, performance & data-integrity pass (2026-07-23)
- **Soft delete + audit trail** and **Administration > Audit Log** — see Data Integrity & Audit Trail above.
- **Encrypted integration credentials** and **mandatory Corporate Finance 2FA** — see Security hardening above.
- **Pagination** — `Dso_Controller::paginate()` (CI native Pagination library) added real `LIMIT`/`OFFSET` server-side pagination to Leads, Reservations, and Notifications (the three most likely to grow unbounded) — previously every list query fetched all matching rows regardless of the client-side DataTables paging already wrapping every `.dso-table`.
- **Dead routes removed** — the two `getProperty`/`getAllProperties` routes in `routes.php` pointed at an `Api` controller that never existed anywhere in the codebase; confirmed no external caller and deleted.
- **Branded first impression** — a favicon (inline SVG data URI, no binary asset), and a dedicated minimal guest layout (`dyafa/layout/guest_header.php`/`guest_footer.php`) for login/2FA screens instead of rendering them inside the full authenticated-app shell with an empty sidebar/topbar.
- **Dashboard charts** — Chart.js (CDN) added to the Daily Sales and HOD Sales dashboards: an MTD revenue trend line chart on both, a target-achievement gauge on Daily, and a top-5-accounts bar chart on HOD. Previously stat tiles/tables only.
- **Design-system consistency** — the ~15 remaining raw hex colors scattered across view files (bypassing the CSS-variable palette in `header.php`) were replaced with the existing variables/utility classes (`.dso-badge.danger`/`.dso-badge.warning` added for priority/limit badges).
- **Legacy CMS link de-emphasized** — the sidebar link to the separate legacy CMS admin panel now renders with reduced visual weight (dimmed, dashed divider, external-link icon) so it reads as a clearly separate system rather than a first-class Dyafa Sales OS menu item, pending the standalone-vs-retire-vs-migrate decision below.
- **Branded invoice PDF** — `portal/invoice_pdf.php` (BRD "Download Invoices") now has a proper header bar with the Dyafa logo mark and brand colors, a status badge, and a footer, instead of a bare unstyled table.

## 2. What's real vs. what's a stub

| Area | Status |
|---|---|
| Auth, roles, dashboards, leads, contracts, accounts, activities, reservations, adhoc sales, targets, collections, portal, notifications, reports, property management, company user management | **Real** — full working CRUD/logic against real SQL |
| Lead scoring | **Heuristic** — deterministic, documented placeholder for a future real AI/ML service |
| AI Lead Generation | **Synthetic placeholder** — `Dso_lead_generator` synthesizes candidates from seeded `dso_market_intelligence` rows, not a real data-acquisition provider; scored via the real heuristic |
| AI Sales Assistant | **Heuristic core, real optional LLM enhancement** — `account_id`/`type`/`estimated_revenue` are always heuristic; free-text action/reason and (validated) property/priority are optionally refined by a genuinely connected LLM provider (OpenAI/Anthropic/Gemini/Groq/OpenRouter/Ollama/Mistral/DeepSeek/xAI/Azure OpenAI/Cohere) configured at `/dyafa/aiconfig`, with a safe heuristic-value fallback on any failure |
| PMS, Finance/ERP, Payment Gateway, Maps, Reporting Platform integrations | **Real plumbing + realistic mock (default)**, same `live`/`mock`/`off` convention — attempts a real HTTP call only once an endpoint is configured; safe deterministic mock otherwise |
| Notification delivery channel (email/SMS/push) | **Out of scope** — in-app `dso_notifications` table only |
| `book.dyafa.com` as a literal separate subdomain | **Out of scope** — portal is served from the same vhost as the internal app |
| Administration: Users & Roles, Teams, Integrations UI, Notification Center, Audit Log | **Real** — dynamic RBAC, territory-scoped Teams, encrypted-credential config editor, broadcast tool, audit trail viewer |
| Dashboard My/Team Performance, Leads scopes/sources, Contract status filters, Account 360°/Performance, Reservation filters + Calendar, Adhoc Opportunities Board, Activities module, Collections sub-screens, Property Availability Settings | **Real** — real SQL/queries, no placeholder views |
| AI Sales Assistant Predictions / Next Best Actions | **Real** — `find_churn_risk_accounts()` (booking-cadence-based) and `find_accounts_needing_next_action()` (activity-recency-based) generators |
| Soft delete + audit trail (Contracts/Accounts/Adhoc/Properties/Collections/Targets/Roles/Teams) | **Real** — `deleted_at` column + shared `dso_audit_log` table, viewable at Administration > Audit Log |
| Integration API key storage | **Real** — encrypted in `dso_integration_credentials`, same boundary as AI provider keys; `mode`/`endpoint`/`timeout` stay in the config file |
| Corporate Finance 2FA | **Real** — dependency-free RFC 6238 TOTP (`Dso_totp.php`), mandatory enrollment + verification for the `CorporateFinance` sub-role |
| List pagination (Leads/Reservations/Notifications) | **Real** — server-side `LIMIT`/`OFFSET` via `Dso_Controller::paginate()` |

## 3. Tech stack

- PHP (CodeIgniter 3 / wiredesignz HMVC), MySQL 8 (InnoDB, utf8mb4)
- No JS framework — plain HTML/CSS + inline `<style>` in `application/views/dyafa/layout/header.php`
- No package manager / build step required

## 4. Local development setup

1. Import schema + seed data into the shared app database (fresh installs get everything from the one file; existing databases from before 2026-07-22 should also run the numbered migrations below in order):
   ```
   mysql -u root -p ci_realestate_web < dyafa_sales_os_schema.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_002_integration_mocks.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_003_vip_notifications.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_004_property_geo.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_005_payment_mock.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_006_corporate_subroles.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_007_ai_lead_gen.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_008_admin_rbac_teams.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_009_activities_generic.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_010_property_availability.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_011_bulk_seed_data.sql
   mysql -u root -p ci_realestate_web < dyafa_sales_os_migration_012_audit_soft_delete_security.sql
   ```
2. `application/config/database.php` already points at `ci_realestate_web` (`root` / `root`, mysqli, empty prefix). No changes required unless your local credentials differ.
3. `application/config/config.php` computes `base_url` dynamically from the request — no hardcoded URL to update for local dev.
4. `application/config/config.php`'s `$config['encryption_key']` must hold a strong random value before any AI Config provider can be saved (it encrypts `dso_ai_providers.api_key_encrypted`) — see the `TODO` comment at that line. Generate one with `php -r "echo bin2hex(random_bytes(32));"` if it isn't already set.
5. Visit `http://<your-host>/dyafa/login` for staff login, or `http://<your-host>/dyafa/portal/login` for the corporate client portal. The legacy CMS admin panel remains at `http://<your-host>/admin` (separate login, unrelated to this module) — linked from the Dyafa sidebar for HOD-level roles.
6. Run the generators manually as needed:
   ```
   php index.php cron dso_generate_notifications
   php index.php cron dso_generate_ai_recommendations
   php index.php cron dso_generate_leads
   ```
   (If your environment's `php index.php` CLI invocation errors on missing `$_SERVER['HTTP_HOST']`/`REQUEST_URI` — a pre-existing quirk of this legacy app's front controller, unrelated to the Dyafa module — hit the same path as a URL through your web server instead, e.g. `curl http://<your-host>/index.php/cron/dso_generate_notifications`.)

## 5. Production deployment

1. **Server requirements**: PHP 7.x/8.x with `mysqli` and `curl` extensions enabled, MySQL/MariaDB 5.7+/8.x, Apache (`mod_rewrite`) or Nginx equivalent.
2. **Code**: deploy the full `ci_realestate_web` directory (this Dyafa module is not a standalone app — it shares the legacy CMS's front controller, session handling and `system/` framework core).
3. **Database**:
   - Provision a MySQL database and import the base application schema first, then:
     ```
     mysql -u <prod_user> -p <prod_db> < dyafa_sales_os_schema.sql
     ```
   - Update `application/config/database.php` with production credentials (`hostname`, `username`, `password`, `database`). Do not commit real production credentials to version control — manage this file per-environment (e.g. via your deploy pipeline or a `.env`-driven override) instead of editing it in place in the repo.
   - Set `'db_debug' => (ENVIRONMENT !== 'production')` (already the default in `database.php`) so SQL errors are never displayed to end users in production.
4. **Environment flag**: set `ENVIRONMENT=production` via the `CI_ENV` server environment variable (see `index.php` line ~56) so PHP error display is disabled and only fatal errors are logged.
5. **Web server**:
   - Point the vhost document root at the `ci_realestate_web` directory.
   - Ensure `mod_rewrite` is enabled so the existing `.htaccess` (front-controller rewrite to `index.php`) works; on Nginx, translate the same rule (rewrite all non-file/non-directory requests to `index.php`).
   - **If the vhost document root is the `ci_realestate_web` folder itself**, `.htaccess`'s `RewriteBase /` is correct as-is.
   - **If instead you're serving from a shared document root with the app in a subdirectory** (e.g. `http://host/dyafa-sales-os/ci_realestate_web/...`, as in local dev under Laragon's default `C:/laragon/www` docroot), update `RewriteBase` in `.htaccess` to that subdirectory path (e.g. `RewriteBase /dyafa-sales-os/ci_realestate_web/`) — otherwise every route 404s. This is the same fix already flagged in `readme.txt`.
   - Serve over HTTPS in production; the app already derives `base_url` from `$_SERVER['HTTPS']`.
6. **File permissions**: ensure the web server user can write to `uploads/` (including the new `uploads/property_maps/` folder, created for this module) and `application/logs/`.
7. **Scheduled jobs**: configure the two generators to run periodically (e.g. every 15–30 minutes) via `cron` (Linux) or Task Scheduler (Windows):
   ```
   php /path/to/ci_realestate_web/index.php cron dso_generate_notifications
   php /path/to/ci_realestate_web/index.php cron dso_generate_ai_recommendations
   ```
8. **Optional — enabling real PMS/Finance integrations**: edit `application/config/dso_integrations.php` and set `dso_pms_endpoint`/`dso_pms_api_key` and/or `dso_finance_endpoint`/`dso_finance_api_key` once real external systems exist. Leave blank to keep the current safe, documented stub behavior.
9. **Change the seeded demo passwords** (see below) before going live, and remove/disable the demo `corporate1` client account if it's not a real client.

## 6. Login credentials (seeded demo data)

| Name | Username | Password | Role | Notes |
|---|---|---|---|---|
| Anas | `anas` | `Passw0rd!` | HOD Sales | Full access — `/dyafa/login` |
| Nidal | `nidal` | `Passw0rd!` | Sales Manager | `/dyafa/login` |
| Ahmad Saleh | `ahmad.saleh` | `Passw0rd!` | Sales Executive | `/dyafa/login` |
| Ali | `ali` | `Passw0rd!` | Sales Coordinator | `/dyafa/login` — only role that can manage Properties |
| Corporate Client One | `corporate1` | `Client123!` | Corporate Client | `/dyafa/portal/login` — scoped to account #1, Acme Corp; legacy flat role, treated the same as CorporateAdmin |

New corporate sub-role users (CorporateAdmin/CorporateHR/CorporateFinance/CorporateTravelCoordinator/CorporateProjectManager) are not seeded by default — create them from **Company Users** (`/dyafa/portal/users`) while logged in as `corporate1` or any CorporateAdmin.

All passwords are bcrypt-hashed in `dyafa_sales_os_schema.sql`; these are demo credentials for the seeded data only — **rotate them before any production use**.

**Legacy CMS admin login** (separate from the Dyafa Sales OS module above, `/index.php/admin/logins`): username `admin`, password `admin` (email `demo@demo.com`). Rotate this before production use — it's the framework's default demo seed account.

**Database credentials** used by this app locally (`application/config/database.php`): host `localhost`, user `root`, password `root`, database `ci_realestate_web`. Replace these with your own production database credentials as described in the deployment section — do not reuse local/demo credentials in production.

## 7. Key navigation paths

| Path | Purpose |
|---|---|
| `/dyafa/login` | Staff login |
| `/dyafa/portal/login` | Corporate client self-service portal login |
| `/dyafa/dashboard` | Daily or HOD dashboard (role-based redirect) |
| `/dyafa/leads`, `/dyafa/contracts`, `/dyafa/accounts`, `/dyafa/reservations`, `/dyafa/adhoc`, `/dyafa/collections`, `/dyafa/targets` | Core CRM modules |
| `/dyafa/properties` | Property Management (Coordinator-managed), now with geo maps |
| `/dyafa/portal/search` | Corporate Portal — hotel/availability search |
| `/dyafa/portal/users` | Corporate Portal — Company User Management (CorporateAdmin-only) |
| `/dyafa/portal/statement`, `/dyafa/portal/invoice_download/<id>` | Corporate Portal — statement + per-invoice PDF download |
| `/dyafa/aiassistant` | AI Sales Assistant recommendations (+ "Generate Now", HOD-only) |
| `/dyafa/aiconfig` | LLM provider configuration for the AI Sales Assistant (HOD-only) |
| `/dyafa/reports/daily_sales` | Reports (14 total, each with CSV export; 7 also offer "Push to Reporting Platform") |
| `/dyafa/notifications` | In-app notifications |
| `/dyafa/activities` | Activities module (My/Team/Log) |
| `/dyafa/admin/users`, `/dyafa/admin/roles`, `/dyafa/admin/teams`, `/dyafa/admin/integrations`, `/dyafa/admin/notificationcenter`, `/dyafa/admin/auditlog` | Administration (Users & Roles, Teams, Integrations, Notification Center, Audit Log) |
| `/admin` | Legacy CMS admin panel (separate login, de-emphasized link in the Dyafa sidebar for HOD-level roles — see decision note below) |

## 8. Open decisions (not blocking, documented so they aren't re-litigated)

- **Legacy CMS fate**: kept standalone (not retired, not migrated) — the sidebar link is now visually de-emphasized (dimmed, external-link icon, "separate legacy system" divider) rather than reading as a first-class Dyafa Sales OS menu item, per the "at minimum" fallback in the presentation audit. A full retire-or-migrate-its-property/blog-data-to-`dso_properties` decision still needs a business call, since it's still actively linked and its own theme/RBAC were intentionally left untouched (a much larger, separate undertaking).
- **Real PMS/Finance/Maps/Payment/Reporting vendor**: still unconfirmed/unnamed by the business — every integration remains in `mock` mode by default. Switching any one to `live` is a one-line config change (`dso_*_mode` + endpoint) plus entering the API key at Administration > Integrations once a real vendor is contracted; no application code needs to change.
- **Full internal REST/JSON API, automated test suite, rate limiting on external calls**: deliberately not built this pass — each is a genuinely large, separate initiative (new controller namespace + token auth; a test framework/fixtures strategy for a codebase with zero existing automated tests; backoff/dedup across 6+ external call sites) rather than a gap that fits alongside the fixes above. Tracked in `todolist.md` section E for a future dedicated session.
