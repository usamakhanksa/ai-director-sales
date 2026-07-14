"use strict";

const db = require("../config/database");

/**
 * The Next.js frontend (Prisma-backed users table) and this Express service
 * (its own Knex-backed users table) are two deliberately separate identity
 * systems with incompatible JWTs — see backend/.env.example. Rather than
 * making the frontend log in twice, the frontend forwards the identity of
 * the user it already authenticated over a shared secret, and this service
 * lazily mirrors a minimal shadow row locally so the scrape_jobs/export_records
 * foreign keys against `users.id` stay satisfied.
 *
 * Returns true if the request was handled (either authenticated or rejected
 * as a malformed internal call) so the caller knows not to fall through to
 * cookie/JWT auth. Returns false when no internal secret was presented at
 * all, so a normal Bearer-token caller can still be checked separately.
 *
 * Deliberately does NOT accept a role/admin claim from the caller: shadow
 * users created here are always plain, non-admin accounts. Express's own
 * admin routes (backend/src/routes/admin.routes.js) are not reachable
 * through this bridge at all — the Next.js admin page manages its own
 * Prisma-backed users/keys/usage instead of proxying admin actions here.
 */
async function tryInternalAuth(req, res, next) {
  const secret = req.headers["x-internal-secret"];
  if (!secret) return false;

  if (!process.env.INTERNAL_API_SECRET || secret !== process.env.INTERNAL_API_SECRET) {
    res.status(401).json({ success: false, error: "Invalid internal secret" });
    return true;
  }

  const userId = Number(req.headers["x-user-id"]);
  const email = req.headers["x-user-email"];
  const name = (req.headers["x-user-name"] || email || "").toString().slice(0, 191);

  if (!userId || !email) {
    res.status(401).json({ success: false, error: "Missing X-User-Id / X-User-Email for internal call" });
    return true;
  }

  try {
    const row = { id: userId, name, email: String(email).slice(0, 191), verified: true, admin: false };
    await db("users").insert(row).onConflict("id").merge({ name, email: row.email });

    req.user = { id: userId, name, email, admin: false };
    req.isInternalCall = true;
    next();
  } catch (err) {
    next(err);
  }
  return true;
}

/** Accepts either the internal frontend bridge or a normal Bearer session. */
function requireAuthOrInternal(requireAuth) {
  return async function (req, res, next) {
    const handled = await tryInternalAuth(req, res, next);
    if (handled) return;
    return requireAuth(req, res, next);
  };
}

module.exports = { requireAuthOrInternal };
