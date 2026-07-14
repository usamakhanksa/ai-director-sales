-- AlterTable
ALTER TABLE `scraped_records` MODIFY `source` ENUM('GOOGLE', 'MAP', 'BING', 'YAHOO', 'DUCKDUCKGO', 'WEBSITE', 'EMAIL', 'PHONE', 'DOCUMENT', 'IMAGE', 'WHOIS', 'INDIAMART', 'JUSTDIAL', 'SULEKHA', 'BUSINESS_DIRECTORY', 'CUSTOM_API') NOT NULL;

-- CreateTable
CREATE TABLE `api_connectors` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `method` VARCHAR(10) NOT NULL DEFAULT 'GET',
    `url` VARCHAR(2048) NOT NULL,
    `api_key` VARCHAR(500) NULL,
    `auth_type` VARCHAR(20) NOT NULL DEFAULT 'none',
    `auth_param` VARCHAR(100) NULL,
    `results_path` VARCHAR(200) NULL,
    `field_map` JSON NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `api_connectors_user_id_idx`(`user_id`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `crm_connections` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `provider` VARCHAR(30) NOT NULL,
    `login_id` VARCHAR(191) NOT NULL,
    `secret` VARCHAR(500) NOT NULL,
    `last_synced_at` DATETIME(3) NULL,
    `last_status` VARCHAR(500) NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `crm_connections_user_id_provider_key`(`user_id`, `provider`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `api_connectors` ADD CONSTRAINT `api_connectors_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `crm_connections` ADD CONSTRAINT `crm_connections_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
