export type ToolFieldKind = "query" | "urls" | "keyword-country" | "contact" | "file" | "domain";

export interface ToolRunConfig {
  /** POST endpoint this tool calls to run a scrape. Omitted for tools with a dedicated custom page (CRM, custom API). */
  apiRoute?: string;
  fieldKind?: ToolFieldKind;
  /** For "contact" field kind: which ScrapeSource this tool charges to. */
  contactType?: "EMAIL" | "PHONE";
  /** Route to a hand-built page instead of the generic runner (CRM connections, custom API). */
  customPage?: string;
}

export interface ToolTemplate {
  slug: string;
  title: string;
  description: string;
  iconSrc: string;
  isNew?: boolean;
  run?: ToolRunConfig;
}

export interface ToolCategory {
  id: string;
  label: string;
  tools: ToolTemplate[];
}

const ICON_BASE = "/assets/iconfonts/dashboard-icon";

export const TOOL_CATEGORIES: ToolCategory[] = [
  {
    id: "live-website-scraper",
    label: "Live Website Scraper",
    tools: [
      {
        slug: "live-website-scraper",
        title: "Live Website Scraper",
        description:
          "Extract data from millions of live websites for the selected country and get detailed data such as phone numbers, email addresses, social media links, and more.",
        iconSrc: `${ICON_BASE}/live.png`,
        run: { apiRoute: "/api/scrape/website-data-scraper/", fieldKind: "urls" },
      },
      {
        slug: "live-website-data",
        title: "Live Website Data",
        description: "Get enhanced data from our actual database in a few seconds.",
        iconSrc: `${ICON_BASE}/website.png`,
        run: { apiRoute: "/api/scrape/website-data-center/", fieldKind: "keyword-country" },
      },
    ],
  },
  {
    id: "search-engines",
    label: "Search Engine Scraper",
    tools: [
      {
        slug: "bing-search-scraper",
        title: "Bing Search Scraper",
        description:
          "Scrape data using keywords on Bing to extract website names, emails, contact numbers, and more efficiently.",
        iconSrc: `${ICON_BASE}/bing.png`,
        run: { apiRoute: "/api/scrape/bing-search/", fieldKind: "query" },
      },
      {
        slug: "google-search-scraper",
        title: "Google Search Scraper",
        description:
          "Scrape data using keywords on Google to extract website names, emails, contact numbers, and more efficiently.",
        iconSrc: `${ICON_BASE}/google.png`,
        run: { apiRoute: "/api/scrape/google-search/", fieldKind: "query" },
      },
      {
        slug: "duckduckgo-search-scraper",
        title: "DuckDuckGo Search Scraper",
        description:
          "Scrape data using keywords on DuckDuckGo to extract website names, emails, contact numbers, and more.",
        iconSrc: `${ICON_BASE}/duckduckgo.png`,
        run: { apiRoute: "/api/scrape/duckduckgo-search/", fieldKind: "query" },
      },
      {
        slug: "yahoo-search-scraper",
        title: "Yahoo Search Scraper",
        description:
          "Scrape data using keywords on Yahoo to extract website names, emails, contact numbers, and more.",
        iconSrc: `${ICON_BASE}/yahoo.png`,
        run: { apiRoute: "/api/scrape/yahoo-search/", fieldKind: "query" },
      },
      {
        slug: "google-map-scraper",
        title: "Google Map Scraper",
        description:
          "Extracts website names, emails, and contact numbers from maps.google.com, providing essential data for your business needs.",
        iconSrc: `${ICON_BASE}/gmapIcon.png`,
        run: { apiRoute: "/api/scrape/google-maps/", fieldKind: "query" },
      },
      {
        slug: "google-maps-pro",
        title: "Google Maps Business Extractor Pro",
        description:
          "Job-queue based Maps scraper: business name, phone, address, website, rating, email, and Instagram/Facebook/LinkedIn/Twitter profiles, exportable to Excel/HTML/CSV.",
        iconSrc: `${ICON_BASE}/gmapIcon.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/google-maps-pro" },
      },
    ],
  },
  {
    id: "social-media",
    label: "Social Media Scraper",
    tools: [
      {
        slug: "instagram-scraper",
        title: "Instagram Profile Scraper",
        description:
          "Extract profile information, followers, following, bio, and recent posts from public Instagram accounts.",
        iconSrc: `${ICON_BASE}/facebook.png`, // Using Facebook icon temporarily
        isNew: true,
        run: { customPage: "/dashboard/tools/social/instagram" },
      },
      {
        slug: "facebook-scraper",
        title: "Facebook Phones & Emails Extractor",
        description:
          "Multi-keyword search across public Facebook profiles/pages to extract phones, emails, addresses, titles, descriptions, and profile links.",
        iconSrc: `${ICON_BASE}/facebook.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/social/facebook" },
      },
      {
        slug: "linkedin-email-finder",
        title: "LinkedIn Email Finder",
        description:
          "Finds professional emails via Google/Bing/Yahoo dorking across all country-specific search domains — no LinkedIn login required.",
        iconSrc: `${ICON_BASE}/linkedin.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/social/linkedin" },
      },
      {
        slug: "twitter-scraper",
        title: "Twitter/X Comment & Profile Scraper",
        description: "Keyword search across tweets and comments to extract phone numbers, emails, and profile links.",
        iconSrc: `${ICON_BASE}/verify.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/social/twitter" },
      },
    ],
  },
  {
    id: "classified",
    label: "Classified & Haraj",
    tools: [
      {
        slug: "haraj-classified-scraper",
        title: "Haraj & Classified Sites Scraper",
        description:
          "Keyword scraper for Haraj Saudi Arabia and 20+ MENA classified sites (OpenSooq, Dubizzle, OLX, Bayut, and more) — extracts post links, phone numbers, and emails.",
        iconSrc: `${ICON_BASE}/directoryIcon.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/classified" },
      },
    ],
  },
  {
    id: "directories",
    label: "B2B Directory Scraper",
    tools: [
      {
        slug: "indiamart-scraper",
        title: "IndiaMART Scraper",
        description: "Extracts website names, emails, and contact numbers from IndiaMART listings.",
        iconSrc: `${ICON_BASE}/directoryIcon.png`,
        run: { apiRoute: "/api/scrape/indiamart/", fieldKind: "urls" },
      },
      {
        slug: "justdial-scraper",
        title: "Justdial Scraper",
        description: "Extracts website names, emails, and contact numbers from Justdial listings.",
        iconSrc: `${ICON_BASE}/directoryIcon.png`,
        run: { apiRoute: "/api/scrape/justdial/", fieldKind: "urls" },
      },
      {
        slug: "sulekha-scraper",
        title: "Sulekha Scraper",
        description: "Extracts website names, emails, and contact numbers from Sulekha listings.",
        iconSrc: `${ICON_BASE}/directoryIcon.png`,
        run: { apiRoute: "/api/scrape/sulekha/", fieldKind: "urls" },
      },
      {
        slug: "business-directory-scraper",
        title: "Business Directory Scraper",
        description:
          "Easily scrape data by entering directory URLs, including email IDs, contact numbers, and domain names.",
        iconSrc: `${ICON_BASE}/directoryIcon.png`,
        run: { apiRoute: "/api/scrape/business-directory/", fieldKind: "urls" },
      },
    ],
  },
  {
    id: "contact-scrapers",
    label: "Bulk Contact Scraper",
    tools: [
      {
        slug: "email-scraper",
        title: "Email Scraper",
        description: "Extracts website names, emails, and contact numbers for your business needs.",
        iconSrc: `${ICON_BASE}/verify.png`,
        run: { apiRoute: "/api/scrape/contact-scraper/", fieldKind: "contact", contactType: "EMAIL" },
      },
      {
        slug: "phone-number-scraper",
        title: "Phone number scraper",
        description: "Extracts website names, emails, and contact numbers for your business needs.",
        iconSrc: `${ICON_BASE}/checker.png`,
        run: { apiRoute: "/api/scrape/contact-scraper/", fieldKind: "contact", contactType: "PHONE" },
      },
      {
        slug: "justdial-crm",
        title: "Justdial CRM",
        description:
          "Our Justdial CRM helps you automatically collect, centralize, and manage customer enquiries from your Justdial account — without any API integration.",
        iconSrc: `${ICON_BASE}/facebook.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/crm/justdial" },
      },
      {
        slug: "indiamart-crm",
        title: "IndiaMart CRM",
        description:
          "Our IndiaMART CRM is built to simplify lead management by automatically fetching and centralizing your IndiaMART enquiries — completely API-free.",
        iconSrc: `${ICON_BASE}/linkedin.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/crm/indiamart" },
      },
    ],
  },
  {
    id: "website-scrapers",
    label: "Web Scraper",
    tools: [
      {
        slug: "website-data-scraper",
        title: "Website Data Scraper",
        description:
          "Gather country-specific domain records, including domain names, emails, and phone numbers.",
        iconSrc: `${ICON_BASE}/websiteIcon.png`,
        run: { apiRoute: "/api/scrape/website-data-scraper/", fieldKind: "urls" },
      },
      {
        slug: "website-data-center",
        title: "Website Data Center",
        description: "Quick searches by country and keyword, delivering results like URLs, emails, and phone numbers.",
        iconSrc: `${ICON_BASE}/map.png`,
        run: { apiRoute: "/api/scrape/website-data-center/", fieldKind: "keyword-country" },
      },
    ],
  },
  {
    id: "document-data-scraper",
    label: "File-Based Scraper",
    tools: [
      {
        slug: "document-data-scraper",
        title: "Document Data Scraper",
        description: "Handles .txt, .csv, and more, efficiently extracting contact numbers and email addresses.",
        iconSrc: `${ICON_BASE}/docIcon.png`,
        run: { apiRoute: "/api/scrape/document-data-scraper/", fieldKind: "file" },
      },
      {
        slug: "image-data-scraper",
        title: "Image Data Scraper",
        description: "Upload any image to extract text or details efficiently, converting visuals into data.",
        iconSrc: `${ICON_BASE}/imageIcon.png`,
        run: { apiRoute: "/api/scrape/image-data-scraper/", fieldKind: "file" },
      },
    ],
  },
  {
    id: "domain-tools",
    label: "Domain Tools",
    tools: [
      {
        slug: "whois-domain-database",
        title: "Whois Domain Database",
        description:
          "Access 8+ years of Whois data via API, filtered year-wise, for an extensive and convenient search experience.",
        iconSrc: `${ICON_BASE}/domain.png`,
        run: { apiRoute: "/api/scrape/whois/", fieldKind: "domain" },
      },
    ],
  },
  {
    id: "custom-api",
    label: "Custom API Connector",
    tools: [
      {
        slug: "custom-api-connector",
        title: "Custom API Connector",
        description:
          "Point ScrapeGenius at any third-party API — paste a URL and key, map the response fields, and run it like any other scraper.",
        iconSrc: `${ICON_BASE}/verify.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/custom-api" },
      },
    ],
  },
  {
    id: "ai-enrichment",
    label: "AI Enrichment",
    tools: [
      {
        slug: "ai-lead-enrichment",
        title: "AI Lead Enrichment & Scoring",
        description:
          "Score any scraped lead as Hot/Warm/Cold based on email type, missing website, and social presence, and mine its reviews for pain points to lead your sales pitch with.",
        iconSrc: `${ICON_BASE}/verify.png`,
        isNew: true,
        run: { customPage: "/dashboard/tools/ai-enrichment" },
      },
    ],
  },
];

export function findTool(slug: string): ToolTemplate | undefined {
  for (const category of TOOL_CATEGORIES) {
    const tool = category.tools.find((t) => t.slug === slug);
    if (tool) return tool;
  }
  return undefined;
}