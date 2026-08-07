<?php
/**
 * Seed eve-n.nl: the six pages, the Hoofdmenu, site settings, the XD export
 * photos (side-loaded into the media library), and a starter blog post, then
 * remove the WordPress defaults.
 *
 * Idempotent by construction: every step checks for what it would create
 * before creating it, so a second run finds everything already in place and
 * changes nothing. Run through bin/seed.sh, never directly — see that file
 * for how it reaches local (docker), staging and production.
 *
 * Page copy is transcribed verbatim from reference/catalog-*.md. The Home and
 * Contact pages stay empty here on purpose: both are fully hard-coded in
 * their templates (front-page.php; page-contact.php once task-005 lands) per
 * the content architecture table in AGENTS.md, so there is nothing to seed
 * into post_content for them.
 *
 * Page templates from tasks 001-006 are resolved by WordPress's normal
 * page-{slug}.php template hierarchy — this script never assigns a template.
 * A page whose template hasn't been merged yet just falls back to index.php
 * until it lands; nothing here fails because of it.
 *
 * @package eve-n
 */

defined( 'WP_CLI' ) || exit( "This script must be run through wp-cli — see bin/seed.sh.\n" );

// Trusted, developer-authored HTML, not user input — skip kses so entities
// and markup are stored exactly as written below.
kses_remove_filters();

// media_handle_sideload() and wp_generate_attachment_metadata() live here;
// eval-file's bare WP-CLI bootstrap doesn't load the wp-admin includes.
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * The site's first administrator, used as the author of everything this
 * script creates.
 *
 * @return int
 */
function even_seed_default_author() {
	$admins = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'order'   => 'ASC',
		)
	);

	return $admins ? (int) $admins[0]->ID : 1;
}

/**
 * Find a post by slug regardless of post type, including trashed/draft ones
 * — used to locate the WordPress defaults before deleting them.
 *
 * @param string $post_type Post type.
 * @param string $slug      post_name to match.
 * @return WP_Post|null
 */
function even_seed_find_post( $post_type, $slug ) {
	$posts = get_posts(
		array(
			'post_type'     => $post_type,
			'name'          => $slug,
			'post_status'   => 'any',
			'numberposts'   => 1,
			'no_found_rows' => true,
		)
	);

	return $posts ? $posts[0] : null;
}

/**
 * Create a page by slug if it doesn't already exist. Never touches a page
 * that's already there — an author's edits after the first seed must
 * survive every later run of this script.
 *
 * @param string $slug    Page slug.
 * @param string $title   Page title.
 * @param string $content Page content, already entity-encoded HTML.
 * @return int Post ID, existing or newly created.
 */
function even_seed_page( $slug, $title, $content ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $existing ) {
		WP_CLI::log( "Page '{$slug}' already exists (#{$existing->ID}), leaving it alone." );
		return (int) $existing->ID;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_author'  => even_seed_default_author(),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( "Failed to create page '{$slug}': " . $post_id->get_error_message() );
	}

	WP_CLI::log( "Created page '{$slug}' (#{$post_id})." );

	return (int) $post_id;
}

/**
 * Set an option only when its value differs — keeps the log honest about
 * what a run actually changed.
 *
 * @param string $option Option name.
 * @param mixed  $value  Desired value.
 */
function even_seed_option( $option, $value ) {
	$current = get_option( $option );

	if ( (string) $current === (string) $value ) {
		WP_CLI::log( "Option '{$option}' already '{$value}'." );
		return;
	}

	update_option( $option, $value );
	WP_CLI::log( "Option '{$option}' set to '{$value}'." );
}

/**
 * Activate the eve-n theme if it isn't already active. Nav menu locations
 * are per-theme, so this has to happen before the menu is assigned.
 */
