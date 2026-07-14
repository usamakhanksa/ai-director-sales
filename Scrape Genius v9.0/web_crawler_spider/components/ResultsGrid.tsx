"use client";

import { useEffect, useMemo, useState } from "react";
import { DataGrid, GridToolbar, type GridColDef, type GridPaginationModel } from "@mui/x-data-grid";

import { getToken } from "@/lib/client-auth";
import { useTranslation } from "@/lib/i18n";

interface ResultsGridProps {
  jobId: number;
}

function dedupeKey(row: Record<string, unknown>): string {
  const { id, job_id, created_at, ...rest } = row;
  return JSON.stringify(rest);
}

/**
 * Sortable/filterable results table (MUI DataGrid) with server-side ajax
 * pagination, a client-side dedupe toggle, and an optional "show all" mode
 * (limit=0 on the API — no page cap) for exports/full review.
 */
export default function ResultsGrid({ jobId }: ResultsGridProps) {
  const { t, dir } = useTranslation();
  const [dedupe, setDedupe] = useState(false);
  const [showAll, setShowAll] = useState(false);
  const [rows, setRows] = useState<Record<string, unknown>[]>([]);
  const [rowCount, setRowCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [paginationModel, setPaginationModel] = useState<GridPaginationModel>({
    page: 0,
    pageSize: 25,
  });

  useEffect(() => {
    let cancelled = false;

    async function load() {
      setLoading(true);
      setError(null);
      try {
        const token = getToken();
        const params = showAll
          ? "limit=0"
          : `limit=${paginationModel.pageSize}&offset=${paginationModel.page * paginationModel.pageSize}`;
        const res = await fetch(`/api/jobs/${jobId}/results?${params}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const body = await res.json();
        if (cancelled) return;
        if (!body.success) throw new Error(body.error || "Unknown error");
        setRows(body.data ?? []);
        setRowCount(Number(body.total ?? body.data?.length ?? 0));
      } catch (err) {
        if (cancelled) return;
        // API unreachable/erroring — fail soft with an empty grid instead of crashing the page
        setRows([]);
        setRowCount(0);
        setError(err instanceof Error ? err.message : "Failed to load results");
      } finally {
        if (!cancelled) setLoading(false);
      }
    }

    load();
    return () => {
      cancelled = true;
    };
  }, [jobId, showAll, paginationModel.page, paginationModel.pageSize]);

  const displayRows = useMemo(() => {
    if (!dedupe) return rows;
    const seen = new Set<string>();
    return rows.filter((row) => {
      const key = dedupeKey(row);
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });
  }, [rows, dedupe]);

  const columns = useMemo<GridColDef[]>(() => {
    const keys = Array.from(new Set(rows.flatMap((r) => Object.keys(r))));
    return keys
      .filter((k) => k !== "id")
      .map((key) => ({
        field: key,
        headerName: key.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()),
        flex: 1,
        minWidth: 140,
        valueGetter: (value: unknown) => (typeof value === "object" && value !== null ? JSON.stringify(value) : value),
      }));
  }, [rows]);

  return (
    <div dir={dir}>
      <div style={{ display: "flex", alignItems: "center", gap: "1.25rem", marginBottom: "0.5rem", flexWrap: "wrap" }}>
        <label style={{ display: "flex", alignItems: "center", gap: "0.4rem", fontSize: "0.82rem" }}>
          <input type="checkbox" checked={dedupe} onChange={(e) => setDedupe(e.target.checked)} />
          {t("common.dedupe")}
        </label>
        <label style={{ display: "flex", alignItems: "center", gap: "0.4rem", fontSize: "0.82rem" }}>
          <input
            type="checkbox"
            checked={showAll}
            onChange={(e) => {
              setShowAll(e.target.checked);
              setPaginationModel({ page: 0, pageSize: paginationModel.pageSize });
            }}
          />
          {t("common.showAll") || "Show all results"}
        </label>
        {!loading && <span style={{ fontSize: "0.8rem", color: "#64748b" }}>{rowCount} total</span>}
      </div>

      {error && (
        <p style={{ color: "#dc2626", fontSize: "0.85rem", marginBottom: "0.5rem" }}>
          {t("common.resultsUnavailable") || "Results are unavailable right now."} ({error})
        </p>
      )}

      {!loading && !error && rows.length === 0 ? (
        <p style={{ color: "#64748b", fontSize: "0.875rem" }}>{t("common.noResults")}</p>
      ) : (
        <div style={{ height: 480, width: "100%" }}>
          <DataGrid
            rows={displayRows.map((r, i) => ({ id: (r.id as number | string | undefined) ?? i, ...r }))}
            columns={columns}
            density="compact"
            loading={loading}
            slots={{ toolbar: GridToolbar }}
            slotProps={{ toolbar: { showQuickFilter: true } }}
            paginationMode={showAll ? "client" : "server"}
            rowCount={showAll ? displayRows.length : rowCount}
            paginationModel={paginationModel}
            onPaginationModelChange={setPaginationModel}
            pageSizeOptions={[10, 25, 50, 100]}
          />
        </div>
      )}
    </div>
  );
}
