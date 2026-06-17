---
name: Jwani tenancy / data-sharing model
description: Which modules scope by user_id vs share company-wide; settles "IDOR" questions about new modules
---

# Jwani data tenancy model

This is a SINGLE-company accounting app where multiple staff users log into the same
company books. Most business entities are **shared company-wide** and only gated by
per-module permissions (`User::MODULES` / `hasAccess()`), NOT by `user_id` ownership.

- **Shared (no user_id scoping in index, no ownership abort on edit/update/destroy):**
  projects, suppliers, materials, contractors, contractor-payments, and the Labor
  module (workers, labor-payments). They stamp `user_id` on create only as a
  created-by audit field.
- **Per-user scoped (the outlier):** purchase-invoices enforce
  `where('user_id', Auth::id())` in index and `abort_unless(...->user_id === Auth::id())`
  on show/edit/update/destroy/print/export.

**Why:** the labor module deliberately mirrors contractor-payments (its closest
analog: payments tied to projects/people, per-currency). Staff need to see all
company labor records, so shared visibility is correct. An architect review flagged
the labor module as "IDOR" by comparing it only to purchase-invoices — that is a
false positive given the dominant shared pattern across the app.

**How to apply:** when adding a new company-resource module, follow the
contractor/contractor-payment pattern (shared, permission-gated, user_id stamped on
create) unless the user explicitly asks for per-user private records. Don't "fix"
the labor module to per-user scoping — it would diverge from contractors and break
multi-staff visibility.
