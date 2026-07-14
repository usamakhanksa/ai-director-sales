"use client";

import DashboardShell from "@/components/DashboardShell";
import JobRunnerPanel from "@/components/JobRunnerPanel";

export default function InstagramScraperPage() {
  return (
    <DashboardShell>
      <JobRunnerPanel
        titleKey="instagram.title"
        descriptionKey="instagram.description"
        createPath="/api/social/instagram"
        buildBody={(keywords) => ({ keywords, config: {} })}
      />
    </DashboardShell>
  );
}
