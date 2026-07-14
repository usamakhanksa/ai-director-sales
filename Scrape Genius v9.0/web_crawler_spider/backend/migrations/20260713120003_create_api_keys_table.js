exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("api_keys");
  if (exists) return;

  await knex.schema.createTable("api_keys", (table) => {
    table.increments("id").primary();
    table.string("key", 191).notNullable().unique();
    table.string("cx", 191).notNullable();
    table.string("provider", 50).notNullable().defaultTo("google_custom_search");
    table.boolean("is_active").notNullable().defaultTo(true);
    table.integer("daily_limit").unsigned().notNullable().defaultTo(100);
    table.timestamps(true, true);

    table.index(["provider", "is_active"], "idx_api_keys_provider_active");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("api_keys");
};
