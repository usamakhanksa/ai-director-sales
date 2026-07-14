const { chromium } = require("playwright");

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({
    userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    locale: "en-US",
  });
  const query = "Eiffel Tower";
  await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}?hl=en`, { waitUntil: "domcontentloaded", timeout: 45000 });
  await page.waitForTimeout(3000);
  console.log("url:", page.url());
  const feedCount = await page.locator('div[role="feed"]').count();
  console.log("feed count:", feedCount);
  const h1 = await page.locator("h1").first().textContent().catch(() => null);
  console.log("h1:", h1);
  await browser.close();
})();
