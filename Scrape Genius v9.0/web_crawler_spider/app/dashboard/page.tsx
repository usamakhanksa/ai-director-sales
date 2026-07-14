"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faClipboardList,
  faGlobe,
  faEnvelope,
  faPhone,
  faMagnifyingGlass,
  faMapLocationDot,
} from "@fortawesome/free-solid-svg-icons";
import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";

import DashboardShell from "@/components/DashboardShell";
import { clearSession, getToken } from "@/lib/client-auth";

interface DashboardStat {
  title: string;
  records: number;
}

interface ApiKeyRow {
  id: number;
  google_api_key: string;
  daily_limit: number;
  used_today: number;
  remaining_today: number;
}

interface SummaryTile {
  key: string;
  label: string;
  icon: IconDefinition;
  value: number;
}

function buildSummaryTiles(stats: DashboardStat[]): SummaryTile[] {
  const total = stats.reduce((sum, s) => sum + s.records, 0);
  const matching = (needle: string) =>
    stats.filter((s) => s.title.toLowerCase().includes(needle)).reduce((sum, s) => sum + s.records, 0);

  return [
    { key: "total", label: "Total Searched Records", icon: faClipboardList, value: total },
    { key: "website", label: "Total Website Scraped", icon: faGlobe, value: matching("website") },
    { key: "email", label: "Total Email Scraped", icon: faEnvelope, value: matching("email") },
    { key: "phone", label: "Total Phone Scraped", icon: faPhone, value: matching("phone") },
    { key: "google", label: "Total Google Searches", icon: faMagnifyingGlass, value: matching("google") },
    { key: "map", label: "Total Map Searches", icon: faMapLocationDot, value: matching("map") },
  ];
}

