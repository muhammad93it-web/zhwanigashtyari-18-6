---
name: Jwani preview routing (/jwani 502)
description: Why localhost:80/jwani can 502 even when Laravel is healthy, and how to fix it
---

The Jwani Laravel app runs on port 5000 (workflow `accounting-system: Jwani App`, served via `php -S` + `server.php` with `PREVIEW_PREFIX=/jwani`). Its `artifact.toml` declares `paths=["/jwani"]`, but in this workspace the `/jwani` preview is actually proxied by the **mockup-sandbox Component Preview Server** (its `vite.config.ts` proxies `/jwani` → `127.0.0.1:5000`), NOT the global proxy alone.

**Symptom:** `curl 127.0.0.1:5000/jwani/login` = 200 (app healthy) but `curl localhost:80/jwani/login` = 502.
**Fix:** restart the workflow `artifacts/mockup-sandbox: Component Preview Server`. After it boots, `localhost:80/jwani/*` serves 200.

**Why it matters:** a 502 on the preview does NOT mean the Laravel app or your migrations are broken — check port 5000 directly first; if that's 200, the preview proxy server just needs starting.
