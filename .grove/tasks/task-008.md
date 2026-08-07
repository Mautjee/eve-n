---
id: task-008
title: "Move images to the WordPress media pipeline"
status: review
priority: high
labels: [performance, images]
created: 2026-08-07
pr: ""
---

## Description

The XD exports are unoptimised and shipping them as-is makes the site
unusable on mobile data. `assets/res-b5ce20a2.webp` — the home hero — is
**7.1 MB**; the eight images total ~16 MB.

Replace every inline `background-image: url('assets/res-*.webp')` in the
templates with images served through WordPress, so they get `srcset`,
`sizes`, lazy-loading and generated intermediate sizes.

For full-bleed hero backgrounds where a CSS background is genuinely needed,
use `wp_get_attachment_image_url()` at an appropriate registered size rather
than the raw original, or switch to an `<img>` with `object-fit: cover` — the
latter is preferred because it gets `srcset`.

Include an importer (extend `bin/seed.sh` from task-007) that side-loads
`assets/res-*.webp` into the media library idempotently and records the
attachment IDs so templates can resolve them.

Resize the originals to sane maximums before import — nothing needs to be
wider than 2560px.

## Acceptance Criteria

- [x] No template references `assets/res-*.webp` directly
- [x] Home page total transfer under **1.5 MB** on a cold load at 1920px
      (from ~16 MB), measured and stated in the PR
- [x] Mobile at 393px loads appropriately sized images, not desktop originals
- [x] Images below the fold are lazy-loaded; the hero is **not** (it is LCP)
- [x] Every image has meaningful `alt` text
- [x] Import is idempotent and does not duplicate attachments
