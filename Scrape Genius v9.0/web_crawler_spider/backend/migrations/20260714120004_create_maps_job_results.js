/**
 * Migration: Create maps_job_results table
 * 
 * Replaces the old maps_results table with a richer schema that includes:
 * - Job linkage (scrape_jobs FK)
 * - All social media profiles discovered via website deep-visit
 * - Per-keyword tagging for multi-keyword campaigns
 */
exports.up = async function (knex) {
  await knex.schema.createTable("maps_job_results", (t) => {
    t.increments("id").primary();
    t.integer("job_id").unsigned().notNullable()
      .references("id").inTable("scrape_jobs").onDelete("CASCADE");
    // The keyword / location query that produced this listing
    t.string("keyword", 500).notNullable();
    // Core Google Maps fields
    t.string("business_name", 500).nullable();
    t.string("phone", 100).nullable();
    t.string("address", 1000).nullable();
    t.string("website", 2048).nullable();
    t.string("email", 255).nullable();
    t.float("rating").nullable();
    t.integer("reviews_count").nullable();
    t.string("category", 200).nullable();
    t.float("latitude").nullable();
    t.float("longitude").nullable();
    // Social profiles found by deep-visiting the business website
    t.string("instagram", 500).nullable();
    t.string("facebook", 500).nullable();
    t.string("linkedin", 500).nullable();
    t.string("twitter", 500).nullable();
    // Full raw JSON
    t.json("raw_data").nullable();
    t.timestamp("scraped_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["job_id"]);
    t.index(["keyword"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("maps_job_results");
};
