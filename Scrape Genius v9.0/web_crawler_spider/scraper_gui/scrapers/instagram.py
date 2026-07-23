"""
scrapers/instagram.py
======================
Instagram profile/keyword scraper. Instagram requires a login for its own
search and blocks unauthenticated API access almost immediately, so - like
the existing ScrapeGenius backend's instagramScraper.js - this uses a
Google-dork SERP discovery pass (site:instagram.com "keyword") to find public
profile URLs, then a stealth Playwright deep-visit of each profile's public
page to read whatever is rendered without login (bio, external link,
follower/following/post counts when Instagram serves them to logged-out
visitors).
"""

import re
from urllib.parse import quote_plus

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, extract_emails, extract_phones, random_delay

DEFAULT_MAX_PROFILES = 1000
NAV_TIMEOUT_MS = 20_000

RESULT_COLUMNS = ["username", "full_name", "bio", "external_link", "followers", "following", "posts", "email", "phone", "profile_url"]

_COUNT_RE = re.compile(r"([\d,.]+[kKmM]?)\s+(followers|following|posts)", re.IGNORECASE)


class InstagramScraper(BaseScraper):
    source_name = "instagram"

    def __init__(self, keywords: str = "", location: str = "", usernames: str = "",
                 max_profiles: int = DEFAULT_MAX_PROFILES):
        super().__init__(keywords=keywords, location=location, usernames=usernames, max_profiles=max_profiles)
        self.keywords = keywords
        self.location = location
        self.usernames = [u.strip().lstrip("@") for u in (usernames or "").split(",") if u.strip()]
        self.max_profiles = max(1, int(max_profiles or DEFAULT_MAX_PROFILES))

    async def run(self):
        async with async_playwright() as pw:
            browser = await pw.chromium.launch(
                headless=True,
                args=["--disable-blink-features=AutomationControlled"],
            )
            try:
                profile_urls = self.usernames and [f"https://www.instagram.com/{u}/" for u in self.usernames]
                if not profile_urls:
                    profile_urls = await self._discover_profiles(browser)

                total = max(len(profile_urls), 1)
                for i, url in enumerate(profile_urls, start=1):
                    if self.cancelled:
                        self.status = JobStatus.CANCELLED
                        return
                    data = await self._scrape_profile(browser, url)
                    if data:
                        self.results.append(data)
                    self.progress = int(i / total * 100)
            finally:
                await browser.close()

    async def _discover_profiles(self, browser):
        full_query = f'site:instagram.com "{self.keywords}" {self.location}'.strip()
        search_url = f"https://www.google.com/search?q={quote_plus(full_query)}&num=100"

        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(30_000)
        urls: "list[str]" = []
        try:
            await page.goto(search_url, wait_until="domcontentloaded")
            await page.wait_for_timeout(1500)
            anchors = await page.locator('a[href*="instagram.com/"]').all()
            seen = set()
            for a in anchors:
                href = await a.get_attribute("href")
                if not href or "/p/" in href or "/reel/" in href or "/explore/" in href:
                    continue
                m = re.search(r"instagram\.com/([A-Za-z0-9._]+)/?", href)
                if not m:
                    continue
                username = m.group(1)
                if username in ("accounts", "about", "explore") or username in seen:
                    continue
                seen.add(username)
                urls.append(f"https://www.instagram.com/{username}/")
                if len(urls) >= self.max_profiles:
                    break
            return urls
        finally:
            await context.close()

    async def _scrape_profile(self, browser, url: str):
        context, page = await create_stealth_context(browser)
        page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
        try:
            await page.goto(url, wait_until="domcontentloaded")
            await page.wait_for_timeout(2500)
            await random_delay(500, 1200)

            page_text = await page.locator("body").inner_text()

            username = url.rstrip("/").rsplit("/", 1)[-1]
            full_name = None
            h1 = page.locator("h1, h2").first
            if await h1.count() > 0:
                try:
                    full_name = (await h1.inner_text()).strip()
                except Exception:
                    full_name = None

            counts = {"followers": None, "following": None, "posts": None}
            for value, label in _COUNT_RE.findall(page_text):
                counts[label.lower()] = value

            external_link = None
            link_el = page.locator('a[href*="l.instagram.com"], a[rel*="me nofollow"]').first
            if await link_el.count() > 0:
                href = await link_el.get_attribute("href")
                external_link = href

            emails = extract_emails(page_text)
            phones = extract_phones(page_text)

            bio_lines = [ln.strip() for ln in page_text.splitlines() if ln.strip()]
            bio = " | ".join(bio_lines[:6]) if bio_lines else None

            return {
                "username": username,
                "full_name": full_name or "N/A",
                "bio": bio or "N/A",
                "external_link": external_link or "N/A",
                "followers": counts["followers"] or "N/A",
                "following": counts["following"] or "N/A",
                "posts": counts["posts"] or "N/A",
                "email": emails[0] if emails else "N/A",
                "phone": phones[0] if phones else "N/A",
                "profile_url": url,
            }
        except PlaywrightTimeoutError:
            print(f"[job {self.job_id}] timeout, skipping {url}")
            return None
        except Exception as exc:
            print(f"[job {self.job_id}] error on {url}: {exc}")
            return None
        finally:
            await context.close()
