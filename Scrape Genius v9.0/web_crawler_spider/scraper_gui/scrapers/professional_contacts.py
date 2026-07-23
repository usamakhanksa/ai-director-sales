"""
scrapers/professional_contacts.py
====================================
Professional Contact Finder - real Hunter.io domain-search integration,
bring-your-own API key (Hunter.io has a free tier). Ports the working
branch of app/api/professional-contacts/route.ts; the Node file's
"Proxycurl" branch returns literal sample/demo data, so that fallback is
NOT ported here - only the real Hunter.io call.
"""

import httpx

HUNTER_ENDPOINT = "https://api.hunter.io/v2/domain-search"


async def find_professional_contacts(domain: str, api_key: str, query: str = "") -> dict:
    domain = (domain or "").strip()
    api_key = (api_key or "").strip()
    if not domain:
        return {"error": "domain is required"}
    if not api_key:
        return {"error": "A Hunter.io API key is required (free tier available at hunter.io) - this app does not ship its own."}

    params = {"domain": domain, "api_key": api_key}
    if query:
        params["query"] = query

    try:
        async with httpx.AsyncClient(timeout=15) as client:
            resp = await client.get(HUNTER_ENDPOINT, params=params)
            resp.raise_for_status()
            data = resp.json()
    except httpx.HTTPStatusError as exc:
        return {"error": f"Hunter.io API error: {exc.response.status_code} {exc.response.text[:300]}"}
    except Exception as exc:
        return {"error": f"Hunter.io request failed: {exc}"}

    emails = data.get("data", {}).get("emails", [])
    contacts = [
        {
            "name": f"{e.get('first_name', '')} {e.get('last_name', '')}".strip() or "N/A",
            "email": e.get("value"),
            "position": e.get("position") or "N/A",
            "confidence": e.get("confidence"),
            "source": "Hunter.io",
        }
        for e in emails
    ]
    return {"domain": domain, "count": len(contacts), "contacts": contacts}
