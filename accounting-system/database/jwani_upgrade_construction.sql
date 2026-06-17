-- =====================================================================
--  سیستەمی ژمێریاری ژوانی گەشتیاری — UPGRADE (تێچووی پڕۆژەی بیناسازی)
--  Jwani Accounting — UPGRADE: Construction & Contractor Project Costing
--
--  ⚠️  ئەمە بۆ سایتە زیندووەکەتە کە پێشتر داتای تێدایە.
--      تەنها بەشە نوێیەکان زیاد دەکات:
--        - projects (پڕۆژە/بینا)
--        - suppliers (دابینکەر)
--        - supplier_transactions (کشف حساب)
--        - purchase_invoices + purchase_invoice_details (وەسڵی کڕینی فرە-هێڵ)
--        - سێ ستوونی نوێ بۆ expenses (project_id, expense_type, reason_description)
--      هیچ داتایەکی کۆن دەستکاری ناکات و ناسڕێتەوە. سەلامەتە چەند جارێک کاری پێبکرێت.
--
--  HOW TO USE (cPanel):
--   1. cPanel  ->  phpMyAdmin
--   2. داتابەیسە زیندووەکەت هەڵبژێرە  ->  تابی "Import"
--   3. ئەم فایلە هەڵبژێرە (jwani_upgrade_construction.sql)  ->  "Go"
--
--  Charset: utf8mb4 (full Kurdish/Arabic support)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+00:00';

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

-- ---------------------------------------------------------------------
-- expenses: add project_id (only if missing)
-- ---------------------------------------------------------------------
SET @has_exp_project := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'expenses' AND COLUMN_NAME = 'project_id'
);
SET @sql_exp_project := IF(@has_exp_project = 0,
  'ALTER TABLE `expenses` ADD COLUMN `project_id` BIGINT UNSIGNED NULL AFTER `user_id`',
  'SELECT 1');
PREPARE st_ep FROM @sql_exp_project; EXECUTE st_ep; DEALLOCATE PREPARE st_ep;

-- expenses: add foreign key for project_id (only if missing)
SET @has_exp_project_fk := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'expenses'
    AND CONSTRAINT_NAME = 'expenses_project_id_foreign'
);
SET @sql_exp_project_fk := IF(@has_exp_project_fk = 0,
  'ALTER TABLE `expenses` ADD CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE st_epfk FROM @sql_exp_project_fk; EXECUTE st_epfk; DEALLOCATE PREPARE st_epfk;

-- expenses: add expense_type (only if missing)
SET @has_exp_type := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'expenses' AND COLUMN_NAME = 'expense_type'
);
SET @sql_exp_type := IF(@has_exp_type = 0,
  'ALTER TABLE `expenses` ADD COLUMN `expense_type` VARCHAR(255) NULL AFTER `payee`',
  'SELECT 1');
PREPARE st_et FROM @sql_exp_type; EXECUTE st_et; DEALLOCATE PREPARE st_et;

-- expenses: add reason_description (only if missing)
SET @has_exp_reason := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'expenses' AND COLUMN_NAME = 'reason_description'
);
SET @sql_exp_reason := IF(@has_exp_reason = 0,
  'ALTER TABLE `expenses` ADD COLUMN `reason_description` TEXT NULL AFTER `description`',
  'SELECT 1');
PREPARE st_er FROM @sql_exp_reason; EXECUTE st_er; DEALLOCATE PREPARE st_er;

-- =====================================================================
--  UPGRADE 2 — دراوی هەر هێڵێک (IQD/USD) + زانیاری گەیەنەر + باڵانسی فرە-دراو + کرێی کار
--  هەموو ئەمانە idempotent-ن (سەلامەتە چەند جارێک کاری پێبکرێت).
-- =====================================================================

-- purchase_invoices: supplier_id nullable (گەیەنەری کاتی بەبێ هەژمار)
ALTER TABLE `purchase_invoices` MODIFY `supplier_id` BIGINT UNSIGNED NULL;