function even_seed_activate_theme() {
	if ( 'eve-n' === get_option( 'template' ) ) {
		WP_CLI::log( "Theme 'eve-n' already active." );
		return;
	}

	$theme = wp_get_theme( 'eve-n' );

	if ( ! $theme->exists() ) {
		WP_CLI::warning( "Theme 'eve-n' not found in wp-content/themes — skipping activation." );
		return;
	}

	switch_theme( 'eve-n' );
	WP_CLI::log( "Activated theme 'eve-n'." );

	// switch_theme() doesn't load eve-n's functions.php into this process —
	// whichever theme was active when PHP booted already had its own
	// functions.php required, and switching mid-request doesn't undo that or
	// load the new one. Without this, add_image_size() never runs on a fresh
	// install, and every image imported below this point silently gets no
	// even-hero/even-card/even-portrait/even-contact size — see the images
	// gotcha in AGENTS.md for why that matters.
	$functions_file = get_theme_file_path( 'functions.php' );

	if ( file_exists( $functions_file ) ) {
		require_once $functions_file;
	}

	// Not even_setup() — that also calls add_theme_support(), which WordPress
	// logs a "called incorrectly" notice for outside its normal
	// after_setup_theme timing. Registering the image sizes is all this
	// script needs.
	if ( function_exists( 'even_register_image_sizes' ) ) {
		even_register_image_sizes();
	}
}

/**
 * Install and switch to the given locale. Falls back to setting WPLANG
 * directly if the language pack can't be downloaded (e.g. no network on a
 * sandboxed environment) — the option is still correct even without the
 * translation files.
 *
 * @param string $locale e.g. 'nl_NL'.
 */
function even_seed_locale( $locale ) {
	// get_option(), not get_locale() — get_locale() memoizes in a global set
	// during bootstrap and won't reflect a change made later in this same
	// process, which would make the check below always look like it failed.
	if ( get_option( 'WPLANG' ) === $locale ) {
		WP_CLI::log( "Locale already '{$locale}'." );
		return;
	}

	WP_CLI::runcommand( "language core install {$locale}", array( 'exit_error' => false ) );
	WP_CLI::runcommand( "site switch-language {$locale}", array( 'exit_error' => false ) );

	// Belt-and-braces: if the language pack couldn't be downloaded (e.g. no
	// network), the site still reports the right locale without it.
	if ( get_option( 'WPLANG' ) !== $locale ) {
		update_option( 'WPLANG', $locale );
	}

	WP_CLI::log( "Locale set to '{$locale}'." );
}

/**
 * Delete a default WordPress post (and its comments) by post type and slug,
 * if it's still there.
 *
 * @param string $post_type Post type.
 * @param string $slug      post_name to match.
 * @param string $label     Human label for logging.
 */
function even_seed_delete_default( $post_type, $slug, $label ) {
	$post = even_seed_find_post( $post_type, $slug );

	if ( ! $post ) {
		WP_CLI::log( "Default {$label} already gone." );
		return;
	}

	$comment_ids = get_comments(
		array(
			'post_id' => $post->ID,
			'fields'  => 'ids',
		)
	);

	foreach ( $comment_ids as $comment_id ) {
		wp_delete_comment( $comment_id, true );
	}

	wp_delete_post( $post->ID, true );
	WP_CLI::log( "Deleted default {$label} (#{$post->ID})." );
}

/**
 * Create the "Hoofdmenu" menu with the given items (in order) if it doesn't
 * exist yet, and assign it to the 'primary' location.
 *
 * @param array<int, array{title: string, page_id: int}> $items Menu items, in display order.
 */
function even_seed_menu( array $items ) {
	$menu_name = 'Hoofdmenu';
	$menu      = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );

		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::error( "Failed to create menu '{$menu_name}': " . $menu_id->get_error_message() );
		}

		WP_CLI::log( "Created menu '{$menu_name}' (#{$menu_id})." );
	} else {
		$menu_id = (int) $menu->term_id;
		WP_CLI::log( "Menu '{$menu_name}' already exists (#{$menu_id})." );
	}

	$existing_items      = wp_get_nav_menu_items( $menu_id );
	$existing_object_ids = $existing_items ? array_map( 'strval', wp_list_pluck( $existing_items, 'object_id' ) ) : array();

	$position = 0;

	foreach ( $items as $item ) {
		++$position;

		if ( in_array( (string) $item['page_id'], $existing_object_ids, true ) ) {
			WP_CLI::log( "Menu item for page #{$item['page_id']} already present, skipping." );
			continue;
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $item['title'],
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $item['page_id'],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $position,
			)
		);

		WP_CLI::log( "Added menu item '{$item['title']}' -> page #{$item['page_id']}." );
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	if ( isset( $locations['primary'] ) && (int) $locations['primary'] === (int) $menu_id ) {
		WP_CLI::log( "'primary' location already assigned to '{$menu_name}'." );
		return;
	}

	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
	WP_CLI::log( "Assigned '{$menu_name}' to the 'primary' location." );
}

