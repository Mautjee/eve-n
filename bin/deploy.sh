#!/usr/bin/env bash
#
# Deploy the eve-n theme to SiteGround.
#
# Ships one directory: wp-content/themes/eve-n/. It never touches uploads,
# plugins or WordPress core. Staging is the default target; production needs
# --production and a typed confirmation.
#
# Connection settings come from .env in the repo root (gitignored).
# See .env.example for the required keys.

set -euo pipefail

readonly THEME_SLUG='eve-n'
readonly FALLBACK_THEME='astra'

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly REPO_ROOT
readonly LOCAL_THEME="${REPO_ROOT}/wp-content/themes/${THEME_SLUG}"
readonly ENV_FILE="${REPO_ROOT}/.env"

TARGET='staging'
DRY_RUN='no'
ACTIVATE='no'

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
Usage: bin/deploy.sh [options]

Rsyncs wp-content/themes/${THEME_SLUG}/ to the SiteGround server and purges the
SG Optimizer cache. Nothing else is transferred.

Options:
  --production   Deploy to production instead of staging. Requires an
                 interactive confirmation.
  --dry-run      Show exactly what would transfer. Changes nothing, on either
                 the filesystem or the site.
  --activate     Run 'wp theme activate ${THEME_SLUG}' on the remote after upload.
                 Off by default.
  -h, --help     Show this help.

Rollback (staging shown; swap the path for production):
  ssh -i \$SG_KEY -p \$SG_PORT \$SG_USER@\$SG_HOST \\
    'cd ~/www/staging2.eve-n.nl/public_html && wp theme activate ${FALLBACK_THEME} && wp sg purge'
EOF
}

# ============================================================================
# Arguments
# ============================================================================

while [ $# -gt 0 ]; do
	case "$1" in
		--production) TARGET='production' ;;
		--dry-run)    DRY_RUN='yes' ;;
		--activate)   ACTIVATE='yes' ;;
		-h|--help)    usage; exit 0 ;;
		*)            usage >&2; die "unknown option: $1" ;;
	esac
	shift
done

# ============================================================================
# Configuration
# ============================================================================

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

# The WordPress root is the themes path minus wp-content/themes. wp-cli needs
# it because the script never cd's into a shell on the server.
case "$REMOTE_THEMES" in
	*/wp-content/themes) WP_PATH="${REMOTE_THEMES%/wp-content/themes}" ;;
	*) die "$PATH_VAR must end in /wp-content/themes, got: $REMOTE_THEMES" ;;
esac

# Expand a leading ~ ourselves; the value comes from a file, not the shell.
SSH_KEY="${SG_KEY/#\~/$HOME}"
[ -f "$SSH_KEY" ] || die "SSH key not found: $SSH_KEY"

[ -d "$LOCAL_THEME" ] || die "local theme not found: $LOCAL_THEME"
[ -f "${LOCAL_THEME}/style.css" ] || die "${LOCAL_THEME}/style.css is missing — that is not a theme"

SSH_CMD=(ssh -i "$SSH_KEY" -p "$SG_PORT" -o BatchMode=yes -o ConnectTimeout=20)
# rsync -e splits on whitespace and understands no quoting, so the key path
# must not contain spaces.
SSH_RSH="${SSH_CMD[*]}"
readonly REMOTE="${SG_USER}@${SG_HOST}"

case "$SSH_KEY" in
	*[[:space:]]*) die "SSH key path must not contain spaces (rsync -e cannot quote it): $SSH_KEY" ;;
esac

remote() { "${SSH_CMD[@]}" "$REMOTE" "$@"; }

# ============================================================================
# Confirmation
# ============================================================================

info "Target:  ${C_BOLD}${TARGET}${C_OFF}"
info "Server:  ${REMOTE}:${SG_PORT}"
info "Path:    ${REMOTE_THEMES}/${THEME_SLUG}/"
info "Source:  wp-content/themes/${THEME_SLUG}/"
[ "$DRY_RUN" = 'yes' ] && warn "DRY RUN — nothing will be written or purged."

if [ "$TARGET" = 'production' ] && [ "$DRY_RUN" = 'no' ]; then
	[ -t 0 ] || die "production deploys need an interactive terminal for confirmation"
	warn "This writes to the LIVE site at ${WP_PATH}."
	printf 'Type %sdeploy production%s to continue: ' "$C_BOLD" "$C_OFF"
	read -r reply
	[ "$reply" = 'deploy production' ] || die "aborted"
fi

# ============================================================================
# Transfer
# ============================================================================

# Trailing slashes on both sides keep --delete scoped strictly inside the theme
# directory. Sibling themes, uploads, plugins and core are never in scope.
rsync_args=(
	--recursive
	--links
	--times
	--perms
	--compress
	--delete
	--human-readable
	--itemize-changes
	--exclude='.git'
	--exclude='.git*'
	--exclude='node_modules'
	--exclude='.wp-env*'
	--exclude='*.map'
	--exclude='.DS_Store'
	--exclude='._*'
	--exclude='*.swp'
	-e "$SSH_RSH"
)

# macOS ships openrsync, which rejects --chmod. Where it is available (GNU
# rsync) force sane web modes; where it is not, the local checkout's modes
# ship as-is, which a normal git clone gets right anyway.
if rsync --chmod=D755,F644 --list-only "$LOCAL_THEME/style.css" >/dev/null 2>&1; then
	# shellcheck disable=SC2054  # the comma belongs to --chmod's argument
	rsync_args+=(--chmod=D755,F644)
else
	warn "rsync has no --chmod; file modes come from the local checkout."
fi

[ "$DRY_RUN" = 'yes' ] && rsync_args+=(--dry-run)

info "Transferring…"
rsync "${rsync_args[@]}" \
	"${LOCAL_THEME}/" \
	"${REMOTE}:${REMOTE_THEMES}/${THEME_SLUG}/"

if [ "$DRY_RUN" = 'yes' ]; then
	echo
	[ "$ACTIVATE" = 'yes' ] && info "Would run: wp theme activate ${THEME_SLUG}"
	info "Would run: wp sg purge"
	ok "Dry run complete. Nothing changed."
	exit 0
fi

# ============================================================================
# Activate and purge
# ============================================================================

if [ "$ACTIVATE" = 'yes' ]; then
	info "Activating ${THEME_SLUG}…"
	remote "wp --path=${WP_PATH} theme activate ${THEME_SLUG}"
fi

# SG Optimizer serves cached HTML and concatenated assets. Without this the
# upload is invisible in a browser.
info "Purging SG Optimizer cache…"
remote "wp --path=${WP_PATH} sg purge"

info "Active theme: $(remote "wp --path=${WP_PATH} theme list --status=active --field=name")"
ok "Deployed to ${TARGET}."
