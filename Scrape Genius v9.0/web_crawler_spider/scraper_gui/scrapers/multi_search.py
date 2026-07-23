"""
scrapers/multi_search.py
==========================
Multi-Engine Unified Search - runs a query across all requested engines
concurrently, tolerates per-engine failure, and interleaves each engine's
results round-robin (not simple concatenation) with re-numbered
positions, same as the Node app's lib/search-engines/index.ts::runMultiSearch.
"""

import asyncio

from .search_engines import ENGINES, search_engine

PER_ENGINE_TIMEOUT = 15


async def _timed(engine: str, query: str, num: int):
    try:
        return await asyncio.wait_for(search_engine(engine, query, num), timeout=PER_ENGINE_TIMEOUT)
    except asyncio.TimeoutError:
        return {"engine": engine, "query": query, "error": "Timed out after 15s"}


async def run_multi_search(query: str, engines: list = None, num: int = 10) -> dict:
    engines = [e for e in (engines or list(ENGINES)) if e in ENGINES]
    if not engines:
        return {"error": f"No valid engines given; choose from {list(ENGINES)}"}

    per_engine = await asyncio.gather(*(_timed(e, query, num) for e in engines))

    status = {r["engine"]: ("error" in r and r["error"] or "ok") for r in per_engine}
    result_lists = [r.get("results", []) for r in per_engine]

    interleaved = []
    for i in range(max((len(rl) for rl in result_lists), default=0)):
        for rl in result_lists:
            if i < len(rl):
                interleaved.append(rl[i])

    for i, item in enumerate(interleaved, start=1):
        item["position"] = i

    return {"query": query, "engines": engines, "status": status, "count": len(interleaved), "results": interleaved}
