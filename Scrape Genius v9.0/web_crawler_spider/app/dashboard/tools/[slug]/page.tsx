"use client";

import { useState, type FormEvent } from "react";
import { useParams } from "next/navigation";

import DashboardShell from "@/components/DashboardShell";
import ResultsTable, { downloadCsv } from "@/components/ResultsTable";
import { findTool } from "@/lib/tools-data";
import { getToken } from "@/lib/client-auth";
import { input, label, button } from "@/lib/ui-styles";

/** Normalizes the wildly different response shapes each scraper route returns into flat rows for the table. */
function responseToRows(fieldKind: string | undefined, data: any): Record<string, unknown>[] {
  if (!data) return [];

  if (fieldKind === "domain" && data.result) {
    return [data.result];
  }

  if (fieldKind === "email") {
    return [data];
  }

  if (fieldKind === "file") {
    const emails = (data.emails ?? []).map((value: string) => ({ type: "email", value }));
    const phones = (data.phones ?? []).map((value: string) => ({ type: "phone", value }));
    return [...emails, ...phones];
  }

  if (fieldKind === "url") {
    const emails = (data.emails ?? []).map((value: string) => ({ type: "email", value }));
    const phones = (data.phones ?? []).map((value: string) => ({ type: "phone", value }));
    const companies = (data.companies ?? []).map((value: string) => ({ type: "company", value }));
    return [...emails, ...phones, ...companies];
  }

  if (Array.isArray(data.items)) {
    return data.items.map((row: unknown) => (typeof row === "object" && row !== null ? row : { value: row }));
  }

  if (Array.isArray(data.results)) {
    return data.results.map((row: unknown) => (typeof row === "object" && row !== null ? row : { value: row }));
  }

  return [];
}

