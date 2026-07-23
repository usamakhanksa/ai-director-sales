"""
scrapers/utils.py
==================
Shared helpers used by every scraper: stealth browser context creation,
bilingual (Arabic/English) email & phone extraction, CSV export, and
short job-id generation.
"""

import csv
import random
import re
import uuid
from typing import List, Optional

STEALTH_USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
    "(KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 "
    "(KHTML, like Gecko) Version/17.0 Safari/605.1.15",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) "
    "Chrome/124.0.0.0 Safari/537.36",
]

# Bilingual-safe patterns: emails are ASCII by spec; phone numbers may contain
# Arabic-Indic digits (٠-٩) alongside Western ones and common separators.
EMAIL_RE = re.compile(r"[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+")
PHONE_RE = re.compile(r"(\+?[\d٠-٩][\d٠-٩\s\-()]{6,}[\d٠-٩])")

STEALTH_INIT_SCRIPT = """
Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
window.chrome = { runtime: {} };
Object.defineProperty(navigator, 'languages', { get: () => ['en-US', 'en'] });
Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3, 4, 5] });
"""


async def create_stealth_context(browser):
    """Create a new browser context + page with basic anti-detection tweaks:
    randomized user-agent/viewport and a webdriver-hiding init script."""
    context = await browser.new_context(
        user_agent=random.choice(STEALTH_USER_AGENTS),
        viewport={"width": random.randint(1280, 1920), "height": random.randint(800, 1080)},
        locale="en-US",
    )
    await context.add_init_script(STEALTH_INIT_SCRIPT)
    page = await context.new_page()
    return context, page


def extract_emails(text: str) -> List[str]:
    if not text:
        return []
    return list(dict.fromkeys(EMAIL_RE.findall(text)))


def extract_phones(text: str) -> List[str]:
    if not text:
        return []
    return [p.strip() for p in dict.fromkeys(PHONE_RE.findall(text))]


def guess_email(first_name: str, last_name: str, domain: str) -> Optional[str]:
    """Best-effort common email-pattern guess when no public email is found."""
    if not (first_name and last_name and domain):
        return None
    first_name = re.sub(r"[^a-zA-Z]", "", first_name).lower()
    last_name = re.sub(r"[^a-zA-Z]", "", last_name).lower()
    if not (first_name and last_name):
        return None
    return f"{first_name}.{last_name}@{domain}"


def save_results_to_csv(results: List[dict], filename: str, columns: Optional[List[str]] = None) -> str:
    if not results:
        columns = columns or []
    else:
        columns = columns or list(results[0].keys())
    with open(filename, "w", newline="", encoding="utf-8-sig") as f:
        writer = csv.DictWriter(f, fieldnames=columns, extrasaction="ignore")
        writer.writeheader()
        for row in results:
            writer.writerow({c: row.get(c, "N/A") for c in columns})
    return filename


def generate_job_id() -> str:
    return uuid.uuid4().hex[:12]


async def random_delay(min_ms: int = 400, max_ms: int = 1400):
    """Human-like pause between actions to reduce anti-bot fingerprinting."""
    import asyncio
    await asyncio.sleep(random.randint(min_ms, max_ms) / 1000)
