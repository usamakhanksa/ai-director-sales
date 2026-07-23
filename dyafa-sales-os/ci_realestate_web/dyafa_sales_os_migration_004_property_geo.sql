-- Dyafa Sales OS - Migration 004
-- Adds lat/lng geo columns to dso_properties so Property Management can show
-- a real embeddable map, not just an uploaded static image/PDF.
-- Safe to re-run: guarded by information_schema check.

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dso_properties'
    AND COLUMN_NAME = 'lat'
);

SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `dso_properties` ADD COLUMN `lat` DECIMAL(10,7) NULL DEFAULT NULL AFTER `address`, ADD COLUMN `lng` DECIMAL(10,7) NULL DEFAULT NULL AFTER `lat`',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
