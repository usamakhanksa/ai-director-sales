/**
 * Advanced Search Dork Generation Service
 * 
 * Generates sophisticated search queries for maximum data extraction
 * using Google dorks, Bing searches, and social media patterns.
 */

"use strict";

const db = require("../config/database");

// Dork templates for various purposes
const DORK_TEMPLATES = {
  // Email harvesting patterns
  EMAIL_HARVESTING: [
    'site:facebook.com OR site:linkedin.com "{keyword}" ("gmail.com" OR "yahoo.com" OR "email me at")',
    'site:instagram.com OR site:twitter.com "{keyword}" ("gmail.com" OR "email" OR "contact")',
    'inurl:contact OR inurl:about "{keyword}" ("gmail.com" OR "email")',
    'site:{domain} ("contact" OR "about") ("email" OR "@")'
  ],
  
  // Phone/WhatsApp harvesting
  PHONE_HARVESTING: [
    'site:instagram.com OR site:facebook.com "{keyword}" ("whatsapp" OR "wa.me" OR "{country_code}")',
    'site:haraj.com.sa OR site:opensooq.com "{keyword}" ("جوال" OR "واتساب" OR "whatsapp")',
    '"phone" OR "tel" OR "call" OR "whatsapp" "{keyword}"'
  ],
  
  // Professional network dorks
  PROFESSIONAL_NETWORKS: [
    'site:linkedin.com/in/ "{keyword}" "{location}" ("gmail.com" OR "email")',
    'site:linkedin.com/company/ "{company}" ("ceo" OR "founder" OR "manager")',
    'site:linkedin.com/in ("{title}" OR "{role}") ("{industry}") ("{location}")',
    'site:facebook.com "contact" "{keyword}"'
  ],
  
  // File harvesting (PDFs, documents)
  FILE_HARVESTING: [
    'site:linkedin.com/in/ "{keyword}" filetype:pdf',
    'intitle:"index of" "contacts.csv" OR "leads.xlsx" "{keyword}"',
    'inurl:"/wp-content/uploads/" "{keyword}" filetype:pdf',
    'filetype:pdf "{keyword}" "contact" OR "email"'
  ],
  
  // MENA/Arabic specific patterns
  MENA_ARABIC: [
    'site:haraj.com.sa "{arabic_keyword}" ("جوال" OR "واتساب" OR "whatsapp")',
    'site:opensooq.com "{arabic_keyword}" ("جوال" OR "واتساب")',
    'site:propertyfinder.sa "{keyword}" "for rent" "{location}"',
    '"{arabic_keyword}" "{location}" ("gmail.com" OR "email")'
  ],
  
  // High-intent business patterns
  HIGH_INTENT_BUSINESS: [
    'site:*.sa "{keyword}" "contact us" ("gmail.com" OR "yahoo.com")',
    'site:*.sa "{keyword}" "book now" -"booking.com" -"agoda" -"expedia"',
    'site:tripadvisor.com "{keyword}" "{location}" ("slow check in" OR "lost reservation" OR "billing error")',
    'site:haraj.com.sa "{keyword}" "للايجار" "{location}" "مؤثثة"'
  ]
};

// Geographic coordinates for targeted searches
const GEO_COORDINATES = {
  SAUDI_ARABIA: {
    RIYADH: { lat: 24.7136, lng: 46.6753, zoom: 12 },
    JEDDAH: { lat: 21.5433, lng: 39.1728, zoom: 12 },
    MECCA: { lat: 21.4225, lng: 39.8262, zoom: 12 },
    MEDINA: { lat: 24.5247, lng: 39.5692, zoom: 12 },
    DAMMAM: { lat: 26.4344, lng: 50.0538, zoom: 12 },
    KHOBAR: { lat: 26.2825, lng: 50.2050, zoom: 12 }
  },
  UAE: {
    DUBAI: { lat: 25.2048, lng: 55.2708, zoom: 12 },
    ABU_DHABI: { lat: 24.4539, lng: 54.3773, zoom: 12 },
    SHARJAH: { lat: 25.0752, lng: 55.3167, zoom: 12 }
  },
  EGYPT: {
    CAIRO: { lat: 30.0444, lng: 31.2357, zoom: 12 },
    ALEXANDRIA: { lat: 31.2001, lng: 29.9187, zoom: 12 },
    GIZA: { lat: 30.0131, lng: 31.2089, zoom: 12 }
  },
  KUWAIT: {
    KUWAIT_CITY: { lat: 29.3759, lng: 47.9774, zoom: 12 }
  },
  QATAR: {
    DOHA: { lat: 25.2854, lng: 51.5310, zoom: 12 }
  }
};

/**
 * Generates search dorks based on provided criteria
 */
