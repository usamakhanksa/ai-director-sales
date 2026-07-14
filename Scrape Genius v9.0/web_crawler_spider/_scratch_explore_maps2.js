const { chromium } = require("playwright");

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36" });
  const query = "coffee shops in New York";
  await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}`, { waitUntil: "domcontentloaded", timeout: 45000 });
  await page.waitForTimeout(3000);

  // Scroll the feed a couple times
  const feed = page.locator('div[role="feed"]');
  for (let i = 0; i < 3; i++) {
    await feed.evaluate((el) => el.scrollBy(0, 2000));
    await page.waitForTimeout(1500);
  }

  const linkCount = await page.locator('div[role="feed"] a[href^="https://www.google.com/maps/place"]').count();
  console.log("place links after scroll:", linkCount);

  // Inspect first 5 listing containers: go up from the <a> to find a stable ancestor
  const results = await page.evaluate(() => {
    const feedEl = document.querySelector('div[role="feed"]');
    const links = Array.from(feedEl.querySelectorAll('a[href^="https://www.google.com/maps/place"]'));
    return links.slice(0, 6).map((a) => {
      // find ancestor that looks like the listing card (walk up until parentElement has multiple such links as siblings' containers)
      let el = a.parentElement;
      let depth = 0;
      let ancestorInfo = [];
      while (el && depth < 6) {
        ancestorInfo.push({ tag: el.tagName, cls: el.className, role: el.getAttribute("role"), textLen: el.innerText ? el.innerText.length : 0 });
        el = el.parentElement;
        depth++;
      }
      return {
        ariaLabel: a.getAttribute("aria-label"),
        href: a.getAttribute("href"),
        ancestorInfo,
      };
    });
  });

  console.log(JSON.stringify(results, null, 2));

  await browser.close();
})();
