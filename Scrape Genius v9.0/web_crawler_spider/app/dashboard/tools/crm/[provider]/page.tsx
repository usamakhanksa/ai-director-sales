"use client";

import { useEffect, useState, type FormEvent } from "react";
import { useParams } from "next/navigation";

import DashboardShell from "@/components/DashboardShell";
import ResultsTable from "@/components/ResultsTable";
import { getToken } from "@/lib/client-auth";
import { input, label, button } from "@/lib/ui-styles";

const TITLES: Record<string, string> = { justdial: "Justdial CRM", indiamart: "IndiaMart CRM" };

function authHeaders() {
  return { Authorization: `Bearer ${getToken()}` };
}

export default function CrmConnectionPage() {
  const params = useParams<{ provider: string }>();
  const provider = params?.provider ?? "";
  const title = TITLES[provider] ?? "CRM Connection";

  const [status, setStatus] = useState<any>(null);
  const [loginId, setLoginId] = useState("");
  const [secret, setSecret] = useState("");
  const [saving, setSaving] = useState(false);
  const [syncing, setSyncing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [syncResult, setSyncResult] = useState<any>(null);

  async function loadStatus() {
    try {
      const res = await fetch(`/api/crm/${provider}/`, { headers: authHeaders() });
      const json = await res.json();
      if (json.success) setStatus(json.data);
    } catch {
      setError("Could not load connection status");
    }
  }

  useEffect(() => {
    if (provider) loadStatus();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [provider]);

  async function handleSave(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setSaving(true);
    try {
      const res = await fetch(`/api/crm/${provider}/`, {
        method: "POST",
        headers: { "Content-Type": "application/json", ...authHeaders() },
        body: JSON.stringify({ loginId, secret }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) setError(json.error || "Failed to save connection");
      else {
        setSecret("");
        await loadStatus();
      }
    } catch {
      setError("Network error while saving connection");
    } finally {
      setSaving(false);
    }
  }

  async function handleDisconnect() {
    await fetch(`/api/crm/${provider}/`, { method: "DELETE", headers: authHeaders() });
    setStatus({ connected: false, provider: provider.toUpperCase() });
    setSyncResult(null);
  }

  async function handleSync() {
    setSyncing(true);
    setError(null);
    setSyncResult(null);
    try {
      const res = await fetch(`/api/crm/${provider}/sync/`, {
        method: "POST",
        headers: authHeaders(),
      });
      const json = await res.json();
      if (!res.ok || !json.success) setError(json.error || "Sync failed");
      else {
        setSyncResult(json.data);
        await loadStatus();
      }
    } catch {
      setError("Network error while syncing");
    } finally {
      setSyncing(false);
    }
  }

  const rows: Record<string, unknown>[] = Array.isArray(syncResult?.results) ? syncResult.results : [];

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>{title}</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>
        Connect your own {title.replace(" CRM", "")} seller account so ScrapeGenius can pull in your enquiries
        without an official API integration. This automates logging into YOUR OWN dashboard — it does not scrape
        other people's accounts.
      </p>

      {error && <p style={{ color: "#c00" }}>{error}</p>}

      {status?.connected ? (
        <div style={{ marginBottom: "1.5rem" }}>
          <p style={{ fontSize: "0.875rem" }}>
            Connected as <strong>{status.loginId}</strong>
            {status.lastSyncedAt && ` · last synced ${new Date(status.lastSyncedAt).toLocaleString()}`}
          </p>
          {status.lastStatus && (
            <p style={{ fontSize: "0.8rem", color: "var(--muted, #64748b)" }}>{status.lastStatus}</p>
          )}
          <div style={{ display: "flex", gap: "0.75rem", marginTop: "0.5rem" }}>
            <button type="button" onClick={handleSync} disabled={syncing} style={button}>
              {syncing ? "Syncing…" : "Sync now"}
            </button>
            <button type="button" onClick={handleDisconnect} style={{ ...button, background: "#eee", color: "#1a1a1a" }}>
              Disconnect
            </button>
          </div>
        </div>
      ) : (
        <form onSubmit={handleSave} style={{ display: "grid", gap: "0.75rem", maxWidth: 420, marginBottom: "1.5rem" }}>
          <label style={label}>
            Login ID (mobile/email)
            <input value={loginId} onChange={(e) => setLoginId(e.target.value)} required style={input} />
          </label>
          <label style={label}>
            Password
            <input type="password" value={secret} onChange={(e) => setSecret(e.target.value)} required style={input} />
          </label>
          <button type="submit" disabled={saving} style={button}>
            {saving ? "Saving…" : "Connect"}
          </button>
        </form>
      )}

      {rows.length > 0 && <ResultsTable rows={rows} />}
    </DashboardShell>
  );
}
