export interface SearchResult {
  title: string;
  url: string;
  snippet?: string;
  engine: string;
  position: number;
}

export interface SearchOptions {
  page?: number;
  limit?: number;
  lang?: string;
  safeSearch?: boolean;
}

export interface SearchEngine {
  readonly id: string;
  search(query: string, options?: SearchOptions): Promise<SearchResult[]>;
  getRemainingQuota?(): number | null;
  /** Whether the last search() call used the official API (true) or the scraper fallback (false/undefined). */
  lastModeWasApi?(): boolean;
}
