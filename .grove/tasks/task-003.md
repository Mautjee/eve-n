---
id: task-003
title: "Over ons page template with person cards"
status: todo
priority: medium
labels: [theme, template]
created: 2026-08-07
pr: ""
---

## Description

Create `wp-content/themes/eve-n/page-over-ons.php`, ported from
`over-ons.html`.

Three parts: hero with "OVER ONS" overlaid, a centred intro paragraph block,
then **two person cards** side by side — rounded rectangles with a cyan border,
each holding a name, a portrait, and a bio.

The people are Eveline Hinfelaar and Thomas Vilain. Their names, portraits and
full verbatim bios are in `reference/catalog-over-ons.md`. Render:
`reference/over-ons.png`; mobile stacks the cards (`phone-over-ons.png`).

Intro paragraph comes from `the_content()`. The person cards need structure the
editor cannot express, so give them a small registered data source rather than
hardcoding markup — a `even_team_members()` helper in `inc/team.php` returning
an array is enough for two people. Note in the PR if you think this should
become a custom post type later.

## Acceptance Criteria

- [ ] `page-over-ons.php` renders hero, intro from `the_content()`, two cards
- [ ] Bios match `reference/catalog-over-ons.md` verbatim, including the
      Dutch punctuation
- [ ] Cards sit side by side on desktop and stack on mobile
- [ ] Matches `reference/over-ons.png` at 1920px and the phone render at 393px
- [ ] Portraits have meaningful `alt` text, not empty or filename-derived
