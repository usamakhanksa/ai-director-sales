-- ScrapeGenius MySQL schema
-- Safe to re-run: every statement uses IF NOT EXISTS.
-- This is a static reference copy of what the Knex migrations in ../migrations create.

CREATE DATABASE IF NOT EXISTS `google-map-scraper-pro`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `google-map-scraper-pro`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `password_hash` VARCHAR(255) NULL,
  `country` VARCHAR(100) NULL,
  `verified` TINYINT(1) NOT NULL DEFAULT 0,
  `verification_code` VARCHAR(10) NULL,
  `verification_code_expires_at` TIMESTAMP NULL,
  `reset_token` VARCHAR(191) NULL,
  `reset_token_expires_at` TIMESTAMP NULL,
  `purchase_code` VARCHAR(191) NULL,
  `purchase_code_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `admin` TINYINT(1) NOT NULL DEFAULT 0,
  `google_id` VARCHAR(191) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`),
  UNIQUE KEY `uniq_users_google_id` (`google_id`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(512) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `revoked` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_auth_tokens_token` (`token`),
  KEY `idx_auth_tokens_user_id` (`user_id`),
  CONSTRAINT `fk_auth_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(191) NOT NULL,
  `cx` VARCHAR(191) NOT NULL,
  `provider` VARCHAR(50) NOT NULL DEFAULT 'google_custom_search',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `daily_limit` INT UNSIGNED NOT NULL DEFAULT 100,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_api_keys_key` (`key`),
  KEY `idx_api_keys_provider_active` (`provider`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `usage_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_key_id` INT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `count` INT UNSIGNED NOT NULL DEFAULT 0,
  `search_type` VARCHAR(20) NOT NULL DEFAULT 'search',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_usage_key_date_type` (`api_key_id`, `date`, `search_type`),
  KEY `idx_usage_date_type` (`date`, `search_type`),
  CONSTRAINT `fk_usage_logs_api_key` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `search_queries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `query` VARCHAR(500) NOT NULL,
  `result_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `search_type` VARCHAR(20) NOT NULL DEFAULT 'search',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_search_queries_user_created` (`user_id`, `created_at`),
  CONSTRAINT `fk_search_queries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `maps_results` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `search_query_id` INT UNSIGNED NULL,
  `place_name` VARCHAR(255) NULL,
  `address` VARCHAR(500) NULL,
  `phone` VARCHAR(50) NULL,
  `website` VARCHAR(500) NULL,
  `category` VARCHAR(255) NULL,
  `rating` DECIMAL(2,1) NULL,
  `reviews_count` INT UNSIGNED NULL,
  `latitude` DECIMAL(10,7) NULL,
  `longitude` DECIMAL(10,7) NULL,
  `raw_json` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_maps_results_query` (`search_query_id`),
  CONSTRAINT `fk_maps_results_query` FOREIGN KEY (`search_query_id`) REFERENCES `search_queries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
