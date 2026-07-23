"""
scrapers/connectors.py
========================
Custom API Connectors - register any third-party HTTP API (query
substitution, auth type, JSON-path result mapping) and invoke it like a
built-in scraper. Ports app/api/api-connectors/route.ts +
[id]/run/route.ts: substitutes `{query}` into the URL, applies the
chosen auth style, resolves a dot-path into the JSON response for the
result list, then maps each item's fields via another set of dot-paths.
"""

import httpx

from .store import load, save

RUN_TIMEOUT = 15
VALID_AUTH_TYPES = {"none", "query", "header", "bearer"}


def _get_path(obj, dot_path: str):
    """Dict/list dot-path getter, e.g. 'data.items' or 'items.0.email'."""
    current = obj
    for part in dot_path.split("."):
        if current is None:
            return None
        if isinstance(current, list):
            try:
                current = current[int(part)]
            except (ValueError, IndexError):
                return None
        elif isinstance(current, dict):
            current = current.get(part)
        else:
            return None
    return current


def list_connectors() -> list:
    return load("connectors", [])


def register_connector(name: str, url: str, method: str = "GET", api_key: str = "",
                        auth_type: str = "none", auth_param: str = "",
                        results_path: str = "", field_map: dict = None) -> dict:
    name, url = (name or "").strip(), (url or "").strip()
    auth_type = (auth_type or "none").lower()
    if not name or not url:
        return {"error": "name and url are required"}
    if auth_type not in VALID_AUTH_TYPES:
        return {"error": f"auth_type must be one of {sorted(VALID_AUTH_TYPES)}"}

    connectors = load("connectors", [])
    connector = {
        "id": len(connectors) + 1,
        "name": name,
        "url": url,
        "method": (method or "GET").upper(),
        "api_key": api_key or "",
        "auth_type": auth_type,
        "auth_param": auth_param or ("key" if auth_type == "query" else "X-API-Key"),
        "results_path": results_path or "",
        "field_map": field_map or {},
    }
    connectors.append(connector)
    save("connectors", connectors)
    return {"registered": True, "connector": connector}


def remove_connector(connector_id: int) -> dict:
    connectors = load("connectors", [])
    remaining = [c for c in connectors if c["id"] != connector_id]
    save("connectors", remaining)
    return {"removed": len(connectors) != len(remaining)}


async def run_connector(connector_id: int, query: str) -> dict:
    connectors = load("connectors", [])
    connector = next((c for c in connectors if c["id"] == connector_id), None)
    if not connector:
        return {"error": f"No connector with id {connector_id}"}

    url = connector["url"].replace("{query}", query or "")
    headers, params = {}, {}

    if connector["auth_type"] == "query" and connector["api_key"]:
        params[connector["auth_param"]] = connector["api_key"]
    elif connector["auth_type"] == "header" and connector["api_key"]:
        headers[connector["auth_param"]] = connector["api_key"]
    elif connector["auth_type"] == "bearer" and connector["api_key"]:
        headers["Authorization"] = f"Bearer {connector['api_key']}"

    try:
        async with httpx.AsyncClient(timeout=RUN_TIMEOUT) as client:
            resp = await client.request(connector["method"], url, headers=headers, params=params)
            resp.raise_for_status()
            data = resp.json()
    except Exception as exc:
        return {"error": f"Connector call failed: {exc}"}

    raw_items = _get_path(data, connector["results_path"]) if connector["results_path"] else data
    if isinstance(raw_items, dict):
        raw_items = [raw_items]
    if not isinstance(raw_items, list):
        raw_items = []

    field_map = connector["field_map"]
    if not field_map:
        results = raw_items
    else:
        results = [{out_key: _get_path(item, in_path) for out_key, in_path in field_map.items()} for item in raw_items]

    return {"connector": connector["name"], "query": query, "count": len(results), "results": results}
