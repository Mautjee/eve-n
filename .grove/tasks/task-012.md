---
id: task-012
title: "Production cutover checklist"
status: blocked
priority: high
labels: [deploy, infra]
created: 2026-08-07
pr: ""
---

## Description

**BLOCKED — needs the operator's explicit go-ahead. Do not run this
unprompted.**

Staging is verified working. This is the sequence to make eve-n.nl live on the
new theme. Everything here has already been rehearsed on staging.

## Sequence

1. **Full backup first** — files and database, via SiteGround Site Tools. The
   theme switch is reversible; an unbacked-up database is not.
2. `./bin/deploy.sh --production` — uploads the theme, asks for a typed
   confirmation. This alone changes nothing visible; the theme arrives inactive.
3. `./bin/seed.sh --production` — creates the six pages, the Hoofdmenu and the
   site settings. Idempotent, and it never touches content that already exists.
4. Activate: `wp theme activate eve-n` on production, then `wp sg purge`.
5. **Deactivate `elementor` and `envato-elements`.** On staging these were
   loading Elementor's full frontend bundle plus three Google Font families
   (Roboto, Roboto Slab, Sintony, 18 weights each) on the front page, because
   the old front page is an Elementor document. That undoes the self-hosted
   fonts and the block-asset dequeues from task-009. Deactivating them removed
   every Google Fonts request and every Elementor asset, with no visual change
   to the new theme.
6. Re-check every page: `/`, `/onze-werkwijze/`, `/projecten/`, `/over-ons/`,
   `/blog/`, `/contact/`, and a post. Expect HTTP 200 and no PHP errors.
7. Purge cache again and confirm in a private window.

## Also decide with the operator

- **`gtranslate`** is active. The design is Dutch-only and nothing in
  `reference/` shows a language switcher. Keep or drop?
- **`sg-ai-studio`** is active and unused by this theme. Probably drop.
- The old front page is Elementor document #8. Consider pointing
  `page_on_front` at a clean page instead, since `front-page.php` ignores page
  content anyway — cosmetic, but it leaves less confusion behind.

## Rollback

```
wp theme activate astra && wp sg purge
```

Theme files stay on the server, so rolling forward again is just
`./bin/deploy.sh --production --activate`.

## Acceptance Criteria

- [ ] Operator has explicitly approved the cutover
- [ ] Backup taken and its location recorded
- [ ] All seven pages return 200 with no PHP errors on production
- [ ] No Google Fonts and no Elementor assets on any page
- [ ] Blog publishing verified by the client on the live site
- [ ] Contact form verified to actually deliver mail to both addresses
- [ ] Rollback tested once, then rolled forward
