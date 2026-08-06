---
id: task-010
title: "Deploy script and cutover runbook"
status: todo
priority: medium
labels: [deploy, infra]
created: 2026-08-07
pr: ""
---

## Description

SSH works and is verified. Connection settings are in `.env` (gitignored; copy
`.env.example` if missing). Publickey auth only — no password.

**Server facts, confirmed 2026-08-07:**

- PHP **8.2.33**, WordPress **7.0.2**, `wp-cli` at `/usr/local/bin/wp`
- Themes: `/home/u2072-mbcxascz03lu/www/eve-n.nl/public_html/wp-content/themes`
- Active theme: **astra**
- Plugins: `elementor`, `sg-ai-studio`, `envato-elements`, `gtranslate`,
  `loco-translate`, `sg-security`, `sg-cachepress`
- **There is no staging site** — only `eve-n.nl` exists

Local `wp-env` runs PHP 8.2 to match production. Keep it that way.

Write `bin/deploy.sh` that rsyncs **only** `wp-content/themes/eve-n/` to the
server. It must never touch `wp-content/uploads`, plugins, or WordPress core.

Because there is no staging site, the theme deploys **inactive** — uploading it
changes nothing visible, and the switch happens only at cutover. That is the
safety mechanism; do not activate from the script by default.

**Cutover risks to work through in the runbook:**

- **Elementor is active**, so the current pages are probably Elementor
  documents. Switching themes may orphan that content. Establish what breaks
  *before* switching, not after.
- `sg-cachepress` caches aggressively — the cutover must purge it or the change
  will not appear.
- `gtranslate` may inject markup into the new theme's pages; check it.
- Rollback is `wp theme activate astra` plus a cache purge. Test it.

Take a **full backup** (files + database) before the first activation.

Confirm with the operator before cutover: does anything on the current live
site need to survive — the GDQ copy, the three client logos, the existing
portrait? None of it appears in the XD design.

## Acceptance Criteria

- [ ] `bin/deploy.sh` pushes the theme folder to staging and nothing else
- [ ] Credentials read from `.env`; nothing secret is committed
- [ ] Dry-run mode showing what would transfer
- [ ] Production requires an explicit flag plus confirmation
- [ ] Rollback documented and tested on staging
- [ ] Staging URL verified: all seven pages render, blog publishes, contact
      form sends
