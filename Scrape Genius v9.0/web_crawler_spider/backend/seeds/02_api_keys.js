exports.seed = async function (knex) {
  const count = await knex("api_keys").count("id as c").first();
  if (Number(count.c) > 0) return;

  await knex("api_keys").insert([
    {
      key: process.env.GOOGLE_API_FALLBACK_KEY || "REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_1",
      cx: process.env.GOOGLE_API_FALLBACK_CX || "REPLACE_WITH_REAL_SEARCH_ENGINE_ID_1",
      provider: "google_custom_search",
      is_active: true,
      daily_limit: 100,
    },
    {
      key: "REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_2",
      cx: "REPLACE_WITH_REAL_SEARCH_ENGINE_ID_2",
      provider: "google_custom_search",
      is_active: true,
      daily_limit: 100,
    },
    {
      // Pseudo key so /v1/search/maps has a usage_logs row to reserve against.
      // Maps scraping (Playwright) needs no real API key/cx, unlike Custom Search.
      key: "local-maps-scraper",
      cx: "n/a",
      provider: "maps_scraper",
      is_active: true,
      daily_limit: 1000,
    },
  ]);
};
