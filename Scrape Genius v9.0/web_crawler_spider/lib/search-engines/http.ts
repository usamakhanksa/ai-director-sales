import { proxyManager } from "./proxy-manager";
import { buildBrowserHeaders, nextUserAgent } from "./user-agents";

export class CaptchaDetectedError extends Error {
  constructor(engine: string) {
    super(`${engine} returned a CAPTCHA/consent challenge page instead of results`);
  }
}

const CAPTCHA_MARKERS = [
  "recaptcha",
  "captcha-form",
  "unusual traffic",
  "/sorry/index",
  "consent.google.com",
  "geo.captcha-delivery.com",
  "verify you are human",
  "px-captcha",
];

function looksLikeCaptcha(html: string): boolean {
  const lower = html.slice(0, 20_000).toLowerCase();
  return CAPTCHA_MARKERS.some((marker) => lower.includes(marker));
}

function sleep(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

interface FetchHtmlOptions {
  engine: string;
  referer?: string;
  timeoutMs?: number;
  maxRetries?: number;
}

/**
 * Fetches a scraper-target URL with UA rotation, optional proxy rotation
 * (PROXY_LIST env), realistic browser headers, randomized inter-request
 * delay, exponential backoff on 429/503, and CAPTCHA detection.
 */
export async function fetchHtml(url: string, opts: FetchHtmlOptions): Promise<string> {
  const { engine, referer, timeoutMs = 10_000, maxRetries = 2 } = opts;

  // Randomized delay before each request to avoid a fixed request cadence.
  await sleep(150 + Math.random() * 350);

  let lastError: Error | null = null;

  for (let attempt = 0; attempt <= maxRetries; attempt += 1) {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), timeoutMs);
    const dispatcher = proxyManager.next();

    try {
      const res = await fetch(url, {
        headers: buildBrowserHeaders(nextUserAgent(), referer),
        signal: controller.signal,
        // @ts-expect-error undici-specific option, valid at runtime on Next's fetch (undici) but not in the DOM lib fetch types.
        dispatcher,
      });

      if (res.status === 429 || res.status === 503) {
        clearTimeout(timeout);
        lastError = new Error(`${engine} rate-limited the request (HTTP ${res.status})`);
        await sleep(2 ** attempt * 1000 + Math.random() * 500);
        continue;
      }

      if (!res.ok) {
        clearTimeout(timeout);
        throw new Error(`${engine} returned an unexpected status (${res.status})`);
      }

      const html = await res.text();
      clearTimeout(timeout);

      if (looksLikeCaptcha(html)) {
        throw new CaptchaDetectedError(engine);
      }

      return html;
    } catch (err) {
      clearTimeout(timeout);
      if (err instanceof CaptchaDetectedError) throw err;
      lastError = err instanceof Error ? err : new Error(String(err));
      if (attempt < maxRetries) {
        await sleep(2 ** attempt * 500 + Math.random() * 300);
      }
    }
  }

  throw lastError ?? new Error(`${engine} request failed`);
}
