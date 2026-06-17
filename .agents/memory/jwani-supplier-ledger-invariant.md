---
name: Jwani supplier ledger invariant
description: How supplier balance + supplier_transactions must stay coherent across purchase, payment, and invoice deletion
---

# Supplier ledger invariant

Supplier `balance` = what we owe the supplier (positive = debt). The ledger is `supplier_transactions` rows, each with `type` (`purchase` raises debt, `payment` lowers it), `amount`, and `balance_after`.

**Rule:** every transaction row's `balance_after` must equal the prior balance adjusted by *that row's own* signed type/amount. Never collapse a multi-effect operation into one row whose `balance_after` doesn't match its `amount`.

**Why:** the supplier كشف حساب (statement) UI renders +/- signs purely from `type`, and shows `balance_after` per row. A row where the math doesn't line up makes the printed statement untrustworthy and unauditable.

**How to apply:** when reversing a purchase invoice that had a partial payment, post TWO reversal rows — a `payment` of the full `total_amount` (undo the purchase) and, if `paid_amount > 0`, a `purchase` of `paid_amount` (undo the payment) — not a single `payment` row of `total_amount` with a netted balance. Lock the invoice (`lockForUpdate` + re-find) inside the DB transaction and bail if already deleted, so concurrent deletes can't double-apply stock/balance reversal.
