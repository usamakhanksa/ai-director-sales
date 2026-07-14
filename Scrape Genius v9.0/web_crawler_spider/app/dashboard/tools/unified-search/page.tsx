"use client";

import { useState, type FormEvent } from "react";

import DashboardShell from "@/components/DashboardShell";
import ResultsTable, { downloadCsv } from "@/components/ResultsTable";
import { getToken } from "@/lib/client-auth";
import { input, label, button } from "@/lib/ui-styles";

const ALL_ENGINES = [
  { id: "google", label: "Google" },
  { id: "bing", label: "Bing" },
  { id: "duckduckgo", label: "DuckDuckGo" },
  { id: "yahoo", label: "Yahoo" },
] as const;

type EngineStatus = { mode: "api" | "scraper" | "failed"; error?: string; count: number };

export default function UnifiedSearchPage() {
  const [query, setQuery] = useState("");
  const [engines, setEngines] = useState<string[]>(ALL_ENGINES.map((e) => e.id));
  const [limit, setLimit] = useState(10);

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [results, setResults] = useState<Record<string, unknown>[]>([]);
  const [engineStatus, setEngineStatus] = useState<Record<string, EngineStatus>>({});
  const [quotaRemaining, setQuotaRemaining] = useState<Record<string, number | null>>({});

  function toggleEngine(id: string) {
    setEngines((prev) => (prev.includes(id) ? prev.filter((e) => e !== id) : [...prev, id]));
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    if (!query.trim() || engines.length === 0) return;

    setError(null);
    setLoading(true);
    setResults([]);

    const token = getToken();

    try {
      const res = await fetch("/api/search", {
        method: "POST",
        headers: { "Content-Type": "application/json", Authorization: `Bearer ${token}` },
        body: JSON.stringify({ q: query, engines, limit }),
      });
      const json = await res.json();

      if (!res.ok || !json.success) {
        setError(json.error || `Request failed (${res.status})`);
      } else {
        setResults(json.data.results ?? []);
        setEngineStatus(json.data.meta?.engineStatus ?? {});
        setQuotaRemaining(json.data.meta?.quotaRemaining ?? {});
      }
    } catch {
      setError("Network error — is the server running?");
    } finally {
      setLoading(false);
    }
  }

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>Multi-Engine Search</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>
        Queries Google, Bing, DuckDuckGo, and Yahoo in parallel. Engines with a configured API key use it
        automatically; otherwise (or on quota exhaustion) results are scraped server-side.
      </p>

      <form onSubmit={handleSubmit} style={{ display: "grid", gap: "0.75rem", maxWidth: 520, marginBottom: "1.5rem" }}>
        <label style={label}>
          Search query
          <input
            style={input}
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="e.g. b2b lead generation software"
            required
          />
        </label>

        <div style={{ display: "flex", gap: "1rem", flexWrap: "wrap" }}>
          {ALL_ENGINES.map((eng) => (
            <label key={eng.id} style={{ display: "flex", alignItems: "center", gap: "0.35rem", fontSize: "0.875rem" }}>
              <input type="checkbox" checked={engines.includes(eng.id)} onChange={() => toggleEngine(eng.id)} />
              {eng.label}
              {quotaRemaining[eng.id] != null && (
                <span style={{ color: "var(--muted, #64748b)" }}>({quotaRemaining[eng.id]} left)</span>
              )}
            </label>
          ))}
        </div>

        <label style={label}>
          Results per engine
          <input
            style={input}
            type="number"
            min={1}
            max={30}
            value={limit}
            onChange={(e) => setLimit(Number(e.target.value) || 10)}
          />
        </label>

        <button type="submit" style={button} disabled={loading || engines.length === 0}>
          {loading ? "Searching…" : "Search"}
        </button>
      </form>

      {error && <p style={{ color: "#dc2626", marginBottom: "1rem" }}>{error}</p>}

      {Object.keys(engineStatus).length > 0 && (
        <div style={{ display: "flex", gap: "0.75rem", flexWrap: "wrap", marginBottom: "1rem", fontSize: "0.8rem" }}>
          {Object.entries(engineStatus).map(([id, status]) => (
            <span
              key={id}
              style={{
                padding: "0.15rem 0.5rem",
                borderRadius: 999,
                border: "1px solid var(--border, #e5e7eb)",
                color: status.mode === "failed" ? "#dc2626" : "var(--muted, #64748b)",
              }}
              title={status.error}
            >
              {id}: {status.mode === "failed" ? "failed" : `${status.mode} · ${status.count}`}
            </span>
          ))}
        </div>
      )}

      {results.length > 0 && (
        <button
          type="button"
          style={{ ...button, marginBottom: "0.75rem" }}
          onClick={() => downloadCsv(results, `search-${Date.now()}.csv`)}
        >
          Export CSV
        </button>
      )}

      <ResultsTable rows={results} />
    </DashboardShell>
  );
}
