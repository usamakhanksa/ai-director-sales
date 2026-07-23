"""
scrapers/facebook.py
======================
Facebook page/profile scraper. Facebook aggressively walls off search and
most content behind a login, so - matching the existing ScrapeGenius
backend's facebookScraper.js - this uses Google-dork SERP discovery
(site:facebook.com "keyword") to find public page/profile URLs, then a
stealth Playwright deep-visit of each to read whatever the logged-out
"page transparency" view renders (name, category, about text, emails/
phones mentioned in the about section).
"""

import re
from urllib.parse import quote_plus

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, extract_emails, extract_phones, random_delay

DEFAULT_MAX_PAGES = 1000
NAV_TIMEOUT_MS = 20_000

RESULT_COLUMNS = ["name", "category", "about", "email", "phone", "website", "page_url"]


class FacebookScraper(BaseScraper):
    source_name = "facebook"

    def __init__(self, keywords: str, location: str = "", max_pages: int = DEFAULT_MAX_PAGES):
        super().__init__(keywords=keywords, location=location, max_pages=max_pages)
        self.keywords = keywords
        self.location = location
        self.max_pages = max(1, int(max_pages or DEFAULT_MAX_PAGES))

    async def run(self):
        async with async_playwright() as pw:
            browser = await pw.chromium.launch(
                headless=True,
                args=["--disable-blink-features=AutomationControlled"],
            )
            try:
                page_urls = await self._discover_pages(browser)
                total = max(len(page_urls), 1)
                for i, url in enumerate(page_urls, start=1):
                    if self.cancelled:
                        self.status = JobStatus.CANCELLED
                        return
                    data = await self._scrape_page(browser, url)
                    if data:
                        self.results.append(data)
                    self.progress = int(i / total * 100)
            finally:
                await browser.close()

    async def _discover_pages(self, browser):
        full_query = f'site:facebook.com "{self.keywords}" {self.location}'.strip()
        search_url = f"https://www.google.com/search?q={quote_plus(full_query)}&num=100"

        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(30_000)
        urls: "list[str]" = []
        try:
            await page.goto(search_url, wait_until="domcontentloaded")
            await page.wait_for_timeout(1500)
            anchors = await page.locator('a[href*="facebook.com/"]').all()
            seen = set()
            for a in anchors:
                href = await a.get_attribute("href")
                if not href:
                    continue
                clean = href.split("&")[0].split("?")[0]
                if any(bad in clean for bad in ("/login", "/help", "/policies", "/ads/", "/sharer")):
                    continue
                if clean in seen:
                    continue
                seen.add(clean)
                urls.append(clean)
                if len(urls) >= self.max_pages:
                    break
            return urls
        finally:
            await context.close()

    async def _scrape_page(self, browser, url: str):
        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        try:
            await page.goto(url, wait_until="domcontentloaded")
            await page.wait_for_timeout(2500)
            await random_delay(500, 1200)

            page_text = await page.locator("body").inner_text()

            name = None
            h1 = page.locator("h1").first
            if await h1.count() > 0:
                try:
                    name = (await h1.inner_text()).strip()
                except Exception:
                    name = None

            category = None
            cat_m = re.search(r"\n([A-Za-z][A-Za-z &/]{2,40})\n(?:·|Page)", page_text)
            if cat_m:
                category = cat_m.group(1).strip()

            emails = extract_emails(page_text)
            phones = extract_phones(page_text)

            website = None
            link_el = page.locator('a[href^="http"]:not([href*="facebook.com"])').first
            if await link_el.count() > 0:
                website = await link_el.get_attribute("href")

            lines = [ln.strip() for ln in page_text.splitlines() if ln.strip()]
            about = " | ".join(lines[:8]) if lines else None

            return {
                "name": name or "N/A",
                "category": category or "N/A",
                "about": about or "N/A",
                "email": emails[0] if emails else "N/A",
                "phone": phones[0] if phones else "N/A",
                "website": website or "N/A",
                "page_url": url,
            }
        except PlaywrightTimeoutError:
            print(f"[job {self.job_id}] timeout, skipping {url}")
            return None
        except Exception as exc:
            print(f"[job {self.job_id}] error on {url}: {exc}")
            return None
        finally:
            await context.close()
