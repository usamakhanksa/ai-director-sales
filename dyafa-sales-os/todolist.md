# Dyafa Sales OS — Full Enhancement To-Do List
Generated from a full-tab review of `URL/Route/Menu Reference` (dated 2026-07-22).

## Important caveat before anything else

I only have the one reference document that was pasted into this conversation.
It repeatedly points to three other files — `enhance.md`, `tasklist.md`, and
`implementation.md` — as the real source of truth for the roadmap, the fix
log, and design intent. None of those were uploaded, and I don't have access
to the actual PHP controllers, views, or database schema. So this list is
built entirely from what the reference document itself documents (its
"Working" / "Partial" / "CONFIRMED LIVE BUG" annotations and the CRUD
matrix) — not from reading your live code.

**If you want the DB migrations/seeds and backend code changes to be real,
runnable artifacts** (not just a plan), I'll need one of:
- the current `application/config/dso_menu.php`, the relevant controllers/models, and your migrations folder, or
- `enhance.md` + `tasklist.md` + `implementation.md` + a schema dump / `db_seeds` folder.

I can start the frontend redesign work right now without any of that, since it's UI-layer.

---

## 0. Documentation inconsistency found during review

Worth resolving before anything else, because it changes whether item 1.1 below is even real:

- The top of the doc states the `dyafa/leads/mine|unassigned|ai` 404 bug was **fixed**, verified against `Leads.php`.
- The `Leads.php` section further down still documents it as a **"CONFIRMED LIVE BUG (re-verified 2026-07-22)"** — tabs still 404 in the browser due to a view/controller mismatch.

These two statements contradict each other in the same file. Check which is actually true in the live app first — it determines whether 1.1 is a 10-minute fix or already closed.

---

## 1. Critical bugs (fix first)

### 1.1 Leads sub-tab links 404 (My Leads / Unassigned / AI Generated)
- **Cause:** `Leads.php:32-37` builds `dso_tabs` URLs as `dyafa/leads/mine`, `dyafa/leads/unassigned`, `dyafa/leads/ai`. The catch-all route rewrites these to `class=Leads, method=mine`, etc. — but those methods don't exist and there's no `_remap()`. The working logic lives at `dyafa/leads/index/mine` etc. instead.
- **Fix (one line, no logic change needed):** either rewrite the tab URLs in the view/partial to `dyafa/leads/index/mine` format, or add a thin `_remap()` / three one-line wrapper methods (`mine()`, `unassigned()`, `ai()`) on `Leads.php` that forward to `index($scope)`.
- **Priority:** Critical — this is a visible, click-through-and-it-breaks bug on a primary sales screen.

### 1.2 Legacy dead routes to nonexistent `Api` controller
- `getProperty/(:any)/(:any)` and `getAllProperties[/(:any)]` in `routes.php` point at an `Api` controller that doesn't exist anywhere in the codebase — 404 if ever hit.
- **Decision needed, not a code fix:** either delete the two route lines (if nothing external still calls them), or scope building a real `Api` controller into item 4 below if a legacy front-end still depends on them.
- **Priority:** Low/cleanup, but cheap to resolve — worth a quick grep of any legacy front-end for calls to these paths before deciding.

---

## 2. Data-integrity gap: inconsistent delete pattern across entities

Leads already does this right (`Dso_leads_model::soft_delete()`). Eight other entities still hard-delete, which means **no undo, no audit trail, and any FK-linked history (a contract tied to a deleted account, a reservation tied to a deleted property) can dangle or cascade-fail**:

| Entity | Current delete | Risk |
|---|---|---|
| Contracts | Hard delete | Deletes revenue/legal history |
| Corporate Accounts | Hard delete | Orphans contracts/reservations/collections tied to the account |
| Adhoc Sales | Hard delete | Loses proposal/event history |
| Properties | Hard delete | Orphans rates, reservations, blackout dates |
| Collections | Hard delete | **Deletes financial/payment records** — highest risk of this group |
| Targets | Hard delete | Loses historical target-vs-actual trail |
| Roles | Hard delete | Can strand users who reference a deleted role_id |
| Teams | Hard delete | Can strand users/territory assignments |
| AI Provider Config | Hard delete | Low risk, config-only |

**Todo:** standardize on the Leads pattern — add a `deleted_at` (or `status='deleted'`) column + `soft_delete()` model method to each, and filter it out of default list queries. Do **Collections and Corporate Accounts first** (financial + relational blast radius), Roles/Teams/AI Config last (lowest risk).

