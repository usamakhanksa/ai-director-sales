"""
scrapers/whois_lookup.py
=========================
Raw TCP WHOIS client (port 43) - no third-party API, no key required.
Always starts at IANA's root WHOIS server, follows its "refer:" line to
the domain's authoritative registry/registrar WHOIS server, and re-queries
there for the full record - the same chase pattern real `whois` CLIs use.
"""

import asyncio
import re
from typing import Optional

IANA_WHOIS = "whois.iana.org"
WHOIS_PORT = 43
SOCKET_TIMEOUT = 8

_REFER_RE = re.compile(r"refer:\s*(\S+)", re.IGNORECASE)
_FIELD_PATTERNS = {
    "registrar": re.compile(r"^\s*Registrar:\s*(.+)$", re.IGNORECASE | re.MULTILINE),
    "creation_date": re.compile(r"^\s*Creation Date:\s*(.+)$", re.IGNORECASE | re.MULTILINE),
    "expiry_date": re.compile(r"^\s*Registr(?:y|ar) Expiry Date:\s*(.+)$", re.IGNORECASE | re.MULTILINE),
    "updated_date": re.compile(r"^\s*Updated Date:\s*(.+)$", re.IGNORECASE | re.MULTILINE),
    "status": re.compile(r"^\s*Domain Status:\s*(.+)$", re.IGNORECASE | re.MULTILINE),
    "name_servers": re.compile(r"^\s*Name Server:\s*(.+)$", re.IGNORECASE | re.MULTILINE),
}


async def _query(server: str, domain: str) -> str:
    reader, writer = await asyncio.wait_for(asyncio.open_connection(server, WHOIS_PORT), timeout=SOCKET_TIMEOUT)
    try:
        writer.write((domain + "\r\n").encode())
        await writer.drain()
        chunks = []
        while True:
            chunk = await asyncio.wait_for(reader.read(4096), timeout=SOCKET_TIMEOUT)
            if not chunk:
                break
            chunks.append(chunk)
        return b"".join(chunks).decode("utf-8", errors="replace")
    finally:
        writer.close()


async def whois_lookup(domain: str) -> dict:
    domain = domain.strip().lower().lstrip("http://").lstrip("https://").split("/")[0]
    if not re.match(r"^[a-z0-9.-]+\.[a-z]{2,}$", domain):
        return {"domain": domain, "error": "Invalid domain format"}

    try:
        iana_raw = await _query(IANA_WHOIS, domain)
    except Exception as exc:
        return {"domain": domain, "error": f"Could not reach {IANA_WHOIS}: {exc}"}

    refer_match = _REFER_RE.search(iana_raw)
    raw = iana_raw
    server_used = IANA_WHOIS
    if refer_match:
        registry_server = refer_match.group(1).strip()
        try:
            raw = await _query(registry_server, domain)
            server_used = registry_server
        except Exception:
            pass  # fall back to the IANA response we already have

    fields = {}
    for key, pattern in _FIELD_PATTERNS.items():
        matches = pattern.findall(raw)
        if not matches:
            continue
        fields[key] = matches if key == "name_servers" else matches[0].strip()

    return {
        "domain": domain,
        "whois_server": server_used,
        "registrar": fields.get("registrar", "N/A"),
        "creation_date": fields.get("creation_date", "N/A"),
        "expiry_date": fields.get("expiry_date", "N/A"),
        "updated_date": fields.get("updated_date", "N/A"),
        "status": fields.get("status", "N/A"),
        "name_servers": fields.get("name_servers", []),
        "raw": raw,
    }
