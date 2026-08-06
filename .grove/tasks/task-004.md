---
id: task-004
title: "Projecten page: accordion from page content"
status: review
priority: medium
labels: [theme, template]
created: 2026-08-07
pr: "https://github.com/Mautjee/eve-n/pull/5"
---

## Description

Create `wp-content/themes/eve-n/page-projecten.php`, ported from
`reviews.html`. **Note the rename** — the old filename says "reviews", the page
is Projecten, and the route is `/projecten/`.

Hero with "PROJECTEN" and a gold underline bar, then five collapsible accordion
cards: white, cyan border, rounded, gold vertical accent bar left of the title,
gold `+` / `-` toggle on the right. All collapsed by default on desktop.

Make the items **editable without a plugin**: parse `the_content()` into
heading/body pairs, where each `<h2>` becomes an accordion title and the markup
following it becomes the panel. That way the client adds a project by adding a
heading and a paragraph in the normal editor.

The accordion JS already exists in `assets/js/main.js` (`.accordion__item` /
`.accordion__header`, toggled `open` class) and the CSS already exists in
`main.css`. Reuse both — do not write new ones.

Spec: `reference/catalog-reviews.md`. Renders: `reference/reviews.png`,
`reference/phone-projecten.png` (which shows item 4 expanded).

## Acceptance Criteria

- [ ] `page-projecten.php` builds accordion items from `the_content()` headings
- [ ] Toggling opens one item and closes the others; `+` becomes `-`
- [ ] Accordion headers are real `<button>`s with `aria-expanded` and are
      reachable and operable by keyboard
- [ ] Matches `reference/reviews.png` at 1920px and the phone render at 393px
- [ ] A page with no headings renders empty rather than fataling
