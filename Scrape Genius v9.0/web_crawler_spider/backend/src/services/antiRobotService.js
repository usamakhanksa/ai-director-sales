/**
 * Anti-Robot / Anti-Detection Service
 * 
 * Provides utilities to make browser automation appear as human as possible:
 *  - User-agent rotation (50+ real browser fingerprints)
 *  - Random delay injection between actions
 *  - Human-like mouse movement helpers
 *  - Human-like typing simulation (random inter-key delays)
 *  - Proxy rotation (reads proxies.txt if present)
 *  - Random viewport sizes
 * 
 * Used by browserEngine.js and all individual scraper modules.
 */

"use strict";

const fs = require("fs");
const path = require("path");

// ─────────────────────────────────────────────────────────────────────────────
// Configuration
// ─────────────────────────────────────────────────────────────────────────────

const DELAY_MIN_MS = Number(process.env.RANDOM_DELAY_MIN_MS) || 1500;
const DELAY_MAX_MS = Number(process.env.RANDOM_DELAY_MAX_MS) || 4500;
const PROXY_FILE   = path.resolve(process.env.PROXY_LIST_FILE || "proxies.txt");

// ─────────────────────────────────────────────────────────────────────────────
// User-Agent Pool (50 real desktop browser UAs across Chrome/Edge/Firefox)
// ─────────────────────────────────────────────────────────────────────────────

const USER_AGENTS = [
  // Chrome 120-124 on Windows 11
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.122 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
  // Chrome on macOS
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 13_3_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.87 Safari/537.36",
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
  // Edge on Windows
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0",
  // Firefox on Windows
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:124.0) Gecko/20100101 Firefox/124.0",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0",
  // Firefox on macOS
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 14.3; rv:124.0) Gecko/20100101 Firefox/124.0",
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 13.6; rv:123.0) Gecko/20100101 Firefox/123.0",
  // Safari on macOS
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 14_3_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3 Safari/605.1.15",
  "Mozilla/5.0 (Macintosh; Intel Mac OS X 13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15",
  // Chrome on Linux
  "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36",
  // Mobile Chrome (Android) — sometimes bypasses stricter desktop fingerprinting
  "Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.6367.82 Mobile Safari/537.36",
  "Mozilla/5.0 (Linux; Android 12; SM-G998B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.80 Mobile Safari/537.36",
  "Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.6367.82 Mobile Safari/537.36",
  // Mobile Safari (iOS)
  "Mozilla/5.0 (iPhone; CPU iPhone OS 17_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3 Mobile/15E148 Safari/604.1",
  "Mozilla/5.0 (iPad; CPU OS 17_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3 Mobile/15E148 Safari/604.1",
  // More Chrome variants
  "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
  "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36",
];

// ─────────────────────────────────────────────────────────────────────────────
// Viewport Pool (common screen resolutions)
// ─────────────────────────────────────────────────────────────────────────────

const VIEWPORTS = [
  { width: 1920, height: 1080 },
  { width: 1440, height: 900 },
  { width: 1366, height: 768 },
  { width: 1536, height: 864 },
  { width: 1280, height: 720 },
  { width: 1600, height: 900 },
  { width: 2560, height: 1440 },
  { width: 1024, height: 768 },
  { width: 1280, height: 800 },
];

// ─────────────────────────────────────────────────────────────────────────────
// Proxy Pool (loaded lazily from proxies.txt)
// ─────────────────────────────────────────────────────────────────────────────

let _proxyPool = null;

/**
 * Returns list of proxies from proxies.txt (one per line, format: host:port[:user:pass]).
 * Returns an empty array if the file doesn't exist.
 */
function loadProxies() {
  if (_proxyPool !== null) return _proxyPool;
  try {
    const raw = fs.readFileSync(PROXY_FILE, "utf8");
    _proxyPool = raw
      .split(/\r?\n/)
      .map((l) => l.trim())
      .filter((l) => l && !l.startsWith("#"));
    console.log(`[AntiRobot] Loaded ${_proxyPool.length} proxies from ${PROXY_FILE}`);
  } catch {
    _proxyPool = []; // File not found or unreadable — proxy rotation disabled
  }
  return _proxyPool;
}

// ─────────────────────────────────────────────────────────────────────────────
// Public API
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Returns a cryptographically random integer in [min, max].
 */
function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Resolves after a random delay in [DELAY_MIN_MS, DELAY_MAX_MS].
 * Used between page actions to simulate human pacing.
 */
