"""
scrapers/google_news.py
=========================
Google News RSS scraper - no API key required. Fetches
https://news.google.com/rss/search?q=...&hl=...&gl=...&ceid=...:lang and
parses the standard RSS <item> fields into {title, link, pubDate, source}.
"""

import xml.etree.ElementTree as ET
from urllib.parse import quote_plus

import httpx

FETCH_TIMEOUT = 15

_LANG_COUNTRY = {
    "en": ("en-US", "US"),
    "ar": ("ar", "SA"),
}


async def fetch_google_news(keyword: str, lang: str = "en", max_results: int = 50) -> dict:
    keyword = (keyword or "").strip()
    if not keyword:
        return {"error": "keyword is required"}

    hl, gl = _LANG_COUNTRY.get(lang, _LANG_COUNTRY["en"])
    ceid = f"{gl}:{hl.split('-')[0]}"
    url = f"https://news.google.com/rss/search?q={quote_plus(keyword)}&hl={hl}&gl={gl}&ceid={ceid}"

    try:
        async with httpx.AsyncClient(timeout=FETCH_TIMEOUT) as client:
            resp = await client.get(url)
            resp.raise_for_status()
    except Exception as exc:
        return {"error": f"Could not fetch Google News RSS: {exc}"}

    try:
        root = ET.fromstring(resp.text)
    except ET.ParseError as exc:
        return {"error": f"Could not parse RSS response: {exc}"}

    items = []
    for item in root.findall(".//item")[:max_results]:
        source_el = item.find("source")
        items.append({
            "title": (item.findtext("title") or "").strip(),
            "link": (item.findtext("link") or "").strip(),
            "pubDate": (item.findtext("pubDate") or "").strip(),
            "source": (source_el.text if source_el is not None else "N/A"),
        })

    return {"keyword": keyword, "lang": lang, "count": len(items), "results": items}
