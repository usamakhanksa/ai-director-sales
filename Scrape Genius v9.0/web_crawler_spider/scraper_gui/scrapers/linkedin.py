"""
scrapers/linkedin.py
=====================
LinkedIn people-search scraper. Direct LinkedIn scraping requires a login
and is aggressively anti-bot, so the default path uses a Google-dork SERP
scrape (site:linkedin.com/in/ "keywords"), reading name/headline/location
straight from the SERP snippet - the same approach as the existing
ScrapeGenius backend's linkedinScraper.js.

If the caller supplies a `session_cookie` (the LinkedIn `li_at` cookie
value), the scraper additionally does a best-effort deep visit of each
profile while authenticated to pull richer public data. This is optional
and always wrapped in try/except so a blocked/challenged profile never
fails the whole job.
"""

import re
from urllib.parse import quote_plus

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, guess_email, random_delay

DEFAULT_MAX_RESULTS = 1000
NAV_TIMEOUT_MS = 20_000

RESULT_COLUMNS = ["name", "headline", "location", "profile_url", "snippet", "email_guessed"]


class LinkedInScraper(BaseScraper):
    source_name = "linkedin"

    def __init__(self, keywords: str, location: str = "", max_results: int = DEFAULT_MAX_RESULTS,
                 session_cookie: str = "", domain_for_email_guess: str = ""):
        super().__init__(keywords=keywords, location=location, max_results=max_results)
        self.keywords = keywords
        self.location = location
        self.max_results = max(1, int(max_results or DEFAULT_MAX_RESULTS))
        self.session_cookie = (session_cookie or "").strip()
        self.domain_for_email_guess = (domain_for_email_guess or "").strip()

    async def run(self):
        async with async_playwright() as pw:
            browser = await pw.chromium.launch(
                headless=True,
                args=["--disable-blink-features=AutomationControlled"],
            )
            try:
                serp_hits = await self._google_dork_search(browser)
                total = max(len(serp_hits), 1)

                for i, hit in enumerate(serp_hits, start=1):
                    if self.cancelled:
                        self.status = JobStatus.CANCELLED
                        return

                    record = dict(hit)
                    if self.session_cookie:
                        deep = await self._deep_scrape_profile(browser, hit["profile_url"])
                        if deep:
                            record.update(deep)

                    if not record.get("email_guessed") and self.domain_for_email_guess:
                        first, last = self._split_name(record.get("name", ""))
                        record["email_guessed"] = guess_email(first, last, self.domain_for_email_guess)
                    else:
                        record.setdefault("email_guessed", None)

                    self.results.append(record)
                    self.progress = int(i / total * 100)
                    await random_delay(500, 1200)
            finally:
                await browser.close()

    @staticmethod
    def _split_name(full_name: str):
        parts = [p for p in re.split(r"\s+", full_name.strip()) if p]
        if len(parts) >= 2:
            return parts[0], parts[-1]
        if len(parts) == 1:
            return parts[0], ""
        return "", ""

    # ---- SERP dork phase ------------------------------------------------------
    async def _google_dork_search(self, browser):
        query_parts = [f'site:linkedin.com/in/ "{self.keywords}"']
        if self.location:
            query_parts.append(f'"{self.location}"')
        dork_query = " ".join(query_parts)
        search_url = f"https://www.google.com/search?q={quote_plus(dork_query)}&num=30"

        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        hits = []
        try:
            await page.goto(search_url, wait_until="domcontentloaded")
            await page.wait_for_timeout(1500)

            result_blocks = await page.locator("div#search div.g, div#rso div.g, div.MjjYud").all()
            seen_urls = set()
            for block in result_blocks:
                if len(hits) >= self.max_results:
                    break
                try:
                    link_el = block.locator("a").first
                    href = await link_el.get_attribute("href")
                    if not href or "linkedin.com/in/" not in href:
                        continue
                    profile_url = href.split("?")[0]
                    if profile_url in seen_urls:
                        continue
                    seen_urls.add(profile_url)

                    title_el = block.locator("h3").first
                    title = (await title_el.inner_text()).strip() if await title_el.count() > 0 else ""

                    snippet_el = block.locator("div[data-sncf], div.VwiC3b").first
                    snippet = (await snippet_el.inner_text()).strip() if await snippet_el.count() > 0 else ""

                    name, headline = self._parse_title(title)
                    location = self._guess_location_from_snippet(snippet)

                    hits.append({
                        "name": name or title or "N/A",
                        "headline": headline or "N/A",
                        "location": location or self.location or "N/A",
                        "profile_url": profile_url,
                        "snippet": snippet or "N/A",
                    })
                except Exception:
                    continue
        except PlaywrightTimeoutError:
            pass
        finally:
            await context.close()
        return hits

    @staticmethod
    def _parse_title(title: str):
        # Google SERP titles for LinkedIn are typically "Name - Headline | LinkedIn"
        title = re.sub(r"\s*\|\s*LinkedIn\s*$", "", title, flags=re.I).strip()
        if " - " in title:
            name, headline = title.split(" - ", 1)
            return name.strip(), headline.strip()
        return title.strip(), ""

    @staticmethod
    def _guess_location_from_snippet(snippet: str):
        m = re.search(r"Location:\s*([^·|]+)", snippet, flags=re.I)
        return m.group(1).strip() if m else None

    # ---- optional authenticated deep scrape ------------------------------------
    async def _deep_scrape_profile(self, browser, profile_url: str):
        """Best-effort authenticated visit using the supplied li_at cookie.
        Never raises - any failure (login wall, checkpoint, layout change)
        just means we keep the SERP-only data for this profile."""
        context = await browser.new_context()
        try:
            await context.add_cookies([{
                "name": "li_at",
                "value": self.session_cookie,
                "domain": ".linkedin.com",
                "path": "/",
                "httpOnly": True,
                "secure": True,
            }])
            page = await context.new_page()
            page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
            await page.goto(profile_url, wait_until="domcontentloaded")
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
            headline = await text_or_none(".text-body-medium.break-words")
            location = await text_or_none(".text-body-small.inline.t-black--light.break-words")
            about = await text_or_none("#about ~ div .display-flex span[aria-hidden='true']")

            experience = []
            exp_items = await page.locator("#experience ~ div ul li").all()
            for item in exp_items[:10]:
                try:
                    txt = (await item.inner_text()).strip()
                    if txt:
                        experience.append(txt.replace("\n", " | "))
                except Exception:
                    continue

            education = []
            edu_items = await page.locator("#education ~ div ul li").all()
            for item in edu_items[:10]:
                try:
                    txt = (await item.inner_text()).strip()
                    if txt:
                        education.append(txt.replace("\n", " | "))
                except Exception:
                    continue

            deep = {}
            if name:
                deep["name"] = name
            if headline:
                deep["headline"] = headline
            if location:
                deep["location"] = location
            if about:
                deep["about"] = about
            if experience:
                deep["experience"] = experience
            if education:
                deep["education"] = education
            return deep
        except Exception as exc:
            print(f"[job {self.job_id}] deep profile scrape failed for {profile_url}: {exc}")
            return None
        finally:
            await context.close()