/**
 * Create the one example blog post if it doesn't exist yet, with its
 * "Ondertitel" subtitle meta set.
 */
function even_seed_example_post() {
	$slug     = 'welkom-bij-de-eve-n-blog';
	$existing = even_seed_find_post( 'post', $slug );

	if ( $existing ) {
		WP_CLI::log( "Example post already exists (#{$existing->ID})." );
		return;
	}

	$content = <<<'HTML'
<p>Fijn dat je hier bent! Op deze plek delen we regelmatig inzichten en ervaringen over teamontwikkeling en samenwerking binnen infrastructuurprojecten &ndash; van de dagelijkse praktijk op de bouwplaats tot onderzoek naar wat samenwerking echt effectief maakt.</p>
<p>Binnenkort volgen hier de eerste artikelen. Heb je een vraag of een onderwerp dat je graag behandeld ziet? Neem gerust contact met ons op.</p>
HTML;

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => 'Welkom bij de Eve-n blog',
			'post_name'    => $slug,
			'post_content' => $content,
			'post_author'  => even_seed_default_author(),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( 'Failed to create the example post: ' . $post_id->get_error_message() );
	}

	update_post_meta( $post_id, 'even_subtitle', 'Kennis delen over samenwerken in de infrastructuur' );
	WP_CLI::log( "Created example post (#{$post_id})." );

	return (int) $post_id;
}

/**
 * Resize (if needed) and re-encode a source image as a properly lossy WebP,
 * ready to side-load into the media library.
 *
 * Two problems, one fix. The XD exports are up to 4096px wide, and several
 * — including the home hero — are *lossless* WebP: a photo encoded lossless
 * comes out several times larger than a lossy encode at the same
 * dimensions. Simply side-loading and letting WordPress generate sizes
 * does not fix the second problem, because WP_Image_Editor_GD::
 * set_quality() deliberately preserves losslessness on save (see
 * https://php.watch/versions/8.1/gd-webp-lossless). Re-encoding through GD
 * directly, bypassing that editor, sidesteps it.
 *
 * @param string $path    Absolute path to the source file.
 * @param int    $max_dim Longest edge, in pixels. Nothing in this theme
 *                         displays wider than 2560.
 * @param int    $quality WebP quality, 1-100.
 * @return string|false Path to a temporary re-encoded file, or false on failure.
 */
function even_seed_prepare_image( $path, $max_dim = 2560, $quality = 82 ) {
	if ( ! function_exists( 'imagecreatefromwebp' ) || ! function_exists( 'imagewebp' ) ) {
		return false;
	}

	$image = @imagecreatefromwebp( $path );

	if ( ! $image ) {
		return false;
	}

	$width  = imagesx( $image );
	$height = imagesy( $image );

	if ( $width > $max_dim || $height > $max_dim ) {
		$scale      = min( $max_dim / $width, $max_dim / $height );
		$new_width  = max( 1, (int) round( $width * $scale ) );
		$new_height = max( 1, (int) round( $height * $scale ) );

		// imagescale() with IMG_BICUBIC returns false on SiteGround's GD 2.3.3
		// while the default mode and imagecopyresampled() both work there. Only
		// images over $max_dim reach this branch, so the failure was invisible
		// locally and silently dropped three home-page photos on the server.
		// Try the best filter first, then fall back rather than giving up.
		$resized = @imagescale( $image, $new_width, $new_height, IMG_BICUBIC );

		if ( ! $resized ) {
			$resized = @imagescale( $image, $new_width, $new_height );
		}

		if ( ! $resized ) {
			$resized = imagecreatetruecolor( $new_width, $new_height );

			if ( $resized && ! imagecopyresampled( $resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height ) ) {
				imagedestroy( $resized );
				$resized = false;
			}
		}

		imagedestroy( $image );

		if ( ! $resized ) {
			return false;
		}

		$image = $resized;
	}

	$tmp = wp_tempnam( wp_basename( $path ) );
	$ok  = imagewebp( $image, $tmp, $quality );
	imagedestroy( $image );

	return $ok ? $tmp : false;
}

