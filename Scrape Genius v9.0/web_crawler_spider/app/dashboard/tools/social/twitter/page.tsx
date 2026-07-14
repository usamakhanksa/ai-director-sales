"use client";

import DashboardShell from "@/components/DashboardShell";
import JobRunnerPanel from "@/components/JobRunnerPanel";

export default function TwitterScraperPage() {
  return (
    <DashboardShell>
      <JobRunnerPanel
        titleKey="twitter.title"
        descriptionKey="twitter.description"
        createPath="/api/social/twitter"
        buildBody={(keywords) => ({ keywords, config: {} })}
      />
    </DashboardShell>
  );
}
