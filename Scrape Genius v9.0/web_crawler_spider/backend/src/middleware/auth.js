const db = require("../config/database");
const { verifyToken } = require("../utils/jwt");

// Verifies the Bearer token's JWT signature, then confirms the session is still
// live in auth_tokens (not revoked / not expired) so admin-side logout/revoke works
// even though the JWT itself hasn't expired yet.
async function requireAuth(req, res, next) {
  try {
    const header = req.headers.authorization || "";
    const [scheme, token] = header.split(" ");

    if (scheme !== "Bearer" || !token) {
      return res.status(401).json({ error: "Missing or invalid Authorization header" });
    }

    let payload;
    try {
      payload = verifyToken(token);
    } catch (err) {
      return res.status(401).json({ error: "Invalid or expired token" });
    }

    const session = await db("auth_tokens")
      .where({ token, revoked: false })
      .andWhere("expires_at", ">", db.fn.now())
      .first();

    if (!session) {
      return res.status(401).json({ error: "Session revoked or expired" });
    }

    const user = await db("users").where({ id: payload.sub }).first();
    if (!user) {
      return res.status(401).json({ error: "User not found" });
    }

    req.user = user;
    req.token = token;
    next();
  } catch (err) {
    next(err);
  }
}

module.exports = { requireAuth };
