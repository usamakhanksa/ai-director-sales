"""
scrapers/base.py
=================
Abstract base class every concrete scraper (Google Maps, LinkedIn, ...)
inherits from. Provides a uniform status/progress/results contract that
main.py's job manager and the frontend polling loop rely on.
"""

import time
import traceback
from abc import ABC, abstractmethod
from enum import Enum
from typing import List, Optional

from .utils import generate_job_id


class JobStatus(str, Enum):
    QUEUED = "queued"
    RUNNING = "running"
    COMPLETED = "completed"
    FAILED = "failed"
    CANCELLED = "cancelled"


class BaseScraper(ABC):
    """Common contract: job_id, status, progress (0-100), results, error_message."""

    #: subclasses override with a short machine name, e.g. "google_maps"
    source_name: str = "base"

    def __init__(self, **params):
        self.job_id: str = generate_job_id()
        self.params: dict = params
        self.status: JobStatus = JobStatus.QUEUED
        self.progress: int = 0
        self.results: List[dict] = []
        self.error_message: Optional[str] = None
        self.start_time: float = time.time()
        self.end_time: Optional[float] = None
        self.cancelled: bool = False

    @abstractmethod
    async def run(self):
        """Perform the scrape. Must update self.progress/self.results as it goes
        and set self.status to COMPLETED (or raise, which the wrapper turns into FAILED)."""
        raise NotImplementedError

    async def run_safely(self):
        """Wrapper invoked by the background task runner: catches all exceptions
        so a scraper bug never crashes the web server, and always stamps end_time."""
        self.status = JobStatus.RUNNING
        try:
            await self.run()
            if self.status not in (JobStatus.CANCELLED, JobStatus.FAILED):
                self.status = JobStatus.COMPLETED
                self.progress = 100
        except Exception as exc:
            self.status = JobStatus.FAILED
            self.error_message = f"{exc}"
            print(f"[job {self.job_id}] FAILED: {exc}")
            traceback.print_exc()
        finally:
            self.end_time = time.time()

    def cancel(self):
        """Cooperative cancel flag; scrapers should check self.cancelled in their loops."""
        self.cancelled = True
        self.status = JobStatus.CANCELLED

    def get_status(self) -> dict:
        elapsed = (self.end_time or time.time()) - self.start_time
        return {
            "job_id": self.job_id,
            "source": self.source_name,
            "status": self.status.value,
            "progress": self.progress,
            "result_count": len(self.results),
            "error": self.error_message,
            "elapsed_seconds": round(elapsed, 2),
            "params": self.params,
        }

    def get_results(self) -> List[dict]:
        return self.results
