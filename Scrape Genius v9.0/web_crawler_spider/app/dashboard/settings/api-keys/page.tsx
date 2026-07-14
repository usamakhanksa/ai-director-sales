"use client";

import { useEffect, useState, type FormEvent } from "react";

import DashboardShell from "@/components/DashboardShell";
import { getToken } from "@/lib/client-auth";
import { input, label, button, secondaryButton, errorText } from "@/lib/ui-styles";

interface ApiKeyRow {
  id: number;
  googleApiKey: string;
  searchEngineId: string;
  dailyLimit: number;
  usedToday: number;
  isActive: boolean;
  createdAt: string;
}

function authHeaders() {
  return { Authorization: `Bearer ${getToken()}` };
}

export default function ApiKeysPage() {
  const [keys, setKeys] = useState<ApiKeyRow[] | null>(null);
  const [error, setError] = useState<string | null>(null);

  const [googleApiKey, setGoogleApiKey] = useState("");
  const [searchEngineId, setSearchEngineId] = useState("");
  const [dailyLimit, setDailyLimit] = useState("100");
  const [saving, setSaving] = useState(false);

  async function loadKeys() {
    try {
      const res = await fetch("/api/keys", { headers: authHeaders() });
      const json = await res.json();
      if (json.success) setKeys(json.data);
      else setError(json.error);
    } catch {
      setError("Could not load API keys");
    }
  }

  useEffect(() => {
    loadKeys();
  }, []);

  async function handleCreate(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setSaving(true);
    try {
      const res = await fetch("/api/keys", {
        method: "POST",
        headers: { "Content-Type": "application/json", ...authHeaders() },
        body: JSON.stringify({ googleApiKey, searchEngineId, dailyLimit: Number(dailyLimit) || 100 }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        setError(json.error || "Failed to save key");
      } else {
        setGoogleApiKey("");
        setSearchEngineId("");
        setDailyLimit("100");
        await loadKeys();
      }
    } catch {
      setError("Network error while saving key");
    } finally {
      setSaving(false);
    }
  }

  async function toggleActive(k: ApiKeyRow) {
    await fetch(`/api/keys/${k.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json", ...authHeaders() },
      body: JSON.stringify({ isActive: !k.isActive }),
    });
    await loadKeys();
  }

  async function handleDelete(id: number) {
    await fetch(`/api/keys/${id}`, { method: "DELETE", headers: authHeaders() });
    await loadKeys();
  }

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>Google Custom Search API Keys</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>
        The Google Search Scraper and Google Maps tools charge usage against these keys. Add a Google Custom Search
        API key and Search Engine ID (cx) here — when one key runs out of daily quota, ScrapeGenius automatically
        rotates to the next key with headroom.
      </p>

      {error && <p style={errorText}>{error}</p>}

      <form onSubmit={handleCreate} style={{ display: "grid", gap: "0.6rem", maxWidth: 480, marginBottom: "2rem" }}>
        <label style={label}>
          Google API key
          <input value={googleApiKey} onChange={(e) => setGoogleApiKey(e.target.value)} required style={input} />
        </label>
        <label style={label}>
          Search Engine ID (cx)
          <input value={searchEngineId} onChange={(e) => setSearchEngineId(e.target.value)} required style={input} />
        </label>
        <label style={label}>
          Daily quota
          <input
            type="number"
            min={1}
            value={dailyLimit}
            onChange={(e) => setDailyLimit(e.target.value)}
            style={input}
          />
        </label>
        <button type="submit" disabled={saving} style={button}>
          {saving ? "Saving…" : "Add key"}
        </button>
      </form>

      <h2 style={{ fontSize: "1.05rem", marginBottom: "0.75rem" }}>Your keys</h2>
      {keys === null && <p>Loading…</p>}
      {keys !== null && keys.length === 0 && (
        <p style={{ color: "var(--muted, #64748b)" }}>
          No keys yet — add one above. Without an active key with quota, Google Search Scraper requests will fail
          with &quot;No active Google Custom Search key with quota left today&quot;.
        </p>
      )}

      <div style={{ display: "grid", gap: "0.75rem" }}>
        {(keys ?? []).map((k) => (
          <div
            key={k.id}
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "center",
              border: "1px solid var(--border, #e5e7eb)",
              borderRadius: 8,
              padding: "0.85rem 1rem",
              opacity: k.isActive ? 1 : 0.55,
            }}
          >
            <div>
              <div style={{ fontFamily: "monospace" }}>{k.googleApiKey}</div>
              <div style={{ fontSize: "0.8rem", color: "var(--muted, #64748b)" }}>
                cx: {k.searchEngineId} · {k.usedToday}/{k.dailyLimit} used today
              </div>
            </div>
            <div style={{ display: "flex", gap: "0.5rem" }}>
              <button
                type="button"
                onClick={() => toggleActive(k)}
                style={{ ...secondaryButton, padding: "0.3rem 0.7rem", fontSize: "0.75rem" }}
              >
                {k.isActive ? "Disable" : "Enable"}
              </button>
              <button
                type="button"
                onClick={() => handleDelete(k.id)}
                style={{ ...secondaryButton, padding: "0.3rem 0.7rem", fontSize: "0.75rem" }}
              >
                Delete
              </button>
            </div>
          </div>
        ))}
      </div>
    </DashboardShell>
  );
}
