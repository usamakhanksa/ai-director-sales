# Scraper Studio

A self-contained web GUI for the Google Maps and LinkedIn scrapers, built with FastAPI + Playwright + BeautifulSoup4/requests.

## Setup

```bash
cd scraper_gui
python -m venv venv
venv\Scripts\activate        # Windows
pip install -r requirements.txt
playwright install chromium
```

## Run

```bash
python run.py
```

Open http://127.0.0.1:8000 in a browser.

**Windows note**: always start the app with `python run.py`, not `uvicorn main:app --reload` directly. Playwright spawns Chromium as a subprocess, but uvicorn's default event loop on Windows (`SelectorEventLoop`) cannot create subprocesses and raises `NotImplementedError`. `run.py` switches to `WindowsProactorEventLoopPolicy` *before* uvicorn creates its loop - doing this inside `main.py` is too late, since uvicorn's CLI creates the loop before it imports the app module.

## Usage

- **Google Maps card**: enter a search query (e.g. `شقق فندقية`), a location (default "Saudi Arabia"), and a max listings cap (1-100). Submitting redirects to a live job page that polls progress every second, streams results into a table as they arrive, and offers a "Download CSV" button once the job finishes.
- **LinkedIn card**: enter keywords (e.g. `Sales Manager Riyadh`), optional location, max results (1-50). This runs a Google-dork search (`site:linkedin.com/in/ "keywords"`) and reads name/headline/location straight from the SERP snippets - no login required. Optionally supply a LinkedIn `li_at` session cookie to enable a best-effort authenticated deep-scrape of each profile (experience, education, about) - if a profile blocks the deep scrape, the SERP-only data is kept instead. An optional "domain" field lets the scraper guess a likely email address from the person's name when no email is publicly visible.
- **Jobs page** (`/jobs`): lists all jobs (queued/running/completed/failed/cancelled) with a link back to each job's live page.

## API Reference

| Method | Path | Body | Notes |
|---|---|---|---|
| POST | `/api/scrape/google-maps` | `{query, location, max_listings}` | Returns `{job_id, status}` |
| POST | `/api/scrape/linkedin` | `{keywords, location, max_results, session_cookie?, domain?}` | Returns `{job_id, status}` |
| GET | `/api/jobs` | - | List all jobs |
| GET | `/api/jobs/{job_id}` | - | Status/progress/error/elapsed |
| GET | `/api/jobs/{job_id}/results` | - | Full results array (JSON) |
| GET | `/api/jobs/{job_id}/download?format=csv` | - | Streams CSV download |
| DELETE | `/api/jobs/{job_id}` | - | Cooperative cancel |

## Notes

- Jobs run as FastAPI `BackgroundTasks` - the request returns instantly with a `job_id`, and the actual Playwright scrape happens off the request/response cycle.
- Jobs (and any generated CSV files) older than 1 hour are cleaned up automatically by a background loop.
- Every scraper failure is caught inside `BaseScraper.run_safely()` - a bug or blocked page marks the job `failed` with an error message shown in the UI; it never crashes the server.
- Extend to a new source by subclassing `scrapers.base.BaseScraper`, implementing `async def run(self)` that updates `self.progress`/`self.results`, and adding a `POST /api/scrape/<source>` route in `main.py` that constructs it and calls `background_tasks.add_task(_run_job, scraper)`.
