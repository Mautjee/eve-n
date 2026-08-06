---
id: task-007
title: "Seed pages, menu and site settings via WP-CLI"
status: todo
priority: medium
labels: [content, wp-cli]
created: 2026-08-07
pr: ""
---

## Description

Write `bin/seed.sh` (or a WP-CLI PHP script) that takes an empty WordPress
install to a fully populated eve-n.nl. It must be **idempotent** — running it
twice changes nothing the second time — because it will be run against local,
staging and production.

It should:

- Create the six pages with exact slugs: `onze-werkwijze`, `projecten`,
  `over-ons`, `blog`, `contact` (plus the front page)
- Seed each page's content **verbatim from `reference/catalog-*.md`** — do not
  retype from screenshots, the copy is already transcribed
- Set the static front page and assign `blog` as the posts page
- Create the "Hoofdmenu" menu with the five links in design order and assign it
  to the `primary` location
- Set site title, tagline, timezone `Europe/Amsterdam`, locale `nl_NL`, date
  format `d-m-Y`, and permalinks to `/%postname%/`
- Delete the WordPress defaults: "Hello world!", the sample page, the default
  comment
- Create one example blog post so the blog is not empty on first view

Depends on the page templates existing (tasks 001-006) only for the templates
to be assignable; write it so missing templates degrade rather than fail.

## Acceptance Criteria

- [ ] Running the script on a fresh `wp-env` install produces the full site
- [ ] Running it a second time reports no changes and creates no duplicates
- [ ] All five nav links resolve; no 404s
- [ ] Page copy matches `reference/catalog-*.md` verbatim
- [ ] Default WordPress content is gone
- [ ] Script documented in AGENTS.md under local development
