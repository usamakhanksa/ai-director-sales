"use client";

import DashboardShell from "@/components/DashboardShell";
import JobRunnerPanel from "@/components/JobRunnerPanel";

export default function GoogleMapsProPage() {
  return (
    <DashboardShell>
      <JobRunnerPanel
        titleKey="mapsPro.title"
        descriptionKey="mapsPro.description"
        createPath="/api/jobs"
        buildBody={(keywords) => ({ module: "google_maps", keywords, config: {} })}
      />
    </DashboardShell>
  );
}