/**
 * Side-load one seed image into the media library, if it isn't there yet.
 *
 * Idempotent via the `_even_seed_key` attachment meta: a second run finds
 * the existing attachment by key and returns its ID rather than importing
 * again. Records the ID in the `even_image_<key>` option so templates can
 * resolve it — see even_seeded_image_id() / even_seeded_image() in
 * inc/media.php.
 *
 * @param string $key   Stable key, e.g. 'home-hero'.
 * @param string $file  File name inside the active theme's assets/img/seed/.
 * @param string $title Media library title.
 * @param string $alt   Alt text, stored on the attachment.
 * @return int Attachment ID, or 0 if the source file is missing or import failed.
 */
function even_seed_image( $key, $file, $title, $alt ) {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'any',
			'numberposts' => 1,
			'meta_key'    => '_even_seed_key',
			'meta_value'  => $key,
		)
	);

	if ( $existing ) {
		$attachment_id = (int) $existing[0]->ID;
		WP_CLI::log( "Image '{$key}' already imported (#{$attachment_id})." );
		update_option( 'even_image_' . $key, $attachment_id );
		return $attachment_id;
	}

	$source = get_theme_file_path( 'assets/img/seed/' . $file );

	if ( ! file_exists( $source ) ) {
		WP_CLI::warning( "Seed image '{$file}' not found for '{$key}' — skipping." );
		return 0;
	}

	$prepared = even_seed_prepare_image( $source );

	if ( ! $prepared ) {
		WP_CLI::warning( "Could not process '{$file}' for '{$key}' — skipping." );
		return 0;
	}

	$attachment_id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $prepared,
		),
		0,
		$title
	);

	if ( is_wp_error( $attachment_id ) ) {
		if ( file_exists( $prepared ) ) {
			wp_delete_file( $prepared );
		}
		WP_CLI::warning( "Failed to import '{$file}' for '{$key}': " . $attachment_id->get_error_message() );
		return 0;
	}

	wp_update_post(
		array(
			'ID'         => $attachment_id,
			'post_title' => $title,
		)
	);
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $attachment_id, '_even_seed_key', $key );
	update_option( 'even_image_' . $key, $attachment_id );

	WP_CLI::log( "Imported image '{$key}' from '{$file}' (#{$attachment_id})." );

	return (int) $attachment_id;
}

/**
 * Set a post's featured image, if it doesn't already have one — never
 * overwrites a photo an editor picked in wp-admin.
 *
 * @param int $post_id       Page or post ID.
 * @param int $attachment_id Attachment ID.
 */
function even_seed_featured_image( $post_id, $attachment_id ) {
	if ( ! $post_id || ! $attachment_id ) {
		return;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		WP_CLI::log( "Post #{$post_id} already has a featured image." );
		return;
	}

	set_post_thumbnail( $post_id, $attachment_id );
	WP_CLI::log( "Set featured image for post #{$post_id} to attachment #{$attachment_id}." );
}

// ========== THEME ==========

even_seed_activate_theme();

// ========== PAGES ==========
// Copy transcribed verbatim from reference/catalog-*.md. See the file header
// for why Home and Contact have no post_content.

$even_onze_werkwijze_content = <<<'HTML'
<p>De infrastructuur kent grote uitdagingen! Samenwerken binnen deze complexe opgaves is cruciaal om effectief en succesvol te zijn. En dat moet je samen goed organiseren! Succesvolle samenwerking vraagt om m&eacute;&eacute;r dan structuur alleen. Het draait ook om het ontwikkelen van samenwerkingsvaardigheden, het versterken van onderling vertrouwen en het bouwen aan een (h)echt team. Eve-n begeleidt dit proces met oog voor de mens &eacute;n het project. Samen maken we van samenwerking een kracht &ndash; voor projecten die niet alleen effici&euml;nt, maar ook met plezier worden gerealiseerd.</p>
<p>Al meer dan tien jaar ondersteunt Eve-n teams in de infrastructuursector bij het versterken van hun samenwerking. Wij geloven dat effectieve samenwerking begint bij goed georganiseerde (bouw)teams. Dat betekent heldere rollen en verantwoordelijkheden, duidelijke communicatieafspraken, een gedeeld doel en een gezamenlijk gedragen planning. Maar daar stopt het niet.</p>
<p>Of het nu gaat om de start van een aanbesteding of de afronding van een complex infraproject &ndash; Eve-n is er in elke fase. We bieden strategisch advies, training en coaching op &aacute;lle niveaus. Onze rol varieert van procesbegeleider en facilitator tot teamcoach &ndash; altijd met een scherp oog voor wat de samenwerking op dat moment nodig heeft.</p>
<p>Met onze teamgerichte aanpak bouwen we aan professionele, samenwerkingsrelaties in het team &eacute;n zorgen we voor duidelijke afspraken met ruimte voor open, eerlijke gesprekken. Zo versterken we de samenwerking, brengen we mensen in beweging en houden we het gezamenlijke doel &eacute;n ieders belangen scherp in beeld.</p>
HTML;

