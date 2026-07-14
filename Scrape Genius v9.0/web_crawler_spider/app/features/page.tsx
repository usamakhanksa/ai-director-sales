import Link from "next/link";

import { TOOL_CATEGORIES } from "@/lib/tools-data";
import styles from "./features.module.css";

export const metadata = {
  title: "ScrapeGenius — Templates",
};

export default function FeaturesPage() {
  return (
    <main className={styles.page}>
      <Link href="/" className={styles.backLink}>
        ← Back
      </Link>
      <h1 className={styles.heading}>Top Featured Templates</h1>
      <p className={styles.subheading}>
        Scrape Genius is a multi-tool data scraping and CRM application. Pick a template below to see what it
        extracts — company name, contact number, email ID, website, GST number, WhatsApp number, social links, and
        more.
      </p>

      {TOOL_CATEGORIES.map((category) => (
        <div key={category.id}>
          <h2 id={category.id} className={styles.categoryLabel}>
            {category.label}
          </h2>
          <div className={styles.grid}>
            {category.tools.map((tool, i) => (
              <Link
                key={tool.slug}
                href={tool.run?.customPage ?? `/dashboard/tools/${tool.slug}`}
                className={styles.card}
                style={{ animationDelay: `${(i % 6) * 60}ms` }}
              >
                {tool.isNew && <span className={styles.newBadge}>New</span>}
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={tool.iconSrc} alt="" className={styles.cardIcon} />
                <h3 className={styles.cardTitle}>{tool.title}</h3>
                <p className={styles.cardDescription}>{tool.description}</p>
                <div className={styles.cardFooter}>
                  <span className={styles.openBadge}>Open tool →</span>
                </div>
              </Link>
            ))}
          </div>
        </div>
      ))}
    </main>
  );
}
