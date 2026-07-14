export type Locale = "en" | "ar";

export const LOCALE_DIR: Record<Locale, "ltr" | "rtl"> = {
  en: "ltr",
  ar: "rtl",
};

export const LOCALE_LABEL: Record<Locale, string> = {
  en: "English",
  ar: "العربية",
};

/** Flat dot-path keyed dictionary — one entry per UI string, EN + AR side by side for easy review. */
export const STRINGS: Record<string, Record<Locale, string>> = {
  // Nav
  "nav.dashboard": { en: "Dashboard", ar: "لوحة التحكم" },
  "nav.liveTools": { en: "Live Tools", ar: "الأدوات المباشرة" },
  "nav.searchEngines": { en: "Search Engine Scraper", ar: "استخراج محركات البحث" },
  "nav.socialMedia": { en: "Social Media Scraper", ar: "استخراج وسائل التواصل" },
  "nav.classified": { en: "Classified & Haraj", ar: "حراج والإعلانات المبوبة" },
  "nav.directories": { en: "B2B Directory Scraper", ar: "استخراج الأدلة التجارية" },
  "nav.contactScrapers": { en: "Bulk Contact Scraper", ar: "استخراج جهات الاتصال بالجملة" },
  "nav.webScraper": { en: "Web Scraper", ar: "استخراج المواقع" },
  "nav.documentScraper": { en: "File-Based Scraper", ar: "استخراج من الملفات" },
  "nav.domainTools": { en: "Domain Tools", ar: "أدوات النطاقات" },
  "nav.aiEnrichment": { en: "AI Enrichment", ar: "التحسين بالذكاء الاصطناعي" },
  "nav.googleMapsPro": { en: "Google Maps Business Extractor", ar: "مستخرج أعمال خرائط جوجل" },
  "nav.customApi": { en: "Custom API Connector", ar: "موصل API مخصص" },
  "nav.leadQualifier": { en: "AI Lead Qualifier", ar: "مؤهل العملاء بالذكاء الاصطناعي" },
  "nav.apiKeys": { en: "API Keys", ar: "مفاتيح API" },
  "nav.jobs": { en: "Job Queue", ar: "قائمة المهام" },
  "nav.export": { en: "Export Manager", ar: "مدير التصدير" },
  "nav.admin": { en: "Admin", ar: "الإدارة" },
  "nav.main": { en: "Main", ar: "الرئيسية" },
  "nav.support": { en: "Support", ar: "الدعم" },
  "nav.settings": { en: "Settings", ar: "الإعدادات" },
  "nav.callUs": { en: "Call Us", ar: "اتصل بنا" },
  "nav.emailUs": { en: "Email Us", ar: "راسلنا" },
  "nav.profile": { en: "Profile", ar: "الملف الشخصي" },

  // Common actions
  "common.run": { en: "Run Scraper", ar: "تشغيل الاستخراج" },
  "common.running": { en: "Running…", ar: "جارٍ التشغيل…" },
  "common.cancel": { en: "Cancel", ar: "إلغاء" },
  "common.export": { en: "Export", ar: "تصدير" },
  "common.download": { en: "Download", ar: "تحميل" },
  "common.results": { en: "Results", ar: "النتائج" },
  "common.status": { en: "Status", ar: "الحالة" },
  "common.progress": { en: "Progress", ar: "التقدم" },
  "common.keywords": { en: "Keywords", ar: "الكلمات المفتاحية" },
  "common.addKeyword": { en: "Add keyword", ar: "إضافة كلمة مفتاحية" },
  "common.importCsv": { en: "Import from CSV", ar: "استيراد من CSV" },
  "common.clearAll": { en: "Clear all", ar: "مسح الكل" },
  "common.logout": { en: "Log out", ar: "تسجيل الخروج" },
  "common.language": { en: "Language", ar: "اللغة" },
  "common.save": { en: "Save", ar: "حفظ" },
  "common.noResults": { en: "No results yet.", ar: "لا توجد نتائج بعد." },
  "common.viewLogs": { en: "View live logs", ar: "عرض السجلات المباشرة" },
  "common.retry": { en: "Retry", ar: "إعادة المحاولة" },
  "common.close": { en: "Close", ar: "إغلاق" },
  "common.format": { en: "Format", ar: "الصيغة" },
  "common.selectAll": { en: "Select all columns", ar: "تحديد كل الأعمدة" },
  "common.dedupe": { en: "Remove duplicates", ar: "إزالة التكرارات" },
  "common.showAll": { en: "Show all results", ar: "عرض كل النتائج" },
  "common.resultsUnavailable": { en: "Results are unavailable right now.", ar: "النتائج غير متاحة حاليًا." },

  // Job statuses
  "status.QUEUED": { en: "Queued", ar: "في الانتظار" },
  "status.RUNNING": { en: "Running", ar: "قيد التشغيل" },
  "status.DONE": { en: "Done", ar: "مكتمل" },
  "status.FAILED": { en: "Failed", ar: "فشل" },
  "status.CANCELLED": { en: "Cancelled", ar: "ملغى" },

  // Facebook module
  "facebook.title": { en: "Facebook Phones & Emails Extractor", ar: "استخراج أرقام وإيميلات فيسبوك" },
  "facebook.description": {
    en: "Multi-keyword search across public Facebook profiles/pages to extract phones, emails, addresses, titles, and profile links.",
    ar: "بحث متعدد الكلمات المفتاحية عبر صفحات وحسابات فيسبوك العامة لاستخراج الهواتف والإيميلات والعناوين والروابط.",
  },

  // Instagram module
  "instagram.title": { en: "Instagram Profile Scraper", ar: "استخراج بيانات حسابات إنستغرام" },
  "instagram.description": {
    en: "Extract profile information, followers, following, bio, and recent posts from public Instagram accounts.",
    ar: "استخراج معلومات الحساب والمتابعين والمتابَعين والسيرة الذاتية وآخر المنشورات من حسابات إنستغرام العامة.",
  },

  // LinkedIn module
  "linkedin.title": { en: "LinkedIn Email Finder", ar: "أداة البحث عن إيميلات لينكدإن" },
  "linkedin.description": {
    en: "Finds professional emails via Google/Bing/Yahoo dorking across all country domains — no LinkedIn login required.",
    ar: "يبحث عن الإيميلات المهنية عبر جوجل وبينج وياهو في جميع نطاقات الدول — دون الحاجة لتسجيل الدخول إلى لينكدإن.",
  },

  // Twitter module
  "twitter.title": { en: "Twitter/X Comment & Profile Scraper", ar: "استخراج تغريدات وتعليقات تويتر/إكس" },
  "twitter.description": {
    en: "Keyword search across tweets and comments to extract phone numbers, emails, and profile links.",
    ar: "بحث بالكلمات المفتاحية عبر التغريدات والتعليقات لاستخراج أرقام الهواتف والإيميلات وروابط الحسابات.",
  },

  // Classified module
  "classified.title": { en: "Haraj & Classified Sites Scraper", ar: "استخراج حراج والمواقع المبوبة" },
  "classified.description": {
    en: "Keyword scraper for Haraj and 20+ MENA classified sites — extracts post links, phone numbers, and emails.",
    ar: "استخراج بالكلمات المفتاحية من حراج وأكثر من 20 موقع إعلانات مبوبة في المنطقة — يستخرج روابط الإعلانات وأرقام الهواتف والإيميلات.",
  },
  "classified.selectSites": { en: "Select sites to search", ar: "اختر المواقع للبحث فيها" },

  // Google Maps Pro
  "mapsPro.title": { en: "Google Maps Business Extractor Pro", ar: "استخراج بيانات الأعمال من خرائط جوجل" },
  "mapsPro.description": {
    en: "Extracts business name, phone, address, website, email, and social profiles from Google Maps listings.",
    ar: "يستخرج اسم النشاط التجاري والهاتف والعنوان والموقع الإلكتروني والإيميل والحسابات الاجتماعية من خرائط جوجل.",
  },

  // Jobs page
  "jobs.title": { en: "Job Queue", ar: "قائمة المهام" },
  "jobs.description": {
    en: "All scraping jobs across every module — monitor progress, view live logs, cancel, or jump to results.",
    ar: "جميع مهام الاستخراج من كل الوحدات — تابع التقدم، اطّلع على السجلات المباشرة، ألغِ المهمة، أو انتقل إلى النتائج.",
  },
  "jobs.module": { en: "Module", ar: "الوحدة" },
  "jobs.extracted": { en: "Extracted", ar: "المستخرج" },
  "jobs.started": { en: "Started", ar: "بدأ في" },
  "jobs.viewResults": { en: "View results", ar: "عرض النتائج" },

  // Export page
  "export.title": { en: "Export Manager", ar: "مدير التصدير" },
  "export.description": {
    en: "History of every export you've generated. Re-download anytime.",
    ar: "سجل بكل عمليات التصدير التي أنشأتها. أعد التحميل في أي وقت.",
  },
  "export.rows": { en: "Rows", ar: "الصفوف" },

  // Admin page
  "admin.title": { en: "Admin Panel", ar: "لوحة الإدارة" },
  "admin.users": { en: "Users", ar: "المستخدمون" },
  "admin.apiKeys": { en: "API Keys", ar: "مفاتيح الـ API" },
  "admin.usage": { en: "Usage Analytics", ar: "تحليلات الاستخدام" },
  "admin.purchaseCodes": { en: "Purchase Codes", ar: "أكواد الشراء" },
};

export function translate(locale: Locale, key: string): string {
  return STRINGS[key]?.[locale] ?? key;
}