export default function DashboardPage() {
  const router = useRouter();
  const [stats, setStats] = useState<DashboardStat[] | null>(null);
  const [apiKeys, setApiKeys] = useState<ApiKeyRow[] | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = getToken();
    if (!token) return;
    const headers = { Authorization: `Bearer ${token}` };

    async function load() {
      try {
        const [statsRes, keysRes] = await Promise.all([
          fetch("/api/dashboard/stats/", { headers }),
          fetch("/api/get_keys/", { headers }),
        ]);

        if (statsRes.status === 401 || keysRes.status === 401) {
          clearSession();
          router.replace("/login");
          return;
        }

        const statsBody = await statsRes.json();
        const keysBody = await keysRes.json();

        setStats(statsBody.success ? statsBody.data : []);
        setApiKeys(keysBody.success ? keysBody.data : []);
      } catch {
        setError("Could not reach the API — is the frontend server running?");
      }
    }

    load();
  }, [router]);

  const loading = stats === null;
  const tiles = buildSummaryTiles(stats ?? []);
  const maxRecords = Math.max(1, ...(stats ?? []).map((s) => s.records));
  const ranked = [...(stats ?? [])].sort((a, b) => b.records - a.records);

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 1.25rem" }}>Dashboard</h1>

      {error && <p style={{ color: "#c00" }}>{error}</p>}

      <div
        style={{
          display: "grid",
          gridTemplateColumns: "repeat(auto-fit, minmax(220px, 1fr))",
          gap: "1rem",
          marginBottom: "1.5rem",
        }}
      >
        {tiles.map((tile) => (
          <div
            key={tile.key}
            style={{
              background: "var(--surface, #fff)",
              border: "1px solid var(--border, #e5e7eb)",
              borderRadius: 10,
              padding: "1.1rem 1.25rem",
              display: "flex",
              alignItems: "center",
              gap: "0.9rem",
            }}
          >
            <div
              style={{
                width: 44,
                height: 44,
                borderRadius: "50%",
                background: "#eef2ff",
                color: "#2563eb",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                fontSize: "1.1rem",
                flexShrink: 0,
              }}
            >
              <FontAwesomeIcon icon={tile.icon} />
            </div>
            <div>
              <div style={{ fontSize: "0.8rem", color: "var(--muted, #64748b)" }}>{tile.label}</div>
              <div style={{ fontSize: "1.3rem", fontWeight: 700 }}>
                {loading ? "…" : tile.value.toLocaleString("en-IN")}
              </div>
            </div>
          </div>
        ))}
      </div>

      <div
        style={{
          display: "grid",
          gridTemplateColumns: "2fr 1fr 1fr",
          gap: "1rem",
        }}
      >
        <section
          style={{
            background: "var(--surface, #fff)",
            border: "1px solid var(--border, #e5e7eb)",
            borderRadius: 10,
            padding: "1.25rem",
          }}
        >
          <h2 style={{ fontSize: "1rem", margin: "0 0 1rem" }}>Scraper Over View</h2>
          {!loading && ranked.length === 0 && (
            <p style={{ color: "var(--muted, #64748b)", fontSize: "0.875rem" }}>
              No scraped records saved yet — this chart fills in as your scrapers post results to{" "}
              <code>/api/saved</code>.
            </p>
          )}
          {ranked.length > 0 && (
            <div style={{ display: "grid", gap: "0.6rem" }}>
              {ranked.map((s) => (
                <div key={s.title} style={{ display: "grid", gap: "0.25rem" }}>
                  <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.8rem" }}>
                    <span>{s.title}</span>
                    <strong>{s.records}</strong>
                  </div>
                  <div style={{ background: "var(--hover, #f1f5f9)", borderRadius: 4, height: 8 }}>
                    <div
                      style={{
                        width: `${Math.max(4, (s.records / maxRecords) * 100)}%`,
                        background: "#2563eb",
                        borderRadius: 4,
                        height: 8,
                      }}
                    />
                  </div>
                </div>
              ))}
            </div>
          )}
        </section>

        <section
          style={{
            background: "var(--surface, #fff)",
            border: "1px solid var(--border, #e5e7eb)",
            borderRadius: 10,
            padding: "1.25rem",
          }}
        >
          <h2 style={{ fontSize: "1rem", margin: "0 0 1rem" }}>Top Used Tools</h2>
          {ranked.length === 0 && (
            <p style={{ color: "var(--muted, #64748b)", fontSize: "0.875rem" }}>Nothing recorded yet.</p>
          )}
          <div style={{ display: "grid", gap: "0.85rem" }}>
            {ranked.slice(0, 5).map((s) => (
              <div key={s.title} style={{ display: "flex", justifyContent: "space-between", fontSize: "0.85rem" }}>
                <span>{s.title}</span>
                <span style={{ color: "var(--muted, #64748b)" }}>{s.records} records</span>
              </div>
            ))}
          </div>
        </section>

        <section
          style={{
            background: "var(--surface, #fff)",
            border: "1px solid var(--border, #e5e7eb)",
            borderRadius: 10,
            padding: "1.25rem",
          }}
        >
          <h2 style={{ fontSize: "1rem", margin: "0 0 1rem" }}>API Keys</h2>
          {apiKeys === null && <p style={{ color: "var(--muted, #64748b)", fontSize: "0.875rem" }}>Loading…</p>}
          {apiKeys !== null && apiKeys.length === 0 && (
            <p style={{ color: "var(--muted, #64748b)", fontSize: "0.875rem" }}>
              No active Google Custom Search keys with quota left today.
            </p>
          )}
          <div style={{ display: "grid", gap: "0.6rem" }}>
            {(apiKeys ?? []).map((k) => (
              <div key={k.id} style={{ fontSize: "0.8rem" }}>
                <div style={{ fontFamily: "monospace" }}>{k.google_api_key}</div>
                <div style={{ color: "var(--muted, #64748b)" }}>
                  {k.remaining_today}/{k.daily_limit} remaining today
                </div>
              </div>
            ))}
          </div>
        </section>
      </div>
    </DashboardShell>
  );
}
