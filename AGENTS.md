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
project replaces it entirely.

**The old site is disposable.** The operator has confirmed nothing on it needs
to be preserved — not the Elementor pages, not the GDQ copy, not the client
logos. Do not plan content migration or spend effort keeping the old theme
working. Staging (`https://staging2.eve-n.nl`) may be broken freely.

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
npx wp-env stop
```

**Use `docker exec` for wp-cli, not `npx wp-env run cli`.** The wrapper
silently swallows writes — `wp option update page_on_front 5` reports success
and changes nothing, and `--porcelain` output comes back polluted with wrapper
text, so `$(...)` captures garbage. Go straight to the container:

```
C=$(docker ps --format '{{.Names}}' | grep -- '-cli-1' | head -1)
docker exec "$C" wp option update page_on_front 5
docker exec "$C" wp rewrite flush --hard
```

Also: `wp rewrite structure` does not persist here either — set
`permalink_structure` with `wp option update` and then flush, or every pretty
URL 404s.

Site: `http://localhost:8888`. Admin: `http://localhost:8888/wp-admin`
(`admin` / `password`). The theme is mounted from `wp-content/themes/eve-n`,
so edits are live on refresh.

**Working in a Grove worktree?** Port 8888 belongs to the main checkout. Each
worktree gets a gitignored `.wp-env.override.json` assigning it a free port —
`wp-env start` picks it up automatically and prints the real URL. Use that URL,
not 8888. If the file is missing, create it — and include the `config` block,
not just the ports: `.wp-env.json`'s `WP_HOME`/`WP_SITEURL` are hard-coded to
`8888`, and without overriding both here too, WordPress 301-redirects every
request back to the main checkout's port and pretty permalinks 404.

```json
{
  "port": 88NN,
  "testsPort": 89NN,
  "config": {
    "WP_SITEURL": "http://localhost:88NN",
    "WP_HOME": "http://localhost:88NN"
  }
}
```

### Seeding content

`bin/seed.sh` takes an empty install to a fully populated eve-n.nl: the six
pages (content transcribed verbatim from `reference/catalog-*.md`), the
"Hoofdmenu" assigned to the `primary` location, site title/tagline/timezone/
locale/permalinks, one example blog post, and it deletes the WordPress
defaults ("Hello world!", the sample page, the default comment). It runs
`bin/seed.php` inside WordPress via `wp eval-file -`, piped over stdin — no
files need to be copied onto the target first.

```
bin/seed.sh                # local wp-env (default)
bin/seed.sh --staging      # staging2.eve-n.nl over SSH, needs .env
bin/seed.sh --production   # live site over SSH, asks for a typed confirmation
```

It's idempotent — every step checks before it creates, so a second run logs
"already exists" / "already set" throughout and changes nothing. Safe to run
after a client has started editing pages: it never touches a page, the menu
or the example post once they exist, only ever creates the ones that are
missing. Page templates from tasks 001-006 are picked up automatically
through WordPress's `page-{slug}.php` hierarchy — this script never assigns
one, so a page whose template hasn't landed yet just renders through
`index.php` until it does.

There is **no build step and no test framework.** Verification is visual:
screenshot at 1920 and 393 and compare against `reference/`.

```
.venv/bin/python slice.py reference/home.png 650 50 --scale 2  # strips
.venv/bin/python crop.py reference/home.png X Y W H out.png 3.0 # zoom
```

### Reading images without burning the budget

Visual comparison is the single largest token cost in this repo, and it is
mostly avoidable. The full-page renders are huge — `reference/home.png` is
2.3 MB, `screenshots/index-1920.png` is 4.2 MB — and reading one costs more
than the entire template you are writing.

- **Read crops, not full pages.** `reference/crops/` already holds pre-sliced
  strips of every render. To check a nav bar or a button, read the strip or
  `crop.py` the region — tens of KB instead of megabytes.
- **Read a full render once** for overall layout, then work from crops.
- **Screenshot the region you changed**, not the whole page, and do not
  re-screenshot after every edit. Batch your changes, then verify once.
- **Do not read your own screenshot back** unless you are actually comparing
  it; capturing it to disk is enough if you only needed it saved.

A worker that reads six full-page PNGs has spent more than a worker that read
sixty crops.

## Deploy

`bin/deploy.sh` rsyncs **only** `wp-content/themes/eve-n/` over SSH and then
purges the SG Optimizer cache. It never writes to uploads, plugins or core.
There is no build step on the server — what is committed is what runs.

Connection settings come from `.env` in the repo root (gitignored; copy
`.env.example`). SiteGround accepts publickey auth only.

| Target | URL | Path |
|---|---|---|
| Staging (default) | `https://staging2.eve-n.nl` | `~/www/staging2.eve-n.nl/public_html` |
| Production (`--production`) | `http://eve-n.nl` | `~/www/eve-n.nl/public_html` |

```
./bin/deploy.sh --dry-run          # show what would transfer, change nothing
./bin/deploy.sh                    # upload to staging + purge cache
./bin/deploy.sh --activate         # upload, activate eve-n, purge cache
./bin/deploy.sh --production       # live site; asks you to type a confirmation
```

**Staging is the default.** No flag means staging. Production needs
`--production` *and* typing `deploy production` at the prompt, so it cannot
happen from a script or by accident.

`--delete` is on, scoped inside `themes/eve-n/`. A file deleted locally is
deleted on the server. Sibling themes are never in scope. Excluded from
transfer: `.git*`, `node_modules`, `.wp-env*`, `*.map`, `.DS_Store`, `*.swp`.

### Rollback

Switch back to the old Astra site and purge. Substitute `eve-n.nl` for
`staging2.eve-n.nl` to roll back production.

```
source .env
ssh -i "${SG_KEY/#\~/$HOME}" -p "$SG_PORT" "$SG_USER@$SG_HOST" \
  'cd ~/www/staging2.eve-n.nl/public_html && wp theme activate astra && wp sg purge'
```

The theme files stay on the server; only the active theme changes, so rolling
forward again is `./bin/deploy.sh --activate`.

### Notes

- **Always purge.** SG Optimizer caches HTML and concatenates assets. The
  script purges for you; a manual upload over SFTP will look like it did
  nothing.
- macOS ships `openrsync`, which has no `--chmod`. The script detects this and
  says so. File modes then come from your checkout — a normal `git clone` gives
  the right ones.
- Verified end-to-end against staging on 2026-08-07: transfer, activate, purge,
  rollback to `astra`, and roll forward again.

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
- **Deploy is SSH + rsync**, not FTP. Use `bin/deploy.sh` — see [Deploy](#deploy).
  There is no build step on the server, which is why the theme must be
  committable and runnable as-is.
- **SG Optimizer caches aggressively.** If a change does not show on staging,
  the cache is the first suspect, not your code. `bin/deploy.sh` purges after
  every upload.
