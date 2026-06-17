---
name: Laravel offline testing in this env (no MySQL, no persistent serve)
description: How to verify Laravel routes/controllers end-to-end without MySQL or a running server, and the SQLite↔MySQL divergence trap.
---

# Testing Laravel here without MySQL or `artisan serve`

There is NO local MySQL, and a backgrounded `php artisan serve` gets SIGKILLed. To verify routes/controllers, boot the kernel from a one-off PHP script (write it to /tmp) and dispatch requests in-process against a temporary SQLite DB (`DB_CONNECTION=sqlite`, a real file, `migrate:fresh --seed`).

**GET route tests:** `$app->make(Http\Kernel::class)` then `$kernel->bootstrap()` BEFORE any model query (otherwise `connection() on null`). For auth-protected routes, bind a request first then set the user: `$app->instance('request', Request::create('/','GET')); $app['auth']->guard()->setUser($user);` — calling `setUser`/`Auth::login` with no bound request throws `SessionGuard::setRequest(): Argument #1 must be ... null given`.

**Write-flow tests (POST):** going through the kernel hits CSRF. Easiest is to bypass HTTP middleware: build a `Request::create('/','POST',$input)`, `$app->instance('request',$req)`, then call the controller method directly via `$app->make(Controller::class)->store($req)`. Validation (`$request->validate()`) and `ValidationException` still fire normally. Watch method signatures — some actions take a route-model only (e.g. `markPaid(Debt $debt)`), not a Request.

# SQLite hides MySQL strict-mode errors

**Why:** SQLite ignores `VARCHAR(n)` length limits; MySQL strict mode rejects overflow with `SQLSTATE[22001] Data too long`. A passing SQLite run does NOT prove MySQL safety.

**How to apply:** keep every validator's `max:` in lockstep with its column length. Audit `string()`/`VARCHAR(255)` columns against controller `max:` rules before shipping a MySQL-targeted app. (Real bug found: `description` validated `max:500` against `VARCHAR(255)` columns.)

# SQLite hides MySQL aggregate + ORDER BY error 1140

**Why:** cloning an Eloquent builder that already has `->latest(col)`/`->orderBy(col)` and then adding an aggregate-only `selectRaw('SUM(...), COUNT(*)')->first()` leaves the `ORDER BY col` in the SQL with no `GROUP BY`. MySQL rejects this with `SQLSTATE[42000] 1140 Mixing of GROUP columns ... is illegal`. SQLite silently allows it, so offline tests pass while the live MySQL page 500s.

**How to apply:** when building a totals/summary query from a cloned list query, strip the order first: `(clone $query)->reorder()->selectRaw('SUM(...)')->first()`. Audit every `(clone $query)->selectRaw('SUM` after a `latest()`/`orderBy()`. (Real bug: ContractorPayment/Expense/Income index totals.)

# Smoke-testing Jwani login via curl (live dev server)

The dev preview is `DB_CONNECTION=sqlite` (`database/database.sqlite`) + `SESSION_DRIVER=file`, fully separate from the live cPanel MySQL site — so resetting the admin (`User::find(1)`, name بەڕێوەبەر) password to a known value via tinker just for a curl login test is safe and never touches production.

Two traps that silently break the curl login flow (both cost real time):
- Never name the shell var `UID` for the dropdown user id — `UID` is bash-readonly, the assignment fails, `user_id` posts empty, login 302s back to `/login` with no error. Use `USERID`.
- `rg -c "<phrase>"` over rendered HTML also matches the phrase inside HTML comments. The back-button comment `<!-- ... گەڕانەوە بۆ بەشەکان ... -->` is always present, so assert on the real anchor (`title="..."`/`href`), not the bare phrase.

Login form posts `user_id` (dropdown value = raw id) + `password` + `_token`; success 302s to the proxy `/jwani`, failure 302s to `127.0.0.1:5000/login`.
