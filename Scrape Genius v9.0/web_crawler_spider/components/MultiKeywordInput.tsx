"use client";

import { useRef, type ChangeEvent } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faXmark, faFileArrowUp } from "@fortawesome/free-solid-svg-icons";

import { useTranslation } from "@/lib/i18n";
import { secondaryButton } from "@/lib/ui-styles";

interface MultiKeywordInputProps {
  keywords: string[];
  onChange: (keywords: string[]) => void;
}

function splitKeywords(raw: string): string[] {
  return raw
    .split(/[\n,]/)
    .map((k) => k.trim())
    .filter(Boolean);
}

/** Textarea + CSV import for entering many search keywords at once, shown as removable tags. */
export default function MultiKeywordInput({ keywords, onChange }: MultiKeywordInputProps) {
  const { t } = useTranslation();
  const fileRef = useRef<HTMLInputElement>(null);

  function addFromText(raw: string) {
    const incoming = splitKeywords(raw);
    if (!incoming.length) return;
    const merged = Array.from(new Set([...keywords, ...incoming]));
    onChange(merged);
  }

  function handleFile(e: ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => addFromText(String(reader.result || ""));
    reader.readAsText(file);
    e.target.value = "";
  }

  function removeAt(index: number) {
    onChange(keywords.filter((_, i) => i !== index));
  }

  return (
    <div style={{ display: "grid", gap: "0.6rem" }}>
      <textarea
        placeholder={`${t("common.addKeyword")}… (one per line, or comma separated)`}
        rows={3}
        style={{
          padding: "0.6rem",
          fontSize: "0.9rem",
          border: "1px solid #ccc",
          borderRadius: 6,
          resize: "vertical",
          fontFamily: "inherit",
        }}
        onKeyDown={(e) => {
          if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            const target = e.currentTarget;
            addFromText(target.value);
            target.value = "";
          }
        }}
        onBlur={(e) => {
          if (e.currentTarget.value.trim()) {
            addFromText(e.currentTarget.value);
            e.currentTarget.value = "";
          }
        }}
      />

      <div style={{ display: "flex", gap: "0.5rem", alignItems: "center", flexWrap: "wrap" }}>
        <button type="button" style={{ ...secondaryButton, width: "auto" }} onClick={() => fileRef.current?.click()}>
          <FontAwesomeIcon icon={faFileArrowUp} style={{ marginInlineEnd: "0.4rem" }} />
          {t("common.importCsv")}
        </button>
        <input ref={fileRef} type="file" accept=".csv,.txt" hidden onChange={handleFile} />

        {keywords.length > 0 && (
          <button
            type="button"
            style={{ ...secondaryButton, width: "auto", color: "#b91c1c" }}
            onClick={() => onChange([])}
          >
            {t("common.clearAll")}
          </button>
        )}

        <span style={{ fontSize: "0.78rem", color: "#64748b" }}>
          {keywords.length} {t("common.keywords").toLowerCase()}
        </span>
      </div>

      {keywords.length > 0 && (
        <div style={{ display: "flex", flexWrap: "wrap", gap: "0.4rem" }}>
          {keywords.map((kw, i) => (
            <span
              key={`${kw}-${i}`}
              style={{
                display: "inline-flex",
                alignItems: "center",
                gap: "0.35rem",
                background: "#eef2ff",
                color: "#1e3a8a",
                borderRadius: 999,
                padding: "0.25rem 0.5rem 0.25rem 0.7rem",
                fontSize: "0.8rem",
              }}
            >
              {kw}
              <button
                type="button"
                aria-label="Remove"
                onClick={() => removeAt(i)}
                style={{
                  background: "none",
                  border: "none",
                  cursor: "pointer",
                  color: "#1e3a8a",
                  display: "flex",
                  padding: 0,
                }}
              >
                <FontAwesomeIcon icon={faXmark} />
              </button>
            </span>
          ))}
        </div>
      )}
    </div>
  );
}
