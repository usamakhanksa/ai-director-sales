"""
scrapers/lead_qualifier.py
=============================
AI Lead Qualifier. The Node reference (leadQualifier.routes.js) calls an
LLM (local Ollama, else Groq free tier) to classify text as LEAD /
NOT_LEAD against a target product - there's no rule-based fallback to
port. Requiring an LLM key/local server here would violate this app's
"no paid API required" rule, so this is a transparent heuristic
classifier instead: keyword overlap between the text and the product
description, plus generic buying-intent phrases, are combined into a
0-100 confidence score. Labeled explicitly as heuristic, not an LLM
judgment - if you have a local Ollama server, wire ai_enrichment's
pattern in with a real call later.
"""

import re

BUYING_INTENT_PHRASES = [
    "looking for", "need a", "need help with", "interested in", "recommend a",
    "quote for", "price for", "hiring", "budget for", "where can i buy",
    "any suggestions for", "who provides", "looking to hire", "want to buy",
]

_WORD_RE = re.compile(r"[a-zA-Z؀-ۿ]{3,}")


def _keywords(text: str) -> set:
    return {w.lower() for w in _WORD_RE.findall(text or "")}


def qualify_lead(text: str, product: str) -> dict:
    text = (text or "").strip()
    product = (product or "").strip()
    if not text or not product:
        return {"error": "both text and product are required"}

    text_lower = text.lower()
    text_words = _keywords(text)
    product_words = _keywords(product)

    overlap = text_words & product_words
    overlap_score = min(60, len(overlap) * 15)

    intent_hits = [p for p in BUYING_INTENT_PHRASES if p in text_lower]
    intent_score = min(40, len(intent_hits) * 20)

    confidence = overlap_score + intent_score
    is_lead = confidence >= 35

    return {
        "text": text,
        "product": product,
        "is_lead": is_lead,
        "confidence": confidence,
        "matched_keywords": sorted(overlap),
        "matched_intent_phrases": intent_hits,
        "method": "heuristic keyword/intent overlap (no LLM call)",
    }
