import {
  faHouse,
  faCircleDot,
  faMagnifyingGlass,
  faBullhorn,
  faShareNodes,
  faUserGroup,
  faServer,
  faFileLines,
  faGlobe,
  faTags,
  faListCheck,
  faFileExport,
  faUserShield,
  faRobot,
  faKey,
  faBullseye,
  faMapLocationDot,
  faPlug,
} from "@fortawesome/free-solid-svg-icons";
import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";

export interface NavItem {
  label: string;
  labelKey?: string;
  href: string;
  icon: IconDefinition;
  adminOnly?: boolean;
}

export const MAIN_NAV: NavItem[] = [
  { label: "Dashboard", labelKey: "nav.dashboard", href: "/dashboard", icon: faHouse },
  {
    label: "Live Tools",
    labelKey: "nav.liveTools",
    href: "/dashboard/tools/live-website-scraper",
    icon: faCircleDot,
  },
  {
    label: "Search Engine Scraper",
    labelKey: "nav.searchEngines",
    href: "/dashboard/tools/google-search-scraper",
    icon: faMagnifyingGlass,
  },
  {
    label: "Social Media Scraper",
    labelKey: "nav.socialMedia",
    href: "/dashboard/tools/social/facebook",
    icon: faBullhorn,
  },
  { label: "Classified & Haraj", labelKey: "nav.classified", href: "/dashboard/tools/classified", icon: faTags },
  {
    label: "Google Maps Business Extractor",
    labelKey: "nav.googleMapsPro",
    href: "/dashboard/tools/google-maps-pro",
    icon: faMapLocationDot,
  },
  {
    label: "B2B Directory Scraper",
    labelKey: "nav.directories",
    href: "/dashboard/tools/business-directory-scraper",
    icon: faShareNodes,
  },
  {
    label: "Bulk Contact Scraper",
    labelKey: "nav.contactScrapers",
    href: "/dashboard/tools/email-scraper",
    icon: faUserGroup,
  },
  { label: "Web Scraper", labelKey: "nav.webScraper", href: "/dashboard/tools/website-data-scraper", icon: faServer },
  {
    label: "File-Based Scraper",
    labelKey: "nav.documentScraper",
    href: "/dashboard/tools/document-data-scraper",
    icon: faFileLines,
  },
  {
    label: "Domain Tools",
    labelKey: "nav.domainTools",
    href: "/dashboard/tools/whois-domain-database",
    icon: faGlobe,
  },
  {
    label: "Custom API Connector",
    labelKey: "nav.customApi",
    href: "/dashboard/tools/custom-api",
    icon: faPlug,
  },
  { label: "AI Enrichment", labelKey: "nav.aiEnrichment", href: "/dashboard/tools/ai-enrichment", icon: faRobot },
  {
    label: "AI Lead Qualifier",
    labelKey: "nav.leadQualifier",
    href: "/dashboard/tools/lead-qualifier",
    icon: faBullseye,
  },
  { label: "API Keys", labelKey: "nav.apiKeys", href: "/dashboard/settings/api-keys", icon: faKey },
  { label: "Job Queue", labelKey: "nav.jobs", href: "/dashboard/jobs", icon: faListCheck },
  { label: "Export Manager", labelKey: "nav.export", href: "/dashboard/export", icon: faFileExport },
  { label: "Admin", labelKey: "nav.admin", href: "/dashboard/admin", icon: faUserShield, adminOnly: true },
];
