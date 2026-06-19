---
name: Jwani Telegram dispatch due-check
description: Late-cron-tolerant scheduling rule for telegram:dispatch — compare against the latest PAST occurrence, not the current period's
---

# Telegram auto-delivery due-check must use the latest *past* occurrence

When deciding if a schedule should fire, compute the most recent occurrence at
or before "now" and fire when `last_sent_at < thatOccurrence`. Do NOT compute
only the current calendar period's occurrence.

**Why:** cron on shared cPanel can run late or be down across a boundary. If you
only look at the current period's occurrence, a missed send is silently skipped:
- Daily: cron resumes next morning before today's send_time → yesterday's send is lost.
- Monthly day-31: clamps to Feb 28; if cron resumes Mar 1, the current-period
  occurrence (Mar 31) is in the future, so February is never sent.

**How to apply:** for daily, latest = today@send_time, or yesterday@send_time if
today's hasn't arrived. For monthly, latest = this month's clamped day_of_month,
or previous month's clamped day if this month's hasn't arrived. Clamp
day_of_month to that month's daysInMonth (use day(1) before subMonthNoOverflow so
daysInMonth is read from the correct month). Manual "send now" must bypass all of
this and never advance last_sent_at. Guard every sendDocument path (backup AND
transactions) against the Telegram ~50MB limit, not just backups.
