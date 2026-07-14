"use client";

import { useEffect, useRef, useState, type ReactNode } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlay, faSpinner } from "@fortawesome/free-solid-svg-icons";

import { getToken } from "@/lib/client-auth";
import { useTranslation } from "@/lib/i18n";
import { button } from "@/lib/ui-styles";
import MultiKeywordInput from "./MultiKeywordInput";
import LiveJobLog from "./LiveJobLog";
import ExportWizard from "./ExportWizard";
import ResultsGrid from "./ResultsGrid";

const POLL_INTERVAL_MS = 3000;
const TERMINAL_STATUSES = new Set(["DONE", "FAILED", "CANCELLED"]);

interface JobStatus {
  status: string;
  progress: number;
  extractedCount: number;
  errorMessage?: string | null;
}

export interface JobRunnerPanelProps {
  /** POST endpoint that creates the job (e.g. "/api/social/facebook", "/api/classified", "/api/jobs"). */
  createPath: string;
  /** Builds the request body from the current keyword list; lets each module shape its own payload. */
  buildBody: (keywords: string[]) => Record<string, unknown>;
  titleKey: string;
  descriptionKey: string;
  /** Extra form fields rendered above the keyword input (e.g. classified site checkboxes). */
  extraFields?: ReactNode;
  /** Disables Run until this returns true (e.g. "at least one site selected"). Defaults to always-ready. */
  canRun?: () => boolean;
}

export default function JobRunnerPanel({
  createPath,
  buildBody,
  titleKey,
  descriptionKey,
  extraFields,
  canRun,
}: JobRunnerPanelProps) {
  const { t } = useTranslation();
  const [keywords, setKeywords] = useState<string[]>([]);
  const [jobId, setJobId] = useState<number | null>(null);
  const [jobStatus, setJobStatus] = useState<JobStatus | null>(null);
  const [starting, setStarting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);

  function authHeaders() {
    return { "Content-Type": "application/json", Authorization: `Bearer ${getToken()}` };
  }

  async function pollStatus(id: number) {
    const res = await fetch(`/api/jobs/${id}`, { headers: authHeaders() });
    const body = await res.json();
    if (!body.success) return;

    setJobStatus(body.data);
    if (TERMINAL_STATUSES.has(body.data.status)) {
      if (pollRef.current) clearInterval(pollRef.current);
      pollRef.current = null;
    }
  }

  useEffect(() => {
    return () => {
      if (pollRef.current) clearInterval(pollRef.current);
    };
  }, []);

  async function handleRun() {
    if (canRun && !canRun()) {
      setError("Select at least one option before running.");
      return;
    }
    if (keywords.length === 0) {
      setError(t("common.keywords") + " required");
      return;
    }

    setStarting(true);
    setError(null);
    setJobStatus(null);

    try {
      const res = await fetch(createPath, {
        method: "POST",
        headers: authHeaders(),
        body: JSON.stringify(buildBody(keywords)),
      });
      const body = await res.json();
      if (!res.ok || !body.success) {
        setError(body.error || "Failed to start job");
        return;
      }

      const newJobId = body.data.jobId;
      setJobId(newJobId);
      setJobStatus({ status: "QUEUED", progress: 0, extractedCount: 0 });

      if (pollRef.current) clearInterval(pollRef.current);
      pollRef.current = setInterval(() => pollStatus(newJobId), POLL_INTERVAL_MS);
      pollStatus(newJobId);
    } catch {
      setError("Could not reach the server");
    } finally {
      setStarting(false);
    }
  }

  const isRunning = jobStatus && !TERMINAL_STATUSES.has(jobStatus.status);

  return (
    <div style={{ display: "grid", gap: "1.25rem" }}>
      <div>
        <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.35rem" }}>{t(titleKey)}</h1>
        <p style={{ color: "#64748b", fontSize: "0.9rem", margin: 0, maxWidth: 720 }}>{t(descriptionKey)}</p>
      </div>

      <section
        style={{
          background: "var(--surface, #fff)",
          border: "1px solid var(--border, #e5e7eb)",
          borderRadius: 10,
          padding: "1.25rem",
          display: "grid",
          gap: "1rem",
        }}
      >
        {extraFields}
        <MultiKeywordInput keywords={keywords} onChange={setKeywords} />

        <div style={{ display: "flex", alignItems: "center", gap: "0.75rem" }}>
          <button
            type="button"
            style={{ ...button, width: "auto", opacity: starting || isRunning ? 0.6 : 1 }}
            disabled={starting || Boolean(isRunning)}
            onClick={handleRun}
          >
            <FontAwesomeIcon
              icon={starting || isRunning ? faSpinner : faPlay}
              spin={Boolean(starting || isRunning)}
              style={{ marginInlineEnd: "0.4rem" }}
            />
            {isRunning ? t("common.running") : t("common.run")}
          </button>
          {error && <span style={{ color: "#c00", fontSize: "0.85rem" }}>{error}</span>}
        </div>

        {jobStatus && (
          <div style={{ display: "grid", gap: "0.4rem" }}>
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.82rem" }}>
              <span>
                {t("common.status")}: <strong>{t(`status.${jobStatus.status}`)}</strong>
              </span>
              <span>
                {jobStatus.extractedCount} {t("common.results").toLowerCase()}
              </span>
            </div>
            <div style={{ background: "#f1f5f9", borderRadius: 4, height: 8 }}>
              <div
                style={{
                  width: `${Math.max(2, jobStatus.progress)}%`,
                  background: jobStatus.status === "FAILED" ? "#dc2626" : "#2563eb",
                  borderRadius: 4,
                  height: 8,
                  transition: "width 0.3s ease",
                }}
              />
            </div>
            {jobStatus.errorMessage && <span style={{ color: "#c00", fontSize: "0.8rem" }}>{jobStatus.errorMessage}</span>}
          </div>
        )}

        <LiveJobLog jobId={jobId} />
      </section>

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
          <h2 style={{ fontSize: "1rem", margin: 0 }}>{t("common.results")}</h2>
          <ExportWizard jobId={jobId} disabled={!jobId || !jobStatus || !TERMINAL_STATUSES.has(jobStatus.status)} />
        </div>
        {jobId && <ResultsGrid key={`${jobId}-${jobStatus?.status}`} jobId={jobId} />}
      </section>
    </div>
  );
}
