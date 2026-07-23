"""
scrapers/google_maps.py
========================
Async, web-friendly port of the CLI google_maps_scraper.py. Same scraping
logic (search -> collect listing URLs -> visit each -> extract fields ->
optionally crawl the business website for an email), but updates
self.progress/self.results incrementally instead of printing to a console,
so the FastAPI job manager and browser UI can poll it live.
"""

import re
import time
from urllib.parse import quote_plus

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, extract_emails, extract_phones, random_delay

NAV_TIMEOUT_MS = 15_000  # ~15s per listing, per the spec
DEFAULT_MAX_LISTINGS = 1000

RESULT_COLUMNS = ["Name", "Address", "Rating", "Price", "Phone", "Website", "Email", "source_url", "Time (seconds)"]


class GoogleMapsScraper(BaseScraper):
    source_name = "google_maps"

    def __init__(self, query: str, location: str = "Saudi Arabia", max_listings: int = DEFAULT_MAX_LISTINGS):
        super().__init__(query=query, location=location, max_listings=max_listings)
        self.query = query
        self.location = location
        self.max_listings = max(1, int(max_listings or DEFAULT_MAX_LISTINGS))

    async def run(self):
        async with async_playwright() as pw:
            browser = await pw.chromium.launch(
                headless=True,
                args=["--disable-blink-features=AutomationControlled"],
            )
            try:
                urls = await self._collect_listing_urls(browser)
                total = max(len(urls), 1)

                for i, url in enumerate(urls, start=1):
                    if self.cancelled:
                        self.status = JobStatus.CANCELLED
                        return

                    data = await self._scrape_listing_with_retry(browser, url)
                    if data:
                        self.results.append(data)

                    self.progress = int(i / total * 100)
            finally:
                await browser.close()

    # ---- search phase --------------------------------------------------------
    async def _collect_listing_urls(self, browser):
        full_query = f"{self.query} {self.location}".strip()
        search_url = f"https://www.google.com/maps/search/{quote_plus(full_query)}"

        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(30_000)
        try:
            await page.goto(search_url, wait_until="domcontentloaded")
            await page.wait_for_timeout(3000)

            feed_selector = 'div[role="feed"]'
            try:
                await page.wait_for_selector(feed_selector, timeout=15_000)
            except PlaywrightTimeoutError:
                # single-result direct redirect
                if "/maps/place" in page.url:
                    return [page.url]
                return []

            urls: set = set()
            stale_rounds = 0
            while len(urls) < self.max_listings and stale_rounds < 6:
                if self.cancelled:
                    break
                anchors = await page.locator(f'{feed_selector} a[href*="/maps/place"]').all()
                before = len(urls)
                for a in anchors:
                    href = await a.get_attribute("href")
                    if href:
                        urls.add(href.split("?")[0])
                    if len(urls) >= self.max_listings:
                        break

                stale_rounds = stale_rounds + 1 if len(urls) == before else 0
                await page.mouse.wheel(0, 4000)
                await random_delay(1000, 1800)

            return list(urls)[: self.max_listings]
        finally:
            await context.close()

    # ---- detail phase --------------------------------------------------------
    async def _scrape_listing_with_retry(self, browser, url: str, retries: int = 1):
        for attempt in range(retries + 1):
            try:
                return await self._scrape_listing(browser, url)
            except PlaywrightTimeoutError:
                if attempt >= retries:
                    print(f"[job {self.job_id}] timeout, skipping {url}")
                    return None
            except Exception as exc:
                if attempt >= retries:
                    print(f"[job {self.job_id}] error on {url}: {exc}")
                    return None
        return None

    async def _scrape_listing(self, browser, url: str) -> dict:
        start = time.time()
        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        try:
            await page.goto(url, wait_until="domcontentloaded")
            await page.wait_for_timeout(2000)

            async def text_or_none(selector):
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

            price_text = await text_or_none('span[aria-label*="Price"]')
            price = None
            if price_text:
                m = re.search(r"[\d,]+", price_text)
                price = m.group(0) if m else None

            phones = extract_phones(phone_raw or "")
            phone = phones[0] if phones else phone_raw

            email = None
            if website:
                email = await self._extract_email_from_website(browser, website)

            elapsed = round(time.time() - start, 2)
            return {
                "Name": name or "N/A",
                "Address": address or "N/A",
                "Rating": rating or "N/A",
                "Price": price or "N/A",
                "Phone": phone or "N/A",
                "Website": website or "N/A",
                "Email": email or "N/A",
                "source_url": url,
                "Time (seconds)": elapsed,
            }
        finally:
            await context.close()

    async def _extract_email_from_website(self, browser, website_url: str):
        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        try:
            await page.goto(website_url, wait_until="domcontentloaded")
            await page.wait_for_timeout(1200)
            html = await page.content()
            emails = extract_emails(html)
            if emails:
                return emails[0]

            contact_link = page.locator('a[href*="contact"]').first
            if await contact_link.count() > 0:
                href = await contact_link.get_attribute("href")
                if href:
                    await page.goto(href, wait_until="domcontentloaded")
                    await page.wait_for_timeout(1200)
                    html = await page.content()
                    emails = extract_emails(html)
                    if emails:
                        return emails[0]
        except Exception as exc:
            print(f"[job {self.job_id}] website email extraction failed for {website_url}: {exc}")
        finally:
            await context.close()
        return None
