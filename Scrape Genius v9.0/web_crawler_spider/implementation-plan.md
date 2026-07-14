# Implementation Plan — LeadForge Pro

## 0. Stack

| Layer | Choice |
|---|---|
| Frontend | Next.js 14 (App Router) + Tailwind + shadcn/ui |
| Backend API | Next.js API routes (light) + FastAPI microservice (heavy jobs: geo-grid search, dedup) |
| DB | PostgreSQL + Prisma ORM |
| Queue | Redis + BullMQ |
| Auth | NextAuth (credentials + optional Google OAuth), tenant-scoped sessions |
| Exports | ExcelJS (.xlsx), native HTML templating, csv-writer |
| Lead sources | Google Places API (Text Search + Place Details), webhook ingestion, CSV import |
| i18n | next-intl (en, ar) with RTL via `dir` attribute + logical CSS props |
| Hosting | VPS/Docker (always-on service, per your no-sleep-serverless preference) |

---

## 1. Database Schema (Prisma)

```prisma
model Tenant {
  id            String     @id @default(cuid())
  name          String
  slug          String     @unique
  brandingLogo  String?
  brandingColor String?
  planTier      String     @default("starter") // starter | agency | white_label
  licenseKey    String     @unique
  createdAt     DateTime   @default(now())
  users         User[]
  campaigns     Campaign[]
  leads         Lead[]
}

model User {
  id        String   @id @default(cuid())
  tenantId  String
  tenant    Tenant   @relation(fields: [tenantId], references: [id])
  email     String   @unique
  passwordHash String
  role      String   @default("member") // owner | admin | member
  createdAt DateTime @default(now())
}

model Campaign {
  id         String    @id @default(cuid())
  tenantId   String
  tenant     Tenant    @relation(fields: [tenantId], references: [id])
  name       String
  module     String    // google_places | webhook | csv_import | enrichment
  keywords   String[]
  locations  String[]
  status     String    @default("queued") // queued | running | completed | failed
  createdAt  DateTime  @default(now())
  completedAt DateTime?
  leads      Lead[]
  jobs       Job[]
}

model Lead {
  id          String   @id @default(cuid())
  tenantId    String
  tenant      Tenant   @relation(fields: [tenantId], references: [id])
  campaignId  String
  campaign    Campaign @relation(fields: [campaignId], references: [id])
  source      String   // google_places | opt_in_form | csv | enrichment
  businessName String?
  contactName String?
  phone       String?
  email       String?
  address     String?
  website     String?
  category    String?
  lat         Float?
  lng         Float?
  rawPayload  Json?
  dedupeHash  String   // hash of normalized phone+email+name, unique per tenant
  createdAt   DateTime @default(now())

  @@unique([tenantId, dedupeHash])
}

model Job {
  id          String   @id @default(cuid())
  campaignId  String
  campaign    Campaign @relation(fields: [campaignId], references: [id])
  status      String   @default("pending") // pending | running | done | error
  progress    Int      @default(0)
  errorLog    String?
  createdAt   DateTime @default(now())
  updatedAt   DateTime @updatedAt
}

model ExportJob {
  id         String   @id @default(cuid())
  tenantId   String
  campaignId String
  format     String   // xlsx | csv | html
  filePath   String?
  status     String   @default("pending")
  createdAt  DateTime @default(now())
}

model LeadScore {
  id            String   @id @default(cuid())
  leadId        String   @unique
  lead          Lead     @relation(fields: [leadId], references: [id])
  score         Int      // 0-100
  scoreFactors  Json     // { hasWebsite: bool, reviewSentiment: number, ... }
  painPoints    String?  // AI-summarized pain points from Google Places reviews
  suggestedPitch String?
  techStack     String[] // detected from public site headers/scripts, e.g. ["WordPress","Cloudbeds"]
  domainAdmin   String?  // from public WHOIS lookup
  scoredAt      DateTime @default(now())
  modelUsed     String   @default("ollama-local") // ollama-local | groq | gemini-flash
}

model EmailCandidate {
  id           String   @id @default(cuid())
  leadId       String
  pattern      String   // e.g. "{first}.{last}@{domain}"
  candidate    String
  smtpVerified Boolean  @default(false)
  verifiedAt   DateTime?
  createdAt    DateTime @default(now())
}
```

