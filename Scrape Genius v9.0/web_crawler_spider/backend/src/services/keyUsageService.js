const db = require("../config/database");

const DAILY_TOTAL_LIMIT = Number(process.env.GOOGLE_SEARCH_DAILY_TOTAL_LIMIT) || 100;
const DEFAULT_PER_KEY_LIMIT = Number(process.env.GOOGLE_SEARCH_DAILY_PER_KEY_LIMIT) || 100;

class DailyLimitReachedError extends Error {
  constructor(message = "Your daily limit has been reached!") {
    super(message);
    this.status = 429;
  }
}

class KeysExhaustedError extends Error {
  constructor(message = "All API keys exhausted for today") {
    super(message);
    this.status = 429;
  }
}

class NoActiveKeysError extends Error {
  constructor(message = "No API accounts available") {
    super(message);
    this.status = 500;
  }
}

function todayDateString() {
  return new Date().toISOString().split("T")[0];
}

/** Mirrors the original /api/get_keys response shape ({ key, cx }[]). */
async function listActiveKeys(provider = "google_custom_search") {
  const rows = await db("api_keys")
    .where({ is_active: true, provider })
    .orderBy("id", "asc");
  return rows.map((r) => ({ key: r.key, cx: r.cx }));
}

/**
 * Atomically picks the least-used active key for today and reserves capacity
 * for it, replacing the old external_usage.json read/sum/sort/save cycle.
 * Reservation happens inside a single short transaction (row-locked), so two
 * concurrent requests can never both grab the same remaining headroom - the
 * caller must reconcile afterwards with releaseUnusedReservation().
 */
async function reserveKeyForSearch({ requestedLimit, searchType = "search", provider = "google_custom_search" }) {
  const today = todayDateString();

  return db.transaction(async (trx) => {
    const activeKeys = await trx("api_keys").where({ is_active: true, provider }).forUpdate();
    if (activeKeys.length === 0) {
      throw new NoActiveKeysError();
    }

    // Make sure every active key has a usage_logs row for today (count 0 if new),
    // without touching existing counts.
    await trx("usage_logs")
      .insert(
        activeKeys.map((k) => ({
          api_key_id: k.id,
          date: today,
          count: 0,
          search_type: searchType,
        }))
      )
      .onConflict(["api_key_id", "date", "search_type"])
      .ignore();

    const usageRows = await trx("usage_logs")
      .whereIn(
        "api_key_id",
        activeKeys.map((k) => k.id)
      )
      .andWhere({ date: today, search_type: searchType })
      .forUpdate();

    const usageByKeyId = new Map(usageRows.map((u) => [u.api_key_id, u]));

    const totalUsedToday = activeKeys.reduce(
      (sum, k) => sum + (usageByKeyId.get(k.id)?.count || 0),
      0
    );
    if (totalUsedToday >= DAILY_TOTAL_LIMIT) {
      throw new DailyLimitReachedError();
    }

    const candidates = activeKeys
      .map((k) => ({
        apiKeyId: k.id,
        key: k.key,
        cx: k.cx,
        dailyLimit: k.daily_limit || DEFAULT_PER_KEY_LIMIT,
        count: usageByKeyId.get(k.id)?.count || 0,
        usageLogId: usageByKeyId.get(k.id)?.id,
      }))
      .filter((c) => c.count < c.dailyLimit)
      .sort((a, b) => a.count - b.count);

    if (candidates.length === 0) {
      throw new KeysExhaustedError();
    }

    const chosen = candidates[0];
    const remainingGlobal = DAILY_TOTAL_LIMIT - totalUsedToday;
    const remainingForKey = chosen.dailyLimit - chosen.count;
    const reserved = Math.max(0, Math.min(requestedLimit, remainingForKey, remainingGlobal));

    await trx("usage_logs")
      .where({ id: chosen.usageLogId })
      .update({ count: chosen.count + reserved, updated_at: trx.fn.now() });

    return {
      apiKeyId: chosen.apiKeyId,
      usageLogId: chosen.usageLogId,
      key: chosen.key,
      cx: chosen.cx,
      reserved,
      dailyLimit: chosen.dailyLimit,
      countBeforeReserve: chosen.count,
    };
  });
}

/** Gives back reserved-but-unused capacity, e.g. when the scrape loop stops early on an error. */
async function releaseUnusedReservation(usageLogId, unusedAmount) {
  if (!unusedAmount || unusedAmount <= 0) return;
  await db("usage_logs")
    .where({ id: usageLogId })
    .update({ count: db.raw("GREATEST(count - ?, 0)", [unusedAmount]), updated_at: db.fn.now() });
}

module.exports = {
  DAILY_TOTAL_LIMIT,
  DEFAULT_PER_KEY_LIMIT,
  DailyLimitReachedError,
  KeysExhaustedError,
  NoActiveKeysError,
  todayDateString,
  listActiveKeys,
  reserveKeyForSearch,
  releaseUnusedReservation,
};
