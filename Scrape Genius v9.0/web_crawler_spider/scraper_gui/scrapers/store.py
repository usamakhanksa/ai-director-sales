"""
scrapers/store.py
===================
Tiny JSON-file persistence layer. This app has no database (unlike the
Node/Next.js backend it's porting from, which uses Prisma/Knex), so
CSE keys, webhooks, custom connectors, dork history, and public-API
clients are persisted as JSON files under scraper_gui/data/ instead -
real, disk-backed state, just not a relational DB.
"""

import json
import os
import threading

DATA_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "data")
os.makedirs(DATA_DIR, exist_ok=True)

_lock = threading.Lock()


def _path(name: str) -> str:
    return os.path.join(DATA_DIR, f"{name}.json")


def load(name: str, default):
    path = _path(name)
    if not os.path.exists(path):
        return default
    with _lock:
        try:
            with open(path, "r", encoding="utf-8") as f:
                return json.load(f)
        except (json.JSONDecodeError, OSError):
            return default


def save(name: str, data) -> None:
    path = _path(name)
    with _lock:
        with open(path, "w", encoding="utf-8") as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
