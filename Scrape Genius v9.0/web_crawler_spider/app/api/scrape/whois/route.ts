import { NextRequest, NextResponse } from "next/server";
import net from "net";
import { z } from "zod";

import { requireAuth } from "@/lib/auth";
import { ok, fail, withErrorHandling } from "@/lib/api-response";
import { saveScrapedRecords } from "@/lib/records";

// Raw TCP WHOIS needs the Node runtime (the `net` module doesn't exist on
// the Edge runtime).
export const runtime = "nodejs";

const whoisSchema = z.object({
  domain: z
    .string()
    .trim()
    .min(3)
    .max(255)
    .regex(/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/i, "Invalid domain"),
});

const OVERALL_TIMEOUT_MS = 10_000;
const SOCKET_TIMEOUT_MS = 6_000;

/** Opens a raw TCP socket to a WHOIS server (port 43), sends the query, and collects the plaintext reply. */
function queryWhois(server: string, query: string, timeoutMs: number): Promise<string> {
  return new Promise((resolve, reject) => {
    const socket = new net.Socket();
    let data = "";
    let settled = false;

    const timer = setTimeout(() => {
      settle(() => reject(new Error(`WHOIS query to ${server} timed out`)));
    }, timeoutMs);

    function settle(fn: () => void) {
      if (settled) return;
      settled = true;
      clearTimeout(timer);
      socket.destroy();
      fn();
    }

    socket.setEncoding("utf8");
    socket.once("connect", () => {
      socket.write(`${query}\r\n`);
    });
    socket.on("data", (chunk) => {
      data += chunk;
    });
    socket.once("close", () => settle(() => resolve(data)));
    socket.once("end", () => settle(() => resolve(data)));
    socket.once("error", (err) => settle(() => reject(err)));

    socket.connect(43, server);
  });
}

/** Looks for a `refer:` or `whois:` line in an IANA root WHOIS response pointing at the authoritative registry server. */
function findReferralServer(text: string): string | undefined {
  for (const rawLine of text.split(/\r?\n/)) {
    const match = rawLine.trim().match(/^(refer|whois)\s*:\s*(\S+)/i);
    if (match) return match[2];
  }
  return undefined;
}

interface ParsedWhois {
  registrar?: string;
  creationDate?: string;
  expiryDate?: string;
  updatedDate?: string;
  nameServers: string[];
  status: string[];
  raw: string;
}

const FIELD_MAP: Record<string, keyof Omit<ParsedWhois, "nameServers" | "status" | "raw">> = {
  registrar: "registrar",
  "sponsoring registrar": "registrar",
  "registrar name": "registrar",
  "creation date": "creationDate",
  created: "creationDate",
  "created on": "creationDate",
  "created date": "creationDate",
  "registered on": "creationDate",
  "domain registration date": "creationDate",
  "registry expiry date": "expiryDate",
  "expiry date": "expiryDate",
  "expiration date": "expiryDate",
  "expires on": "expiryDate",
  expires: "expiryDate",
  "domain expiration date": "expiryDate",
  "updated date": "updatedDate",
  "last updated": "updatedDate",
  "last updated on": "updatedDate",
  "last modified": "updatedDate",
  changed: "updatedDate",
};

/** Parses loose WHOIS plaintext (format varies per registry) into a normalized key/value object, keeping the raw text alongside. */
function parseWhoisText(raw: string): ParsedWhois {
  const nameServers: string[] = [];
  const status: string[] = [];
  const parsed: Partial<ParsedWhois> = {};

  for (const rawLine of raw.split(/\r?\n/)) {
    const line = rawLine.trim();
    if (!line || line.startsWith("%") || line.startsWith("#")) continue;

    const idx = line.indexOf(":");
    if (idx === -1) continue;

    const key = line.slice(0, idx).trim().toLowerCase();
    const value = line.slice(idx + 1).trim();
    if (!value) continue;

    if (key === "name server" || key === "nserver" || key.replace(/\s+/g, "") === "nameserver") {
      nameServers.push(value);
      continue;
    }
    if (key === "domain status" || key === "status") {
      status.push(value);
      continue;
    }

    const mapped = FIELD_MAP[key];
    if (mapped && !parsed[mapped]) {
      parsed[mapped] = value;
    }
  }

  return {
    registrar: parsed.registrar,
    creationDate: parsed.creationDate,
    expiryDate: parsed.expiryDate,
    updatedDate: parsed.updatedDate,
    nameServers: Array.from(new Set(nameServers)),
    status: Array.from(new Set(status)),
    raw,
  };
}

async function lookupWhois(domain: string): Promise<ParsedWhois> {
  const ianaResponse = await queryWhois("whois.iana.org", domain, SOCKET_TIMEOUT_MS);
  const referral = findReferralServer(ianaResponse);

  if (!referral) {
    // No referral found — fall back to whatever IANA gave us rather than failing outright.
    return parseWhoisText(ianaResponse);
  }

  try {
    const registryResponse = await queryWhois(referral, domain, SOCKET_TIMEOUT_MS);
    return parseWhoisText(registryResponse);
  } catch {
    // Registry server unreachable — still surface the IANA data we do have.
    return parseWhoisText(ianaResponse);
  }
}

function withOverallTimeout<T>(promise: Promise<T>, ms: number): Promise<T> {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => reject(new Error("WHOIS lookup timed out")), ms);
    promise.then(
      (v) => {
        clearTimeout(timer);
        resolve(v);
      },
      (e) => {
        clearTimeout(timer);
        reject(e);
      },
    );
  });
}

// Real raw WHOIS lookup over TCP (no API key, no npm package): asks
// whois.iana.org which registry is authoritative for the domain's TLD, then
// queries that registry directly and parses the plaintext response.
export async function POST(req: NextRequest): Promise<NextResponse> {
  return withErrorHandling(async () => {
    const user = await requireAuth(req);
    const body = whoisSchema.parse(await req.json());
    const domain = body.domain.toLowerCase();

    let parsed: ParsedWhois;
    try {
      parsed = await withOverallTimeout(lookupWhois(domain), OVERALL_TIMEOUT_MS);
    } catch (err: unknown) {
      const reason = err instanceof Error ? err.message : "Unknown error";
      return fail(`WHOIS lookup failed: ${reason}`, 502);
    }

    await saveScrapedRecords(user.id, "WHOIS", domain, [parsed as unknown as Record<string, unknown>]);

    return ok({
      query: domain,
      source: "WHOIS",
      result: parsed,
    });
  });
}
