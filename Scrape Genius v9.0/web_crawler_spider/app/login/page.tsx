"use client";

import { useState, type FormEvent } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";

import { saveSession } from "@/lib/client-auth";
import { page, label, input, button, errorText } from "@/lib/ui-styles";

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      const res = await fetch("/api/auth/login/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const body = await res.json();

      if (!res.ok || !body.success) {
        setError(body.error || "Login failed");
        return;
      }

      saveSession(body.data.token, body.data.user);
      router.push("/dashboard");
    } catch {
      setError("Network error — is the frontend server running?");
    } finally {
      setLoading(false);
    }
  }

  return (
    <main style={page}>
      <h1 style={{ fontSize: "1.5rem", marginBottom: "1.5rem" }}>Log in</h1>
      <form onSubmit={handleSubmit} style={{ display: "flex", flexDirection: "column", gap: "0.75rem" }}>
        <label style={label}>
          Email
          <input
            type="email"
            required
            autoComplete="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            style={input}
          />
        </label>
        <label style={label}>
          Password
          <input
            type="password"
            required
            autoComplete="current-password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            style={input}
          />
        </label>
        {error && <p style={errorText}>{error}</p>}
        <button type="submit" disabled={loading} style={button}>
          {loading ? "Logging in…" : "Log in"}
        </button>
      </form>
      <p style={{ marginTop: "1.5rem", color: "#555", fontSize: "0.875rem" }}>
        No account? <Link href="/signup">Sign up</Link>
      </p>
    </main>
  );
}
