"use client";

import { useMemo, useState } from "react";

function csvEscape(value: unknown): string {
  const str = value == null ? "" : typeof value === "object" ? JSON.stringify(value) : String(value);
  return `"${str.replace(/"/g, '""')}"`;
}

function cellText(value: unknown): string {
  if (value == null) return "—";
  if (Array.isArray(value)) return value.join(", ") || "—";
  if (typeof value === "object") return JSON.stringify(value);
  return String(value);
}

export function rowsToCsv(rows: Record<string, unknown>[]): string {
  if (!rows.length) return "";
  const headers = Array.from(new Set(rows.flatMap((r) => Object.keys(r))));
  return [headers.join(","), ...rows.map((r) => headers.map((h) => csvEscape(r[h])).join(","))].join("\n");
}

export function downloadCsv(rows: Record<string, unknown>[], filename: string) {
  const csv = rowsToCsv(rows);
  if (!csv) return;
  const blob = new Blob([csv], { type: "text/csv" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}

const CONTACT_KEYS = ["phone", "email", "website", "allPhones", "allEmails", "allWebsites"];

function hasContactValue(row: Record<string, unknown>, key: string): boolean {
  const value = row[key];
  if (Array.isArray(value)) return value.length > 0;
  return value != null && value !== "";
}

const PAGE_SIZE_OPTIONS = [25, 50, 100, 250];

/** Renders an array of flat-ish objects as a table, columns = union of keys across all rows. */
export default function ResultsTable({ rows }: { rows: Record<string, unknown>[] }) {
  const [search, setSearch] = useState("");
  const [contactFilter, setContactFilter] = useState<"all" | "phone" | "email" | "website">("all");
  const [pageSize, setPageSize] = useState<number>(PAGE_SIZE_OPTIONS[0]);
  const [page, setPage] = useState(0);
  const [showAll, setShowAll] = useState(false);

  const headers = useMemo(() => Array.from(new Set(rows.flatMap((r) => Object.keys(r)))), [rows]);

  const filtered = useMemo(() => {
    let result = rows;

    if (contactFilter === "phone") result = result.filter((r) => hasContactValue(r, "phone") || hasContactValue(r, "allPhones"));
    else if (contactFilter === "email") result = result.filter((r) => hasContactValue(r, "email") || hasContactValue(r, "allEmails"));
    else if (contactFilter === "website") result = result.filter((r) => hasContactValue(r, "website") || hasContactValue(r, "allWebsites"));

    const q = search.trim().toLowerCase();
    if (q) {
      result = result.filter((r) => headers.some((h) => cellText(r[h]).toLowerCase().includes(q)));
    }

    return result;
  }, [rows, headers, search, contactFilter]);

  const totalPages = showAll ? 1 : Math.max(1, Math.ceil(filtered.length / pageSize));
  const clampedPage = Math.min(page, totalPages - 1);
  const pageRows = showAll ? filtered : filtered.slice(clampedPage * pageSize, clampedPage * pageSize + pageSize);

  if (!rows.length) {
    return <p style={{ color: "var(--muted, #64748b)", fontSize: "0.875rem" }}>No results.</p>;
  }

  const contactCounts = {
    phone: rows.filter((r) => hasContactValue(r, "phone") || hasContactValue(r, "allPhones")).length,
    email: rows.filter((r) => hasContactValue(r, "email") || hasContactValue(r, "allEmails")).length,
    website: rows.filter((r) => hasContactValue(r, "website") || hasContactValue(r, "allWebsites")).length,
  };

  return (
    <div style={{ display: "grid", gap: "0.6rem" }}>
      <div style={{ display: "flex", gap: "0.6rem", flexWrap: "wrap", alignItems: "center" }}>
        <input
          value={search}
          onChange={(e) => {
            setSearch(e.target.value);
            setPage(0);
          }}
          placeholder="Filter results…"
          style={{
            flex: "1 1 220px",
            padding: "0.4rem 0.6rem",
            fontSize: "0.8rem",
            border: "1px solid var(--border, #e5e7eb)",
            borderRadius: 6,
          }}
        />
        <select
          value={contactFilter}
          onChange={(e) => {
            setContactFilter(e.target.value as typeof contactFilter);
            setPage(0);
          }}
          style={{ padding: "0.4rem 0.6rem", fontSize: "0.8rem", border: "1px solid var(--border, #e5e7eb)", borderRadius: 6 }}
        >
          <option value="all">All rows ({rows.length})</option>
          <option value="phone">Has mobile number ({contactCounts.phone})</option>
          <option value="email">Has email ({contactCounts.email})</option>
          <option value="website">Has website link ({contactCounts.website})</option>
        </select>
      </div>

      <div style={{ overflowX: "auto", border: "1px solid var(--border, #e5e7eb)", borderRadius: 8 }}>
        <table style={{ borderCollapse: "collapse", width: "100%", fontSize: "0.8rem" }}>
          <thead>
            <tr>
              {headers.map((h) => (
                <th
                  key={h}
                  style={{
                    textAlign: "left",
                    padding: "0.5rem 0.75rem",
                    borderBottom: "1px solid var(--border, #e5e7eb)",
                    background: "var(--hover, #f8fafc)",
                    whiteSpace: "nowrap",
                  }}
                >
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {pageRows.map((row, i) => (
              <tr key={i}>
                {headers.map((h) => (
                  <td
                    key={h}
                    style={{
                      padding: "0.5rem 0.75rem",
                      borderBottom: "1px solid var(--border, #f1f5f9)",
                      maxWidth: 320,
                      overflow: "hidden",
                      textOverflow: "ellipsis",
                      whiteSpace: "nowrap",
                    }}
                    title={cellText(row[h])}
                  >
                    {cellText(row[h])}
                  </td>
                ))}
              </tr>
            ))}
            {pageRows.length === 0 && (
              <tr>
                <td colSpan={headers.length} style={{ padding: "0.75rem", color: "var(--muted, #64748b)" }}>
                  No results match this filter.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <div style={{ display: "flex", gap: "0.75rem", flexWrap: "wrap", alignItems: "center", fontSize: "0.8rem" }}>
        <span style={{ color: "var(--muted, #64748b)" }}>
          {filtered.length} of {rows.length} result{rows.length === 1 ? "" : "s"}
        </span>

        <label style={{ display: "flex", alignItems: "center", gap: "0.3rem" }}>
          <input
            type="checkbox"
            checked={showAll}
            onChange={(e) => {
              setShowAll(e.target.checked);
              setPage(0);
            }}
          />
          Show all (no pagination)
        </label>

        {!showAll && (
          <>
            <label style={{ display: "flex", alignItems: "center", gap: "0.3rem" }}>
              Rows per page
              <select
                value={pageSize}
                onChange={(e) => {
                  setPageSize(Number(e.target.value));
                  setPage(0);
                }}
                style={{ padding: "0.25rem 0.4rem", border: "1px solid var(--border, #e5e7eb)", borderRadius: 6 }}
              >
                {PAGE_SIZE_OPTIONS.map((n) => (
                  <option key={n} value={n}>
                    {n}
                  </option>
                ))}
              </select>
            </label>

            <div style={{ display: "flex", alignItems: "center", gap: "0.4rem" }}>
              <button
                type="button"
                onClick={() => setPage((p) => Math.max(0, p - 1))}
                disabled={clampedPage === 0}
                style={{ padding: "0.25rem 0.6rem", borderRadius: 6, border: "1px solid var(--border, #e5e7eb)" }}
              >
                Prev
              </button>
              <span>
                Page {clampedPage + 1} of {totalPages}
              </span>
              <button
                type="button"
                onClick={() => setPage((p) => Math.min(totalPages - 1, p + 1))}
                disabled={clampedPage >= totalPages - 1}
                style={{ padding: "0.25rem 0.6rem", borderRadius: 6, border: "1px solid var(--border, #e5e7eb)" }}
              >
                Next
              </button>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
