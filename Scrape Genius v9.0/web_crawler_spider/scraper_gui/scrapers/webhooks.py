"""
scrapers/webhooks.py
======================
Webhooks - register a URL + event types and actually receive outbound
POSTs on job lifecycle events. The Node app (app/api/webhooks/register/
route.ts) only implements registration - grep across the whole repo
found no dispatch/outbound-POST code anywhere - so this Python port adds
the missing half: dispatch() is called from main.py's job runner at
JOB_STARTED / JOB_COMPLETED / JOB_FAILED, and posts the payload to every
active subscription matching that event.
"""

import httpx

from .store import load, save

VALID_EVENTS = {"JOB_STARTED", "JOB_COMPLETED", "JOB_FAILED", "EXPORT_READY", "SCRAPE_DATA_AVAILABLE"}
DISPATCH_TIMEOUT = 8


def list_webhooks() -> list:
    return load("webhooks", [])


def register_webhook(url: str, events: list) -> dict:
    url = (url or "").strip()
    events = [e for e in (events or []) if e in VALID_EVENTS]
    if not url:
        return {"error": "url is required"}
    if not events:
        return {"error": f"events must be a non-empty subset of {sorted(VALID_EVENTS)}"}

    hooks = load("webhooks", [])
    hook = {"id": len(hooks) + 1, "url": url, "events": events, "is_active": True}
    hooks.append(hook)
    save("webhooks", hooks)
    return {"registered": True, "webhook": hook}


def deactivate_webhook(webhook_id: int) -> dict:
    hooks = load("webhooks", [])
    found = False
    for h in hooks:
        if h["id"] == webhook_id:
            h["is_active"] = False
            found = True
    save("webhooks", hooks)
    return {"deactivated": found}


async def dispatch(event: str, payload: dict) -> None:
    """Fire-and-forget best-effort POST to every active subscriber of `event`."""
    hooks = [h for h in load("webhooks", []) if h.get("is_active") and event in h.get("events", [])]
    if not hooks:
        return
    body = {"event": event, **payload}
    async with httpx.AsyncClient(timeout=DISPATCH_TIMEOUT) as client:
        for hook in hooks:
            try:
                await client.post(hook["url"], json=body)
            except Exception as exc:
                print(f"[webhook] delivery to {hook['url']} for {event} failed: {exc}")
