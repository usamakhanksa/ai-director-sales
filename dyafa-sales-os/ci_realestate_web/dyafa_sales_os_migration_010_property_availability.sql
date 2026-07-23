-- =====================================================================
-- Dyafa Sales OS - Migration 010: Property Availability Settings
-- Adds: dso_property_blackout_dates
-- Alters: dso_properties.is_bookable
--
-- Scope note: "availability" elsewhere in this app means contract-eligibility
-- (dso_contracts.allowed_properties), NOT a day-by-day room-inventory
-- calendar - there is no such calendar in this schema and this migration
-- does not add one. This migration only adds a simple per-property
-- bookable flag plus a blackout-dates list (Property Management >
-- Availability Settings, Sales-Coordinator managed), structurally
-- identical in spirit to dso_property_rates.
--
-- Idempotent: guarded with information_schema checks so it can be re-run,
-- matching the convention established in migrations 002-009.
-- Import: mysql -u root ci_realestate_web < dyafa_sales_os_migration_010_property_availability.sql
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- dso_properties.is_bookable - simple availability flag, defaults to 1 so
-- every existing property stays bookable unchanged until a Sales
-- Coordinator explicitly turns it off via Availability Settings.
-- ---------------------------------------------------------------------
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_properties' AND COLUMN_NAME = 'is_bookable');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_properties` ADD COLUMN `is_bookable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- dso_property_blackout_dates - per-property blackout date rows, a
-- property has many (mirrors dso_property_rates' one-to-many shape).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dso_property_blackout_dates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `blackout_date` DATE NOT NULL,
  `reason` VARCHAR(200) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_property_blackout_dates_property` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
