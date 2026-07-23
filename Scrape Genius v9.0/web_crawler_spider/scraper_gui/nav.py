"""
nav.py
=======
Single source of truth for the sidebar navigation and the per-tool page
configs that main.py renders through templates/job_form.html and
templates/instant_tool.html. Every feature in the README now has a real
route backed by real logic - there are no "Soon" placeholders left.

A small number of features are BYO-key by nature (Google Custom Search,
Hunter.io professional contacts) because they wrap a real third-party
paid API this app cannot ship its own key for - those are still real,
functional integrations once you supply your own key, not stubs.
"""

NAV_GROUPS = [
    {
        "title": "Search & Discovery",
        "items": [
            {"label": "Search Engines (Google/Bing/Yahoo/DDG)", "url": "/tools/search-engines"},
            {"label": "Multi-Engine Unified Search", "url": "/tools/multi-search"},
            {"label": "Google CSE Key Management", "url": "/tools/cse-keys"},
            {"label": "Search Dorks Generator", "url": "/tools/dorks"},
        ],
    },
    {
        "title": "Social Media",
        "items": [
            {"label": "Instagram", "url": "/tools/instagram"},
            {"label": "Facebook", "url": "/tools/facebook"},
            {"label": "LinkedIn", "url": "/tools/linkedin"},
            {"label": "Twitter / X", "url": "/tools/twitter"},
        ],
    },
    {
        "title": "Business Data & Directories",
        "items": [
            {"label": "B2B Directories (IndiaMart/JustDial/Sulekha)", "url": "/tools/b2b-directory"},
            {"label": "Google Maps Extractor", "url": "/tools/google-maps"},
            {"label": "Classified Ads (Haraj + MENA)", "url": "/tools/classifieds"},
            {"label": "CRM Connections", "url": "/tools/crm"},
        ],
    },
    {
        "title": "Web & File Data Extraction",
        "items": [
            {"label": "Deep Website Crawler", "url": "/tools/website-crawler"},
            {"label": "WHOIS Domain Lookup", "url": "/tools/whois"},
            {"label": "Document Scraper (.txt/.csv/.docx)", "url": "/tools/document"},
            {"label": "Image Scraper (OCR)", "url": "/tools/image-ocr"},
            {"label": "Contact Scraper", "url": "/tools/contact"},
        ],
    },
    {
        "title": "AI & Enrichment",
        "items": [
            {"label": "AI Enrichment (Lead Scoring)", "url": "/tools/ai-enrichment"},
            {"label": "AI Lead Qualifier", "url": "/tools/lead-qualifier"},
            {"label": "Professional Contact Finder (Hunter.io)", "url": "/tools/professional-contacts"},
            {"label": "Zero-Cost AI Scraper (r.jina.ai)", "url": "/tools/zero-cost-ai"},
        ],
    },
    {
        "title": "News & Verification",
        "items": [
            {"label": "Google News RSS Scraper", "url": "/tools/google-news"},
            {"label": "Email Verifier", "url": "/tools/email-verify"},
        ],
    },
    {
        "title": "Automation",
        "items": [
            {"label": "Webhooks", "url": "/tools/webhooks"},
        ],
    },
    {
        "title": "Extensibility",
        "items": [
            {"label": "Custom API Connectors", "url": "/tools/connectors"},
            {"label": "Public Scrape API", "url": "/tools/public-api"},
        ],
    },
]

