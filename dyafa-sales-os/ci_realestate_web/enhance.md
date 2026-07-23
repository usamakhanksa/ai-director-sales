# Dyafa Sales OS — Enhancement Roadmap

**Update 2026-07-23**: every P0 and P1 item below, plus the pagination item
under P2 §2, was closed this session — see `todolist.md` sections A/B/C/E
for verified detail and `README.md`/`implementation.md` for full
architecture notes. One correction to this file's own prior claim: P0 §1
("Leads tab links 404") was already fixed by an earlier session before this
document was last updated — re-verified against the live `Leads.php`
before starting, not re-fixed. The rest of this file is kept as-is below
for historical context (what the gap analysis looked like before this
session), with `[RESOLVED 2026-07-23]` markers inline.

Updated 2026-07-22 (full-system review). This is the single prioritized
backlog for the whole app, replacing the narrower per-feature notes that used
to live at the top of this file (moved to "Completed work log" below). Cross-
referenced against `apiurl.md` (route/CRUD inventory), `tasklist.md` (build
history), and `remainingtask.md` (last closed initiative).

The system is in genuinely good shape: 20+ controllers, full RBAC with
DB-backed roles/permissions, territory-scoped Teams, 14 real SQL reports with
CSV export, three-mode (live/mock/off) integrations for PMS/Finance/Maps/
Payment/Reporting, an LLM-backed AI Sales Assistant with 6 provider adapters,
and a self-service Corporate Portal. What follows is what's actually left,
ranked by impact and confirmed against the live code (not guessed).

## P0 — Confirmed live bugs (fix first, small/cheap)

1. **[ALREADY FIXED before 2026-07-23 — re-verified, not stale] Leads tab links 404** — `application/controllers/dyafa/Leads.php:32-37`
   builds tab URLs as `dyafa/leads/mine|unassigned|ai`, but `index($scope)`
   only accepts the scope as a 3rd URI *parameter*, not a method name, and
   there's no `_remap()`. Clicking "My Leads" / "Unassigned" / "AI Generated"
   in the sidebar-rendered tab bar (`dyafa/partials/list_tabs.php`, included
   from `leads/list.php:5`) 404s today. **Fix:** add three one-line passthrough
   methods to `Leads.php` —
   ```php
   public function mine() { $this->index('mine'); }
   public function unassigned() { $this->index('unassigned'); }
   public function ai() { $this->index('ai'); }
   ```
   This is strictly additive, touches no other file, and needs no route or
   view change. (Full root-cause trace in `apiurl.md`.)

2. **[RESOLVED 2026-07-23 — routes deleted] `getProperty` / `getAllProperties` legacy routes are dead** —
   `application/config/routes.php:49-51` route to an `Api` controller that
   does not exist anywhere in the codebase. Either delete these two route
   lines (if nothing external calls them) or build the controller (see P2 §1
   below, since this is really the seed of a real API layer). Confirm via
   grep for external references (mobile app, cron, docs) before deleting.

## P1 — Real gaps worth closing (data integrity / operability)

3. **[RESOLVED 2026-07-23 — migration 012 + `Dso_Controller::audit()`/`soft_delete_row()`] No soft delete or audit trail on financially/legally significant
   entities.** Contracts, Corporate Accounts, Adhoc Sales, Properties,
   Collections, Targets, Roles, and Teams all use a hard
   `$this->db->delete()` with no history retained. Leads is the only entity
   with `soft_delete()`. For a sales/finance system this matters two ways:
   - A finance-relevant row (a Contract or a Collection payment record) being
     hard-deleted with no trace is a real audit/compliance risk.
   - **Recommendation:** add a generic `deleted_at` column + a shared
     `Dso_Controller::soft_delete_row($model, $table, $id)` helper (or a thin
     trait) so every `delete()` method becomes one line, matching the
     `Leads::delete()` pattern already established. Start with Contracts and
     Collections (highest financial exposure), then Properties/Accounts.
   - Pair this with a lightweight `dso_audit_log` table (`user_id`, `table`,
     `row_id`, `action`, `before_json`, `after_json`, `created_at`) written
     from one central point (e.g. a `Dso_Controller::audit()` call at the top
     of every `add()/edit()/delete()`), which also closes the "Audit trail
     for provider config changes" idea already flagged under AI Config below.

4. **[RESOLVED 2026-07-23 — `Portal::user_edit()`] Corporate Portal users have no `edit()` method.** `Portal.php` has
   `users()`, `user_add()`, `user_toggle_status($id)` but nothing to edit an
   existing company user's name/email/sub-role/capabilities once created —
   a CorporateAdmin who mistypes an email or needs to change a sub-role has
   no path except deactivate + recreate. Add `Portal::user_edit($id)`
   mirroring `user_add()`'s validation, ownership-checked against
   `account_id` like every other Portal method.

