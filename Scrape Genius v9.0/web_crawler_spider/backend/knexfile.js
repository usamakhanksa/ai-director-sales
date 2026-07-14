require("dotenv").config();

const base = {
  client: "mysql2",
  connection: {
    host: process.env.DB_HOST || "localhost",
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || "root",
    password: process.env.DB_PASSWORD || "",
    database: process.env.DB_NAME || "google-map-scraper-pro",
    timezone: "Z",
  },
  pool: { min: 2, max: 10 },
  migrations: { directory: "./migrations", tableName: "knex_migrations" },
  seeds: { directory: "./seeds" },
};

module.exports = {
  development: base,
  production: base,
};
