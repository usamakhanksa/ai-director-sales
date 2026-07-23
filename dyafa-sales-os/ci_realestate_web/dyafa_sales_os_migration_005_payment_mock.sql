-- Dyafa Sales OS - Migration 005
-- Adds payment_reference/payment_synced_at to dso_collections, mirroring
-- migration 002's finance_reference/finance_synced_at pattern, so the
-- Payment Gateway mock integration has somewhere to persist its reference.
-- Safe to re-run: guarded by information_schema check.

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dso_collections'
    AND COLUMN_NAME = 'payment_reference'
);

SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `dso_collections` ADD COLUMN `payment_reference` VARCHAR(50) NULL DEFAULT NULL AFTER `finance_synced_at`, ADD COLUMN `payment_synced_at` DATETIME NULL DEFAULT NULL AFTER `payment_reference`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
