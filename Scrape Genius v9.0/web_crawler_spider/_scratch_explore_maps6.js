const { chromium } = require("playwright");

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({
    userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    locale: "en-US",
  });
  const query = "restaurants in Chicago";
  await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}?hl=en`, { waitUntil: "domcontentloaded", timeout: 45000 });
  await page.waitForTimeout(3000);

  const feed = page.locator('div[role="feed"]');
  for (let i = 0; i < 4; i++) {
    await feed.evaluate((el) => el.scrollBy(0, 2000));
    await page.waitForTimeout(1500);
  }

  const texts = await page.evaluate(() => {
    const nodes = Array.from(document.querySelectorAll('div[role="feed"] div[role="article"]'));
    return nodes.slice(0, 6).map((n) => n.innerText);
  });
  texts.forEach((t, i) => console.log(`\n=== ${i} ===\n${t}`));

  // also check aria-label on the whole article/link for review count patterns
  const ariaLabels = await page.evaluate(() => {
    const nodes = Array.from(document.querySelectorAll('div[role="feed"] div[role="article"] span[role="img"]'));
    return nodes.map(n => n.getAttribute('aria-label'));
  });
  console.log("rating aria-labels:", ariaLabels);

  await browser.close();
})();
