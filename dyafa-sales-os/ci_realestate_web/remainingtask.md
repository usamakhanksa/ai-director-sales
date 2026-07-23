# Dyafa Sales OS — Close remaining BRD sidebar gaps

## Context

`apiurl.md` documents the current state of the `dyafa` CI3 module against the BRD sidebar spec. Most core CRUD (Leads/Contracts/Accounts/Reservations/Adhoc/Targets/Collections/Properties/Reports/Notifications) is complete and follows one consistent controller/model/view pattern (see `application/core/Dso_Controller.php`, and e.g. `application/controllers/dyafa/Leads.php` as the canonical CRUD shape). What's missing, confirmed by direct code inspection:

1. Dashboard: My Performance / Team Performance
2. Leads: My Leads / Unassigned Leads / Lead Sources / AI Generated Leads filters
3. AI Lead Generation as its own menu group: Recommendations / Lead Scoring Config / Generate Leads / AI Settings
4. Contracts: Active / Pending Approval / Expiring Soon status filters
5. Corporate Accounts: Account 360° View, Performance
6. Reservations: Pending / Today's Check-ins / Today's Check-outs / Calendar (AJAX, drag-drop)
7. Adhoc Sales: Opportunities Board (kanban, AJAX drag-drop), Proposals, Events, Adhoc Revenue
8. Activities as a standalone top-level module: My/Team Activities, Log Activity
9. Targets: explicit "Set Targets" node
10. Collections: Credit Limits, Invoices, Statements as distinct screens
11. Property Management: Availability Settings
12. AI Sales Assistant: Predictions / Next Best Actions / Analytics as distinct views
13. **Administration (net-new, biggest piece)**: Users & Roles with full dynamic RBAC (DB-backed roles/permissions replacing hardcoded arrays), Teams with territory/property assignment enforced in data scoping, Integrations config-file editor UI, Notification Center admin

User decisions locked in: full dynamic RBAC, Teams own territory/property assignments enforced across scoping queries, Integrations UI edits the existing config file (no new table), Calendar/Kanban are AJAX+JS (drag-drop) rather than static HTML.

Given the scale (13 feature areas, several schema changes, a cross-cutting RBAC rewrite), this is being executed as one coherent effort but delivered/tested in the phases below so each phase is independently verifiable.

## Architecture decisions

