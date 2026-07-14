"use client";

import { useEffect, useState, type FormEvent } from "react";

import DashboardShell from "@/components/DashboardShell";
import ResultsTable, { downloadCsv } from "@/components/ResultsTable";
import { getToken } from "@/lib/client-auth";
import { input, label, button, secondaryButton } from "@/lib/ui-styles";

interface Connector {
  id: number;
  name: string;
  method: string;
  url: string;
  hasApiKey: boolean;
  authType: string;
  authParam: string | null;
  resultsPath: string | null;
  fieldMap: Record<string, string> | null;
}

function authHeaders() {
  return { Authorization: `Bearer ${getToken()}` };
}

export default function CustomApiConnectorPage() {
  const [connectors, setConnectors] = useState<Connector[] | null>(null);
  const [error, setError] = useState<string | null>(null);

  const [name, setName] = useState("");
  const [method, setMethod] = useState<"GET" | "POST">("GET");
  const [url, setUrl] = useState("");
  const [apiKey, setApiKey] = useState("");
  const [authType, setAuthType] = useState<"none" | "query" | "header" | "bearer">("none");
  const [authParam, setAuthParam] = useState("");
  const [resultsPath, setResultsPath] = useState("");
  const [fieldMapText, setFieldMapText] = useState('{"companyName":"name","website":"url"}');
  const [creating, setCreating] = useState(false);

  const [runningId, setRunningId] = useState<number | null>(null);
  const [runQuery, setRunQuery] = useState("");
  const [runResult, setRunResult] = useState<any>(null);
  const [runError, setRunError] = useState<string | null>(null);

  async function loadConnectors() {
    try {
      const res = await fetch("/api/api-connectors/", { headers: authHeaders() });
      const json = await res.json();
      if (json.success) setConnectors(json.data);
      else setError(json.error);
    } catch {
      setError("Could not load connectors");
    }
  }

  useEffect(() => {
    loadConnectors();
  }, []);

  async function handleCreate(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setCreating(true);
    try {
      let fieldMap: Record<string, string> | undefined;
      if (fieldMapText.trim()) {
        try {
          fieldMap = JSON.parse(fieldMapText);
        } catch {
          setError("Field map must be valid JSON");
          setCreating(false);
          return;
        }
      }

      const res = await fetch("/api/api-connectors/", {
        method: "POST",
        headers: { "Content-Type": "application/json", ...authHeaders() },
        body: JSON.stringify({
          name,
          method,
          url,
          apiKey: apiKey || undefined,
          authType,
          authParam: authParam || undefined,
          resultsPath: resultsPath || undefined,
          fieldMap,
        }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        setError(json.error || "Failed to create connector");
      } else {
        setName("");
        setUrl("");
        setApiKey("");
        setAuthParam("");
        setResultsPath("");
        await loadConnectors();
      }
    } catch {
      setError("Network error while creating connector");
    } finally {
      setCreating(false);
    }
  }

  async function handleDelete(id: number) {
    await fetch(`/api/api-connectors/${id}/`, { method: "DELETE", headers: authHeaders() });
    await loadConnectors();
  }

  async function handleRun(id: number) {
    setRunError(null);
    setRunResult(null);
    try {
      const res = await fetch(`/api/api-connectors/${id}/run/`, {
        method: "POST",
        headers: { "Content-Type": "application/json", ...authHeaders() },
        body: JSON.stringify({ query: runQuery }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) setRunError(json.error || "Run failed");
      else setRunResult(json.data);
    } catch {
      setRunError("Network error while running connector");
    }
  }

  const rows: Record<string, unknown>[] = Array.isArray(runResult?.results) ? runResult.results : [];

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>Custom API Connector</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>
        Point ScrapeGenius at any third-party JSON API — paste a URL and key, map the response fields, and run it
        like any other scraper.
      </p>

      {error && <p style={{ color: "#c00" }}>{error}</p>}

      <form onSubmit={handleCreate} style={{ display: "grid", gap: "0.6rem", maxWidth: 560, marginBottom: "2rem" }}>
        <label style={label}>
          Name
          <input value={name} onChange={(e) => setName(e.target.value)} required style={input} />
        </label>
        <label style={label}>
          URL (use <code>{"{query}"}</code> as a placeholder for the search term)
          <input
            value={url}
            onChange={(e) => setUrl(e.target.value)}
            required
            placeholder="https://api.example.com/search?q={query}"
            style={input}
          />
        </label>
        <div style={{ display: "flex", gap: "0.75rem" }}>
          <label style={{ ...label, flex: 1 }}>
            Method
            <select value={method} onChange={(e) => setMethod(e.target.value as "GET" | "POST")} style={input}>
              <option value="GET">GET</option>
              <option value="POST">POST</option>
            </select>
          </label>
          <label style={{ ...label, flex: 1 }}>
            Auth type
            <select value={authType} onChange={(e) => setAuthType(e.target.value as any)} style={input}>
              <option value="none">None</option>
              <option value="query">Query param</option>
              <option value="header">Header</option>
              <option value="bearer">Bearer token</option>
            </select>
          </label>
        </div>
        {authType !== "none" && (
          <label style={label}>
            API key
            <input value={apiKey} onChange={(e) => setApiKey(e.target.value)} style={input} />
          </label>
        )}
        {(authType === "query" || authType === "header") && (
          <label style={label}>
            {authType === "query" ? "Query param name" : "Header name"}
            <input value={authParam} onChange={(e) => setAuthParam(e.target.value)} style={input} />
          </label>
        )}
        <label style={label}>
          Results path (dot-path to the array in the response, optional)
          <input value={resultsPath} onChange={(e) => setResultsPath(e.target.value)} placeholder="data.items" style={input} />
        </label>
        <label style={label}>
          Field map (JSON: normalized field → dot-path in each result item)
          <textarea value={fieldMapText} onChange={(e) => setFieldMapText(e.target.value)} rows={3} style={input} />
        </label>
        <button type="submit" disabled={creating} style={button}>
          {creating ? "Saving…" : "Save connector"}
        </button>
      </form>

      <h2 style={{ fontSize: "1.05rem", marginBottom: "0.75rem" }}>Your connectors</h2>
      {connectors === null && <p>Loading…</p>}
      {connectors !== null && connectors.length === 0 && <p style={{ color: "var(--muted, #64748b)" }}>No connectors yet.</p>}

      <div style={{ display: "grid", gap: "1rem" }}>
        {(connectors ?? []).map((c) => (
          <div key={c.id} style={{ border: "1px solid var(--border, #e5e7eb)", borderRadius: 8, padding: "1rem" }}>
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "0.5rem" }}>
              <strong>{c.name}</strong>
              <button type="button" onClick={() => handleDelete(c.id)} style={{ ...secondaryButton, padding: "0.3rem 0.7rem", fontSize: "0.75rem" }}>
                Delete
              </button>
            </div>
            <div style={{ fontSize: "0.8rem", color: "var(--muted, #64748b)", marginBottom: "0.5rem", wordBreak: "break-all" }}>
              {c.method} {c.url} {c.hasApiKey ? "· has key" : ""}
            </div>
            <div style={{ display: "flex", gap: "0.5rem", marginBottom: "0.5rem" }}>
              <input
                placeholder="Query"
                value={runningId === c.id ? runQuery : ""}
                onChange={(e) => {
                  setRunningId(c.id);
                  setRunQuery(e.target.value);
                }}
                style={{ ...input, flex: 1 }}
              />
              <button type="button" onClick={() => handleRun(c.id)} style={{ ...button, padding: "0.4rem 0.8rem" }}>
                Run
              </button>
            </div>
            {runningId === c.id && runError && <p style={{ color: "#c00", fontSize: "0.8rem" }}>{runError}</p>}
            {runningId === c.id && rows.length > 0 && (
              <div style={{ display: "grid", gap: "0.5rem" }}>
                <button
                  type="button"
                  onClick={() => downloadCsv(rows, `${c.name}-results.csv`)}
                  style={{ ...secondaryButton, padding: "0.3rem 0.7rem", fontSize: "0.75rem", justifySelf: "start" }}
                >
                  Export CSV
                </button>
                <ResultsTable rows={rows} />
              </div>
            )}
          </div>
        ))}
      </div>
    </DashboardShell>
  );
}
