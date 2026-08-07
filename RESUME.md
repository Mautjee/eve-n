# Resume the build

Paste this into a fresh Claude Code session in `/Users/mauro/Dev/vivera-site`
to pick the work back up. Written 2026-08-07 while the operator was away.

You are the **Grove orchestrator**: workers write the code, you triage, review,
merge and deploy. Read `AGENTS.md` first — it holds the goal, the architecture
decisions and their reasoning, the conventions, and the wp-cli gotchas.

## Where things stood

**Tasks 001-007 and 009-011 are merged and live on
https://staging2.eve-n.nl.** Ten PRs reviewed and merged.

Every page verified on staging — HTTP 200, zero PHP errors, zero Google Fonts,
zero Elementor assets: `/`, `/onze-werkwijze/`, `/projecten/`, `/over-ons/`,
`/blog/`, `/contact/`, and the example post. Components confirmed: 5 accordion
items, 2 team cards, 4 contact fields, blog card grid, real WP nav menu,
`lang="nl-NL"`, sitemap and robots.txt.

`bin/seed.sh` populates a site from empty and is idempotent. `bin/deploy.sh`
ships the theme and purges cache. Both verified against staging.

**Elementor and envato-elements were deactivated on staging.** They loaded
Elementor's frontend bundle plus three Google Font families on the front page,
because `page_on_front` is an old Elementor document. This undid task-009's
font work. The same must be done on production — see task-012.

## The one thing left

**task-008 — images to the media pipeline.** Not merged. It was still running
at 135 turns when the usage limit hit 100%. It is the only thing standing
between the site and a Lighthouse Performance pass: LCP is ~44s, entirely from
`home-hero.webp` at 7.5 MB served eagerly. Everything else in the report is
green (SEO 100, Best Practices 100, Accessibility 95, Performance 75).

Check `gv ls --json` first — the worker may have finished, died, or be
mid-retry.

**Do not run `gv untrack task-008 --rm` before checking the worktree.** As of
02:30 it held ~36 minutes of *uncommitted* work, and `--rm` deletes the
worktree. Look first:

```
W=/Users/mauro/Dev/.worktrees/vivera-site/task-008-move-images-to-the-wordpress
git -C "$W" status --short
git -C "$W" log --oneline main..HEAD
```

If there are uncommitted changes worth keeping, salvage them before doing
anything destructive:

```
git -C "$W" add -A && git -C "$W" commit -m "task-008: WIP salvaged"
git -C "$W" push -u origin task-008-move-images-to-the-wordpress
```

Only re-grab from scratch once you are sure nothing is worth keeping.

Two things that worktree had already worked out, worth not rediscovering:

- `add_image_size()` from `even_setup()` does not register when the theme is
  switched mid-request, so on a fresh install every imported image silently
  gets no generated sizes. Fix: call `even_setup()` explicitly in the importer.
- It moved the bundled fallback images to `assets/img/seed/`, separating
  "shipped with the theme so a fresh install is not broken" from "belongs in
  the media library". That split is worth keeping.

Note this task cost **$12.15 over 173 turns on Sonnet** — more than any Opus
task in this project. Image work is genuinely fiddly; budget for it.

## How to continue

```
git pull
gv ls --json                    # who is alive, who has a PR
gh pr list
```

1. **Re-grab anything dead**: `gv grab <task> --repo vivera-site --model claude-sonnet-5`
2. **Give each worktree its own port** — `.wp-env.override.json` with
   `{"port": 89NN, "testsPort": 89NN}`, gitignored. Without this the second
   worker to start WordPress fails on the port 8888 collision.
3. **Review each PR by reading the diff**, not just the description.
4. **Merge.** Conflicts in `functions.php` and `assets/css/main.css` are
   expected and always additive — keep both sides. Then check:
   - no `<<<<<<<` / `>>>>>>>` markers anywhere
   - CSS braces balanced
   - `php -l` clean: `docker run --rm -v "$PWD/<file>:/f.php" php:8.2-cli php -l /f.php`
5. **Verify locally**, then `./bin/deploy.sh --activate`, then confirm staging
   still serves `wp-content/themes/eve-n` with no errors.

**Staging only. Do not deploy to production.**

## Open items for the operator

- **Real copy is missing.** The Projecten cards and blog posts are still lorem
  ipsum. Needs five project descriptions and a first blog post from EVE-N.
- **Production cutover** has not happened and needs their explicit go-ahead.
- **Rotate the SiteGround SSH passphrase** — it was pasted into a chat
  transcript on 2026-08-07.
