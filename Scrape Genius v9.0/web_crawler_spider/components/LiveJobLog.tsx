"use client";

import { useEffect, useRef, useState } from "react";

import { getToken } from "@/lib/client-auth";
import { useTranslation } from "@/lib/i18n";

interface LogEntry {
  level?: "INFO" | "WARN" | "ERROR";
  message?: string;
  type?: string;
  status?: string;
  progress?: number;
  created_at?: string;
}

const LEVEL_COLOR: Record<string, string> = {
  INFO: "#60a5fa",
  WARN: "#facc15",
  ERROR: "#f87171",
};

/**
 * Streams real-time scraper logs for a job via the /api/jobs/:id/logs SSE
 * endpoint. Uses fetch + a manual reader (not EventSource) because
 * EventSource can't send an Authorization header.
 */
export default function LiveJobLog({ jobId }: { jobId: number | null }) {
  const { t } = useTranslation();
  const [lines, setLines] = useState<LogEntry[]>([]);
  const [paused, setPaused] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!jobId) return;
    const token = getToken();
    if (!token) return;

    const controller = new AbortController();
    setLines([]);

    (async () => {
      try {
        const res = await fetch(`/api/jobs/${jobId}/logs`, {
          headers: { Authorization: `Bearer ${token}` },
          signal: controller.signal,
        });
        if (!res.body) return;

        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = "";

        for (;;) {
          const { value, done } = await reader.read();
          if (done) break;
          buffer += decoder.decode(value, { stream: true });

          const events = buffer.split("\n\n");
          buffer = events.pop() ?? "";

          for (const evt of events) {
            const dataLine = evt.split("\n").find((l) => l.startsWith("data: "));
            if (!dataLine) continue;
            try {
              const parsed = JSON.parse(dataLine.slice(6));
              setLines((prev) => [...prev, parsed]);
            } catch {
              // ignore malformed keep-alive/comment lines
            }
          }
        }
      } catch {
        // stream ended or aborted — nothing to do
      }
    })();

    return () => controller.abort();
  }, [jobId]);

  useEffect(() => {
    if (paused || !containerRef.current) return;
    containerRef.current.scrollTop = containerRef.current.scrollHeight;
  }, [lines, paused]);

  if (!jobId) return null;

  return (
    <div
      ref={containerRef}
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
      style={{
        background: "#0b1220",
        color: "#cbd5e1",
        borderRadius: 8,
        padding: "0.75rem 0.9rem",
        fontFamily: "ui-monospace, monospace",
        fontSize: "0.78rem",
        maxHeight: 220,
        overflowY: "auto",
      }}
    >
      {lines.length === 0 && <div style={{ opacity: 0.6 }}>{t("common.viewLogs")}…</div>}
      {lines.map((line, i) => {
        if (line.type === "status") {
          return (
            <div key={i} style={{ opacity: 0.7 }}>
              [{t(`status.${line.status}`)}] {line.progress}%
            </div>
          );
        }
        if (line.type === "stream_end") return null;
        return (
          <div key={i}>
            <span style={{ color: LEVEL_COLOR[line.level || "INFO"] }}>[{line.level || "INFO"}]</span>{" "}
            {line.message}
          </div>
        );
      })}
    </div>
  );
}
