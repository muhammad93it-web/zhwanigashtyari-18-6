-- =====================================================================
--  سیستەمی ژمێریاری ژوانی گەشتیاری — Database Setup
--  Jwani Accounting System — full schema + initial data
--
--  HOW TO USE (cPanel):
--   1. Open cPanel  ->  phpMyAdmin
--   2. On the left, click your database (e.g. zhwadqwq_db)
--   3. Click the "Import" tab at the top
--   4. Choose this file (jwani_database_setup.sql) and click "Go"
--   5. Done. Log in with: admin@jwani.com / password
--
--  Charset: utf8mb4 (full Kurdish/Arabic support)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+00:00';

-- ---------------------------------------------------------------------
-- migrations (so Laravel knows the schema is already applied)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `permissions` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- password_reset_tokens
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- sessions
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- exchange_rates
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `exchange_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usd_to_iqd` decimal(12,4) NOT NULL COMMENT '1 USD = X IQD',
  `notes` varchar(255) DEFAULT NULL,
  `set_by` varchar(255) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exchange_rates_effective_from_index` (`effective_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- clients
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_name_index` (`name`),
  KEY `clients_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- transactions
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` enum('sale','purchase','debit','credit') NOT NULL COMMENT 'sale=فرۆشتن, purchase=کڕین, debit=قەرز/بردراو, credit=دانەوەی قەرز/هێنراو',
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'USD',
  `amount` decimal(15,2) NOT NULL COMMENT 'Original entered amount',
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL COMMENT 'Rate locked at transaction creation. Historical accuracy preserved.',
  `description` varchar(255) NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_reference_number_unique` (`reference_number`),
  KEY `transactions_client_id_transaction_date_index` (`client_id`,`transaction_date`),
  KEY `transactions_type_transaction_date_index` (`type`,`transaction_date`),
  KEY `transactions_transaction_date_index` (`transaction_date`),
  KEY `transactions_reference_number_index` (`reference_number`),
  KEY `transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `transactions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `project_id` bigint unsigned DEFAULT NULL,
  `payee` varchar(255) NOT NULL,
  `expense_type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `currency` enum('USD','IQD') NOT NULL DEFAULT 'IQD',
  `amount` decimal(15,2) NOT NULL,
  `amount_usd` decimal(15,4) NOT NULL,
  `amount_iqd` decimal(15,2) NOT NULL,
  `exchange_rate_usd_to_iqd` decimal(12,4) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reason_description` text,
  `reference_number` varchar(50) NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_reference_number_unique` (`reference_number`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_user_id_foreign` (`user_id`),
  KEY `expenses_project_id_foreign` (`project_id`),
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
-- projects (پڕۆژە/بینا)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_name_index` (`name`),
  KEY `projects_is_active_index` (`is_active`),
  KEY `projects_client_id_foreign` (`client_id`),
  CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- expenses -> projects FK (added here, after `projects` exists, to avoid creation-order issues)
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

-- ---------------------------------------------------------------------
-- suppliers (دابینکەرەکان)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_name_index` (`name`),
  KEY `suppliers_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- supplier_transactions (کشف حساب / لێژەری دابینکەر)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `supplier_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` enum('purchase','payment') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_transactions_date_index` (`date`),
  KEY `supplier_transactions_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `supplier_transactions_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `supplier_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- purchase_invoices (وەسڵی کڕین — سەرەکی)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_invoices_date_index` (`date`),
  KEY `purchase_invoices_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_invoices_user_id_foreign` (`user_id`),
  CONSTRAINT `purchase_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- purchase_invoice_details (هێڵەکانی وەسڵی کڕین)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase_invoice_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_invoice_id` bigint unsigned NOT NULL,
  `material_id` bigint unsigned DEFAULT NULL,
  `custom_type` varchar(255) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT '0.000',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `project_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_invoice_details_purchase_invoice_id_foreign` (`purchase_invoice_id`),
  KEY `purchase_invoice_details_material_id_foreign` (`material_id`),
  KEY `purchase_invoice_details_project_id_foreign` (`project_id`),
  CONSTRAINT `purchase_invoice_details_purchase_invoice_id_foreign` FOREIGN KEY (`purchase_invoice_id`) REFERENCES `purchase_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_invoice_details_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_invoice_details_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  INITIAL DATA
-- =====================================================================

-- Mark migrations as applied
INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2024_01_01_000001_create_users_table', 1),
  ('2024_01_01_000002_create_exchange_rates_table', 1),
  ('2024_01_01_000003_create_clients_table', 1),
  ('2024_01_01_000004_create_transactions_table', 1),
  ('2024_01_01_000005_create_incomes_table', 1),
  ('2024_01_01_000006_create_expenses_table', 1),
  ('2024_01_01_000007_create_debts_table', 1),
  ('2024_01_01_000008_create_materials_table', 1),
  ('2024_01_01_000009_create_material_movements_table', 1),
  ('2024_01_01_000010_create_contractors_table', 1),
  ('2024_01_01_000011_create_contractor_payments_table', 1),
  ('2024_01_01_000012_create_documents_table', 1),
  ('2024_01_01_000013_add_role_to_users_table', 1),
  ('2026_06_17_000001_create_projects_table', 1),
  ('2026_06_17_000002_create_suppliers_table', 1),
  ('2026_06_17_000003_create_supplier_transactions_table', 1),
  ('2026_06_17_000004_create_purchase_invoices_table', 1),
  ('2026_06_17_000005_create_purchase_invoice_details_table', 1),
  ('2026_06_17_000006_add_project_fields_to_expenses_table', 1);

-- Admin user  (login: admin@jwani.com  /  password)
INSERT INTO `users` (`name`, `email`, `password`, `is_admin`, `created_at`, `updated_at`) VALUES
  ('بەڕێوەبەر', 'admin@jwani.com', '$2y$10$oMqwFOWnc4ebLn8p5hBLkOFNHeOyBszF4kwmjEoqbpqEAUcKNupXK', 1, NOW(), NOW());

-- Initial exchange rate
INSERT INTO `exchange_rates` (`usd_to_iqd`, `notes`, `set_by`, `effective_from`, `created_at`, `updated_at`) VALUES
  (1310.0000, 'ڕێژەی سەرەتایی', 'بەڕێوەبەر', NOW(), NOW(), NOW());

-- Sample clients
INSERT INTO `clients` (`name`, `phone`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
  ('ئەحمەد حسێن', '07501234567', 'هەولێر', 1, NOW(), NOW()),
  ('سارا محمد', '07709876543', 'سلێمانی', 1, NOW(), NOW()),
  ('کارزان عبدوللا', '07701112233', 'دهۆک', 1, NOW(), NOW()),
  ('شرکەتی نمونە', '07701234567', 'هەولێر، گەڕەکی کەرکووک', 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
--  END — Log in at your site with  admin@jwani.com  /  password
--  IMPORTANT: change this password immediately after first login.
-- =====================================================================
