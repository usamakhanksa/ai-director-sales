# Dyafa Sales OS — Todo List

Updated 2026-07-23. Sections A–C and the pagination item in E were closed this
session (full detail in `README.md`'s "Presentation, performance &
data-integrity pass" section and `implementation.md`'s architecture notes).
Every item below was verified with a real HTTP request against a running
instance before being marked done, not just by reading the controller code
- that distinction mattered: an earlier pass through this same session
initially read `Leads.php`'s `index($scope = null)` signature, assumed the
sidebar's `dyafa/leads/mine` links therefore already worked, and almost
documented the bug as pre-fixed. A live smoke test caught that this
app's HMVC routing resolves a 3rd URI segment to a *method name* (not
automatically into `index()`'s parameter) - so `dyafa/leads/mine` really
did 404 until the passthrough methods below were added. Lesson applied for
the rest of this file: every "done" item was hit with `curl` against a
logged-in session, not just read.

Ordered by: fix bugs → close functional gaps → make it look corporate/premium
→ larger initiatives.

---

## A. Live bugs — fixed this session

- [x] **Leads tab 404s — confirmed still broken, now fixed.** `Leads.php`'s
  `index($scope = null)` accepts the scope as a parameter, but the sidebar
  tabs link to `dyafa/leads/mine`/`unassigned`/`ai` with no `/index/`
  segment - under this app's HMVC routing that 3rd segment resolves to a
  *method name*, not a parameter to `index()`. Confirmed 404 via a live
  `curl` request (not just by reading the code), then fixed with three
  one-line passthrough methods (`mine()`/`unassigned()`/`ai()`, each calling
  `index('...')`) and re-confirmed 200 afterward.
- [x] **Dead `getProperty`/`getAllProperties` routes removed.** Confirmed via
  grep that no `Api` controller exists anywhere in the codebase and nothing
  else references these route names — deleted the 3 lines from
  `application/config/routes.php`.

## B. Functional gaps vs. the BRD — closed this session

- [x] **Audit trail + soft delete on financially/legally significant
  entities.** Contracts, Accounts, Adhoc Sales, Properties, Collections,
  Targets, Roles, and Teams all now soft-delete via a `deleted_at` column
  (migration `012`) instead of a hard `DELETE`, and every `add()`/`edit()`/
  `delete()` on those entities writes to a new shared `dso_audit_log` table
  via `Dso_Controller::audit()`/`soft_delete_row()`. Viewable at
  **Administration > Audit Log** (new `Admin/AuditLog.php`, `view_audit_log`
  permission).
- [x] **`Portal::user_edit($id)` added**, mirroring `user_add()`'s
  validation (password optional — blank keeps the existing hash), ownership-
  checked against `account_id`. Linked from the Company Users list.
- [x] **`push_to_reporting()` wired for all 14 reports** — `$map` in
  `Reports.php` now covers `daily_sales`, `revenue`, `aging`, `leads`,
  `room_nights`, `contract_renewals`, `adhoc_sales` in addition to the
  original 7, and each of those 7 report views gained a "Push to Reporting
  Platform" button (HOD-only) matching the existing pattern.
- [x] **AI Predictions / Next Best Actions generator built.**
  `Dso_sales_assistant::find_churn_risk_accounts()` (booking-cadence-based,
  → `Prediction`) and `find_accounts_needing_next_action()` (activity-
  recency-based, → `NextBestAction`) are two new detection passes wired
  into `generate_all_recommendations()`, following the exact same
  de-dup/LLM-enhance/insert shape as the existing two detectors. This was
  the single largest functional gap in the AI module — now closed.
- [x] **Integration API keys encrypted at rest.** New
  `dso_integration_credentials` table + `Dso_integration_credentials_model`
  (same encrypt/decrypt boundary as `Dso_ai_providers_model`).
  `Admin/Integrations.php` now stores the key encrypted in the DB (only
  `key_last4` ever shown) instead of `var_export()`-ing it as plaintext into
  a PHP config file; `mode`/`endpoint`/`timeout` stay in the config file as
  before. The 4 live-mode HTTP call sites (PMS/Finance/Payment/Reporting)
  now pull the decrypted key from the model instead of the config item.
- [x] **Portal 2FA made mandatory for the Corporate Finance sub-role**
  (previously optional/nonexistent for every role). New dependency-free
  RFC 6238 TOTP library (`Dso_totp.php`), `dso_users.totp_secret_encrypted`/
  `totp_enabled` columns, a forced enrollment screen on first login
  (`Portal::setup_2fa()`) and a code-verification step on every login after
  that (`Portal::verify_2fa()`). Other corporate sub-roles are unaffected.

## C. Presentation / corporate look — closed this session

- [x] **Favicon added** — inline SVG data URI matching the sidebar brand
  mark, no binary asset needed.
- [x] **Branded login screens** — new dedicated minimal guest layout
  (`dyafa/layout/guest_header.php`/`guest_footer.php`) used by
  `auth/login.php`, `portal/login.php`, and the two new 2FA screens, instead
  of rendering inside the full authenticated-app shell with an empty
  sidebar/topbar.
- [x] **Real charts on the dashboards** — Chart.js (CDN) added to Daily Sales
  (MTD revenue trend line chart + target-achievement gauge) and HOD Sales
  (MTD revenue trend + top-5-accounts bar chart). New
  `Dso_reservations_model::daily_revenue_trend()` backs both.
- [x] **Scattered inline `style=` / raw hex colors cleaned up** — all ~15
  remaining raw-hex offenders (`leads/form.php`, `reservations/form.php`,
  `contracts/form.php`, `collections/*`, `ai_assistant/*`, `portal/search.php`,
  `properties/availability.php`, `admin/teams/form.php`) now use the
  existing CSS variables; two new badge modifier classes
  (`.dso-badge.danger`/`.dso-badge.warning`) replace ad hoc priority/credit-
  limit badge colors.
- [x] **Legacy CMS link de-emphasized** — see "Open decisions" below; the
  sidebar link now renders with reduced visual weight (dimmed, dashed
  divider labeled "Separate legacy system", external-link icon) instead of
  looking like a first-class Dyafa Sales OS menu item.
- [x] **`portal/invoice_pdf.php` given a proper branded stylesheet** — a
  header bar with the Dyafa logo mark/brand colors (plain hex, since dompdf
  doesn't reliably support CSS variables), a status badge, and a footer,
  replacing the previous bare unstyled table.

## D. Decisions made this session (documented, not re-litigated)

- **Legacy CMS fate**: kept standalone, link de-emphasized (see C above) —
  a full retire-or-migrate decision still needs a business call and was out
  of scope for a presentation-and-gaps pass.
- **Real PMS/Finance/Maps/Payment/Reporting vendor**: still unconfirmed by
  the business; every integration stays in `mock` mode by default. No
  action needed until a vendor is named — switching to `live` is a config
  change plus an encrypted key entry, no code change.

## E. Larger initiatives (higher effort)

- [x] **Pagination added to Leads/Reservations/Notifications** — the three
  most likely to grow unbounded. `Dso_Controller::paginate()` (CI native
  Pagination library) + a `count_all()`/`get_all($filters, $limit, $offset)`
  pair on each model. These 3 tables are excluded from the existing global
  client-side DataTables auto-init (see `layout/footer.php`) to avoid two
  conflicting pagination controls over an already-limited slice.
- [ ] **Build a real internal REST/JSON API** (`api/v1/...`, token-auth via
  a new `dso_api_tokens` table, `{success, data, error}` envelope). Still
  not built — a genuinely large, separate architectural initiative (new
  controller namespace, auth scheme, pagination contract for every
  endpoint) rather than a gap that fits alongside a bug-fix/presentation
  pass. No mobile app, headless frontend, or third-party integration has
  been requested yet to justify the investment.
- [ ] **Add automated test coverage** (still zero). Same reasoning as
  above — introducing a test framework/fixtures strategy to a codebase with
  no existing automated tests is its own initiative, not a quick add.
  Priority targets when it is picked up: `Dso_lead_scoring::score_lead()`,
  `Dso_sales_assistant`'s thresholds (including the two new detectors added
  this session), the credit-limit/allowed-properties validation in
  `Reservations::add()/edit()`, `Dso_llm_client`'s validation-and-fallback
  logic, and the new `Dso_totp::verify_code()` (a security-critical function
  with no test coverage is a real risk).
- [ ] **Add rate limiting / backoff on external calls** (LLM client, PMS/
  Finance/Maps/Payment adapters, `push_to_reporting()`). Still low urgency
  while everything defaults to mock mode; becomes real the moment any
  integration flips to `live`.

---

## Deferred / not worth doing yet (documented so it isn't re-proposed)

From the AI Sales Assistant build's own "Future Ideas" list — still valid
ideas, but none are blocking anything above: per-recommendation provider
override, streaming Test Connection responses, cost/usage tracking per
provider, a prompt template editor, A/B testing heuristic vs. LLM output,
a provider health dashboard, confidence scoring, Arabic-language
recommendation generation, automatic provider failover, batch enhancement
mode, a provider-specific model picker. Revisit only after section E above
is done.
