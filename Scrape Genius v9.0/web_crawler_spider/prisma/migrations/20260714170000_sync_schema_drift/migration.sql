-- Reconciles the live database with prisma/schema.prisma, preserving existing rows
-- in `users` and `api_keys` (schema had drifted ahead of the DB with no migration applied).
-- NOTE: the `users` and `api_keys` sections of this migration already applied successfully
-- in an earlier partial run (MySQL DDL isn't transactional) and were removed from this file
-- to avoid re-running them; only the remaining steps below are still pending.

-- ============ usage_logs: rename request_count -> count, backfill first ============
ALTER TABLE `usage_logs` DROP FOREIGN KEY `usage_logs_api_key_id_fkey`;
DROP INDEX `usage_logs_api_key_id_date_key` ON `usage_logs`;
DROP INDEX `usage_logs_date_idx` ON `usage_logs`;

ALTER TABLE `usage_logs`
    ADD COLUMN `count` INTEGER NULL,
    ADD COLUMN `search_type` VARCHAR(20) NOT NULL DEFAULT 'search',
    ADD COLUMN `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    ADD COLUMN `updated_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3);

UPDATE `usage_logs` SET `count` = `request_count`;

ALTER TABLE `usage_logs`
    MODIFY `count` INTEGER NOT NULL DEFAULT 0,
    DROP COLUMN `request_count`,
    DROP COLUMN `last_reset_at`;

CREATE INDEX `idx_usage_date_type` ON `usage_logs`(`date`, `search_type`);
CREATE UNIQUE INDEX `uniq_usage_key_date_type` ON `usage_logs`(`api_key_id`, `date`, `search_type`);
ALTER TABLE `usage_logs` ADD CONSTRAINT `usage_logs_api_key_id_fkey` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ============ scraped_records: additive enum values ============
ALTER TABLE `scraped_records` MODIFY `source` ENUM('GOOGLE', 'MAP', 'BING', 'YAHOO', 'DUCKDUCKGO', 'WEBSITE', 'EMAIL', 'PHONE', 'DOCUMENT', 'IMAGE', 'WHOIS', 'INDIAMART', 'JUSTDIAL', 'SULEKHA', 'BUSINESS_DIRECTORY', 'CUSTOM_API', 'INSTAGRAM', 'FACEBOOK', 'LINKEDIN', 'TWITTER', 'TIKTOK', 'YOUTUBE', 'ECOMMERCE', 'REVIEW_PLATFORM', 'REAL_ESTATE', 'JOB_BOARD', 'NEWS_RSS') NOT NULL;

-- ============ New tables ============
CREATE TABLE `scrape_jobs` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `keywords` JSON NOT NULL,
    `config` JSON NULL,
    `status` ENUM('QUEUED', 'RUNNING', 'DONE', 'FAILED', 'CANCELLED') NOT NULL DEFAULT 'QUEUED',
    `progress` INTEGER NOT NULL DEFAULT 0,
    `extracted_count` INTEGER NOT NULL DEFAULT 0,
    `error_message` VARCHAR(1000) NULL,
    `started_at` DATETIME(3) NULL,
    `completed_at` DATETIME(3) NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `scrape_jobs_user_id_status_idx`(`user_id`, `status`),
    INDEX `scrape_jobs_created_at_idx`(`created_at`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `scraper_logs` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `job_id` INTEGER NOT NULL,
    `level` ENUM('INFO', 'WARN', 'ERROR', 'DEBUG') NOT NULL DEFAULT 'INFO',
    `message` VARCHAR(2000) NOT NULL,
    `meta` JSON NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `scraper_logs_job_id_created_at_idx`(`job_id`, `created_at`),
    INDEX `scraper_logs_level_idx`(`level`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `export_records` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `job_id` INTEGER NULL,
    `format` ENUM('XLSX', 'CSV', 'HTML', 'TXT') NOT NULL,
    `file_path` VARCHAR(2048) NOT NULL,
    `file_size` INTEGER NULL,
    `row_count` INTEGER NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `export_records_user_id_idx`(`user_id`),
    INDEX `export_records_job_id_idx`(`job_id`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `refresh_tokens` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `token` VARCHAR(191) NOT NULL,
    `expires_at` DATETIME(3) NOT NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `refresh_tokens_token_key`(`token`),
    INDEX `refresh_tokens_user_id_idx`(`user_id`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `api_client_keys` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `key` VARCHAR(191) NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `rate_limit` INTEGER NOT NULL DEFAULT 1000,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `expiresAt` DATETIME(3) NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT true,

    UNIQUE INDEX `api_client_keys_key_key`(`key`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `api_usage_logs` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `api_key_id` INTEGER NOT NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `api_usage_logs_api_key_id_created_at_idx`(`api_key_id`, `created_at`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `instagram_results` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `job_id` INTEGER NOT NULL,
    `username` VARCHAR(191) NOT NULL,
    `data` JSON NOT NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `instagram_results_job_id_idx`(`job_id`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `user_search_keys` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `google_api_key` VARCHAR(191) NOT NULL,
    `search_engine_id` VARCHAR(191) NOT NULL,
    `daily_limit` INTEGER NOT NULL DEFAULT 100,
    `is_active` BOOLEAN NOT NULL DEFAULT true,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `idx_user_search_keys_user_active`(`user_id`, `is_active`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `user_search_usage_logs` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_search_key_id` INTEGER NOT NULL,
    `date` DATE NOT NULL,
    `request_count` INTEGER NOT NULL DEFAULT 0,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `uniq_user_search_usage_key_date`(`user_search_key_id`, `date`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============ Foreign keys for new tables ============
ALTER TABLE `scrape_jobs` ADD CONSTRAINT `scrape_jobs_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `scraper_logs` ADD CONSTRAINT `scraper_logs_job_id_fkey` FOREIGN KEY (`job_id`) REFERENCES `scrape_jobs`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `export_records` ADD CONSTRAINT `export_records_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `export_records` ADD CONSTRAINT `export_records_job_id_fkey` FOREIGN KEY (`job_id`) REFERENCES `scrape_jobs`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `refresh_tokens` ADD CONSTRAINT `refresh_tokens_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `api_client_keys` ADD CONSTRAINT `api_client_keys_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `api_usage_logs` ADD CONSTRAINT `api_usage_logs_api_key_id_fkey` FOREIGN KEY (`api_key_id`) REFERENCES `api_client_keys`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `instagram_results` ADD CONSTRAINT `instagram_results_job_id_fkey` FOREIGN KEY (`job_id`) REFERENCES `scrape_jobs`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `user_search_keys` ADD CONSTRAINT `user_search_keys_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `user_search_usage_logs` ADD CONSTRAINT `user_search_usage_logs_user_search_key_id_fkey` FOREIGN KEY (`user_search_key_id`) REFERENCES `user_search_keys`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
