"""
run.py
=======
Windows-safe launcher for the Scraper Studio app.

`uvicorn main:app --reload` on Windows creates its event loop BEFORE it
imports main.py (the import happens inside uvicorn's own asyncio.run() call),
so setting the event loop policy inside main.py is too late - uvicorn has
already committed to the default SelectorEventLoop, which cannot spawn
subprocesses and breaks Playwright (NotImplementedError from
asyncio.create_subprocess_exec).

Running this script instead sets WindowsProactorEventLoopPolicy BEFORE
uvicorn.run() is called, so the loop it creates internally supports
subprocesses from the start.

IMPORTANT: `reload=True` cannot be used here. When reload is on, uvicorn
runs the app in a child "server process" with `use_subprocess=True`, and
uvicorn's own loop setup (uvicorn/loops/asyncio.py::asyncio_setup)
unconditionally does:

    if sys.platform == "win32" and use_subprocess:
        asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())

That overwrites the Proactor policy right before the event loop is
created, no matter what policy this script or main.py set first - so
Playwright's subprocess spawning breaks again. Reload is therefore
disabled; restart the process manually after changing code.

Usage:
    python run.py
"""

import asyncio
import sys

if sys.platform == "win32":
    asyncio.set_event_loop_policy(asyncio.WindowsProactorEventLoopPolicy())

import uvicorn

if __name__ == "__main__":
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=False)
