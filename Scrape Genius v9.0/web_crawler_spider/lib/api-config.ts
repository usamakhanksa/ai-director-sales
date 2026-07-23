export interface ApiRouteConfig {
  path: string;
  method?: string;
  description: string;
  requiresAuth: boolean;
}

export interface ApiServiceConfig {
  serviceName: string;
  baseUrl: string;
  routes: ApiRouteConfig[];
}

export const API_SERVICES: ApiServiceConfig[] = [
  {
    serviceName: "Authentication",
    baseUrl: "/api/auth",
    routes: [
      { path: "/login", method: "POST", description: "User login", requiresAuth: false },
      { path: "/signup", method: "POST", description: "User registration", requiresAuth: false },
    ],
  },
  {
    serviceName: "Dashboard",
    baseUrl: "/api/dashboard",
    routes: [
      { path: "/stats", method: "GET", description: "Get dashboard statistics", requiresAuth: true },
    ],
  },
  {
    serviceName: "Scraping",
    baseUrl: "/api/scrape",
    routes: [
      { path: "/google-search", method: "POST", description: "Google search scraper", requiresAuth: true },
      { path: "/bing-search", method: "POST", description: "Bing search scraper", requiresAuth: true },
      { path: "/yahoo-search", method: "POST", description: "Yahoo search scraper", requiresAuth: true },
      { path: "/duckduckgo-search", method: "POST", description: "DuckDuckGo search scraper", requiresAuth: true },
      { path: "/google-maps", method: "POST", description: "Google Maps scraper", requiresAuth: true },
      { path: "/contact-scraper", method: "POST", description: "Contact scraper", requiresAuth: true },
      { path: "/website-data-scraper", method: "POST", description: "Website data scraper", requiresAuth: true },
      { path: "/website-data-center", method: "POST", description: "Website data center", requiresAuth: true },
      { path: "/business-directory", method: "POST", description: "Business directory scraper", requiresAuth: true },
      { path: "/document-data-scraper", method: "POST", description: "Document data scraper", requiresAuth: true },
      { path: "/image-data-scraper", method: "POST", description: "Image data scraper", requiresAuth: true },
      { path: "/indiamart", method: "POST", description: "IndiaMART scraper", requiresAuth: true },
      { path: "/justdial", method: "POST", description: "Justdial scraper", requiresAuth: true },
      { path: "/sulekha", method: "POST", description: "Sulekha scraper", requiresAuth: true },
      { path: "/whois", method: "POST", description: "WHOIS domain database", requiresAuth: true },
    ],
  },
  {
    serviceName: "Social Media",
    baseUrl: "/api/social",
    routes: [
      { path: "/facebook", method: "POST", description: "Facebook scraper", requiresAuth: true },
      { path: "/instagram", method: "POST", description: "Instagram scraper", requiresAuth: true },
      { path: "/linkedin", method: "POST", description: "LinkedIn scraper", requiresAuth: true },
      { path: "/twitter", method: "POST", description: "Twitter/X scraper", requiresAuth: true },
    ],
  },
  {
    serviceName: "Jobs & Export",
    baseUrl: "/api",
    routes: [
      { path: "/jobs", method: "GET", description: "Job queue management", requiresAuth: true },
      { path: "/export", method: "POST", description: "Export management", requiresAuth: true },
      { path: "/keys", method: "GET", description: "API keys management", requiresAuth: true },
    ],
  },
  {
    serviceName: "AI Services",
    baseUrl: "/api",
    routes: [
      { path: "/ai-enrichment", method: "POST", description: "AI lead enrichment", requiresAuth: true },
      { path: "/lead-qualifier", method: "POST", description: "AI lead qualifier", requiresAuth: true },
    ],
  },
  {
    serviceName: "Utilities",
    baseUrl: "/api",
    routes: [
      { path: "/get_keys", method: "GET", description: "Get API keys", requiresAuth: true },
      { path: "/update_usage", method: "POST", description: "Update usage logs", requiresAuth: true },
      { path: "/saved", method: "GET", description: "Saved searches", requiresAuth: true },
      { path: "/dorks", method: "POST", description: "Dork search functionality", requiresAuth: true },
      { path: "/professional-contacts", method: "POST", description: "Professional contacts", requiresAuth: true },
      { path: "/classified", method: "POST", description: "Classified ads scraper", requiresAuth: true },
    ],
  },
];

export const getAllApiRoutes = (): ApiRouteConfig[] => {
  return API_SERVICES.flatMap(service => 
    service.routes.map(route => ({
      ...route,
      path: `${service.baseUrl}${route.path}`
    }))
  );
};

export const getApiRouteByPath = (path: string): ApiRouteConfig | undefined => {
  return getAllApiRoutes().find(route => route.path === path);
};