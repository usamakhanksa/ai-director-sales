"""
scrapers/cse_keys.py
======================
Google Custom Search (CSE) key management - bring-your-own key(s), same
model as the Node app's googleSearchService/keyUsageService: the app
never ships its own paid Google key, but once you register your own
`key` + `cx` (Custom Search Engine ID) here, searches actually hit the
real Google Custom Search JSON API and per-key daily usage is tracked
and enforced.
"""

import datetime

import httpx

from .store import load, save

DAILY_LIMIT_DEFAULT = 100
CSE_ENDPOINT = "https://www.googleapis.com/customsearch/v1"


def _today() -> str:
    return datetime.date.today().isoformat()


def list_keys() -> list:
    keys = load("cse_keys", [])
    today = _today()
    return [{**k, "used_today": k.get("usage", {}).get(today, 0)} for k in keys]


def add_key(key: str, cx: str, daily_limit: int = DAILY_LIMIT_DEFAULT, label: str = "") -> dict:
    key, cx = key.strip(), cx.strip()
    if not key or not cx:
        return {"error": "key and cx are both required"}

    keys = load("cse_keys", [])
    keys.append({
        "id": len(keys) + 1,
        "label": label or f"key-{len(keys) + 1}",
        "key": key,
        "cx": cx,
        "daily_limit": int(daily_limit or DAILY_LIMIT_DEFAULT),
        "usage": {},
    })
    save("cse_keys", keys)
    return {"saved": True, "count": len(keys)}


def remove_key(key_id: int) -> dict:
    keys = load("cse_keys", [])
    remaining = [k for k in keys if k["id"] != key_id]
    save("cse_keys", remaining)
    return {"removed": len(keys) != len(remaining), "count": len(remaining)}


def _pick_key():
    keys = load("cse_keys", [])
    today = _today()
    best = None
    for k in keys:
        used = k.get("usage", {}).get(today, 0)
        remaining = k["daily_limit"] - used
        if remaining > 0 and (best is None or remaining > best[1]):
            best = (k, remaining)
    return best[0] if best else None


def _record_usage(key_id: int):
    keys = load("cse_keys", [])
    today = _today()
    for k in keys:
        if k["id"] == key_id:
            k.setdefault("usage", {})
            k["usage"][today] = k["usage"].get(today, 0) + 1
    save("cse_keys", keys)


async def cse_search(query: str, num: int = 10) -> dict:
    query = (query or "").strip()
    if not query:
        return {"error": "query is required"}

    key_entry = _pick_key()
    if not key_entry:
        return {"error": "No Google CSE key with remaining daily quota is registered. Add one via /tools/cse-keys."}

    params = {"key": key_entry["key"], "cx": key_entry["cx"], "q": query, "num": min(num, 10)}
    try:
        async with httpx.AsyncClient(timeout=15) as client:
            resp = await client.get(CSE_ENDPOINT, params=params)
            resp.raise_for_status()
            data = resp.json()
    except httpx.HTTPStatusError as exc:
        return {"error": f"Google CSE API error: {exc.response.status_code} {exc.response.text[:300]}"}
    except Exception as exc:
        return {"error": f"Google CSE request failed: {exc}"}

    _record_usage(key_entry["id"])

    items = data.get("items", [])
    return {
        "query": query,
        "key_used": key_entry["label"],
        "count": len(items),
        "results": [{"title": i.get("title"), "link": i.get("link"), "snippet": i.get("snippet")} for i in items],
    }