---

## 2. Phased Roadmap

### Phase 1 — Foundation (Week 1)
- Repo scaffold: Next.js + Tailwind + Prisma + PostgreSQL (Docker Compose for local dev).
- Auth: NextAuth credentials provider, tenant-aware session/JWT.
- Multi-tenant middleware: every query scoped by `tenantId` from session.
- `.env.example` with all required keys.

### Phase 2 — Core Lead Engine (Week 2)
- Google Places module: keyword+location grid search, pagination handling, rate-limit backoff, field mapping into `Lead`.
- BullMQ job queue: campaign → job → worker → writes leads → updates progress.
- Dedup logic: hash on normalized phone/email/name per tenant.

### Phase 3 — Opt-in & Import Modules (Week 3)
- Webhook endpoint (`/api/webhooks/leads/:tenantSlug`) with signature verification, for client landing-page form submissions.
- CSV import: column-mapping UI, validation, bulk insert with dedup.

### Phase 4 — Frontend Dashboard (Week 3–4)
- Dashboard shell: sidebar module selector, live campaign cards, status badges.
- Campaign builder: keyword/location input, module picker, launch button.
- Live progress: polling or WebSocket job status.
- Leads table: filter, search, dedupe view, bulk select.
- EN/AR i18n with RTL flip (next-intl + `dir` switch + logical Tailwind classes `ps-4`/`pe-4` instead of `pl-4`/`pr-4`).

### Phase 5 — Export Engine (Week 4)
- ExcelJS export: one sheet per campaign, styled headers, auto-filter.
- HTML report generator (server-rendered template, downloadable).
- CSV export for lightweight cases.
- Export history log (`ExportJob` table).

### Phase 6 — White-Label & Multi-Tenant Polish (Week 5)
- Tenant branding: logo/color injected into layout via CSS variables.
- License key validation on tenant provisioning.
- Per-tenant plan-tier gating (module manifest JSON drives visible nav items — no DB call needed for UI).

### Phase 7 — Hardening & Deployment (Week 5–6)
- Rate limiting on webhook + API routes.
- Error logging (Sentry or simple structured logs).
- Docker Compose production config; deploy to VPS behind Cloudflare tunnel (per your existing edge setup).
- Backup strategy for Postgres.

### Phase 8 — Documentation
- `README.md`: setup, env vars, module descriptions.
- `enhanced-features.md`: full feature list, module breakdown table, roadmap, changelog — mirrors the structure you wanted, scoped to compliant sources.

### Phase 9 — AI Scoring & Enrichment (Week 6–7)
Adds an intelligence layer on top of leads you've already legitimately collected (Google Places, opt-in forms, CSV import). Nothing in this phase scrapes a new source or bypasses any platform's access controls — it only analyzes data already sitting in your `Lead` table plus data the lead's own public-facing site/business record exposes to any visitor.

