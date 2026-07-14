exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("usage_logs");
  if (exists) return;

  await knex.schema.createTable("usage_logs", (table) => {
    table.increments("id").primary();
    table
      .integer("api_key_id")
      .unsigned()
      .notNullable()
      .references("id")
      .inTable("api_keys")
      .onDelete("CASCADE");
    table.date("date").notNullable();
    table.integer("count").unsigned().notNullable().defaultTo(0);
    table.string("search_type", 20).notNullable().defaultTo("search"); // 'search' | 'maps'
    table.timestamps(true, true);

    table.unique(["api_key_id", "date", "search_type"], "uniq_usage_key_date_type");
    table.index(["date", "search_type"], "idx_usage_date_type");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("usage_logs");
};
