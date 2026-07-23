/**
 * Migration: Create linkedin_profiles table
 * 
 * Stores detailed LinkedIn profile information including contact details when available
 */
exports.up = async function (knex) {
  await knex.schema.createTable("linkedin_profiles", (t) => {
    t.increments("id").primary();
    t.integer("job_id").unsigned().notNullable()
      .references("id").inTable("scrape_jobs").onDelete("CASCADE");
    // 768 chars (not 2048) so the unique index fits MySQL's 3072-byte max key
    // length under utf8mb4 (768 * 4 = 3072) — plenty for real LinkedIn URLs.
    t.string("profile_url", 768).notNullable().unique(); // LinkedIn profile URL
    t.string("full_name", 500).nullable(); // Full name of the profile owner
    t.string("first_name", 255).nullable(); // First name
    t.string("last_name", 255).nullable(); // Last name
    t.string("title", 500).nullable(); // Job title
    t.string("location", 255).nullable(); // Location
    t.text("description").nullable(); // About/description section
    t.string("photo_url", 2048).nullable(); // Profile photo URL
    t.string("email", 255).nullable(); // Email if publicly available
    t.string("phone", 100).nullable(); // Phone if publicly available
    t.json("experiences").nullable(); // JSON array of work experiences
    t.json("education").nullable(); // JSON array of education history
    t.json("skills").nullable(); // JSON array of skills
    t.json("volunteer_experiences").nullable(); // JSON array of volunteer experiences
    t.json("certifications").nullable(); // JSON array of certifications
    t.json("languages").nullable(); // JSON array of languages
    t.string("industry", 255).nullable(); // Industry field
    t.integer("connections_count").nullable(); // Number of connections (if available)
    t.string("premium_account_type", 255).nullable(); // Premium account type
    t.boolean("is_open_to_work").defaultTo(false); // Open to work indicator
    t.boolean("is_following").defaultTo(false); // Following indicator
    t.boolean("is_connection").defaultTo(false); // Connection indicator
    t.json("raw_data").nullable(); // Full raw JSON for any un-normalized fields
    t.timestamp("scraped_at").defaultTo(knex.fn.now()).notNullable();
    t.timestamp("updated_at").defaultTo(knex.fn.now()).notNullable();

    t.index(["job_id"]);
    // profile_url is already indexed via .unique() above — no separate index needed.
    t.index(["email"]);
    t.index(["phone"]);
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("linkedin_profiles");
};