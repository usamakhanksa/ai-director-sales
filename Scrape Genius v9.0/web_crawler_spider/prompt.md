# Build Prompt — "LeadForge Pro" (Compliant Lead-Gen CRM)

Use this as the master prompt for yourself, a dev, or an AI coding assistant to build the system.

---

## Role

Act as an expert full-stack architect building a **multi-tenant, white-label-ready B2B lead-generation and CRM platform** called **LeadForge Pro**. This replaces "Scrape Genius Pro" — same UX ambition, same resale model, same export pipeline — but every data source is one you're actually allowed to collect, store, and resell. No scraping of Facebook/LinkedIn/Instagram/Twitter/classifieds profiles, no anti-bot/CAPTCHA bypass, no headless-browser stealth mode.

## Objective

Generate a complete, production-ready frontend + backend for a lead-gen CRM that:
1. Pulls verified business leads from **official, ToS-compliant APIs** (Google Places API primarily).
2. Captures **opt-in leads** from client-owned web forms (contact forms, landing pages).
3. Lets an agency manage multiple client workspaces (multi-tenant), each with isolated data.
4. Segregates, deduplicates, and exports leads by campaign/keyword into Excel/HTML/CSV.
5. Is white-label and resale-ready: branding, license keys, per-tenant billing hooks.

## System Architecture & Requirements

### 1. Frontend (UI/UX)
- **Design:** Modern Material Design, fully responsive, smooth transitions (Framer Motion or CSS transitions).
- **Localization:** Full English + Arabic, proper RTL layout via `dir="rtl"` + logical CSS properties (no hardcoded left/right).
- **Styling:** Tailwind CSS, componentized (not inline) for maintainability across a codebase this size.
- **Dynamic module loading:** Dashboard reads a JSON manifest of enabled modules per tenant/plan tier — no DB round-trip needed to render nav.
- **Auth:** NextAuth (or Laravel Sanctum if PHP stack preferred) — only authenticated, tenant-scoped users can launch a campaign or view leads.

### 2. Backend (Lead Engine)
- **Core:** Next.js API routes + FastAPI microservice for heavier jobs (Google Places pagination, geo-grid search, dedup logic), per established stack preference.
- **Job queue:** Redis + BullMQ (Node) or Celery (Python) for async campaign runs — same "Job Manager" concept as the original spec, just orchestrating API calls instead of browser automation.
- **Data sources (modular, pluggable):**
  - **Google Places API module** — keyword + location grid search → business name, phone, address, website, category, rating.
  - **Opt-in Form Ingestion module** — webhook receiver for client landing-page/contact-form submissions (Zapier/native webhook).
  - **CSV/Manual Import module** — agency uploads a client-provided or purchased-with-consent list.
  - **Enrichment module (optional)** — Clearbit/Hunter.io-style *paid, compliant* email-finding APIs that operate under their own ToS, not scraping.
- **Export formats:** `.xlsx` (ExcelJS), `.csv`, `.html` report, one sheet/section per campaign/keyword.
- **API:** Modular REST endpoints so leads can push into external CRMs (HubSpot, Zoho, generic webhook) later.

### 3. Modules to Implement
| Module | Input | Fields Extracted | Legality |
|---|---|---|---|
| Google Places Search | Keywords + location(s) | Business name, phone, address, website, category, geo-coords, rating | ✅ Official API, ToS-compliant |
| Opt-in Form Capture | Webhook from client site | Name, email, phone, message, source page | ✅ User-submitted, consented |
| CSV/List Import | Uploaded file | Whatever columns are mapped | ✅ Agency/client-owned data |
| Enrichment (optional) | Domain or name | Verified email, job title | ✅ Via licensed enrichment API only |

### 4. Business & Delivery Constraints
- Clean, commented, modular code — resale-ready.
- Multi-tenant DB isolation (row-level `tenant_id` scoping or schema-per-tenant).
- Detailed in-code documentation.
- No component of the system performs unauthorized scraping, CAPTCHA bypass, or bot-detection evasion, on any platform.

## Your Task
1. Propose the enhanced system architecture (already outlined below in the implementation plan).
2. Generate the full DB schema (Prisma).
3. Generate backend module code (Google Places integration, job queue, export engine) module-by-module.
4. Generate the frontend dashboard (module selector, campaign builder, live progress, export wizard) with EN/AR RTL support.
5. Provide `.env.example` and deployment notes.

Proceed directly to generating each piece — no scraping module will be produced under any framing of this prompt.
