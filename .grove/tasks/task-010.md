---
id: task-010
title: "Deploy to SiteGround staging"
status: blocked
priority: medium
labels: [deploy, infra]
created: 2026-08-07
pr: ""
---

## Description

**SSH is available** — `ssh.eve-n.nl`, port 18765, user in `.env`. Deploy with
`rsync -avz --delete` over SSH; no FTP needed.

**BLOCKED** on one thing: SiteGround accepts **publickey auth only** (password
auth is refused outright), and no key on this machine is registered with it.
The operator must add a public key in Site Tools > Devs > SSH Keys Manager
before an agent can connect. Connection settings are in `.env`; copy
`.env.example` if it is missing.

Once connected, confirm the remote themes path and record it in `.env`:

```
ssh -p $SG_PORT $SG_USER@$SG_HOST 'ls -d ~/www/*/public_html/wp-content/themes'
```

Then: write `bin/deploy.sh` that pushes **only** `wp-content/themes/eve-n/` to
the SiteGround **staging** site. It must never touch `wp-content/uploads`,
plugins, or WordPress core, and must never target production without an
explicit flag and a confirmation prompt.

Also document the cutover: the live site currently runs a different design on
the Astra theme under the "Eve(N)|n=3" branding. Switching themes changes the
whole site at once, so the runbook needs a rollback step.

Confirm before building: does anything on the current live site need to
survive — the GDQ copy, the three client logos, the existing portrait? None of
it appears in the XD design.

## Acceptance Criteria

- [ ] `bin/deploy.sh` pushes the theme folder to staging and nothing else
- [ ] Credentials read from `.env`; nothing secret is committed
- [ ] Dry-run mode showing what would transfer
- [ ] Production requires an explicit flag plus confirmation
- [ ] Rollback documented and tested on staging
- [ ] Staging URL verified: all seven pages render, blog publishes, contact
      form sends
