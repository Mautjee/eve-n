# Resume the build

Updated 2026-10-09. Read `AGENTS.md` first — it holds the goal, the architecture
decisions and their reasoning, the conventions, and the wp-cli gotchas.

## Where things stand

**The build is complete and live on https://eve-n.nl.** Production cutover on
2026-10-09. Staging is https://staging2.eve-n.nl.

Verified against production on 2026-10-09:

- `/`, `/onze-werkwijze/`, `/projecten/`, `/over-ons/`, `/blog/`, `/contact/` and a blog
  post all return HTTP 200 over HTTPS with a valid certificate, and `www` 301s to the
  apex.
- The theme is `eve-n` on every page. Zero Elementor, zero Google Fonts, zero Astra in
  the served HTML.
- The header carries the bundled white house as the home button; the hero carries the
  three-bar E mark from the media library, whited out with
  `filter: brightness(0) invert(1)`.
- Images are WebP with full `srcset` ladders. Canonical and `og:` tags are present.
- Home page cold load is 0.98 MB, down from ~16 MB.

Local Lighthouse before task-008 landed: SEO 100, Best Practices 100, Accessibility 95,
Performance 75 — the 75 was entirely the 7.5 MB hero, since fixed, so re-measure if you
want the number confirmed above 90.

`bin/seed.sh` takes a site from empty to populated; `bin/deploy.sh` ships the theme and
purges the cache. Both were rehearsed repeatedly against staging.

## What is left

**`http://eve-n.nl` serves a 200 instead of redirecting to HTTPS.** nginx answers plain
HTTP directly. Canonical is https so search engines are covered, but there is neither a
redirect nor HSTS. Fix with SiteGround's "Enforce HTTPS" or an `.htaccess` rule.

**Real copy is missing, and it is live.** Two separate fixes, and it matters which:

- **Home page** — the lorem ipsum is hardcoded in `front-page.php` (`card__body`
  paragraphs). That is a code change. `AGENTS.md` records Home as "hardcoded for v1,
  revisit later".
- **`/projecten/`** — the lorem is page content. `page-projecten.php` parses accordion
  items out of `the_content()`, and the text was seeded from
  `reference/catalog-reviews.md`, which is itself placeholder. Editing the page in
  `wp-admin` is enough; no template change needed. Needs five project descriptions from
  EVE-N.

**`seed.php` skips content that already exists instead of converging it.** Previously
tracked as task-013: on staging the example post had no featured image because the
script refuses to touch a post that already exists. Production is fine (the post has its
image), but the gap is still in `bin/seed.php`.

**Minor:** the hero mark ships `alt=""`. Decorative is defensible given the visible word
EVE-N sits beside it, but task-014 asked for meaningful Dutch alt text.

## Platform traps found the hard way

- **`imagescale()` with `IMG_BICUBIC` returns false on SiteGround's GD 2.3.3.** Three
  home-page photos silently failed to import — exactly the three source files over the
  2560px cap, the only ones reaching the resize branch. It was not memory (768M) and not
  decoding. The code now falls back to the default filter, then to
  `imagecopyresampled`.
- **Elementor and envato-elements must stay deactivated on production.** While active
  they loaded Elementor's frontend bundle plus three Google Font families on the front
  page, undoing task-009's font work.
- **`wp-env run cli` silently swallows writes** — use `docker exec` into the `-cli-1`
  container instead. See `AGENTS.md` for the exact incantation.
- **Syntax-check PHP without a local PHP install:**
  `docker run --rm -v "$PWD/<file>:/f.php" php:8.2-cli php -l /f.php`

## Working from a fresh machine

- **Local WordPress needs Docker, and the user must be in the `docker` group**, or
  `npx wp-env start` cannot reach the socket.
- **`.env` is gitignored.** Copy `.env.example` and fill it in; the confirmed theme paths
  are documented in the example.
- **The SiteGround deploy key is per-machine.** Register the new public key in Site
  Tools → Devs → SSH Keys Manager, then point `SG_KEY` at it.
- **Rotate the old SiteGround SSH passphrase** — it was pasted into a chat transcript on
  2026-08-07.

## Deploy

```
./bin/deploy.sh --dry-run      # show what would transfer, change nothing
./bin/deploy.sh                # staging (the default), then purge cache
./bin/deploy.sh --activate     # staging, activate eve-n, purge cache
./bin/deploy.sh --production   # live; asks you to type a confirmation
```

**Always purge.** SG Optimizer caches HTML and concatenates assets, so a manual SFTP
upload looks like it did nothing. `bin/deploy.sh` purges for you.

Rollback is switching the active theme back to `astra` and purging — the exact command
is in `AGENTS.md`. The theme files stay on the server, so rolling forward again is
`./bin/deploy.sh --activate`.

## Content architecture reminder

Which parts are editable in `wp-admin` versus fixed in a template decides whether the
client can maintain this site without a developer. Default to editable; hardcode only
where the layout genuinely cannot survive free-form content. The per-page table is in
`AGENTS.md`.