- **Local AI lead scoring (Ollama):** Run `llama3.1`/`qwen2.5` locally, no per-call API cost. Input: structured lead fields + the review text Google Places' own API already returns (`reviews` field on Place Details — official, no scraping). Output: `LeadScore.score` (0–100) + `scoreFactors` JSON breakdown (has website, review sentiment, listing completeness, recency).
- **Pain-point summarization:** Same Ollama pass summarizes recurring complaints from the *official* Places reviews field into `painPoints`, and drafts a one-line `suggestedPitch`. Optional fallback to Groq/Gemini Flash free tiers if you want faster inference than local.
- **Tech-stack detection:** Fetch the lead's own public website's HTML `<head>`/`<script>` tags (a normal HTTP GET any browser makes) and pattern-match for known platforms (WordPress, Cloudbeds, Opera, SiteMinder, Shopify). This is equivalent to what BuiltWith/Wappalyzer do — reading what the site itself publicly serves, not extracting data the site owner restricted.
- **WHOIS enrichment:** Query the public WHOIS record for the lead's domain (registrant/admin contact where not privacy-shielded) via a standard WHOIS API. Public registry data, no auth bypass.
- **Email pattern + SMTP verification:** Generate standard candidate patterns (`first.last@domain`, `f.last@domain`) from a contact name you already have (e.g., from an opt-in form or public "About" page), then verify via SMTP handshake (industry-standard technique used by Hunter.io/ZeroBounce/NeverBounce) — never sends an email, just confirms mailbox existence.
- **n8n orchestration:** Wire `Lead created` → Ollama scoring → `LeadScore` write → CRM notification → (optional) WhatsApp/email follow-up trigger. Pure workflow automation over your own data.

---

## 3. Module Breakdown (Final)

| Module | Input | Extracted Fields | Legality Basis |
|---|---|---|---|
| Google Places Search | Keywords + locations | Business name, phone, address, website, category, geo | Official Google API, paid tier |
| Opt-in Form Capture | Webhook payload | Name, email, phone, message, source URL | Submitted directly by the lead |
| CSV/List Import | Uploaded file | Mapped columns | Agency/client-owned or purchased-with-consent data |
| Enrichment (optional) | Domain/name | Verified email, title | Licensed enrichment API (Hunter.io/Clearbit-class), used per their ToS |
| AI Lead Scoring | Existing `Lead` row + official Places reviews field | `score`, `painPoints`, `suggestedPitch` | Analysis of your own data + officially-returned API fields, no scraping |
| Tech-Stack Detection | Lead's public website | `techStack[]` (WordPress, Cloudbeds, etc.) | Reads what the site publicly serves to any visitor |
| WHOIS Enrichment | Lead's domain | `domainAdmin` | Public registry lookup |
| Email Pattern + SMTP Verify | Contact name + domain | Verified `EmailCandidate` | Standard industry technique (Hunter.io/ZeroBounce-class) |

---

## 4. Deliverables Checklist
- [ ] Prisma schema + migrations
- [ ] Auth + multi-tenant middleware
- [ ] Google Places integration module
- [ ] Job queue + worker
- [ ] Webhook ingestion endpoint
- [ ] CSV import UI + parser
- [ ] Dashboard (EN/AR RTL)
- [ ] Export engine (xlsx/csv/html)
- [ ] `.env.example`
- [ ] `enhanced-features.md`
- [ ] Deployment guide (Docker Compose + Cloudflare tunnel)
- [ ] Ollama local scoring pipeline + `LeadScore` writer
- [ ] Tech-stack detection module (public HTML header/script check)
- [ ] WHOIS enrichment module
- [ ] Email pattern generator + SMTP verification module
- [ ] n8n workflow: lead created → score → CRM/WhatsApp notify

## 5. Environment Variables (`.env.example`)
```
DATABASE_URL=postgresql://user:pass@localhost:5432/leadforge
NEXTAUTH_SECRET=
NEXTAUTH_URL=http://localhost:3000
GOOGLE_PLACES_API_KEY=
REDIS_URL=redis://localhost:6379
WEBHOOK_SIGNING_SECRET=
ENRICHMENT_API_KEY=
DEFAULT_LOCALE=en

# Phase 9 — AI Scoring & Enrichment
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=qwen2.5
GROQ_API_KEY=
GEMINI_API_KEY=
WHOIS_API_KEY=
SMTP_VERIFY_TIMEOUT_MS=5000
N8N_WEBHOOK_URL=
```

---

**Next step:** tell me which phase to generate first — I'd suggest starting with Phase 1 + 2 (foundation + Google Places module) since everything else builds on that, and I can produce the actual working code for that phase in this session.
