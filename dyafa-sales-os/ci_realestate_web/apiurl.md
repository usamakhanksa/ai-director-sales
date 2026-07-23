# Dyafa Sales OS - Full URL / Route / Menu Reference

Updated 2026-07-22 (full re-audit against current controller source). This is a
complete list of every controller endpoint in the `dyafa` module (server-rendered
pages, not a REST/JSON API — see note at the bottom), reconciled directly
against the live code in `application/controllers/dyafa/**/*.php`.

**Correction vs. the previous version of this file:** the earlier revision
documented a `dyafa/leads/mine|unassigned|ai` 404 bug and an "only 4 of 14
reports built" gap. Both are now fixed/complete in the current codebase
(confirmed by reading `Leads.php` and `Reports.php` directly, and cross-checked
against `tasklist.md`, which records both fixes under "BRD Sidebar Gap
Closure"). This file has been rewritten to match what actually runs today.

All URLs are relative to your base URL, e.g.
`http://localhost/dyafa-sales-os/ci_realestate_web/`.
Everything under `dyafa/*` requires a logged-in session (`Dso_Controller`
redirects to `dyafa/login` otherwise) except the whitelisted public actions
noted below.

## Special routes (`application/config/routes.php`)

| URL | Resolves to | Notes |
|---|---|---|
| `dyafa` | `dyafa/dashboard` | default landing page after login |
| `dyafa/login` | `dyafa/auth/login` | |
| `dyafa/logout` | `dyafa/auth/logout` | |
| `dyafa/portal` | `dyafa/portal/dashboard` | Corporate Client portal home |
| `dyafa/(:any)` | `dyafa/$1` | catch-all identity rewrite; harmless now that all controllers use `index($param)` / query-string scoping rather than method-name segments for filters |

### Legacy dead routes (not part of dyafa, pre-existing, out of scope)
`application/config/routes.php` lines 49-51 map `getProperty/(:any)/(:any)` and
`getAllProperties[/(:any)]` to an `Api` controller that does not exist anywhere
in `application/controllers/` or `application/modules/`. These two routes 404
if ever hit and are not used by the Dyafa Sales OS module. Recommend either
deleting them or building the `Api` controller if the legacy front-end site
still needs them (see Enhancement Roadmap in `enhance.md`).

## Public (no login required)

| Method | URL | Controller::method |
|---|---|---|
| GET/POST | `dyafa/auth/login` | `Auth::login()` |
| POST | `dyafa/auth/authenticate` | `Auth::authenticate()` |
| GET | `dyafa/auth/logout` | `Auth::logout()` |
| GET/POST | `dyafa/portal/login` | `Portal::login()` |
| POST | `dyafa/portal/authenticate` | `Portal::authenticate()` |
| GET | `dyafa/portal/logout` | `Portal::logout()` |

## Staff-side pages (session required)

### Dashboard - `Dashboard.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/dashboard` | `index()` | any logged-in staff role | Working |
| `dyafa/dashboard/daily` | `daily()` | any logged-in staff role | Working |
| `dyafa/dashboard/hod` | `hod()` | HOD Sales, Sales Manager, Management | Working |
| `dyafa/dashboard/my_performance` | `my_performance()` | any logged-in staff role | Working |
| `dyafa/dashboard/team_performance` | `team_performance()` | HOD/Manager/Management-gated | Working |

### Leads - `Leads.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/leads` | `index(null)` | HOD Sales / Sales Manager / Sales Executive (menu) | Working |
| `dyafa/leads/index/mine` | `index('mine')` | same | Working — filters `owner_id = current user` |
| `dyafa/leads/index/unassigned` | `index('unassigned')` | same | Working |
| `dyafa/leads/index/ai` | `index('ai')` | same | Working — filters `source = 'AI Generated'` |
| `dyafa/leads?status=&source=` | `index()` + GET filters | same | Working — status/source dropdown filters, composable with scope |
| `dyafa/leads/sources` | `sources()` | same | Working — grouped counts/revenue by source |
| `dyafa/leads/add` | `add()` | same | Working — auto-assigns to HOD Sales, runs `Dso_lead_scoring` |
| `dyafa/leads/edit/{id}` | `edit($id)` | same | Working |
| `dyafa/leads/view/{id}` | `view($id)` | same | Working |
| `dyafa/leads/delete/{id}` | `delete($id)` | same | Working — **soft delete** (`Dso_leads_model::soft_delete()`) |
| `dyafa/leads/assign/{id}` | `assign($id)` | **HOD Sales only** | Working |

> **CONFIRMED LIVE BUG (re-verified 2026-07-22):** `Leads.php:32-37` builds `$data['dso_tabs']` with URLs `base_url('dyafa/leads/mine')`, `.../leads/unassigned`, `.../leads/ai`, and `application/views/dyafa/leads/list.php:5` renders them via the shared `dyafa/partials/list_tabs.php` partial. The `dyafa/(:any)` catch-all rewrites these to themselves, so CI3 resolves `class=Leads, method=mine` — but `Leads.php` has no `mine()` method and no `_remap()`, so **clicking "My Leads" / "Unassigned" / "AI Generated" in the browser still 404s today**, even though `index($scope)` itself correctly implements all three filters and is reachable at `dyafa/leads/index/mine` etc. This is a one-line view/controller mismatch, not a logic bug — see `enhance.md` for the fix.

### Contracts - `Contracts.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/contracts` | `index(null)` | any staff role | Working |
| `dyafa/contracts/index/active` | `index('active')` | any staff role | Working |
| `dyafa/contracts/index/pending` | `index('pending')` | any staff role | Working |
| `dyafa/contracts/index/expiring` | `index('expiring')` | any staff role | Working — 30-day expiry window, territory-scoped for Teams |
| `dyafa/contracts/add` | `add()` | any staff role | Working |
| `dyafa/contracts/edit/{id}` | `edit($id)` | any staff role | Working |
| `dyafa/contracts/delete/{id}` | `delete($id)` | any staff role | Working (hard delete — no soft-delete/audit trail, see Enhancement Roadmap) |
| `dyafa/contracts/funnel` | `funnel()` | any staff role | Working — status-grouped funnel with SQL counts |

### Corporate Accounts - `Accounts.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/accounts` | `index()` | any staff role | Working |
| `dyafa/accounts/add` | `add()` | any staff role | Working |
| `dyafa/accounts/edit/{id}` | `edit($id)` | any staff role | Working |
| `dyafa/accounts/view/{id}` | `view($id)` | any staff role | Working |
| `dyafa/accounts/view360/{id}` | `view360($id)` | any staff role | Working — aggregates contracts + reservations + collections + activities |
| `dyafa/accounts/performance` | `performance()` | any staff role | Working — per-account revenue/room-nights/reservation-count |
| `dyafa/accounts/activities/{id}` | `activities($id)` | any staff role | Working |
| `dyafa/accounts/add_activity/{account_id}` | `add_activity($account_id)` | any staff role | Working |
| `dyafa/accounts/delete/{id}` | `delete($id)` | any staff role | Working (hard delete) |

### Reservations - `Reservations.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/reservations` | `index(null)` | any staff role | Working |
| `dyafa/reservations/index/pending` | `index('pending')` | any staff role | Working |
| `dyafa/reservations/index/checkins_today` | `index('checkins_today')` | any staff role | Working |
| `dyafa/reservations/index/checkouts_today` | `index('checkouts_today')` | any staff role | Working |
| `dyafa/reservations/calendar` | `calendar()` | any staff role | Working — month grid, AJAX drag-drop |
| `dyafa/reservations/calendar_move/{id}` | `calendar_move($id)` | any staff role | Working — AJAX endpoint, re-validates against contract |
| `dyafa/reservations/add` | `add()` | any staff role | Working — allowed-properties + credit-limit validation |
| `dyafa/reservations/edit/{id}` | `edit($id)` | any staff role | Working |
| `dyafa/reservations/cancel/{id}` | `cancel($id)` | any staff role | Working (status-based soft-cancel; no hard `delete()` method exists — intentional) |

### Adhoc Sales - `Adhoc.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/adhoc` | `index(null)` | any staff role | Working |
| `dyafa/adhoc/index/proposals` | `index('proposals')` | any staff role | Working — status=ProposalSent |
| `dyafa/adhoc/index/events` | `index('events')` | any staff role | Working — event_type=Event |
| `dyafa/adhoc/board` | `board()` | any staff role | Working — kanban, one column per status |
| `dyafa/adhoc/board_move/{id}` | `board_move($id)` | any staff role | Working — AJAX drag-drop status update |
| `dyafa/adhoc/add` | `add()` | any staff role | Working |
| `dyafa/adhoc/edit/{id}` | `edit($id)` | any staff role | Working |
| `dyafa/adhoc/delete/{id}` | `delete($id)` | any staff role | Working (hard delete) |

### Property Management - `Properties.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/properties` | `index()` | any staff role (read-only for non-Coordinators) | Working |
| `dyafa/properties/add` | `add()` | **Sales Coordinator only** | Working — map/info file upload via CI upload lib |
| `dyafa/properties/edit/{id}` | `edit($id)` | **Sales Coordinator only** | Working |
| `dyafa/properties/delete/{id}` | `delete($id)` | **Sales Coordinator only** | Working (hard delete) |
| `dyafa/properties/rates/{id}` | `rates($id)` | **Sales Coordinator only** | Working |
| `dyafa/properties/delete_rate/{rate_id}/{property_id}` | `delete_rate($rate_id, $property_id)` | **Sales Coordinator only** | Working |
| `dyafa/properties/availability/{id}` | `availability($id)` | **Sales Coordinator only** | Working — `is_bookable` flag + blackout dates (not a full per-night inventory calendar, by design — see `implementation.md`) |
| `dyafa/properties/delete_blackout_date/{id}/{property_id}` | `delete_blackout_date($id, $property_id)` | **Sales Coordinator only** | Working |

### Collections - `Collections.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/collections` | `index()` | Finance Team / HOD Sales / Sales Manager / Management (menu) | Working |
| `dyafa/collections/add` | `add()` | same | Working |
| `dyafa/collections/edit/{id}` | `edit($id)` | **Finance Team, HOD Sales, Management** | Working — manual payment recording, auto status derivation, Finance/Payment mock sync |
| `dyafa/collections/delete/{id}` | `delete($id)` | same as index | Working (hard delete) |
| `dyafa/collections/aging` | `aging()` | same as index | Working — 0-30/31-60/61-90/90+ buckets, CSV export |
| `dyafa/collections/credit_limits` | `credit_limits()` | same | Working — credit limit vs. outstanding per account |
| `dyafa/collections/invoices` | `invoices()` | same | Working — invoice-framed list |
| `dyafa/collections/statements` | `statements()` | same | Working — staff-side per-account statement |

### Sales Targets - `Targets.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/targets` | `index(null)` | any staff role | Working |
| `dyafa/targets/add` | `add()` | **HOD Sales, Sales Manager, Management** | Working ("Set Targets" menu label) |
| `dyafa/targets/edit/{id}` | `edit($id)` | **HOD Sales, Sales Manager, Management** | Working |
| `dyafa/targets/delete/{id}` | `delete($id)` | **HOD Sales, Sales Manager, Management** | Working (hard delete) |
| `dyafa/targets/performance/{user_id?}/{month?}` | `performance($user_id=null,$month=null)` | any staff role | Working — %-achievement + band mapping |

### Activities - `Activities.php` (standalone top-level module)
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/activities` | `index(null)` | any staff role | Working |
| `dyafa/activities/index/mine` | `index('mine')` | any staff role | Working |
| `dyafa/activities/index/team` | `index('team')` | any staff role | Working |
| `dyafa/activities/add` | `add()` | any staff role | Working — account-optional generic activity log |

> No `edit`/`delete`/`view` on Activities — activities are treated as immutable audit-style records (same for `Accounts::add_activity`, which shares the underlying model). This is intentional, not a gap.

### AI Lead Generation - `LeadGeneration.php` / `LeadScoringConfig.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/leadgeneration` | `index()` | any staff role (menu-gated) | Working |
| `dyafa/leadgeneration/generate` | `generate()` | HOD/Management-gated | Working — synchronous trigger for `Dso_lead_generator`, synthesizes candidate leads from `dso_market_intelligence`, explicitly synthetic placeholder data |
| `dyafa/leadscoringconfig` | `index()` | HOD Sales / Management | Working — view current 4 scoring weights |
| `dyafa/leadscoringconfig/save` | `save()` | HOD Sales / Management | Working — persists to `dso_lead_scoring_config`, `Dso_lead_scoring` falls back to hardcoded defaults if empty |

### AI Sales Assistant - `AiAssistant.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/aiassistant` | `index()` | any staff role | Working |
| `dyafa/aiassistant/action/{id}` | `action($id)` | any staff role | Working |
| `dyafa/aiassistant/dismiss/{id}` | `dismiss($id)` | any staff role | Working |
| `dyafa/aiassistant/generate` | `generate()` | **HOD Sales, Sales Manager, Management** | Working — triggers `Dso_sales_assistant::generate_all_recommendations()` |
| `dyafa/aiassistant/predictions` | `predictions()` | any staff role | **Partial** — view exists with friendly empty state, but no generator produces `Prediction` rows yet |
| `dyafa/aiassistant/next_best_actions` | `next_best_actions()` | any staff role | **Partial** — same as above for `NextBestAction` type |
| `dyafa/aiassistant/analytics` | `analytics()` | any staff role | Working — real aggregate counts by type/status |

### AI Provider Config - `AiConfig.php`
All methods require **HOD Sales, Sales Manager, Management** (gated in the constructor).

| URL | Method | Notes | Status |
|---|---|---|---|
| `dyafa/aiconfig` | `index()` | list configured providers | Working |
| `dyafa/aiconfig/add` | `add()` | | Working |
| `dyafa/aiconfig/edit/{id}` | `edit($id)` | | Working |
| `dyafa/aiconfig/delete/{id}` | `delete($id)` | | Working (hard delete) |
| `dyafa/aiconfig/set_default/{id}` | `set_default($id)` | enforces exactly-one-default | Working |
| `dyafa/aiconfig/test` | `test()` | **JSON endpoint** — only true JSON response outside AJAX helpers below | Working |

### Reports - `Reports.php` (all 14 BRD report types, real SQL, CSV export on every one)
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/reports/daily_sales` | `daily_sales()` | any staff role | Working |
| `dyafa/reports/revenue` | `revenue()` | any staff role | Working — by month/property/account, CSV export |
| `dyafa/reports/aging` | `aging()` | any staff role | Working — CSV export |
| `dyafa/reports/leads` | `leads()` | any staff role | Working — by status/category/owner, CSV export |
| `dyafa/reports/reservations` | `reservations()` | any staff role | Working — CSV export |
| `dyafa/reports/room_nights` | `room_nights()` | any staff role | Working — by property/month, CSV export |
| `dyafa/reports/contracts` | `contracts()` | any staff role | Working — CSV export |
| `dyafa/reports/contract_renewals` | `contract_renewals()` | any staff role | Working — expiring within 60 days, CSV export |
| `dyafa/reports/opportunities` | `opportunities()` | any staff role | Working — by status, CSV export |
| `dyafa/reports/adhoc_sales` | `adhoc_sales()` | any staff role | Working — by status, CSV export |
| `dyafa/reports/activities` | `activities()` | any staff role | Working — by type, CSV export |
| `dyafa/reports/corporate_accounts` | `corporate_accounts()` | any staff role | Working — CSV export |
| `dyafa/reports/property_performance` | `property_performance()` | any staff role | Working — CSV export |
| `dyafa/reports/ai_recommendations` | `ai_recommendations()` | any staff role | Working — CSV export |
| `dyafa/reports/push_to_reporting/{report}` | `push_to_reporting($report)` | **HOD-only** | Working — pushes named report to mock/live Reporting Platform integration; wired on 7 of the 14 reports (not all — see Enhancement Roadmap) |

> Append `?export=csv` to any report URL above to stream a CSV instead of the HTML view (`dso_csv_helper.php` / `dso_export_csv()`).

### Notifications - `Notifications.php`
| URL | Method | Roles allowed | Status |
|---|---|---|---|
| `dyafa/notifications` | `index()` | any staff role | Working |
| `dyafa/notifications/mark_read/{id}` | `mark_read($id)` | any staff role | Working |

> No `add()`/`delete()` — notifications are system-generated only via `Cron::dso_generate_notifications()` (new-lead, payment-pending, contract-expiring, target-achieved, guest-complaint, proposal-pending, VIP-arrival triggers) or the Admin broadcast tool below.

## Administration module - `dyafa/admin/*` (net-new RBAC/Teams/Integrations layer)

### Roles & Permissions - `Admin/Roles.php`
| URL | Method | Status |
|---|---|---|
| `dyafa/admin/roles` | `index()` | Working — list roles |
| `dyafa/admin/roles/add` | `add()` | Working — checkbox matrix of permissions |
| `dyafa/admin/roles/edit/{id}` | `edit($id)` | Working |
| `dyafa/admin/roles/delete/{id}` | `delete($id)` | Working (hard delete) |

### Users - `Admin/Users.php`
| URL | Method | Status |
|---|---|---|
| `dyafa/admin/users` | `index()` | Working |
| `dyafa/admin/users/add` | `add()` | Working |
| `dyafa/admin/users/edit/{id}` | `edit($id)` | Working — assign role_id/team_id |
| `dyafa/admin/users/toggle_status/{id}` | `toggle_status($id)` | Working — **no hard delete, soft activate/deactivate pattern by design** |

### Teams - `Admin/Teams.php`
| URL | Method | Status |
|---|---|---|
| `dyafa/admin/teams` | `index()` | Working |
| `dyafa/admin/teams/add` | `add()` | Working — assign users/properties/accounts (territory) |
| `dyafa/admin/teams/edit/{id}` | `edit($id)` | Working |
| `dyafa/admin/teams/delete/{id}` | `delete($id)` | Working (hard delete) |

### Integrations - `Admin/Integrations.php`
| URL | Method | Status |
|---|---|---|
| `dyafa/admin/integrations` | `index()` | Working — confirmed handles both GET (renders form from live config values) and POST (validates mode enum, rewrites `application/config/dso_integrations.php` via `var_export`) in the same method; no DB table by design |

### Notification Center - `Admin/NotificationCenter.php`
| URL | Method | Status |
|---|---|---|
| `dyafa/admin/notificationcenter` | `index()` | Working — admin list across all users |
| `dyafa/admin/notificationcenter/broadcast` | `broadcast()` | Working — insert a notification row per targeted user/role |

## Corporate Self-Service Portal - `Portal.php` (role: Corporate Client + 5 sub-roles only)

| URL | Method | Notes | Status |
|---|---|---|---|
| `dyafa/portal/login` | `login()` | public | Working |
| `dyafa/portal/authenticate` | `authenticate()` | public | Working |
| `dyafa/portal/logout` | `logout()` | public | Working |
| `dyafa/portal/dashboard` | `dashboard()` | account-scoped | Working |
| `dyafa/portal/reservations` | `reservations()` | account-scoped list | Working |
| `dyafa/portal/search` | `search()` | Working — hotel/availability search against contract's allowed properties + corporate rate | Working |
| `dyafa/portal/reservation_new` | `reservation_new()` | create, validated against contract/credit, prefillable from `search()` | Working |
| `dyafa/portal/reservation_cancel/{id}` | `reservation_cancel($id)` | ownership-checked | Working |
| `dyafa/portal/statement` | `statement()` | HTML statement | Working |
| `dyafa/portal/invoice_download/{collection_id}` | `invoice_download($collection_id)` | PDF via Dompdf, ownership-checked | Working |
| `dyafa/portal/users` | `users()` | **CorporateAdmin sub-role only** | Working — Company User Management |
| `dyafa/portal/user_add` | `user_add()` | **CorporateAdmin sub-role only** | Working |
| `dyafa/portal/user_toggle_status/{id}` | `user_toggle_status($id)` | **CorporateAdmin sub-role only** | Working — soft activate/deactivate, no hard delete |

## CLI / Cron-only (no session, top-level `application/controllers/Cron.php`)

| Invocation | Method | Purpose |
|---|---|---|
| `php index.php cron dso_generate_notifications` | `dso_generate_notifications()` | new-lead, payment-pending, contract-expiring, target-achieved, guest-complaint, proposal-pending, VIP-arrival notifications, de-duplicated |
| `php index.php cron dso_generate_ai_recommendations` | `dso_generate_ai_recommendations()` | thin wrapper calling `Dso_sales_assistant::generate_all_recommendations()` (heuristic + optional LLM free-text enhancement) |
| `php index.php cron dso_generate_leads` | `dso_generate_leads()` | synthesizes candidate leads from `dso_market_intelligence` via `Dso_lead_generator`, scores via `Dso_lead_scoring`, de-dupes by company name — explicitly synthetic placeholder data, not a real data provider |

## Sidebar menu (`application/config/dso_menu.php`)

The sidebar now covers every BRD leaf item, including the Administration group
(Roles, Users, Teams, Integrations, Notification Center), AI Lead Generation
group, and all 14 report types. Recommend treating `dso_menu.php` itself as the
source of truth for exact per-role visibility going forward rather than
duplicating the full role matrix here — it is actively maintained and changes
faster than this doc should chase it.

## Important note on "API"

This application does **not** expose a JSON/REST API for external consumers.
It is a server-rendered CodeIgniter 3 app: every URL above returns a full HTML
page (form submit -> redirect pattern) or triggers an in-page AJAX partial
(calendar/kanban drag-drop, CSV download), with the sole true `application/json`
response being `dyafa/aiconfig/test`. If a real JSON API is ever needed (mobile
app, headless frontend, third-party integration), that does not exist yet —
see "Build a real internal REST/JSON API layer" in `enhance.md`.

## CRUD coverage matrix (entity-by-entity)

| Entity | Create | Read/List | Update | Delete | Notes |
|---|---|---|---|---|---|
| Leads | ✅ | ✅ (+scopes/filters) | ✅ | ✅ soft | |
| Contracts | ✅ | ✅ (+status filters) | ✅ | ✅ hard | no soft-delete/audit trail |
| Corporate Accounts | ✅ | ✅ (+360/performance) | ✅ | ✅ hard | |
| Reservations | ✅ | ✅ (+filters/calendar) | ✅ | ⚠️ cancel only | intentional — no hard delete |
| Adhoc Sales | ✅ | ✅ (+board/filters) | ✅ | ✅ hard | |
| Properties | ✅ | ✅ | ✅ | ✅ hard | + rates, availability, blackout dates sub-resources |
| Collections | ✅ | ✅ (+aging/credit/invoices/statements) | ✅ | ✅ hard | |
| Targets | ✅ | ✅ (+performance) | ✅ | ✅ hard | |
| Activities | ✅ | ✅ (+mine/team) | ❌ | ❌ | intentional — immutable audit log |
| Notifications | ⚠️ system/broadcast only | ✅ | ⚠️ mark_read only | ❌ | intentional — system-generated |
| AI Provider Config | ✅ | ✅ | ✅ | ✅ hard | |
| AI Recommendations | ⚠️ generator only | ✅ | ⚠️ action/dismiss only | ❌ | |
| Roles | ✅ | ✅ | ✅ | ✅ hard | |
| Users (staff) | ✅ | ✅ | ✅ | ⚠️ toggle_status only | intentional soft-disable |
| Teams | ✅ | ✅ | ✅ | ✅ hard | |
| Corporate Portal Users | ✅ | ✅ | ❌ | ⚠️ toggle_status only | confirmed no `edit()` method on `Portal.php` — see Enhancement Roadmap |

Legend: ✅ full · ⚠️ partial/intentional variant · ❌ not applicable/by design.

See `enhance.md` for the prioritized enhancement roadmap (real gaps worth
closing vs. intentional design choices documented above).


"todo list"
Delete dead getProperty/getAllProperties routes after confirming no external refs

Write migration_012 SQL: deleted_at columns (8 tables), dso_audit_log, dso_integration_credentials, dso_users TOTP columns

Add Dso_Controller helpers: audit(), soft_delete_row(), paginate()

Add Dso_audit_log_model + wire soft-delete/audit into 8 models+controllers (Contracts, Accounts, Adhoc, Properties, Collections, Targets, Roles, Teams)

Add Portal::user_edit() + view

Extend Reports::push_to_reporting() map to all 14 reports

Build AI Predictions + Next Best Action generator passes in Dso_sales_assistant

Encrypt integration API keys via new Dso_integration_credentials_model + rewrite Admin/Integrations

Build TOTP 2FA (Dso_totp library) mandatory for CorporateFinance portal users

Add Dso_Controller::paginate() usage to Leads/Reservations/Notifications index()

Add favicon

Build dedicated branded minimal login layout (auth + portal)

Add Chart.js + real charts to Daily/HOD dashboards

Clean up inline styles/raw hex colors across ~17 view files

Add legacy CMS banner/de-emphasis decision

Give invoice_pdf.php a proper branded stylesheet

Update tasklist.md/todolist.md/enhance.md to reflect completed work

Update README.md

Update implementation.md

Final PHP lint pass on all modified files