### RBAC rewrite (touches every controller)
- New tables: `dso_roles` (id, name, is_system), `dso_permissions` (id, key, label, group), `dso_role_permissions` (role_id, permission_id), plus `dso_users.role_id` FK added alongside the existing `role` string column (kept for backward compat during migration, existing enum values seeded as system roles 1:1).
- `Dso_Controller::require_role(array $roles)` becomes `require_permission($permission_key)`; add a compatibility shim so all 40+ existing call sites (`require_role(['HOD Sales','Sales Manager'])`) keep working by resolving legacy role-name arrays to an equivalent permission check during a transition, but **new code and the Administration module use `require_permission()` only**. A seed migration creates one permission per existing distinct `require_role()` call-site pattern found in the controllers (grep confirms these clusters: dashboard/hod, leads/assign, contracts add/edit/delete, collections edit, targets add/edit/delete, aiassistant/generate, aiconfig/*, properties add/edit/delete/rates).
- `Dso_permissions_model` + `Dso_roles_model` (standard CRUD, following the existing model pattern — no base_model, per `implementation.md`'s established convention).
- New controller `application/controllers/dyafa/Admin/Roles.php` (list/add/edit/delete roles, checkbox matrix of permissions per role) + `Admin/Users.php` (list/add/edit users, assign role_id, activate/deactivate — supersedes ad hoc user creation).

### Teams + territory
- New table `dso_teams` (id, name, hod_user_id, created_at) + `dso_team_properties` (team_id, property_id) + `dso_team_accounts` (team_id, account_id) for territory scoping, + `dso_users.team_id` FK.
- Scoping helper `Dso_Controller::my_team_property_ids()` / `my_team_account_ids()` used to filter Leads/Contracts/Accounts/Reservations list queries for non-HOD roles when a team has explicit territory rows (falls back to "no restriction" if the team has zero territory rows assigned, to avoid breaking existing seeded data).
- Controller `application/controllers/dyafa/Admin/Teams.php` (CRUD teams, assign users/properties/accounts to a team).

### Integrations UI
- `application/controllers/dyafa/Admin/Integrations.php`: reads `application/config/dso_integrations.php` into a form, on POST rewrites the file (var_export of a clean assoc array into the same `$config['dso_integrations']` shape), gated to `require_permission('manage_integrations')`.

### Notification Center admin
- `application/controllers/dyafa/Admin/NotificationCenter.php`: list all notifications across users (admin view over existing `dso_notifications`), plus a "broadcast" add form (insert a notification row per targeted user/role). No new table needed — reuses `dso_notifications`.

### AI Lead Generation group
- New menu group separate from AI Sales Assistant. `Dso_lead_scoring` is currently a hardcoded heuristic library — add `dso_lead_scoring_config` table (weight per signal) + `Admin`-adjacent controller `dyafa/LeadScoringConfig.php` so HOD can tune weights; `Dso_lead_scoring` reads weights from this table (falls back to current hardcoded defaults if table empty). "Generate Leads" = new UI trigger calling the existing `Cron::dso_generate_leads()` logic synchronously (extract shared logic into `Dso_lead_generator` library callable from both cron and controller). "AI Settings" = alias/reuse existing `AiConfig` controller (already built) surfaced under the new menu group — no duplicate code.

### Reservation Calendar & Opportunities Board (AJAX)
- `Reservations::calendar()` renders a month grid (plain PHP/CSS) and an AJAX endpoint `Reservations::calendar_events($month)` returning JSON events; client-side JS (vanilla, no new library — matches "no new frontend dependency" spirit while still being AJAX-driven) renders draggable event blocks, POSTs date changes to `Reservations::calendar_move($id)`.
- `Adhoc::board()` kanban with columns per `dso_adhoc_sales.status` enum values; drag-drop posts to `Adhoc::board_move($id)` (AJAX, updates status). "Proposals"/"Events" = status-filtered views of the same table (no schema change — `dso_adhoc_sales.type` or `status` already differentiates, confirmed against schema). "Adhoc Revenue" = cross-link to existing `reports/adhoc_sales`.

### Everything else (menu filters, dashboards, Activities module, Collections sub-views, Account 360, Availability Settings, AI Assistant sub-views)
- All follow the existing CRUD/list-filter pattern exactly: new controller method (or query param on `index()`) + new/reused view + new `dso_menu.php` entry + role/permission gate. No architectural novelty — listed in the phase table below with concrete method names.

## Phases & concrete file changes

**Phase 0 — Migrations** (new file `dyafa_sales_os_migration_008_admin_rbac_teams.sql`): create `dso_roles`, `dso_permissions`, `dso_role_permissions`, `dso_teams`, `dso_team_properties`, `dso_team_accounts`, `dso_lead_scoring_config`; alter `dso_users` add `role_id`, `team_id`; seed roles/permissions from the existing hardcoded role list and controller `require_role()` call sites. Seed dummy data (a few teams, role/permission rows, team-territory assignments) for testing.

**Phase 1 — RBAC core**: `Dso_roles_model`, `Dso_permissions_model`, update `Dso_Controller` with `require_permission()` + legacy shim, `Admin/Roles.php` + `Admin/Users.php` + views (`admin/roles/list.php`,`form.php`; `admin/users/list.php`,`form.php`).

**Phase 2 — Teams**: `Dso_teams_model`, `Admin/Teams.php` + views, scoping helpers in `Dso_Controller`, wire into `Leads::index()`, `Accounts::index()`, `Contracts::index()`, `Reservations::index()` (add optional territory filter, additive — no behavior change for teams with no assigned territory).

**Phase 3 — Integrations + Notification Center**: `Admin/Integrations.php` + view, `Admin/NotificationCenter.php` + views (list.php broadcast-list, form.php broadcast-add).

**Phase 4 — Dashboard/Leads/Contracts/Reservations/Adhoc/Activities/Targets/Collections/Properties/AI menu-and-filter items**: table below, one row per BRD leaf item, each = controller method + view (new or filtered reuse) + menu entry.

| BRD item | Controller::method (new unless noted) | View |
|---|---|---|
| My Performance | `Dashboard::my_performance()` | `dashboard/my_performance.php` |
| Team Performance | `Dashboard::team_performance()` | `dashboard/team_performance.php` |
| My Leads | `Leads::index()` + `?scope=mine` | reuse `leads/list.php` |
| Unassigned Leads | `Leads::index()` + `?scope=unassigned` | reuse `leads/list.php` |
| Lead Sources | `Leads::sources()` | `leads/sources.php` (group-by source) |
| AI Generated Leads | `Leads::index()` + `?scope=ai` | reuse `leads/list.php` |
| Recommendations (AI Lead Gen) | reuse `AiAssistant::index()` | — |
| Lead Scoring Config | `LeadScoringConfig::index/edit()` | `lead_scoring_config/form.php` |
| Generate Leads | `LeadGeneration::generate()` (new controller, sync trigger) | `lead_generation/result.php` |
| AI Settings | reuse `AiConfig::index()` | — |
| Active/Pending/Expiring Contracts | `Contracts::index()` + `?status=` | reuse `contracts/list.php` |
| Account 360° View | `Accounts::view360($id)` | `accounts/view360.php` (aggregates contracts+reservations+collections+activities) |
| Account Performance | `Accounts::performance()` | `accounts/performance.php` |
| Pending/Check-ins/Check-outs Reservations | `Reservations::index()` + `?status=`/`?date_filter=` | reuse list |
| Reservation Calendar | `Reservations::calendar()` + `calendar_events()` + `calendar_move()` | `reservations/calendar.php` + JS |
| Opportunities Board | `Adhoc::board()` + `board_move()` | `adhoc/board.php` + JS |
| Proposals / Events | `Adhoc::index()` + `?type=` | reuse `adhoc/list.php` |
| Adhoc Revenue | link to `reports/adhoc_sales` | — |
| My/Team Activities | new `Activities.php` controller: `index()` + `?scope=` | `activities/list.php` |
| Log Activity | `Activities::add()` | `activities/form.php` |
| Set Targets | reuse `Targets::add()` under explicit menu label | — |
| Credit Limits | `Collections::credit_limits()` | `collections/credit_limits.php` |
| Invoices | `Collections::invoices()` | `collections/invoices.php` |
| Statements | `Collections::statements()` | `collections/statements.php` |
| Availability Settings | `Properties::availability($id)` | `properties/availability.php` |
| Predictions / Next Best Actions / Analytics | `AiAssistant::predictions()/next_best_actions()/analytics()` filtered by `dso_ai_recommendations.type` (extend enum via migration) | 3 new views |

**Phase 5 — Menu wiring**: rewrite `application/config/dso_menu.php` to add Administration group + all new children above, permission-gated via new `require_permission` keys where applicable (fallback to role arrays for legacy entries not yet migrated).

**Phase 6 — Docs**: update `tasklist.md` (mark all above as done, replace stale "Missing Features Backlog" section), `README.md` (fix the stale "only 4 reports" inconsistency flagged during exploration, add Administration/RBAC/Teams sections), `implementation.md` (document RBAC/Teams architecture decisions above), create/refresh dummy seed data note.

## Verification
- Run `dyafa_sales_os_migration_008_admin_rbac_teams.sql` against local DB, confirm idempotent (information_schema-guarded like migrations 002-005).
- Manual smoke test per phase: log in as each seeded role, hit every new URL, confirm CRUD add/edit/delete round-trips and redirects/flashdata work (matches existing pattern, no automated test suite exists in this repo per exploration — confirm none was missed before skipping this).
- Confirm legacy `require_role()` call sites still function unchanged (regression check) after the compatibility shim is added.
- Confirm sidebar renders correctly per role (HOD sees Administration, Sales Exec does not) via `dso_render_menu()`.
