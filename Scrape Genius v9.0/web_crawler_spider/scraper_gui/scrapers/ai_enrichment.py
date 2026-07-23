"""
scrapers/ai_enrichment.py
============================
AI Enrichment - heuristic (rule-based) lead scoring, ported from
backend/src/services/aiEnhancementService.js::calculateLeadScore. This
is deliberately NOT an LLM call - it's the same free, deterministic
rule set the Node app uses, labeled honestly as heuristic rather than
"AI" so nobody mistakes it for a model judgment.
"""

import re

FREE_EMAIL_RE = re.compile(r"@(gmail|yahoo|hotmail|outlook)\.", re.IGNORECASE)


def score_lead(email: str = "", website: str = "", social_links: list = None,
               phone: str = "", review_severity_score: float = 0) -> dict:
    social_links = social_links or []
    score = 0
    reasons = []

    if email and FREE_EMAIL_RE.search(email):
        score += 25
        reasons.append("+25: uses a free email provider (gmail/yahoo/hotmail/outlook)")
    if not website:
        score += 20
        reasons.append("+20: no website on file")
    if social_links:
        score += 10
        reasons.append("+10: has social media presence")
    if phone and len(re.sub(r"\D", "", phone)) > 10:
        score += 10
        reasons.append("+10: has a full-length phone number")
    if review_severity_score and review_severity_score > 7:
        score += 15
        reasons.append("+15: review/pain-point severity score above 7")

    score = min(score, 100)
    if score >= 70:
        bucket = "Hot"
    elif score >= 40:
        bucket = "Warm"
    else:
        bucket = "Cold"

    return {"score": score, "bucket": bucket, "reasons": reasons, "method": "heuristic (rule-based, no LLM)"}