5. **[RESOLVED 2026-07-23 — all 14 wired] `push_to_reporting()` is only wired on 7 of the 14 reports.**
   `Reports.php:33-57` implements a generic `push_to_reporting($report)` with
   a `$map` array that currently only covers `reservations`, `contracts`,
   `opportunities`, `activities`, `corporate_accounts`,
   `property_performance`, `ai_recommendations`. Extend `$map` to cover the
   remaining 7 (`daily_sales`, `revenue`, `aging`, `leads`, `room_nights`,
   `contract_renewals`, `adhoc_sales`) for parity — trivial, same pattern,
   just needs each report's underlying model+method pair added to `$map`.

6. **[RESOLVED 2026-07-23 — `find_churn_risk_accounts()`/`find_accounts_needing_next_action()`] AI Predictions / Next Best Actions have views but no generator.**
   `AiAssistant::predictions()` and `next_best_actions()`
   (`application/controllers/dyafa/AiAssistant.php:77,100`) filter
   `dso_ai_recommendations` by `type` values that nothing currently inserts —
   confirmed empty-state-only in `tasklist.md`'s own "Remaining Backlog".
   This is the single largest functional gap left in the AI module. Building
   it means extending `Dso_sales_assistant::generate_all_recommendations()`
   with two new detection passes (e.g. "predicted churn risk" from activity
   recency + reservation cadence, "next best action" from the existing
   heuristic's suggested_next_action field promoted to a first-class
   recommendation type) — same shape as the existing inactive-account/
   expiring-contract passes, so no new architecture needed, just two more
   detectors.

7. **[RESOLVED 2026-07-23 — `dso_integration_credentials`, encrypted] `Admin/Integrations.php` writes plaintext API keys into a PHP file on
   disk via `var_export()`** (`_save()`, lines 44-71) with no encryption —
   inconsistent with `Dso_ai_providers_model`, which explicitly encrypts
   provider API keys and only ever displays `key_last4`. Any git commit,
   backup, or shared-hosting file read exposes these keys in plaintext.
   **Recommendation:** route PMS/Finance/Maps/Payment/Reporting API keys
   through the same `encrypt_key()`/`decrypt_key()` boundary already built
   for AI providers (`application/models/dyafa/Dso_ai_providers_model.php`),
   storing them in a small `dso_integration_credentials` table instead of the
   config file, while keeping `mode`/`endpoint`/`timeout` in the file as
   non-secret settings.

## P2 — Larger, higher-effort initiatives

