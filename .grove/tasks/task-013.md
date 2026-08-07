---
id: task-013
title: "seed.php: converge existing content, don't just skip it"
status: todo
priority: low
labels: [wp-cli, content]
created: 2026-08-07
pr: ""
---

## Description

`bin/seed.php` is idempotent in the weak sense — it never creates a duplicate —
but not in the strong sense: when something already exists it returns early and
never brings it to the desired state.

Found on staging. `even_seed_example_post()` logs "Example post already exists
(#91)" and returns, so the post never got a featured image, because it had been
created by an earlier run from before the image pipeline existed. The blog index
then rendered a card with no image. Fixed by hand on staging with
`wp post meta update 91 _thumbnail_id 95`; the script still has the gap.

This matters whenever the script gains a new step: every site seeded before that
step stays stale forever, and the only signal is a "already exists" line that
looks like success.

Make each step converge rather than skip. Setting a field that is currently
empty is not clobbering a client's edit — leaving it empty is the bug. The
existing rule still holds: never overwrite a value the client has actually set.

Audit every `even_seed_*` function for the same shape, not just the example
post. `even_seed_page()` is the other obvious one — a page created before the
catalog copy was finalised keeps whatever it had.

## Acceptance Criteria

- [ ] An existing example post with no featured image gets one on the next run
- [ ] An existing post that already has a featured image is left alone
- [ ] Every `even_seed_*` function audited; findings noted in the PR even where
      no change was needed
- [ ] Running twice on a fresh install still reports no changes the second time
- [ ] Verified against staging, which currently has content from several
      generations of this script
