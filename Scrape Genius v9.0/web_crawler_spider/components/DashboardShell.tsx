"use client";

import { useEffect, useState, type ReactNode } from "react";
import { usePathname, useRouter } from "next/navigation";
import Link from "next/link";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faCircleQuestion,
  faMoon,
  faSun,
  faUser,
  faRightFromBracket,
  faChevronRight,
  faBars,
  faPhone,
  faEnvelope,
  faFolderOpen,
  faSliders,
  faLock,
  faWrench,
} from "@fortawesome/free-solid-svg-icons";

import { clearSession, getToken, getUser, type StoredUser } from "@/lib/client-auth";
import { getNavigationItems, NAVIGATION_BY_CATEGORY, type NavItem } from "@/lib/nav-data";
import { useTranslation, LOCALE_LABEL, type Locale } from "@/lib/i18n";
import styles from "./DashboardShell.module.css";

const THEME_KEY = "sg_theme";
const RUNNING_JOBS_POLL_MS = 15000;

// Group navigation items by category with labels
const NAVIGATION_GROUPS = [
  { id: "main", label: "Main", icon: faFolderOpen },
  { id: "tools", label: "Tools", icon: faWrench },
  { id: "settings", label: "Settings", icon: faSliders },
  { id: "admin", label: "Admin", icon: faLock },
];

export default function DashboardShell({ children }: { children: ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();
  const { t, locale, dir, setLocale } = useTranslation();

  const [user, setUser] = useState<StoredUser | null>(null);
  const [checked, setChecked] = useState(false);
  const [dark, setDark] = useState(false);
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const [runningJobs, setRunningJobs] = useState(0);

  useEffect(() => {
    const token = getToken();
    if (!token) {
      router.replace("/login");
      return;
    }
    setUser(getUser());
    setChecked(true);
    setDark(localStorage.getItem(THEME_KEY) === "dark");
  }, [router]);

  useEffect(() => {
    if (!checked) return;
    const token = getToken();
    if (!token) return;

    let cancelled = false;
    async function pollRunningJobs() {
      try {
        const res = await fetch("/api/jobs?status=RUNNING&limit=1", {
          headers: { Authorization: `Bearer ${token}` },
        });
        if (!res.ok) return;
        const body = await res.json();
        if (!cancelled && body?.success) setRunningJobs(body.total ?? 0);
      } catch {
        // Silently ignore — the badge just won't update this cycle.
      }
    }

    pollRunningJobs();
    const interval = setInterval(pollRunningJobs, RUNNING_JOBS_POLL_MS);
    return () => {
      cancelled = true;
      clearInterval(interval);
    };
  }, [checked]);

  function toggleTheme() {
    setDark((prev) => {
      const next = !prev;
      localStorage.setItem(THEME_KEY, next ? "dark" : "light");
      return next;
    });
  }

  function handleLogout() {
    clearSession();
    router.replace("/login");
  }

  if (!checked || !user) return null;

  // Get navigation items based on user role
  const navigationItems = getNavigationItems(user.role);

  return (
    <div className={`${styles.shell} ${dark ? styles.dark : ""}`} dir={dir}>
      <aside className={`${styles.sidebar} ${sidebarOpen ? styles.sidebarOpen : ""}`}>
        <div className={styles.sidebarHeader}>
          <Link href="/dashboard" className={styles.logo}>
            SCRAPE<span className={styles.logoAccent}>GENIUS</span>
          </Link>
        </div>

        {/* Render navigation grouped by category */}
        {NAVIGATION_GROUPS.map((group) => {
          const groupItems = NAVIGATION_BY_CATEGORY[group.id as keyof typeof NAVIGATION_BY_CATEGORY] || [];
          const visibleItems = groupItems.filter((item) => !item.adminOnly || user.role === "ADMIN");
          
          if (visibleItems.length === 0) return null;
          
          return (
            <nav key={group.id} className={styles.navSection}>
              <div className={styles.navSectionLabel}>
                <FontAwesomeIcon icon={group.icon} className={styles.navIcon} />
                {group.label}
              </div>
              {visibleItems.map((item) => {
                const active = pathname === item.href.split("#")[0];
                const isJobs = item.href === "/dashboard/jobs";
                return (
                  <Link
                    key={item.label}
                    href={item.href}
                    className={`${styles.navItem} ${active ? styles.navItemActive : ""}`}
                    onClick={() => setSidebarOpen(false)}
                  >
                    <FontAwesomeIcon icon={item.icon} className={styles.navIcon} />
                    {item.labelKey ? t(item.labelKey) : item.label}
                    {isJobs && runningJobs > 0 ? (
                      <span className={styles.jobBadge}>{runningJobs}</span>
                    ) : (
                      <FontAwesomeIcon icon={faChevronRight} className={styles.chevron} />
                    )}
                  </Link>
                );
              })}
            </nav>
          );
        })}

        <nav className={styles.navSection} style={{ marginTop: "auto" }}>
          <div className={styles.navSectionLabel}>{t("nav.support")}</div>
          <span className={styles.supportItem}>
            <FontAwesomeIcon icon={faPhone} className={styles.navIcon} />
            {t("nav.callUs")}
          </span>
          <span className={styles.supportItem}>
            <FontAwesomeIcon icon={faEnvelope} className={styles.navIcon} />
            {t("nav.emailUs")}
          </span>
        </nav>
      </aside>

      <div className={styles.main}>
        <header className={styles.topbar}>
          <button
            type="button"
            className={styles.menuButton}
            onClick={() => setSidebarOpen((v) => !v)}
            aria-label="Toggle navigation"
          >
            <FontAwesomeIcon icon={faBars} />
          </button>

          <div />

          <div className={styles.topbarActions}>
            <button
              type="button"
              className={styles.langButton}
              onClick={() => setLocale(locale === "en" ? "ar" : "en")}
              title={t("common.language")}
            >
              {locale === "en" ? LOCALE_LABEL.ar : LOCALE_LABEL.en}
            </button>
            <button type="button" className={styles.iconButton} title="Help">
              <FontAwesomeIcon icon={faCircleQuestion} />
            </button>
            <span className={styles.youtubeBadge}>YouTube</span>
            <button type="button" className={styles.iconButton} onClick={toggleTheme} title="Toggle theme">
              <FontAwesomeIcon icon={dark ? faSun : faMoon} />
            </button>
            <div className={styles.userMenu}>
              <button
                type="button"
                className={styles.iconButton}
                onClick={() => setMenuOpen((v) => !v)}
                title="Account"
              >
                <FontAwesomeIcon icon={faUser} />
              </button>
              {menuOpen && (
                <div className={styles.userDropdown}>
                  <div className={styles.userDropdownName}>{user.name}</div>
                  <div className={styles.userDropdownEmail}>{user.email}</div>
                  <button type="button" className={styles.userDropdownLogout} onClick={handleLogout}>
                    {t("common.logout")}
                  </button>
                </div>
              )}
            </div>
            <button type="button" className={styles.iconButton} onClick={handleLogout} title={t("common.logout")}>
              <FontAwesomeIcon icon={faRightFromBracket} />
            </button>
          </div>
        </header>

        <main className={styles.content}>{children}</main>
      </div>
    </div>
  );
}