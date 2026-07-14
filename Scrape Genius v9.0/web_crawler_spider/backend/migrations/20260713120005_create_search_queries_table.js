exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("search_queries");
  if (exists) return;

  await knex.schema.createTable("search_queries", (table) => {
    table.increments("id").primary();
    table
      .integer("user_id")
      .unsigned()
      .nullable()
      .references("id")
      .inTable("users")
      .onDelete("SET NULL");
    table.string("query", 500).notNullable();
    table.integer("result_count").unsigned().notNullable().defaultTo(0);
    table.string("search_type", 20).notNullable().defaultTo("search"); // 'search' | 'maps'
    table.timestamp("created_at").notNullable().defaultTo(knex.fn.now());

    table.index(["user_id", "created_at"], "idx_search_queries_user_created");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("search_queries");
};
