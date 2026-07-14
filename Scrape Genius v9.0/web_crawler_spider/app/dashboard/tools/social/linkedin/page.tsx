"use client";

import DashboardShell from "@/components/DashboardShell";
import JobRunnerPanel from "@/components/JobRunnerPanel";

export default function LinkedInScraperPage() {
  return (
    <DashboardShell>
      <JobRunnerPanel
        titleKey="linkedin.title"
        descriptionKey="linkedin.description"
        createPath="/api/social/linkedin"
        buildBody={(keywords) => ({ keywords, config: {} })}
      />
    </DashboardShell>
  );
}
