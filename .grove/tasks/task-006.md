---
id: task-006
title: "Blog: index grid and single post"
status: in-progress
priority: high
labels: [theme, template, blog]
created: 2026-08-07
pr: ""
---

## Description

**This is the core requirement of the project** — the reason it is WordPress
rather than a static site. Eveline and Thomas must be able to write a post in
`wp-admin` and have it appear correctly, including pasting formatted text from
Word or Google Docs.

Two templates in `wp-content/themes/eve-n/`:

**`home.php`** (blog index, from `blogs.html`) — hero with "BLOG", an intro
block, then a 2-column card grid. Each card: featured image with a blue border,
a gold rule beneath, then date (`d-m-Y`), title, and subtitle. Cards link to
the post. Paginate at 4 per row-pair; add pagination controls, since the design
only shows four placeholder cards and the client will exceed that.

**`single.php`** (from `blog-pagina.html`) — full-bleed hero with the post
title and subtitle centred over the featured image with a dark overlay, then a
centred single-column body from `the_content()`.

Posts need an **"Ondertitel"** field (the design's subtitle, distinct from the
excerpt). Register it as post meta with a meta box, `show_in_rest` true,
sanitised on save. Put the code in `inc/blog.php`.

Specs: `reference/catalog-blogs.md`, `reference/catalog-blog-pagina.md`.
Renders: `blogs.png`, `phone-blog.png`, `blog-pagina.png`,
`phone-blog-pagina.png`.

## Acceptance Criteria

- [ ] `home.php` renders posts in the card grid; two columns desktop, one mobile
- [ ] Cards show featured image, gold rule, date, title, subtitle, and link to
      the post
- [ ] Pagination works past the first page
- [ ] `single.php` renders title + subtitle over the hero, body centred
- [ ] "Ondertitel" is editable in the post editor and is sanitised on save
- [ ] A post with **no featured image and no subtitle** renders without a
      broken layout or a PHP notice
- [ ] Content pasted from Word renders readably: headings, bold, italic, lists,
      links, and inline images all styled
- [ ] Matches the blog renders at 1920px and 393px
