"use client";

import { getToken } from "./client-auth";

/** Fetches an authenticated file download (Bearer token) and saves it via a Blob URL, since a plain <a href> or window.open navigation can't carry the Authorization header. */
export async function downloadAuthenticated(url: string, fallbackFileName: string): Promise<void> {
  const token = getToken();
  const res = await fetch(url, { headers: { Authorization: `Bearer ${token}` } });
  if (!res.ok) throw new Error(`Download failed (${res.status})`);

  const disposition = res.headers.get("content-disposition") || "";
  const match = disposition.match(/filename="?([^";]+)"?/);
  const fileName = match?.[1] || fallbackFileName;

  const blob = await res.blob();
  const blobUrl = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = blobUrl;
  a.download = fileName;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(blobUrl);
}
