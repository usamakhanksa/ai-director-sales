import { getAllApiRoutes, getApiRouteByPath, ApiRouteConfig } from './api-config';
import { findTool, type ToolTemplate } from './tools-data';

export interface RouteMapping {
  uiRoute: string;
  apiRoutes: ApiRouteConfig[];
  tool?: ToolTemplate;
}

export interface RouteCategory {
  category: string;
  uiRoutes: string[];
  apiRoutes: ApiRouteConfig[];
}

/**
 * Maps UI routes in the dashboard to their corresponding API endpoints
 */
export const UI_TO_API_ROUTE_MAP: Record<string, string[]> = {
  // Dashboard routes
  '/dashboard': ['/api/dashboard/stats', '/api/get_keys'],
  
  // Search engine tools
  '/dashboard/tools/google-search-scraper': ['/api/scrape/google-search'],
  '/dashboard/tools/bing-search-scraper': ['/api/scrape/bing-search'],
  '/dashboard/tools/yahoo-search-scraper': ['/api/scrape/yahoo-search'],
  '/dashboard/tools/duckduckgo-search-scraper': ['/api/scrape/duckduckgo-search'],
  '/dashboard/tools/unified-search': ['/api/search/unified'], // hypothetical unified search API
  
  // Maps tools
  '/dashboard/tools/google-maps-scraper': ['/api/scrape/google-maps'],
  '/dashboard/tools/google-maps-pro': ['/api/scrape/google-maps'],
  
  // Social media tools
  '/dashboard/tools/social/facebook': ['/api/social/facebook'],
  '/dashboard/tools/social/instagram': ['/api/social/instagram'],
  '/dashboard/tools/social/linkedin': ['/api/social/linkedin'],
  '/dashboard/tools/social/twitter': ['/api/social/twitter'],
  
  // Contact scraping tools
  '/dashboard/tools/email-scraper': ['/api/scrape/contact-scraper'],
  '/dashboard/tools/phone-number-scraper': ['/api/scrape/contact-scraper'],
  
  // Website scraping tools
  '/dashboard/tools/website-data-scraper': ['/api/scrape/website-data-scraper'],
  '/dashboard/tools/website-data-center': ['/api/scrape/website-data-center'],
  
  // Directory scraping tools
  '/dashboard/tools/business-directory-scraper': ['/api/scrape/business-directory'],
  '/dashboard/tools/indiamart-scraper': ['/api/scrape/indiamart'],
  '/dashboard/tools/justdial-scraper': ['/api/scrape/justdial'],
  '/dashboard/tools/sulekha-scraper': ['/api/scrape/sulekha'],
  
  // Document/file tools
  '/dashboard/tools/document-data-scraper': ['/api/scrape/document-data-scraper'],
  '/dashboard/tools/image-data-scraper': ['/api/scrape/image-data-scraper'],
  
  // Domain tools
  '/dashboard/tools/whois-domain-database': ['/api/scrape/whois'],
  
  // AI tools
  '/dashboard/tools/ai-enrichment': ['/api/ai-enrichment'],
  '/dashboard/tools/lead-qualifier': ['/api/lead-qualifier'],
  
  // CRM tools
  '/dashboard/tools/crm/justdial': ['/api/crm/justdial'],
  '/dashboard/tools/crm/indiamart': ['/api/crm/indiamart'],
  
  // Custom API connector
  '/dashboard/tools/custom-api': ['/api/api-connectors'],
  
  // Other tools
  '/dashboard/tools/classified': ['/api/classified'],
  '/dashboard/tools/live-website-scraper': ['/api/scrape/website-data-scraper'],
  
  // Settings routes
  '/dashboard/settings/api-keys': ['/api/keys', '/api/get_keys'],
  '/dashboard/settings': ['/api/user'],
  
  // Jobs and exports
  '/dashboard/jobs': ['/api/jobs'],
  '/dashboard/export': ['/api/export'],
  
  // Admin routes
  '/dashboard/admin': ['/api/admin/users', '/api/admin/usage', '/api/admin/purchase-codes'],
};

/**
 * Gets all API routes associated with a given UI route
 */
export function getApiRoutesForUi(uiRoute: string): ApiRouteConfig[] {
  const apiPaths = UI_TO_API_ROUTE_MAP[uiRoute] || [];
  return apiPaths.map(path => getApiRouteByPath(path)).filter(Boolean) as ApiRouteConfig[];
}

/**
 * Gets the UI route that corresponds to an API route
 */
export function getUiRouteForApi(apiRoute: string): string | undefined {
  return Object.keys(UI_TO_API_ROUTE_MAP).find(uiRoute => 
    UI_TO_API_ROUTE_MAP[uiRoute].includes(apiRoute)
  );
}

/**
 * Gets all route mappings
 */
export function getAllRouteMappings(): RouteMapping[] {
  return Object.entries(UI_TO_API_ROUTE_MAP).map(([uiRoute, apiRoutes]) => ({
    uiRoute,
    apiRoutes: apiRoutes.map(path => getApiRouteByPath(path)).filter(Boolean) as ApiRouteConfig[],
    tool: findTool(uiRoute.replace('/dashboard/tools/', '').split('/')[0])
  }));
}

/**
 * Groups route mappings by category
 */
export function getRouteMappingsByCategory(): Record<string, RouteMapping[]> {
  const mappings = getAllRouteMappings();
  const categorized: Record<string, RouteMapping[]> = {};

  mappings.forEach(mapping => {
    // Determine category from UI route
    let category = 'general';
    if (mapping.uiRoute.includes('/tools/')) {
      if (mapping.uiRoute.includes('/search-')) {
        category = 'search-engines';
      } else if (mapping.uiRoute.includes('/social/')) {
        category = 'social-media';
      } else if (mapping.uiRoute.includes('/maps-')) {
        category = 'maps';
      } else if (mapping.uiRoute.includes('/email-') || mapping.uiRoute.includes('/contact-')) {
        category = 'contact';
      } else if (mapping.uiRoute.includes('/website-')) {
        category = 'websites';
      } else if (mapping.uiRoute.includes('/directory-')) {
        category = 'directories';
      } else if (mapping.uiRoute.includes('/document-')) {
        category = 'documents';
      } else if (mapping.uiRoute.includes('/crm/')) {
        category = 'crm';
      } else if (mapping.uiRoute.includes('/ai-')) {
        category = 'ai';
      } else {
        category = 'tools';
      }
    } else if (mapping.uiRoute.includes('/settings/')) {
      category = 'settings';
    } else if (mapping.uiRoute.includes('/jobs/')) {
      category = 'jobs';
    } else if (mapping.uiRoute.includes('/admin/')) {
      category = 'admin';
    }

    if (!categorized[category]) {
      categorized[category] = [];
    }
    categorized[category].push(mapping);
  });

  return categorized;
}

/**
 * Validates if a UI route has corresponding API routes
 */
export function isValidRoutePair(uiRoute: string, apiRoute?: string): boolean {
  const apiRoutes = getApiRoutesForUi(uiRoute);
  if (!apiRoute) {
    return apiRoutes.length > 0;
  }
  return apiRoutes.some(route => route.path === apiRoute);
}

/**
 * Gets all available UI routes
 */
export function getAllUiRoutes(): string[] {
  return Object.keys(UI_TO_API_ROUTE_MAP);
}

/**
 * Gets all API routes in the system
 */
export function getAllSystemApiRoutes(): ApiRouteConfig[] {
  return getAllApiRoutes();
}