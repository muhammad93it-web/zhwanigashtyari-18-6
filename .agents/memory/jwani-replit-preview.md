---
name: Previewing the Jwani PHP/Laravel app inside Replit
description: How to make the PHP app viewable/interactive in the Replit preview pane, and the non-obvious quirks that block the obvious approaches.
---

# Previewing the PHP/Laravel app in the Replit pane

The Replit monorepo preview is built for Node artifacts. `createArtifact()` has no
PHP type, and a hand-written `.replit-artifact/artifact.toml` registers the app in
`listArtifacts()` but is **NOT** wired into the running reverse proxy — only
`createArtifact` updates the proxy route table. So a custom path at `localhost:80`
keeps 404-ing to the mockup server. Confirmed empirically.

**What actually works (interactive in-pane preview):** bridge through the
mockup-sandbox vite dev server, which is the catch-all on port 80. Add a
`server.proxy` entry in `artifacts/mockup-sandbox/vite.config.ts` forwarding the
app's path prefix to the Laravel port, then restart the mockup workflow. Chain:
browser → infra proxy → mockup vite → Laravel. Serve Laravel under that same path
prefix; Blade views use Laravel's `route()` helper everywhere (no hard-coded
paths), so prefixing works once URLs generate with the prefix.

**Critical quirk:** `php artisan serve` does NOT forward arbitrary custom env vars
to its `php -S` worker (it whitelists), so a `getenv()` for a bespoke var reads
empty in the served request even though it shows on the parent process. Fix: run
the built-in server directly (`php -S host:port server.php`) with the custom vars
prepended on the command line. A root `server.php` (standard Laravel dev router)
is harmless on cPanel — Apache serves via `public/index.php` and never invokes it.

**Dual-target safety (this app ALSO ships as a ZIP to a live cPanel host):** any
preview-only code must no-op in production. Two lessons learned from review:
- Gate on a bespoke preview var **AND** a Replit-environment signal (`REPL_ID`),
  not the bespoke var alone — defense against a stray env var on a shared host.
- If forcing the root URL (`URL::forceRootUrl`), **validate the host against an
  allowlist** (`*.replit.dev` / `*.repl.co`) before applying, or a bad env value
  could redirect generated links/forms to an external domain (phishing risk).

**Why:** the live site must be 100% unaffected; env-name-only gating is "likely
safe" but not airtight, and forceRootUrl without host validation is a real
security hole.

**Local DB for preview:** no MySQL here; `config/database.php` has an added sqlite
connection and a dev `.env` (excluded from ZIP) points at a migrated+seeded
sqlite file. Login uses the seeder's default admin user/password.

**ZIP hygiene:** if rebuilding the ZIP, ensure `database/*.sqlite`,
`.replit-artifact/`, and the dev `.env` do not ship.

# Static-file drop-in to screenshot a view 404s (server.php docroot mismatch)

Don't prerender a Blade view to a `.html` in `public/` (or `public/jwani/`) to screenshot it through the proxy — it 404s. `server.php` does `if (file_exists(__DIR__.'/public'.$uri)) return false;`, but `php -S` runs with **no `-t`**, so its docroot is the PROJECT ROOT, not `public/`. On `return false` the built-in server serves `<project-root>/$uri` (nonexistent) → 404. Both `/x.html` and `/jwani/x.html` 404 even with the file in `public/`. To screenshot a view, add a temporary Laravel route, or just render offline and inspect/grep the HTML.