-- purchase_invoices: deliverer_name
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='deliverer_name');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `deliverer_name` VARCHAR(255) NULL AFTER `supplier_id`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoices: deliverer_phone
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='deliverer_phone');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `deliverer_phone` VARCHAR(255) NULL AFTER `deliverer_name`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoices: deliverer_address
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='deliverer_address');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `deliverer_address` VARCHAR(255) NULL AFTER `deliverer_phone`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoices: vehicle_number
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='vehicle_number');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `vehicle_number` VARCHAR(255) NULL AFTER `deliverer_address`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoices: vehicle_type
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='vehicle_type');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `vehicle_type` VARCHAR(255) NULL AFTER `vehicle_number`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoices: project_id (+ FK)
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='project_id');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `project_id` BIGINT UNSIGNED NULL AFTER `user_id`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;
SET @c := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND CONSTRAINT_NAME='purchase_invoices_project_id_foreign');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD CONSTRAINT `purchase_invoices_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoices: per-currency totals
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoices' AND COLUMN_NAME='total_iqd');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoices` ADD COLUMN `total_iqd` DECIMAL(15,2) NOT NULL DEFAULT 0, ADD COLUMN `total_usd` DECIMAL(15,2) NOT NULL DEFAULT 0, ADD COLUMN `paid_iqd` DECIMAL(15,2) NOT NULL DEFAULT 0, ADD COLUMN `paid_usd` DECIMAL(15,2) NOT NULL DEFAULT 0, ADD COLUMN `remaining_iqd` DECIMAL(15,2) NOT NULL DEFAULT 0, ADD COLUMN `remaining_usd` DECIMAL(15,2) NOT NULL DEFAULT 0','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- purchase_invoice_details: currency
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_invoice_details' AND COLUMN_NAME='currency');
SET @s := IF(@c=0,'ALTER TABLE `purchase_invoice_details` ADD COLUMN `currency` ENUM(''IQD'',''USD'') NOT NULL DEFAULT ''IQD'' AFTER `line_total`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- suppliers: balance_iqd / balance_usd
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='suppliers' AND COLUMN_NAME='balance_iqd');
SET @s := IF(@c=0,'ALTER TABLE `suppliers` ADD COLUMN `balance_iqd` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `balance`, ADD COLUMN `balance_usd` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `balance_iqd`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- supplier_transactions: currency
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='supplier_transactions' AND COLUMN_NAME='currency');
SET @s := IF(@c=0,'ALTER TABLE `supplier_transactions` ADD COLUMN `currency` ENUM(''IQD'',''USD'') NOT NULL DEFAULT ''IQD'' AFTER `type`','SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- ---------------------------------------------------------------------
-- workers (کرێکار/وەستا)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `workers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `default_hourly_rate` decimal(15,2) DEFAULT NULL,
  `default_currency` enum('IQD','USD') NOT NULL DEFAULT 'IQD',
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workers_name_index` (`name`),
  KEY `workers_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- labor_payments (کرێی کار — پارەدانی کرێکار بەسەعات/جێگیر)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `labor_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `worker_id` bigint unsigned DEFAULT NULL,
  `worker_name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `is_hourly` tinyint(1) NOT NULL DEFAULT '1',
  `hours` decimal(10,2) DEFAULT NULL,
  `hourly_rate` decimal(15,2) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` enum('IQD','USD') NOT NULL DEFAULT 'IQD',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `labor_payments_date_index` (`date`),
  KEY `labor_payments_user_id_foreign` (`user_id`),
  KEY `labor_payments_project_id_foreign` (`project_id`),
  KEY `labor_payments_worker_id_foreign` (`worker_id`),
  CONSTRAINT `labor_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `labor_payments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `labor_payments_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Mark the new migrations as applied (INSERT IGNORE = safe to re-run)
-- so a later `php artisan migrate` does not try to re-create them.
-- ---------------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`)
SELECT v.migration, COALESCE((SELECT MAX(`batch`) FROM (SELECT * FROM `migrations`) AS mm), 1) + 1
FROM (
  SELECT '2026_06_17_000001_create_projects_table' AS migration
  UNION ALL SELECT '2026_06_17_000002_create_suppliers_table'
  UNION ALL SELECT '2026_06_17_000003_create_supplier_transactions_table'
  UNION ALL SELECT '2026_06_17_000004_create_purchase_invoices_table'
  UNION ALL SELECT '2026_06_17_000005_create_purchase_invoice_details_table'
  UNION ALL SELECT '2026_06_17_000006_add_project_fields_to_expenses_table'
  UNION ALL SELECT '2026_06_17_000007_extend_purchases_for_currency_and_deliverer'
  UNION ALL SELECT '2026_06_17_000008_create_workers_table'
  UNION ALL SELECT '2026_06_17_000009_create_labor_payments_table'
) AS v
WHERE NOT EXISTS (
  SELECT 1 FROM (SELECT * FROM `migrations`) AS m2 WHERE m2.`migration` = v.migration
);

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
--  END — بەشە نوێیەکان زیادکران. هیچ داتایەکی کۆن نەسڕایەوە.
-- =====================================================================
