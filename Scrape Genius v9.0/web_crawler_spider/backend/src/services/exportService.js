/**
 * Export Service
 * 
 * Generates export files from scraping job results in four formats:
 *   - XLSX (Excel) — separate sheets per keyword, styled with colors
 *   - CSV          — standard comma-separated, UTF-8 BOM for Arabic/Excel compat
 *   - HTML         — styled self-contained HTML report with table
 *   - TXT          — plain text, one record per line
 * 
 * Files are saved to: EXPORT_DIR/{jobId}/{timestamp}/results.{ext}
 * A record is inserted into export_records for download tracking.
 * 
 * Usage:
 *   const { exportResults } = require('./exportService');
 *   const exportId = await exportResults({ jobId, userId, format: 'xlsx' });
 */

"use strict";

const path = require("path");
const fs   = require("fs");
const db   = require("../config/database");

const EXPORT_DIR = path.resolve(process.env.EXPORT_DIR || "./exports");

// ─────────────────────────────────────────────────────────────────────────────
// Result Fetchers (pull data from the appropriate table based on job module)
// ─────────────────────────────────────────────────────────────────────────────

async function fetchResults(jobId) {
  const job = await db("scrape_jobs").where({ id: jobId }).first();
  if (!job) throw new Error(`Job ${jobId} not found`);

  const module = job.module;

  if (["facebook", "linkedin", "twitter"].includes(module)) {
    const rows = await db("social_results").where({ job_id: jobId }).orderBy("keyword");
    return {
      module,
      columns: ["#", "Keyword", "Source", "Name", "Email", "Phone", "Address", "Title", "Description", "Profile URL", "Scraped At"],
      rows: rows.map((r, i) => [
        i + 1, r.keyword, r.source, r.name, r.email, r.phone,
        r.address, r.title, r.description, r.profile_url, formatDate(r.scraped_at),
      ]),
      keywords: [...new Set(rows.map((r) => r.keyword))],
      rawRows: rows,
    };
  }

  if (["haraj", "classified_generic"].includes(module)) {
    const rows = await db("classified_results").where({ job_id: jobId }).orderBy("keyword");
    return {
      module,
      columns: ["#", "Keyword", "Source", "Post Title", "Post Link", "Phone", "Email", "Price", "Location", "Scraped At"],
      rows: rows.map((r, i) => [
        i + 1, r.keyword, r.source, r.post_title, r.post_link,
        r.phone, r.email, r.price, r.location, formatDate(r.scraped_at),
      ]),
      keywords: [...new Set(rows.map((r) => r.keyword))],
      rawRows: rows,
    };
  }

  if (module === "google_maps") {
    const rows = await db("maps_job_results").where({ job_id: jobId }).orderBy("keyword");
    return {
      module,
      columns: ["#", "Keyword", "Business Name", "Phone", "Email", "Address", "Website", "Rating", "Reviews", "Category", "Instagram", "Facebook", "LinkedIn", "Twitter", "Scraped At"],
      rows: rows.map((r, i) => [
        i + 1, r.keyword, r.business_name, r.phone, r.email, r.address, r.website,
        r.rating, r.reviews_count, r.category, r.instagram, r.facebook, r.linkedin, r.twitter,
        formatDate(r.scraped_at),
      ]),
      keywords: [...new Set(rows.map((r) => r.keyword))],
      rawRows: rows,
    };
  }

  // website_crawler — uses scraped_records
  const rows = await db("scraped_records").where({ query: jobId.toString() }).orWhere(function() {
    // Look up records by job user_id + source WEBSITE + query matching keywords
    this.where({ source: "WEBSITE" });
  }).limit(5000);

  return {
    module,
    columns: ["#", "Query/URL", "Source", "Data", "Scraped At"],
    rows: rows.map((r, i) => [i + 1, r.query, r.source, JSON.stringify(r.data), formatDate(r.scraped_at)]),
    keywords: ["website"],
    rawRows: rows,
  };
}

function formatDate(d) {
  if (!d) return "";
  return new Date(d).toISOString().replace("T", " ").substring(0, 19);
}

// ─────────────────────────────────────────────────────────────────────────────
// XLSX Export
// ─────────────────────────────────────────────────────────────────────────────

