/**
 * In-memory, per-process quota tracker + key rotator for env-configured API
 * keys (Google/Bing/etc). Not persisted — restarts reset counters, which is
 * fine since it only exists to avoid burning through a key that already 429'd
 * today within this process's lifetime. Per-user DB-backed keys (lib/keys.ts)
 * remain the source of truth for the existing per-user Google routes.
 */
interface KeyState {
  usedToday: number;
  date: string;
  disabledUntil?: number;
}

class QuotaTracker {
  private state = new Map<string, KeyState>();

  private today(): string {
    return new Date().toISOString().slice(0, 10);
  }

  private stateFor(provider: string, key: string): KeyState {
    const id = `${provider}:${key}`;
    const today = this.today();
    let s = this.state.get(id);
    if (!s || s.date !== today) {
      s = { usedToday: 0, date: today };
      this.state.set(id, s);
    }
    return s;
  }

  /** Picks the least-used key under dailyLimit and not temporarily disabled (e.g. after a 429). */
  pickKey(provider: string, keys: string[], dailyLimit: number): string | null {
    const today = this.today();
    const now = Date.now();
    const candidates = keys
      .map((key) => ({ key, s: this.stateFor(provider, key) }))
      .filter(({ s }) => s.date === today && s.usedToday < dailyLimit)
      .filter(({ s }) => !s.disabledUntil || s.disabledUntil < now)
      .sort((a, b) => a.s.usedToday - b.s.usedToday);

    return candidates[0]?.key ?? null;
  }

  recordUse(provider: string, key: string, incrementBy = 1): void {
    const s = this.stateFor(provider, key);
    s.usedToday += incrementBy;
  }

  /** Marks a key as temporarily unusable (e.g. after a 429/quota-exceeded response) so rotation skips it. */
  disableTemporarily(provider: string, key: string, forMs = 60 * 60 * 1000): void {
    const s = this.stateFor(provider, key);
    s.disabledUntil = Date.now() + forMs;
  }

  remaining(provider: string, keys: string[], dailyLimit: number): number {
    const today = this.today();
    return keys.reduce((sum, key) => {
      const s = this.stateFor(provider, key);
      const used = s.date === today ? s.usedToday : 0;
      return sum + Math.max(0, dailyLimit - used);
    }, 0);
  }
}

export const quotaTracker = new QuotaTracker();

/** Parses a comma-separated env var into a list of trimmed, non-empty keys. */
export function parseKeyPool(envValue: string | undefined): string[] {
  if (!envValue) return [];
  return envValue
    .split(",")
    .map((k) => k.trim())
    .filter(Boolean);
}
