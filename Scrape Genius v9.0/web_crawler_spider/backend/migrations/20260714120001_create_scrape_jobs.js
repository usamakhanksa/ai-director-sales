/**
 * Migration: Create scrape_jobs table
 * 
 * Stores all scraping job records — one row per campaign.
 * The job manager reads/writes this table to track queue status and progress.
 * Workers update `status`, `progress`, `extracted_count` during execution.
 */
exports.up = async function (knex) {
  await knex.schema.createTable("scrape_jobs", (t) => {
    t.increments("id").primary();
    t.integer("user_id").unsigned().notNullable()
      .references("id").inTable("users").onDelete("CASCADE");
    // The scraping module identifier: "facebook", "linkedin", "twitter",
    // "google_maps", "website_crawler", "haraj", "classified_generic", etc.
    t.string("module", 50).notNullable();
    // JSON array of keyword strings to search for
    t.json("keywords").notNullable();
    // Optional JSON config (country, engines, proxy, depth, etc.)
    t.json("config").nullable();
    // Job lifecycle status
    t.enum("status", ["QUEUED", "RUNNING", "DONE", "FAILED", "CANCELLED"])
      .defaultTo("QUEUED").notNullable();
    // 0-100 progress percentage updated by the worker
    t.integer("progress").defaultTo(0).notNullable();
    // Total records extracted so far
    t.integer("extracted_count").defaultTo(0).notNullable();
    // Error message if status=FAILED
    t.string("error_message", 1000).nullable();
    t.timestamp("started_at").nullable();
    t.timestamp("completed_at").nullable();
    t.timestamp("created_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["user_id", "status"]);
    t.index(["created_at"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("scrape_jobs");
};
