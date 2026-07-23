"""
scrapers/email_verifier.py
============================
Free, no-API-key email verifier:
  1. Syntax check (RFC-ish regex, same as ScrapeGenius's Zod-based check)
  2. MX-record DNS lookup (does the domain actually accept mail?)
  3. Disposable / free-provider domain classification (built-in lists)
  4. Typo-correction suggestion against common providers (difflib)
"""

import difflib
import re

import dns.resolver

_EMAIL_RE = re.compile(r"^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$")

FREE_PROVIDERS = {
    "gmail.com", "yahoo.com", "hotmail.com", "outlook.com", "icloud.com",
    "aol.com", "live.com", "msn.com", "protonmail.com", "gmx.com", "mail.com",
}

# A small, commonly-abused set - real deployments should pull a maintained list.
DISPOSABLE_DOMAINS = {
    "mailinator.com", "10minutemail.com", "guerrillamail.com", "tempmail.com",
    "yopmail.com", "trashmail.com", "throwawaymail.com", "getnada.com",
    "temp-mail.org", "fakeinbox.com",
}

_COMMON_DOMAINS = list(FREE_PROVIDERS) + [
    "company.com", "business.com",
]


def _suggest_domain(domain: str):
    matches = difflib.get_close_matches(domain, _COMMON_DOMAINS, n=1, cutoff=0.75)
    return matches[0] if matches and matches[0] != domain else None


def verify_email(email: str) -> dict:
    email = (email or "").strip()
    result = {"email": email, "valid_syntax": False, "has_mx": False,
              "is_free_provider": False, "is_disposable": False, "suggestion": None}

    if not _EMAIL_RE.match(email):
        result["reason"] = "Invalid email syntax"
        return result

    result["valid_syntax"] = True
    domain = email.rsplit("@", 1)[-1].lower()
    result["is_free_provider"] = domain in FREE_PROVIDERS
    result["is_disposable"] = domain in DISPOSABLE_DOMAINS
    result["suggestion"] = _suggest_domain(domain)

    try:
        answers = dns.resolver.resolve(domain, "MX", lifetime=6)
        result["has_mx"] = len(answers) > 0
        result["mx_records"] = sorted(str(a.exchange).rstrip(".") for a in answers)
    except dns.resolver.NXDOMAIN:
        result["reason"] = "Domain does not exist"
    except dns.resolver.NoAnswer:
        result["reason"] = "Domain has no MX records (cannot receive mail)"
    except Exception as exc:
        result["reason"] = f"MX lookup failed: {exc}"

    result["deliverable"] = result["valid_syntax"] and result["has_mx"] and not result["is_disposable"]
    return result
