---
name: Jwani login user-dropdown security
description: Why the login/forgot dropdowns use user IDs (not emails) and return generic reset responses
---

# Login user-dropdown security (Jwani accounting app)

The app (PHP Laravel 11, public at zhwanigashtyari.com) replaces the login email
text field with a dropdown of users, per the owner's request ("select a user, no typing").

**Rule:** the login AND forgot-password dropdowns must use the user `id` as the
`<option value>` and the user's **name** as the visible label. Never put email
addresses in these public pages (not as values, not as labels). The controller
resolves `user_id` → email server-side before `Auth::attempt` / `Password::sendResetLink`.

**Rule:** forgot-password must always return the same generic "if the account
exists, a link was sent" message regardless of whether the account exists.

**Why:** rendering all user emails on the unauthenticated login page leaks internal
identities and enables account enumeration / targeted attacks on a public domain.
A code review flagged this as a blocking issue. IDs + name labels keep the exact
requested UX without disclosing emails.

**How to apply:** if asked to "show emails in the dropdown" or "add a user", keep
values as IDs and labels as names. Login/forgot POST routes are also rate-limited
(`throttle:10,1` login, `throttle:6,1` forgot/reset).
