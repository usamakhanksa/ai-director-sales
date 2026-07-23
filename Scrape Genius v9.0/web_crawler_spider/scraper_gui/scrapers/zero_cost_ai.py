"""
scrapers/zero_cost_ai.py
==========================
"Zero-cost AI scraper" - fetches clean Markdown for any URL via the free
r.jina.ai reader proxy (no API key), then extracts emails/phones and a
naive company-name guess (first non-empty markdown heading/line) with
bilingual (Arabic/English) regex support.
"""

import httpx

from .utils import extract_emails, extract_phones

FETCH_TIMEOUT = 25


def _guess_company_name(markdown: str):
    for line in markdown.splitlines():
        line = line.strip().lstrip("#").strip()
        if line:
            return line[:120]
    return None


async def scrape_via_reader(url: str) -> dict:
    url = (url or "").strip()
    if not url:
        return {"error": "url is required"}
    if not url.startswith(("http://", "https://")):
        url = f"https://{url}"

    reader_url = f"https://r.jina.ai/{url}"
    try:
        async with httpx.AsyncClient(timeout=FETCH_TIMEOUT) as client:
            resp = await client.get(reader_url)
            resp.raise_for_status()
    except Exception as exc:
        return {"source_url": url, "error": f"Could not fetch via r.jina.ai reader: {exc}"}

    markdown = resp.text
    return {
        "source_url": url,
        "company_name_guess": _guess_company_name(markdown) or "N/A",
        "emails": extract_emails(markdown),
        "phones": extract_phones(markdown),
        "markdown": markdown[:20_000],  # cap payload; full text is still on r.jina.ai if needed
    }
