export const dynamic = "force-dynamic";
import { NextRequest } from "next/server";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, withErrorHandling } from "@/lib/api-response";

const enrichSchema = z.object({
  email: z.string().trim().optional().nullable(),
  phone: z.string().trim().optional().nullable(),
  website: z.string().trim().optional().nullable(),
  socialLinks: z.record(z.string(), z.string()).optional().nullable(),
  reviews: z.array(z.string()).optional().nullable(),
});

const GENERIC_EMAIL_PROVIDERS = /@(gmail|yahoo|hotmail|outlook)\./i;

const NEGATIVE_WORDS = [
  "bad",
  "worst",
  "terrible",
  "rude",
  "slow",
  "poor",
  "disappointed",
  "never again",
  "awful",
  "unresponsive",
  "scam",
  "broken",
  "late",
  "dirty",
  "overpriced",
];
const POSITIVE_WORDS = [
  "great",
  "excellent",
  "amazing",
  "friendly",
  "fast",
  "professional",
  "recommend",
  "best",
  "helpful",
  "clean",
  "love",
];

function analyzeReviewSentiment(reviews: string[]) {
  if (!reviews || reviews.length === 0) {
    return { overallSentiment: "neutral" as const, painPoints: [] as string[], severityScore: 0 };
  }

  let negativeHits = 0;
  let positiveHits = 0;
  const painPoints: string[] = [];

  for (const review of reviews.slice(0, 25)) {
    const text = review.toLowerCase();
    const hitNegative = NEGATIVE_WORDS.filter((w) => text.includes(w));
    const hitPositive = POSITIVE_WORDS.filter((w) => text.includes(w));
    negativeHits += hitNegative.length;
    positiveHits += hitPositive.length;
    if (hitNegative.length > 0) painPoints.push(review.trim().slice(0, 160));
  }

  const total = negativeHits + positiveHits;
  const severityScore = total === 0 ? 0 : Math.round((negativeHits / total) * 10);
  const overallSentiment = severityScore >= 6 ? "negative" : severityScore <= 2 ? "positive" : "neutral";

  return { overallSentiment, painPoints: painPoints.slice(0, 5), severityScore };
}

function calculateLeadScore(input: {
  email?: string | null;
  phone?: string | null;
  website?: string | null;
  socialLinks?: Record<string, string> | null;
  severityScore: number;
}) {
  let score = 0;
  const factors: string[] = [];

  if (input.email && GENERIC_EMAIL_PROVIDERS.test(input.email)) {
    score += 25;
    factors.push("Uses a generic email provider (high conversion potential)");
  }

  if (!input.website) {
    score += 20;
    factors.push("No website detected (potential upsell opportunity)");
  }

  if (input.socialLinks && Object.keys(input.socialLinks).length > 0) {
    score += 10;
    factors.push("Active on social media");
  }

  if (input.phone) {
    score += 10;
    factors.push("Phone number available for direct outreach");
  }

  if (input.severityScore > 6) {
    score += 30;
    factors.push("Negative reviews indicate operational pain points");
  } else if (input.severityScore > 3) {
    score += 15;
    factors.push("Mixed reviews suggest room for improvement");
  }

  score = Math.min(score, 100);
  const rating = score >= 70 ? "Hot Lead" : score >= 40 ? "Warm Lead" : "Cold Lead";

  return { score, factors, rating };
}

// POST /api/ai-enrichment — scores a scraped lead and mines its reviews for pain points.
export async function POST(req: NextRequest) {
  return withErrorHandling(async () => {
    await requireAuth(req);
    const body = enrichSchema.parse(await req.json());

    const sentiment = analyzeReviewSentiment(body.reviews ?? []);
    const leadScore = calculateLeadScore({
      email: body.email,
      phone: body.phone,
      website: body.website,
      socialLinks: body.socialLinks,
      severityScore: sentiment.severityScore,
    });

    return ok({ leadScore, sentiment });
  });
}
