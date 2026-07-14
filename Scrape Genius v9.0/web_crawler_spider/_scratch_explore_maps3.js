const { chromium } = require("playwright");

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36" });
  const query = "coffee shops in New York";
  await page.goto(`https://www.google.com/maps/search/${encodeURIComponent(query)}`, { waitUntil: "domcontentloaded", timeout: 45000 });
  await page.waitForTimeout(3000);

  const feed = page.locator('div[role="feed"]');
  for (let i = 0; i < 3; i++) {
    await feed.evaluate((el) => el.scrollBy(0, 2000));
    await page.waitForTimeout(1500);
  }

  const articles = await page.evaluate(() => {
    const nodes = Array.from(document.querySelectorAll('div[role="feed"] div[role="article"]'));
    return nodes.slice(0, 4).map((n) => ({
      ariaLabel: n.getAttribute("aria-label"),
      innerText: n.innerText,
      innerHTML: n.innerHTML,
    }));
  });

  articles.forEach((a, i) => {
    console.log(`\n===== ARTICLE ${i} (${a.ariaLabel}) =====`);
    console.log("--- innerText ---");
    console.log(a.innerText);
  });

  require("fs").writeFileSync(__dirname + "/_scratch_article0.html", articles[0] ? articles[0].innerHTML : "");

  await browser.close();
})();
