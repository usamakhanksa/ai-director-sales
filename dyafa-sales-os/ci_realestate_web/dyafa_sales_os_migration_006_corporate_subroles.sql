-- Dyafa Sales OS - Migration 006
-- Extends dso_users.role ENUM with 5 corporate sub-roles (BRD Section 11:
-- Company User Management) so a corporate account can have more than one
-- flat 'Corporate Client' login: Administrator, HR, Finance, Travel
-- Coordinator, Project Manager - each scoped to the same account_id column
-- that already exists on dso_users.
--
-- MySQL can't add ENUM values conditionally in one statement the way the
-- other migrations guard columns, so this always re-declares the full ENUM
-- (a MODIFY COLUMN with the superset of values is idempotent/safe to re-run).

ALTER TABLE `dso_users`
  MODIFY COLUMN `role` ENUM(
    'HOD Sales','Sales Manager','Sales Executive','Sales Coordinator',
    'Reservation Team','Finance Team','Management','Corporate Client',
    'CorporateAdmin','CorporateHR','CorporateFinance',
    'CorporateTravelCoordinator','CorporateProjectManager'
  ) NOT NULL;
