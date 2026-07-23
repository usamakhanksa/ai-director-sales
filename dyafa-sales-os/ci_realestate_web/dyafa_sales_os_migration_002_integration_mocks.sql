-- ---------------------------------------------------------------------
-- Dyafa Sales OS - Migration 002
-- Adds PMS/Finance mock-integration reference columns.
-- Safe to run on an existing install; run once after
-- dyafa_sales_os_schema.sql. New installs get these columns directly from
-- the updated schema file, this migration is for databases created before
-- this change.
-- ---------------------------------------------------------------------

ALTER TABLE `dso_reservations`
  ADD COLUMN `pms_reference` VARCHAR(50) NULL DEFAULT NULL AFTER `status`,
  ADD COLUMN `pms_room_no` VARCHAR(20) NULL DEFAULT NULL AFTER `pms_reference`,
  ADD COLUMN `pms_status` VARCHAR(30) NULL DEFAULT NULL AFTER `pms_room_no`,
  ADD COLUMN `pms_synced_at` DATETIME NULL DEFAULT NULL AFTER `pms_status`;

ALTER TABLE `dso_collections`
  ADD COLUMN `finance_reference` VARCHAR(50) NULL DEFAULT NULL AFTER `status`,
  ADD COLUMN `finance_synced_at` DATETIME NULL DEFAULT NULL AFTER `finance_reference`;
