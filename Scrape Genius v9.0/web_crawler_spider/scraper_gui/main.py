"""
main.py
========
FastAPI entry point for the Scraper Studio web app. Wires together:
  - the in-memory job manager (active_jobs dict) + real webhook dispatch
    on job lifecycle events (JOB_STARTED/JOB_COMPLETED/JOB_FAILED)
  - background task execution for scrapers (non-blocking)
  - JSON API endpoints for starting/polling/downloading jobs
  - Jinja2-rendered dashboard + job progress pages + a full sidebar where
    every feature area is a real, working route (job-queue scrapers,
    instant synchronous tools, BYO-key integrations) - no "Soon" stubs.

Run with:  python run.py
(NOT `uvicorn main:app --reload` directly on Windows - see run.py for why:
uvicorn's own Windows loop setup forces WindowsSelectorEventLoopPolicy
whenever reload/workers are on, which breaks Playwright's subprocess
spawning no matter what policy is set beforehand.)
"""

import asyncio
import os
import sys
import tempfile
import time
from typing import Dict, Optional

if sys.platform == "win32":
    # uvicorn --reload spawns a separate child "server process" that
    # re-imports this module fresh; the Proactor policy set in run.py's
    # parent process does not carry over, so it must be set again here
    # before this child process creates its event loop.
    asyncio.set_event_loop_policy(asyncio.WindowsProactorEventLoopPolicy())

from fastapi import BackgroundTasks, FastAPI, Request, UploadFile, File
from fastapi.responses import FileResponse, JSONResponse
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates

from nav import NAV_GROUPS, JOB_TOOLS, INSTANT_TOOLS
from scrapers.base import BaseScraper
from scrapers.google_maps import GoogleMapsScraper, RESULT_COLUMNS as GMAPS_COLUMNS
from scrapers.linkedin import LinkedInScraper, RESULT_COLUMNS as LINKEDIN_COLUMNS
from scrapers.instagram import InstagramScraper, RESULT_COLUMNS as INSTAGRAM_COLUMNS
from scrapers.facebook import FacebookScraper, RESULT_COLUMNS as FACEBOOK_COLUMNS
from scrapers.twitter import TwitterScraper, RESULT_COLUMNS as TWITTER_COLUMNS
from scrapers.classifieds import ClassifiedsScraper, RESULT_COLUMNS as CLASSIFIEDS_COLUMNS
from scrapers.b2b_directory import B2BDirectoryScraper, RESULT_COLUMNS as B2B_COLUMNS
from scrapers.website_crawler import WebsiteCrawler, RESULT_COLUMNS as CRAWLER_COLUMNS
from scrapers.crm import CRMSyncScraper, RESULT_COLUMNS as CRM_COLUMNS, save_credentials, list_crm_connections
from scrapers.utils import save_results_to_csv
from scrapers.whois_lookup import whois_lookup
from scrapers.email_verifier import verify_email
from scrapers.contact_scraper import extract_contacts
from scrapers.google_news import fetch_google_news
from scrapers.zero_cost_ai import scrape_via_reader
from scrapers.document_scraper import extract_from_document
from scrapers.image_ocr import extract_from_image
from scrapers.search_engines import ENGINES, search_engine
from scrapers.multi_search import run_multi_search
from scrapers.dork_generator import generate_dorks, get_dork_history
from scrapers.cse_keys import list_keys as cse_list_keys, add_key as cse_add_key, remove_key as cse_remove_key, cse_search
from scrapers.ai_enrichment import score_lead
from scrapers.lead_qualifier import qualify_lead
from scrapers.professional_contacts import find_professional_contacts
from scrapers.webhooks import list_webhooks, register_webhook, deactivate_webhook, dispatch as dispatch_webhook
from scrapers.connectors import list_connectors, register_connector, remove_connector, run_connector
from scrapers.public_api import list_api_keys, issue_api_key, revoke_api_key, check_and_consume

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
JOB_TTL_SECONDS = 60 * 60  # 1 hour, per spec 7.2

app = FastAPI(title="Scraper Studio")
app.mount("/static", StaticFiles(directory=os.path.join(BASE_DIR, "static")), name="static")
templates = Jinja2Templates(directory=os.path.join(BASE_DIR, "templates"))

# job_id -> BaseScraper instance
active_jobs: Dict[str, BaseScraper] = {}
# job_id -> path of a generated CSV export, for later cleanup
_export_files: Dict[str, str] = {}

