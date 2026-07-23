-- =====================================================================
-- Dyafa Sales OS - Migration 012: Audit trail, soft delete, encrypted
-- integration credentials, mandatory 2FA for Corporate Finance users.
--
-- Closes enhance.md/todolist.md section B gaps:
--   - Soft delete + audit trail on financially/legally significant
--     entities (Contracts, Corporate Accounts, Adhoc Sales, Properties,
--     Collections, Targets, Roles, Teams) - Leads already had soft_delete().
--   - dso_audit_log - single shared audit trail table, written from
--     Dso_Controller::audit() at the top of every add()/edit()/delete().
--   - dso_integration_credentials - encrypted-at-rest PMS/Finance/Maps/
--     Payment/Reporting API keys (mirrors the existing
--     Dso_ai_providers.api_key_encrypted boundary), replacing the plaintext
--     var_export() write in Admin/Integrations.php. mode/endpoint/timeout
--     stay in application/config/dso_integrations.php (non-secret).
--   - dso_users TOTP columns - mandatory 2FA enrollment for the
--     CorporateFinance corporate sub-role (BRD Section 10 lists 2FA as
--     optional, but the same portal exposes invoices/credit limits/
--     outstanding balances to this specific sub-role - see Portal.php).
--
-- Idempotent: guarded with information_schema checks / CREATE TABLE IF NOT
-- EXISTS, matching the convention established in migrations 002-011.
-- Import: mysql -u root ci_realestate_web < dyafa_sales_os_migration_012_audit_soft_delete_security.sql
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- deleted_at - additive soft-delete column on every entity that still
-- hard-deletes today. NULL = not deleted; a timestamp = soft-deleted.
-- All existing get_all()/get() queries are updated in the model layer to
-- filter `deleted_at IS NULL`, so existing seeded data is unaffected.
-- ---------------------------------------------------------------------
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_contracts' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_contracts` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `updated_at`, ADD KEY `idx_dso_contracts_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_accounts' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_accounts` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`, ADD KEY `idx_dso_accounts_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_adhoc_sales' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_adhoc_sales` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`, ADD KEY `idx_dso_adhoc_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_properties' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_properties` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `updated_at`, ADD KEY `idx_dso_properties_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_collections' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_collections` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`, ADD KEY `idx_dso_collections_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_targets' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_targets` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`, ADD KEY `idx_dso_targets_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_roles' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_roles` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`, ADD KEY `idx_dso_roles_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_teams' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_teams` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`, ADD KEY `idx_dso_teams_deleted_at` (`deleted_at`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- dso_audit_log - single shared audit trail for every add()/edit()/
-- delete() across the 8 entities above, written by Dso_Controller::audit().
-- before_json/after_json are the full row (or posted data) snapshots.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dso_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `table_name` VARCHAR(100) NOT NULL,
  `row_id` INT UNSIGNED NULL DEFAULT NULL,
  `action` ENUM('create','update','delete') NOT NULL,
  `before_json` TEXT NULL,
  `after_json` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_audit_log_table_row` (`table_name`,`row_id`),
  KEY `idx_dso_audit_log_user` (`user_id`),
  KEY `idx_dso_audit_log_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_integration_credentials - encrypted-at-rest API keys for the
-- PMS/Finance/Maps/Payment/Reporting integrations, one row per
-- integration_key ('dso_pms','dso_finance','dso_maps','dso_payment',
-- 'dso_reporting'). Mirrors dso_ai_providers.api_key_encrypted /
-- key_last4 exactly (see Dso_ai_providers_model). mode/endpoint/timeout
-- remain non-secret settings in application/config/dso_integrations.php.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dso_integration_credentials` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `integration_key` VARCHAR(40) NOT NULL,
  `api_key_encrypted` TEXT NULL,
  `key_last4` VARCHAR(4) NULL,
  `updated_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_integration_credentials_key` (`integration_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_users - TOTP 2FA columns. totp_secret_encrypted uses the same CI
-- Encryption library boundary as dso_ai_providers.api_key_encrypted
-- (never decrypted for display). totp_enabled is set once the user
-- completes enrollment; CorporateFinance users are forced through the
-- enrollment screen on next login until this flips to 1 (see Portal.php).
-- ---------------------------------------------------------------------
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_users' AND COLUMN_NAME = 'totp_secret_encrypted');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_users` ADD COLUMN `totp_secret_encrypted` TEXT NULL DEFAULT NULL AFTER `status`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_users' AND COLUMN_NAME = 'totp_enabled');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_users` ADD COLUMN `totp_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `totp_secret_encrypted`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- Administration > Audit Log viewer permission (surfaces dso_audit_log,
-- written by Dso_Controller::audit()/soft_delete_row() above). Granted to
-- the same roles as manage_roles, matching the existing Administration
-- permission convention in migration 008.
-- ---------------------------------------------------------------------
INSERT INTO `dso_permissions` (`permission_key`, `label`, `group_name`) VALUES
('view_audit_log', 'View Audit Log', 'Administration')
ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `group_name` = VALUES(`group_name`);

INSERT INTO `dso_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `dso_roles` r JOIN `dso_permissions` p ON (
  r.name IN ('HOD Sales', 'Management') AND p.permission_key = 'view_audit_log'
)
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);