// The five accordion items are identical placeholder copy in the XD design
// (reference/catalog-reviews.md) — the client's real project write-ups are
// still pending, see the front-page.php precedent for the same placeholder.
$even_project_item = "<h2>Project 1</h2>\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>\n";

$even_projecten_content = str_repeat( $even_project_item, 5 );

$even_over_ons_content = <<<'HTML'
<p><strong>Eve-n is opgericht in 2013 door Eveline Hinfelaar MBA.</strong></p>
<p>Inmiddels bestaat ons team uit twee ervaren experts in teamontwikkeling en samenwerking binnen de infrastructuursector.</p>
<p>Met jarenlange ervaring bij uiteenlopende opdrachtgevers brengen we in kaart wat nodig is om teams sterker te maken en resultaten te behalen. Wij geloven dat effectieve verandering maatwerk vereist &ndash; op elk niveau van de organisatie, van de werkvloer tot het management. Onze aanpak is dan ook altijd zorgvuldig afgestemd op de mensen, de context en de doelen van het project. Waar nodig, en als het de resultaten kan versterken, zoekt Eve-n samenwerking binnen een kring van coaches, adviseurs en experts.</p>
HTML;

$even_blog_content = <<<'HTML'
<p><strong>We geloven sterk in het delen van kennis &ndash; want goede samenwerking begint met begrijpen wat werkt.</strong></p>
<p>Dat doen we door het schrijven van blogs over thema's die we in onze dagelijkse praktijk tegen komen. Daarnaast is Eveline Hinfelaar actief met onderzoek naar effectieve samenwerking in de infrastructuur, binnen haar PhD aan de Universiteit van Twente (afdeling Infrastructuur &amp; Management....)</p>
<p>We delen hier inzichten, ervaringen en onderzoeksresultaten over oa. teamontwikkeling en effectieve samenwerking in projecten en programma's; seriematig - en programmatisch samenwerken, raamcontracten &amp; andere langdurige overeenkomsten; en het onderzoek naar de resultaten die we daarin samen kunnen bereiken.</p>
HTML;

$even_home_id           = even_seed_page( 'home', 'Home', '' );
$even_onze_werkwijze_id = even_seed_page( 'onze-werkwijze', 'Onze Werkwijze', $even_onze_werkwijze_content );
$even_projecten_id      = even_seed_page( 'projecten', 'Projecten', $even_projecten_content );
$even_over_ons_id       = even_seed_page( 'over-ons', 'Over ons', $even_over_ons_content );
$even_blog_id           = even_seed_page( 'blog', 'Blog', $even_blog_content );
$even_contact_id        = even_seed_page( 'contact', 'Contact', '' );

// ========== IMAGES ==========
// Side-loaded from the XD exports (assets/res-*.webp) via their theme copies
// under assets/img/seed/ — see AGENTS.md's images gotcha: the raw exports
// are unoptimised, up to 7MB each, ~16MB total.

