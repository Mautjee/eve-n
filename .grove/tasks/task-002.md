---
id: task-002
title: "Onze Werkwijze page template"
status: in-progress
priority: high
labels: [theme, template]
created: 2026-08-07
pr: ""
---

## Description

Create `wp-content/themes/eve-n/page-onze-werkwijze.php`, ported from
`onze-werkwijze.html`.

Simple page: full-bleed hero with the page title overlaid left-aligned in white
Menlo-style type, then a single centred column of body copy on white.

**This page must be fully editable.** The hero title comes from
`the_title()`, the hero image from the featured image (with the XD export as
fallback), and the body from `the_content()`. Do not hardcode the paragraphs
into the template — they get seeded as page content in task-007 instead.

Copy and measurements: `reference/catalog-onze-werkwijze.md`. Renders:
`reference/onze-werkwijze.png`, `reference/phone-onze-werkwijze.png`.

The first paragraph is bold in the design; that is authored content, so it
should come through as `<strong>` from the editor, not a template rule.

## Acceptance Criteria

- [ ] `page-onze-werkwijze.php` renders hero + `the_content()`
- [ ] Hero title from `the_title()`, image from featured image with fallback
- [ ] Body copy renders editor content, including headings and lists
- [ ] Matches `reference/onze-werkwijze.png` at 1920px and the phone render
      at 393px
- [ ] No PHP notices with `WP_DEBUG` on
