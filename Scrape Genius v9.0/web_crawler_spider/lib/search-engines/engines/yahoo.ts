import * as cheerio from "cheerio";

import { fetchHtml } from "../http";
import type { SearchEngine, SearchOptions, SearchResult } from "../types";

/** Yahoo wraps outbound links in `r.search.yahoo.com/...` with the real URL between `/RU=` and the next `/`. */
function decodeYahooHref(href: string): string {
  if (!href) return "";
  const match = href.match(/\/RU=([^/]+)\//);
  if (!match) return href;
  try {
    return decodeURIComponent(match[1]);
  } catch {
    return href;
  }
}

/** No public search API — always scrapes search.yahoo.com. */
export class YahooEngine implements SearchEngine {
  readonly id = "yahoo";

  lastModeWasApi(): boolean {
    return false;
  }

  getRemainingQuota(): number | null {
    return null;
  }

  async search(query: string, options: SearchOptions = {}): Promise<SearchResult[]> {
    const limit = Math.min(options.limit ?? 10, 30);
    const params = new URLSearchParams({ p: query });
    if (options.page && options.page > 1) params.set("b", String((options.page - 1) * 10 + 1));

    const html = await fetchHtml(`https://search.yahoo.com/search?${params}`, { engine: "Yahoo" });
    const $ = cheerio.load(html);
    const results: SearchResult[] = [];

    $("#web ol.reg > li").each((_, li) => {
      if (results.length >= limit) return;
      const $li = $(li);
      const $container = $li.find(".dd.algo, .algo-sr").first();
      const $scope = $container.length ? $container : $li;

      const title = $scope.find("h3.title").first().text().trim();
      const $a = $scope.find(".compTitle a").first();
      const url = decodeYahooHref($a.attr("href") || "");
      const snippet = $scope.find(".compText p, .compText .aAbs").first().text().trim();

      if (title && url) {
        results.push({ title, url, snippet: snippet || undefined, engine: this.id, position: results.length + 1 });
      }
    });

    return results;
  }
}
