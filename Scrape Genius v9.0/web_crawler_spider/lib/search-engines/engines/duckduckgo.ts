import * as cheerio from "cheerio";

import { fetchHtml } from "../http";
import type { SearchEngine, SearchOptions, SearchResult } from "../types";

/** DuckDuckGo wraps outbound links in `//duckduckgo.com/l/?uddg=<url-encoded>`. */
function decodeDuckDuckGoHref(href: string): string {
  if (!href) return "";
  const full = href.startsWith("//") ? `https:${href}` : href;
  try {
    return new URL(full).searchParams.get("uddg") || full;
  } catch {
    return full;
  }
}

/** No official public search API — always scrapes the HTML-only lite endpoint. */
export class DuckDuckGoEngine implements SearchEngine {
  readonly id = "duckduckgo";

  lastModeWasApi(): boolean {
    return false;
  }

  getRemainingQuota(): number | null {
    return null;
  }

  async search(query: string, options: SearchOptions = {}): Promise<SearchResult[]> {
    const limit = Math.min(options.limit ?? 10, 30);
    const params = new URLSearchParams({ q: query });
    if (options.page && options.page > 1) params.set("s", String((options.page - 1) * 30));
    if (options.safeSearch) params.set("kp", "1");

    const html = await fetchHtml(`https://html.duckduckgo.com/html/?${params}`, { engine: "DuckDuckGo" });
    const $ = cheerio.load(html);
    const results: SearchResult[] = [];

    $("a.result__a").each((_, a) => {
      if (results.length >= limit) return;
      const $a = $(a);
      const title = $a.text().trim();
      const url = decodeDuckDuckGoHref($a.attr("href") || "");
      const snippet = $a.closest(".result, .result__body").find(".result__snippet").first().text().trim();

      if (title && url) {
        results.push({ title, url, snippet: snippet || undefined, engine: this.id, position: results.length + 1 });
      }
    });

    return results;
  }
}
