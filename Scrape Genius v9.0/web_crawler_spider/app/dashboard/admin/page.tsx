"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";

import DashboardShell from "@/components/DashboardShell";
import { getToken, getUser } from "@/lib/client-auth";
import { useTranslation } from "@/lib/i18n";

interface AdminUser {
  id: number;
  name: string;
  email: string;
  role: "ADMIN" | "USER";
  isVerified: boolean;
  createdAt: string;
}

interface PurchaseCode {
  id: number;
  code: string;
  isActive: boolean;
  expiresAt: string | null;
  user: { name: string; email: string } | null;
}

interface UsageSummary {
  totalUsers: number;
  totalRecords: number;
  totalActiveApiKeys: number;
  bySource: { source: string; count: number }[];
}

type Tab = "users" | "codes" | "usage";

function authHeaders() {
  return { "Content-Type": "application/json", Authorization: `Bearer ${getToken()}` };
}

export default function AdminPage() {
  const router = useRouter();
  const { t } = useTranslation();
  const [tab, setTab] = useState<Tab>("users");
  const [allowed, setAllowed] = useState<boolean | null>(null);
  const [users, setUsers] = useState<AdminUser[] | null>(null);
  const [codes, setCodes] = useState<PurchaseCode[] | null>(null);
  const [usage, setUsage] = useState<UsageSummary | null>(null);

  useEffect(() => {
    const storedUser = getUser();
    if (!storedUser || storedUser.role !== "ADMIN") {
      router.replace("/dashboard");
      return;
    }
    setAllowed(true);
  }, [router]);

  useEffect(() => {
    if (!allowed) return;
    if (tab === "users" && users === null) {
      fetch("/api/admin/users", { headers: authHeaders() })
        .then((r) => r.json())
        .then((b) => setUsers(b.success ? b.data : []));
    }
    if (tab === "codes" && codes === null) {
      fetch("/api/admin/purchase-codes", { headers: authHeaders() })
        .then((r) => r.json())
        .then((b) => setCodes(b.success ? b.data : []));
    }
    if (tab === "usage" && usage === null) {
      fetch("/api/admin/usage", { headers: authHeaders() })
        .then((r) => r.json())
        .then((b) => setUsage(b.success ? b.data : null));
    }
  }, [allowed, tab, users, codes, usage]);

  async function toggleRole(u: AdminUser) {
    const nextRole = u.role === "ADMIN" ? "USER" : "ADMIN";
    const res = await fetch(`/api/admin/users/${u.id}`, {
      method: "PATCH",
      headers: authHeaders(),
      body: JSON.stringify({ role: nextRole }),
    });
    if (res.ok) setUsers((prev) => prev!.map((x) => (x.id === u.id ? { ...x, role: nextRole } : x)));
  }

  async function generateCode() {
    const res = await fetch("/api/admin/purchase-codes", { method: "POST", headers: authHeaders(), body: "{}" });
    const body = await res.json();
    if (body.success) setCodes((prev) => [{ ...body.data, user: null }, ...(prev ?? [])]);
  }

  if (allowed === null) return null;

  const tabs: { key: Tab; labelKey: string }[] = [
    { key: "users", labelKey: "admin.users" },
    { key: "codes", labelKey: "admin.purchaseCodes" },
    { key: "usage", labelKey: "admin.usage" },
  ];

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 1rem" }}>{t("admin.title")}</h1>

      <div style={{ display: "flex", gap: "0.5rem", marginBottom: "1.25rem" }}>
        {tabs.map((tb) => (
          <button
            key={tb.key}
            type="button"
            onClick={() => setTab(tb.key)}
            style={{
              padding: "0.5rem 1rem",
              borderRadius: 8,
              border: "1px solid var(--border, #e5e7eb)",
              background: tab === tb.key ? "#2563eb" : "var(--surface, #fff)",
              color: tab === tb.key ? "#fff" : "inherit",
              cursor: "pointer",
              fontSize: "0.85rem",
            }}
          >
            {t(tb.labelKey)}
          </button>
        ))}
      </div>

      {tab === "users" && (
        <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "0.85rem", background: "var(--surface, #fff)", border: "1px solid var(--border, #e5e7eb)", borderRadius: 10 }}>
          <thead>
            <tr style={{ textAlign: "left", borderBottom: "1px solid var(--border, #e5e7eb)" }}>
              <th style={{ padding: "0.7rem 1rem" }}>Name</th>
              <th style={{ padding: "0.7rem 1rem" }}>Email</th>
              <th style={{ padding: "0.7rem 1rem" }}>Role</th>
              <th style={{ padding: "0.7rem 1rem" }}>Verified</th>
              <th style={{ padding: "0.7rem 1rem" }} />
            </tr>
          </thead>
          <tbody>
            {(users ?? []).map((u) => (
              <tr key={u.id} style={{ borderBottom: "1px solid var(--border, #e5e7eb)" }}>
                <td style={{ padding: "0.6rem 1rem" }}>{u.name}</td>
                <td style={{ padding: "0.6rem 1rem" }}>{u.email}</td>
                <td style={{ padding: "0.6rem 1rem" }}>{u.role}</td>
                <td style={{ padding: "0.6rem 1rem" }}>{u.isVerified ? "Yes" : "No"}</td>
                <td style={{ padding: "0.6rem 1rem" }}>
                  <button
                    type="button"
                    onClick={() => toggleRole(u)}
                    style={{ background: "none", border: "1px solid #cbd5e1", borderRadius: 6, padding: "0.25rem 0.6rem", cursor: "pointer", fontSize: "0.78rem" }}
                  >
                    Make {u.role === "ADMIN" ? "User" : "Admin"}
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}

      {tab === "codes" && (
        <div style={{ display: "grid", gap: "1rem" }}>
          <button
            type="button"
            onClick={generateCode}
            style={{ width: "fit-content", padding: "0.5rem 1rem", background: "#2563eb", color: "#fff", border: "none", borderRadius: 6, cursor: "pointer", fontSize: "0.85rem" }}
          >
            + Generate code
          </button>
          <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "0.85rem", background: "var(--surface, #fff)", border: "1px solid var(--border, #e5e7eb)", borderRadius: 10 }}>
            <thead>
              <tr style={{ textAlign: "left", borderBottom: "1px solid var(--border, #e5e7eb)" }}>
                <th style={{ padding: "0.7rem 1rem" }}>Code</th>
                <th style={{ padding: "0.7rem 1rem" }}>Active</th>
                <th style={{ padding: "0.7rem 1rem" }}>Claimed by</th>
              </tr>
            </thead>
            <tbody>
              {(codes ?? []).map((c) => (
                <tr key={c.id} style={{ borderBottom: "1px solid var(--border, #e5e7eb)" }}>
                  <td style={{ padding: "0.6rem 1rem", fontFamily: "monospace" }}>{c.code}</td>
                  <td style={{ padding: "0.6rem 1rem" }}>{c.isActive ? "Yes" : "No"}</td>
                  <td style={{ padding: "0.6rem 1rem" }}>{c.user ? `${c.user.name} (${c.user.email})` : "—"}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {tab === "usage" && usage && (
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))", gap: "1rem" }}>
          <div style={{ background: "var(--surface, #fff)", border: "1px solid var(--border, #e5e7eb)", borderRadius: 10, padding: "1.1rem" }}>
            <div style={{ fontSize: "0.8rem", color: "#64748b" }}>Total Users</div>
            <div style={{ fontSize: "1.4rem", fontWeight: 700 }}>{usage.totalUsers}</div>
          </div>
          <div style={{ background: "var(--surface, #fff)", border: "1px solid var(--border, #e5e7eb)", borderRadius: 10, padding: "1.1rem" }}>
            <div style={{ fontSize: "0.8rem", color: "#64748b" }}>Total Scraped Records</div>
            <div style={{ fontSize: "1.4rem", fontWeight: 700 }}>{usage.totalRecords}</div>
          </div>
          <div style={{ background: "var(--surface, #fff)", border: "1px solid var(--border, #e5e7eb)", borderRadius: 10, padding: "1.1rem" }}>
            <div style={{ fontSize: "0.8rem", color: "#64748b" }}>Active API Keys</div>
            <div style={{ fontSize: "1.4rem", fontWeight: 700 }}>{usage.totalActiveApiKeys}</div>
          </div>
          <div style={{ gridColumn: "1 / -1", background: "var(--surface, #fff)", border: "1px solid var(--border, #e5e7eb)", borderRadius: 10, padding: "1.1rem" }}>
            <div style={{ fontSize: "0.85rem", fontWeight: 600, marginBottom: "0.6rem" }}>By Source</div>
            {usage.bySource.map((row) => (
              <div key={row.source} style={{ display: "flex", justifyContent: "space-between", fontSize: "0.82rem", padding: "0.25rem 0" }}>
                <span>{row.source}</span>
                <strong>{row.count}</strong>
              </div>
            ))}
          </div>
        </div>
      )}
    </DashboardShell>
  );
}
