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
 * The "Ondertitel" box in the post editor.
 */
function even_add_subtitle_meta_box() {
	add_meta_box(
		'even-subtitle',
		__( 'Ondertitel', 'eve-n' ),
		'even_render_subtitle_meta_box',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_post', 'even_add_subtitle_meta_box' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Post being edited.
 */
function even_render_subtitle_meta_box( $post ) {
	wp_nonce_field( 'even_save_subtitle', 'even_subtitle_nonce' );
	?>
	<p>
		<label for="even-subtitle-field">
			<?php esc_html_e( 'Korte ondertitel, onder de titel op de blogkaart en in de header van het bericht.', 'eve-n' ); ?>
		</label>
	</p>
	<input
		type="text"
		id="even-subtitle-field"
		name="even_subtitle"
		class="widefat"
		maxlength="160"
		value="<?php echo esc_attr( even_get_subtitle( $post ) ); ?>"
	>
	<?php
}

/**
 * Persist the subtitle from the editor form.
 *
 * Block-editor saves go through REST, where `register_post_meta()` above does the
 * sanitising and the auth check; those requests carry no nonce, so bail early
 * rather than wiping the value.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function even_save_subtitle( $post_id, $post ) {
	if ( ! isset( $_POST['even_subtitle_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['even_subtitle_nonce'] ) ), 'even_save_subtitle' ) ) {
		return;
	}

	if ( wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$subtitle = isset( $_POST['even_subtitle'] )
		? even_sanitize_subtitle( wp_unslash( $_POST['even_subtitle'] ) )
		: '';

	if ( '' === $subtitle ) {
		delete_post_meta( $post_id, EVEN_SUBTITLE_META_KEY );
		return;
	}

	update_post_meta( $post_id, EVEN_SUBTITLE_META_KEY, $subtitle );
}
add_action( 'save_post_post', 'even_save_subtitle', 10, 2 );

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
