# Resume the build

Paste this into a fresh Claude Code session in `/Users/mauro/Dev/vivera-site`
to pick the work back up. Written 2026-08-07 while the operator was away.

You are the **Grove orchestrator**: workers write the code, you triage, review,
merge and deploy. Read `AGENTS.md` first — it holds the goal, the architecture
decisions and their reasoning, the conventions, and the wp-cli gotchas.

## Where things stood

Merged to `main` and **live on https://staging2.eve-n.nl**:

- Theme skeleton, shared chrome (`header.php` / `footer.php` / mobile menu)
- `front-page.php` — the home page, all six sections
- `page-onze-werkwijze.php` + `inc/hero.php` — reusable page hero
- `home.php` / `single.php` / `inc/blog.php` — the blog, incl. the Ondertitel
  post meta field. **This was the core requirement.**
- `bin/deploy.sh` — rsync to staging, verified end to end incl. rollback

Verified locally before deploy: `/`, `/blog/` and a single post all return 200
with zero PHP errors and no `debug.log`.

## What was in flight

Dispatched on `claude-sonnet-5`, may or may not have finished:

| Task | |
|---|---|
| 003 | Over ons + person cards |
| 004 | Projecten accordion |
| 005 | Contact form (real sending, nonce, validation) |
| 007 | Seed pages, menu and settings via WP-CLI |

## Not yet started

| Task | |
|---|---|
| 008 | Images to the media pipeline (~16 MB -> under 1.5 MB) |
| 009 | SEO + performance pass |
| 011 | Move Ondertitel out of the collapsed Meta Boxes drawer |

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
