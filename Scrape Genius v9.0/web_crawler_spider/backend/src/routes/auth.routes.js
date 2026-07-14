const express = require("express");
const crypto = require("crypto");
const bcrypt = require("bcryptjs");
const { OAuth2Client } = require("google-auth-library");

const db = require("../config/database");
const { signToken, expiresAtSqlExpression } = require("../utils/jwt");
const { requireAuth } = require("../middleware/auth");
const { requireAdmin } = require("../middleware/admin");
const { authLimiter } = require("../middleware/rateLimiter");
const { listActiveKeys } = require("../services/keyUsageService");

const router = express.Router();
const googleOAuthClient = new OAuth2Client(process.env.GOOGLE_OAUTH_CLIENT_ID);

function publicUser(user) {
  return {
    id: user.id,
    name: user.name,
    email: user.email,
    country: user.country,
    verified: !!user.verified,
    admin: !!user.admin,
    purchase_code_verified: !!user.purchase_code_verified,
  };
}

function generateVerificationCode() {
  return String(Math.floor(100000 + Math.random() * 900000)); // 6 digits
}

async function issueSession(user) {
  const token = signToken({ sub: user.id, email: user.email, admin: !!user.admin });
  await db("auth_tokens").insert({
    user_id: user.id,
    token,
    expires_at: expiresAtSqlExpression(),
  });
  return token;
}

router.post("/signup", authLimiter, async (req, res, next) => {
  try {
    const { name, email, password, country } = req.body;
    if (!name || !email || !password) {
      return res.status(400).json({ error: "name, email and password are required" });
    }

    const existing = await db("users").where({ email }).first();
    if (existing) {
      return res.status(409).json({ error: "An account with this email already exists" });
    }

    const password_hash = await bcrypt.hash(password, 10);
    const verification_code = generateVerificationCode();
    const verification_code_expires_at = expiresAtSqlExpression("15m");

    const [id] = await db("users").insert({
      name,
      email,
      password_hash,
      country: country || null,
      verified: false,
      verification_code,
      verification_code_expires_at,
    });

    // TODO: wire up a real mail provider. Logged for local/dev use in the meantime.
    console.log(`✉️  Verification code for ${email}: ${verification_code}`);

    const user = await db("users").where({ id }).first();
    res.status(201).json({ message: "Signup successful. Please verify your email.", user: publicUser(user) });
  } catch (err) {
    next(err);
  }
});

router.post("/login", authLimiter, async (req, res, next) => {
  try {
    const { email, password } = req.body;
    if (!email || !password) {
      return res.status(400).json({ error: "email and password are required" });
    }

    const user = await db("users").where({ email }).first();
    if (!user || !user.password_hash) {
      return res.status(401).json({ error: "Invalid email or password" });
    }

    const matches = await bcrypt.compare(password, user.password_hash);
    if (!matches) {
      return res.status(401).json({ error: "Invalid email or password" });
    }

    if (!user.verified) {
      return res.status(403).json({ error: "Please verify your email before logging in" });
    }

    const token = await issueSession(user);
    res.json({ token, user: publicUser(user) });
  } catch (err) {
    next(err);
  }
});

router.post("/signuploginwithgoogle", authLimiter, async (req, res, next) => {
  try {
    const { idToken } = req.body;
    if (!idToken) {
      return res.status(400).json({ error: "idToken is required" });
    }
    if (!process.env.GOOGLE_OAUTH_CLIENT_ID) {
      return res.status(500).json({ error: "Google sign-in is not configured on this server" });
    }

    const ticket = await googleOAuthClient.verifyIdToken({
      idToken,
      audience: process.env.GOOGLE_OAUTH_CLIENT_ID,
    });
    const payload = ticket.getPayload();

    let user = await db("users").where({ google_id: payload.sub }).first();
    if (!user) {
      user = await db("users").where({ email: payload.email }).first();
    }

    if (!user) {
      const [id] = await db("users").insert({
        name: payload.name || payload.email,
        email: payload.email,
        google_id: payload.sub,
        verified: true,
      });
      user = await db("users").where({ id }).first();
    } else if (!user.google_id) {
      await db("users").where({ id: user.id }).update({ google_id: payload.sub, verified: true });
      user = await db("users").where({ id: user.id }).first();
    }

    const token = await issueSession(user);
    res.json({ token, user: publicUser(user) });
  } catch (err) {
    next(err);
  }
});

