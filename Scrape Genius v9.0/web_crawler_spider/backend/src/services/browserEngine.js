/**
 * Browser Engine Service
 * 
 * Centralised factory for creating stealthy Playwright browser instances and
 * per-job browser contexts. Uses `playwright-extra` with `puppeteer-extra-plugin-stealth`
 * to defeat common bot-detection checks (webdriver flag, Chrome runtime props, etc.).
 * 
 * Usage:
 *   const { createBrowser, createContext, closeBrowser } = require('./browserEngine');
 *   const browser = await createBrowser();
 *   const { context, page } = await createContext(browser);
 *   // ... use page ...
 *   await closeBrowser(browser);
 */

"use strict";

const {
  getStealthLaunchOptions,
  getStealthContextOptions,
  randomDelay,
} = require("./antiRobotService");

// Import additional anti-detection libraries
const { FingerprintGenerator } = require('fingerprint-generator');
const { FingerprintInjector } = require('fingerprint-injector');
const ghostCursor = require('ghost-cursor');

// Initialize fingerprint generator
const fingerprintGenerator = new FingerprintGenerator();
const fingerprintInjector = new FingerprintInjector();

// ─────────────────────────────────────────────────────────────────────────────
// Stealth Browser Setup
// ─────────────────────────────────────────────────────────────────────────────

let _stealthChromium = null;

/**
 * Lazily initialises playwright-extra with the stealth plugin.
 * Falls back to plain playwright if playwright-extra isn't installed.
 */
function getStealthChromium() {
  if (_stealthChromium) return _stealthChromium;

  try {
    // playwright-extra wraps playwright and applies stealth patches
    const { chromium } = require("playwright-extra");
    const StealthPlugin = require("puppeteer-extra-plugin-stealth");
    chromium.use(StealthPlugin());
    _stealthChromium = chromium;
    console.log("[BrowserEngine] Using playwright-extra with stealth plugin ✅");
  } catch {
    // Graceful degradation: use plain playwright if stealth not installed
    const { chromium } = require("playwright");
    _stealthChromium = chromium;
    console.warn(
      "[BrowserEngine] playwright-extra not found — falling back to plain Playwright. " +
      "Run: npm install playwright-extra puppeteer-extra-plugin-stealth"
    );
  }

  return _stealthChromium;
}

/**
 * Sets up request interception to block images, fonts, and tracking requests
 * @param {import('playwright').Page} page
 */
async function setupRequestInterception(page) {
  await page.route('**/*', (route) => {
    const url = route.request().url();
    const blockTypes = ['.png', '.jpg', '.jpeg', '.gif', '.svg', '.woff', '.woff2', '.ttf', 'google-analytics.com', 'doubleclick.net', 'googletagmanager.com', 'facebook.net', 'connect.facebook.net'];
    if (blockTypes.some(ext => url.includes(ext))) {
      route.abort();
    } else {
      route.continue();
    }
  });
}

/**
 * Launches a new stealth browser instance with advanced anti-detection.
 * 
 * @param {object} opts
 * @param {boolean} [opts.headless=true]   Run headless (set false for debugging)
 * @param {string}  [opts.proxy]           Optional proxy URL (overrides random)
 * @param {boolean} [opts.blockMedia=true] Whether to block images and fonts
 * @returns {Promise<import('playwright').Browser>}
 */
async function createBrowser({ headless = true, proxy, blockMedia = true } = {}) {
  const launchOpts = getStealthLaunchOptions(headless);

  // Override proxy if explicitly provided
  if (proxy) {
    launchOpts.proxy = { server: proxy };
  }

  const chromium = getStealthChromium();
  const browser = await chromium.launch(launchOpts);
  console.log(`[BrowserEngine] Browser launched (headless=${headless})`);
  return browser;
}

/**
 * Creates an isolated browser context + a new page, with enhanced stealth options.
 * Each scraping job should get its own context to avoid cookie/session bleed.
 * 
 * @param {import('playwright').Browser} browser
 * @param {object} [overrides]   Optional context option overrides
 * @param {boolean} [blockMedia=true] Whether to block media requests
 * @returns {Promise<{ context: BrowserContext, page: Page }>}
 */
