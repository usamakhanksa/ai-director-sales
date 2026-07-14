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

  const data = await page.evaluate(() => {
    const nodes = Array.from(document.querySelectorAll('div[role="feed"] div[role="article"]'));
    const n = nodes[0];
    // find all <a> tags inside article with aria-label or text
    const links = Array.from(n.querySelectorAll("a")).map((a) => ({
      cls: a.className,
      aria: a.getAttribute("aria-label"),
      href: a.getAttribute("href"),
      text: a.textContent,
    }));
    return { html: n.outerHTML, links };
  });

  console.log(JSON.stringify(data.links, null, 2));
  require("fs").writeFileSync(__dirname + "/_scratch_article_full.html", data.html);

  await browser.close();
})();
