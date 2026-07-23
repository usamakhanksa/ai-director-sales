-- =====================================================================
-- Dyafa Sales OS - Migration 009: Generic Activities module
-- Alters: dso_activities.account_id becomes nullable so the new top-level
--         Activities module (My Activities / Team Activities / Log Activity)
--         can log an activity without requiring an account/lead to be
--         picked first. Accounts::add_activity($account_id) keeps working
--         unchanged - it still always supplies an account_id.
-- Idempotent: guarded with information_schema checks so it can be re-run,
-- matching the convention established in migrations 002-008.
-- Import: mysql -u root ci_realestate_web < dyafa_sales_os_migration_009_activities_generic.sql
-- =====================================================================

SET NAMES utf8mb4;

SET @col_nullable = (
  SELECT IS_NULLABLE FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_activities' AND COLUMN_NAME = 'account_id'
);
SET @sql = IF(@col_nullable = 'NO', 'ALTER TABLE `dso_activities` MODIFY COLUMN `account_id` INT UNSIGNED NULL DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