# source_name -> CSV column order, for /api/jobs/{id}/download
RESULT_COLUMNS_BY_SOURCE = {
    "google_maps": GMAPS_COLUMNS,
    "linkedin": LINKEDIN_COLUMNS,
    "instagram": INSTAGRAM_COLUMNS,
    "facebook": FACEBOOK_COLUMNS,
    "twitter": TWITTER_COLUMNS,
    "classifieds": CLASSIFIEDS_COLUMNS,
    "b2b_directory": B2B_COLUMNS,
    "website_crawler": CRAWLER_COLUMNS,
    "crm_sync": CRM_COLUMNS,
}

# source_name -> scraper class, for the generic /api/scrape/{source} used by
# the Public Scrape API forwarder (kept in sync with the job starters below)
SCRAPER_CLASSES = {
    "google_maps": GoogleMapsScraper,
    "linkedin": LinkedInScraper,
    "instagram": InstagramScraper,
    "facebook": FacebookScraper,
    "twitter": TwitterScraper,
    "classifieds": ClassifiedsScraper,
    "b2b_directory": B2BDirectoryScraper,
    "website_crawler": WebsiteCrawler,
}


def render(request: Request, template: str, ctx: Optional[dict] = None):
    """TemplateResponse wrapper that always injects the sidebar nav data."""
    ctx = ctx or {}
    return templates.TemplateResponse(template, {"request": request, "nav_groups": NAV_GROUPS, **ctx})


# --------------------------------------------------------------------------- #
# Background execution + cleanup (with real webhook dispatch)
# --------------------------------------------------------------------------- #
async def _run_job(scraper: BaseScraper):
    active_jobs[scraper.job_id] = scraper
    await dispatch_webhook("JOB_STARTED", {"job_id": scraper.job_id, "source": scraper.source_name})

    await scraper.run_safely()

    event = "JOB_COMPLETED" if scraper.status.value == "completed" else "JOB_FAILED"
    await dispatch_webhook(event, scraper.get_status())
    if scraper.results:
        await dispatch_webhook("SCRAPE_DATA_AVAILABLE", {"job_id": scraper.job_id, "result_count": len(scraper.results)})


def _cleanup_old_jobs():
    """Remove jobs (and their CSV export files) older than JOB_TTL_SECONDS."""
    now = time.time()
    stale_ids = [
        job_id for job_id, job in active_jobs.items()
        if job.end_time and (now - job.end_time) > JOB_TTL_SECONDS
    ]
    for job_id in stale_ids:
        active_jobs.pop(job_id, None)
        csv_path = _export_files.pop(job_id, None)
        if csv_path and os.path.exists(csv_path):
            try:
                os.remove(csv_path)
            except OSError:
                pass


async def _cleanup_loop():
    while True:
        await asyncio.sleep(300)
        _cleanup_old_jobs()


@app.on_event("startup")
async def on_startup():
    asyncio.create_task(_cleanup_loop())


def _get_job(job_id: str) -> Optional[BaseScraper]:
    return active_jobs.get(job_id)


# --------------------------------------------------------------------------- #
# Frontend pages
# --------------------------------------------------------------------------- #
@app.get("/")
async def index(request: Request):
    return render(request, "index.html")


@app.get("/job")
async def job_page(request: Request, job_id: str):
    return render(request, "job.html", {"job_id": job_id})


@app.get("/jobs")
async def jobs_page(request: Request):
    jobs = [job.get_status() for job in sorted(active_jobs.values(), key=lambda j: j.start_time, reverse=True)]
    return render(request, "index.html", {"jobs": jobs})


@app.get("/tools/{tool_key}")
async def tool_page(request: Request, tool_key: str):
    if tool_key in JOB_TOOLS:
        return render(request, "job_form.html", {"tool": JOB_TOOLS[tool_key]})
    if tool_key in INSTANT_TOOLS:
        return render(request, "instant_tool.html", {"tool": INSTANT_TOOLS[tool_key]})
    return JSONResponse({"error": "unknown tool"}, status_code=404)


