# Resume the build

Paste this into a fresh Claude Code session in `/Users/mauro/Dev/vivera-site`
to pick the work back up. Written 2026-08-07 while the operator was away.

You are the **Grove orchestrator**: workers write the code, you triage, review,
merge and deploy. Read `AGENTS.md` first — it holds the goal, the architecture
decisions and their reasoning, the conventions, and the wp-cli gotchas.

## Where things stood

**All twelve build tasks are merged and live on https://staging2.eve-n.nl.**
Eleven PRs reviewed and merged. The build is feature-complete.

Verified on staging — every page HTTP 200, zero PHP errors, zero Google Fonts,
zero Elementor assets: `/`, `/onze-werkwijze/`, `/projecten/`, `/over-ons/`,
`/blog/`, `/contact/`, and a post. All 8 images in the media library with
`srcset`. **Home page cold load: 0.98 MB, down from ~16 MB.**

Lighthouse (local, before task-008 landed): SEO 100, Best Practices 100,
Accessibility 95, Performance 75 — the 75 was entirely the 7.5 MB hero, now
fixed, so re-measure to confirm it clears 90.

`bin/seed.sh` populates a site from empty; `bin/deploy.sh` ships the theme and
purges cache. Both rehearsed repeatedly against staging.

**Elementor and envato-elements are deactivated on staging** — they loaded
Elementor's frontend bundle plus three Google Font families on the front page,
undoing task-009's font work. Must be repeated on production (task-012).

## Two fixes made directly, not by a worker

Both found while verifying the staging deploy, both fixed at the usage limit
when dispatching a worker was impractical:

- **`imagescale()` with `IMG_BICUBIC` returns false on SiteGround's GD 2.3.3.**
  Three home-page photos silently failed to import — exactly the three source
  files over the 2560px cap, the only ones reaching the resize branch. Not
  memory (768M) and not decoding. Now falls back to the default filter, then to
  `imagecopyresampled`. Committed.
- **The example post on staging had no featured image**, because `seed.php`
  skips content that already exists rather than converging it. Patched staging
  by hand; the script gap is tracked as task-013.

## What is left

| Task | |
|---|---|
| 012 | **Production cutover — blocked on the operator's approval** |
| 013 | `seed.php` should converge existing content, not skip it (low) |

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
