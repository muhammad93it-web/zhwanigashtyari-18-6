---
name: Jwani reset / zero-out feature
description: How section/master reset works, where it lives in the UI, and the recompute assumption it relies on.
---

# Reset / zero-out (ResetService + Settings)

Reset is **centralized in admin-only Settings**, NOT as per-module index buttons.
**Why:** a destructive zero-out next to every module's normal actions invites accidental clicks; one PIN-gated admin surface is safer. (This deviates from the original plan that wanted module-level buttons — disclosed to and accepted by the user.)
**How to apply:** add new resettable sections to `ResetService::SECTIONS` + a button in `settings/index.blade.php`, never to module index pages.

Reset **keeps masters** (suppliers/materials/drivers/workers/contractors/clients/projects) and only deletes transactional rows (invoices/payments/income/expense/debts/movements/transactions), child→parent, inside `DB::transaction`, then **recomputes**: material stock from `purchase_invoice_details` + `material_movements`, supplier/driver balances from the **last `balance_after` per currency**.
**Why:** balances are not stored as the source of truth; the ledger is. Recompute keeps stock/balance coherent after deletion instead of blindly zeroing.
**Assumption:** supplier/driver ledgers are append-only snapshots (last row = current balance). If historical ledger rows ever become editable/reorderable, switch recompute to replay-by-date/id.

# PIN gate
PIN stored **hashed** (`Hash::make`, verify-only via `Hash::check`) under AppSetting key `reset_pin` — plain `set()`, not `setEncrypted`. Routes are POST + CSRF + admin middleware. It is an accidental-destruction gate, not protection against a malicious admin (admins can reset the PIN).
