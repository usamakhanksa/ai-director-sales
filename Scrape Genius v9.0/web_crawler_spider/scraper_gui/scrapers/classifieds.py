"""
scrapers/classifieds.py
=========================
Classified-ads scraper covering Haraj (full RTL Arabic support) plus the
same 11 Saudi/Gulf/MENA marketplaces supported by the Node
harajScraper.js (backend/src/scrapers/harajScraper.js) and the
"Select sites to search" list in the dashboard's Classified tool:
Haraj, OpenSooq, Dubizzle (sa.dubizzle.com), OLX Kuwait, OLX Egypt,
Mubawab (Saudi Arabia), Bayut (Saudi Arabia), Property Finder (Saudi
Arabia), Syarah, Expatriates.com, 4Sale Kuwait — via a per-site config
table of search-URL template + CSS selectors. Sites not in SITE_CONFIGS
fall back to a heuristic pass: any anchor whose visible text is followed
by a price-shaped number.

Anti-bot note: these sites change markup frequently and some (Dubizzle,
PropertyFinder) serve most listings via client-side XHR that a plain
Playwright DOM scrape may miss - this covers whatever the initial
server-rendered / hydrated DOM exposes, same ceiling as the existing
harajScraper.js this ports from.
"""

import re
from urllib.parse import quote, urljoin

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError

from .base import BaseScraper, JobStatus
from .utils import create_stealth_context, extract_emails, extract_phones, random_delay

DEFAULT_MAX_LISTINGS = 1000
NAV_TIMEOUT_MS = 20_000

RESULT_COLUMNS = ["title", "price", "location", "phone", "email", "listing_url", "site"]

PRICE_RE = re.compile(
    r"[\d,]+\s*(?:ريال|SAR|جنيه|EGP|دينار|KWD|QAR|AED|درهم|ر\.س|\$|€|£)", re.UNICODE
)
LOCATION_RE = re.compile(
    r"(?:الرياض|جدة|مكة|الدمام|Riyadh|Jeddah|Mecca|Dammam|Kuwait City|Dubai|Cairo|Doha)[^،,\n]*",
    re.IGNORECASE,
)

# site key -> search url template with {q}, listing card selector, title selector,
# price selector, link selector. Mirrors CLASSIFIED_SITES in harajScraper.js so
# both scrapers cover the same platforms with the same site keys (lower-cased).
SITE_CONFIGS = {
    "haraj": {
        # Haraj is a React/Vite SPA that renders listing cards with stable
        # data-testid attributes rather than semantic tags/class names (see
        # haraj_dump.html captured 2026-07-14) - Tailwind utility classes are
        # not selector-stable, data-testid is.
        "search_url": "https://haraj.com.sa/search/{q}",
        "card": "[data-testid='post-item']",
        "title": "[data-testid='post-title-link'] h3",
        "price": None,  # price is absent on most cards (services/jobs); PRICE_RE fallback on card text handles it
        "link": "a[data-testid='post-title-link']",
    },
    "opensooq": {
        "search_url": "https://sa.opensooq.com/en/search?term={q}",
        "card": "div[data-testid='postCard'], .postListItem, .listing-item, article",
        "title": "h2, h3, .postTitle, .title",
        "price": ".priceColor, .postPrice",
        "link": "a",
    },
    "dubizzle": {
        "search_url": "https://sa.dubizzle.com/classifieds/?q={q}",
        "card": "article, [class*='listing'], [class*='AdTile']",
        "title": "h2, h3, [class*='title']",
        "price": "[aria-label='Price']",
        "link": "a",
    },
    "olx_kw": {
        "search_url": "https://olx.com.kw/en/search/?q={q}",
        "card": "[data-aut-id='itemBox'], .listing-item",
        "title": "[data-aut-id='itemTitle'], h3",
        "price": None,
        "link": "a",
    },
    "olx_eg": {
        "search_url": "https://eg.olx.com.eg/en/search/?q={q}",
        "card": "[data-aut-id='itemBox'], .listing-item",
        "title": "[data-aut-id='itemTitle'], h3",
        "price": None,
        "link": "a",
    },
    "mubawab_sa": {
        "search_url": "https://mubawab.sa/en/search/?q={q}",
        "card": "li.listingBox, article, [class*='listing']",
        "title": "h3, .listingTitle",
        "price": None,
        "link": "a",
    },
    "bayut": {
        "search_url": "https://www.bayut.sa/en/property/search/?q={q}",
        "card": "article, [class*='listing'], [class*='PropertyCard']",
        "title": "h3, [class*='title'], [class*='heading']",
        "price": None,
        "link": "a",
    },
    "propertyfinder": {
        "search_url": "https://www.propertyfinder.sa/en/search?q={q}",
        "card": "article, [class*='card'], [class*='listing']",
        "title": "h3, [class*='title']",
        "price": None,
        "link": "a",
    },
    "syarah": {
        "search_url": "https://syarah.com/search?q={q}",
        "card": "[class*='car-card'], article, [class*='listing']",
        "title": "h2, h3, [class*='title']",
        "price": None,
        "link": "a",
    },
    "expatriates": {
        "search_url": "https://www.expatriates.com/classifieds/saudi-arabia/?q={q}",
        "card": ".listing, article, [class*='ad']",
        "title": "h2, h3, .listing-title",
        "price": None,
        "link": "a",
    },
    "forsale_kw": {
        "search_url": "https://4sale.com.kw/en/search?q={q}",
        "card": "article, [class*='card'], [class*='listing']",
        "title": "h3, [class*='title']",
        "price": None,
        "link": "a",
    },
}

