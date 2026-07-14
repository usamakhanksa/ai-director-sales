import type { CSSProperties } from "react";

export const page: CSSProperties = {
  fontFamily: "system-ui, sans-serif",
  maxWidth: 380,
  margin: "4rem auto",
  padding: "0 1.5rem",
  color: "#1a1a1a",
};

export const widePage: CSSProperties = {
  fontFamily: "system-ui, sans-serif",
  maxWidth: 720,
  margin: "3rem auto",
  padding: "0 1.5rem",
  color: "#1a1a1a",
};

export const label: CSSProperties = {
  display: "flex",
  flexDirection: "column",
  gap: "0.25rem",
  fontSize: "0.875rem",
  color: "#333",
};

export const input: CSSProperties = {
  padding: "0.5rem",
  fontSize: "1rem",
  border: "1px solid #ccc",
  borderRadius: 4,
};

export const button: CSSProperties = {
  padding: "0.6rem",
  fontSize: "1rem",
  background: "#0070f3",
  color: "#fff",
  border: "none",
  borderRadius: 4,
  cursor: "pointer",
};

export const secondaryButton: CSSProperties = {
  ...button,
  background: "#eee",
  color: "#1a1a1a",
};

export const errorText: CSSProperties = { color: "#c00", margin: 0, fontSize: "0.875rem" };
