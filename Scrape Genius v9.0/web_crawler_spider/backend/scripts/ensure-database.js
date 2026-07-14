require("dotenv").config();
const mysql = require("mysql2/promise");

async function main() {
  const dbName = process.env.DB_NAME || "scrapegenius_backend";
  const conn = await mysql.createConnection({
    host: process.env.DB_HOST || "localhost",
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || "root",
    password: process.env.DB_PASSWORD || "",
  });

  await conn.query(
    `CREATE DATABASE IF NOT EXISTS \`${dbName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`
  );
  console.log(`Database "${dbName}" is ready.`);
  await conn.end();
}

main().catch((err) => {
  console.error("Could not create database:", err.message);
  process.exit(1);
});
