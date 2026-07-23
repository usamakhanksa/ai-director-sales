"""
scrapers/contact_scraper.py
=============================
Direct email/phone extraction from a URL (plain HTTP fetch, no browser -
this is a lightweight instant tool, not a stealth deep-crawl) or from
raw pasted text.
"""

import httpx
from bs4 import BeautifulSoup

from .utils import extract_emails, extract_phones

FETCH_TIMEOUT = 15
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
                  "(KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
}


async def extract_contacts(url: str = "", text: str = "") -> dict:
    url = (url or "").strip()
    text = (text or "").strip()

    if url:
        try:
            async with httpx.AsyncClient(timeout=FETCH_TIMEOUT, follow_redirects=True, headers=HEADERS) as client:
                resp = await client.get(url)
                resp.raise_for_status()
        except Exception as exc:
            return {"source": url, "error": f"Could not fetch URL: {exc}"}

        soup = BeautifulSoup(resp.text, "html.parser")
        page_text = soup.get_text(" ")
        # mailto:/tel: links catch obfuscated-in-text but present-in-markup contacts too
        mailto = [a["href"].replace("mailto:", "").split("?")[0]
                  for a in soup.select('a[href^="mailto:"]')]
        tel = [a["href"].replace("tel:", "") for a in soup.select('a[href^="tel:"]')]

        emails = list(dict.fromkeys(extract_emails(page_text) + mailto))
        phones = list(dict.fromkeys(extract_phones(page_text) + tel))
        return {"source": url, "emails": emails, "phones": phones}

    if text:
        return {"source": "raw_text", "emails": extract_emails(text), "phones": extract_phones(text)}

    return {"error": "Provide either a url or text"}
