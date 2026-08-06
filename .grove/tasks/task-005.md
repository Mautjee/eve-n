---
id: task-005
title: "Contact page: form and contact blocks"
status: todo
priority: medium
labels: [theme, template, forms]
created: 2026-08-07
pr: ""
---

## Description

Create `wp-content/themes/eve-n/page-contact.php`, ported from `contact.html`.

Hero with "CONTACT", then a two-column form — three pill inputs stacked left
(`Naam`, `Email`, `Onderwerp`) and a tall `Text` textarea right, with a centred
teal `SEND` pill below. Then two contact blocks: name, email, phone, portrait.

Contact details, verbatim from `reference/catalog-contact.md`:

- Thomas Vilain — T.vilain@eve-n.nl — +31 6 81 44 02 25
- Eveline Hinfelaar — E.hinfelaar@eve-n.nl — +31 6 24 65 68 33

**The form must actually send.** Handle the POST in the theme via
`admin_post_` / `admin_post_nopriv_`, with: a nonce, server-side validation of
all four fields, `is_email()` on the address, honeypot or equivalent spam
guard, `wp_mail()` to both addresses, and a redirect back with a success or
error state. Never echo submitted input back unescaped.

Do not install a form plugin for this — four fields do not justify one.

## Acceptance Criteria

- [ ] Form renders per `reference/contact.png`; stacks on mobile
      (`phone-contact.png`)
- [ ] Submitting a valid form sends mail and shows a Dutch success message
- [ ] Invalid input redisplays the form with Dutch errors and preserves what
      was typed
- [ ] Nonce verified; missing or bad nonce is rejected
- [ ] Submitted values are escaped on output; no XSS via any field
- [ ] Inputs have real `<label>`s (visually hidden is fine) — placeholders
      alone are not accessible
