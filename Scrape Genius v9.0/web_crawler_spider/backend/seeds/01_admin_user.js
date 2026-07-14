const bcrypt = require("bcryptjs");

const ADMIN_EMAIL = "admin@scrapegenius.com";
const ADMIN_PASSWORD = "ChangeMe123!"; // change immediately after first login

exports.seed = async function (knex) {
  const existing = await knex("users").where({ email: ADMIN_EMAIL }).first();
  if (existing) return;

  const password_hash = await bcrypt.hash(ADMIN_PASSWORD, 10);

  await knex("users").insert({
    name: "ScrapeGenius Admin",
    email: ADMIN_EMAIL,
    password_hash,
    country: "N/A",
    verified: true,
    purchase_code_verified: true,
    admin: true,
  });

  // eslint-disable-next-line no-console
  console.log(`Seeded admin user ${ADMIN_EMAIL} / ${ADMIN_PASSWORD} (change this password immediately).`);
};
