import { NextResponse } from "next/server";
import { ZodError } from "zod";
import { AuthError } from "./auth";

export function ok<T>(data: T, status = 200) {
  return NextResponse.json({ success: true, data }, { status });
}

export function fail(error: string, status = 400) {
  return NextResponse.json({ success: false, error }, { status });
}

/** Wraps a route handler body so every route gets the same {success,data?,error?} error shape. */
export function withErrorHandling(handler: () => Promise<NextResponse>) {
  return handler().catch((err: unknown) => {
    if (err instanceof ZodError) {
      return fail(err.issues.map((i) => `${i.path.join(".")}: ${i.message}`).join("; "), 400);
    }
    if (err instanceof AuthError) {
      return fail(err.message, err.status);
    }
    console.error("❌ Unhandled API error:", err);
    return fail("Internal Server Error", 500);
  });
}