async function createContext(browser, overrides = {}, blockMedia = true) {
  // Generate a random fingerprint for this context
  const fingerprint = fingerprintGenerator.getFingerprint({
    devices: ['desktop'],
    operatingSystems: ['windows', 'macos'],
    browsers: ['chrome'],
  });

  const contextOpts = { 
    ...getStealthContextOptions(), 
    ...overrides,
    viewport: fingerprint.viewport,
    userAgent: fingerprint.userAgent,
  };
  
  const context = await browser.newContext(contextOpts);

  // Inject the fingerprint into the context
  await fingerprintInjector.attachFingerprintToPlaywright(context, fingerprint);

  const page = await context.newPage();

  // Set extra headers on each request
  await page.setExtraHTTPHeaders({
    "Accept-Language": "en-US,en;q=0.9",
    "Accept-Encoding": "gzip, deflate, br",
  });

  // Inject JavaScript patches into every new page to defeat webdriver detection
  await context.addInitScript(() => {
    // Delete the webdriver property (set by Playwright/CDP)
    Object.defineProperty(navigator, "webdriver", { get: () => undefined });

    // Spoof navigator.plugins to look like a real browser
    Object.defineProperty(navigator, "plugins", {
      get: () => [
        { name: "Chrome PDF Plugin" },
        { name: "Chrome PDF Viewer" },
        { name: "Native Client" },
      ],
    });

    // Override chrome runtime to avoid detection
    if (typeof window !== "undefined") {
      window.chrome = {
        runtime: {},
        loadTimes: () => ({}),
        csi: () => ({}),
        app: {},
      };
    }

    // Spoof language navigator
    Object.defineProperty(navigator, "languages", {
      get: () => ["en-US", "en"],
    });

    // Remove headless trace in userAgent
    const ua = navigator.userAgent;
    if (ua.includes("HeadlessChrome")) {
      Object.defineProperty(navigator, "userAgent", {
        get: () => ua.replace("HeadlessChrome", "Chrome"),
      });
    }
    
    // Additional fingerprint spoofing
    Object.defineProperty(navigator, "platform", { get: () => fingerprint.navigator.platform });
    Object.defineProperty(navigator, "hardwareConcurrency", { get: () => fingerprint.navigator.hardwareConcurrency });
    Object.defineProperty(navigator, "deviceMemory", { get: () => fingerprint.navigator.deviceMemory });
    Object.defineProperty(navigator, "maxTouchPoints", { get: () => fingerprint.navigator.maxTouchPoints });
  });


  // Setup request interception if enabled
  if (blockMedia) {
    await setupRequestInterception(page);
  }

  return { context, page };
}

/**
 * Performs a human-like click using ghost cursor
 * @param {import('playwright').Page} page
 * @param {string} selector
 */
async function humanClick(page, selector) {
  const cursor = ghostCursor.createCursor(page);
  await cursor.click(selector);
}

/**
 * Performs human-like typing with variable delays
 * @param {import('playwright').Page} page
 * @param {string} selector
 * @param {string} text
 */
async function humanType(page, selector, text) {
  const cursor = ghostCursor.createCursor(page);
  await cursor.move(selector);
  await page.type(selector, text, { delay: 50 + Math.random() * 100 }); // variable delay
}

// ─────────────────────────────────────────────────────────────────────────────
// Public API
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Safely closes a browser instance (swallows errors).
 * @param {import('playwright').Browser} browser
 */
async function closeBrowser(browser) {
  try {
    await browser.close();
  } catch (err) {
    console.warn("[BrowserEngine] Error closing browser:", err.message);
  }
}

/**
 * Opens a URL on a page with retry logic and human-like behaviour.
 * 
 * @param {import('playwright').Page} page
 * @param {string} url
 * @param {number} [retries=3]
 */
async function navigateTo(page, url, retries = 3) {
  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      await page.goto(url, {
        waitUntil: "domcontentloaded",
        timeout: Number(process.env.PLAYWRIGHT_TIMEOUT) || 30000,
      });
      await randomDelay(800, 2000); // Brief post-load pause
      return;
    } catch (err) {
      if (attempt === retries) throw err;
      console.warn(`[BrowserEngine] Navigation failed (attempt ${attempt}/${retries}): ${err.message}`);
      await randomDelay(2000, 5000);
    }
  }
}

module.exports = {
  createBrowser,
  createContext,
  closeBrowser,
  navigateTo,
  humanClick,
  humanType,
  setupRequestInterception,
};