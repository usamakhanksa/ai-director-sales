/**
 * Migration: Create social_results table
 * 
 * Stores structured results from Facebook, LinkedIn, Twitter, and Instagram
 * scraping jobs. Each row is one contact record extracted for a given keyword.
 */
exports.up = async function (knex) {
  await knex.schema.createTable("social_results", (t) => {
    t.increments("id").primary();
    t.integer("job_id").unsigned().notNullable()
      .references("id").inTable("scrape_jobs").onDelete("CASCADE");
    // Which social platform this result came from
    t.enum("source", ["FACEBOOK", "LINKEDIN", "TWITTER", "INSTAGRAM"])
      .notNullable();
    // The keyword that produced this result
    t.string("keyword", 500).notNullable();
    // Extracted contact fields
    t.string("name", 500).nullable();
    t.string("phone", 100).nullable();
    t.string("email", 255).nullable();
    t.string("address", 1000).nullable();
    t.string("title", 500).nullable();        // Job title / page title
    t.text("description").nullable();         // About text / bio
    t.string("profile_url", 2048).nullable(); // Direct link to profile/page
    // Platform-specific extras
    t.integer("followers").nullable();
    t.text("tweet_text").nullable();
    t.timestamp("post_date").nullable();
    // Full raw JSON for any un-normalized fields
    t.json("raw_data").nullable();
    t.timestamp("scraped_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["job_id"]);
    t.index(["source", "keyword"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("social_results");
};