# ---- job-queue tools (POST -> job_id, poll /job?job_id=...) -----------------
JOB_TOOLS = {
    "google-maps": {
        "title": "Google Maps Business Extractor",
        "description": "Playwright deep crawl of Maps listings plus a secondary website-enrichment pass for emails.",
        "header_class": "bg-primary",
        "endpoint": "/api/scrape/google-maps",
        "fields": [
            {"name": "query", "label": "Search query", "type": "text", "required": True, "placeholder": "شقق فندقية"},
            {"name": "location", "label": "Location", "type": "text", "value": "Saudi Arabia"},
            {"name": "max_listings", "label": "Max listings (unlimited)", "type": "number", "value": "1000", "min": "1"},
        ],
    },
    "linkedin": {
        "title": "LinkedIn People Search",
        "description": "Google-dork SERP discovery (site:linkedin.com/in/) with optional deep profile visit using your li_at cookie.",
        "header_class": "bg-success",
        "endpoint": "/api/scrape/linkedin",
        "fields": [
            {"name": "keywords", "label": "Keywords", "type": "text", "required": True, "placeholder": "Sales Manager Riyadh"},
            {"name": "location", "label": "Location (optional)", "type": "text", "placeholder": "Saudi Arabia"},
            {"name": "max_results", "label": "Max results (unlimited)", "type": "number", "value": "1000", "min": "1"},
            {"name": "domain", "label": "Email-guess domain (optional)", "type": "text", "placeholder": "company.com"},
            {"name": "session_cookie", "label": "LinkedIn session cookie (li_at) - optional", "type": "password"},
        ],
    },
    "instagram": {
        "title": "Instagram Scraper",
        "description": "Google-dork discovery (site:instagram.com) of public profiles, then a stealth deep-visit of each.",
        "header_class": "bg-danger",
        "endpoint": "/api/scrape/instagram",
        "fields": [
            {"name": "keywords", "label": "Keywords", "type": "text", "placeholder": "coffee shop riyadh"},
            {"name": "location", "label": "Location (optional)", "type": "text"},
            {"name": "usernames", "label": "Or specific usernames, comma-separated (optional)", "type": "text", "placeholder": "nasa, natgeo"},
            {"name": "max_profiles", "label": "Max profiles (unlimited)", "type": "number", "value": "1000", "min": "1"},
        ],
    },
    "facebook": {
        "title": "Facebook Scraper",
        "description": "Google-dork discovery (site:facebook.com) of public pages, then a stealth deep-visit of each.",
        "header_class": "bg-primary",
        "endpoint": "/api/scrape/facebook",
        "fields": [
            {"name": "keywords", "label": "Keywords", "type": "text", "required": True, "placeholder": "restaurant riyadh"},
            {"name": "location", "label": "Location (optional)", "type": "text"},
            {"name": "max_pages", "label": "Max pages (unlimited)", "type": "number", "value": "1000", "min": "1"},
        ],
    },
    "twitter": {
        "title": "Twitter / X Scraper",
        "description": "Stealth search-timeline scroll and scrape of live results for a keyword/query.",
        "header_class": "bg-dark",
        "endpoint": "/api/scrape/twitter",
        "fields": [
            {"name": "keywords", "label": "Search query", "type": "text", "required": True, "placeholder": "#RiyadhStartups"},
            {"name": "max_tweets", "label": "Max tweets (unlimited)", "type": "number", "value": "1000", "min": "1"},
        ],
    },
    "classifieds": {
        "title": "Classified Ads Scraper",
        "description": "Haraj (dedicated selectors) plus generic MENA marketplaces (OpenSooq, Dubizzle, OLX). Full RTL Arabic support.",
        "header_class": "bg-warning",
        "endpoint": "/api/scrape/classifieds",
        "fields": [
            {"name": "query", "label": "Search query", "type": "text", "required": True, "placeholder": "سيارة للبيع"},
            {"name": "site", "label": "Site", "type": "select", "options": [
                {"value": "haraj", "label": "Haraj", "selected": True},
                {"value": "opensooq", "label": "OpenSooq"},
                {"value": "dubizzle", "label": "Dubizzle (Saudi Arabia)"},
                {"value": "olx_kw", "label": "OLX Kuwait"},
                {"value": "olx_eg", "label": "OLX Egypt"},
                {"value": "mubawab_sa", "label": "Mubawab (Saudi Arabia)"},
                {"value": "bayut", "label": "Bayut (Saudi Arabia)"},
                {"value": "propertyfinder", "label": "Property Finder (Saudi Arabia)"},
                {"value": "syarah", "label": "Syarah"},
                {"value": "expatriates", "label": "Expatriates.com"},
                {"value": "forsale_kw", "label": "4Sale Kuwait"},
            ]},
            {"name": "max_listings", "label": "Max listings (unlimited)", "type": "number", "value": "1000", "min": "1"},
        ],
    },
    "b2b-directory": {
        "title": "B2B Directory Scraper",
        "description": "IndiaMart / JustDial / Sulekha - httpx first, falls back to Playwright if the page looks blocked.",
        "header_class": "bg-info",
        "endpoint": "/api/scrape/b2b-directory",
        "fields": [
            {"name": "query", "label": "Search query", "type": "text", "required": True, "placeholder": "steel pipe suppliers"},
            {"name": "provider", "label": "Directory", "type": "select", "options": [
                {"value": "indiamart", "label": "IndiaMart", "selected": True},
                {"value": "justdial", "label": "JustDial"},
                {"value": "sulekha", "label": "Sulekha"},
            ]},
            {"name": "max_urls", "label": "Max listings (unlimited)", "type": "number", "value": "1000", "min": "1"},
        ],
    },
    "website-crawler": {
        "title": "Deep Website Crawler",
        "description": "BFS crawl of one domain with automatic httpx -> Playwright fallback for JS-heavy pages.",
        "header_class": "bg-secondary",
        "endpoint": "/api/scrape/website-crawler",
        "fields": [
            {"name": "start_url", "label": "Start URL", "type": "text", "required": True, "placeholder": "https://example.com"},
            {"name": "max_pages", "label": "Max pages", "type": "number", "value": "100", "min": "1"},
            {"name": "max_depth", "label": "Max crawl depth", "type": "number", "value": "3", "min": "0"},
        ],
    },
    "crm": {
        "title": "CRM Connections (JustDial / IndiaMART)",
        "description": "Save your own seller-dashboard login (encrypted at rest) then run a best-effort Playwright login + sync.",
        "header_class": "bg-dark",
        "endpoint": "/api/scrape/crm-sync",
        "fields": [
            {"name": "provider", "label": "Provider", "type": "select", "options": [
                {"value": "justdial", "label": "JustDial", "selected": True},
                {"value": "indiamart", "label": "IndiaMART"},
            ]},
        ],
        "note": "Save your login_id/secret first via the 'Save CRM credentials' box below, then run the sync.",
        "credentials_endpoint": "/api/crm/credentials",
    },
}

