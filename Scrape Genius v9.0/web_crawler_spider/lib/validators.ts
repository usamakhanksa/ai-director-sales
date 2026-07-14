import { z } from "zod";

export const signupSchema = z.object({
  name: z.string().min(1).max(191),
  email: z.string().email().max(191),
  password: z.string().min(8).max(255),
});

export const loginSchema = z.object({
  email: z.string().email().max(191),
  password: z.string().min(1),
});

export const updateUsageSchema = z.object({
  api_key_id: z.number().int().positive(),
  increment_by: z.number().int().positive().max(100).optional().default(1),
});

export const scrapeSourceSchema = z.enum([
  "GOOGLE",
  "MAP",
  "BING",
  "YAHOO",
  "DUCKDUCKGO",
  "WEBSITE",
  "EMAIL",
  "PHONE",
  "DOCUMENT",
  "IMAGE",
  "WHOIS",
  "INDIAMART",
  "JUSTDIAL",
  "SULEKHA",
  "BUSINESS_DIRECTORY",
  "CUSTOM_API",
]);

export const savedSchema = z.object({
  query: z.string().min(1).max(500),
  source: scrapeSourceSchema,
  data: z.union([z.array(z.record(z.any())), z.record(z.any())]),
  // Lets the frontend label the dashboard tile (e.g. "Emails Scraped"); falls
  // back to a generic "<source> Records Scraped" if omitted.
  stat_type: z.string().min(1).max(100).optional(),
});

export const purchaseCodeActivateSchema = z.object({
  code: z.string().min(1).max(64),
});
