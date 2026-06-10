-- =====================================================================
--  سیستەمی ژمێریاری ژوانی گەشتیاری — UPGRADE (بەڕێوەبردنی بەکارهێنەران)
--  Jwani Accounting — UPGRADE: user roles & permissions
--
--  ⚠️  ئەمە بۆ سایتە زیندووەکەتە. دوو ستوون زیاد دەکات بۆ خشتەی users:
--      is_admin (بەڕێوەبەرە یان نا) و permissions (دەسەڵاتەکان).
--      بەکارهێنەرە کۆنەکان هەموو دەسەڵاتیان دەمێنێتەوە (دەبنە بەڕێوەبەر).
--      سەلامەتە چەند جارێک کاری پێبکرێت.
--
--  HOW TO USE (cPanel):
--   1. cPanel  ->  phpMyAdmin
--   2. داتابەیسە زیندووەکەت هەڵبژێرە  ->  تابی "Import"
--   3. ئەم فایلە هەڵبژێرە (jwani_upgrade_users.sql)  ->  "Go"
-- =====================================================================

-- Add `is_admin` if it does not already exist
SET @has_is_admin := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_admin'
);
SET @sql_is_admin := IF(@has_is_admin = 0,
  'ALTER TABLE `users` ADD COLUMN `is_admin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password`',
  'SELECT 1');
PREPARE st1 FROM @sql_is_admin; EXECUTE st1; DEALLOCATE PREPARE st1;

-- Add `permissions` if it does not already exist
SET @has_perms := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'permissions'
);
SET @sql_perms := IF(@has_perms = 0,
  'ALTER TABLE `users` ADD COLUMN `permissions` TEXT NULL AFTER `is_admin`',
  'SELECT 1');
PREPARE st2 FROM @sql_perms; EXECUTE st2; DEALLOCATE PREPARE st2;

-- Existing accounts keep full access (become managers) so nothing breaks.
UPDATE `users` SET `is_admin` = 1 WHERE `is_admin` = 0 AND `permissions` IS NULL;

-- Record this migration as run so a later `php artisan migrate` does not
-- attempt to re-add these columns. Safe to run twice (inserts only if absent).
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2024_01_01_000013_add_role_to_users_table', COALESCE((SELECT MAX(`batch`) FROM (SELECT * FROM `migrations`) AS m), 1)
WHERE NOT EXISTS (
  SELECT 1 FROM (SELECT * FROM `migrations`) AS m2
  WHERE m2.`migration` = '2024_01_01_000013_add_role_to_users_table'
);

-- =====================================================================
--  END
-- =====================================================================
