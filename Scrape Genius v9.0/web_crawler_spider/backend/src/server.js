require("dotenv").config();

const app = require("./app");
const db = require("./config/database");

const PORT = process.env.PORT || 4000;

async function start() {
  try {
    await db.raw("SELECT 1");
    console.log("✅ Connected to MySQL");
  } catch (err) {
    console.error("❌ Could not connect to MySQL:", err.message);
    process.exit(1);
  }

  app.listen(PORT, () => {
    console.log(`🚀 ScrapeGenius backend listening on http://localhost:${PORT}`);
  });
}

start();

process.on("SIGINT", async () => {
  await db.destroy();
  process.exit(0);
});
