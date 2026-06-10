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