router.post("/forget", authLimiter, async (req, res, next) => {
  try {
    const { email } = req.body;
    if (!email) {
      return res.status(400).json({ error: "email is required" });
    }

    const user = await db("users").where({ email }).first();
    if (user) {
      const reset_token = crypto.randomBytes(32).toString("hex");
      const reset_token_expires_at = expiresAtSqlExpression("1h");
      await db("users").where({ id: user.id }).update({ reset_token, reset_token_expires_at });
      // TODO: wire up a real mail provider.
      console.log(`✉️  Password reset token for ${email}: ${reset_token}`);
    }

    // Always respond the same way regardless of whether the email exists, to avoid account enumeration.
    res.json({ message: "If that email exists, a password reset link has been sent." });
  } catch (err) {
    next(err);
  }
});

router.post("/reset-password", authLimiter, async (req, res, next) => {
  try {
    const { reset_token, password } = req.body;
    if (!reset_token || !password) {
      return res.status(400).json({ error: "reset_token and password are required" });
    }

    const user = await db("users")
      .where({ reset_token })
      .andWhere("reset_token_expires_at", ">", db.fn.now())
      .first();
    if (!user) {
      return res.status(400).json({ error: "Invalid or expired reset token" });
    }

    const password_hash = await bcrypt.hash(password, 10);
    await db("users")
      .where({ id: user.id })
      .update({ password_hash, reset_token: null, reset_token_expires_at: null });

    res.json({ message: "Password updated successfully" });
  } catch (err) {
    next(err);
  }
});

router.post("/verification", authLimiter, async (req, res, next) => {
  try {
    const { email, code } = req.body;
    if (!email || !code) {
      return res.status(400).json({ error: "email and code are required" });
    }

    const user = await db("users")
      .where({ email, verification_code: code })
      .andWhere("verification_code_expires_at", ">", db.fn.now())
      .first();
    if (!user) {
      return res.status(400).json({ error: "Invalid or expired verification code" });
    }

    await db("users")
      .where({ id: user.id })
      .update({ verified: true, verification_code: null, verification_code_expires_at: null });

    res.json({ message: "Email verified successfully" });
  } catch (err) {
    next(err);
  }
});

router.post("/logout", requireAuth, async (req, res, next) => {
  try {
    await db("auth_tokens").where({ token: req.token }).update({ revoked: true });
    res.json({ message: "Logged out" });
  } catch (err) {
    next(err);
  }
});

router.get("/restricted/purchasecodeactivation/:code", requireAuth, async (req, res, next) => {
  try {
    const { code } = req.params;
    // NOTE: this is a local stub. The original SaaS validated purchase codes against
    // Envato's API (a vendor-side licensing check). Plug real verification in here
    // (e.g. Envato's /market/author/sale endpoint) if you need that guarantee.
    const looksValid = /^[a-f0-9-]{20,40}$/i.test(code);
    if (!looksValid) {
      return res.status(400).json({ success: false, error: "Invalid purchase code format" });
    }

    await db("users").where({ id: req.user.id }).update({ purchase_code: code, purchase_code_verified: true });
    res.json({ success: true, message: "Purchase code activated" });
  } catch (err) {
    next(err);
  }
});

router.get("/restricted/getgooglecode", requireAuth, async (req, res, next) => {
  try {
    const data = await listActiveKeys();
    res.json({ success: true, data });
  } catch (err) {
    next(err);
  }
});

router.get("/restricted/users", requireAuth, requireAdmin, async (req, res, next) => {
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

module.exports = router;