# --------------------------------------------------------------------------- #
# Job-queue scraper APIs (Google Maps, LinkedIn, Instagram, Facebook,
# Twitter/X, Classifieds, B2B Directories, Website Crawler, CRM Sync) -
# each starts a background job and returns a job_id the frontend polls
# via the shared /api/jobs/{id} endpoints below.
# --------------------------------------------------------------------------- #
@app.post("/api/scrape/google-maps")
async def start_google_maps(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    query = (body.get("query") or "").strip()
    location = (body.get("location") or "Saudi Arabia").strip()
    max_listings = body.get("max_listings", 1000)

    if not query:
        return JSONResponse({"error": "query is required"}, status_code=400)

    scraper = GoogleMapsScraper(query=query, location=location, max_listings=max_listings)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/linkedin")
async def start_linkedin(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    keywords = (body.get("keywords") or "").strip()
    location = (body.get("location") or "").strip()
    max_results = body.get("max_results", 1000)
    session_cookie = (body.get("session_cookie") or "").strip()
    domain_for_email_guess = (body.get("domain") or "").strip()

    if not keywords:
        return JSONResponse({"error": "keywords is required"}, status_code=400)

    scraper = LinkedInScraper(
        keywords=keywords,
        location=location,
        max_results=max_results,
        session_cookie=session_cookie,
        domain_for_email_guess=domain_for_email_guess,
    )
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/instagram")
async def start_instagram(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    keywords = (body.get("keywords") or "").strip()
    location = (body.get("location") or "").strip()
    usernames = (body.get("usernames") or "").strip()
    max_profiles = body.get("max_profiles", 1000)

    if not keywords and not usernames:
        return JSONResponse({"error": "keywords or usernames is required"}, status_code=400)

    scraper = InstagramScraper(keywords=keywords, location=location, usernames=usernames, max_profiles=max_profiles)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/facebook")
async def start_facebook(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    keywords = (body.get("keywords") or "").strip()
    location = (body.get("location") or "").strip()
    max_pages = body.get("max_pages", 1000)

    if not keywords:
        return JSONResponse({"error": "keywords is required"}, status_code=400)

    scraper = FacebookScraper(keywords=keywords, location=location, max_pages=max_pages)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/twitter")
async def start_twitter(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    keywords = (body.get("keywords") or "").strip()
    max_tweets = body.get("max_tweets", 1000)

    if not keywords:
        return JSONResponse({"error": "keywords is required"}, status_code=400)

    scraper = TwitterScraper(keywords=keywords, max_tweets=max_tweets)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/classifieds")
async def start_classifieds(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    query = (body.get("query") or "").strip()
    site = (body.get("site") or "haraj").strip()
    max_listings = body.get("max_listings", 1000)

    if not query:
        return JSONResponse({"error": "query is required"}, status_code=400)

    scraper = ClassifiedsScraper(query=query, site=site, max_listings=max_listings)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/b2b-directory")
async def start_b2b_directory(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    query = (body.get("query") or "").strip()
    provider = (body.get("provider") or "indiamart").strip()
    max_urls = body.get("max_urls", 1000)

    if not query:
        return JSONResponse({"error": "query is required"}, status_code=400)

    scraper = B2BDirectoryScraper(query=query, provider=provider, max_urls=max_urls)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/scrape/website-crawler")
async def start_website_crawler(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    start_url = (body.get("start_url") or "").strip()
    max_pages = body.get("max_pages", 100)
    max_depth = body.get("max_depth", 3)

    if not start_url:
        return JSONResponse({"error": "start_url is required"}, status_code=400)

    scraper = WebsiteCrawler(start_url=start_url, max_pages=max_pages, max_depth=max_depth)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


@app.post("/api/crm/credentials")
async def save_crm_credentials(request: Request):
    body = await request.json()
    return save_credentials(body.get("provider", ""), body.get("login_id", ""), body.get("secret", ""))


@app.get("/api/crm/credentials")
async def get_crm_credentials():
    return list_crm_connections()


@app.post("/api/scrape/crm-sync")
async def start_crm_sync(request: Request, background_tasks: BackgroundTasks):
    body = await request.json()
    provider = (body.get("provider") or "").strip()
    if not provider:
        return JSONResponse({"error": "provider is required"}, status_code=400)

    scraper = CRMSyncScraper(provider=provider)
    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


# --------------------------------------------------------------------------- #
# Shared job endpoints (work for any scraper type)
# --------------------------------------------------------------------------- #
@app.get("/api/jobs")
async def list_jobs():
    _cleanup_old_jobs()
    return [job.get_status() for job in sorted(active_jobs.values(), key=lambda j: j.start_time, reverse=True)]


@app.get("/api/jobs/{job_id}")
async def job_status(job_id: str):
    job = _get_job(job_id)
    if not job:
        return JSONResponse({"error": "job not found"}, status_code=404)
    return job.get_status()


@app.get("/api/jobs/{job_id}/results")
async def job_results(job_id: str):
    job = _get_job(job_id)
    if not job:
        return JSONResponse({"error": "job not found"}, status_code=404)
    return job.get_results()


@app.delete("/api/jobs/{job_id}")
async def cancel_job(job_id: str):
    job = _get_job(job_id)
    if not job:
        return JSONResponse({"error": "job not found"}, status_code=404)
    job.cancel()
    return job.get_status()


@app.get("/api/jobs/{job_id}/download")
async def download_job(job_id: str, format: str = "csv"):
    job = _get_job(job_id)
    if not job:
        return JSONResponse({"error": "job not found"}, status_code=404)
    if format != "csv":
        return JSONResponse({"error": "only csv is supported"}, status_code=400)

    columns = RESULT_COLUMNS_BY_SOURCE.get(job.source_name)
    tmp_dir = tempfile.gettempdir()
    filename = os.path.join(tmp_dir, f"scraper_gui_{job.source_name}_{job_id}.csv")
    save_results_to_csv(job.get_results(), filename, columns=columns)
    _export_files[job_id] = filename

    return FileResponse(
        filename,
        media_type="text/csv",
        filename=f"{job.source_name}_{job_id}.csv",
    )


# --------------------------------------------------------------------------- #
# Instant tools (synchronous request/response, no job queue)
# --------------------------------------------------------------------------- #
@app.post("/api/tools/whois")
async def api_whois(request: Request):
    body = await request.json()
    domain = (body.get("domain") or "").strip()
    if not domain:
        return JSONResponse({"error": "domain is required"}, status_code=400)
    return await whois_lookup(domain)


@app.post("/api/tools/email-verify")
async def api_email_verify(request: Request):
    body = await request.json()
    email = (body.get("email") or "").strip()
    if not email:
        return JSONResponse({"error": "email is required"}, status_code=400)
    return verify_email(email)


@app.post("/api/tools/contact-scrape")
async def api_contact_scrape(request: Request):
    body = await request.json()
    return await extract_contacts(url=body.get("url", ""), text=body.get("text", ""))


@app.get("/api/tools/google-news")
async def api_google_news(keyword: str = "", lang: str = "en"):
    if not keyword.strip():
        return JSONResponse({"error": "keyword is required"}, status_code=400)
    return await fetch_google_news(keyword=keyword, lang=lang)


@app.post("/api/tools/zero-cost-ai")
async def api_zero_cost_ai(request: Request):
    body = await request.json()
    url = (body.get("url") or "").strip()
    if not url:
        return JSONResponse({"error": "url is required"}, status_code=400)
    return await scrape_via_reader(url)


@app.post("/api/tools/document-scrape")
async def api_document_scrape(file: UploadFile = File(...)):
    raw = await file.read()
    return extract_from_document(file.filename, raw)


@app.post("/api/tools/image-ocr")
async def api_image_ocr(file: UploadFile = File(...)):
    raw = await file.read()
    return extract_from_image(file.filename, raw)


# --------------------------------------------------------------------------- #
# Search Engines / Multi-Engine Search / Dorks / CSE Keys
# --------------------------------------------------------------------------- #
@app.get("/api/scrape/search-engine")
async def api_search_engine(query: str = "", engine: str = "google", num: int = 10):
    if not query.strip():
        return JSONResponse({"error": "query is required"}, status_code=400)
    return await search_engine(engine, query, num)


@app.get("/api/scrape/multi-search")
async def api_multi_search(query: str = "", num: int = 10, engines: str = ""):
    if not query.strip():
        return JSONResponse({"error": "query is required"}, status_code=400)
    engine_list = [e.strip() for e in engines.split(",") if e.strip()] or list(ENGINES)
    return await run_multi_search(query, engine_list, num)


@app.post("/api/tools/dorks")
async def api_generate_dorks(request: Request):
    body = await request.json()
    return generate_dorks(
        keyword=body.get("keyword", ""), location=body.get("location", ""),
        country=body.get("country", "SA"), intent=body.get("intent", "EMAIL_HARVESTING"),
        platforms=body.get("platforms", []), language=body.get("language", "en"),
    )


@app.get("/api/tools/dorks/history")
async def api_dorks_history():
    return get_dork_history()


@app.get("/api/tools/cse-keys")
async def api_cse_keys_list():
    return cse_list_keys()


@app.post("/api/tools/cse-keys")
async def api_cse_keys_add(request: Request):
    body = await request.json()
    return cse_add_key(body.get("key", ""), body.get("cx", ""), body.get("daily_limit", 100), body.get("label", ""))


@app.delete("/api/tools/cse-keys/{key_id}")
async def api_cse_keys_remove(key_id: int):
    return cse_remove_key(key_id)


@app.get("/api/tools/cse-search")
async def api_cse_search(query: str = ""):
    if not query.strip():
        return JSONResponse({"error": "query is required"}, status_code=400)
    return await cse_search(query)


# --------------------------------------------------------------------------- #
# AI Enrichment / Lead Qualifier / Professional Contacts
# --------------------------------------------------------------------------- #
@app.post("/api/tools/ai-enrichment")
async def api_ai_enrichment(request: Request):
    body = await request.json()
    return score_lead(
        email=body.get("email", ""), website=body.get("website", ""),
        social_links=body.get("social_links", []), phone=body.get("phone", ""),
        review_severity_score=float(body.get("review_severity_score") or 0),
    )


@app.post("/api/tools/lead-qualifier")
async def api_lead_qualifier(request: Request):
    body = await request.json()
    return qualify_lead(body.get("text", ""), body.get("product", ""))


@app.get("/api/tools/professional-contacts")
async def api_professional_contacts(domain: str = "", api_key: str = "", query: str = ""):
    return await find_professional_contacts(domain, api_key, query)


# --------------------------------------------------------------------------- #
# Webhooks (registration + real dispatch, see _run_job above)
# --------------------------------------------------------------------------- #
@app.get("/api/webhooks")
async def api_webhooks_list():
    return list_webhooks()


@app.post("/api/webhooks")
async def api_webhooks_register(request: Request):
    body = await request.json()
    events = body.get("events", "")
    if isinstance(events, str):
        events = [e.strip() for e in events.split(",") if e.strip()]
    return register_webhook(body.get("url", ""), events)


@app.delete("/api/webhooks/{webhook_id}")
async def api_webhooks_deactivate(webhook_id: int):
    return deactivate_webhook(webhook_id)


# --------------------------------------------------------------------------- #
# Custom API Connectors
# --------------------------------------------------------------------------- #
@app.get("/api/connectors")
async def api_connectors_list():
    return list_connectors()


@app.post("/api/connectors")
async def api_connectors_register(request: Request):
    body = await request.json()
    return register_connector(
        name=body.get("name", ""), url=body.get("url", ""), method=body.get("method", "GET"),
        api_key=body.get("api_key", ""), auth_type=body.get("auth_type", "none") or "none",
        auth_param=body.get("auth_param", ""), results_path=body.get("results_path", ""),
        field_map=body.get("field_map", {}),
    )


@app.delete("/api/connectors/{connector_id}")
async def api_connectors_remove(connector_id: int):
    return remove_connector(connector_id)


@app.post("/api/connectors/run")
async def api_connectors_run(request: Request):
    body = await request.json()
    connector_id = int(body.get("connector_id", 0))
    return await run_connector(connector_id, body.get("query", ""))


# --------------------------------------------------------------------------- #
# Public Scrape API - key issuance/management + the rate-limited,
# key-authenticated /api/v1/scrape endpoint that forwards to a scraper.
# --------------------------------------------------------------------------- #
@app.get("/api/v1/keys")
async def api_v1_keys_list():
    return list_api_keys()


@app.post("/api/v1/keys")
async def api_v1_keys_issue(request: Request):
    body = await request.json()
    return issue_api_key(body.get("label", ""), body.get("daily_limit", 300))


@app.delete("/api/v1/keys/{key_id}")
async def api_v1_keys_revoke(key_id: int):
    return revoke_api_key(key_id)


@app.post("/api/v1/scrape")
async def api_v1_scrape(request: Request, background_tasks: BackgroundTasks):
    api_key = request.headers.get("x-api-key") or request.query_params.get("api_key", "")
    ok, message, _ = check_and_consume(api_key)
    if not ok:
        return JSONResponse({"error": message}, status_code=401 if "key" in message.lower() else 429)

    body = await request.json()
    module = (body.get("module") or "").strip()
    config = body.get("config", {})

    scraper_cls = SCRAPER_CLASSES.get(module)
    if not scraper_cls:
        return JSONResponse({"error": f"Unknown module '{module}'. Choose one of {list(SCRAPER_CLASSES)}"}, status_code=400)

    try:
        scraper = scraper_cls(**config)
    except TypeError as exc:
        return JSONResponse({"error": f"Invalid config for '{module}': {exc}"}, status_code=400)

    background_tasks.add_task(_run_job, scraper)
    return {"job_id": scraper.job_id, "status": scraper.status.value}


# Do not run this module directly (`python main.py`) or via bare
# `uvicorn main:app` on Windows - use `python run.py`, which sets the
# Proactor event loop policy before uvicorn ever creates its loop.
