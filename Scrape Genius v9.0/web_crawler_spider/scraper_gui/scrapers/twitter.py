"""
scrapers/twitter.py
=====================
Twitter/X search scraper. Uses a stealth Playwright context to load the
logged-out search results timeline (https://x.com/search?q=...&f=live),
scrolls to load more tweets, and extracts each tweet's author handle,
body text, and timestamp from the rendered `article[data-testid="tweet"]`
cells - the same DOM shape ScrapeGenius's twitterScraper.js/
twitterGraphqlClient.js target.
"""

from urllib.parse import quote_plus

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, extract_emails, extract_phones, random_delay

DEFAULT_MAX_TWEETS = 1000
NAV_TIMEOUT_MS = 30_000
STALE_ROUND_LIMIT = 8

RESULT_COLUMNS = ["handle", "tweet_text", "timestamp", "tweet_url", "email", "phone"]


class TwitterScraper(BaseScraper):
    source_name = "twitter"

    def __init__(self, keywords: str, max_tweets: int = DEFAULT_MAX_TWEETS):
        super().__init__(keywords=keywords, max_tweets=max_tweets)
        self.keywords = keywords
        self.max_tweets = max(1, int(max_tweets or DEFAULT_MAX_TWEETS))

    async def run(self):
        async with async_playwright() as pw:
            browser = await pw.chromium.launch(
                headless=True,
                args=["--disable-blink-features=AutomationControlled"],
            )
            try:
                search_url = f"https://x.com/search?q={quote_plus(self.keywords)}&f=live"
                context, page = await create_stealth_context(browser)
                page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
                try:
                    await page.goto(search_url, wait_until="domcontentloaded")
                    await page.wait_for_timeout(3000)

                    try:
                        await page.wait_for_selector('article[data-testid="tweet"]', timeout=15_000)
                    except PlaywrightTimeoutError:
                        return

                    seen_ids = set()
                    stale_rounds = 0
                    while len(self.results) < self.max_tweets and stale_rounds < STALE_ROUND_LIMIT:
                        if self.cancelled:
                            self.status = JobStatus.CANCELLED
                            return

                        cells = await page.locator('article[data-testid="tweet"]').all()
                        before = len(seen_ids)
                        for cell in cells:
                            if len(self.results) >= self.max_tweets:
                                break
                            data = await self._extract_tweet(cell)
                            if data and data["tweet_url"] not in seen_ids:
                                seen_ids.add(data["tweet_url"])
                                self.results.append(data)

                        stale_rounds = stale_rounds + 1 if len(seen_ids) == before else 0
                        self.progress = min(99, int(len(self.results) / max(self.max_tweets, 1) * 100))
                        await page.mouse.wheel(0, 4000)
                        await random_delay(1200, 2200)
                finally:
                    await context.close()
            finally:
                await browser.close()

    async def _extract_tweet(self, cell):
        try:
            handle = None
            handle_el = cell.locator('a[href^="/"][role="link"] span:has-text("@")').first
            if await handle_el.count() > 0:
                handle = (await handle_el.inner_text()).strip()

            text_el = cell.locator('div[data-testid="tweetText"]').first
            tweet_text = (await text_el.inner_text()).strip() if await text_el.count() > 0 else ""

            time_el = cell.locator("time").first
            timestamp = await time_el.get_attribute("datetime") if await time_el.count() > 0 else None

            link_el = cell.locator("a:has(time)").first
            href = await link_el.get_attribute("href") if await link_el.count() > 0 else None
            tweet_url = f"https://x.com{href}" if href else None
            if not tweet_url:
                return None

            emails = extract_emails(tweet_text)
            phones = extract_phones(tweet_text)

            return {
                "handle": handle or "N/A",
                "tweet_text": tweet_text or "N/A",
                "timestamp": timestamp or "N/A",
                "tweet_url": tweet_url,
                "email": emails[0] if emails else "N/A",
                "phone": phones[0] if phones else "N/A",
            }
        except Exception:
            return None
