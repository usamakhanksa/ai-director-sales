const express = require("express");

const db = require("../config/database");
const { requireAuth } = require("../middleware/auth");
const { requireAdmin } = require("../middleware/admin");
const { todayDateString } = require("../services/keyUsageService");

const router = express.Router();

router.use(requireAuth, requireAdmin);

// --- Users ---------------------------------------------------------------

router.get("/users", async (req, res, next) => {
  try {
    const limit = Math.min(Number(req.query.limit) || 500, 1000);
    const users = await db("users")
      .select("id", "name", "email", "country", "verified", "admin", "purchase_code_verified", "created_at")
      .orderBy("id", "desc")
      .limit(limit);
    res.json({ success: true, data: users });
  } catch (err) {
    next(err);
  }
});

router.patch("/users/:id", async (req, res, next) => {
  try {
    const { id } = req.params;
    const { verified, admin, purchase_code_verified } = req.body;

    const updates = {};
    if (typeof verified === "boolean") updates.verified = verified;
    if (typeof admin === "boolean") updates.admin = admin;
    if (typeof purchase_code_verified === "boolean") updates.purchase_code_verified = purchase_code_verified;

    if (Object.keys(updates).length === 0) {
      return res.status(400).json({ error: "No valid fields to update" });
    }

    const count = await db("users").where({ id }).update(updates);
    if (count === 0) {
      return res.status(404).json({ error: "User not found" });
    }

    res.json({ success: true, message: "User updated" });
  } catch (err) {
    next(err);
  }
});

// --- API keys --------------------------------------------------------------

router.get("/api-keys", async (req, res, next) => {
  try {
    const keys = await db("api_keys").orderBy("id", "asc");
    res.json({ success: true, data: keys });
  } catch (err) {
    next(err);
  }
});

router.post("/api-keys", async (req, res, next) => {
  try {
    const { key, cx, provider, daily_limit, is_active } = req.body;
    if (!key || !cx) {
      return res.status(400).json({ error: "key and cx are required" });
    }

    const [id] = await db("api_keys").insert({
      key,
      cx,
      provider: provider || "google_custom_search",
      daily_limit: daily_limit || 100,
      is_active: is_active !== undefined ? !!is_active : true,
    });

    res.status(201).json({ success: true, data: await db("api_keys").where({ id }).first() });
  } catch (err) {
    next(err);
  }
});

router.patch("/api-keys/:id", async (req, res, next) => {
  try {
    const { id } = req.params;
    const { key, cx, provider, daily_limit, is_active } = req.body;

    const updates = {};
    if (key !== undefined) updates.key = key;
    if (cx !== undefined) updates.cx = cx;
    if (provider !== undefined) updates.provider = provider;
    if (daily_limit !== undefined) updates.daily_limit = daily_limit;
    if (is_active !== undefined) updates.is_active = !!is_active;

    if (Object.keys(updates).length === 0) {
      return res.status(400).json({ error: "No valid fields to update" });
    }

    const count = await db("api_keys").where({ id }).update(updates);
    if (count === 0) {
      return res.status(404).json({ error: "API key not found" });
    }

    res.json({ success: true, message: "API key updated" });
  } catch (err) {
    next(err);
  }
});

router.delete("/api-keys/:id", async (req, res, next) => {
  try {
    const { id } = req.params;
    const count = await db("api_keys").where({ id }).del();
    if (count === 0) {
      return res.status(404).json({ error: "API key not found" });
    }
    res.json({ success: true, message: "API key deleted" });
  } catch (err) {
    next(err);
  }
});

// --- Usage stats -----------------------------------------------------------

router.get("/usage", async (req, res, next) => {
  try {
    const date = req.query.date || todayDateString();

    const rows = await db("api_keys")
      .leftJoin("usage_logs", function joinUsage() {
        this.on("usage_logs.api_key_id", "=", "api_keys.id").andOn(
          "usage_logs.date",
          "=",
          db.raw("?", [date])
        );
      })
      .select(
        "api_keys.id as api_key_id",
        "api_keys.key",
        "api_keys.provider",
        "api_keys.is_active",
        "api_keys.daily_limit",
        "usage_logs.search_type",
        "usage_logs.count"
      )
      .orderBy("api_keys.id", "asc");

    res.json({ success: true, date, data: rows });
  } catch (err) {
    next(err);
  }
});

router.get("/search-queries", async (req, res, next) => {
  try {
    const limit = Math.min(Number(req.query.limit) || 100, 500);
    const rows = await db("search_queries")
      .select("id", "user_id", "query", "result_count", "search_type", "created_at")
      .orderBy("created_at", "desc")
      .limit(limit);
    res.json({ success: true, data: rows });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
