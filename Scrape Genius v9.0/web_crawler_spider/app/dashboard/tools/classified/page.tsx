"use client";

import { useState } from "react";

import DashboardShell from "@/components/DashboardShell";
import JobRunnerPanel from "@/components/JobRunnerPanel";
import { useTranslation } from "@/lib/i18n";

const SITES: { key: string; label: string }[] = [
  { key: "HARAJ", label: "Haraj (Saudi Arabia)" },
  { key: "OPENSOOQ", label: "OpenSooq" },
  { key: "DUBIZZLE", label: "Dubizzle (sa.dubizzle.com)" },
  { key: "OLX_KW", label: "OLX Kuwait" },
  { key: "OLX_EG", label: "OLX Egypt" },
  { key: "MUBAWAB_SA", label: "Mubawab (Saudi Arabia)" },
  { key: "BAYUT", label: "Bayut (Saudi Arabia)" },
  { key: "PROPERTYFINDER", label: "Property Finder (Saudi Arabia)" },
  { key: "SYARAH", label: "Syarah" },
  { key: "EXPATRIATES", label: "Expatriates.com" },
  { key: "FORSALE_KW", label: "4Sale Kuwait" },
];

export default function ClassifiedScraperPage() {
  const { t } = useTranslation();
  const [sites, setSites] = useState<string[]>(["HARAJ", "OPENSOOQ", "DUBIZZLE"]);

  function toggleSite(key: string) {
    setSites((prev) => (prev.includes(key) ? prev.filter((s) => s !== key) : [...prev, key]));
  }

  return (
    <DashboardShell>
      <JobRunnerPanel
        titleKey="classified.title"
        descriptionKey="classified.description"
        createPath="/api/classified"
        buildBody={(keywords) => ({ site: "haraj", keywords, config: { sites } })}
        canRun={() => sites.length > 0}
        extraFields={
          <div>
            <div style={{ fontSize: "0.85rem", fontWeight: 600, marginBottom: "0.5rem" }}>
              {t("classified.selectSites")}
            </div>
            <div style={{ display: "flex", flexWrap: "wrap", gap: "0.6rem" }}>
              {SITES.map((site) => (
                <label
                  key={site.key}
                  style={{
                    display: "inline-flex",
                    alignItems: "center",
                    gap: "0.35rem",
                    fontSize: "0.8rem",
                    background: sites.includes(site.key) ? "#eef2ff" : "#f8fafc",
                    border: "1px solid #e2e8f0",
                    borderRadius: 6,
                    padding: "0.3rem 0.55rem",
                    cursor: "pointer",
                  }}
                >
                  <input type="checkbox" checked={sites.includes(site.key)} onChange={() => toggleSite(site.key)} />
                  {site.label}
                </label>
              ))}
            </div>
          </div>
        }
      />
    </DashboardShell>
  );
}
