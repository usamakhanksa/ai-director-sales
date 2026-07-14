const jwt = require("jsonwebtoken");
const db = require("../config/database");

const JWT_SECRET = process.env.JWT_SECRET;
const JWT_EXPIRES_IN = process.env.JWT_EXPIRES_IN || "7d";

if (!JWT_SECRET) {
  throw new Error("JWT_SECRET is not set. Copy .env.example to .env and configure it.");
}

function signToken(payload) {
  return jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES_IN });
}

function verifyToken(token) {
  return jwt.verify(token, JWT_SECRET);
}

const SQL_UNIT = { s: "SECOND", m: "MINUTE", h: "HOUR", d: "DAY" };

/**
 * Returns a knex.raw() expiry expression computed by MySQL itself
 * (DATE_ADD(NOW(), INTERVAL ...)), rather than a JS Date sent over the wire.
 * Computing it client-side caused stored expiries to drift from the server's
 * NOW() whenever the app server and MySQL disagreed on timezone handling.
 */
function expiresAtSqlExpression(durationString = JWT_EXPIRES_IN) {
  const match = /^(\d+)([smhd])$/.exec(durationString);
  const amount = match ? Number(match[1]) : 7;
  const unit = match ? SQL_UNIT[match[2]] : "DAY";
  return db.raw(`DATE_ADD(NOW(), INTERVAL ${amount} ${unit})`);
}

module.exports = { signToken, verifyToken, expiresAtSqlExpression };
