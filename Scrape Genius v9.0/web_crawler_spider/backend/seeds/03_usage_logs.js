function todayDateString() {
  return new Date().toISOString().split("T")[0];
}

exports.seed = async function (knex) {
  const firstKey = await knex("api_keys").orderBy("id", "asc").first();
  if (!firstKey) return;

  const today = todayDateString();
  const existing = await knex("usage_logs")
    .where({ api_key_id: firstKey.id, date: today, search_type: "search" })
    .first();
  if (existing) return;

  await knex("usage_logs").insert({
    api_key_id: firstKey.id,
    date: today,
    count: 0,
    search_type: "search",
  });
};
