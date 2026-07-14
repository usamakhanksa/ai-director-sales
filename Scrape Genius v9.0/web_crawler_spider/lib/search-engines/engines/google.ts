import * as cheerio from "cheerio";

import { pickAvailableKey, incrementKeyUsage, UsageLimitError } from "@/lib/keys";
import { fetchHtml } from "../http";
import { parseKeyPool, quotaTracker } from "../quota";
import type { SearchEngine, SearchOptions, SearchResult } from "../types";

const PROVIDER = "google";
const DAILY_LIMIT = Number(process.env.GOOGLE_DAILY_LIMIT || 100);

/**
 * Google Custom Search. API mode tries (in order): the requesting user's own
 * DB-stored key (lib/keys.ts, existing per-user rotation), then a pool of
 * global env keys (GOOGLE_API_KEYS, comma-separated, sharing GOOGLE_CX).
 * Falls back to scraping www.google.com/search when no key has quota left
 * or the API errors with a quota/rate-limit status.
 */
export class GoogleEngine implements SearchEngine {
  readonly id = "google";
  private usedApi = false;

  constructor(private userId?: number) {}

  lastModeWasApi(): boolean {
    return this.usedApi;
  }

  getRemainingQuota(): number | null {
    const keys = parseKeyPool(process.env.GOOGLE_API_KEYS);
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
    if (this.userId) {
      const userKey = await pickAvailableKey(this.userId);
      if (userKey) {
        try {
          const results = await this.callApi(userKey.googleApiKey, userKey.searchEngineId, query, limit, options);
          await incrementKeyUsage(userKey.id, userKey.dailyLimit, 1);
          this.usedApi = true;
          return results;
        } catch (err) {
          if (!(err instanceof UsageLimitError)) {
            console.error("Google Custom Search (user key) failed, falling back:", err);
          }
        }
      }
    }

    const cx = process.env.GOOGLE_CX;
    const pool = parseKeyPool(process.env.GOOGLE_API_KEYS);
    if (!cx || pool.length === 0) return null;

    const key = quotaTracker.pickKey(PROVIDER, pool, DAILY_LIMIT);
    if (!key) return null;

    try {
      const results = await this.callApi(key, cx, query, limit, options);
      quotaTracker.recordUse(PROVIDER, key);
      this.usedApi = true;
      return results;
    } catch (err) {
      console.error("Google Custom Search (env key) failed, falling back to scraper:", err);
      quotaTracker.disableTemporarily(PROVIDER, key);
      return null;
    }
  }

  private async callApi(
    key: string,
    cx: string,
    query: string,
    limit: number,
    options: SearchOptions,
  ): Promise<SearchResult[]> {
    const params = new URLSearchParams({
      key,
      cx,
      q: query,
      num: String(Math.min(limit, 10)),
      start: String(((options.page ?? 1) - 1) * 10 + 1),
    });
    if (options.lang) params.set("lr", `lang_${options.lang}`);
    if (options.safeSearch) params.set("safe", "active");

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 10_000);
    let res: Response;
    try {
      res = await fetch(`https://www.googleapis.com/customsearch/v1?${params}`, { signal: controller.signal });
    } finally {
      clearTimeout(timeout);
    }

    const json: any = await res.json().catch(() => null);
    if (!res.ok) {
      const message = json?.error?.message || `HTTP ${res.status}`;
      throw new Error(`Google Custom Search API error: ${message}`);
    }

    const items: any[] = Array.isArray(json?.items) ? json.items : [];
    return items.slice(0, limit).map((item, i) => ({
      title: item.title,
      url: item.link,
      snippet: item.snippet,
      engine: this.id,
      position: i + 1,
    }));
  }

  private async scrape(query: string, limit: number, options: SearchOptions): Promise<SearchResult[]> {
    const params = new URLSearchParams({ q: query });
    if (options.page && options.page > 1) params.set("start", String((options.page - 1) * 10));
    if (options.lang) params.set("lr", `lang_${options.lang}`);
    if (options.safeSearch) params.set("safe", "active");

    const html = await fetchHtml(`https://www.google.com/search?${params}`, { engine: "Google" });
    const $ = cheerio.load(html);
    const results: SearchResult[] = [];

    $("div.g, div.MjjYud").each((_, el) => {
      if (results.length >= limit) return;
      const $el = $(el);
      const $a = $el.find("a").first();
      const href = $a.attr("href") || "";
      const title = $el.find("h3").first().text().trim();
      const snippet = $el.find("div.VwiC3b, span.aCOpRe").first().text().trim();

      if (title && href.startsWith("http")) {
        results.push({ title, url: href, snippet: snippet || undefined, engine: this.id, position: results.length + 1 });
      }
    });

    return results;
  }
}
