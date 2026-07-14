import { NextRequest, NextResponse } from "next/server";
import { chromium } from "playwright";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { prisma } from "@/lib/prisma";
import { decryptSecret } from "@/lib/crypto";
import { saveScrapedRecords } from "@/lib/records";
import { extractEmails, extractPhones } from "@/lib/scrapers/extract";

export const runtime = "nodejs";

const PROVIDERS = ["justdial", "indiamart"] as const;
type Provider = "JUSTDIAL" | "INDIAMART";

const LOGIN_URLS: Record<Provider, string> = {
  JUSTDIAL: "https://www.justdial.com/",
  INDIAMART: "https://seller.indiamart.com/",
};

// Candidate selectors tried in order per field — real login-page markup
// wasn't verified against a live logged-in account, so this is a
// best-effort list rather than a confirmed working flow.
const LOGIN_FIELD_SELECTORS = "input[type='email'], input[type='tel'], input[name*='mobile' i], input[name*='phone' i], input[name*='email' i], input[name*='user' i]";
const PASSWORD_FIELD_SELECTORS = "input[type='password']";
const SUBMIT_SELECTORS = "button[type='submit'], input[type='submit'], button:has-text('Login'), button:has-text('Sign in')";

function resolveProvider(raw: string): Provider | null {
  const lower = raw.toLowerCase();
  return PROVIDERS.includes(lower as (typeof PROVIDERS)[number]) ? (lower.toUpperCase() as Provider) : null;
}

async function attemptSync(provider: Provider, loginId: string, secret: string) {
  const browser = await chromium.launch({ headless: true });
  try {
    const page = await browser.newPage({
      userAgent:
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36",
    });
    await page.goto(LOGIN_URLS[provider], { waitUntil: "domcontentloaded", timeout: 20_000 });
    await page.waitForTimeout(1500);

    const loginField = page.locator(LOGIN_FIELD_SELECTORS).first();
    if ((await loginField.count()) === 0) {
      return { status: "Login form not found on the provider's page — selectors may need updating", results: [] };
    }
    await loginField.fill(loginId);

    const passwordField = page.locator(PASSWORD_FIELD_SELECTORS).first();
    if (await passwordField.count()) {
      await passwordField.fill(secret);
    }

    const submitButton = page.locator(SUBMIT_SELECTORS).first();
    if (await submitButton.count()) {
      await submitButton.click({ timeout: 5000 }).catch(() => undefined);
      await page.waitForTimeout(3000);
    }

    const html = await page.content();
    const emails = extractEmails(html);
    const phones = extractPhones(html);

    if (!emails.length && !phones.length) {
      return {
        status: "Login submitted but no leads/contacts were found on the resulting page — this account/provider's dashboard layout may need a dedicated selector",
        results: [],
      };
    }

    const results = [
      ...emails.map((email) => ({ email })),
      ...phones.map((phone) => ({ phone })),
    ];
    return { status: `Synced via generic page scan — found ${results.length} contact(s)`, results };
  } finally {
    await browser.close();
  }
}

// Attempts to log into the user's own Justdial/IndiaMART seller dashboard
// with their saved credentials and pull leads/enquiries. Best-effort: the
// exact login form and leads-table markup for these sites wasn't verified
// against a real logged-in account, so failures are expected and reported
// via `lastStatus` rather than a 500.
export async function POST(
  req: NextRequest,
  { params }: { params: { provider: string } },
): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const provider = resolveProvider(params.provider);
    if (!provider) return fail("Unknown CRM provider — use 'justdial' or 'indiamart'", 400);

    const connection = await prisma.crmConnection.findUnique({
      where: { userId_provider: { userId: user.id, provider } },
    });
    if (!connection) return fail("No saved connection for this provider — save your login first", 404);

    let status: string;
    let results: Record<string, unknown>[] = [];
    try {
      const secret = decryptSecret(connection.secret);
      const outcome = await attemptSync(provider, connection.loginId, secret);
      status = outcome.status;
      results = outcome.results;
    } catch (err: unknown) {
      status = `Sync failed: ${err instanceof Error ? err.message : "Unknown error"}`;
    }

    await prisma.crmConnection.update({
      where: { userId_provider: { userId: user.id, provider } },
      data: { lastSyncedAt: new Date(), lastStatus: status.slice(0, 500) },
    });

    if (results.length) {
      await saveScrapedRecords(user.id, provider, `${provider} CRM sync`, results);
    }

    return ok({ status, count: results.length, results });
  });
}
