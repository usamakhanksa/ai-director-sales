"""
scrapers/website_crawler.py
=============================
Deep Website Crawler - BFS crawl of a single domain with automatic
httpx -> Playwright fallback for JS-heavy SPAs, porting
backend/src/scrapers/websiteCrawler.js. Fallback trigger matches the
Node heuristic: page looks JS-heavy if the visible text is tiny
relative to the raw HTML size (client-side-rendered shell).
"""

import re
from urllib.parse import urljoin, urlparse

import httpx
from bs4 import BeautifulSoup
from playwright.async_api import async_playwright

from .base import BaseScraper, JobStatus
from .utils import extract_emails, extract_phones, random_delay

DEFAULT_MAX_PAGES = 100
DEFAULT_MAX_DEPTH = 3
FETCH_TIMEOUT = 12

ASSET_EXT_RE = re.compile(r"\.(jpg|jpeg|png|gif|svg|css|js|pdf|zip|mp4|mp3|woff2?|ico)(\?|$)", re.IGNORECASE)

RESULT_COLUMNS = ["url", "depth", "emails", "phones", "title"]


class WebsiteCrawler(BaseScraper):
    source_name = "website_crawler"

    def __init__(self, start_url: str, max_pages: int = DEFAULT_MAX_PAGES, max_depth: int = DEFAULT_MAX_DEPTH):
        super().__init__(start_url=start_url, max_pages=max_pages, max_depth=max_depth)
        self.start_url = start_url.strip()
        self.max_pages = max(1, int(max_pages or DEFAULT_MAX_PAGES))
        self.max_depth = max(0, int(max_depth or DEFAULT_MAX_DEPTH))
        self.domain = urlparse(self.start_url).netloc
        self._browser = None
        self._playwright = None

    async def run(self):
        queue = [(self.start_url, 0)]
        seen = {self.start_url}

        async with httpx.AsyncClient(timeout=FETCH_TIMEOUT, follow_redirects=True) as client:
            try:
                while queue and len(self.results) < self.max_pages:
                    if self.cancelled:
                        self.status = JobStatus.CANCELLED
                        return

                    url, depth = queue.pop(0)
                    page_data = await self._fetch_and_extract(client, url)
                    if page_data:
                        page_data["depth"] = depth
                        self.results.append(page_data)
                        if depth < self.max_depth:
                            for link in page_data.pop("_links", []):
                                if link not in seen and len(seen) < self.max_pages * 20:
                                    seen.add(link)
                                    queue.append((link, depth + 1))
                    self.progress = min(99, int(len(self.results) / self.max_pages * 100))
                    await random_delay(500, 1500)
            finally:
                if self._browser:
                    await self._browser.close()
                if self._playwright:
                    await self._playwright.stop()

    async def _fetch_and_extract(self, client: httpx.AsyncClient, url: str):
        html, text = None, None
        try:
            resp = await client.get(url)
            html = resp.text
            soup = BeautifulSoup(html, "html.parser")
            text = soup.get_text(" ", strip=True)
        except Exception:
            html, text = "", ""

        is_js_heavy = len(text) < 200 and len(html) > 5000
        if is_js_heavy or not html:
            rendered = await self._render_with_playwright(url)
            if rendered:
                html, text = rendered

        if not html:
            return None

        soup = BeautifulSoup(html, "html.parser")
        title = soup.title.get_text(strip=True) if soup.title else ""
        links = []
        for a in soup.select("a[href]"):
            href = a.get("href")
            absolute = urljoin(url, href)
            if urlparse(absolute).netloc == self.domain and not ASSET_EXT_RE.search(absolute):
                links.append(absolute.split("#")[0])

        return {
            "url": url,
            "title": title,
            "emails": extract_emails(text),
            "phones": extract_phones(text),
            "_links": list(dict.fromkeys(links)),
        }

    async def _render_with_playwright(self, url: str):
        try:
            if not self._playwright:
                self._playwright = await async_playwright().start()
                self._browser = await self._playwright.chromium.launch(headless=True)
            page = await self._browser.new_page()
            try:
                await page.goto(url, wait_until="domcontentloaded", timeout=20_000)
                await page.wait_for_timeout(1500)
                html = await page.content()
                text = await page.locator("body").inner_text()
                return html, text
            finally:
                await page.close()
        except Exception as exc:
            print(f"[job {self.job_id}] Playwright fallback failed for {url}: {exc}")
            return None
