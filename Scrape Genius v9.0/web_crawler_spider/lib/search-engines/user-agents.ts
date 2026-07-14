/**
 * Small pool of realistic, current desktop-browser UA strings. Rotated
 * per-request for scraper fallback calls to avoid a single fixed
 * fingerprint. Deliberately static (no `user-agents` npm dependency) —
 * search engines fingerprint on more than the UA string, and a short vetted
 * list of real UAs is safer than a generator that can emit stale/broken ones.
 */
const USER_AGENTS = [
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0",
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15",
  "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0",
];

let cursor = 0;

export function nextUserAgent(): string {
  const ua = USER_AGENTS[cursor % USER_AGENTS.length];
  cursor += 1;
  return ua;
}

/** Realistic header set to accompany a rotated UA (Accept-Language, DNT, sec-fetch hints). */
export function buildBrowserHeaders(userAgent: string, referer?: string): Record<string, string> {
  return {
    "User-Agent": userAgent,
    Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
    "Accept-Language": "en-US,en;q=0.9",
    DNT: "1",
    "Upgrade-Insecure-Requests": "1",
    "Sec-Fetch-Dest": "document",
    "Sec-Fetch-Mode": "navigate",
    "Sec-Fetch-Site": referer ? "same-origin" : "none",
    ...(referer ? { Referer: referer } : {}),
  };
}
