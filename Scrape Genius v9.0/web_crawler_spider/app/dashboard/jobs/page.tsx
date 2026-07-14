"use client";

import { useCallback, useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faBan, faTable } from "@fortawesome/free-solid-svg-icons";

import DashboardShell from "@/components/DashboardShell";
import ResultsGrid from "@/components/ResultsGrid";
import LiveJobLog from "@/components/LiveJobLog";
import ExportWizard from "@/components/ExportWizard";
import { getToken } from "@/lib/client-auth";
import { useTranslation } from "@/lib/i18n";

interface JobRow {
  id: number;
  module: string;
  keywords: string[];
  status: string;
  progress: number;
  extractedCount: number;
  createdAt: string;
}

const POLL_MS = 5000;

export default function JobsPage() {
  const { t } = useTranslation();
  const [jobs, setJobs] = useState<JobRow[] | null>(null);
  const [selectedJobId, setSelectedJobId] = useState<number | null>(null);

  const load = useCallback(async () => {
    const token = getToken();
    if (!token) return;
    const res = await fetch("/api/jobs?limit=100", { headers: { Authorization: `Bearer ${token}` } });
    const body = await res.json();
    if (body.success) setJobs(body.data);
  }, []);

  useEffect(() => {
    load();
    const interval = setInterval(load, POLL_MS);
    return () => clearInterval(interval);
  }, [load]);

  async function cancelJob(id: number) {
    const token = getToken();
    await fetch(`/api/jobs/${id}`, { method: "DELETE", headers: { Authorization: `Bearer ${token}` } });
    load();
  }

  function viewResults(id: number) {
    setSelectedJobId(id);
  }

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.35rem" }}>{t("jobs.title")}</h1>
      <p style={{ color: "#64748b", fontSize: "0.9rem", margin: "0 0 1.25rem", maxWidth: 720 }}>
        {t("jobs.description")}
      </p>

      <div
        style={{
          background: "var(--surface, #fff)",
          border: "1px solid var(--border, #e5e7eb)",
          borderRadius: 10,
          overflowX: "auto",
          marginBottom: "1.5rem",
        }}
      >
        <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "0.85rem" }}>
          <thead>
            <tr style={{ textAlign: "left", borderBottom: "1px solid var(--border, #e5e7eb)" }}>
              <th style={{ padding: "0.7rem 1rem" }}>{t("jobs.module")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("common.keywords")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("common.status")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("common.progress")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("jobs.extracted")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("jobs.started")}</th>
              <th style={{ padding: "0.7rem 1rem" }} />
            </tr>
          </thead>
          <tbody>
            {jobs === null && (
              <tr>
                <td colSpan={7} style={{ padding: "1rem", color: "#64748b" }}>
                  …
                </td>
              </tr>
            )}
            {jobs !== null && jobs.length === 0 && (
              <tr>
                <td colSpan={7} style={{ padding: "1rem", color: "#64748b" }}>
                  {t("common.noResults")}
                </td>
              </tr>
            )}
            {(jobs ?? []).map((job) => (
              <tr key={job.id} style={{ borderBottom: "1px solid var(--border, #e5e7eb)" }}>
                <td style={{ padding: "0.65rem 1rem" }}>{job.module}</td>
                <td style={{ padding: "0.65rem 1rem", maxWidth: 260 }}>
                  {(job.keywords || []).slice(0, 3).join(", ")}
                  {job.keywords?.length > 3 ? ` +${job.keywords.length - 3}` : ""}
                </td>
                <td style={{ padding: "0.65rem 1rem" }}>{t(`status.${job.status}`)}</td>
                <td style={{ padding: "0.65rem 1rem" }}>{job.progress}%</td>
                <td style={{ padding: "0.65rem 1rem" }}>{job.extractedCount}</td>
                <td style={{ padding: "0.65rem 1rem" }}>{new Date(job.createdAt).toLocaleString()}</td>
                <td style={{ padding: "0.65rem 1rem", display: "flex", gap: "0.5rem" }}>
                  <button
                    type="button"
                    title={t("jobs.viewResults")}
                    onClick={() => viewResults(job.id)}
                    style={{ background: "none", border: "none", cursor: "pointer", color: "#2563eb" }}
                  >
                    <FontAwesomeIcon icon={faTable} />
                  </button>
                  {(job.status === "QUEUED" || job.status === "RUNNING") && (
                    <button
                      type="button"
                      title={t("common.cancel")}
                      onClick={() => cancelJob(job.id)}
                      style={{ background: "none", border: "none", cursor: "pointer", color: "#dc2626" }}
                    >
                      <FontAwesomeIcon icon={faBan} />
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {selectedJobId && (
        <section
          style={{
            background: "var(--surface, #fff)",
            border: "1px solid var(--border, #e5e7eb)",
            borderRadius: 10,
            padding: "1.25rem",
            display: "grid",
            gap: "0.85rem",
          }}
        >
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", flexWrap: "wrap", gap: "0.75rem" }}>
            <h2 style={{ fontSize: "1rem", margin: 0 }}>
              {t("common.results")} — {t("jobs.module")} #{selectedJobId}
            </h2>
            <ExportWizard jobId={selectedJobId} />
          </div>
          <LiveJobLog jobId={selectedJobId} />
          <ResultsGrid jobId={selectedJobId} />
        </section>
      )}
    </DashboardShell>
  );
}
