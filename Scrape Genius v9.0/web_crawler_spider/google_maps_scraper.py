"""
google_maps_scraper.py
=======================
Standalone async Google Maps scraper with:
  - live JSON output for every step (search + detail)
  - live progress bar + per-listing timing
  - interactive CLI (Start / Pause / Resume / Stop / Delete job) running
    concurrently with the scrape via asyncio
  - internal job queue (list of listing URLs) with delete-by-url support
  - website crawl for email/phone extraction with TWO selectable engines:
      1) Playwright  - renders JS, handles SPA/anti-bot sites (slower, robust)
      2) BeautifulSoup4 + requests - plain HTTP GET + HTML parse (fast, static sites)
      3) Both (auto)  - try BeautifulSoup4 first, fall back to Playwright if it
                         returns nothing (best of both worlds)
    Google Maps search/detail pages themselves always use Playwright, since
    Maps is a JS-rendered SPA that a plain requests+bs4 GET cannot render.
  - retry-once-on-timeout error handling
  - automatic CSV export on completion or stop

Usage:
    python google_maps_scraper.py
    (then follow the on-screen menu; or answer the initial prompts)

Requirements:
    pip install playwright beautifulsoup4 requests
    playwright install chromium
"""

import asyncio
import csv
import json
import re
import sys
import time
from dataclasses import dataclass, field, asdict
from datetime import datetime
from enum import Enum
from typing import List, Optional

import requests
from bs4 import BeautifulSoup
from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError


# --------------------------------------------------------------------------- #
# Config
# --------------------------------------------------------------------------- #
class ScrapeEngine(str, Enum):
    PLAYWRIGHT = "playwright"
    BS4 = "bs4"
    BOTH = "both"


REQUESTS_TIMEOUT = 15
REQUESTS_HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
    )
}
MAX_LISTINGS = 100
NAV_TIMEOUT_MS = 30_000
RETRY_COUNT = 1
GOOGLE_MAPS_URL = "https://www.google.com/maps/search/{query}"
EMAIL_RE = re.compile(r"[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+")
PHONE_RE = re.compile(r"(\+?\d[\d\s\-()]{6,}\d)")
CSV_COLUMNS = ["Name", "Address", "Rating", "Price (SAR)", "Phone", "Website", "Email", "Time (seconds)"]


def log_json(label: str, payload: dict) -> None:
    """Pretty-print a JSON payload to the console as a live event."""
    print(f"\n===== {label} @ {datetime.now().strftime('%H:%M:%S')} =====")
    print(json.dumps(payload, ensure_ascii=False, indent=2, default=str))
    print("=" * (len(label) + 20))


def print_progress(done: int, total: int, elapsed: float) -> None:
    total = max(total, 1)
    ratio = done / total
    bar_len = 30
    filled = int(bar_len * ratio)
    bar = "#" * filled + "-" * (bar_len - filled)
    sys.stdout.write(f"\r[{bar}] {done}/{total} ({ratio*100:5.1f}%)  last item: {elapsed:.2f}s   ")
    sys.stdout.flush()
    if done >= total:
        print()


# --------------------------------------------------------------------------- #
# Job model
# --------------------------------------------------------------------------- #
class JobStatus(str, Enum):
    PENDING = "pending"
    DONE = "done"
    FAILED = "failed"
    DELETED = "deleted"


@dataclass
class Job:
    url: str
    status: JobStatus = JobStatus.PENDING
    result: Optional[dict] = None


@dataclass
class ScraperState:
    query: str
    location: str
    engine: ScrapeEngine = ScrapeEngine.BOTH
    jobs: List[Job] = field(default_factory=list)
    paused: bool = False
    stopped: bool = False
    started: bool = False
    finished: bool = False


