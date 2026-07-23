"""
scrapers/b2b_directory.py
===========================
B2B Directory scraper for IndiaMart / JustDial / Sulekha (and any other
generic business-directory URL), porting lib/scrapers/directory.ts:
plain httpx fetch first, falls back to Playwright if the page looks
blocked (tiny body / CAPTCHA-shaped text) or the fetch throws, then
extracts contacts plus a provider-tuned company-name guess.

Note (same caveat as the Node source): these per-provider name
selectors are best-effort, not guaranteed-current against live markup -
directory sites change their DOM frequently.
"""

import re

import httpx
from bs4 import BeautifulSoup
from playwright.async_api import async_playwright

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, extract_emails, extract_phones, random_delay

FETCH_TIMEOUT = 12
DEFAULT_MAX_URLS = 1000

RESULT_COLUMNS = ["url", "provider", "company_name", "emails", "phones"]

BLOCKED_RE = re.compile(r"captcha|access denied|are you a human|attention required", re.IGNORECASE)

NAME_SELECTORS = {
    "indiamart": ["h1", ".compname", ".company-name", ".fs20"],
    "justdial": ["h1", ".jcn", ".resultbox_title", ".title-bold"],
    "sulekha": ["h1", ".company-name", ".biz-name"],
}

SEARCH_URLS = {
    "indiamart": "https://dir.indiamart.com/search.mp?ss={q}",
    "justdial": "https://www.justdial.com/search?q={q}",
    "sulekha": "https://www.sulekha.com/{q}",
}


def _looks_blocked(html: str) -> bool:
    return len(html) < 2000 or bool(BLOCKED_RE.search(html))


class B2BDirectoryScraper(BaseScraper):
    source_name = "b2b_directory"

    def __init__(self, query: str, provider: str = "indiamart", max_urls: int = DEFAULT_MAX_URLS):
        super().__init__(query=query, provider=provider, max_urls=max_urls)
        self.query = query
        self.provider = (provider or "indiamart").strip().lower()
        self.max_urls = max(1, int(max_urls or DEFAULT_MAX_URLS))

    async def run(self):
        from urllib.parse import quote_plus
        search_url = SEARCH_URLS.get(self.provider, SEARCH_URLS["indiamart"]).format(q=quote_plus(self.query))

        async with httpx.AsyncClient(timeout=FETCH_TIMEOUT, follow_redirects=True) as client:
            listing_urls = await self._discover_listing_urls(client, search_url)
            listing_urls = listing_urls[: self.max_urls]

            total = max(len(listing_urls), 1)
            browser = None
            playwright = None
            try:
                for i, url in enumerate(listing_urls, start=1):
                    if self.cancelled:
                        self.status = JobStatus.CANCELLED
                        return

                    html = await self._fetch_or_blocked(client, url)
                    if html is None:
                        browser, playwright = await self._ensure_browser(browser, playwright)
                        html = await self._render(browser, url)

                    if html:
                        self.results.append(self._extract(url, html))
                    self.progress = int(i / total * 100)
                    await random_delay(300, 800)
            finally:
                if browser:
                    await browser.close()
                if playwright:
                    await playwright.stop()

    async def _discover_listing_urls(self, client: httpx.AsyncClient, search_url: str):
        try:
            resp = await client.get(search_url)
            soup = BeautifulSoup(resp.text, "html.parser")
        except Exception:
            return []
        urls = []
        for a in soup.select("a[href]"):
            href = a.get("href")
            if href and href.startswith("http") and self.provider in href:
                urls.append(href)
        return list(dict.fromkeys(urls))

    async def _fetch_or_blocked(self, client: httpx.AsyncClient, url: str):
        try:
            resp = await client.get(url)
            if _looks_blocked(resp.text):
                return None
            return resp.text
        except Exception:
            return None

    async def _ensure_browser(self, browser, playwright):
        if browser:
            return browser, playwright
        playwright = await async_playwright().start()
        browser = await playwright.chromium.launch(headless=True)
        return browser, playwright

    async def _render(self, browser, url: str):
        context, page = await create_stealth_context(browser)
        try:
            await page.goto(url, wait_until="domcontentloaded", timeout=20_000)
            await page.wait_for_timeout(1500)
            return await page.content()
        except Exception as exc:
            print(f"[job {self.job_id}] Playwright render failed for {url}: {exc}")
            return None
        finally:
            await context.close()

    def _extract(self, url: str, html: str) -> dict:
        soup = BeautifulSoup(html, "html.parser")
        text = soup.get_text(" ", strip=True)

        company_name = None
        for selector in NAME_SELECTORS.get(self.provider, ["h1"]):
            el = soup.select_one(selector)
            if el and el.get_text(strip=True):
                company_name = el.get_text(strip=True)
                break

        return {
            "url": url,
            "provider": self.provider,
            "company_name": company_name or "N/A",
            "emails": extract_emails(text),
            "phones": extract_phones(text),
        }
