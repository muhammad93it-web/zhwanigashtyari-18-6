---
name: Jwani cPanel asset() / image paths
description: Why Laravel asset() image links break on this shared-hosting site and the robust fix.
---

On the live Namecheap cPanel site, Laravel `asset('images/x.png')` links rendered as
broken images (logo showed a broken-image icon). Root cause is the shared-hosting
path/APP_URL setup: the generated absolute URL did not resolve to the served file.

**Fix that works:** inline small images as a base64 data URI directly in the Blade
view. Define once per file with `@php($logoSrc = "data:image/png;base64,...")` at the
top, then use `{{ $logoSrc }}` for both the `<img>` and the favicon `<link>`. This is
independent of APP_URL, docroot location, and whether `public/` was mapped correctly.

**Why:** we cannot inspect or reliably configure the customer's cPanel docroot/APP_URL,
and the app ships as a ZIP. A self-contained data URI removes all path dependencies.

**How to apply:** keep inlined images small (resize to ~128px, ~20KB → ~30KB base64)
since the data URI lives in the HTML of every page that uses the layout. For larger
images prefer fixing APP_URL, but for logos/icons inlining is the safe default here.

## Compute base64 at runtime, never paste a giant literal
A hardcoded `data:image/png;base64,...` literal in a Blade view (layout header) was
silently TRUNCATED/corrupted, so the `<img>` rendered broken while sibling text showed
fine. The login/dashboard worked because they compute it at runtime:
`is_file(public_path('images/logo.png')) ? 'data:image/png;base64,'.base64_encode(file_get_contents(...)) : ''`.
**Rule:** always inline images via runtime `base64_encode(file_get_contents(public_path(...)))`,
never by pasting a multi-KB base64 string into source — long literals get mangled.
**How to verify:** decode the rendered img src; a valid full logo here is ~81.6k base64 chars.
