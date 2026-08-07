<?php
/**
 * Blog: the "Ondertitel" post meta field, and the blog index query.
 *
 * The design gives every post a subtitle under the title — on the index card and
 * again under the title in the post hero. It is not the excerpt: the excerpt is a
 * summary of the body, the subtitle is part of the headline. So it gets its own
 * meta field, editable in the post editor.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta key holding a post's "Ondertitel".
 */
const EVEN_SUBTITLE_META_KEY = 'even_subtitle';

/**
 * Cards per page on the blog index: two rows of two, as in the XD design.
 *
 * The design shows exactly four placeholder cards; the client will write more
 * than four, hence the pagination controls in `home.php`.
 *
 * @return int
 */
function even_blog_posts_per_page() {
	return max( 1, (int) apply_filters( 'even_blog_posts_per_page', 4 ) );
}

/**
 * Clean a submitted subtitle. Plain text only — it is rendered inside a heading
 * block, never as markup.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function even_sanitize_subtitle( $value ) {
	return sanitize_text_field( (string) $value );
}

/**
 * Whether the current user may write the subtitle. Used as the meta auth
 * callback, which guards REST writes.
 *
 * @param bool   $allowed Current decision.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post being edited.
 * @return bool
 */
function even_subtitle_auth( $allowed, $meta_key, $post_id ) {
	unset( $allowed, $meta_key );
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Register the subtitle as post meta so it is available to REST, the block
 * editor and WP-CLI, not only to the meta box below.
 */
function even_register_subtitle_meta() {
	register_post_meta(
		'post',
		EVEN_SUBTITLE_META_KEY,
		array(
			'type'              => 'string',
			'description'       => __( 'Ondertitel', 'eve-n' ),
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'even_sanitize_subtitle',
			'auth_callback'     => 'even_subtitle_auth',
		)
	);
}
add_action( 'init', 'even_register_subtitle_meta' );

/**
 * A post's subtitle, or an empty string when it has none.
 *
 * @param int|WP_Post|null $post Post, defaults to the current one.
 * @return string
 */
function even_get_subtitle( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	return trim( (string) get_post_meta( $post->ID, EVEN_SUBTITLE_META_KEY, true ) );
}

/**
 * The "Ondertitel" panel in the block editor sidebar.
 *
 * A native `PluginDocumentSettingPanel`, not a classic meta box — that box
 * used to land in the collapsed "Meta Boxes" drawer at the bottom of the
 * editor, where the client had to know to expand it to find the field.
 * The panel reads and writes through the `register_post_meta()` REST field
 * above, so no separate save handler is needed here.
 */
function even_enqueue_subtitle_panel() {
	$screen = get_current_screen();

	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	$js = get_theme_file_path( 'assets/js/subtitle-panel.js' );

	wp_enqueue_script(
		'even-subtitle-panel',
		get_theme_file_uri( 'assets/js/subtitle-panel.js' ),
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ),
		file_exists( $js ) ? filemtime( $js ) : EVEN_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'even_enqueue_subtitle_panel' );

/**
 * Show four cards per page on the blog index, matching the design's grid.
 *
 * @param WP_Query $query Query being prepared.
 */
function even_blog_index_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() ) {
		$query->set( 'posts_per_page', even_blog_posts_per_page() );
	}
}
add_action( 'pre_get_posts', 'even_blog_index_query' );
