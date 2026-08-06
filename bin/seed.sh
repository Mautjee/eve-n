#!/usr/bin/env bash
#
# Seed eve-n.nl: run bin/seed.php inside WordPress via `wp eval-file -`,
# piped over stdin so nothing has to be copied onto the target first.
#
# Idempotent — safe to run repeatedly against local, staging or production.
# A second run logs "already exists" / "already set" for everything and
# creates no duplicates.
#
# Connection settings for --staging/--production come from .env in the repo
# root (gitignored). See .env.example for the required keys.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly REPO_ROOT
readonly SEED_PHP="${REPO_ROOT}/bin/seed.php"
readonly ENV_FILE="${REPO_ROOT}/.env"

TARGET='local'

# ============================================================================
# Output
# ============================================================================

if [ -t 1 ]; then
	C_BOLD=$'\033[1m'; C_RED=$'\033[31m'; C_GREEN=$'\033[32m'
	C_YELLOW=$'\033[33m'; C_OFF=$'\033[0m'
else
	C_BOLD=''; C_RED=''; C_GREEN=''; C_YELLOW=''; C_OFF=''
fi

info()  { printf '%s==>%s %s\n' "$C_BOLD" "$C_OFF" "$*"; }
warn()  { printf '%s==>%s %s\n' "$C_YELLOW" "$C_OFF" "$*" >&2; }
ok()    { printf '%s==>%s %s\n' "$C_GREEN" "$C_OFF" "$*"; }
die()   { printf '%serror:%s %s\n' "$C_RED" "$C_OFF" "$*" >&2; exit 1; }

usage() {
	cat <<EOF
Usage: bin/seed.sh [options]

Populates an empty WordPress install with the six eve-n.nl pages, the
Hoofdmenu, site settings, and one example blog post, then removes the
WordPress defaults. Idempotent: safe to run again at any time.

Options:
  --local        Seed the local wp-env install (default). Uses the wp-env
                 cli container mounted from this checkout via 'docker exec'
                 — the 'npx wp-env run cli' wrapper silently swallows writes.
  --staging      Seed https://staging2.eve-n.nl over SSH.
  --production   Seed the live site over SSH. Requires an interactive
                 confirmation.
  -h, --help     Show this help.
EOF
}

# ============================================================================
# Arguments
# ============================================================================

while [ $# -gt 0 ]; do
	case "$1" in
		--local)      TARGET='local' ;;
		--staging)    TARGET='staging' ;;
		--production) TARGET='production' ;;
		-h|--help)    usage; exit 0 ;;
		*)            usage >&2; die "unknown option: $1" ;;
	esac
	shift
done

[ -f "$SEED_PHP" ] || die "$SEED_PHP not found"

# ============================================================================
# Local: find the wp-env cli container mounted from this checkout
# ============================================================================

find_local_container() {
	local name mounts
	for name in $(docker ps --format '{{.Names}}' | grep -E -- '-cli-1$' || true); do
		case "$name" in
			*-tests-cli-1) continue ;;
		esac
		mounts=$(docker inspect "$name" --format '{{range .Mounts}}{{.Source}}{{"\n"}}{{end}}' 2>/dev/null) || continue
		if printf '%s\n' "$mounts" | grep -qF "$REPO_ROOT"; then
			printf '%s\n' "$name"
			return 0
		fi
	done
	return 1
}

# ============================================================================
# Run
# ============================================================================

if [ "$TARGET" = 'local' ]; then
	CONTAINER="$(find_local_container)" || die "no running wp-env cli container mounted from ${REPO_ROOT} — run 'npx wp-env start' first"

	info "Target:    ${C_BOLD}local${C_OFF}"
	info "Container: ${CONTAINER}"

	docker exec -i "$CONTAINER" wp eval-file - < "$SEED_PHP"
	ok "Seeded local."
	exit 0
fi

[ -f "$ENV_FILE" ] || die "$ENV_FILE not found. Copy .env.example to .env and fill it in."

set -a
# shellcheck source=/dev/null
. "$ENV_FILE"
set +a

for var in SG_HOST SG_PORT SG_USER SG_KEY; do
	[ -n "${!var:-}" ] || die "$var is not set in $ENV_FILE"
done

if [ "$TARGET" = 'production' ]; then
	PATH_VAR='SG_THEMES_PATH'
else
	PATH_VAR='SG_STAGING_THEMES_PATH'
fi
REMOTE_THEMES="${!PATH_VAR:-}"
[ -n "$REMOTE_THEMES" ] || die "$PATH_VAR is not set in $ENV_FILE"

# The WordPress root is the themes path minus wp-content/themes.
case "$REMOTE_THEMES" in
	*/wp-content/themes) WP_PATH="${REMOTE_THEMES%/wp-content/themes}" ;;
	*) die "$PATH_VAR must end in /wp-content/themes, got: $REMOTE_THEMES" ;;
esac

SSH_KEY="${SG_KEY/#\~/$HOME}"
[ -f "$SSH_KEY" ] || die "SSH key not found: $SSH_KEY"

SSH_CMD=(ssh -i "$SSH_KEY" -p "$SG_PORT" -o BatchMode=yes -o ConnectTimeout=20)
readonly REMOTE="${SG_USER}@${SG_HOST}"

info "Target: ${C_BOLD}${TARGET}${C_OFF}"
info "Server: ${REMOTE}:${SG_PORT}"
info "Path:   ${WP_PATH}"

if [ "$TARGET" = 'production' ]; then
	[ -t 0 ] || die "production seeding needs an interactive terminal for confirmation"
	warn "This writes to the LIVE site at ${WP_PATH}."
	printf 'Type %sseed production%s to continue: ' "$C_BOLD" "$C_OFF"
	read -r reply
	[ "$reply" = 'seed production' ] || die "aborted"
fi

"${SSH_CMD[@]}" "$REMOTE" "wp --path=${WP_PATH} eval-file -" < "$SEED_PHP"
ok "Seeded ${TARGET}."
