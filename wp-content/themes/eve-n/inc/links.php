<?php
/**
 * Internal link resolution.
 *
 * Templates link to the six fixed pages by slug. Resolving through
 * get_page_by_path() rather than hard-coding a pretty URL keeps the links
 * working under plain permalinks and when a page is later renamed or moved
 * under a parent.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * Permalink of a fixed page, by slug.
 *
 * Falls back to the pretty URL so templates still render something sensible
 * before the pages are seeded (see task-007).
 *
 * @param string $slug Page slug, e.g. 'onze-werkwijze'.
 * @return string
 */
function even_page_url( $slug ) {
	$page = get_page_by_path( $slug );

	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * URL of a still-static image bundled with the theme.
 *
 * A single seam for task-008, which replaces these with attachments from the
 * media library so they get srcset and generated sizes.
 *
 * @param string $file File name inside assets/img/.
 * @return string
 */
function even_theme_image_url( $file ) {
	return get_theme_file_uri( 'assets/img/' . $file );
}
