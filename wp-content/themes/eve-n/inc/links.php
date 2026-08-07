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