*(Note: Reservations' cancel-only / no hard-delete, Activities' no-edit-no-delete, Notifications' system-generated-only, and Users'/Portal-Users' toggle-status-only are called out in the source doc as intentional design, not gaps — leaving those alone.)*

---

## 3. Feature completion gaps

### 3.1 AI Assistant — `predictions()` and `next_best_actions()` are UI shells
- Both views render with a friendly empty state, but no generator (cron or on-demand) actually produces `Prediction` or `NextBestAction` rows yet — only the recommendation generator (`generate_all_recommendations()`) is wired up.
- **Todo:** either build the two missing generators (heuristic first, LLM-enhanced later, matching the existing recommendation pattern), or if these are intentionally future-scoped, relabel them in-app so "Partial" doesn't read as broken to a user who clicks in.

### 3.2 Reports → Reporting Platform push is only wired on 7 of 14 reports
- `push_to_reporting/{report}` exists and works, but isn't hooked up to all 14 report types.
- **Todo:** get the list of which 7 are done vs. missing (this reference doc doesn't say which), then wire the remaining ones — should be mechanical once the pattern from the first 7 is copied.

### 3.3 Corporate Portal Users have no `edit()`
- `Portal.php` supports `add()` and `toggle_status()` for company users but no edit — so a CorporateAdmin can't fix a typo'd name/email without deactivating and re-adding.
- **Todo:** add `Portal::user_edit($id)`, gated the same as `users()`/`user_add()` (CorporateAdmin sub-role only).

### 3.4 No real JSON/REST API layer
- Every route is server-rendered HTML/redirect; the only true JSON response in the whole app is `aiconfig/test`.
- **Todo (larger, scope it separately):** if a mobile app, headless frontend, or third-party integration is actually on the roadmap, this needs a deliberate API layer (auth via token not session, versioned endpoints, JSON serializers for each model) rather than bolting JSON onto existing controllers.

### 3.5 Admin/Integrations config has no DB table (file-based via `var_export`)
- `Admin/Integrations.php` reads/writes `application/config/dso_integrations.php` directly. Works, but: no change history, no rollback, and a bad POST could corrupt a live config file.
- **Todo:** consider migrating to a `dso_integrations` config table with the same key/value shape, keeping the file as a cached fallback — gives you an audit trail on who changed an integration mode and when.

---

## 4. Frontend redesign — "beautiful, attractive, animated" pass

This is UI-layer work I can start immediately without backend access. Below is a per-screen todo list translating "eye-catching, realistic, animated" into concrete, buildable pieces, grouped by the tabs in the reference doc:

| Screen / Tab | Redesign ideas |
|---|---|
| **Dashboard** (main / daily / HOD / team) | Animated count-up KPI cards, gradient/glassmorphism stat tiles, chart entrance animations (bars growing, lines drawing in), live-feel "last updated" pulse indicator |
| **Leads** (list + mine/unassigned/AI tabs) | Color-coded status/source badges, animated tab-switch transitions, skeleton loaders on filter change, inline score visualization (progress ring) for `Dso_lead_scoring` output |
| **Contracts / Adhoc board** | Kanban view with smooth drag-and-drop (already AJAX-backed for adhoc — just needs a modern kanban UI on top), card-flip or expand animation on click |
| **Reservations calendar** | Animated month transitions, drag-drop with a "snap" micro-interaction, color-coded check-in/out states, hover previews |
| **Collections (aging, credit limits)** | Animated bucket bar chart (0-30/31-60/61-90/90+), color gradient by risk, CSV export with a subtle download-progress animation |
| **Reports (all 14)** | Shared interactive chart component (hover tooltips, animated draw-in), consistent export button treatment across all 14 instead of one-off styling |
| **AI Assistant / Recommendations** | Card-based recommendation feed with dismiss/action swipe-style micro-interactions, empty states redesigned to feel intentional rather than blank (relevant to 3.1 above) |
| **Portal (corporate client)** | Distinct, more consumer-friendly visual identity from the staff-side (it's a different audience), animated search/availability results |

**Suggested next step:** I can build a live interactive mockup (HTML/React artifact) of one flagship screen — Dashboard or the Leads board — right now, as a concrete style direction, before touching every one of the ~40 screens. That's usually faster than describing a design system in prose.

---

## 5. DB migrations & seeds — what's actually needed

I can't write real migration files without the schema, but here's the concrete migration list this review implies, schema-agnostic:

1. `deleted_at TIMESTAMP NULL` (+ index) added to: `contracts`, `corporate_accounts`, `adhoc_sales`, `properties`, `collections`, `targets`, `roles`, `teams`, `ai_provider_config` — for item 2.
2. New/confirm-existing tables for `predictions` and `next_best_actions` if they don't already exist as distinct tables from `ai_recommendations` — for item 3.1.
3. A `reporting_platform_pushed` flag or log table per report type (or a generic `report_push_log`) so item 3.2's rollout can be tracked per report.
4. Optional: `dso_integrations` table (key, value, updated_by, updated_at) to replace/back the file-based config in item 3.5.
5. Seeds: sample data per entity covering every filter/scope this doc documents (e.g., leads seeded across `mine`/`unassigned`/`ai` source, reservations seeded across `pending`/`checkins_today`/`checkouts_today`) so the redesigned UI in section 4 has real states to render against instead of empty screens.

**To turn this into runnable migration/seed files**, send me the current migrations folder (so I match your existing naming/framework convention — this looks like it could be plain CI3 SQL migrations or a separate migration library) or a schema dump.

---

## What I'd suggest as the actual next step

Given the scope here (roughly 20 modules, full frontend + backend + DB), I don't think it's useful for me to guess my way through backend code and migrations for a codebase I can't see. Two things I *can* do productively right now without any more input from you:

- **A.** Build a live animated mockup of the Dashboard or Leads screen as the visual style direction for the whole redesign.
- **B.** Draft the schema-agnostic soft-delete migration SQL for the 8 entities in section 2, written generically enough to adapt once you confirm your migration tool/format.

Let me know which you'd like first — or send over the codebase/schema and I'll go deeper on the real fixes.
