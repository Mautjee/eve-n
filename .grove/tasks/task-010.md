---
id: task-010
title: "Deploy script and staging cutover"
status: review
priority: high
labels: [deploy, infra]
created: 2026-08-07
pr: "https://github.com/Mautjee/eve-n/pull/1"
---

## Description

Build `bin/deploy.sh` and prove the deploy path end-to-end against staging.

SSH is verified and working. Connection settings are in `.env` (gitignored;
copy `.env.example` if missing). Publickey auth only — no password.

**Server facts, confirmed 2026-08-07:**

- PHP **8.2.33**, WordPress **7.0.2**, `wp-cli` at `/usr/local/bin/wp`
- Production: `~/www/eve-n.nl/public_html` -> `http://eve-n.nl`
- **Staging: `~/www/staging2.eve-n.nl/public_html` -> `https://staging2.eve-n.nl`**
- Active theme on both: `astra`
- Plugins: `elementor`, `sg-ai-studio`, `envato-elements`, `gtranslate`,
  `loco-translate`, `sg-security`, `sg-cachepress`

Local `wp-env` runs PHP 8.2 to match production. Keep it that way.

**The existing site is disposable.** The operator has confirmed the current
Astra/Elementor design is being fully replaced and nothing on it needs to be
preserved. Do not spend effort protecting the old theme, migrating Elementor
documents, or planning a content-preserving migration. On staging you may break
whatever you like.

## What to build

`bin/deploy.sh` that rsyncs **only** `wp-content/themes/eve-n/` to the server.

- Reads connection settings from `.env`
- Targets **staging by default**; production requires an explicit `--production`
  flag *and* an interactive confirmation
- `--dry-run` showing exactly what would transfer
- Never touches `wp-content/uploads`, plugins, or WordPress core
- Excludes dev files: `.git`, `node_modules`, `.wp-env*`, `*.map`
- Purges `sg-cachepress` after upload (`wp sg purge` or equivalent), otherwise
  changes will not appear
- `--activate` flag running `wp theme activate eve-n` remotely, off by default

Then run it against staging and activate the theme there, so the pipeline is
proven now rather than discovered broken at launch. It is fine that the theme
is still incomplete — the point is to verify transfer, activation and cache
purging work.

Document the deploy and rollback commands in AGENTS.md. Rollback is
`wp theme activate astra` plus a cache purge.

## Acceptance Criteria

- [x] `bin/deploy.sh` transfers the theme folder to staging and nothing else
- [x] Credentials come from `.env`; nothing secret is committed
- [x] `--dry-run` works and transfers nothing
- [x] Production requires `--production` plus confirmation; verify by running
      without the flag and observing it target staging
- [x] Theme deployed and activated on `https://staging2.eve-n.nl`, confirmed
      serving the eve-n theme rather than astra
- [x] Cache purge runs and the change is visible without a manual purge
- [x] Rollback to astra tested on staging and documented
- [x] Deploy and rollback documented in AGENTS.md
