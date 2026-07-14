import { prisma } from "@/lib/prisma";
import type { ScrapeSource } from "@prisma/client";

const SOURCE_LABELS: Record<ScrapeSource, string> = {
  GOOGLE: "Google",
  MAP: "Map",
  BING: "Bing",
  YAHOO: "Yahoo",
  DUCKDUCKGO: "DuckDuckGo",
  WEBSITE: "Website",
  EMAIL: "Email",
  PHONE: "Phone",
  DOCUMENT: "Document",
  IMAGE: "Image",
  WHOIS: "Whois",
  INDIAMART: "IndiaMART",
  JUSTDIAL: "Justdial",
  SULEKHA: "Sulekha",
  BUSINESS_DIRECTORY: "Business Directory",
  CUSTOM_API: "Custom API",
  INSTAGRAM: "Instagram",
  FACEBOOK: "Facebook",
  LINKEDIN: "LinkedIn",
  TWITTER: "Twitter/X",
  TIKTOK: "TikTok",
  YOUTUBE: "YouTube",
  ECOMMERCE: "E-commerce",
  REVIEW_PLATFORM: "Review Platform",
  REAL_ESTATE: "Real Estate",
  JOB_BOARD: "Job Board",
  NEWS_RSS: "News/RSS",
};

export function labelForSource(source: ScrapeSource): string {
  return SOURCE_LABELS[source];
}

/**
 * Persists a batch of scraped rows as one ScrapedRecord and bumps the
 * matching DashboardStat tile, atomically. Shared by /api/saved and every
 * scraper route so a scrape's results always show up on the dashboard
 * without a second round-trip from the client.
 */
export async function saveScrapedRecords(
  userId: number,
  source: ScrapeSource,
  query: string,
  data: unknown[] | Record<string, unknown>,
  statTypeOverride?: string,
) {
  const recordCount = Array.isArray(data) ? data.length : 1;
  const statType = statTypeOverride || `${labelForSource(source)} Records Scraped`;

  const [record, stat] = await prisma.$transaction([
    prisma.scrapedRecord.create({
      data: {
        userId,
        query: query.slice(0, 500),
        source,
        data: data as any,
      },
    }),
    prisma.dashboardStat.upsert({
      where: { userId_statType: { userId, statType } },
      update: { recordCount: { increment: recordCount } },
      create: { userId, statType, recordCount },
    }),
  ]);

  return { record, stat };
}
