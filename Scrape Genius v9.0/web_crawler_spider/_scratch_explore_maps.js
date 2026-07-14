const { chromium } = require("playwright");

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36" });
  const query = "coffee shops in New York";
  await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}`, { waitUntil: "domcontentloaded", timeout: 45000 });

  // try to dismiss consent dialog if present
  try {
    const consentBtn = page.locator('button:has-text("Accept all"), button:has-text("I agree"), form[action*="consent"] button');
    if (await consentBtn.first().isVisible({ timeout: 5000 })) {
      await consentBtn.first().click();
      console.log("clicked consent");
    }
  } catch (e) {
    console.log("no consent dialog", e.message);
  }

  await page.waitForTimeout(3000);

  const feedCount = await page.locator('div[role="feed"]').count();
  console.log("feed count:", feedCount);

  const url = page.url();
  console.log("current url:", url);

  // dump some structure
  const html = await page.content();
  require("fs").writeFileSync(__dirname + "/maps-dump.html", html);
  console.log("html length:", html.length);

  // try common listing link selector
  const linkCount = await page.locator('a[href^="https://www.google.com/maps/place"]').count();
  console.log("place link count:", linkCount);

  const feedChildren = await page.locator('div[role="feed"] > div').count();
  console.log("feed direct children:", feedChildren);

  await browser.close();
})();
