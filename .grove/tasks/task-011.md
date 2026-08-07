---
id: task-011
title: "Ondertitel: move out of the collapsed Meta Boxes drawer"
status: review
priority: low
labels: [blog, editor-ux]
created: 2026-08-07
pr: "https://github.com/Mautjee/eve-n/pull/9"
---

## Description

Raised by the task-006 author while building the blog.

The "Ondertitel" field is a classic meta box, so in the block editor it lands
in the **collapsed Meta Boxes drawer at the bottom of the page**. The client has
to know to expand it. Since the whole point of this project is that Eveline and
Thomas can publish without a developer, a field they cannot find is a field that
stays empty — and the design shows the subtitle on every card and every post
hero.

Move it into the post sidebar as a native panel. `wp.plugins.registerPlugin`
with `PluginDocumentSettingPanel` and `wp.element.createElement` does this in
roughly 30 lines with **no build step**, which matters — there is no bundler in
this repo and we are not adding one.

Keep the existing `register_post_meta` registration in `inc/blog.php`; it
already has `show_in_rest`, a sanitise callback and an auth callback, so the
REST path the sidebar uses is ready. Remove the classic meta box only once the
sidebar panel works.

## Acceptance Criteria

- [ ] "Ondertitel" appears in the post sidebar, not the Meta Boxes drawer
- [ ] Typing a subtitle and publishing stores it; reopening shows the value
- [ ] Sanitisation still holds — `<script>alert(1)</script>` comes back escaped
- [ ] No build step added; no new npm dependency
- [ ] Existing posts with a subtitle set via the old meta box still render it
