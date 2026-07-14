exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("user_search_usage_logs");
  if (exists) return;

  await knex.schema.createTable("user_search_usage_logs", (table) => {
    table.increments("id").primary();
    table
      .integer("user_search_key_id")
      .unsigned()
      .notNullable()
      .references("id")
      .inTable("user_search_keys")
      .onDelete("CASCADE");
    table.date("date").notNullable();
    table.integer("request_count").unsigned().notNullable().defaultTo(0);
    table.timestamps(true, true);

    table.unique(["user_search_key_id", "date"], "uniq_user_search_usage_key_date");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("user_search_usage_logs");
};
