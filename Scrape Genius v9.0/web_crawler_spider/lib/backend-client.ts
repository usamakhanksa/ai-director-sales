import "server-only";
import type { User } from "@prisma/client";

/**
 * Server-side-only bridge to the Express scraping microservice (backend/).
 * That service has its own, deliberately separate login system (see
 * backend/.env.example) — rather than making users log in twice, we forward
 * the identity this app already verified over a shared secret that never
 * reaches the browser. Only call this from Next.js Route Handlers (`app/api/**`),
 * never from client components.
 */

const BACKEND_URL = process.env.SCRAPER_BACKEND_URL || "http://localhost:4000";
const INTERNAL_SECRET = process.env.INTERNAL_API_SECRET;

function identityHeaders(user: User): Record<string, string> {
  if (!INTERNAL_SECRET) {
    throw new Error("INTERNAL_API_SECRET is not configured on the frontend — set it in .env");
  }
  return {
    "X-Internal-Secret": INTERNAL_SECRET,
    "X-User-Id": String(user.id),
    "X-User-Email": user.email,
    "X-User-Name": user.name,
  };
}

export interface BackendResult<T = unknown> {
  status: number;
  ok: boolean;
  body: { success: boolean; data?: T; error?: string; total?: number; message?: string } | null;
}

/** JSON round-trip to the Express backend, scoped to `user`. */
export async function backendFetch<T = unknown>(
  user: User,
  path: string,
  init?: { method?: string; body?: unknown; query?: Record<string, string | number | undefined> }
): Promise<BackendResult<T>> {
  const url = new URL(path, BACKEND_URL);
  if (init?.query) {
    for (const [key, value] of Object.entries(init.query)) {
      if (value !== undefined) url.searchParams.set(key, String(value));
    }
  }

  const res = await fetch(url, {
    method: init?.method || "GET",
    headers: {
      "Content-Type": "application/json",
      ...identityHeaders(user),
    },
    body: init?.body !== undefined ? JSON.stringify(init.body) : undefined,
    cache: "no-store",
  });

  let body: BackendResult<T>["body"] = null;
  try {
    body = await res.json();
  } catch {
    body = null;
  }

  return { status: res.status, ok: res.ok, body };
}

/** Raw passthrough for streaming responses (SSE logs, file downloads). */
export async function backendFetchRaw(
  user: User,
  path: string,
  init?: { method?: string }
): Promise<Response> {
  const url = new URL(path, BACKEND_URL);
  return fetch(url, {
    method: init?.method || "GET",
    headers: identityHeaders(user),
    cache: "no-store",
    // @ts-expect-error - Node's fetch requires this for streamed responses
    duplex: "half",
  });
}

