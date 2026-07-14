/**
 * Migration: Create scraper_logs table
 * 
 * Structured log entries emitted by scrapers during job execution.
 * The SSE endpoint streams these to the frontend for real-time progress display.
 * Rows are automatically cleaned up after MAX_LOG_AGE_DAYS (default 7).
 */
exports.up = async function (knex) {
  await knex.schema.createTable("scraper_logs", (t) => {
    t.increments("id").primary();
    t.integer("job_id").unsigned().notNullable()
      .references("id").inTable("scrape_jobs").onDelete("CASCADE");
    // Log severity level
    t.enum("level", ["INFO", "WARN", "ERROR", "DEBUG"]).defaultTo("INFO").notNullable();
    // Human-readable log message
    t.string("message", 2000).notNullable();
    // Optional structured metadata (url, keyword, count, etc.)
    t.json("meta").nullable();
    t.timestamp("created_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["job_id", "created_at"]);
    t.index(["level"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("scraper_logs");
};
