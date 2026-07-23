-- =====================================================================
-- Dyafa Sales OS - Migration 011: Bulk Sample Seed Data
--
-- NOTE: on inspecting the live dev DB, most tables already carry ~10-18
-- realistic rows beyond what dyafa_sales_os_schema.sql alone seeds
-- (accounts, contracts, leads, reservations, collections, targets,
-- adhoc_sales, activities, property_rates, property_blackout_dates,
-- ai_providers all already populated). This migration only tops up the
-- tables that were genuinely still thin/empty:
--   - dso_teams: 2 -> 10
--   - dso_team_properties / dso_team_accounts: filled out to match
--   - dso_ai_recommendations: 0 -> 10 (the one table with zero rows)
--
-- Idempotent: teams uses ON DUPLICATE KEY UPDATE; team_properties/
-- team_accounts use ON DUPLICATE KEY UPDATE against their unique pair
-- keys; ai_recommendations has no natural unique key so this file
-- should only be imported once (guarded with a row-count check below).
--
-- Import: mysql -u root ci_realestate_web < dyafa_sales_os_migration_011_bulk_seed_data.sql
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- dso_teams: 2 existing (ids 1-2) -> +8 (ids 3-10). No strict FK on
-- hod_user_id, so it's fine to reuse the 5 existing dso_users ids.
-- ---------------------------------------------------------------------
INSERT INTO `dso_teams` (`id`, `name`, `hod_user_id`, `created_at`) VALUES
(3, 'Team Central', 1, NOW()),
(4, 'Team Eastern', 2, NOW()),
(5, 'Team Western', 1, NOW()),
(6, 'Team Northern', 2, NOW()),
(7, 'Team Corporate Accounts', 1, NOW()),
(8, 'Team Hospitality', 2, NOW()),
(9, 'Team Enterprise', 1, NOW()),
(10, 'Team Events', 2, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ---------------------------------------------------------------------
-- dso_team_properties / dso_team_accounts: fill out territory coverage
-- across the new teams and the accounts/properties that exist today.
-- ---------------------------------------------------------------------
INSERT INTO `dso_team_properties` (`team_id`, `property_id`) VALUES
(3, 1), (4, 2), (5, 3), (6, 1), (7, 2), (8, 3), (9, 1), (10, 2)
ON DUPLICATE KEY UPDATE `team_id` = VALUES(`team_id`);

INSERT INTO `dso_team_accounts` (`team_id`, `account_id`) VALUES
(3, 4), (4, 5), (5, 6), (6, 7), (7, 8), (8, 9), (9, 10)
ON DUPLICATE KEY UPDATE `team_id` = VALUES(`team_id`);

-- ---------------------------------------------------------------------
-- dso_ai_recommendations: 0 existing -> 10 rows (heuristic AI Sales
-- Assistant output placeholders, per the model's existing doc comment).
-- Guarded so re-importing this file doesn't duplicate rows.
-- ---------------------------------------------------------------------
SET @ai_recs_count = (SELECT COUNT(*) FROM `dso_ai_recommendations`);
SET @do_seed_ai_recs = IF(@ai_recs_count = 0, 1, 0);

INSERT INTO `dso_ai_recommendations` (`account_id`,`type`,`suggested_action`,`suggested_property_id`,`estimated_revenue`,`priority`,`reason`,`status`,`assigned_to`,`created_at`)
SELECT * FROM (
    SELECT 9 AS account_id,'ContractRenewal' AS type,'Reach out to renew the expired contract before the account churns.' AS suggested_action,1 AS suggested_property_id,80000.00 AS estimated_revenue,'High' AS priority,'Contract expired on 2026-04-30 with no renewal activity logged.' AS reason,'New' AS status,1 AS assigned_to,NOW() AS created_at
    UNION ALL SELECT 3,'InactiveAccount','Re-engage Desert Rose Events with a check-in call and updated rates.',3,25000.00,'Medium','No reservations or activities logged in the last 60 days.','New',1,NOW()
    UNION ALL SELECT 2,'ContractRenewal','Prepare renewal proposal ahead of the August expiry date.',3,20000.00,'High','Contract expires 2026-08-15, no renewal discussion yet.','New',3,NOW()
    UNION ALL SELECT 6,'InactiveAccount','Follow up on the pending contract approval to unlock reservations.',1,25000.00,'Medium','Contract has been pending approval for over 2 weeks.','Actioned',1,NOW()
    UNION ALL SELECT 7,'ContractRenewal','Contract expires soon; offer a multi-year renewal with loyalty discount.',1,35000.00,'Medium','Contract expires 2026-08-10.','New',1,NOW()
    UNION ALL SELECT 4,'InactiveAccount','Suggest bundling adhoc event space with room block for Q4.',2,15000.00,'Low','Account has one confirmed reservation but no adhoc activity.','New',3,NOW()
    UNION ALL SELECT 8,'ContractRenewal','Highlight VIP loyalty perks ahead of contract renewal conversation.',2,20000.00,'Medium','VIP account with contract renewing in under a year.','Dismissed',3,NOW()
    UNION ALL SELECT 5,'InactiveAccount','Offer healthcare-sector group rate to increase room-night volume.',3,30000.00,'Medium','Estimated revenue potential higher than current booking pace.','New',3,NOW()
    UNION ALL SELECT 1,'ContractRenewal','Propose upgraded corporate rate tier given strong booking history.',1,40000.00,'High','Account has consistent high-value bookings this quarter.','Actioned',3,NOW()
    UNION ALL SELECT 10,'InactiveAccount','Introduce hospitality-group multi-property package.',2,50000.00,'High','New account with strong initial adhoc pipeline but no reservations yet.','New',1,NOW()
) AS seed_rows
WHERE @do_seed_ai_recs = 1;