# --------------------------------------------------------------------------- #
# Core scraper
# --------------------------------------------------------------------------- #
class GoogleMapsScraper:
    def __init__(self, query: str, location: str = "Saudi Arabia", engine: ScrapeEngine = ScrapeEngine.BOTH):
        self.state = ScraperState(query=query, location=location, engine=engine)
        self.results: List[dict] = []
        self.playwright = None
        self.browser = None

    # ---- search phase ------------------------------------------------------
    async def collect_listing_urls(self) -> List[str]:
        full_query = f"{self.state.query} {self.state.location}".strip()
        search_url = GOOGLE_MAPS_URL.format(query=full_query.replace(" ", "+"))

        log_json("SEARCH STARTED", {"query": self.state.query, "location": self.state.location, "url": search_url})

        page = await self.browser.new_page()
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        try:
            await page.goto(search_url, wait_until="domcontentloaded")
            await page.wait_for_timeout(3000)

            feed_selector = 'div[role="feed"]'
            try:
                await page.wait_for_selector(feed_selector, timeout=NAV_TIMEOUT_MS)
            except PlaywrightTimeoutError:
                # single-result pages skip the feed and load the place directly
                log_json("SEARCH RESULT", {"note": "no feed found - possible single-result redirect", "current_url": page.url})
                await page.close()
                return [page.url] if "/maps/place" in page.url else []

            urls: set = set()
            same_count_rounds = 0
            while len(urls) < MAX_LISTINGS and same_count_rounds < 6:
                anchors = await page.locator(f'{feed_selector} a[href*="/maps/place"]').all()
                before = len(urls)
                for a in anchors:
                    href = await a.get_attribute("href")
                    if href:
                        urls.add(href.split("?")[0] if "?" in href else href)
                    if len(urls) >= MAX_LISTINGS:
                        break

                if len(urls) == before:
                    same_count_rounds += 1
                else:
                    same_count_rounds = 0

                await page.mouse.wheel(0, 4000)
                await page.wait_for_timeout(1500)

            url_list = list(urls)[:MAX_LISTINGS]
            log_json("SEARCH RESULTS COLLECTED", {"count": len(url_list), "urls": url_list})
            return url_list
        finally:
            await page.close()

    # ---- detail phase: website crawl (email/phone) --------------------------
    def _find_contact_href_bs4(self, soup: BeautifulSoup, base_url: str) -> Optional[str]:
        link = soup.find("a", href=re.compile("contact", re.I))
        if not link:
            return None
        href = link.get("href")
        if not href:
            return None
        if href.startswith("http"):
            return href
        from urllib.parse import urljoin
        return urljoin(base_url, href)

    def extract_contacts_bs4(self, website_url: str) -> dict:
        """Fast, static-HTML scrape using requests + BeautifulSoup4 (no JS rendering)."""
        found = {"email": None, "phone": None, "engine": "bs4"}
        try:
            resp = requests.get(website_url, headers=REQUESTS_HEADERS, timeout=REQUESTS_TIMEOUT)
            resp.raise_for_status()
            soup = BeautifulSoup(resp.text, "html.parser")
            text = soup.get_text(" ", strip=True)

            email_match = EMAIL_RE.search(str(soup)) or EMAIL_RE.search(text)
            if email_match:
                found["email"] = email_match.group(0)

            phone_match = PHONE_RE.search(text)
            if phone_match:
                found["phone"] = phone_match.group(0).strip()

            if not found["email"]:
                contact_url = self._find_contact_href_bs4(soup, website_url)
                if contact_url:
                    resp2 = requests.get(contact_url, headers=REQUESTS_HEADERS, timeout=REQUESTS_TIMEOUT)
                    resp2.raise_for_status()
                    soup2 = BeautifulSoup(resp2.text, "html.parser")
                    text2 = soup2.get_text(" ", strip=True)
                    email_match2 = EMAIL_RE.search(str(soup2)) or EMAIL_RE.search(text2)
                    if email_match2:
                        found["email"] = email_match2.group(0)
                    if not found["phone"]:
                        phone_match2 = PHONE_RE.search(text2)
                        if phone_match2:
                            found["phone"] = phone_match2.group(0).strip()
        except Exception as exc:
            print(f"\n[warn][bs4] failed to fetch {website_url}: {exc}")
        return found

    async def extract_contacts_playwright(self, website_url: str) -> dict:
        """JS-rendered scrape using Playwright (handles SPA/anti-bot sites)."""
        found = {"email": None, "phone": None, "engine": "playwright"}
        page = await self.browser.new_page()
        try:
            await page.goto(website_url, wait_until="domcontentloaded", timeout=NAV_TIMEOUT_MS)
            await page.wait_for_timeout(1500)
            html = await page.content()
            text = re.sub(r"<[^>]+>", " ", html)

            match = EMAIL_RE.search(html)
            if match:
                found["email"] = match.group(0)
            phone_match = PHONE_RE.search(text)
            if phone_match:
                found["phone"] = phone_match.group(0).strip()

            if not found["email"]:
                contact_link = page.locator('a[href*="contact"]').first
                if await contact_link.count() > 0:
                    href = await contact_link.get_attribute("href")
                    if href:
                        await page.goto(href, wait_until="domcontentloaded", timeout=NAV_TIMEOUT_MS)
                        await page.wait_for_timeout(1500)
                        html = await page.content()
                        text = re.sub(r"<[^>]+>", " ", html)
                        match = EMAIL_RE.search(html)
                        if match:
                            found["email"] = match.group(0)
                        if not found["phone"]:
                            phone_match = PHONE_RE.search(text)
                            if phone_match:
                                found["phone"] = phone_match.group(0).strip()
        except Exception as exc:
            print(f"\n[warn][playwright] failed to fetch {website_url}: {exc}")
        finally:
            await page.close()
        return found

    async def extract_contacts(self, website_url: str) -> dict:
        """Dispatch to the configured engine(s) and return {email, phone, engine}."""
        if not website_url:
            return {"email": None, "phone": None, "engine": "none"}

        loop = asyncio.get_event_loop()

        if self.state.engine == ScrapeEngine.BS4:
            result = await loop.run_in_executor(None, self.extract_contacts_bs4, website_url)
        elif self.state.engine == ScrapeEngine.PLAYWRIGHT:
            result = await self.extract_contacts_playwright(website_url)
        else:  # BOTH: try bs4 first (cheap/fast), fall back to Playwright if empty
            result = await loop.run_in_executor(None, self.extract_contacts_bs4, website_url)
            if not result.get("email") and not result.get("phone"):
                result = await self.extract_contacts_playwright(website_url)
                result["engine"] = "playwright (bs4 fallback)"
            else:
                result["engine"] = "bs4"

        log_json("WEBSITE CONTACT SCRAPE", {"url": website_url, **result})
        return result

    async def scrape_detail(self, url: str) -> dict:
        start = time.time()
        page = await self.browser.new_page()
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        try:
            await page.goto(url, wait_until="domcontentloaded")
            await page.wait_for_timeout(2500)

            async def text_or_none(selector: str) -> Optional[str]:
                loc = page.locator(selector).first
                if await loc.count() > 0:
                    try:
                        return (await loc.inner_text()).strip()
                    except Exception:
                        return None
                return None

            name = await text_or_none("h1")
            rating = await text_or_none('span[role="img"][aria-label*="star"]')
            address = await text_or_none('button[data-item-id="address"]')
            phone_raw = await text_or_none('button[data-item-id^="phone"]')
            website_el = page.locator('a[data-item-id="authority"]').first
            website = await website_el.get_attribute("href") if await website_el.count() > 0 else None

            # price - Google Maps shows a $ / price-level indicator, fall back to SAR text scan
            price_text = await text_or_none('span[aria-label*="Price"]')
            price_sar = None
            if price_text:
                money = re.search(r"[\d,]+", price_text)
                if money:
                    price_sar = money.group(0)

            phone = None
            if phone_raw:
                m = PHONE_RE.search(phone_raw)
                phone = m.group(0).strip() if m else phone_raw

            email = None
            if website:
                contacts = await self.extract_contacts(website)
                email = contacts.get("email")
                # prefer a phone found on the Maps card; fall back to the website's phone
                if not phone:
                    phone = contacts.get("phone")

            elapsed = round(time.time() - start, 2)
            data = {
                "Name": name or "N/A",
                "Address": address or "N/A",
                "Rating": rating or "N/A",
                "Price (SAR)": price_sar or "N/A",
                "Phone": phone or "N/A",
                "Website": website or "N/A",
                "Email": email or "N/A",
                "Time (seconds)": elapsed,
                "source_url": url,
            }
            log_json("LISTING DETAIL", data)
            return data
        finally:
            await page.close()

    async def scrape_detail_with_retry(self, url: str) -> Optional[dict]:
        for attempt in range(RETRY_COUNT + 1):
            try:
                return await self.scrape_detail(url)
            except PlaywrightTimeoutError as exc:
                print(f"\n[warn] timeout on {url} (attempt {attempt + 1}): {exc}")
                if attempt >= RETRY_COUNT:
                    print(f"[error] giving up on {url} after {RETRY_COUNT + 1} attempts")
                    return None
            except Exception as exc:
                print(f"\n[warn] error on {url} (attempt {attempt + 1}): {exc}")
                if attempt >= RETRY_COUNT:
                    print(f"[error] giving up on {url} after {RETRY_COUNT + 1} attempts")
                    return None
        return None

    # ---- orchestration -------------------------------------------------------
    async def run(self):
        self.playwright = await async_playwright().start()
        self.browser = await self.playwright.chromium.launch(headless=True)
        try:
            urls = await self.collect_listing_urls()
            self.state.jobs = [Job(url=u) for u in urls]
            self.state.started = True

            total = len(self.state.jobs)
            done = 0

            for job in self.state.jobs:
                # respond to stop
                if self.state.stopped:
                    print("\n[info] scraper stopped by user, halting remaining jobs.")
                    break

                # respond to pause (poll until resumed or stopped)
                while self.state.paused and not self.state.stopped:
                    await asyncio.sleep(0.5)

                if self.state.stopped:
                    break

                if job.status == JobStatus.DELETED:
                    continue

                start = time.time()
                data = await self.scrape_detail_with_retry(job.url)
                elapsed = time.time() - start

                if data:
                    job.status = JobStatus.DONE
                    job.result = data
                    self.results.append(data)
                else:
                    job.status = JobStatus.FAILED

                done += 1
                print_progress(done, total, elapsed)

            self.state.finished = True
        finally:
            await self.browser.close()
            await self.playwright.stop()
            self.export_csv()

    # ---- job queue management -------------------------------------------------
    def delete_job(self, url: str) -> bool:
        for job in self.state.jobs:
            if job.url == url and job.status == JobStatus.PENDING:
                job.status = JobStatus.DELETED
                log_json("JOB DELETED", {"url": url})
                return True
        return False

    def pause(self):
        self.state.paused = True
        log_json("SCRAPER PAUSED", {"time": datetime.now().isoformat()})

    def resume(self):
        self.state.paused = False
        log_json("SCRAPER RESUMED", {"time": datetime.now().isoformat()})

    def stop(self):
        self.state.stopped = True
        log_json("SCRAPER STOPPED", {"time": datetime.now().isoformat()})

    def list_jobs(self):
        payload = [{"url": j.url, "status": j.status.value} for j in self.state.jobs]
        log_json("JOB QUEUE", {"jobs": payload})

    # ---- csv export ------------------------------------------------------
    def export_csv(self):
        if not self.results:
            print("\n[info] no results to export.")
            return
        filename = f"google_maps_results_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        with open(filename, "w", newline="", encoding="utf-8-sig") as f:
            writer = csv.DictWriter(f, fieldnames=CSV_COLUMNS)
            writer.writeheader()
            for row in self.results:
                writer.writerow({col: row.get(col, "N/A") for col in CSV_COLUMNS})
        log_json("CSV EXPORTED", {"filename": filename, "rows": len(self.results)})


