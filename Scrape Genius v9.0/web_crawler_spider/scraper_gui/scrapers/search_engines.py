"""
scrapers/search_engines.py
============================
Direct HTTP + parser search-engine scrapers - ports the logic from the
Node app's lib/search-engines/engines/{google,bing,yahoo,duckduckgo}.ts
scrape-fallback path (no headless browser, no paid API key required).

Each engine: rotate a real desktop user-agent, add human-ish headers,
fetch the engine's plain HTML search page, parse with BeautifulSoup
using the same selectors as the Node version, and decode each engine's
redirect-wrapped result URL back to the real target URL.
"""

import base64
import random
import re
from urllib.parse import quote_plus, parse_qs, urlparse

import httpx
from bs4 import BeautifulSoup

FETCH_TIMEOUT = 15

USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0",
]

CAPTCHA_MARKERS = (
    "recaptcha", "captcha-form", "unusual traffic", "/sorry/index",
    "consent.google.com", "geo.captcha-delivery.com", "verify you are human", "px-captcha",
)


class CaptchaDetectedError(Exception):
    pass


def _headers():
    return {
        "User-Agent": random.choice(USER_AGENTS),
        "Accept-Language": "en-US,en;q=0.9",
        "DNT": "1",
        "Sec-Fetch-Mode": "navigate",
        "Sec-Fetch-Dest": "document",
    }


async def _fetch_html(url: str) -> str:
    async with httpx.AsyncClient(timeout=FETCH_TIMEOUT, follow_redirects=True) as client:
        resp = await client.get(url, headers=_headers())
        resp.raise_for_status()
        html = resp.text

    lowered = html[:20_000].lower()
    if any(marker in lowered for marker in CAPTCHA_MARKERS):
        raise CaptchaDetectedError(f"CAPTCHA/consent wall detected for {url}")
    return html


def _decode_bing_redirect(href: str) -> str:
    m = re.search(r"[?&]u=a1([^&]+)", href)
    if not m:
        return href
    b64 = m.group(1).replace("-", "+").replace("_", "/")
    b64 += "=" * (-len(b64) % 4)
    try:
        return base64.b64decode(b64).decode("utf-8", errors="replace")
    except Exception:
        return href


def _decode_ddg_redirect(href: str) -> str:
    if "duckduckgo.com/l/" not in href:
        return href
    qs = parse_qs(urlparse(href).query)
    return qs.get("uddg", [href])[0]


def _decode_yahoo_redirect(href: str) -> str:
    m = re.search(r"/RU=([^/]+)/", href)
    if not m:
        return href
    from urllib.parse import unquote
    return unquote(m.group(1))


async def search_google(query: str, num: int = 10) -> list:
    html = await _fetch_html(f"https://www.google.com/search?q={quote_plus(query)}&num={num}")
    soup = BeautifulSoup(html, "html.parser")
    results = []
    for card in soup.select("div.g, div.MjjYud"):
        a = card.select_one("a")
        h3 = card.select_one("h3")
        snippet = card.select_one("div.VwiC3b, span.aCOpRe")
        if not (a and h3 and a.get("href", "").startswith("http")):
            continue
        results.append({"title": h3.get_text(strip=True), "link": a["href"],
                         "snippet": snippet.get_text(strip=True) if snippet else ""})
        if len(results) >= num:
            break
    return results


async def search_bing(query: str, num: int = 10) -> list:
    html = await _fetch_html(f"https://www.bing.com/search?q={quote_plus(query)}")
    soup = BeautifulSoup(html, "html.parser")
    results = []
    for card in soup.select("li.b_algo"):
        a = card.select_one("h2 a")
        snippet = card.select_one(".b_caption p")
        if not a or not a.get("href"):
            continue
        results.append({"title": a.get_text(strip=True), "link": _decode_bing_redirect(a["href"]),
                         "snippet": snippet.get_text(strip=True) if snippet else ""})
        if len(results) >= num:
            break
    return results


async def search_duckduckgo(query: str, num: int = 10) -> list:
    html = await _fetch_html(f"https://html.duckduckgo.com/html/?q={quote_plus(query)}")
    soup = BeautifulSoup(html, "html.parser")
    results = []
    for a in soup.select("a.result__a"):
        href = a.get("href")
        if not href:
            continue
        snippet_el = a.find_parent(class_="result__body")
        snippet = snippet_el.select_one(".result__snippet") if snippet_el else None
        results.append({"title": a.get_text(strip=True), "link": _decode_ddg_redirect(href),
                         "snippet": snippet.get_text(strip=True) if snippet else ""})
        if len(results) >= num:
            break
    return results


async def search_yahoo(query: str, num: int = 10) -> list:
    html = await _fetch_html(f"https://search.yahoo.com/search?p={quote_plus(query)}")
    soup = BeautifulSoup(html, "html.parser")
    results = []
    for card in soup.select("#web ol.reg > li, .dd.algo, .algo-sr"):
        a = card.select_one("h3.title a, .compTitle a")
        snippet = card.select_one(".compText p, .aAbs")
        if not a or not a.get("href"):
            continue
        results.append({"title": a.get_text(strip=True), "link": _decode_yahoo_redirect(a["href"]),
                         "snippet": snippet.get_text(strip=True) if snippet else ""})
        if len(results) >= num:
            break
    return results


ENGINES = {
    "google": search_google,
    "bing": search_bing,
    "duckduckgo": search_duckduckgo,
    "yahoo": search_yahoo,
}


async def search_engine(engine: str, query: str, num: int = 10) -> dict:
    func = ENGINES.get(engine)
    if not func:
        return {"engine": engine, "error": f"Unknown engine '{engine}'. Choose one of {list(ENGINES)}."}
    try:
        results = await func(query, num)
        return {"engine": engine, "query": query, "count": len(results), "results": results}
    except CaptchaDetectedError as exc:
        return {"engine": engine, "query": query, "error": str(exc)}
    except Exception as exc:
        return {"engine": engine, "query": query, "error": f"Fetch/parse failed: {exc}"}
