---
id: task-009
title: "Performance, SEO and metadata pass"
status: in-progress
priority: low
labels: [performance, seo]
created: 2026-08-07
pr: ""
---

## Description

Cleanup pass before launch. Small, mostly `functions.php` and a new
`inc/seo.php`.

**Performance**
- Dequeue the block-library CSS (`wp-block-library`, `wp-block-library-theme`,
  `classic-theme-styles`) and global styles on the front end — this is a
  classic theme that does not use blocks there
- Remove the emoji script and styles
- Self-host the Roboto webfont rather than calling Google Fonts, or at minimum
  `preload` it — it is currently a render-blocking third-party request

**SEO / metadata**
- `<html lang="nl-NL">` (currently `en-US`)
- Per-page `<meta name="description">` and canonical URL
- Open Graph and Twitter card tags, falling back to the featured image
- `robots.txt` and an XML sitemap
- Favicon and touch icons from the EVE-N mark
- JSON-LD `Organization` schema with the real contact details

Do not install an SEO plugin. This is perhaps 120 lines and the site has seven
pages.

## Acceptance Criteria

- [ ] No block-library or emoji assets on any front-end page
- [ ] `lang="nl-NL"` on every page
- [ ] Every page has a unique title, description and canonical
- [ ] Sharing a blog post to LinkedIn or WhatsApp shows title, description
      and image
- [ ] Lighthouse on the home page: Performance >= 90, Accessibility >= 95,
      Best Practices >= 95, SEO >= 95 — scores stated in the PR