# ---- instant tools (synchronous request/response, no job queue) ------------
INSTANT_TOOLS = {
    "whois": {
        "title": "WHOIS Domain Lookup",
        "description": "Raw TCP WHOIS (port 43) with IANA registrar-referral chasing - no API key.",
        "endpoint": "/api/tools/whois",
        "method": "POST",
        "fields": [{"name": "domain", "label": "Domain", "type": "text", "required": True, "placeholder": "example.com"}],
    },
    "email-verify": {
        "title": "Email Verifier",
        "description": "Syntax check + free MX-record DNS lookup, disposable/free-provider detection, typo suggestions.",
        "endpoint": "/api/tools/email-verify",
        "method": "POST",
        "fields": [{"name": "email", "label": "Email address", "type": "email", "required": True, "placeholder": "someone@example.com"}],
    },
    "contact": {
        "title": "Contact Scraper",
        "description": "Direct email/phone extraction from a URL or raw pasted text.",
        "endpoint": "/api/tools/contact-scrape",
        "method": "POST",
        "fields": [
            {"name": "url", "label": "URL (optional)", "type": "text", "placeholder": "https://example.com/contact"},
            {"name": "text", "label": "Or raw text (optional)", "type": "textarea"},
        ],
    },
    "google-news": {
        "title": "Google News RSS Scraper",
        "description": "Structured {title, link, pubDate, source} results for any keyword + language - no API key.",
        "endpoint": "/api/tools/google-news",
        "method": "GET",
        "fields": [
            {"name": "keyword", "label": "Keyword", "type": "text", "required": True, "placeholder": "Saudi Arabia real estate"},
            {"name": "lang", "label": "Language", "type": "select", "options": [
                {"value": "en", "label": "English"}, {"value": "ar", "label": "Arabic"},
            ]},
        ],
    },
    "zero-cost-ai": {
        "title": "Zero-Cost AI Scraper",
        "description": "Fetches clean Markdown for any URL via the free r.jina.ai reader and extracts emails/phones/company name.",
        "endpoint": "/api/tools/zero-cost-ai",
        "method": "POST",
        "fields": [{"name": "url", "label": "URL", "type": "text", "required": True, "placeholder": "https://example.com"}],
    },
    "document": {
        "title": "Document Scraper",
        "description": "Upload a .txt, .csv, or .docx file and extract emails/phones.",
        "endpoint": "/api/tools/document-scrape",
        "method": "POST",
        "multipart": True,
        "fields": [{"name": "file", "label": "Document file", "type": "file", "accept": ".txt,.csv,.docx", "required": True}],
    },
    "image-ocr": {
        "title": "Image Scraper (OCR)",
        "description": "OCRs an uploaded image with Tesseract and extracts emails/phones from the recognized text.",
        "endpoint": "/api/tools/image-ocr",
        "method": "POST",
        "multipart": True,
        "fields": [{"name": "file", "label": "Image file", "type": "file", "accept": "image/*", "required": True}],
    },
    "search-engines": {
        "title": "Search Engine Scrapers",
        "description": "Direct HTTP + parser scraping of Google, Bing, Yahoo, or DuckDuckGo results - no API key, no browser.",
        "endpoint": "/api/scrape/search-engine",
        "method": "GET",
        "fields": [
            {"name": "query", "label": "Query", "type": "text", "required": True, "placeholder": "riyadh real estate agencies"},
            {"name": "engine", "label": "Engine", "type": "select", "options": [
                {"value": "google", "label": "Google"}, {"value": "bing", "label": "Bing"},
                {"value": "duckduckgo", "label": "DuckDuckGo"}, {"value": "yahoo", "label": "Yahoo"},
            ]},
            {"name": "num", "label": "Result count", "type": "number", "value": "10"},
        ],
    },
    "multi-search": {
        "title": "Multi-Engine Unified Search",
        "description": "Fans one query out across all four engines concurrently and interleaves the results.",
        "endpoint": "/api/scrape/multi-search",
        "method": "GET",
        "fields": [
            {"name": "query", "label": "Query", "type": "text", "required": True, "placeholder": "riyadh real estate agencies"},
            {"name": "num", "label": "Result count per engine", "type": "number", "value": "10"},
        ],
    },
    "dorks": {
        "title": "Search Dorks Generator",
        "description": "Templated dork-query generation (keyword/location/intent/platform/language), with history.",
        "endpoint": "/api/tools/dorks",
        "method": "POST",
        "list_endpoint": "/api/tools/dorks/history",
        "list_label": "View dork history",
        "fields": [
            {"name": "keyword", "label": "Keyword", "type": "text", "required": True, "placeholder": "real estate agency"},
            {"name": "location", "label": "Location", "type": "text", "placeholder": "Riyadh"},
            {"name": "country", "label": "Country code", "type": "text", "placeholder": "SA"},
            {"name": "intent", "label": "Intent", "type": "text", "placeholder": "EMAIL_HARVESTING"},
        ],
    },
    "cse-keys": {
        "title": "Google Custom Search Key Management",
        "description": "Bring your own Google Programmable Search Engine key + cx. Runs a real Google CSE API search with per-key daily quota tracking.",
        "endpoint": "/api/tools/cse-search",
        "method": "GET",
        "list_endpoint": "/api/tools/cse-keys",
        "list_label": "View saved keys & usage",
        "fields": [
            {"name": "query", "label": "Search query", "type": "text", "required": True},
        ],
        "secondary": {
            "title": "Add a Google CSE key",
            "description": "Get a free key + Search Engine ID (cx) at programmablesearchengine.google.com.",
            "endpoint": "/api/tools/cse-keys",
            "method": "POST",
            "submit_label": "Save key",
            "fields": [
                {"name": "label", "label": "Label", "type": "text"},
                {"name": "key", "label": "API key", "type": "password", "required": True},
                {"name": "cx", "label": "Search Engine ID (cx)", "type": "text", "required": True},
                {"name": "daily_limit", "label": "Daily limit", "type": "number"},
            ],
        },
    },
    "ai-enrichment": {
        "title": "AI Enrichment (Heuristic Lead Scoring)",
        "description": "Rule-based 0-100 lead score (free email provider, no website, socials, phone completeness, review severity) - not an LLM call.",
        "endpoint": "/api/tools/ai-enrichment",
        "method": "POST",
        "fields": [
            {"name": "email", "label": "Email (optional)", "type": "email"},
            {"name": "website", "label": "Website (optional)", "type": "text"},
            {"name": "phone", "label": "Phone (optional)", "type": "text"},
            {"name": "review_severity_score", "label": "Review severity score 0-10 (optional)", "type": "number"},
        ],
    },
    "lead-qualifier": {
        "title": "AI Lead Qualifier (Heuristic)",
        "description": "Keyword/buying-intent overlap classifier between raw text and your product description - heuristic, no LLM call.",
        "endpoint": "/api/tools/lead-qualifier",
        "method": "POST",
        "fields": [
            {"name": "text", "label": "Text to classify", "type": "textarea", "required": True},
            {"name": "product", "label": "Your product/service", "type": "text", "required": True},
        ],
    },
    "professional-contacts": {
        "title": "Professional Contact Finder",
        "description": "Real Hunter.io domain-search - bring your own free-tier Hunter.io API key.",
        "endpoint": "/api/tools/professional-contacts",
        "method": "GET",
        "fields": [
            {"name": "domain", "label": "Domain", "type": "text", "required": True, "placeholder": "example.com"},
            {"name": "api_key", "label": "Hunter.io API key", "type": "password", "required": True},
        ],
    },
    "webhooks": {
        "title": "Webhooks",
        "description": "Register a URL for JOB_STARTED / JOB_COMPLETED / JOB_FAILED / EXPORT_READY / SCRAPE_DATA_AVAILABLE - every job now actually dispatches these.",
        "endpoint": "/api/webhooks",
        "method": "POST",
        "list_endpoint": "/api/webhooks",
        "list_label": "View registered webhooks",
        "fields": [
            {"name": "url", "label": "Webhook URL", "type": "text", "required": True, "placeholder": "https://your-server.com/webhook"},
            {"name": "events", "label": "Events (comma-separated)", "type": "text", "required": True,
             "placeholder": "JOB_STARTED,JOB_COMPLETED,JOB_FAILED"},
        ],
    },
    "connectors": {
        "title": "Custom API Connectors",
        "description": "Register any third-party HTTP API and run it with a query - {query} substitution, auth, and JSON-path result mapping.",
        "endpoint": "/api/connectors/run",
        "method": "POST",
        "list_endpoint": "/api/connectors",
        "list_label": "View registered connectors",
        "fields": [
            {"name": "connector_id", "label": "Connector ID (see list below)", "type": "number", "required": True},
            {"name": "query", "label": "Query", "type": "text", "required": True},
        ],
        "secondary": {
            "title": "Register a connector",
            "description": "URL may contain a {query} placeholder.",
            "endpoint": "/api/connectors",
            "method": "POST",
            "submit_label": "Register connector",
            "fields": [
                {"name": "name", "label": "Name", "type": "text", "required": True},
                {"name": "url", "label": "URL", "type": "text", "required": True, "placeholder": "https://api.example.com/search?q={query}"},
                {"name": "method", "label": "HTTP method", "type": "text", "placeholder": "GET"},
                {"name": "api_key", "label": "API key (optional)", "type": "password"},
                {"name": "auth_type", "label": "Auth type", "type": "text", "placeholder": "none / query / header / bearer"},
                {"name": "auth_param", "label": "Auth param name", "type": "text"},
                {"name": "results_path", "label": "Results JSON dot-path", "type": "text", "placeholder": "data.items"},
            ],
        },
    },
    "public-api": {
        "title": "Public Scrape API",
        "description": "Issue API keys for third-party integrations to call this app's scrapers via /api/v1/scrape, rate-limited per key per day.",
        "endpoint": "/api/v1/keys",
        "method": "POST",
        "list_endpoint": "/api/v1/keys",
        "list_label": "View issued API keys",
        "fields": [
            {"name": "label", "label": "Client label", "type": "text", "placeholder": "my-integration"},
            {"name": "daily_limit", "label": "Daily request limit", "type": "number", "placeholder": "300"},
        ],
    },
}