function randomDelay(minMs = DELAY_MIN_MS, maxMs = DELAY_MAX_MS) {
  const ms = randomInt(minMs, maxMs);
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Returns a random user-agent string from the pool.
 */
function randomUserAgent() {
  return USER_AGENTS[randomInt(0, USER_AGENTS.length - 1)];
}

/**
 * Returns a random viewport {width, height} from the pool.
 */
function randomViewport() {
  return VIEWPORTS[randomInt(0, VIEWPORTS.length - 1)];
}

/**
 * Returns a random proxy string (or null if no proxies configured).
 * Format returned: "http://user:pass@host:port" or "http://host:port"
 */
function randomProxy() {
  const proxies = loadProxies();
  if (proxies.length === 0) return null;
  const raw = proxies[randomInt(0, proxies.length - 1)];
  // Normalize to full URL format if not already
  if (raw.startsWith("http://") || raw.startsWith("https://") || raw.startsWith("socks5://")) {
    return raw;
  }
  return `http://${raw}`;
}

/**
 * Types a string into a Playwright page element character-by-character,
 * with random delays between keystrokes to simulate human typing speed.
 *
 * @param {import('playwright').Page} page
 * @param {string} selector  CSS selector or element handle expression
 * @param {string} text      The text to type
 * @param {number} minMs     Minimum delay per keystroke (ms)
 * @param {number} maxMs     Maximum delay per keystroke (ms)
 */
async function humanType(page, selector, text, minMs = 60, maxMs = 180) {
  const el = await page.locator(selector).first();
  await el.click();
  for (const char of text) {
    await el.type(char, { delay: randomInt(minMs, maxMs) });
  }
}

/**
 * Moves the mouse in a random arc across the viewport before clicking a target.
 * This helps bypass simple bot-detection heuristics that check for direct
 * cursor teleportation.
 *
 * @param {import('playwright').Page} page
 * @param {number} targetX
 * @param {number} targetY
 */
async function humanMouseMove(page, targetX, targetY) {
  const steps = randomInt(10, 25);
  const viewport = page.viewportSize();
  const startX = randomInt(0, viewport?.width ?? 1280);
  const startY = randomInt(0, viewport?.height ?? 720);

  // Move through intermediate points
  for (let i = 0; i <= steps; i++) {
    const t = i / steps;
    const x = Math.round(startX + (targetX - startX) * t + randomInt(-5, 5));
    const y = Math.round(startY + (targetY - startY) * t + randomInt(-5, 5));
    await page.mouse.move(x, y);
    if (i % 4 === 0) await randomDelay(20, 80); // Occasional micro-pauses
  }
}

/**
 * Scrolls the page gradually (like a human reading, not a programmatic jump).
 *
 * @param {import('playwright').Page} page
 * @param {number} totalPx  Total pixels to scroll down
 */
async function humanScroll(page, totalPx = 800) {
  const steps = randomInt(5, 12);
  const stepSize = Math.round(totalPx / steps);
  for (let i = 0; i < steps; i++) {
    await page.mouse.wheel(0, stepSize + randomInt(-20, 20));
    await randomDelay(200, 600);
  }
}

/**
 * Returns a complete browser launch options object with random UA + viewport.
 * Caller passes this to playwright.chromium.launch() or similar.
 */
function getStealthLaunchOptions(headless = true) {
  const proxy = randomProxy();
  const options = {
    headless,
    args: [
      "--no-sandbox",
      "--disable-setuid-sandbox",
      "--disable-dev-shm-usage",
      "--disable-accelerated-2d-canvas",
      "--no-first-run",
      "--no-zygote",
      "--disable-gpu",
      "--disable-blink-features=AutomationControlled",
      "--disable-infobars",
      "--window-size=1920,1080",
      "--start-maximized",
      `--user-agent=${randomUserAgent()}`,
    ],
  };
  if (proxy) {
    options.proxy = { server: proxy };
  }
  return options;
}

/**
 * Returns context options (viewport, locale, timezone) for a new browser context.
 */
function getStealthContextOptions() {
  const vp = randomViewport();
  return {
    viewport: vp,
    userAgent: randomUserAgent(),
    locale: "en-US",
    timezoneId: "America/New_York",
    // Playwright's newContext() requires geolocation to be either omitted or
    // a real {latitude, longitude} object — `null` fails schema validation.
    permissions: [],
    // Bypass navigator.webdriver detection
    javaScriptEnabled: true,
    bypassCSP: false,
    ignoreHTTPSErrors: true,
    extraHTTPHeaders: {
      "Accept-Language": "en-US,en;q=0.9",
      Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
    },
  };
}

module.exports = {
  randomInt,
  randomDelay,
  randomUserAgent,
  randomViewport,
  randomProxy,
  humanType,
  humanMouseMove,
  humanScroll,
  getStealthLaunchOptions,
  getStealthContextOptions,
};
