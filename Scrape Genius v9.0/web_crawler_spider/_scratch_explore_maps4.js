const { chromium } = require("playwright");

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({
    userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    locale: "en-US",
  });
  const query = "plumbers in Austin";
  await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}?hl=en`, { waitUntil: "domcontentloaded", timeout: 45000 });
  await page.waitForTimeout(3000);

  const feed = page.locator('div[role="feed"]');
  for (let i = 0; i < 4; i++) {
    await feed.evaluate((el) => el.scrollBy(0, 2000));
    await page.waitForTimeout(1500);
  }

  const articles = await page.evaluate(() => {
    const nodes = Array.from(document.querySelectorAll('div[role="feed"] div[role="article"]'));
    return nodes.slice(0, 8).map((n) => ({
      ariaLabel: n.getAttribute("aria-label"),
      nameSpan: n.querySelector("a.hfpxzc")?.getAttribute("aria-label"),
      href: n.querySelector("a.hfpxzc")?.getAttribute("href"),
      ratingAria: n.querySelector('span[role="img"]')?.getAttribute("aria-label"),
      ratingNum: n.querySelector(".MW4etd")?.textContent,
      innerText: n.innerText,
    }));
  });

  articles.forEach((a, i) => {
    console.log(`\n===== ARTICLE ${i} =====`);
    console.log("name:", a.nameSpan);
    console.log("ratingAria:", a.ratingAria, "ratingNum:", a.ratingNum);
    console.log("innerText:\n" + a.innerText);
  });

  await browser.close();
})();
