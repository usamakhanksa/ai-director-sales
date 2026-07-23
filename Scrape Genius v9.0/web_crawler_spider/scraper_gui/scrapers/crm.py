"""
scrapers/crm.py
=================
CRM Connections - store your own JustDial/IndiaMART seller-dashboard
login, encrypted at rest, then a Playwright-driven best-effort login +
data pull. Ports app/api/crm/[provider]/route.ts (credential storage)
and sync/route.ts (Playwright login flow).

Encryption note: the Node version does AES-256-GCM with a key derived
from JWT_SECRET. This app has no JWT secret/session system, so credentials
are encrypted with `cryptography.fernet.Fernet` instead (also
authenticated AES under the hood) using a key generated once and stored
locally in data/crm_secret.key - same goal (never store the plaintext
secret), different primitive.

Caveat carried over verbatim from the Node source: the login-page
selectors below are generic/best-effort, not confirmed against a live
JustDial/IndiaMART seller login page today - directory sites change
their login markup often.
"""

import os

from cryptography.fernet import Fernet
from playwright.async_api import async_playwright

from .base import BaseScraper, JobStatus
from .store import DATA_DIR, load, save
from .utils import extract_emails, extract_phones

SECRET_PATH = os.path.join(DATA_DIR, "crm_secret.key")

PROVIDER_URLS = {
    "justdial": "https://www.justdial.com/",
    "indiamart": "https://seller.indiamart.com/",
}

LOGIN_SELECTORS = {
    "email_or_mobile": "input[type=email], input[name*=mobile], input[name*=email], input[type=tel]",
    "password": "input[type=password]",
    "submit": "button[type=submit], button:has-text('Login'), button:has-text('Sign in')",
}

RESULT_COLUMNS = ["provider", "emails", "phones", "note"]


def _get_fernet() -> Fernet:
    if not os.path.exists(SECRET_PATH):
        with open(SECRET_PATH, "wb") as f:
            f.write(Fernet.generate_key())
    with open(SECRET_PATH, "rb") as f:
        return Fernet(f.read())


def save_credentials(provider: str, login_id: str, secret: str) -> dict:
    provider = (provider or "").strip().lower()
    if provider not in PROVIDER_URLS:
        return {"error": f"provider must be one of {list(PROVIDER_URLS)}"}
    if not login_id or not secret:
        return {"error": "login_id and secret are both required"}

    fernet = _get_fernet()
    creds = load("crm_credentials", {})
    creds[provider] = {"login_id": login_id, "secret_encrypted": fernet.encrypt(secret.encode()).decode()}
    save("crm_credentials", creds)
    return {"saved": True, "provider": provider}


def list_crm_connections() -> list:
    creds = load("crm_credentials", {})
    return [{"provider": p, "login_id": v["login_id"]} for p, v in creds.items()]


class CRMSyncScraper(BaseScraper):
    source_name = "crm_sync"

    def __init__(self, provider: str):
        super().__init__(provider=provider)
        self.provider = (provider or "").strip().lower()

    async def run(self):
        creds = load("crm_credentials", {})
        entry = creds.get(self.provider)
        if not entry:
            self.error_message = f"No saved credentials for '{self.provider}'. Save them first."
            self.status = JobStatus.FAILED
            return

        fernet = _get_fernet()
        secret = fernet.decrypt(entry["secret_encrypted"].encode()).decode()
        login_url = PROVIDER_URLS[self.provider]

        async with async_playwright() as pw:
            browser = await pw.chromium.launch(headless=True)
            try:
                page = await browser.new_page()
                await page.goto(login_url, wait_until="domcontentloaded", timeout=30_000)
                self.progress = 20

                id_field = page.locator(LOGIN_SELECTORS["email_or_mobile"]).first
                pw_field = page.locator(LOGIN_SELECTORS["password"]).first
                submit_btn = page.locator(LOGIN_SELECTORS["submit"]).first

                if await id_field.count() == 0:
                    self.error_message = (
                        f"Could not find a login field on {login_url} with the generic selectors - "
                        "the site's login markup has likely changed; selectors need updating."
                    )
                    self.status = JobStatus.FAILED
                    return

                await id_field.fill(entry["login_id"])
                self.progress = 40
                if await pw_field.count() > 0:
                    await pw_field.fill(secret)
                self.progress = 60
                if await submit_btn.count() > 0:
                    await submit_btn.click()
                    await page.wait_for_timeout(4000)
                self.progress = 80

                text = await page.locator("body").inner_text()
                self.results.append({
                    "provider": self.provider,
                    "emails": extract_emails(text),
                    "phones": extract_phones(text),
                    "note": "Best-effort generic login + page-text extraction; verify manually if login failed.",
                })
                self.progress = 100
            finally:
                await browser.close()