1. **Build a real internal REST/JSON API layer.** Confirmed: outside
   `dyafa/aiconfig/test`, the entire app is server-rendered HTML with no
   JSON API (see `apiurl.md`'s "Important note on API"). If there's any
   plan for a mobile app, a headless frontend, or third-party integrations
   (the BRD's Section 22 only covers *outbound* calls to PMS/Finance, not
   inbound), this is the biggest architectural gap. Suggested shape for a
   CI3 app without a framework rewrite:
   - A new `application/controllers/api/` namespace (versioned:
     `api/v1/leads`, `api/v1/reservations`, etc.), each a thin controller
     reusing the existing `Dso_*_model` classes (no duplicate query logic).
   - Token-based auth (a simple `Authorization: Bearer <token>` checked
     against a new `dso_api_tokens` table, scoped to a user + optional
     read-only flag) since session-cookie auth doesn't work for external
     clients.
   - Consistent JSON envelope (`{success, data, error}`) and real pagination
     (`?page=&per_page=`) on every list endpoint — currently no `index()`
     method anywhere paginates; they all `get_all()` unbounded, which will
     become a real performance problem as `dso_leads`/`dso_reservations`
     grow.
   - This also gives you a clean place to finally implement (or delete) the
     dead `getProperty`/`getAllProperties` legacy routes from P0 §2.

2. **[RESOLVED 2026-07-23 for Leads/Reservations/Notifications, the 3 named below] Pagination on list endpoints.** Every `index()` across the 20+
   dyafa controllers calls a model's `get_all($filters)` with no `LIMIT`/
   `OFFSET`. Fine at demo-data scale, a real problem once `dso_leads`,
   `dso_reservations`, or `dso_notifications` reach thousands of rows —
   full table scans rendered as one giant HTML table per page load.
   Recommend a small shared `Dso_Controller::paginate($model, $method,
   $filters, $per_page = 25)` helper (CI's native `Pagination` library) added
   incrementally, starting with Leads/Reservations/Notifications (the three
   most likely to grow unbounded fastest).

3. **Automated test coverage is zero.** Confirmed in `remainingtask.md`'s
   own verification notes: "no automated test suite exists in this repo."
   Every verification step across all prior sessions has been manual smoke
   testing per role. For a system this size (RBAC + territory scoping +
   financial calculations in Collections/Targets/Reservations credit-limit
   checks), at minimum add PHPUnit coverage for the pure-logic pieces that
   don't need a DB fixture: `Dso_lead_scoring::score_lead()`,
   `Dso_sales_assistant`'s detection thresholds, the credit-limit/allowed-
   properties validation in `Reservations::add()/edit()`, and
   `Dso_llm_client::_apply_llm_candidate_fields()`'s validation-and-fallback
   logic (highest-value target since it silently swallows LLM output that
   fails validation — a regression there would be invisible without a test).

4. **Rate limiting / API-key rotation for external LLM and integration
   calls** — no throttling exists on `Dso_llm_client`, the PMS/Finance/Maps/
   Payment mock-or-live adapters, or `push_to_reporting()`. A misconfigured
   "Generate Now" button mashed repeatedly, or a live-mode integration with
   a slow upstream, has no backoff/dedup. Low urgency while everything
   defaults to mock mode, but becomes a real cost/reliability risk the
   moment any integration flips to `live`.

5. **Legacy CMS (`application/controllers/admin/*`, `Main`/`Property`/`Blog`/
   `Payment` front-end) is a separate, older codebase living alongside the
   Dyafa Sales OS module** and was out of scope for every prior session's
   work. It has its own `manage/add_new/edit/delete` CRUD pattern (see
   `apiurl.md` if that inventory is re-added) with no RBAC integration, no
   shared session/auth with `Dso_Controller`, and the two dead API routes
   from P0 §2 belong to it. Worth an explicit decision: keep it running
   standalone (current state), retire it, or eventually migrate its
   front-end (property listings, blog, packages/subscriptions) to read from
   the same `dso_properties` table Dyafa Sales OS now owns, instead of two
   independent property data models.

## Completed work log (kept for history — do not re-implement)

Full build history lives in `tasklist.md` (all core CRUD + BRD sidebar gap
closure) and `remainingtask.md` (RBAC/Teams/Admin module architecture). The
AI Sales Assistant multi-provider LLM build (originally the sole content of
this file) is summarized below since its "Future Ideas" list is still a live
source for items 6 and 7 above.

### AI Sales Assistant — Multi-Provider LLM Config + Palette Redesign

All 11 build-order steps complete: `dso_llm.php` provider metadata (11
providers), `dso_ai_providers` schema + model with encrypt/decrypt boundary
and exactly-one-default enforcement, adapter library (`Llm_adapter_interface`
+ OpenAI-compatible/Anthropic/Gemini/Cohere adapters), `Dso_llm_client` facade
with `enhance_recommendation()` (never throws, always falls back to the
heuristic) and `test_connection()`, `AiConfig` controller + views (HOD-only,
Test Connection AJAX), `Dso_sales_assistant::generate_all_recommendations()`
wrapping both recommendation loops, `Cron::dso_generate_ai_recommendations()`
slimmed to a thin wrapper, `AiAssistant::generate()` (HOD-only), and a 5-color
CSS-variable palette restyle of the shared header.

Self-review confirmed at the time: decrypted API keys never echoed to any
view (only `key_last4`), `set_default()` enforces exactly-one-default,
role gates confirmed on `AiConfig` constructor and `AiAssistant::generate()`.

Remaining ideas from that session not yet built (superseded/tracked above
where applicable):
- Per-recommendation model override (pick a different provider for a single
  regenerate).
- Streaming responses for Test Connection / future interactive prompts.
- Cost & usage tracking per provider (`dso_ai_usage_log` table).
- Prompt template editor (versioned, admin-editable).
- A/B testing heuristic vs. LLM output on actioned/dismissed rates.
- Provider health dashboard (last_test_status/last_tested_at/failure rate).
- Confidence scoring (LLM returns `CONFIDENCE: high|medium|low`).
- Multi-language recommendation generation (Arabic prompt context).
- Automatic provider failover (secondary provider before full heuristic
  fallback).
- Rate limiting / dedup on the LLM call layer (see P2 §4 above — now
  generalized to all external integrations, not just LLM).
- Batch enhancement mode (one combined prompt for all pending drafts).
- Provider-specific model picker (dropdown from each provider's `/models`
  endpoint instead of free text).
- Audit trail for provider config changes (see P1 §3 above — now
  generalized to a shared `dso_audit_log`, not AI-provider-specific).
- Extend the palette restyle to remaining hardcoded inline badge colors in
  `ai_assistant/list.php`, `leads/list.php`, etc.
