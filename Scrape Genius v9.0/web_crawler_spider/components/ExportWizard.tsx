"use client";

import { useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faDownload, faSpinner } from "@fortawesome/free-solid-svg-icons";

import { getToken } from "@/lib/client-auth";
import { downloadAuthenticated } from "@/lib/download-file";
import { useTranslation } from "@/lib/i18n";
import { button } from "@/lib/ui-styles";

type ExportFormat = "XLSX" | "CSV" | "HTML" | "TXT";
const FORMATS: ExportFormat[] = ["XLSX", "CSV", "HTML", "TXT"];

interface ExportWizardProps {
  jobId: number | null;
  disabled?: boolean;
}

/** Format picker that triggers a server-side export job, then downloads the resulting file. */
export default function ExportWizard({ jobId, disabled }: ExportWizardProps) {
  const { t } = useTranslation();
  const [format, setFormat] = useState<ExportFormat>("XLSX");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleExport() {
    if (!jobId) return;
    setLoading(true);
    setError(null);
    try {
      const token = getToken();
      const res = await fetch("/api/export", {
        method: "POST",
        headers: { "Content-Type": "application/json", Authorization: `Bearer ${token}` },
        body: JSON.stringify({ jobId, format }),
      });
      const body = await res.json();
      if (!res.ok || !body.success) {
        setError(body.error || "Export failed");
        return;
      }
      await downloadAuthenticated(body.data.downloadUrl, `job-${jobId}-export.${format.toLowerCase()}`);
    } catch {
      setError("Could not reach the server");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div style={{ display: "flex", alignItems: "center", gap: "0.6rem", flexWrap: "wrap" }}>
      <select
        value={format}
        onChange={(e) => setFormat(e.target.value as ExportFormat)}
        style={{ padding: "0.45rem 0.6rem", borderRadius: 6, border: "1px solid #ccc", fontSize: "0.85rem" }}
      >
        {FORMATS.map((f) => (
          <option key={f} value={f}>
            {f}
          </option>
        ))}
      </select>
      <button
        type="button"
        style={{ ...button, width: "auto", opacity: disabled || !jobId ? 0.5 : 1 }}
        disabled={disabled || !jobId || loading}
        onClick={handleExport}
      >
        <FontAwesomeIcon icon={loading ? faSpinner : faDownload} spin={loading} style={{ marginInlineEnd: "0.4rem" }} />
        {t("common.export")}
      </button>
      {error && <span style={{ color: "#c00", fontSize: "0.8rem" }}>{error}</span>}
    </div>
  );
}
