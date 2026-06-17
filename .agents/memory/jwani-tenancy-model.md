---
name: Jwani tenancy model
description: Which Jwani records are per-user vs shared company-wide, and why
---

# Jwani tenancy model

Most records in this accounting app are **shared company-wide**, NOT scoped per user. Any authed user with the relevant permission (e.g. `perm:suppliers`) can view/edit/print/export any record.

**Why:** purchase invoices were originally scoped with `where('user_id', Auth::id())` + `abort_unless(... 403)` on show/edit/update/destroy/print/export. This caused 403s when a different staff member opened an invoice another user created — wrong model for a single-company ledger. Removed per-user scoping so the books are shared.

**How to apply:** when adding controllers/queries for company financial data (suppliers, invoices, payments, statements), do NOT filter by `user_id` and do NOT add ownership `abort_unless`. Gate access by route middleware permission instead. Keep `user_id` only as an audit/creator stamp, never as a visibility filter.
