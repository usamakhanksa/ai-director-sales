-- Dyafa Sales OS - Migration 007
-- AI Lead Generation: adds an 'AI Generated' source to dso_leads (so
-- synthesized candidate leads are clearly distinguishable from human-entered
-- ones) and a small dso_market_intelligence seed table the generator reads
-- from (industries/cities/company-size signals - explicitly synthetic
-- placeholder data, not a real data-acquisition feed; see implementation.md).

ALTER TABLE `dso_leads`
  MODIFY COLUMN `source` ENUM('Referral','Website','ColdCall','Event','Partner','Other','AI Generated') NOT NULL DEFAULT 'Other';

DROP TABLE IF EXISTS `dso_market_intelligence`;
CREATE TABLE `dso_market_intelligence` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `industry` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `company_size_band` ENUM('Small','Medium','Large','Enterprise') NOT NULL DEFAULT 'Medium',
  `avg_estimated_revenue` DECIMAL(14,2) NOT NULL DEFAULT 0,
  `avg_estimated_room_nights` INT NOT NULL DEFAULT 0,
  `signal_strength` INT NOT NULL DEFAULT 50 COMMENT '0-100, how strong this market signal is - feeds directly into the generated lead priority',
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
