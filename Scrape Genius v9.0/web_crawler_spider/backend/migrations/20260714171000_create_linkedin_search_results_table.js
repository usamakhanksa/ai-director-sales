/**
 * Migration: Create linkedin_search_results table
 * 
 * Stores LinkedIn search results for keyword-based searches
 */
exports.up = async function (knex) {
  await knex.schema.createTable("linkedin_search_results", (t) => {
    t.increments("id").primary();
    t.integer("job_id").unsigned().notNullable()
      .references("id").inTable("scrape_jobs").onDelete("CASCADE");
    t.string("search_keyword", 500).notNullable(); // The keyword that was searched
    // 768 chars (not 2048) so the index below fits MySQL's 3072-byte max key
    // length under utf8mb4 (768 * 4 = 3072) — plenty for real LinkedIn URLs.
    t.string("profile_url", 768).notNullable(); // LinkedIn profile URL
    t.string("full_name", 500).nullable(); // Full name of the profile owner
    t.string("first_name", 255).nullable(); // First name
    t.string("last_name", 255).nullable(); // Last name
    t.string("title", 500).nullable(); // Job title
    t.string("location", 255).nullable(); // Location
    t.text("description").nullable(); // About/description section
    t.string("photo_url", 2048).nullable(); // Profile photo URL
    t.string("email", 255).nullable(); // Email if publicly available
    t.string("phone", 100).nullable(); // Phone if publicly available
    t.json("raw_data").nullable(); // Full raw JSON for any un-normalized fields
    t.timestamp("scraped_at").defaultTo(knex.fn.now()).notNullable();
    t.timestamp("updated_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["job_id"]);
    t.index(["search_keyword"]);
    t.index(["profile_url"]);
    t.index(["email"]);
    t.index(["phone"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("linkedin_search_results");
};