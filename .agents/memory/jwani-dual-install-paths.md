---
name: Jwani dual install/upgrade paths
description: How schema changes must be authored so fresh SQL import, SQL upgrade script, and artisan migrate never collide on the live cPanel site.
---

This app ships to Namecheap cPanel shared hosting as a ZIP. There are THREE ways the
DB schema can change, and a new schema change must stay safe across ALL of them:

1. **Fresh install** — user imports `database/jwani_database_setup.sql` via phpMyAdmin.
   It creates every table AND seeds the `migrations` table with each migration name.
2. **SQL upgrade** — user imports a hand-written `database/jwani_upgrade_*.sql` against
   the live DB. Must be idempotent: guard `ALTER TABLE ... ADD COLUMN` with
   information_schema + PREPARE/EXECUTE so re-running is a no-op.
3. **artisan migrate** — rarely used on shared hosting, but possible.

**Rule for any column-adding migration:**
- Make the Laravel migration idempotent: wrap adds in `if (! Schema::hasColumn(...))`
  and drops in `Schema::hasColumn(...)` filters. Otherwise a later `artisan migrate`
  after an SQL upgrade crashes with duplicate-column.
- Add the migration name to the `INSERT INTO migrations` list in `jwani_database_setup.sql`
  (fresh installs must mark it as already run).
- In the matching `jwani_upgrade_*.sql`, also insert the migration row using a
  `INSERT ... SELECT ... WHERE NOT EXISTS` guard (migrations table has no unique key,
  so plain INSERT IGNORE won't dedupe).

**Why:** missing any of the three lets one install path leave the DB in a state where
another path errors out on the live site. Architect review caught exactly this gap.

**How to apply:** every time you add a migration that alters an existing table, touch
all three files in lockstep and verify idempotency by running the migration's `up()`
twice against a fresh SQLite DB.
