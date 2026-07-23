-- =====================================================================
-- Dyafa Sales OS - Database schema + seed data
-- Target DB: ci_realestate_web (same DB as legacy app, dbprefix is empty)
-- All tables use the dso_ prefix baked literally into the table name.
-- Engine: InnoDB, Charset: utf8mb4
-- Import: mysql -u root ci_realestate_web < dyafa_sales_os_schema.sql
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- dso_users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_users`;
CREATE TABLE `dso_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('HOD Sales','Sales Manager','Sales Executive','Sales Coordinator','Reservation Team','Finance Team','Management','Corporate Client','CorporateAdmin','CorporateHR','CorporateFinance','CorporateTravelCoordinator','CorporateProjectManager') NOT NULL,
  `account_id` INT UNSIGNED NULL DEFAULT NULL,
  `status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_users_username` (`username`),
  UNIQUE KEY `uq_dso_users_email` (`email`),
  KEY `idx_dso_users_role` (`role`),
  KEY `idx_dso_users_account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_accounts (corporate accounts)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_accounts`;
CREATE TABLE `dso_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` INT UNSIGNED NULL DEFAULT NULL,
  `company_name` VARCHAR(200) NOT NULL,
  `industry` VARCHAR(100) NULL,
  `city` VARCHAR(100) NULL,
  `primary_contact_person` VARCHAR(150) NULL,
  `primary_contact_mobile` VARCHAR(50) NULL,
  `primary_contact_email` VARCHAR(150) NULL,
  `account_owner_id` INT UNSIGNED NULL,
  `status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  `is_vip` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_accounts_contract_id` (`contract_id`),
  KEY `idx_dso_accounts_owner` (`account_owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_contracts
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_contracts`;
CREATE TABLE `dso_contracts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT UNSIGNED NULL DEFAULT NULL,
  `company_name` VARCHAR(200) NOT NULL,
  `contract_number` VARCHAR(50) NOT NULL,
  `start_date` DATE NULL,
  `expiry_date` DATE NULL,
  `payment_terms` VARCHAR(100) NULL,
  `credit_days` INT NULL DEFAULT 0,
  `credit_limit` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `account_manager_id` INT UNSIGNED NULL,
  `allowed_properties` TEXT NULL,
  `corporate_rates` JSON NULL,
  `status` ENUM('Active','Pending Approval','Pending Renewal','Expired','Suspended','Cancelled') NOT NULL DEFAULT 'Pending Approval',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_contracts_number` (`contract_number`),
  KEY `idx_dso_contracts_account` (`account_id`),
  KEY `idx_dso_contracts_manager` (`account_manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_leads
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_leads`;
CREATE TABLE `dso_leads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` VARCHAR(200) NOT NULL,
  `industry` VARCHAR(100) NULL,
  `contact_person` VARCHAR(150) NULL,
  `mobile` VARCHAR(50) NULL,
  `email` VARCHAR(150) NULL,
  `city` VARCHAR(100) NULL,
  `estimated_revenue` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `estimated_room_nights` INT NOT NULL DEFAULT 0,
  `priority` ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  `lead_score` INT NOT NULL DEFAULT 0,
  `lead_category` VARCHAR(20) NULL,
  `suggested_next_action` TEXT NULL,
  `lead_owner_id` INT UNSIGNED NULL,
  `source` ENUM('Referral','Website','ColdCall','Event','Partner','Other','AI Generated') NOT NULL DEFAULT 'Other',
  `status` ENUM('New','Contacted','Qualified','ProposalSent','Negotiation','Won','Lost') NOT NULL DEFAULT 'New',
  `notified` TINYINT NOT NULL DEFAULT 0,
  `is_deleted` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dso_leads_owner` (`lead_owner_id`),
  KEY `idx_dso_leads_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_market_intelligence (seed signals for AI Lead Generation - explicitly
-- synthetic placeholder data, not a real market-data feed; see implementation.md)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_market_intelligence`;
CREATE TABLE `dso_market_intelligence` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `industry` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `company_size_band` ENUM('Small','Medium','Large','Enterprise') NOT NULL DEFAULT 'Medium',
  `avg_estimated_revenue` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `avg_estimated_room_nights` INT NOT NULL DEFAULT 0,
  `signal_strength` INT NOT NULL DEFAULT 50,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_market_intel_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dso_market_intelligence` (`industry`, `city`, `company_size_band`, `avg_estimated_revenue`, `avg_estimated_room_nights`, `signal_strength`, `is_active`) VALUES
('Construction', 'Riyadh', 'Large', 180000.00, 220, 75, 1),
('Oil & Gas', 'Dammam', 'Enterprise', 350000.00, 400, 85, 1),
('Healthcare', 'Jeddah', 'Medium', 90000.00, 120, 60, 1),
('Education', 'Riyadh', 'Medium', 60000.00, 80, 45, 1),
('Government', 'Riyadh', 'Enterprise', 400000.00, 450, 90, 1),
('Retail', 'Jeddah', 'Small', 30000.00, 40, 35, 1),
('Technology', 'Riyadh', 'Medium', 100000.00, 100, 55, 1),
('Logistics', 'Dammam', 'Large', 150000.00, 180, 65, 1);

-- ---------------------------------------------------------------------
-- dso_activities
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_activities`;
CREATE TABLE `dso_activities` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT UNSIGNED NOT NULL,
  `activity_type` ENUM('Call','Meeting','Visit','FollowUp','Reservation','Collection','Complaint','Opportunity') NOT NULL,
  `notes` TEXT NULL,
  `activity_date` DATETIME NOT NULL,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_activities_account` (`account_id`),
  KEY `idx_dso_activities_created_by` (`created_by`),
  KEY `idx_dso_activities_type` (`activity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_reservations
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_reservations`;
CREATE TABLE `dso_reservations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT UNSIGNED NOT NULL,
  `property` VARCHAR(150) NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `rate` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `room_nights` INT NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `status` ENUM('Pending','Confirmed','CheckedIn','Extended','CheckedOut','Cancelled','NoShow') NOT NULL DEFAULT 'Pending',
  `pms_reference` VARCHAR(50) NULL DEFAULT NULL,
  `pms_room_no` VARCHAR(20) NULL DEFAULT NULL,
  `pms_status` VARCHAR(30) NULL DEFAULT NULL,
  `pms_synced_at` DATETIME NULL DEFAULT NULL,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dso_reservations_account` (`account_id`),
  KEY `idx_dso_reservations_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_adhoc_sales
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_adhoc_sales`;
CREATE TABLE `dso_adhoc_sales` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT UNSIGNED NULL DEFAULT NULL,
  `event_type` ENUM('Wedding','Birthday','MeetingRoom','Event','Catering','Conference','Retreat','GroupBooking','CoffeeBreak') NOT NULL,
  `event_date` DATE NOT NULL,
  `pax` INT NOT NULL DEFAULT 0,
  `estimated_value` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `status` ENUM('Inquiry','ProposalSent','Negotiation','Confirmed','Completed','Cancelled','Lost') NOT NULL DEFAULT 'Inquiry',
  `notes` TEXT NULL,
  `owner_id` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_adhoc_owner` (`owner_id`),
  KEY `idx_dso_adhoc_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_targets
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_targets`;
CREATE TABLE `dso_targets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `month` CHAR(7) NOT NULL COMMENT 'format YYYY-MM',
  `revenue_target` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `room_nights_target` INT NOT NULL DEFAULT 0,
  `reservations_target` INT NOT NULL DEFAULT 0,
  `collections_target` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `adhoc_revenue_target` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `meetings_target` INT NOT NULL DEFAULT 0,
  `visits_target` INT NOT NULL DEFAULT 0,
  `calls_target` INT NOT NULL DEFAULT 0,
  `new_leads_target` INT NOT NULL DEFAULT 0,
  `new_contracts_target` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_targets_user_month` (`user_id`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_collections
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_collections`;
CREATE TABLE `dso_collections` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT UNSIGNED NOT NULL,
  `invoice_no` VARCHAR(50) NOT NULL,
  `amount` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `due_date` DATE NOT NULL,
  `paid_amount` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `status` ENUM('Pending','PartiallyPaid','Paid','Overdue') NOT NULL DEFAULT 'Pending',
  `finance_reference` VARCHAR(50) NULL DEFAULT NULL,
  `finance_synced_at` DATETIME NULL DEFAULT NULL,
  `payment_reference` VARCHAR(50) NULL DEFAULT NULL,
  `payment_synced_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_collections_account` (`account_id`),
  KEY `idx_dso_collections_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_notifications
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_notifications`;
CREATE TABLE `dso_notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `role` VARCHAR(50) NULL DEFAULT NULL,
  `type` VARCHAR(50) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_notifications_user` (`user_id`),
  KEY `idx_dso_notifications_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_properties (master property list - Sales Coordinator managed)
-- Reservations/contracts keep matching by plain `name` string (see
-- implementation.md); this table is additive, not a breaking FK migration.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_properties`;
CREATE TABLE `dso_properties` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `city` VARCHAR(100) NULL,
  `address` TEXT NULL,
  `lat` DECIMAL(10,7) NULL DEFAULT NULL,
  `lng` DECIMAL(10,7) NULL DEFAULT NULL,
  `description` TEXT NULL,
  `total_rooms` INT NULL,
  `map_file` VARCHAR(255) NULL,
  `info_file` VARCHAR(255) NULL,
  `status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dso_properties_name` (`name`),
  KEY `idx_dso_properties_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_property_rates (standard rate list per property)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_property_rates`;
CREATE TABLE `dso_property_rates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `rate_type` VARCHAR(100) NOT NULL,
  `rate` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_property_rates_property` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_ai_recommendations (heuristic AI Sales Assistant output, see
-- application/libraries/Dso_sales_assistant.php - documented placeholder,
-- not real ML, generated by Cron::dso_generate_ai_recommendations())
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_ai_recommendations`;
CREATE TABLE `dso_ai_recommendations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT UNSIGNED NOT NULL,
  `type` ENUM('InactiveAccount','ContractRenewal') NOT NULL,
  `suggested_action` TEXT NOT NULL,
  `suggested_property_id` INT UNSIGNED NULL,
  `estimated_revenue` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `priority` ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  `reason` TEXT NULL,
  `status` ENUM('New','Actioned','Dismissed') NOT NULL DEFAULT 'New',
  `assigned_to` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dso_ai_recs_account` (`account_id`),
  KEY `idx_dso_ai_recs_status` (`status`),
  KEY `idx_dso_ai_recs_assigned` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_ai_providers - configured LLM provider connections used by
-- Dso_llm_client.php to enhance the free-text fields (suggested_action,
-- reason) of AI Sales Assistant recommendations. api_key_encrypted is
-- CI Encryption library ciphertext (application/config/config.php
-- encryption_key must be set) - never decrypted for display, only
-- key_last4 is shown in the admin UI. Exactly one row should have
-- is_default = 1 at a time (enforced in Dso_ai_providers_model, not here).
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dso_ai_providers`;
CREATE TABLE `dso_ai_providers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider_key` VARCHAR(40) NOT NULL,
  `label` VARCHAR(100) NOT NULL,
  `base_url` VARCHAR(255) NOT NULL,
  `api_key_encrypted` TEXT NULL,
  `key_last4` VARCHAR(4) NULL,
  `model` VARCHAR(100) NOT NULL,
  `extra_params` JSON NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `last_test_status` ENUM('Untested','Success','Failed') NOT NULL DEFAULT 'Untested',
  `last_test_message` VARCHAR(255) NULL,
  `last_tested_at` DATETIME NULL,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dso_ai_providers_key` (`provider_key`),
  KEY `idx_dso_ai_providers_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- dso_adhoc_sales - add optional venue link (no legacy string data to
-- preserve here, unlike dso_reservations.property / allowed_properties)
-- ---------------------------------------------------------------------
ALTER TABLE `dso_adhoc_sales`
  ADD COLUMN `venue_property_id` INT UNSIGNED NULL AFTER `event_type`,
  ADD KEY `idx_dso_adhoc_venue` (`venue_property_id`);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- Users (plaintext passwords documented in implementation.md)
-- Anas / Passw0rd!            role: HOD Sales
-- Nidal / Passw0rd!           role: Sales Manager
-- Ahmad Saleh / Passw0rd!     role: Sales Executive
-- Ali / Passw0rd!             role: Sales Coordinator
-- corporate1 / Client123!     role: Corporate Client (linked to account 1)
INSERT INTO `dso_users` (`id`,`name`,`email`,`username`,`password`,`role`,`account_id`,`status`,`created_at`) VALUES
(1,'Anas','anas@dyafa.com','anas','$2y$10$.wO5xuTxjqU2iKzTL5KuSO2pSlZOgO6Xc7XYWssGcDEAa8/MKsUJS','HOD Sales',NULL,'Active',NOW()),
(2,'Nidal','nidal@dyafa.com','nidal','$2y$10$.wO5xuTxjqU2iKzTL5KuSO2pSlZOgO6Xc7XYWssGcDEAa8/MKsUJS','Sales Manager',NULL,'Active',NOW()),
(3,'Ahmad Saleh','ahmad.saleh@dyafa.com','ahmad.saleh','$2y$10$.wO5xuTxjqU2iKzTL5KuSO2pSlZOgO6Xc7XYWssGcDEAa8/MKsUJS','Sales Executive',NULL,'Active',NOW()),
(4,'Ali','ali@dyafa.com','ali','$2y$10$.wO5xuTxjqU2iKzTL5KuSO2pSlZOgO6Xc7XYWssGcDEAa8/MKsUJS','Sales Coordinator',NULL,'Active',NOW()),
(5,'Corporate Client One','client1@acmecorp.com','corporate1','$2y$10$6l5d1dfeeboABTw1wpvgrOcFNRdbvkH8SDdc44nZmqgbiTQ/83B5G','Corporate Client',1,'Active',NOW());

-- Accounts (account_owner_id references dso_users)
INSERT INTO `dso_accounts` (`id`,`contract_id`,`company_name`,`industry`,`city`,`primary_contact_person`,`primary_contact_mobile`,`primary_contact_email`,`account_owner_id`,`status`,`created_at`) VALUES
(1,1,'Acme Corp','Manufacturing','Riyadh','Sara Al-Otaibi','0501111111','sara@acmecorp.com',3,'Active',NOW()),
(2,2,'Falcon Trading','Trading','Jeddah','Omar Fahad','0502222222','omar@falcontrading.com',3,'Active',NOW()),
(3,NULL,'Desert Rose Events','Events','Dammam','Huda Nasser','0503333333','huda@desertrose.com',1,'Active',NOW());

-- Contracts
INSERT INTO `dso_contracts` (`id`,`account_id`,`company_name`,`contract_number`,`start_date`,`expiry_date`,`payment_terms`,`credit_days`,`credit_limit`,`account_manager_id`,`allowed_properties`,`corporate_rates`,`status`,`created_at`) VALUES
(1,1,'Acme Corp','CN-2026-001','2026-01-01','2026-12-31','Net 30',30,50000.00,3,'Dyafa Riyadh Tower,Dyafa Business Suites','{"Dyafa Riyadh Tower":350,"Dyafa Business Suites":300}','Active',NOW()),
(2,2,'Falcon Trading','CN-2026-002','2026-02-01','2026-08-15','Net 15',15,20000.00,3,'Dyafa Jeddah Corniche','{"Dyafa Jeddah Corniche":280}','Pending Renewal',NOW()),
(3,NULL,'New Prospect LLC','CN-2026-003','2026-06-01','2027-05-31','Net 30',30,30000.00,1,'Dyafa Riyadh Tower','{"Dyafa Riyadh Tower":320}','Pending Approval',NOW());

UPDATE `dso_accounts` SET `contract_id` = 1 WHERE `id` = 1;
UPDATE `dso_accounts` SET `contract_id` = 2 WHERE `id` = 2;

-- Leads (varied scores/categories)
INSERT INTO `dso_leads` (`company_name`,`industry`,`contact_person`,`mobile`,`email`,`city`,`estimated_revenue`,`estimated_room_nights`,`priority`,`lead_score`,`lead_category`,`suggested_next_action`,`lead_owner_id`,`source`,`status`,`notified`,`is_deleted`,`created_at`) VALUES
('Gulf Steel Co','Manufacturing','Khalid Fahmy','0511111111','khalid@gulfsteel.com','Riyadh',480000,450,'High',97,'Hot','Immediate call within 24 hours and schedule site visit',1,'Referral','New',0,0,NOW()),
('Zenith Pharma','Healthcare','Lina Youssef','0512222222','lina@zenithpharma.com','Jeddah',300000,300,'High',82,'High','Call within 48 hours and send tailored proposal',1,'Website','Contacted',0,0,NOW()),
('Coral Bay Resort Ops','Hospitality','Yousef Al-Amri','0513333333','yousef@coralbay.com','Dammam',150000,150,'Medium',63,'Medium','Schedule a follow-up call within the week',3,'Event','New',0,0,NOW()),
('Bright Path School','Education','Maha Idris','0514444444','maha@brightpath.edu','Riyadh',60000,60,'Low',44,'Low','Add to nurture campaign and re-check quarterly',3,'ColdCall','New',0,0,NOW()),
('Tiny Traders','Retail','Faisal Noor','0515555555','faisal@tinytraders.com','Khobar',10000,10,'Low',22,'Discard','Nurture via email campaign only',4,'Other','New',0,0,NOW()),
('Al-Noor Logistics','Logistics','Rania Sami','0516666666','rania@alnoorlog.com','Riyadh',220000,200,'Medium',71,'Medium','Schedule a follow-up call within the week',1,'Partner','Qualified',0,0,NOW()),
('Skyline Consulting','Consulting','Tariq Hamdan','0517777777','tariq@skylineconsult.com','Jeddah',420000,380,'High',91,'High','Call within 48 hours and send tailored proposal',3,'Referral','ProposalSent',0,0,NOW());

-- Reservations (linked to accounts 1 and 2, respecting contract allowed_properties)
INSERT INTO `dso_reservations` (`account_id`,`property`,`check_in`,`check_out`,`rate`,`room_nights`,`total_amount`,`status`,`created_by`,`created_at`) VALUES
(1,'Dyafa Riyadh Tower','2026-07-01','2026-07-05',350.00,4,1400.00,'Confirmed',3,NOW()),
(2,'Dyafa Jeddah Corniche','2026-07-10','2026-07-12',280.00,2,560.00,'CheckedOut',3,NOW());

-- Collections (one overdue for aging report testing)
INSERT INTO `dso_collections` (`account_id`,`invoice_no`,`amount`,`due_date`,`paid_amount`,`status`,`created_at`) VALUES
(1,'INV-2026-1001',1400.00,'2026-06-15',0.00,'Overdue',NOW()),
(2,'INV-2026-1002',560.00,'2026-07-25',200.00,'PartiallyPaid',NOW());

-- Target for Ahmad Saleh (Sales Executive, user id 3) for current month
INSERT INTO `dso_targets` (`user_id`,`month`,`revenue_target`,`room_nights_target`,`reservations_target`,`collections_target`,`adhoc_revenue_target`,`meetings_target`,`visits_target`,`calls_target`,`new_leads_target`,`new_contracts_target`,`created_at`) VALUES
(3,'2026-07',20000.00,150,20,15000.00,5000.00,10,8,40,10,2,NOW());

-- Properties - names copied verbatim from dso_contracts.allowed_properties /
-- corporate_rates keys and dso_reservations.property above so the existing
-- string-matching allowed-properties validation keeps matching unchanged.
INSERT INTO `dso_properties` (`id`,`name`,`city`,`total_rooms`,`status`,`created_by`,`created_at`) VALUES
(1,'Dyafa Riyadh Tower','Riyadh',180,'Active',4,NOW()),
(2,'Dyafa Business Suites','Riyadh',90,'Active',4,NOW()),
(3,'Dyafa Jeddah Corniche','Jeddah',120,'Active',4,NOW());
