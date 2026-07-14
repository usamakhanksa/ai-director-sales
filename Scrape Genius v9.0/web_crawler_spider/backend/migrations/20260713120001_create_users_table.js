exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("users");
  if (exists) return;

  await knex.schema.createTable("users", (table) => {
    table.increments("id").primary();
    table.string("name", 191).notNullable();
    table.string("email", 191).notNullable().unique();
    table.string("password_hash", 255).nullable();
    table.string("country", 100).nullable();
    table.boolean("verified").notNullable().defaultTo(false);
    table.string("verification_code", 10).nullable();
    table.timestamp("verification_code_expires_at").nullable();
    table.string("reset_token", 191).nullable();
    table.timestamp("reset_token_expires_at").nullable();
    table.string("purchase_code", 191).nullable();
    table.boolean("purchase_code_verified").notNullable().defaultTo(false);
    table.boolean("admin").notNullable().defaultTo(false);
    table.string("google_id", 191).nullable().unique();
    table.timestamps(true, true);

    table.index(["email"], "idx_users_email");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("users");
};
