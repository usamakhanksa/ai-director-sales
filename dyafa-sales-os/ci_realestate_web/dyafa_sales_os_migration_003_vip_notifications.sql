-- Dyafa Sales OS - Migration 003
-- Adds an is_vip flag to dso_accounts so Cron::_notify_vip_arrival() has a
-- real signal to trigger on (no such flag existed before this migration).
-- Safe to re-run: guarded by information_schema check.

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dso_accounts'
    AND COLUMN_NAME = 'is_vip'
);

SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `dso_accounts` ADD COLUMN `is_vip` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
