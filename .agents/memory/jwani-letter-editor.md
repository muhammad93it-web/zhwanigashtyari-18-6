---
name: Jwani letter print page = inline WYSIWYG editor
description: The letter print view is an in-browser editor; what persists vs what doesn't, and why.
---

# Letter print page is a Word-like editor (resources/views/letters/print.blade.php)

The print page is NOT a static template — it is an inline editor the user opens right before printing.

- All visible text is `contenteditable`; edits made there are **EPHEMERAL** (print-only), NOT saved to the DB. By design — the request was "edit like Word before printing". To persist letter content, use the normal create/edit form.
- Theme is driven by CSS custom properties on `.sheet`: `--ink` (text), `--accent` (lines/titles/header rule), `--paper` (background), `--footer` (footer bar). Four color pickers + a logo-replace input write these to `localStorage` key `jwani_letter_style_v1`, so palette/logo persist **PER-BROWSER** (not per-letter, not in DB). A reset button clears the key.

**Why per-browser:** avoids a schema change while still letting the user keep their preferred look across letters/reprints on their own machine. If they ever ask for per-letter styling, that needs a new column (e.g. JSON `style`) in lockstep across all three schema files.

Default palette matches the official letterhead image: navy `--accent #1f3a63`, footer `--footer #15294e`, centered recipient/subject, signature block on the LEFT (`justify-content: flex-end` under RTL), navy footer bar with phone icon. Print color fidelity relies on `print-color-adjust: exact` on body/sheet/footer.
