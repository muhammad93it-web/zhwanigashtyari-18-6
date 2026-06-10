---
name: Blade inline @php pitfall
description: Why inline @php(EXPR) at file top breaks on some Laravel 11 Blade versions, and the safe form
---

Never use the inline `@php(EXPR)` directive — especially at the very top of a file, and especially when another `@php ... @endphp` block exists later in the same file.

**Why:** On some Laravel 11 Blade versions, inline `@php($x = ...)` compiles to `<?php($x = ...` — `<?php` with NO trailing space before `(`. PHP's tokenizer requires whitespace after `<?php`, so this is malformed. Worse, when a later `@php ... @endphp` block exists, the unterminated top `@php` collides with that block's `@endphp`, corrupting variable scope. Observed symptom: a variable defined inside the *later* block (`$kNow`) reported as "Undefined variable" at the line that uses it, even though it is assigned right above. A file with no second `@php` block (e.g. login page) appeared to work, masking the bug — so test every page, not just one.

**How to apply:** Always use the block form with an explicit `@endphp`: `@php $x = "..."; @endphp` (compiles to `<?php $x = "..."; ?>`). Verify offline by `Blade::compileString($src)` and confirm the top compiles to a properly-closed `<?php ... ?>`. To catch this across all views: compile every `*.blade.php` with `Blade::compileString`, write each result to a temp file, and run `php -l` on it. Do NOT eval the compiled output with `<?php` tags stripped — raw HTML throws a false-positive "unexpected token <".
