-- =====================================================================
-- Dyafa Sales OS - Migration 008: Administration (dynamic RBAC + Teams)
-- Adds: dso_roles, dso_permissions, dso_role_permissions, dso_teams,
--       dso_team_properties, dso_team_accounts, dso_lead_scoring_config
-- Alters: dso_users (role_id, team_id), dso_ai_recommendations.type enum
--         (adds Prediction/NextBestAction for the AI Sales Assistant
--         Predictions / Next Best Actions sub-views)
-- Idempotent: guarded with information_schema checks so it can be re-run,
-- matching the convention established in migrations 002-007.
-- Import: mysql -u root ci_realestate_web < dyafa_sales_os_migration_008_admin_rbac_teams.sql
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- dso_roles - replaces the hardcoded array in application/config/dso_roles.php
-- as the source of truth for role management (Administration > Users & Roles).
-- The legacy dso_users.role string column is kept unmodified for backward
-- compatibility with every existing require_role() call site; role_id is
-- additive and is what new Dso_Controller::require_permission() code path uses.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dso_roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = seeded from legacy dso_users.role enum, cannot be deleted',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dso_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `permission_key` VARCHAR(100) NOT NULL,
  `label` VARCHAR(150) NOT NULL,
  `group_name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_permissions_key` (`permission_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dso_role_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_role_permissions` (`role_id`,`permission_id`),
  KEY `idx_dso_role_permissions_role` (`role_id`),
  KEY `idx_dso_role_permissions_permission` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_teams - Administration > Teams, also drives Team Performance
-- dashboard and territory-based data scoping (dso_team_properties /
-- dso_team_accounts). A team with zero territory rows means "no
-- restriction" for its members, so existing seeded data keeps working
-- unchanged until an HOD explicitly assigns a territory.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dso_teams` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `hod_user_id` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_teams_hod` (`hod_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dso_team_properties` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` INT UNSIGNED NOT NULL,
  `property_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_team_properties` (`team_id`,`property_id`),
  KEY `idx_dso_team_properties_team` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dso_team_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` INT UNSIGNED NOT NULL,
  `account_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_team_accounts` (`team_id`,`account_id`),
  KEY `idx_dso_team_accounts_team` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_lead_scoring_config - Administration-adjacent HOD screen (AI Lead
-- Generation > Lead Scoring Config) to tune the weights that
-- application/libraries/Dso_lead_scoring.php currently hardcodes. Empty
-- table = library falls back to its existing hardcoded defaults, so this
-- migration alone changes no runtime behavior until rows are added via the UI.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dso_lead_scoring_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `signal_key` VARCHAR(100) NOT NULL COMMENT 'e.g. estimated_revenue, estimated_room_nights, priority, market_intelligence_match',
  `label` VARCHAR(150) NOT NULL,
  `weight` INT NOT NULL DEFAULT 0,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_lead_scoring_config_signal` (`signal_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_users - additive role_id/team_id. Legacy `role` string column and
-- every existing require_role() call site keep working unchanged.
-- ---------------------------------------------------------------------
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_users' AND COLUMN_NAME = 'role_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_users` ADD COLUMN `role_id` INT UNSIGNED NULL DEFAULT NULL AFTER `role`, ADD KEY `idx_dso_users_role_id` (`role_id`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dso_users' AND COLUMN_NAME = 'team_id');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `dso_users` ADD COLUMN `team_id` INT UNSIGNED NULL DEFAULT NULL AFTER `role_id`, ADD KEY `idx_dso_users_team_id` (`team_id`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- dso_ai_recommendations.type - extend enum with Prediction/NextBestAction
-- so AiAssistant::predictions()/next_best_actions() have real, distinct
-- data to filter on instead of reusing the existing two types.
-- ---------------------------------------------------------------------
ALTER TABLE `dso_ai_recommendations`
  MODIFY COLUMN `type` ENUM('InactiveAccount','ContractRenewal','Prediction','NextBestAction') NOT NULL;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- Roles: one row per existing dso_roles.php entry, is_system = 1 (cannot
-- be deleted from the Administration > Users & Roles screen).
INSERT INTO `dso_roles` (`name`, `is_system`) VALUES
('HOD Sales', 1),
('Sales Manager', 1),
('Sales Executive', 1),
('Sales Coordinator', 1),
('Reservation Team', 1),
('Finance Team', 1),
('Management', 1),
('Corporate Client', 1)
ON DUPLICATE KEY UPDATE `is_system` = VALUES(`is_system`);

-- Permissions: one per distinct require_role()-gated action cluster found
-- across the controllers (dashboard/hod, leads/assign, contracts add/edit/
-- delete, collections edit, targets add/edit/delete, aiassistant/generate,
-- aiconfig/*, properties add/edit/delete/rates) plus the new Administration
-- screens (manage_users, manage_roles, manage_teams, manage_integrations,
-- manage_notifications, manage_lead_scoring).
INSERT INTO `dso_permissions` (`permission_key`, `label`, `group_name`) VALUES
('view_hod_dashboard', 'View HOD Dashboard', 'Dashboard'),
('view_team_performance', 'View Team Performance', 'Dashboard'),
('assign_leads', 'Assign Leads', 'Leads'),
('manage_lead_scoring', 'Manage Lead Scoring Config', 'Leads'),
('generate_leads', 'Generate AI Leads', 'Leads'),
('manage_contracts', 'Add / Edit / Delete Contracts', 'Contracts'),
('manage_collections', 'Edit Collections', 'Collections'),
('manage_targets', 'Add / Edit / Delete Targets', 'Targets'),
('generate_ai_recommendations', 'Generate AI Recommendations', 'AI Sales Assistant'),
('manage_ai_config', 'Manage AI Provider Config', 'AI Sales Assistant'),
('manage_properties', 'Add / Edit / Delete Properties', 'Property Management'),
('manage_users', 'Manage Users', 'Administration'),
('manage_roles', 'Manage Roles & Permissions', 'Administration'),
('manage_teams', 'Manage Teams', 'Administration'),
('manage_integrations', 'Manage Integrations', 'Administration'),
('manage_notifications', 'Manage Notification Center', 'Administration')
ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `group_name` = VALUES(`group_name`);

-- Role/permission grants matching current hardcoded require_role() arrays.
INSERT INTO `dso_role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `dso_roles` r JOIN `dso_permissions` p ON (
  (r.name IN ('HOD Sales','Sales Manager','Management') AND p.permission_key IN ('view_hod_dashboard','view_team_performance','manage_contracts','generate_ai_recommendations','manage_ai_config','manage_targets'))
  OR (r.name IN ('HOD Sales','Management') AND p.permission_key IN ('assign_leads','manage_lead_scoring','generate_leads','manage_users','manage_roles','manage_teams','manage_integrations','manage_notifications'))
  OR (r.name IN ('Finance Team','HOD Sales','Management') AND p.permission_key = 'manage_collections')
  OR (r.name = 'Sales Coordinator' AND p.permission_key = 'manage_properties')
  OR (r.name = 'Sales Executive' AND p.permission_key = 'manage_contracts')
)
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- Backfill dso_users.role_id from the legacy role string for all seeded users.
UPDATE `dso_users` u JOIN `dso_roles` r ON r.name = u.role SET u.role_id = r.id WHERE u.role_id IS NULL;

-- Teams: two seed teams, HOD Sales (user 1) leads Team Riyadh, Sales
-- Manager (user 2) leads Team Coastal, with Sales Executive (user 3) and
-- Sales Coordinator (user 4) assigned so Team Performance has real data.
INSERT INTO `dso_teams` (`id`, `name`, `hod_user_id`, `created_at`) VALUES
(1, 'Team Riyadh', 1, NOW()),
(2, 'Team Coastal', 2, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

UPDATE `dso_users` SET `team_id` = 1 WHERE `id` IN (1, 3) AND `team_id` IS NULL;
UPDATE `dso_users` SET `team_id` = 2 WHERE `id` IN (2, 4) AND `team_id` IS NULL;

-- Territory: Team Riyadh owns the two Riyadh properties + Acme Corp
-- account; Team Coastal owns the Jeddah property + Falcon Trading. Desert
-- Rose Events (account 3) is deliberately left unassigned to any team, so
-- the "falls back to no restriction" rule can be exercised/verified too.
INSERT INTO `dso_team_properties` (`team_id`, `property_id`) VALUES
(1, 1), (1, 2), (2, 3)
ON DUPLICATE KEY UPDATE `team_id` = VALUES(`team_id`);

INSERT INTO `dso_team_accounts` (`team_id`, `account_id`) VALUES
(1, 1), (2, 2)
ON DUPLICATE KEY UPDATE `team_id` = VALUES(`team_id`);

-- Lead scoring config seed - mirrors the current hardcoded weights in
-- Dso_lead_scoring.php so turning this table on changes nothing until an
-- HOD actually edits a weight via the new UI.
INSERT INTO `dso_lead_scoring_config` (`signal_key`, `label`, `weight`, `updated_at`) VALUES
('estimated_revenue', 'Estimated Revenue Weight', 40, NOW()),
('estimated_room_nights', 'Estimated Room Nights Weight', 30, NOW()),
('priority', 'Stated Priority Weight', 15, NOW()),
('market_intelligence_match', 'Market Intelligence Match Weight', 15, NOW())
ON DUPLICATE KEY UPDATE `label` = VALUES(`label`);
