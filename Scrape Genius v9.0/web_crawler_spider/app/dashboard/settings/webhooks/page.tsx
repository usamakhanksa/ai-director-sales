"use client";

import { useEffect, useState, type FormEvent } from "react";

import DashboardShell from "@/components/DashboardShell";
import { getToken } from "@/lib/client-auth";
import { input, label, button, errorText } from "@/lib/ui-styles";

const EVENT_TYPES = ["JOB_STARTED", "JOB_COMPLETED", "JOB_FAILED", "EXPORT_READY", "SCRAPE_DATA_AVAILABLE"] as const;

interface WebhookRow {
  id: number;
  url: string;
  events: string[];
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
}

function authHeaders() {
  return { Authorization: `Bearer ${getToken()}` };
}

export default function WebhooksPage() {
  const [webhooks, setWebhooks] = useState<WebhookRow[] | null>(null);
  const [error, setError] = useState<string | null>(null);

  const [url, setUrl] = useState("");
  const [events, setEvents] = useState<string[]>(["JOB_COMPLETED"]);
  const [saving, setSaving] = useState(false);

  async function loadWebhooks() {
    try {
      const res = await fetch("/api/webhooks/register", { headers: authHeaders() });
      const json = await res.json();
      if (json.success) setWebhooks(json.data);
      else setError(json.error);
    } catch {
      setError("Could not load webhooks");
    }
  }

  useEffect(() => {
    loadWebhooks();
  }, []);

  function toggleEvent(event: string) {
    setEvents((prev) => (prev.includes(event) ? prev.filter((e) => e !== event) : [...prev, event]));
  }

  async function handleCreate(e: FormEvent) {
    e.preventDefault();
    setError(null);
    if (events.length === 0) {
      setError("Select at least one event");
      return;
    }
    setSaving(true);
    try {
      const res = await fetch("/api/webhooks/register", {
        method: "POST",
        headers: { "Content-Type": "application/json", ...authHeaders() },
        body: JSON.stringify({ url, events, isActive: true }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        setError(json.error || "Failed to save webhook");
      } else {
        setUrl("");
        setEvents(["JOB_COMPLETED"]);
        await loadWebhooks();
      }
    } catch {
      setError("Network error while saving webhook");
    } finally {
      setSaving(false);
    }
  }

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>Webhooks</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>
        Register a URL to receive an HTTP POST notification when job or export events happen — useful for
        integrating ScrapeGenius with your own systems.
      </p>

      {error && <p style={errorText}>{error}</p>}

      <form onSubmit={handleCreate} style={{ display: "grid", gap: "0.6rem", maxWidth: 480, marginBottom: "2rem" }}>
        <label style={label}>
          Webhook URL
          <input
            value={url}
            onChange={(e) => setUrl(e.target.value)}
            required
            type="url"
            placeholder="https://your-app.com/webhooks/scrapegenius"
            style={input}
          />
        </label>
        <div>
          <div style={{ fontSize: "0.85rem", marginBottom: "0.4rem" }}>Events</div>
          <div style={{ display: "grid", gap: "0.3rem" }}>
            {EVENT_TYPES.map((event) => (
              <label key={event} style={{ display: "flex", alignItems: "center", gap: "0.5rem", fontSize: "0.85rem" }}>
                <input type="checkbox" checked={events.includes(event)} onChange={() => toggleEvent(event)} />
                {event}
              </label>
            ))}
          </div>
        </div>
        <button type="submit" disabled={saving} style={button}>
          {saving ? "Saving…" : "Register webhook"}
        </button>
      </form>

      <h2 style={{ fontSize: "1.05rem", marginBottom: "0.75rem" }}>Your webhooks</h2>
      {webhooks === null && <p>Loading…</p>}
      {webhooks !== null && webhooks.length === 0 && (
        <p style={{ color: "var(--muted, #64748b)" }}>No webhooks registered yet — add one above.</p>
      )}

      <div style={{ display: "grid", gap: "0.75rem" }}>
        {(webhooks ?? []).map((w) => (
          <div
            key={w.id}
            style={{
              border: "1px solid var(--border, #e5e7eb)",
              borderRadius: 8,
              padding: "0.85rem 1rem",
              opacity: w.isActive ? 1 : 0.55,
            }}
          >
            <div style={{ fontFamily: "monospace", wordBreak: "break-all" }}>{w.url}</div>
            <div style={{ fontSize: "0.8rem", color: "var(--muted, #64748b)" }}>
              {w.events.join(", ")} · {w.isActive ? "Active" : "Inactive"}
            </div>
          </div>
        ))}
      </div>
    </DashboardShell>
  );
}
