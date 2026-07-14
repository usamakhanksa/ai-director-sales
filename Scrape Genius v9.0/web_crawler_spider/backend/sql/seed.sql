-- ScrapeGenius seed data (raw SQL alternative to `npm run seed`)
-- Password for admin@scrapegenius.com is: ChangeMe123!  (bcrypt hash below, cost factor 10)
-- CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN.

USE `google-map-scraper-pro`;

INSERT INTO `users` (`name`, `email`, `password_hash`, `country`, `verified`, `purchase_code_verified`, `admin`)
SELECT 'ScrapeGenius Admin', 'admin@scrapegenius.com',
       '$2a$10$PT2n0gaJBFBZFeFxIWHIiOR6DnEWBU0sgixCEspDm7iZYd8Qq474e',
       'N/A', 1, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `email` = 'admin@scrapegenius.com');

INSERT INTO `api_keys` (`key`, `cx`, `provider`, `is_active`, `daily_limit`)
SELECT 'REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_1', 'REPLACE_WITH_REAL_SEARCH_ENGINE_ID_1', 'google_custom_search', 1, 100
WHERE NOT EXISTS (SELECT 1 FROM `api_keys` WHERE `key` = 'REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_1');

INSERT INTO `api_keys` (`key`, `cx`, `provider`, `is_active`, `daily_limit`)
SELECT 'REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_2', 'REPLACE_WITH_REAL_SEARCH_ENGINE_ID_2', 'google_custom_search', 1, 100
WHERE NOT EXISTS (SELECT 1 FROM `api_keys` WHERE `key` = 'REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_2');

-- Pseudo key so /v1/search/maps has a usage_logs row to reserve against.
INSERT INTO `api_keys` (`key`, `cx`, `provider`, `is_active`, `daily_limit`)
SELECT 'local-maps-scraper', 'n/a', 'maps_scraper', 1, 1000
WHERE NOT EXISTS (SELECT 1 FROM `api_keys` WHERE `key` = 'local-maps-scraper');

INSERT INTO `usage_logs` (`api_key_id`, `date`, `count`, `search_type`)
SELECT k.id, CURDATE(), 0, 'search'
FROM `api_keys` k
WHERE k.`key` = 'REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_1'
  AND NOT EXISTS (
    SELECT 1 FROM `usage_logs` u
    WHERE u.api_key_id = k.id AND u.date = CURDATE() AND u.search_type = 'search'
  );
