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

**Rule for adding a FOREIGN KEY to an existing table:**
- The SQL setup/upgrade files add the FK unconditionally (MySQL). The Laravel
  migration must guard the FK with `DB::getDriverName() === 'mysql'` because SQLite
  cannot `ALTER TABLE ... ADD FOREIGN KEY` (and doesn't enforce FKs by default) — an
  unguarded `$table->foreign(...)` in a `Schema::table()` ALTER crashes dev SQLite.
  FKs declared inline at CREATE time (`->constrained()`) are fine on both; only the
  ALTER-ADD-FK case needs the guard. Otherwise the SQL paths gain a constraint the
  migrate path silently lacks → lockstep drift.

**Why:** missing any of the three lets one install path leave the DB in a state where
another path errors out on the live site. Architect review caught exactly this gap
(both the column-add and the FK-add).

**How to apply:** every time you add a migration that alters an existing table, touch
all three files in lockstep and verify idempotency by running the migration's `up()`
twice against a fresh SQLite DB.