$even_image_manifest = array(
	'home-hero'        => array(
		'file'  => 'home-hero.webp',
		'title' => 'Bouwteam op de bouwplaats',
		'alt'   => __( 'Bouwteam in overleg op de bouwplaats, gezien van boven', 'eve-n' ),
	),
	'home-werkwijze'   => array(
		'file'  => 'home-werkwijze.webp',
		'title' => 'Pijl omhoog getekend met krijt',
		'alt'   => __( 'Hand die met krijt een pijl naar boven tekent, symbool voor groei en vooruitgang', 'eve-n' ),
	),
	'home-blog'        => array(
		'file'  => 'home-blog.webp',
		'title' => 'Documenten met gekleurde tabbladen',
		'alt'   => __( 'Vrouw die documenten met gekleurde tabbladen ordent aan haar bureau', 'eve-n' ),
	),
	'home-contact-cta' => array(
		'file'  => 'home-contact-cta.webp',
		'title' => 'Skyline van Rotterdam',
		'alt'   => __( 'Skyline van Rotterdam met de Erasmusbrug in de avondschemering', 'eve-n' ),
	),
	'portrait-eveline' => array(
		'file'  => 'portrait-eveline.webp',
		'title' => 'Eveline Hinfelaar',
		'alt'   => __( 'Portret van Eveline Hinfelaar, oprichter van Eve-n', 'eve-n' ),
	),
	'portrait-thomas'  => array(
		'file'  => 'portrait-thomas.webp',
		'title' => 'Thomas Vilain',
		'alt'   => __( 'Portret van Thomas Vilain, teamlid bij Eve-n', 'eve-n' ),
	),
	'blog-placeholder' => array(
		'file'  => 'blog-placeholder.webp',
		'title' => 'Bouwteam bekijkt bouwtekeningen',
		'alt'   => __( 'Bouwteam bekijkt bouwtekeningen op de bouwplaats, gezien van boven', 'eve-n' ),
	),
	'werkwijze-hero'   => array(
		'file'  => 'werkwijze-hero.webp',
		'title' => 'Team werkt samen tijdens een brainstormsessie',
		'alt'   => __( 'Team dat samen ideeën uitwerkt met plaknotities tijdens een brainstormsessie', 'eve-n' ),
	),
);

$even_image_ids = array();

foreach ( $even_image_manifest as $even_image_key => $even_image ) {
	$even_image_ids[ $even_image_key ] = even_seed_image( $even_image_key, $even_image['file'], $even_image['title'], $even_image['alt'] );
}

// Over Ons and Onze Werkwijze each get their own hero photo. Over Ons
// matches the XD design (over-ons.html's own hero was res-2104ab6e, the same
// photo as the home page's "Onze Werkwijze" section). Onze Werkwijze didn't
// have a distinct hero in the ported static site — every subpage shared the
// generic one — so it gets the otherwise-unused werkwijze-hero export
// instead of sharing that fallback. Every other page without a featured
// image still falls back to it; see even_hero_image() in inc/hero.php.
even_seed_featured_image( $even_over_ons_id, $even_image_ids['home-werkwijze'] );
even_seed_featured_image( $even_onze_werkwijze_id, $even_image_ids['werkwijze-hero'] );

// ========== SITE SETTINGS ==========

even_seed_option( 'blogname', 'EVE-N' );
even_seed_option( 'blogdescription', 'Bouwen aan succes; teamontwikkeling en samenwerking binnen infrastructuurprojecten' );
even_seed_option( 'timezone_string', 'Europe/Amsterdam' );
even_seed_option( 'date_format', 'd-m-Y' );
even_seed_locale( 'nl_NL' );

even_seed_option( 'permalink_structure', '/%postname%/' );

// The 'rewrite flush --hard' *subcommand*, not flush_rewrite_rules() directly
// — inside eval-file, get_home_path() can't reliably resolve where .htaccess
// lives, and the plain PHP call then silently skips writing it, leaving every
// pretty URL 404ing on Apache. Running it as a real WP-CLI command sidesteps
// that; see AGENTS.md's "no docker exec `wp-env run cli`" rewrite gotcha.
WP_CLI::runcommand( 'rewrite flush --hard', array( 'exit_error' => false ) );

// ========== FRONT PAGE / POSTS PAGE ==========

even_seed_option( 'show_on_front', 'page' );
even_seed_option( 'page_on_front', $even_home_id );
even_seed_option( 'page_for_posts', $even_blog_id );

// ========== REMOVE WORDPRESS DEFAULTS ==========

even_seed_delete_default( 'post', 'hello-world', "'Hello world!' post" );
even_seed_delete_default( 'page', 'sample-page', 'sample page' );

// ========== MENU ==========

even_seed_menu(
	array(
		array(
			'title'   => 'Werkwijze',
			'page_id' => $even_onze_werkwijze_id,
		),
		array(
			'title'   => 'Projecten',
			'page_id' => $even_projecten_id,
		),
		array(
			'title'   => 'Over ons',
			'page_id' => $even_over_ons_id,
		),
		array(
			'title'   => 'Blog',
			'page_id' => $even_blog_id,
		),
		array(
			'title'   => 'Contact',
			'page_id' => $even_contact_id,
		),
	)
);

// ========== EXAMPLE BLOG POST ==========

$even_example_post_id = even_seed_example_post();
even_seed_featured_image( $even_example_post_id, $even_image_ids['blog-placeholder'] );

WP_CLI::success( 'eve-n.nl seeded.' );
