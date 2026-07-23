import {
  faHouse,
  faCircleDot,
  faMagnifyingGlass,
  faBullhorn,
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
  faLayerGroup,
  faGear,
  faWrench,
  faChartSimple,
  faDatabase,
  faAddressCard,
  faUserCircle,
  faIdBadge,
  faNewspaper,
  faEnvelopeCircleCheck,
  faSatelliteDish,
} from "@fortawesome/free-solid-svg-icons";
import {
  faInstagram,
  faTwitter,
  faLinkedin,
} from "@fortawesome/free-brands-svg-icons";
import type { IconDefinition } from "@fortawesome/fontawesome-svg-core";

export interface NavItem {
  label: string;
  labelKey?: string;
  href: string;
  icon: IconDefinition;
  adminOnly?: boolean;
  category?: string; // Added category for better organization
}

// Dashboard navigation items - UI routes that appear in the sidebar
export const DASHBOARD_NAVIGATION: NavItem[] = [
  { 
    label: "Dashboard", 
    labelKey: "nav.dashboard", 
    href: "/dashboard", 
    icon: faHouse,
    category: "main"
  },
  {
    label: "Live Tools",
    labelKey: "nav.liveTools",
    href: "/dashboard/tools/live-website-scraper",
    icon: faCircleDot,
    category: "tools"
  },
  {
    label: "Search Engine Scraper",
    labelKey: "nav.searchEngines",
    href: "/dashboard/tools/google-search-scraper",
    icon: faMagnifyingGlass,
    category: "tools"
  },
  {
    label: "Multi-Engine Search",
    labelKey: "nav.unifiedSearch",
    href: "/dashboard/tools/unified-search",
    icon: faLayerGroup,
    category: "tools"
  },
  {
    label: "Social Media Scraper",
    labelKey: "nav.socialMedia",
    href: "/dashboard/tools/social/facebook",
    icon: faBullhorn,
    category: "tools"
  },
  {
    label: "Instagram Scraper",
    labelKey: "nav.instagram",
    href: "/dashboard/tools/social/instagram",
    icon: faInstagram,
    category: "tools"
  },
  {
    label: "Twitter / X Scraper",
    labelKey: "nav.twitter",
    href: "/dashboard/tools/social/twitter",
    icon: faTwitter,
    category: "tools"
  },
  {
    label: "LinkedIn Profile Scraper",
    labelKey: "nav.linkedin",
    href: "/dashboard/tools/social/linkedin",
    icon: faLinkedin,
    category: "tools"
  },
  {
    label: "LinkedIn Search",
    labelKey: "nav.linkedinSearch",
    href: "/dashboard/tools/social/linkedin/search",
    icon: faLinkedin,
    category: "tools"
  },
  {
    label: "Classified & Haraj", 
    labelKey: "nav.classified", 
    href: "/dashboard/tools/classified", 
    icon: faTags,
    category: "tools"
  },
  {
    label: "Google Maps Business Extractor",
    labelKey: "nav.googleMapsPro",
    href: "/dashboard/tools/google-maps-pro",
    icon: faMapLocationDot,
    category: "tools"
  },
  {
    label: "B2B Directory Scraper",
    labelKey: "nav.directories",
    href: "/dashboard/tools/business-directory-scraper",
    icon: faDatabase, // Changed to a more appropriate icon
    category: "tools"
  },
  {
    label: "Bulk Contact Scraper",
    labelKey: "nav.contactScrapers",
    href: "/dashboard/tools/email-scraper",
    icon: faUserGroup,
    category: "tools"
  },
  { 
    label: "Web Scraper", 
    labelKey: "nav.webScraper", 
    href: "/dashboard/tools/website-data-scraper", 
    icon: faServer,
    category: "tools"
  },
  {
    label: "File-Based Scraper",
    labelKey: "nav.documentScraper",
    href: "/dashboard/tools/document-data-scraper",
    icon: faFileLines,
    category: "tools"
  },
  {
    label: "Domain Tools",
    labelKey: "nav.domainTools",
    href: "/dashboard/tools/whois-domain-database",
    icon: faGlobe,
    category: "tools"
  },
  {
    label: "Custom API Connector",
    labelKey: "nav.customApi",
    href: "/dashboard/tools/custom-api",
    icon: faPlug,
    category: "tools"
  },
  { 
    label: "AI Enrichment", 
    labelKey: "nav.aiEnrichment", 
    href: "/dashboard/tools/ai-enrichment", 
    icon: faRobot,
    category: "tools"
  },
  {
    label: "AI Lead Qualifier",
    labelKey: "nav.leadQualifier",
    href: "/dashboard/tools/lead-qualifier",
    icon: faBullseye,
    category: "tools"
  },
  {
    label: "Professional Contact Finder",
    labelKey: "nav.professionalContacts",
    href: "/dashboard/tools/professional-contacts",
    icon: faAddressCard,
    category: "tools"
  },
  {
    label: "Zero-Cost AI Scraper",
    labelKey: "nav.aiScraper",
    href: "/dashboard/tools/ai-scraper",
    icon: faRobot,
    category: "tools"
  },
  {
    label: "Google News Scraper",
    labelKey: "nav.googleNews",
    href: "/dashboard/tools/google-news",
    icon: faNewspaper,
    category: "tools"
  },
  {
    label: "Email Verifier",
    labelKey: "nav.emailVerifier",
    href: "/dashboard/tools/email-verifier",
    icon: faEnvelopeCircleCheck,
    category: "tools"
  },
  {
    label: "API Keys",
    labelKey: "nav.apiKeys",
    href: "/dashboard/settings/api-keys",
    icon: faKey,
    category: "settings"
  },
  {
    label: "Profile",
    labelKey: "nav.profile",
    href: "/dashboard/settings/profile",
    icon: faUserCircle,
    category: "settings"
  },
  {
    label: "Account",
    labelKey: "nav.account",
    href: "/dashboard/settings/account",
    icon: faIdBadge,
    category: "settings"
  },
  {
    label: "Webhooks",
    labelKey: "nav.webhooks",
    href: "/dashboard/settings/webhooks",
    icon: faSatelliteDish,
    category: "settings"
  },
  { 
    label: "Job Queue", 
    labelKey: "nav.jobs", 
    href: "/dashboard/jobs", 
    icon: faListCheck,
    category: "main"
  },
  { 
    label: "Export Manager", 
    labelKey: "nav.export", 
    href: "/dashboard/export", 
    icon: faFileExport,
    category: "main"
  },
  { 
    label: "Settings", 
    labelKey: "nav.settings", 
    href: "/dashboard/settings", 
    icon: faGear,
    category: "settings"
  },
  { 
    label: "Admin", 
    labelKey: "nav.admin", 
    href: "/dashboard/admin", 
    icon: faUserShield, 
    adminOnly: true,
    category: "admin"
  },
];

// Separate export for the main navigation that was previously used
export const MAIN_NAV: NavItem[] = DASHBOARD_NAVIGATION;

// New organized navigation by category
export const NAVIGATION_BY_CATEGORY = {
  main: DASHBOARD_NAVIGATION.filter(item => item.category === 'main'),
  tools: DASHBOARD_NAVIGATION.filter(item => item.category === 'tools'),
  settings: DASHBOARD_NAVIGATION.filter(item => item.category === 'settings'),
  admin: DASHBOARD_NAVIGATION.filter(item => item.category === 'admin'),
};

// Utility functions for navigation
export const getNavigationItems = (role: string = "USER"): NavItem[] => {
  return DASHBOARD_NAVIGATION.filter((item) => !item.adminOnly || role === "ADMIN");
};

export const getNavigationCategories = (): string[] => {
  return Array.from(new Set(DASHBOARD_NAVIGATION.map(item => item.category)));
};

export const getNavigationByCategory = (category: string, role: string = "USER"): NavItem[] => {
  return DASHBOARD_NAVIGATION
    .filter(item => item.category === category)
    .filter(item => !item.adminOnly || role === "ADMIN");
};