# --------------------------------------------------------------------------- #
# CLI menu + concurrent control loop
# --------------------------------------------------------------------------- #
MENU = """
=========================================
  Google Maps Scraper - Command Menu
=========================================
  [s] Start scraping
  [p] Pause
  [r] Resume
  [x] Stop
  [d] Delete a job (by URL)
  [l] List job queue
  [q] Quit (stops scraper first if running)
=========================================
> """


async def command_loop(scraper: GoogleMapsScraper, scrape_task_holder: dict):
    loop = asyncio.get_event_loop()
    while True:
        cmd = await loop.run_in_executor(None, input, MENU)
        cmd = cmd.strip().lower()

        if cmd == "s":
            if scrape_task_holder.get("task") is None:
                scrape_task_holder["task"] = asyncio.create_task(scraper.run())
                print("[info] scraping started in background...")
            else:
                print("[info] scraper already started.")

        elif cmd == "p":
            scraper.pause()

        elif cmd == "r":
            scraper.resume()

        elif cmd == "x":
            scraper.stop()

        elif cmd == "d":
            url = await loop.run_in_executor(None, input, "Enter job URL to delete: ")
            deleted = scraper.delete_job(url.strip())
            if not deleted:
                print("[warn] job not found or already processed.")

        elif cmd == "l":
            scraper.list_jobs()

        elif cmd == "q":
            scraper.stop()
            task = scrape_task_holder.get("task")
            if task:
                await task
            print("[info] exiting.")
            break

        else:
            print("[warn] unknown command.")

        task = scrape_task_holder.get("task")
        if task and task.done() and scraper.state.finished:
            print("[info] scraping finished. You may still 'q' to exit or 's' will no-op.")


def choose_engine() -> ScrapeEngine:
    print("\nWebsite contact-scraping engine (Google Maps search itself always uses Playwright):")
    print("  [1] Playwright   - JS-rendered, slower, most robust")
    print("  [2] BeautifulSoup4 - fast static HTML parse via requests")
    print("  [3] Both (auto)  - try BeautifulSoup4 first, fall back to Playwright  [default]")
    choice = input("Choose 1/2/3: ").strip()
    return {
        "1": ScrapeEngine.PLAYWRIGHT,
        "2": ScrapeEngine.BS4,
        "3": ScrapeEngine.BOTH,
    }.get(choice, ScrapeEngine.BOTH)


async def main():
    print("Google Maps Scraper")
    query = input("Enter search query (e.g. شقق فندقية): ").strip() or "شقق فندقية"
    location = input("Enter location [default: Saudi Arabia]: ").strip() or "Saudi Arabia"
    engine = choose_engine()

    scraper = GoogleMapsScraper(query=query, location=location, engine=engine)
    scrape_task_holder = {"task": None}

    await command_loop(scraper, scrape_task_holder)


if __name__ == "__main__":
    asyncio.run(main())
