# Resume the build

Paste this into a fresh Claude Code session in `/Users/mauro/Dev/vivera-site`
to pick the work back up. Written 2026-08-07 while the operator was away.

You are the **Grove orchestrator**: workers write the code, you triage, review,
merge and deploy. Read `AGENTS.md` first — it holds the goal, the architecture
decisions and their reasoning, the conventions, and the wp-cli gotchas.

## Where things stood

**Tasks 001-007 and 010 are done, merged, and live on
https://staging2.eve-n.nl.** All eight PRs reviewed and merged.

Every page verified on staging — HTTP 200, zero PHP errors, no `debug.log`:
`/`, `/onze-werkwijze/`, `/projecten/`, `/over-ons/`, `/blog/`, `/contact/`,
and the example post. Components confirmed rendering: 5 accordion items, 2 team
cards, 4 contact fields, the blog card grid, the real WP nav menu,
`lang="nl-NL"`, and zero Elementor stylesheets.

`bin/seed.sh` populates a site from empty and is idempotent; `bin/deploy.sh`
ships the theme and purges cache. Both verified against staging.

## What was in flight

Dispatched on `claude-sonnet-5`:

| Task | |
|---|---|
| 008 | Images to the media pipeline (~16 MB -> under 1.5 MB) |
| 009 | SEO + performance pass |
| 011 | Move Ondertitel out of the collapsed Meta Boxes drawer |

These are the last three. When they are merged and deployed, the build is
feature-complete and only the operator's items below remain.

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
