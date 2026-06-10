-- =====================================================================
--  سیستەمی ژمێریاری ژوانی گەشتیاری — UPGRADE (بەشە نوێیەکان)
--  Jwani Accounting — UPGRADE script for an EXISTING/LIVE database
--
--  ⚠️  ئەمە بۆ سایتە زیندووەکەتە کە پێشتر داتای تێدایە.
--      تەنها ٨ خشتەی نوێ زیاد دەکات (دارایی، کۆگا، وەستا، نووسراو).
--      هیچ بەکارهێنەر/کڕیار/ڕێژەی دۆلار/مامەڵە دەستکاری ناکات و ناسڕێتەوە.
--
--  USE THIS (not jwani_database_setup.sql) when the database ALREADY has
--  data. It only ADDS the 8 new module tables. It does NOT touch users,
--  clients, exchange_rates, or transactions. Safe to run more than once.
--
--  HOW TO USE (cPanel):
--   1. cPanel  ->  phpMyAdmin
--   2. Left side: click your existing database
--   3. Top: "Import" tab
--   4. Choose this file (jwani_upgrade_modules.sql)  ->  "Go"
--
--  Charset: utf8mb4 (full Kurdish/Arabic support)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+00:00';

-- ---------------------------------------------------------------------
-- incomes (وەرگرتنی پارە)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `incomes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `source` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `amount` decimal(15,2) NOT NULL,
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_number` varchar(50) NOT NULL,
  `income_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incomes_reference_number_unique` (`reference_number`),
  KEY `incomes_income_date_index` (`income_date`),
  KEY `incomes_user_id_foreign` (`user_id`),
  CONSTRAINT `incomes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- expenses (خەرجکردنی پارە)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `payee` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `amount` decimal(15,2) NOT NULL,
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_number` varchar(50) NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_reference_number_unique` (`reference_number`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_user_id_foreign` (`user_id`),
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- debts (قەرزەکان)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `debts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `party_name` varchar(255) NOT NULL,
  `direction` enum('receivable','payable') NOT NULL COMMENT 'receivable=قەرزی لای خەڵک, payable=قەرزی ئێمە',
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `amount` decimal(15,2) NOT NULL,
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL,
  `status` enum('open','paid') NOT NULL DEFAULT 'open',
  `description` varchar(255) DEFAULT NULL,
  `reference_number` varchar(50) NOT NULL,
  `debt_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `debts_reference_number_unique` (`reference_number`),
  KEY `debts_direction_status_index` (`direction`,`status`),
  KEY `debts_debt_date_index` (`debt_date`),
  KEY `debts_user_id_foreign` (`user_id`),
  CONSTRAINT `debts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- materials (کۆگا)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'دانە',
  `category` varchar(255) DEFAULT NULL,
  `current_stock` decimal(15,3) NOT NULL DEFAULT '0.000',
  `min_stock` decimal(15,3) DEFAULT NULL,
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materials_name_index` (`name`),
  KEY `materials_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- material_movements (کڕین/فرۆشتنی مەواد)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `material_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `material_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `type` enum('purchase','sale') NOT NULL COMMENT 'purchase=کڕین, sale=فرۆشتن',
  `quantity` decimal(15,3) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `amount` decimal(15,2) NOT NULL,
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL,
  `party_name` varchar(255) DEFAULT NULL,
  `reference_number` varchar(50) NOT NULL,
  `movement_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_movements_reference_number_unique` (`reference_number`),
  KEY `material_movements_type_movement_date_index` (`type`,`movement_date`),
  KEY `material_movements_material_id_movement_date_index` (`material_id`,`movement_date`),
  KEY `material_movements_user_id_foreign` (`user_id`),
  KEY `material_movements_client_id_foreign` (`client_id`),
  CONSTRAINT `material_movements_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `material_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_movements_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- contractors (وەستا)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contractors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `work_type` enum('per_meter','contract') NOT NULL COMMENT 'per_meter=بە مەتر, contract=قۆنتەرات',
  `rate_per_meter` decimal(15,2) DEFAULT NULL,
  `contract_amount` decimal(15,2) DEFAULT NULL,
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contractors_name_index` (`name`),
  KEY `contractors_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- contractor_payments (پارەدانی وەستا)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contractor_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contractor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `amount` decimal(15,2) NOT NULL,
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL,
  `meters` decimal(15,3) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contractor_payments_reference_number_unique` (`reference_number`),
  KEY `contractor_payments_contractor_id_payment_date_index` (`contractor_id`,`payment_date`),
  KEY `contractor_payments_user_id_foreign` (`user_id`),
  CONSTRAINT `contractor_payments_contractor_id_foreign` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contractor_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- documents (نووسراوەکان)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `doc_type` varchar(255) DEFAULT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `recipient` varchar(255) DEFAULT NULL,
  `body` longtext,
  `doc_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_doc_date_index` (`doc_date`),
  KEY `documents_user_id_foreign` (`user_id`),
  CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Mark the new migrations as applied (INSERT IGNORE = safe to re-run).
-- Existing rows 000001–000004 are left untouched.
-- ---------------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
  ('2024_01_01_000005_create_incomes_table', 2),
  ('2024_01_01_000006_create_expenses_table', 2),
  ('2024_01_01_000007_create_debts_table', 2),
  ('2024_01_01_000008_create_materials_table', 2),
  ('2024_01_01_000009_create_material_movements_table', 2),
  ('2024_01_01_000010_create_contractors_table', 2),
  ('2024_01_01_000011_create_contractor_payments_table', 2),
  ('2024_01_01_000012_create_documents_table', 2);

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
--  END — بەشە نوێیەکان زیادکران. هیچ داتایەکی کۆن نەسڕایەوە.
-- =====================================================================