async function exportXLSX(results, filePath) {
  const ExcelJS = require("exceljs");
  const wb = new ExcelJS.Workbook();
  wb.creator = "ScrapeGenius Pro";
  wb.created  = new Date();

  // Header style
  const headerFill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF2563EB" } };
  const headerFont = { color: { argb: "FFFFFFFF" }, bold: true, size: 11 };

  // Create one sheet per keyword, plus a summary sheet
  const keywordGroups = {};
  for (const row of results.rawRows) {
    const kw = row.keyword || "All";
    if (!keywordGroups[kw]) keywordGroups[kw] = [];
    keywordGroups[kw].push(row);
  }

  // Summary sheet
  const summarySheet = wb.addWorksheet("Summary");
  summarySheet.addRow(["Keyword", "Record Count", "Exported At"]);
  summarySheet.getRow(1).font = headerFont;
  summarySheet.getRow(1).fill = headerFill;
  for (const [kw, rows] of Object.entries(keywordGroups)) {
    summarySheet.addRow([kw, rows.length, new Date().toISOString()]);
  }
  summarySheet.columns = [{ width: 40 }, { width: 15 }, { width: 25 }];

  // Per-keyword sheets
  for (const [kw, kwRows] of Object.entries(keywordGroups)) {
    const sheetName = kw.replace(/[*?:\\/[\]]/g, "_").substring(0, 31);
    const ws = wb.addWorksheet(sheetName);

    // Add header row
    const headerRow = ws.addRow(results.columns);
    headerRow.eachCell((cell) => {
      cell.fill = headerFill;
      cell.font = headerFont;
      cell.alignment = { vertical: "middle", horizontal: "center" };
    });
    ws.getRow(1).height = 22;

    // Map raw rows to data rows aligned with column order
    for (const row of kwRows) {
      const dataRow = results.rows.find((r) => {
        // Match by keyword position
        if (results.columns.includes("Keyword")) {
          return r[results.columns.indexOf("Keyword")] === kw;
        }
        return true;
      });
      // Add row based on column mapping
      ws.addRow(results.columns.map((col, ci) => {
        const colMap = { "Keyword": "keyword", "Source": "source", "Name": "name",
          "Email": "email", "Phone": "phone", "Address": "address", "Title": "title",
          "Description": "description", "Profile URL": "profile_url",
          "Post Title": "post_title", "Post Link": "post_link",
          "Business Name": "business_name", "Website": "website", "Rating": "rating",
          "Reviews": "reviews_count", "Category": "category",
          "Instagram": "instagram", "Facebook": "facebook",
          "LinkedIn": "linkedin", "Twitter": "twitter",
          "Scraped At": "scraped_at", "Price": "price", "Location": "location",
        };
        if (col === "#") return kwRows.indexOf(row) + 1;
        return row[colMap[col]] ?? "";
      }));
    }

    // Auto-fit columns
    ws.columns.forEach((col) => {
      col.width = Math.min(60, Math.max(12, (col.header?.length || 10) + 4));
    });
  }

  await wb.xlsx.writeFile(filePath);
}

// ─────────────────────────────────────────────────────────────────────────────
// CSV Export
// ─────────────────────────────────────────────────────────────────────────────

function exportCSV(results, filePath) {
  // UTF-8 BOM so Excel opens Arabic text correctly
  const BOM = "\uFEFF";
  const lines = [results.columns.map(csvEscape).join(",")];
  for (const row of results.rows) {
    lines.push(row.map((v) => csvEscape(v)).join(","));
  }
  fs.writeFileSync(filePath, BOM + lines.join("\r\n"), "utf8");
}

function csvEscape(val) {
  const s = val == null ? "" : String(val);
  if (s.includes(",") || s.includes('"') || s.includes("\n")) {
    return `"${s.replace(/"/g, '""')}"`;
  }
  return s;
}

// ─────────────────────────────────────────────────────────────────────────────
// HTML Export
// ─────────────────────────────────────────────────────────────────────────────

