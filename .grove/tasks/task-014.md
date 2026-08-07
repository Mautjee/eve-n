---
id: task-014
title: "Header house as home button; white E mark in the hero"
status: review
priority: high
labels: [theme, branding]
created: 2026-08-07
pr: "https://github.com/Mautjee/eve-n/pull/12"
---

## Description

Restore the two brand marks to what `reference/home.png` actually shows. Right
now the header carries the EVE-N wordmark and the hero carries the white house
— the design has these the other way round.

| Slot | Now | Should be |
|---|---|---|
| Header top-left, every page | EVE-N wordmark | **White house**, as the home button |
| Home hero, beside "EVE-N" | White house | **Three-bar E mark, in white** |

Check `reference/home.png` and `reference/phone-home.png` before starting. Use
`reference/crops/` rather than the full renders — see AGENTS.md on image cost.

### 1. Header — the house

`even_logo()` already falls back to the bundled white house
(`assets/img/logo.webp`) when no `custom_logo` theme mod is set, and the
surrounding `<a class="nav-logo" href="/" aria-label="Home">` already makes it
a home button. **The operator will clear the theme mod on production; you do
not need a template change for this.**

What you do need: confirm the header CSS suits a square-ish mark again. The
current rule is `height: 28px; width: auto; max-width: 170px`, added for the
5:1 wordmark. The house is 1.08:1, so it renders ~30x28 — fine, but verify it
against the render and adjust if the design wants a true 28x28.

### 2. Hero — the E mark, white

Replace the house `<img>` in `front-page.php`'s `.hero-logo` with the three-bar
E mark, resolved **from the media library** (the operator wants brand assets to
live there, not bundled loose in the theme).

The source is `Eve-nicoonkleur.png` — teal/gold/black bars on a transparent
canvas, 2000x2000, artwork square at 1.00:1. **The gaps between the bars are
transparent, not opaque white**, so a filter recolour produces a clean mark
over the hero photo rather than a solid block. Verified before writing this.

**Make it white with CSS, not a new asset.** There is no white version of the
icon and the operator does not want one maintained:

```css
filter: brightness(0) invert(1);
```

Scope that to the hero mark only. The header house is already white — do not
filter it, and do not apply this to `.nav-logo img`.

**Resolution and fallback.** Follow the existing pattern: bundle the source in
`assets/img/seed/`, import it through `bin/seed.php` under a key (suggest
`hero-mark`) so it is idempotent and works in any environment, and resolve it
in the template via `even_seeded_image()`. If the attachment cannot be
resolved, fall back to the bundled house rather than rendering a broken image
— the front page must never break because an option is unset.

Keep the `alt` meaningful and Dutch-appropriate; it sits beside the visible
word "EVE-N", so avoid repeating that text verbatim.

## Constraints

- **Do not touch production or staging.** Build and verify in local `wp-env`
  only. The operator runs the production deploy; that is not your step.
- Append CSS as a new named section; do not edit existing rules.
- No build step, no new npm dependency.

## Acceptance Criteria

- [ ] Hero shows the three-bar E mark in white beside "EVE-N", matching
      `reference/home.png` at 1920px and `phone-home.png` at 393px
- [ ] The mark's transparent gaps stay transparent — it is not a white block
- [ ] Header still shows the white house, undistorted, linking to `/` with its
      `aria-label`, verified with the `custom_logo` theme mod cleared locally
- [ ] The white filter applies only to the hero mark, not the header
- [ ] With the attachment missing or the option unset, the front page still
      renders — falls back, no PHP notice, no broken image
- [ ] `bin/seed.sh --local` imports the icon; a second run reports no changes
      and creates no duplicate
- [ ] `php -l` clean, CSS braces balanced, no PHP notices with `WP_DEBUG` on
