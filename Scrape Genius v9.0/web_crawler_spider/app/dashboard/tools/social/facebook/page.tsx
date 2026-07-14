"use client";

import DashboardShell from "@/components/DashboardShell";
import JobRunnerPanel from "@/components/JobRunnerPanel";

export default function FacebookScraperPage() {
  return (
    <DashboardShell>
      <JobRunnerPanel
        titleKey="facebook.title"
        descriptionKey="facebook.description"
        createPath="/api/social/facebook"
        buildBody={(keywords) => ({ keywords, config: {} })}
      />
    </DashboardShell>
  );
}
