"use client";

import { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faDownload } from "@fortawesome/free-solid-svg-icons";

import DashboardShell from "@/components/DashboardShell";
import { getToken } from "@/lib/client-auth";
import { downloadAuthenticated } from "@/lib/download-file";
import { useTranslation } from "@/lib/i18n";

interface ExportRow {
  id: number;
  job_id: number | null;
  format: string;
  row_count: number;
  file_path: string;
  created_at: string;
}

export default function ExportHistoryPage() {
  const { t } = useTranslation();
  const [exports, setExports] = useState<ExportRow[] | null>(null);

  useEffect(() => {
    const token = getToken();
    if (!token) return;
    fetch("/api/export?limit=100", { headers: { Authorization: `Bearer ${token}` } })
      .then((res) => res.json())
      .then((body) => setExports(body.success ? body.data : []));
  }, []);

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.35rem" }}>{t("export.title")}</h1>
      <p style={{ color: "#64748b", fontSize: "0.9rem", margin: "0 0 1.25rem", maxWidth: 720 }}>
        {t("export.description")}
      </p>

      <div
        style={{
          background: "var(--surface, #fff)",
          border: "1px solid var(--border, #e5e7eb)",
          borderRadius: 10,
          overflowX: "auto",
        }}
      >
        <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "0.85rem" }}>
          <thead>
            <tr style={{ textAlign: "left", borderBottom: "1px solid var(--border, #e5e7eb)" }}>
              <th style={{ padding: "0.7rem 1rem" }}>{t("jobs.module")} #</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("common.format")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("export.rows")}</th>
              <th style={{ padding: "0.7rem 1rem" }}>{t("jobs.started")}</th>
              <th style={{ padding: "0.7rem 1rem" }} />
            </tr>
          </thead>
          <tbody>
            {exports === null && (
              <tr>
                <td colSpan={5} style={{ padding: "1rem", color: "#64748b" }}>

                </td>
              </tr>
            )}
            {exports !== null && exports.length === 0 && (
              <tr>
                <td colSpan={5} style={{ padding: "1rem", color: "#64748b" }}>
                  {t("common.noResults")}
                </td>
              </tr>
            )}
            {(exports ?? []).map((exp) => (
              <tr key={exp.id} style={{ borderBottom: "1px solid var(--border, #e5e7eb)" }}>
                <td style={{ padding: "0.65rem 1rem" }}>{exp.job_id ?? "—"}</td>
                <td style={{ padding: "0.65rem 1rem" }}>{exp.format}</td>
                <td style={{ padding: "0.65rem 1rem" }}>{exp.row_count}</td>
                <td style={{ padding: "0.65rem 1rem" }}>{new Date(exp.created_at).toLocaleString()}</td>
                <td style={{ padding: "0.65rem 1rem" }}>
                  <button
                    type="button"
                    onClick={() =>
                      downloadAuthenticated(
                        `/api/export/${exp.id}/download`,
                        `export-${exp.id}.${exp.format.toLowerCase()}`
                      )
                    }
                    style={{ background: "none", border: "none", cursor: "pointer", color: "#2563eb" }}
                    title={t("common.download")}
                  >
                    <FontAwesomeIcon icon={faDownload} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </DashboardShell>
  );
}
