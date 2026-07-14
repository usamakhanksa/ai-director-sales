import Link from "next/link";

import { page, button, secondaryButton } from "@/lib/ui-styles";

const ROUTES = [
  { method: "POST", path: "/api/auth/signup" },
  { method: "POST", path: "/api/auth/login" },
  { method: "GET", path: "/api/get_keys" },
  { method: "POST", path: "/api/update_usage" },
  { method: "GET", path: "/api/dashboard/stats" },
  { method: "POST", path: "/api/saved" },
  { method: "POST", path: "/api/purchase-code/activate" },
];

export default function Home() {
  return (
    <main style={page}>
      <h1 style={{ fontSize: "1.5rem", marginBottom: "0.25rem" }}>ScrapeGenius</h1>
      <p style={{ color: "#555", marginBottom: "1.5rem" }}>
        Log in to view your dashboard, or sign up for a new account.
      </p>
      <div style={{ display: "flex", gap: "0.75rem", marginBottom: "2.5rem" }}>
        <Link href="/login" style={{ ...button, textDecoration: "none", textAlign: "center" }}>
          Log in
        </Link>
        <Link href="/signup" style={{ ...secondaryButton, textDecoration: "none", textAlign: "center" }}>
          Sign up
        </Link>
      </div>

      <p style={{ marginBottom: "2rem" }}>
        <Link href="/features" style={{ color: "#0070f3" }}>
          Browse all scraper &amp; CRM templates →
        </Link>
      </p>

      <h2 style={{ fontSize: "1rem", marginBottom: "0.5rem" }}>API routes</h2>
      <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
        {ROUTES.map((r) => (
          <li key={r.path} style={{ fontFamily: "monospace", padding: "0.25rem 0" }}>
            <span style={{ display: "inline-block", width: 48, color: "#0070f3" }}>{r.method}</span>
            {r.path}
          </li>
        ))}
      </ul>
    </main>
  );
}