export default function ToolRunnerPage() {
  const params = useParams<{ slug: string }>();
  const slug = params?.slug ?? "";
  const tool = findTool(slug);

  const [query, setQuery] = useState("");
  const [urls, setUrls] = useState("");
  const [keyword, setKeyword] = useState("");
  const [country, setCountry] = useState("");
  const [contactSource, setContactSource] = useState<"url" | "text">("url");
  const [contactValue, setContactValue] = useState("");
  const [domain, setDomain] = useState("");
  const [file, setFile] = useState<File | null>(null);
  const [limit, setLimit] = useState(10);
  const [url, setUrl] = useState("");
  const [email, setEmail] = useState("");

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [response, setResponse] = useState<any>(null);

  if (!tool || !tool.run?.apiRoute) {
    return (
      <DashboardShell>
        <p>Tool not found.</p>
      </DashboardShell>
    );
  }

  const { apiRoute, fieldKind, method = "POST" } = tool.run;
  const contactType = tool.run.contactType;

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setResponse(null);
    setLoading(true);

    const token = getToken();

    try {
      let res: Response;

      if (fieldKind === "file") {
        if (!file) {
          setError("Choose a file first");
          setLoading(false);
          return;
        }
        const formData = new FormData();
        formData.append("file", file);
        res = await fetch(apiRoute!, {
          method: "POST",
          headers: { Authorization: `Bearer ${token}` },
          body: formData,
        });
      } else {
        let body: Record<string, unknown> = {};
        if (fieldKind === "query") body = { q: query, query, limit };
        else if (fieldKind === "urls") body = { urls: urls.split("\n").map((s) => s.trim()).filter(Boolean) };
        else if (fieldKind === "keyword-country") body = { keyword, country: country || undefined, limit };
        else if (fieldKind === "contact")
          body =
            contactSource === "url"
              ? { type: contactType, url: contactValue }
              : { type: contactType, text: contactValue };
        else if (fieldKind === "domain") body = { domain };
        else if (fieldKind === "url") body = { url };
        else if (fieldKind === "email") body = { email };

        if (method === "GET") {
          const searchParams = new URLSearchParams();
          for (const [key, value] of Object.entries(body)) {
            if (value !== undefined && value !== null && value !== "") searchParams.set(key, String(value));
          }
          res = await fetch(`${apiRoute}?${searchParams.toString()}`, {
            method: "GET",
            headers: { Authorization: `Bearer ${token}` },
          });
        } else {
          res = await fetch(apiRoute!, {
            method: "POST",
            headers: { "Content-Type": "application/json", Authorization: `Bearer ${token}` },
            body: JSON.stringify(body),
          });
        }
      }

      const json = await res.json();
      if (!res.ok || !json.success) {
        setError(json.error || `Request failed (${res.status})`);
      } else {
        setResponse(json.data);
      }
    } catch {
      setError("Network error — is the server running?");
    } finally {
      setLoading(false);
    }
  }

  const rows = responseToRows(fieldKind, response);

  return (
    <DashboardShell>
      <h1 style={{ fontSize: "1.35rem", margin: "0 0 0.25rem" }}>{tool.title}</h1>
      <p style={{ color: "var(--muted, #64748b)", marginBottom: "1.25rem", maxWidth: 640 }}>{tool.description}</p>

      <form onSubmit={handleSubmit} style={{ display: "grid", gap: "0.75rem", maxWidth: 520, marginBottom: "1.5rem" }}>
        {fieldKind === "query" && (
          <>
            <label style={label}>
              Search query
              <input value={query} onChange={(e) => setQuery(e.target.value)} required style={input} />
            </label>
            <label style={label}>
              Result limit
              <input
                type="number"
                min={1}
                value={limit}
                onChange={(e) => setLimit(Number(e.target.value))}
                style={input}
              />
            </label>
          </>
        )}

        {fieldKind === "urls" && (
          <label style={label}>
            URLs (one per line)
            <textarea
              value={urls}
              onChange={(e) => setUrls(e.target.value)}
              required
              rows={5}
              style={input}
              placeholder={"https://example.com\nhttps://another-site.com"}
            />
          </label>
        )}

        {fieldKind === "keyword-country" && (
          <>
            <label style={label}>
              Keyword
              <input value={keyword} onChange={(e) => setKeyword(e.target.value)} required style={input} />
            </label>
            <label style={label}>
              Country (optional)
              <input value={country} onChange={(e) => setCountry(e.target.value)} style={input} />
            </label>
            <label style={label}>
              Result limit
              <input
                type="number"
                min={1}
                value={limit}
                onChange={(e) => setLimit(Number(e.target.value))}
                style={input}
              />
            </label>
          </>
        )}

        {fieldKind === "contact" && (
          <>
            <div style={{ display: "flex", gap: "1rem", fontSize: "0.875rem" }}>
              <label>
                <input
                  type="radio"
                  checked={contactSource === "url"}
                  onChange={() => setContactSource("url")}
                />{" "}
                From URL
              </label>
              <label>
                <input
                  type="radio"
                  checked={contactSource === "text"}
                  onChange={() => setContactSource("text")}
                />{" "}
                From pasted text
              </label>
            </div>
            {contactSource === "url" ? (
              <label style={label}>
                Page URL
                <input value={contactValue} onChange={(e) => setContactValue(e.target.value)} required style={input} />
              </label>
            ) : (
              <label style={label}>
                Text
                <textarea
                  value={contactValue}
                  onChange={(e) => setContactValue(e.target.value)}
                  required
                  rows={5}
                  style={input}
                />
              </label>
            )}
          </>
        )}

        {fieldKind === "domain" && (
          <label style={label}>
            Domain
            <input value={domain} onChange={(e) => setDomain(e.target.value)} required placeholder="example.com" style={input} />
          </label>
        )}

        {fieldKind === "url" && (
          <label style={label}>
            URL
            <input
              value={url}
              onChange={(e) => setUrl(e.target.value)}
              required
              type="url"
              placeholder="https://example.com"
              style={input}
            />
          </label>
        )}

        {fieldKind === "email" && (
          <label style={label}>
            Email address
            <input
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              type="email"
              placeholder="name@example.com"
              style={input}
            />
          </label>
        )}

        {fieldKind === "file" && (
          <label style={label}>
            File
            <input type="file" onChange={(e) => setFile(e.target.files?.[0] ?? null)} required style={input} />
          </label>
        )}

        <button type="submit" disabled={loading} style={button}>
          {loading ? "Running…" : "Run scraper"}
        </button>
      </form>

      {error && <p style={{ color: "#c00" }}>{error}</p>}

      {response && (
        <div style={{ display: "grid", gap: "0.75rem" }}>
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
            <span style={{ fontSize: "0.875rem", color: "var(--muted, #64748b)" }}>
              {rows.length} result{rows.length === 1 ? "" : "s"}
              {typeof response.count === "number" && response.count !== rows.length ? ` (${response.count} reported)` : ""}
            </span>
            {rows.length > 0 && (
              <button type="button" onClick={() => downloadCsv(rows, `${slug}-results.csv`)} style={{ ...button, padding: "0.4rem 0.8rem", fontSize: "0.8rem" }}>
                Export CSV
              </button>
            )}
          </div>
          <ResultsTable rows={rows} />
          {Array.isArray(response.failed) && response.failed.length > 0 && (
            <p style={{ color: "#b45309", fontSize: "0.8rem" }}>
              {response.failed.length} URL(s) failed: {response.failed.map((f: any) => f.url).join(", ")}
            </p>
          )}
        </div>
      )}
    </DashboardShell>
  );
}
