/**
 * The common shape every scraper normalizes its output into, so the
 * dashboard results table, CSV export, and /api/saved payload all work
 * the same way regardless of which tool produced the row.
 */
export interface NormalizedRecord {
  companyName?: string;
  phone?: string;
  email?: string;
  website?: string;
  gstNumber?: string;
  whatsapp?: string;
  socialLinks?: Record<string, string>;
  followers?: number;
  metaTitle?: string;
  metaKeywords?: string;
  metaDescription?: string;
  address?: string;
  snippet?: string;
  source?: string;
  [key: string]: unknown;
}
