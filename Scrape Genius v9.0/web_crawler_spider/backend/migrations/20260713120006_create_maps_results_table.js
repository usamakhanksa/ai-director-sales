exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("maps_results");
  if (exists) return;

  await knex.schema.createTable("maps_results", (table) => {
    table.increments("id").primary();
    table
      .integer("search_query_id")
      .unsigned()
      .nullable()
      .references("id")
      .inTable("search_queries")
      .onDelete("CASCADE");
    table.string("place_name", 255).nullable();
    table.string("address", 500).nullable();
    table.string("phone", 50).nullable();
    table.string("website", 500).nullable();
    table.string("category", 255).nullable();
    table.decimal("rating", 2, 1).nullable();
    table.integer("reviews_count").unsigned().nullable();
    table.decimal("latitude", 10, 7).nullable();
    table.decimal("longitude", 10, 7).nullable();
    table.json("raw_json").nullable();
    table.timestamp("created_at").notNullable().defaultTo(knex.fn.now());

    table.index(["search_query_id"], "idx_maps_results_query");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("maps_results");
};