function exportHTML(results, filePath, jobId) {
  const rows = results.rows.map((row) =>
    `<tr>${row.map((v) => `<td>${escHtml(v)}</td>`).join("")}</tr>`
  ).join("\n");

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scrape Genius Pro — Export Report (Job ${jobId})</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',Arial,sans-serif;background:#0f172a;color:#e2e8f0;padding:2rem}
    h1{font-size:1.5rem;margin-bottom:0.5rem;color:#60a5fa}
    .meta{font-size:0.8rem;color:#94a3b8;margin-bottom:1.5rem}
    .table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #1e293b}
    table{width:100%;border-collapse:collapse;font-size:0.825rem}
    thead tr{background:#1e3a8a;color:#fff}
    th{padding:10px 14px;text-align:left;font-weight:600;white-space:nowrap}
    tbody tr:nth-child(even){background:#1e293b}
    tbody tr:nth-child(odd){background:#0f172a}
    tbody tr:hover{background:#1d4ed8;transition:background 0.15s}
    td{padding:8px 14px;border-bottom:1px solid #1e293b;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    td a{color:#60a5fa;text-decoration:none}
    .footer{margin-top:1rem;font-size:0.75rem;color:#475569;text-align:right}
  </style>
</head>
<body>
  <h1>📊 Scrape Genius Pro — Export Report</h1>
  <div class="meta">Job ID: ${jobId} | Module: ${results.module} | Exported: ${new Date().toISOString()} | Total Records: ${results.rows.length}</div>
  <div class="table-wrap">
    <table>
      <thead><tr>${results.columns.map((c) => `<th>${escHtml(c)}</th>`).join("")}</tr></thead>
      <tbody>${rows}</tbody>
    </table>
  </div>
  <div class="footer">Generated by ScrapeGenius Pro v9.0 — ${new Date().toLocaleDateString()}</div>
</body>
</html>`;

  fs.writeFileSync(filePath, html, "utf8");
}

function escHtml(v) {
  if (v == null) return "";
  return String(v)
    .replace(/&/g, "&amp;").replace(/</g, "&lt;")
    .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

// ─────────────────────────────────────────────────────────────────────────────
// TXT Export
// ─────────────────────────────────────────────────────────────────────────────

function exportTXT(results, filePath) {
  const header = `SCRAPEGENIUS PRO — Export Report\nGenerated: ${new Date().toISOString()}\nTotal Records: ${results.rows.length}\n${"=".repeat(80)}\n\n`;
  const lines = results.rows.map((row) =>
    results.columns.map((col, i) => `${col}: ${row[i] ?? ""}`).join("\n") + "\n" + "-".repeat(80)
  ).join("\n");
  fs.writeFileSync(filePath, header + lines, "utf8");
}

// ─────────────────────────────────────────────────────────────────────────────
// Public API
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Generates an export file for a job and records it in the DB.
 * 
 * @param {object} opts
 * @param {number} opts.jobId
 * @param {number} opts.userId
 * @param {"XLSX"|"CSV"|"HTML"|"TXT"} opts.format
 * @returns {Promise<{ exportId: number, filePath: string }>}
 */
async function exportResults({ jobId, userId, format }) {
  const fmt = format.toUpperCase();
  const allowed = ["XLSX", "CSV", "HTML", "TXT"];
  if (!allowed.includes(fmt)) throw new Error(`Invalid format: ${format}`);

  // Fetch and shape the results
  const results = await fetchResults(jobId);
  if (results.rows.length === 0) throw new Error("No results to export for this job");

  // Create output directory
  const timestamp = Date.now();
  const outDir = path.join(EXPORT_DIR, String(jobId), String(timestamp));
  fs.mkdirSync(outDir, { recursive: true });
  const ext = fmt.toLowerCase() === "xlsx" ? "xlsx" : fmt.toLowerCase();
  const filePath = path.join(outDir, `results_${jobId}_${timestamp}.${ext}`);

  // Generate file
  if (fmt === "XLSX") await exportXLSX(results, filePath);
  else if (fmt === "CSV") exportCSV(results, filePath);
  else if (fmt === "HTML") exportHTML(results, filePath, jobId);
  else exportTXT(results, filePath);

  // Get file size
  const fileSize = fs.statSync(filePath).size;

  // Record in DB
  const [exportId] = await db("export_records").insert({
    job_id: jobId,
    user_id: userId,
    format: fmt,
    file_path: filePath,
    file_size: fileSize,
    row_count: results.rows.length,
  });

  return { exportId, filePath, rowCount: results.rows.length };
}

/**
 * Returns the file path for a given export record.
 * @param {number} exportId
 * @param {number} userId   (used to authorize the download)
 */
async function getExportFilePath(exportId, userId) {
  const record = await db("export_records").where({ id: exportId, user_id: userId }).first();
  if (!record) throw new Error("Export not found or access denied");
  if (!fs.existsSync(record.file_path)) throw new Error("Export file no longer exists on disk");
  return { filePath: record.file_path, format: record.format, rowCount: record.row_count };
}

module.exports = { exportResults, getExportFilePath };
