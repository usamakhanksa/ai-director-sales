import * as cheerio from "cheerio";

import { fetchHtml } from "../http";
import { parseKeyPool, quotaTracker } from "../quota";
import type { SearchEngine, SearchOptions, SearchResult } from "../types";

const PROVIDER = "bing";
const DAILY_LIMIT = Number(process.env.BING_DAILY_LIMIT || 1000);

/**
 * Bing wraps outbound result links in a `bing.com/ck/a?...&u=a1<base64url>&...`
 * redirect. Same decoding logic as app/api/scrape/bing-search/route.ts.
 */
function decodeBingHref(href: string): string {
  if (!href) return "";
  const match = href.match(/[?&]u=a1([^&]+)/);
  if (!match) return href;

  let b64 = decodeURIComponent(match[1]).replace(/-/g, "+").replace(/_/g, "/");
  while (b64.length % 4 !== 0) b64 += "=";

  try {
    return Buffer.from(b64, "base64").toString("utf-8") || href;
  } catch {
    return href;
  }
}

/** Bing Web Search API v7 (api.bing.microsoft.com), falling back to scraping bing.com/search. */
export class BingEngine implements SearchEngine {
  readonly id = "bing";
  private usedApi = false;

  lastModeWasApi(): boolean {
    return this.usedApi;
  }

  getRemainingQuota(): number | null {
    const keys = parseKeyPool(process.env.BING_API_KEYS);
    if (keys.length === 0) return null;
    return quotaTracker.remaining(PROVIDER, keys, DAILY_LIMIT);
  }

  async search(query: string, options: SearchOptions = {}): Promise<SearchResult[]> {
    const limit = Math.min(options.limit ?? 10, 30);
    this.usedApi = false;

    const apiResults = await this.tryApi(query, limit, options);
    if (apiResults) return apiResults;

    return this.scrape(query, limit, options);
  }

  private async tryApi(query: string, limit: number, options: SearchOptions): Promise<SearchResult[] | null> {
    const pool = parseKeyPool(process.env.BING_API_KEYS);
    if (pool.length === 0) return null;

    const key = quotaTracker.pickKey(PROVIDER, pool, DAILY_LIMIT);
    if (!key) return null;

    const params = new URLSearchParams({
      q: query,
      count: String(Math.min(limit, 50)),
      offset: String(((options.page ?? 1) - 1) * limit),
    });
    if (options.lang) params.set("mkt", options.lang);
    if (options.safeSearch) params.set("safeSearch", "Strict");

    try {
      const controller = new AbortController();
      const timeout = setTimeout(() => controller.abort(), 10_000);
      let res: Response;
      try {
        res = await fetch(`https://api.bing.microsoft.com/v7.0/search?${params}`, {
          headers: { "Ocp-Apim-Subscription-Key": key },
          signal: controller.signal,
        });
      } finally {
        clearTimeout(timeout);
      }

      if (res.status === 429 || res.status === 403) {
        quotaTracker.disableTemporarily(PROVIDER, key);
        return null;
      }

      const json: any = await res.json().catch(() => null);
      if (!res.ok) throw new Error(json?.errors?.[0]?.message || `HTTP ${res.status}`);

      quotaTracker.recordUse(PROVIDER, key);
      this.usedApi = true;

      const items: any[] = Array.isArray(json?.webPages?.value) ? json.webPages.value : [];
      return items.slice(0, limit).map((item, i) => ({
        title: item.name,
        url: item.url,
        snippet: item.snippet,
        engine: this.id,
        position: i + 1,
      }));
    } catch (err) {
      console.error("Bing Web Search API failed, falling back to scraper:", err);
      return null;
    }
  }

  private async scrape(query: string, limit: number, options: SearchOptions): Promise<SearchResult[]> {
    const first = ((options.page ?? 1) - 1) * limit + 1;
    const params = new URLSearchParams({ q: query, count: String(limit), first: String(first) });
    if (options.safeSearch) params.set("adlt", "strict");

    const html = await fetchHtml(`https://www.bing.com/search?${params}`, { engine: "Bing" });
    const $ = cheerio.load(html);
    const results: SearchResult[] = [];

    $("li.b_algo").each((_, el) => {
      if (results.length >= limit) return;
      const $el = $(el);
      const $a = $el.find("h2 a").first();
      const title = $a.text().trim();
      const url = decodeBingHref($a.attr("href") || "");
      const snippet = $el.find(".b_caption p").first().text().trim();

      if (title && url) {
        results.push({ title, url, snippet: snippet || undefined, engine: this.id, position: results.length + 1 });
      }
    });

    return results;
  }
}
