"use client";

import { useState, type FormEvent } from "react";

import DashboardShell from "@/components/DashboardShell";
import { getToken } from "@/lib/client-auth";
import { input, label, button, errorText } from "@/lib/ui-styles";

interface EnrichmentResult {
  leadScore: { score: number; factors: string[]; rating: string };
  sentiment: { overallSentiment: string; painPoints: string[]; severityScore: number };
}

function authHeaders() {
  return { "Content-Type": "application/json", Authorization: `Bearer ${getToken()}` };
}

const ratingColor: Record<string, string> = {
  "Hot Lead": "#dc2626",
  "Warm Lead": "#d97706",
  "Cold Lead": "#2563eb",
};

export default function AiEnrichmentPage() {
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [website, setWebsite] = useState("");
  const [socialLinksText, setSocialLinksText] = useState("");
  const [reviewsText, setReviewsText] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [result, setResult] = useState<EnrichmentResult | null>(null);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setResult(null);
    setLoading(true);

    try {
      let socialLinks: Record<string, string> | undefined;
      if (socialLinksText.trim()) {
        try {
          socialLinks = JSON.parse(socialLinksText);
        } catch {
          setError("Social links must be valid JSON, e.g. {\"facebook\": \"https://facebook.com/acme\"}");
          setLoading(false);
          return;
        }
      }

      const reviews = reviewsText
        .split("\n")
        .map((r) => r.trim())
        .filter(Boolean);

      const res = await fetch("/api/ai-enrichment", {
        method: "POST",
        headers: authHeaders(),
        body: JSON.stringify({
          email: email || undefined,
          phone: phone || undefined,
          website: website || undefined,
          socialLinks,
          reviews,
        }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        setError(json.error || "Enrichment failed");
      } else {
        setResult(json.data);
      }
    } catch {
      setError("Network error while enriching lead");
    } finally {
      setLoading(false);
    }
  }

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>AI Enrichment</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>
        Paste details from any scraped lead — this scores it (Hot/Warm/Cold), flags the reasons why, and mines any
        reviews for pain points you can lead a sales pitch with.
      </p>

      {error && <p style={errorText}>{error}</p>}

      <form onSubmit={handleSubmit} style={{ display: "grid", gap: "0.6rem", maxWidth: 560, marginBottom: "2rem" }}>
        <label style={label}>
          Email
          <input value={email} onChange={(e) => setEmail(e.target.value)} placeholder="owner@gmail.com" style={input} />
        </label>
        <label style={label}>
          Phone
          <input value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="+1 555 0100" style={input} />
        </label>
        <label style={label}>
          Website (leave blank if none found)
          <input value={website} onChange={(e) => setWebsite(e.target.value)} placeholder="https://example.com" style={input} />
        </label>
        <label style={label}>
          Social links (JSON, optional)
          <textarea
            value={socialLinksText}
            onChange={(e) => setSocialLinksText(e.target.value)}
            rows={2}
            placeholder='{"facebook": "https://facebook.com/acme"}'
            style={input}
          />
        </label>
        <label style={label}>
          Reviews (one per line, optional)
          <textarea
            value={reviewsText}
            onChange={(e) => setReviewsText(e.target.value)}
            rows={5}
            placeholder={"Service was slow and rude...\nGreat experience, highly recommend!"}
            style={input}
          />
        </label>
        <button type="submit" disabled={loading} style={button}>
          {loading ? "Analyzing…" : "Enrich lead"}
        </button>
      </form>

      {result && (
        <div style={{ display: "grid", gap: "1rem", maxWidth: 640 }}>
          <div style={{ border: "1px solid var(--border, #e5e7eb)", borderRadius: 8, padding: "1rem" }}>
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "0.5rem" }}>
              <strong>Lead score</strong>
              <span
                style={{
                  fontWeight: 700,
                  color: ratingColor[result.leadScore.rating] ?? "#111",
                }}
              >
                {result.leadScore.rating} — {result.leadScore.score}/100
              </span>
            </div>
            <ul style={{ margin: 0, paddingLeft: "1.1rem", fontSize: "0.85rem" }}>
              {result.leadScore.factors.length === 0 && <li>No scoring signals detected.</li>}
              {result.leadScore.factors.map((f) => (
                <li key={f}>{f}</li>
              ))}
            </ul>
          </div>

          <div style={{ border: "1px solid var(--border, #e5e7eb)", borderRadius: 8, padding: "1rem" }}>
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "0.5rem" }}>
              <strong>Review sentiment</strong>
              <span style={{ fontSize: "0.85rem", textTransform: "capitalize" }}>
                {result.sentiment.overallSentiment} (severity {result.sentiment.severityScore}/10)
              </span>
            </div>
            {result.sentiment.painPoints.length === 0 ? (
              <p style={{ margin: 0, fontSize: "0.85rem", color: "var(--muted, #64748b)" }}>
                No reviews supplied, or no pain points detected.
              </p>
            ) : (
              <ul style={{ margin: 0, paddingLeft: "1.1rem", fontSize: "0.85rem" }}>
                {result.sentiment.painPoints.map((p, i) => (
                  <li key={i}>{p}</li>
                ))}
              </ul>
            )}
          </div>
        </div>
      )}
    </DashboardShell>
  );
}
