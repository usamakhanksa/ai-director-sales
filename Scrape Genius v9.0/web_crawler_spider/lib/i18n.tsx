"use client";

import { createContext, useContext, useEffect, useMemo, useState, type ReactNode } from "react";
import { LOCALE_DIR, LOCALE_LABEL, translate, type Locale } from "./i18n-strings";

const LOCALE_KEY = "sg_locale";

interface I18nContextValue {
  locale: Locale;
  dir: "ltr" | "rtl";
  setLocale: (locale: Locale) => void;
  t: (key: string) => string;
}

const I18nContext = createContext<I18nContextValue | null>(null);

function applyDocumentDirection(locale: Locale) {
  if (typeof document === "undefined") return;
  document.documentElement.lang = locale;
  document.documentElement.dir = LOCALE_DIR[locale];
}

export function I18nProvider({ children }: { children: ReactNode }) {
  const [locale, setLocaleState] = useState<Locale>("en");

  useEffect(() => {
    const stored = (localStorage.getItem(LOCALE_KEY) as Locale | null) || "en";
    setLocaleState(stored);
    applyDocumentDirection(stored);
  }, []);

  function setLocale(next: Locale) {
    localStorage.setItem(LOCALE_KEY, next);
    setLocaleState(next);
    applyDocumentDirection(next);
  }

  const value = useMemo<I18nContextValue>(
    () => ({
      locale,
      dir: LOCALE_DIR[locale],
      setLocale,
      t: (key: string) => translate(locale, key),
    }),
    [locale]
  );

  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
}

/** Falls back to English + a no-op setter outside the provider so pages can't crash if it's ever missing. */
export function useTranslation(): I18nContextValue {
  const ctx = useContext(I18nContext);
  if (ctx) return ctx;
  return { locale: "en", dir: "ltr", setLocale: () => {}, t: (key: string) => translate("en", key) };
}

export { LOCALE_LABEL };
export type { Locale };
