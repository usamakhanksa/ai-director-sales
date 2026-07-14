"use client";

function csvEscape(value: unknown): string {
  const str = value == null ? "" : typeof value === "object" ? JSON.stringify(value) : String(value);
  return `"${str.replace(/"/g, '""')}"`;
}

function cellText(value: unknown): string {
  if (value == null) return "—";
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

/** Renders an array of flat-ish objects as a table, columns = union of keys across all rows. */
export default function ResultsTable({ rows }: { rows: Record<string, unknown>[] }) {
  if (!rows.length) {
    return <p style={{ color: "var(--muted, #64748b)", fontSize: "0.875rem" }}>No results.</p>;
  }
  const headers = Array.from(new Set(rows.flatMap((r) => Object.keys(r))));

  return (
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
          {rows.map((row, i) => (
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
        </tbody>
      </table>
    </div>
  );
}
