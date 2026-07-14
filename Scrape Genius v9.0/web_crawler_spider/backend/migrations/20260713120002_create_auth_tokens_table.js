exports.up = async function (knex) {
  const exists = await knex.schema.hasTable("auth_tokens");
  if (exists) return;

  await knex.schema.createTable("auth_tokens", (table) => {
    table.increments("id").primary();
    table
      .integer("user_id")
      .unsigned()
      .notNullable()
      .references("id")
      .inTable("users")
      .onDelete("CASCADE");
    table.string("token", 512).notNullable().unique();
    table.timestamp("expires_at").notNullable();
    table.boolean("revoked").notNullable().defaultTo(false);
    table.timestamp("created_at").notNullable().defaultTo(knex.fn.now());

    table.index(["user_id"], "idx_auth_tokens_user_id");
  });
};

exports.down = async function (knex) {
  await knex.schema.dropTableIfExists("auth_tokens");
};
