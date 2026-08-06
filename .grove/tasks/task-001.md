---
id: task-001
title: "Home page template (front-page.php)"
status: in-progress
priority: high
labels: [theme, template]
created: 2026-08-07
pr: ""
---

## Description

Port `index.html` (repo root) into `wp-content/themes/eve-n/front-page.php`.

The markup and CSS already exist and are visually verified — this is a port,
not a redesign. Move the `<main>` contents of `index.html` into the template,
keep the section banner comments, and drop the duplicated header/footer (those
now come from `get_header()` / `get_footer()`).

Six sections, in order: hero (EVE-N logo + subtitle + scroll chevron), Onze
Werkwijze (teal panel + photo left), Over Ons (white, centred, gold-bar
heading), Blog (teal panel + photo, `row-reverse`), Projecten (two bordered
cards), Contact CTA (photo background).

Copy is verbatim in `reference/catalog-home.md`. Layout truth is
`reference/home.png` (desktop) and `reference/phone-home.png` (mobile).

Text stays hardcoded for v1 — see the content architecture table in AGENTS.md.
Images stay as inline `background-image` for now; task-008 moves them to the
media pipeline. Do not solve that here.

## Acceptance Criteria

- [ ] `front-page.php` renders all six sections in order
- [ ] Every button links to the right page: Werkwijze -> `/onze-werkwijze/`,
      Over Ons -> `/over-ons/`, Blog -> `/blog/`, Contact -> `/contact/`
- [ ] Visually matches `reference/home.png` at 1920px and
      `reference/phone-home.png` at 393px; screenshots saved to `screenshots/`
- [ ] No PHP notices, warnings or deprecations with `WP_DEBUG` on
- [ ] Mobile menu opens and closes; hero collapses per the phone render