const generateDorks = (options = {}) => {
  const {
    keyword,
    location,
    country = 'SA',
    intent = 'general',
    platforms = [],
    language = 'en'
  } = options;

  const dorks = [];
  
  // Determine the appropriate template category based on intent
  let templateCategory = 'EMAIL_HARVESTING';
  
  switch (intent.toLowerCase()) {
    case 'email':
    case 'contact':
      templateCategory = 'EMAIL_HARVESTING';
      break;
    case 'phone':
    case 'whatsapp':
      templateCategory = 'PHONE_HARVESTING';
      break;
    case 'professional':
    case 'linkedin':
      templateCategory = 'PROFESSIONAL_NETWORKS';
      break;
    case 'files':
    case 'documents':
      templateCategory = 'FILE_HARVESTING';
      break;
    case 'mena':
    case 'arabic':
      templateCategory = 'MENA_ARABIC';
      break;
    case 'high_intent':
    case 'business':
      templateCategory = 'HIGH_INTENT_BUSINESS';
      break;
    default:
      templateCategory = 'EMAIL_HARVESTING';
  }

  // Get templates for the selected category
  const templates = DORK_TEMPLATES[templateCategory] || DORK_TEMPLATES.EMAIL_HARVESTING;
  
  // Generate dorks using the templates
  templates.forEach(template => {
    let dork = template
      .replace(/{keyword}/g, keyword || '')
      .replace(/{location}/g, location || '')
      .replace(/{country_code}/g, getCountryCode(country))
      .replace(/{country}/g, country)
      .replace(/{domain}/g, getDomainByCountry(country));
    
    // For Arabic content, also generate Arabic variations
    if (language === 'ar' || containsArabic(keyword)) {
      const arabicDork = generateArabicVariations(dork, keyword);
      if (arabicDork) dorks.push(arabicDork);
    }
    
    dorks.push(dork);
  });

  // Add platform-specific dorks if specific platforms are requested
  if (platforms.length > 0) {
    platforms.forEach(platform => {
      const platformDorks = generatePlatformSpecificDorks(platform, keyword, location, country);
      dorks.push(...platformDorks);
    });
  }

  // Remove duplicates and empty strings
  return [...new Set(dorks.filter(d => d.trim() !== ''))];
};

/**
 * Generates platform-specific dorks
 */
const generatePlatformSpecificDorks = (platform, keyword, location, country) => {
  const platformDorks = [];
  
  switch (platform.toLowerCase()) {
    case 'google':
      platformDorks.push(
        `https://www.google.com/search?q=${encodeURIComponent(keyword + (location ? ` ${location}` : ''))}`,
        `https://www.google.com/search?q=${encodeURIComponent(`${keyword} ${location} ${getCountryCode(country)}`)}`
      );
      break;
      
    case 'bing':
      platformDorks.push(
        `https://www.bing.com/search?q=${encodeURIComponent(keyword + (location ? ` ${location}` : ''))}`
      );
      break;
      
    case 'yahoo':
      platformDorks.push(
        `https://search.yahoo.com/search?p=${encodeURIComponent(keyword + (location ? ` ${location}` : ''))}`
      );
      break;
      
    case 'duckduckgo':
      platformDorks.push(
        `https://html.duckduckgo.com/html/?q=${encodeURIComponent(keyword + (location ? ` ${location}` : ''))}`
      );
      break;
      
    case 'google_maps':
      if (location && GEO_COORDINATES[country]?.[location.toUpperCase()]) {
        const coords = GEO_COORDINATES[country][location.toUpperCase()];
        platformDorks.push(
          `https://www.google.com/maps/search/${encodeURIComponent(keyword)}/${coords.lat},${coords.lng},${coords.zoom}z`
        );
      } else {
        platformDorks.push(
          `https://www.google.com/maps/search/${encodeURIComponent(keyword + (location ? ` ${location}` : ''))}`
        );
      }
      break;
      
    case 'linkedin':
      platformDorks.push(
        `https://www.google.com/search?q=site:linkedin.com/in/ "${encodeURIComponent(keyword)}" "${encodeURIComponent(location)}"`
      );
      break;
      
    case 'facebook':
      platformDorks.push(
        `https://www.google.com/search?q=site:facebook.com "${encodeURIComponent(keyword)}" "${encodeURIComponent(location)}"`
      );
      break;
      
    case 'twitter':
      platformDorks.push(
        `https://www.google.com/search?q=site:twitter.com "${encodeURIComponent(keyword)}" "${encodeURIComponent(location)}"`
      );
      break;
  }
  
  return platformDorks;
};

/**
 * Generates Arabic variations of dorks
 */
const generateArabicVariations = (dork, keyword) => {
  if (!containsArabic(keyword)) return null;
  
  // Convert Arabic keywords to URL encoded format
  const arabicEncoded = encodeURIComponent(keyword);
  return dork.replace(keyword, arabicEncoded);
};

/**
 * Checks if a string contains Arabic characters
 */
const containsArabic = (str) => {
  const arabicRegex = /[\u0600-\u06FF]/;
  return arabicRegex.test(str);
};

/**
 * Gets country code based on country abbreviation
 */
const getCountryCode = (country) => {
  const codes = {
    'SA': '+966',  // Saudi Arabia
    'EG': '+20',   // Egypt
    'KW': '+965',  // Kuwait
    'QA': '+974',  // Qatar
    'AE': '+971',  // UAE
    'JO': '+962',  // Jordan
    'LB': '+961',  // Lebanon
    'MA': '+212',  // Morocco
    'TN': '+216'   // Tunisia
  };
  return codes[country] || '';
};

/**
 * Gets domain based on country
 */
const getDomainByCountry = (country) => {
  const domains = {
    'SA': '.sa',
    'EG': '.eg',
    'KW': '.kw',
    'QA': '.qa',
    'AE': '.ae',
    'JO': '.jo',
    'LB': '.lb',
    'MA': '.ma',
    'TN': '.tn'
  };
  return domains[country] || '.com';
};

/**
 * Saves generated dorks to database for tracking
 */
const saveDorkHistory = async (userId, dorks, options) => {
  try {
    const dorkHistory = {
      user_id: userId,
      dorks: JSON.stringify(dorks),
      options: JSON.stringify(options),
      created_at: new Date()
    };
    
    await db('dork_history').insert(dorkHistory);
  } catch (error) {
    console.error('Error saving dork history:', error);
  }
};

module.exports = {
  generateDorks,
  generatePlatformSpecificDorks,
  saveDorkHistory,
  GEO_COORDINATES,
  DORK_TEMPLATES
};