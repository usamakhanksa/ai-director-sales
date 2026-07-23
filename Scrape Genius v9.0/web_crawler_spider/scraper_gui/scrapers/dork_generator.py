"""
scrapers/dork_generator.py
============================
Search Dorks Generator - pure string templating, ported from the Node
backend's dorkGenerator.js. No network calls; given a keyword/location/
intent/platform/language it produces ready-to-use dork query strings
(and, for the selected platforms, ready-made search URLs).
"""

import re
from urllib.parse import quote_plus

from .store import load, save

COUNTRY_CODES = {
    "SA": "+966", "EG": "+20", "KW": "+965", "QA": "+974",
    "AE": "+971", "JO": "+962", "LB": "+961", "MA": "+212", "TN": "+216",
}
COUNTRY_DOMAINS = {
    "SA": ".sa", "EG": ".eg", "KW": ".kw", "QA": ".qa",
    "AE": ".ae", "JO": ".jo", "LB": ".lb", "MA": ".ma", "TN": ".tn",
}

DORK_TEMPLATES = {
    "EMAIL_HARVESTING": [
        'site:facebook.com OR site:linkedin.com "{keyword}" ("gmail.com" OR "yahoo.com" OR "email me at")',
        '"{keyword}" "{location}" ("contact us" OR "email:" OR "@gmail.com" OR "@yahoo.com")',
        'intext:"{keyword}" intext:"@" "{location}" -site:facebook.com -site:linkedin.com',
    ],
    "PHONE_HARVESTING": [
        '"{keyword}" "{location}" ("{country_code}" OR "call us" OR "whatsapp")',
        '"{keyword}" intext:"{country_code}" "{location}"',
    ],
    "PROFESSIONAL_NETWORKS": [
        'site:linkedin.com/in "{keyword}" "{location}"',
        'site:linkedin.com/company "{keyword}" "{location}"',
    ],
    "FILE_HARVESTING": [
        '"{keyword}" "{location}" filetype:pdf ("email" OR "contact")',
        '"{keyword}" filetype:xlsx OR filetype:csv "{location}"',
    ],
    "MENA_ARABIC": [
        '"{keyword}" "{location}" ("اتصل بنا" OR "البريد الإلكتروني" OR "{country_code}")',
        'site:facebook.com "{keyword}" "{location}" "واتساب"',
    ],
    "HIGH_INTENT_BUSINESS": [
        '"{keyword}" "{location}" ("hiring" OR "now open" OR "request a quote")',
        '"{keyword}" site:{domain} ("careers" OR "services" OR "contact")',
    ],
}

PLATFORM_SEARCH_URLS = {
    "google": "https://www.google.com/search?q={q}",
    "bing": "https://www.bing.com/search?q={q}",
    "yahoo": "https://search.yahoo.com/search?p={q}",
    "duckduckgo": "https://html.duckduckgo.com/html/?q={q}",
    "google_maps": "https://www.google.com/maps/search/{q}",
    "linkedin": "https://www.google.com/search?q=site:linkedin.com+{q}",
    "facebook": "https://www.google.com/search?q=site:facebook.com+{q}",
    "twitter": "https://www.google.com/search?q=site:twitter.com+{q}",
}

_ARABIC_RE = re.compile(r"[؀-ۿ]")


def generate_dorks(keyword: str, location: str = "", country: str = "SA",
                    intent: str = "EMAIL_HARVESTING", platforms: list = None, language: str = "en") -> dict:
    keyword = (keyword or "").strip()
    if not keyword:
        return {"error": "keyword is required"}

    country = (country or "SA").upper()
    templates = DORK_TEMPLATES.get(intent, DORK_TEMPLATES["EMAIL_HARVESTING"])
    context = {
        "keyword": keyword,
        "location": location or "",
        "country_code": COUNTRY_CODES.get(country, ""),
        "country": country,
        "domain": COUNTRY_DOMAINS.get(country, ".com"),
    }

    dorks = [t.format(**context) for t in templates]

    is_arabic = bool(_ARABIC_RE.search(keyword)) or language == "ar"
    if is_arabic:
        dorks.append(f'"{quote_plus(keyword)}" "{location}"')

    platform_urls = {}
    for platform in (platforms or []):
        template = PLATFORM_SEARCH_URLS.get(platform)
        if template:
            query = f"{keyword} {location}".strip()
            platform_urls[platform] = template.format(q=quote_plus(query))

    result = {
        "keyword": keyword, "location": location, "country": country,
        "intent": intent, "language": language,
        "dorks": dorks, "platform_urls": platform_urls,
    }

    history = load("dork_history", [])
    history.insert(0, result)
    save("dork_history", history[:200])

    return result


def get_dork_history(limit: int = 50) -> list:
    return load("dork_history", [])[:limit]
