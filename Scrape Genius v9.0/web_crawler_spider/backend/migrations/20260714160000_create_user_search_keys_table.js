exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("user_search_keys");
  if (exists) return;

  await knex.schema.createTable("user_search_keys", (table) => {
    table.increments("id").primary();
    table
      .integer("user_id")
      .unsigned()
      .notNullable()
      .references("id")
      .inTable("users")
      .onDelete("CASCADE");
    table.string("google_api_key", 191).notNullable();
    table.string("search_engine_id", 191).notNullable();
    table.integer("daily_limit").unsigned().notNullable().defaultTo(100);
    table.boolean("is_active").notNullable().defaultTo(true);
    table.timestamps(true, true);

    table.index(["user_id", "is_active"], "idx_user_search_keys_user_active");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("user_search_keys");
};
