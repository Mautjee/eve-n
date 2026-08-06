# vivera-site — eve-n.nl

## The goal

Rebuild **eve-n.nl** as a custom WordPress theme that matches the Adobe XD
design in `reference/`, in Dutch, working on desktop and mobile, with a blog
that Eveline Hinfelaar and Thomas Vilain can write and publish from `wp-admin`
without a developer.

EVE-N is a Dutch consultancy for team development and collaboration in
infrastructure projects. Seven pages: Home, Onze Werkwijze, Projecten, Over ons,
Blog index, Blog post, Contact.

**What is live today is not this site.** `eve-n.nl` currently runs an older
design on the Astra theme under the branding "Eve(N)|n=3", with no blog. This
project replaces it.

## Why WordPress

The host is **SiteGround shared hosting**, which runs PHP and MySQL and
[does not support Node.js](https://www.siteground.com/kb/node-js-available/).
Astro, Next and anything Node-based are therefore impossible on this host —
this is settled, do not re-litigate it.

WordPress was chosen over hand-written PHP because the blog requirement (a
non-technical author pasting formatted text from other sources, with images,
drafts and publishing) otherwise means hand-building auth, sessions, CSRF, a
rich-text editor, paste sanitisation, image upload and resizing — weeks of work
and a bespoke security surface. SiteGround also ships one-click WordPress,
staging, and managed updates.

The trade-off accepted: WordPress is heavier than static. It is mitigated by
writing a lean custom theme — **no page builder, no theme framework, no plugin
sprawl.** Keep it that way.

## Layout

- `wp-content/themes/eve-n/` — **the deliverable.** The only thing that ships.
  - `style.css` — theme header only. Real CSS is `assets/css/main.css`.
  - `functions.php` — theme supports, asset loading, nav helpers.
  - `header.php` / `footer.php` — shared chrome, written once.
  - `assets/css/main.css` — the full stylesheet, ported verbatim from the
    static site, plus a "WordPress integration" block at the end.
  - `assets/js/main.js` — mobile menu, accordion, card toggle. No dependencies.
- `reference/` — **design source of truth, read-only.**
  - `catalog-<page>.md` — written specs per page: exact colours, type sizes and
    **verbatim Dutch copy**. Seed page content from these, never retype from a
    screenshot.
  - `<page>.png` / `phone-<page>.png` — desktop and phone artboard renders.
  - `crops/` — sliced strips of those renders for detailed reading.
  - `globalResources.json` / `interactions.json` — raw XD exports.
- `*.html` + `assets/` (repo root) — the **static reconstruction**, the port
  source. Each page's markup and CSS is already written and visually verified;
  porting a page means moving that markup into a PHP template, not designing it
  again. Do not delete these until every page is ported.
- `screenshots/` — browser captures for comparison, `<page>-<width>[-vN].png`
  (1920 = desktop, 393 = phone). Gitignored.
- `.grove/tasks/task-NNN.md` — the work queue.

## Local development

```
npm install
npx wp-env start          # WordPress + MySQL on Docker, PHP 8.2
npx wp-env run cli wp ...  # wp-cli
npx wp-env stop
```

Site: `http://localhost:8888`. Admin: `http://localhost:8888/wp-admin`
(`admin` / `password`). The theme is mounted from `wp-content/themes/eve-n`,
so edits are live on refresh.

There is **no build step and no test framework.** Verification is visual:
screenshot at 1920 and 393 and compare against `reference/`.

```
.venv/bin/python slice.py reference/home.png 650 50 --scale 2  # strips
.venv/bin/python crop.py reference/home.png X Y W H out.png 3.0 # zoom
```

## Content architecture

Which parts are editable in `wp-admin` versus fixed in a template. Follow this —
it is the difference between a site the client can maintain and one they cannot.

| Page | Structure | Text |
|---|---|---|
| Home | Template | Hardcoded for v1 (composed layout; revisit later) |
| Onze Werkwijze | Template | `the_content()` — fully editable |
| Over ons | Template | Intro via `the_content()`; person cards structured |
| Projecten | Template | Accordion items parsed from `the_content()` headings |
| Contact | Template | Form + contact blocks structured |
| Blog | WordPress | **Fully editable — this is the point of the project** |

Default to editable. Hardcode only where the layout genuinely cannot survive
free-form content.

## Conventions

- **PHP**: WordPress coding standards. Tabs, not spaces. Escape on output
  (`esc_html`, `esc_attr`, `esc_url`), never trust input. Prefix every global
  function `even_`. `defined( 'ABSPATH' ) || exit;` at the top of every file.
- **Templates**: keep the `<!-- ========== SECTION ========== -->` banner
  comments from the static HTML. Semantic elements. Dutch copy.
- **Shared-file contention**: workers run in parallel worktrees. Put new PHP
  helpers in `inc/<area>.php` with a single `require` line in `functions.php`
  rather than appending large blocks to `functions.php`. Append new CSS as a
  clearly-named section rather than editing existing rules.
- **CSS**: BEM-ish — block `.section-teal`, element `.section-teal__photo`,
  modifier `.btn--ghost`. All colours and sizes come from the `:root` tokens.
  One breakpoint: `@media (max-width: 768px)`.
  **The component CSS already exists.** Most tasks need little or no new CSS —
  check `main.css` before writing any.
- **JS**: one `DOMContentLoaded` handler, `querySelectorAll` + `forEach`, state
  as a toggled `open` class, null-guards before wiring listeners.
- **Language**: the site is Dutch (`lang="nl"`). UI strings go through
  `__( '...', 'eve-n' )`. HTML entities (`&ndash;`, `&copy;`) over literal
  non-ASCII.

## Gotchas

- **`reviews.html` is the Projecten page.** It becomes the `/projecten` route;
  the old filename is misleading.
- **Images are unoptimised XD exports.** `res-b5ce20a2.webp` (home hero) is
  **7.1 MB**; the set totals ~16 MB. They must go through the WordPress media
  pipeline for `srcset` before launch. Do not ship them as raw CSS
  `background-image` URLs.
- **`index.html` still has lorem ipsum** in the Projecten cards, as does the
  blog placeholder content. Real copy is pending from the client.
- **WordPress injects block-library CSS** on every page. This is a classic
  theme that does not use blocks on the front end — dequeue it.
- **The nav is a real WP menu** ("Hoofdmenu") with a hardcoded fallback in
  `even_nav_menu()`. The stylesheet targets `a.active`; WordPress marks the
  parent `<li>`, so `even_nav_link_active_class()` copies the state onto the
  anchor. Do not "fix" this by changing the CSS.
- **`download.sh` is dead.** It hardcodes an absolute path and an expired Adobe
  CDN token, and writes files without extensions. Do not run it.
- **Deploy is FTP** to SiteGround (SSH availability unconfirmed). No build step
  on the server, which is why the theme must be committable and runnable as-is.
