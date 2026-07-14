/**
 * Migration: Create classified_results table
 * 
 * Stores results from Haraj, OpenSooq, Dubizzle, OLX, and all other
 * supported classified / marketplace sites. Each row is one ad listing.
 */
exports.up = async function (knex) {
  const CLASSIFIED_SOURCES = [
    "HARAJ", "OPENSOOQ", "MSTAML", "BEY3", "AQARI", "EXPATRIATES",
    "ALMURABA", "ALWASEET", "DUBIZZLE", "OLX", "TAYARA", "MUBAWAB",
    "FORSALE", "QATARLIVING", "MOURJAN", "MZAD", "PROPERTYFINDER",
    "BAYUT", "MOTORY", "SYARAH", "MAROOF", "CLASSIFIED_GENERIC",
  ];

  await knex.schema.createTable("classified_results", (t) => {
    t.increments("id").primary();
    t.integer("job_id").unsigned().notNullable()
      .references("id").inTable("scrape_jobs").onDelete("CASCADE");
    // Which classified site this result came from
    t.enum("source", CLASSIFIED_SOURCES).notNullable();
    // The keyword that produced this result
    t.string("keyword", 500).notNullable();
    // Post data extracted from the listing
    t.string("post_title", 1000).nullable();
    t.string("post_link", 2048).nullable();
    t.string("phone", 100).nullable();
    t.string("email", 255).nullable();
    t.string("price", 100).nullable();
    t.string("location", 500).nullable();
    t.timestamp("post_date").nullable();
    // Full raw JSON for any un-normalized fields
    t.json("raw_data").nullable();
    t.timestamp("scraped_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["job_id"]);
    t.index(["source", "keyword"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("classified_results");
};