# Accept the same site keys used by the dashboard UI (SITES in
# app/dashboard/tools/classified/page.tsx) and the Node scraper's
# CLASSIFIED_SITES, in addition to the lower-case keys above.
SITE_KEY_ALIASES = {
    "HARAJ": "haraj",
    "OPENSOOQ": "opensooq",
    "DUBIZZLE": "dubizzle",
    "OLX_KW": "olx_kw",
    "OLX_EG": "olx_eg",
    "OLX": "olx_eg",
    "MUBAWAB_SA": "mubawab_sa",
    "BAYUT": "bayut",
    "PROPERTYFINDER": "propertyfinder",
    "SYARAH": "syarah",
    "EXPATRIATES": "expatriates",
    "FORSALE_KW": "forsale_kw",
}


class ClassifiedsScraper(BaseScraper):
    source_name = "classifieds"

    def __init__(self, query: str, site: str = "haraj", max_listings: int = DEFAULT_MAX_LISTINGS):
        super().__init__(query=query, site=site, max_listings=max_listings)
        self.query = query
        raw_site = (site or "haraj").strip()
        self.site = SITE_KEY_ALIASES.get(raw_site.upper(), raw_site.lower())
        self.max_listings = max(1, int(max_listings or DEFAULT_MAX_LISTINGS))

    async def run(self):
        config = SITE_CONFIGS.get(self.site)
        if not config:
            self.error_message = (
                f"'{self.site}' has no dedicated selector config; falling back to a generic "
                "title/price heuristic which may miss listings on JS-heavy sites."
            )
            config = {
                "search_url": None,
                "card": "article, li, div[class*='card'], div[class*='listing']",
                "title": "h2, h3, a",
                "price": None,
                "link": "a",
            }

        async with async_playwright() as pw:
            browser = await pw.chromium.launch(
                headless=True,
                args=["--disable-blink-features=AutomationControlled"],
            )
            try:
                # Node's harajScraper.js (and browsers generally) percent-encode search
                # terms with encodeURIComponent, which turns a space into "%20" - not
                # "+". Haraj's search URL is a path segment (/search/<term>), where a
                # literal "+" is NOT treated as a space by the server, so quote_plus
                # (form-encoding convention) silently broke every multi-word Haraj
                # query; `quote(..., safe="")` matches encodeURIComponent instead.
                url = (config["search_url"] or f"https://www.google.com/search?q={{q}}").format(
                    q=quote(f"{self.query} site:{self.site}.com" if not config["search_url"] else self.query, safe="")
                )
                context, page = await create_stealth_context(browser)
                page.set_default_navigation_timeout(NAV_TIMEOUT_MS)
                try:
                    await page.goto(url, wait_until="domcontentloaded")
                    await page.wait_for_timeout(2500)

                    cards = await page.locator(config["card"]).all()
                    total = max(len(cards[: self.max_listings]), 1)
                    for i, card in enumerate(cards[: self.max_listings], start=1):
                        if self.cancelled:
                            self.status = JobStatus.CANCELLED
                            return
                        data = await self._extract_card(card, config)
                        if data:
                            self.results.append(data)
                        self.progress = int(i / total * 100)
                        await random_delay(150, 400)
                finally:
                    await context.close()
            finally:
                await browser.close()

    async def _extract_card(self, card, config):
        try:
            title = None
            title_el = card.locator(config["title"]).first
            if await title_el.count() > 0:
                title = (await title_el.inner_text()).strip()

            price = None
            if config["price"]:
                price_el = card.locator(config["price"]).first
                if await price_el.count() > 0:
                    price = (await price_el.inner_text()).strip()
            if not price:
                card_text = await card.inner_text()
                m = PRICE_RE.search(card_text)
                price = m.group(0).strip() if m else None

            link_el = card.locator(config["link"]).first
            href = await link_el.get_attribute("href") if await link_el.count() > 0 else None
            if href and href.startswith("/"):
                href = urljoin(SITE_CONFIGS.get(self.site, {}).get("search_url", "https://example.com/"), href)

            card_text = await card.inner_text()
            phones = extract_phones(card_text)
            emails = extract_emails(card_text)
            location_match = LOCATION_RE.search(card_text)

            if not title and not href:
                return None

            return {
                "title": title or "N/A",
                "price": price or "N/A",
                "location": location_match.group(0).strip()[:200] if location_match else "N/A",
                "phone": phones[0] if phones else "N/A",
                "email": emails[0] if emails else "N/A",
                "listing_url": href or "N/A",
                "site": self.site,
            }
        except Exception:
            return None
