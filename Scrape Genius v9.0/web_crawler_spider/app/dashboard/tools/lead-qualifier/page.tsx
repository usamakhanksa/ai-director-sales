"use client";

import { useState, type FormEvent } from "react";

import DashboardShell from "@/components/DashboardShell";
import { getToken } from "@/lib/client-auth";
import { input, label, button, errorText } from "@/lib/ui-styles";

interface ClassifyResult {
  isLead: boolean;
  label: "LEAD" | "NOT_LEAD";
  reason?: string;
}

interface JobClassifyResult {
  jobId: number;
  classifiedCount: number;
  results: Array<{ id: number } & ClassifyResult>;
}

function authHeaders() {
  return { "Content-Type": "application/json", Authorization: `Bearer ${getToken()}` };
}

const DEFAULT_PRODUCT = "hotel management software / PMS";

export default function LeadQualifierPage() {
  const [product, setProduct] = useState(DEFAULT_PRODUCT);
  const [adText, setAdText] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [result, setResult] = useState<ClassifyResult | null>(null);

  const [jobId, setJobId] = useState("");
  const [jobLoading, setJobLoading] = useState(false);
  const [jobError, setJobError] = useState<string | null>(null);
  const [jobResult, setJobResult] = useState<JobClassifyResult | null>(null);

  async function handleClassify(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setResult(null);
    if (!adText.trim()) {
      setError("Paste the ad text to classify");
      return;
    }
    setLoading(true);
    try {
      const res = await fetch("/api/lead-qualifier", {
        method: "POST",
        headers: authHeaders(),
        body: JSON.stringify({ mode: "classify", text: adText, product }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        setError(json.error || "Classification failed");
      } else {
        setResult(json.data);
      }
    } catch {
      setError("Network error while classifying");
    } finally {
      setLoading(false);
    }
  }

  async function handleClassifyJob(e: FormEvent) {
    e.preventDefault();
    setJobError(null);
    setJobResult(null);
    const idNum = Number(jobId);
    if (!idNum) {
      setJobError("Enter a valid job ID (from Job Queue)");
      return;
    }
    setJobLoading(true);
    try {
      const res = await fetch("/api/lead-qualifier", {
        method: "POST",
        headers: authHeaders(),
        body: JSON.stringify({ mode: "classify-job", jobId: idNum, product }),
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        setJobError(json.error || "Bulk classification failed");
      } else {
        setJobResult(json.data);
      }
    } catch {
      setJobError("Network error while classifying job results");
    } finally {
      setJobLoading(false);
    }
  }

  const leadCount = jobResult?.results.filter((r) => r.isLead).length ?? 0;

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>AI Lead Qualifier</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.5rem", maxWidth: 680 }}>
        Runs each scraped classified/Haraj ad through an LLM prompt to decide whether the poster is actually
        <strong> requesting</strong> what you sell, versus selling something else or noise — so your sales team only
        sees qualified leads.
      </p>

      <div style={{ display: "grid", gap: "0.4rem", maxWidth: 680, marginBottom: "1.5rem" }}>
        <label style={label}>
          What you sell (used in the classification prompt)
          <input value={product} onChange={(e) => setProduct(e.target.value)} style={input} />
        </label>
      </div>

      <div
        style={{
          display: "grid",
          gridTemplateColumns: "minmax(0, 1fr) minmax(0, 1fr)",
          gap: "1.5rem",
          maxWidth: 1080,
        }}
      >
        {/* Single ad classification */}
        <section style={{ border: "1px solid var(--border, #e5e7eb)", borderRadius: 10, padding: "1.1rem" }}>
          <h2 style={{ fontSize: "1rem", margin: "0 0 0.75rem" }}>Classify a single ad</h2>
          {error && <p style={errorText}>{error}</p>}
          <form onSubmit={handleClassify} style={{ display: "grid", gap: "0.6rem" }}>
            <label style={label}>
              Ad text (Arabic or English)
              <textarea
                value={adText}
                onChange={(e) => setAdText(e.target.value)}
                rows={7}
                placeholder="أحتاج نظام إدارة فنادق لفندقي، من يرشح برنامجًا جيدًا؟"
                style={input}
              />
            </label>
            <button type="submit" disabled={loading} style={button}>
              {loading ? "Classifying…" : "Classify"}
            </button>
          </form>

          {result && (
            <div
              style={{
                marginTop: "1rem",
                borderRadius: 8,
                padding: "0.75rem 1rem",
                background: result.isLead ? "#ecfdf5" : "#f8fafc",
                border: `1px solid ${result.isLead ? "#a7f3d0" : "#e2e8f0"}`,
              }}
            >
              <strong style={{ color: result.isLead ? "#059669" : "#64748b" }}>
                {result.isLead ? "✓ LEAD" : "✕ NOT A LEAD"}
              </strong>
              {result.reason && (
                <p style={{ margin: "0.35rem 0 0", fontSize: "0.8rem", color: "var(--muted, #64748b)" }}>
                  {result.reason}
                </p>
              )}
            </div>
          )}
        </section>

        {/* Bulk job classification */}
        <section style={{ border: "1px solid var(--border, #e5e7eb)", borderRadius: 10, padding: "1.1rem" }}>
          <h2 style={{ fontSize: "1rem", margin: "0 0 0.75rem" }}>Classify a scraped job's results</h2>
          {jobError && <p style={errorText}>{jobError}</p>}
          <form onSubmit={handleClassifyJob} style={{ display: "grid", gap: "0.6rem" }}>
            <label style={label}>
              Job ID (from Classified & Haraj scraper, see Job Queue)
              <input value={jobId} onChange={(e) => setJobId(e.target.value)} placeholder="e.g. 42" style={input} />
            </label>
            <button type="submit" disabled={jobLoading} style={button}>
              {jobLoading ? "Classifying…" : "Classify unlabeled results"}
            </button>
          </form>

          {jobResult && (
            <div style={{ marginTop: "1rem" }}>
              <p style={{ margin: "0 0 0.5rem", fontSize: "0.85rem" }}>
                Classified {jobResult.classifiedCount} result(s) —{" "}
                <strong style={{ color: "#059669" }}>{leadCount} lead(s)</strong>,{" "}
                {jobResult.classifiedCount - leadCount} not a lead.
              </p>
              <div style={{ maxHeight: 320, overflowY: "auto", display: "grid", gap: "0.35rem" }}>
                {jobResult.results.map((r) => (
                  <div
                    key={r.id}
                    style={{
                      display: "flex",
                      justifyContent: "space-between",
                      fontSize: "0.8rem",
                      padding: "0.35rem 0.6rem",
                      borderRadius: 6,
                      background: r.isLead ? "#ecfdf5" : "#f8fafc",
                    }}
                  >
                    <span>Result #{r.id}</span>
                    <strong style={{ color: r.isLead ? "#059669" : "#94a3b8" }}>{r.label}</strong>
                  </div>
                ))}
              </div>
            </div>
          )}
        </section>
      </div>
    </DashboardShell>
  );
}
