<?php
/**
 * Resolve the images bin/seed.php side-loads from the XD exports
 * (assets/res-*.webp) into the media library.
 *
 * Templates never hold an attachment ID directly — IDs differ between local,
 * staging and production. Instead each seeded image gets a stable string key
 * (see the manifest in bin/seed.php), recorded in an option, so a template
 * can ask for e.g. 'home-hero' and get whatever ID that environment's import
 * produced.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attachment ID for a seeded image, by its manifest key.
 *
 * @param string $key e.g. 'home-hero'. See bin/seed.php for the full list.
 * @return int 0 if the image hasn't been seeded (yet).
 */
function even_seeded_image_id( $key ) {
	return (int) get_option( 'even_image_' . $key );
}

/**
 * Print a seeded image as an `<img>`, or nothing if it hasn't been seeded.
 *
 * Alt text comes from the attachment's own library metadata unless `$attr`
 * overrides it — bin/seed.php sets a real description on import, so most
 * call sites don't need to pass one.
 *
 * @param string $key   Manifest key.
 * @param string $size  Registered image size.
 * @param array  $attr  Extra `<img>` attributes.
 */
function even_seeded_image( $key, $size, array $attr = array() ) {
	$id = even_seeded_image_id( $key );

	if ( ! $id ) {
		return;
	}

	echo wp_get_attachment_image( $id, $size, false, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
