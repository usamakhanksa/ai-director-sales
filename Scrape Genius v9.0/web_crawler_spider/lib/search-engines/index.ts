import { BingEngine } from "./engines/bing";
import { DuckDuckGoEngine } from "./engines/duckduckgo";
import { GoogleEngine } from "./engines/google";
import { YahooEngine } from "./engines/yahoo";
import type { SearchEngine, SearchOptions, SearchResult } from "./types";

export type { SearchEngine, SearchOptions, SearchResult };
export { GoogleEngine, BingEngine, DuckDuckGoEngine, YahooEngine };

export const ENGINE_IDS = ["google", "bing", "duckduckgo", "yahoo"] as const;
export type EngineId = (typeof ENGINE_IDS)[number];

export function createEngine(id: EngineId, userId?: number): SearchEngine {
  switch (id) {
    case "google":
      return new GoogleEngine(userId);
    case "bing":
      return new BingEngine();
    case "duckduckgo":
      return new DuckDuckGoEngine();
    case "yahoo":
      return new YahooEngine();
  }
}

export interface EngineOutcome {
  engine: EngineId;
  results: SearchResult[];
  mode: "api" | "scraper" | "failed";
  error?: string;
  quotaRemaining: number | null;
}

export interface MultiSearchResult {
  query: string;
  results: SearchResult[];
  engines: EngineOutcome[];
}

/** Interleaves per-engine result lists round-robin so no single engine dominates the top of the merged list. */
function interleave(lists: SearchResult[][]): SearchResult[] {
  const merged: SearchResult[] = [];
  const maxLen = Math.max(0, ...lists.map((l) => l.length));
  for (let i = 0; i < maxLen; i += 1) {
    for (const list of lists) {
      if (list[i]) merged.push(list[i]);
    }
  }
  return merged;
}

/**
 * Runs the requested engines in parallel (each with its own timeout via
 * fetchHtml/callApi), tolerating individual failures, and returns merged +
 * per-engine results plus quota metadata.
 */
export async function runMultiSearch(
  query: string,
  requestedEngines: EngineId[],
  options: SearchOptions,
  userId?: number,
): Promise<MultiSearchResult> {
  const engines = requestedEngines.map((id) => ({ id, engine: createEngine(id, userId) }));

  const outcomes = await Promise.all(
    engines.map(async ({ id, engine }): Promise<EngineOutcome> => {
      try {
        const results = await withTimeout(engine.search(query, options), 15_000, id);
        return {
          engine: id,
          results,
          mode: engine.lastModeWasApi?.() ? "api" : "scraper",
          quotaRemaining: engine.getRemainingQuota?.() ?? null,
        };
      } catch (err) {
        console.error(`Search engine "${id}" failed:`, err);
        return {
          engine: id,
          results: [],
          mode: "failed",
          error: err instanceof Error ? err.message : String(err),
          quotaRemaining: engine.getRemainingQuota?.() ?? null,
        };
      }
    }),
  );

  const merged = interleave(outcomes.map((o) => o.results)).map((r, i) => ({ ...r, position: i + 1 }));

  return { query, results: merged, engines: outcomes };
}

function withTimeout<T>(promise: Promise<T>, ms: number, label: string): Promise<T> {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => reject(new Error(`${label} timed out after ${ms}ms`)), ms);
    promise.then(
      (v) => {
        clearTimeout(timer);
        resolve(v);
      },
      (e) => {
        clearTimeout(timer);
        reject(e);
      },
    );
  });
}
