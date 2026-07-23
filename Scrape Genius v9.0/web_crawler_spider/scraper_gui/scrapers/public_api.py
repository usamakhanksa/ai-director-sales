"""
scrapers/public_api.py
========================
Public Scrape API support - API-key issuance/lookup and per-key daily
rate limiting, ported from app/api/v1/scrape/route.ts (key lookup +
DB-row-count rate check) and backend/src/middleware/rateLimiter.js
(simple counter, no Redis/token-bucket). The actual "forward to a
scraper module" step lives in main.py's /api/v1/scrape route, since it
needs the already-imported scraper classes and job runner.
"""

import datetime
import secrets

from .store import load, save

DEFAULT_DAILY_LIMIT = 300


def _today() -> str:
    return datetime.date.today().isoformat()


def list_api_keys() -> list:
    keys = load("api_clients", [])
    today = _today()
    return [{**k, "used_today": k.get("usage", {}).get(today, 0)} for k in keys]


def issue_api_key(label: str = "", daily_limit: int = DEFAULT_DAILY_LIMIT) -> dict:
    keys = load("api_clients", [])
    new_key = secrets.token_urlsafe(24)
    entry = {
        "id": len(keys) + 1,
        "label": label or f"client-{len(keys) + 1}",
        "api_key": new_key,
        "daily_limit": int(daily_limit or DEFAULT_DAILY_LIMIT),
        "is_active": True,
        "usage": {},
    }
    keys.append(entry)
    save("api_clients", keys)
    return entry


def revoke_api_key(key_id: int) -> dict:
    keys = load("api_clients", [])
    found = False
    for k in keys:
        if k["id"] == key_id:
            k["is_active"] = False
            found = True
    save("api_clients", keys)
    return {"revoked": found}


def check_and_consume(api_key: str):
    """Returns (ok: bool, message: str, key_entry: dict|None)."""
    if not api_key:
        return False, "Missing x-api-key header or api_key query param", None

    keys = load("api_clients", [])
    entry = next((k for k in keys if k["api_key"] == api_key), None)
    if not entry or not entry.get("is_active"):
        return False, "Invalid or revoked API key", None

    today = _today()
    used = entry.get("usage", {}).get(today, 0)
    if used >= entry["daily_limit"]:
        return False, f"Daily rate limit of {entry['daily_limit']} requests exceeded", entry

    entry.setdefault("usage", {})
    entry["usage"][today] = used + 1
    save("api_clients", keys)
    return True, "ok", entry
