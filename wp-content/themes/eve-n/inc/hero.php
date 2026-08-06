<?php
/**
 * Page hero helpers.
 *
 * The subpage heroes in the XD design are all the same component: a full-bleed
 * photograph with the page title overlaid, left-aligned and vertically centred.
 * Both halves are editable — the title is the page title and the photograph is
 * the featured image — so no template names a copy or an image file itself.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print the hero photograph for the current post: its featured image when one
 * is set, otherwise the export shipped with the theme.
 *
 * The photograph is a real `<img>` rather than a CSS background so that
 * WordPress can serve `srcset` and `sizes`. It is never lazy-loaded — it is the
 * largest contentful paint on every subpage.
 */
function even_hero_image() {
	if ( has_post_thumbnail() ) {
		echo wp_get_attachment_image(
			get_post_thumbnail_id(),
			'full',
			false,
			array(
				'class'         => 'hero__photo',
				'sizes'         => '100vw',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decoding'      => 'sync',
			)
		);
		return;
	}

	// Cropped from the XD export `assets/res-b5ce20a2.webp`.
	$fallback = 'assets/img/hero-page.webp';

	if ( ! file_exists( get_theme_file_path( $fallback ) ) ) {
		return;
	}

	printf(
		'<img class="hero__photo" src="%1$s" width="1920" height="600" alt="%2$s" fetchpriority="high" decoding="sync">',
		esc_url( get_theme_file_uri( $fallback ) ),
		esc_attr__( 'Bouwteam in gesprek rond een tekening op een bouwlocatie, gezien van boven', 'eve-n' )
	);
}

/**
 * Print a full-bleed page hero with the page title overlaid.
 */
function even_page_hero() {
	?>
	<section class="hero hero--page hero--photo">
		<div class="hero__media">
			<?php even_hero_image(); ?>
		</div>
		<div class="hero-content">
			<h1><?php the_title(); ?></h1>
		</div>
	</section>
	<?php
}
