# vivera-site

A static, hand-written marketing website for EVE-N (a Dutch consultancy for team
development and collaboration in infrastructure projects). It is seven standalone
HTML pages in Dutch (`lang="nl"`) sharing one stylesheet and one script, with no
build step, no framework, and no server-side code. The pages are a
pixel-reconstruction of an Adobe XD design: the original artboard renders, the
exported image resources, and the XD `globalResources.json` / `interactions.json`
dumps live in `reference/` and are the source of truth for colours, type sizes,
and copy. Python/bash helper scripts exist only to fetch and slice those
reference renders for visual inspection.

## Layout

- `*.html` (repo root) — the deliverable pages, one file per route:
  `index.html`, `onze-werkwijze.html`, `reviews.html` (titled "Projecten"),
  `over-ons.html`, `blogs.html`, `blog-pagina.html`, `contact.html`.
  Header, mobile menu, and footer markup are duplicated verbatim in every page.
- `assets/css/style.css` — the entire stylesheet (~1065 lines). Starts with a
  `:root` design-token block, then one commented section per component.
- `assets/js/main.js` — the entire script (~50 lines): mobile menu toggle,
  accordion toggle, project-card toggle. No dependencies.
- `assets/res-*.webp` — image resources exported from XD, referenced by content
  hash filename from the HTML.
- `reference/` — design source of truth. `catalog-<page>.md` are per-page written
  specs (colours, sizes, copy) derived from the renders; `<page>.png` and
  `phone-<page>.png` are the desktop/phone artboard renders; `crops/` holds
  sliced/zoomed strips of those renders; `globalResources.json` and
  `interactions.json` are raw XD exports.
- `screenshots/` — browser screenshots of the built pages for comparison against
  `reference/`, named `<page>-<viewport-width>[-vN].png` (1920 = desktop,
  393 = phone).
- `download.sh`, `slice.py`, `slice.sh`, `crop.py` — tooling for the reference
  workflow only; they do not touch the site output.
- `.grove/` — Grove workspace config and markdown task files (`tasks/task-NNN.md`
  with YAML frontmatter: id, title, status, priority, labels, created, pr).

There is no test directory and no test framework.

## Build / test / lint

There is no build, test, or lint tooling in this repo — no `package.json`,
`Makefile`, `requirements.txt`, or `pyproject.toml`. The commands that exist:

```
open index.html                              # view the site directly (file://)
python3 -m http.server 8000                  # or serve the root and open :8000
./download.sh                                # re-fetch XD renders + assets into reference/ and assets/
./slice.sh reference/home.png 650 50         # sips-based slicing -> reference/crops/home-NN.png
.venv/bin/python slice.py reference/home.png 650 50 --scale 2   # PIL equivalent, supports --scale
.venv/bin/python crop.py reference/home.png X Y W H out.png 3.0 # zoom a region
```

`slice.py` / `crop.py` need the checked-in `.venv` (Pillow); `slice.sh` needs
macOS `sips`. There is no requirements file — the venv is the dependency record.

## Conventions

- HTML: 2-space indent, `<!-- ========== SECTION ========== -->` banner comments
  between major blocks, semantic elements (`header`/`nav`/`main`/`section`/
  `article`/`footer`), Dutch copy, `aria-label` on nav and logo links,
  `aria-hidden="true"` on decorative glyphs. HTML entities (`&ndash;`, `&copy;`)
  instead of literal non-ASCII.
- CSS: BEM-ish naming — block `.section-teal`, element `.section-teal__photo`,
  modifier `.btn--ghost` / `.hero--home`. All colours, sizes, and spacing come
  from `--custom-properties` declared in `:root`; component sections are
  delimited by `/* ---- Name ---- */` comments. One breakpoint only:
  `@media (max-width: 768px)`, collected in a single block at the end of the file.
- Per-page overrides are written as inline `style="..."` on the section (e.g.
  `background-image`, `flex-direction: row-reverse`) rather than as new classes.
- JS: a single `DOMContentLoaded` handler, `querySelectorAll` + `forEach`,
  state expressed as a toggled `open` class; null-guards (`if (hamburger && …)`)
  before wiring listeners. No error handling beyond those guards.
- Nav: the current page's link carries `class="active"` in both the desktop
  `.nav-links` and the `.mobile-menu` copy.
- Bash scripts use `set -euo pipefail` and a one-line usage comment at the top.
- No commit style is observable — the repo has zero commits.

## Gotchas

- The git repo has **no commits at all** and **no `.gitignore`**. `.venv/`
  (~2500 files), `reference/`, `screenshots/`, and multi-megabyte `assets/*.webp`
  are all untracked and unignored; a naive `git add -A` would commit them.
- `download.sh` hardcodes an absolute `ROOT=/Users/mauro/Dev/vivera-site` and an
  expiring Adobe CDN access token; it also writes `assets/res-*` **without a file
  extension**, while the HTML references `assets/res-*.webp`. Files must be
  renamed after download.
- `assets/res-b5ce20a2.webp` (the home hero background) is 7.5 MB and
  `res-9c128ef7.webp` is 2.4 MB; images are unoptimised full-size XD exports.
- Changing shared chrome (header, mobile menu, footer) means editing all seven
  HTML files — there is no include mechanism.
- `reviews.html` is the "Projecten" page; the filename and the visible title
  do not match.
- Cards in `index.html` still contain lorem-ipsum placeholder copy.
- `.venv` includes `pytesseract` (and therefore expects a system `tesseract`
  binary), but no script in the repo calls it.
