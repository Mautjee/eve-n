<?php
/**
 * SEO and metadata: per-page description/canonical/Open Graph/Twitter card,
 * JSON-LD Organization schema, robots.txt.
 *
 * No SEO plugin — seven pages don't justify one. Title tags already come from
 * core's `add_theme_support( 'title-tag' )` in functions.php; the XML sitemap
 * is core's `/wp-sitemap.xml`, enabled by default since WP 5.5.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * Force `lang="nl-NL"` on the front end regardless of the site's WPLANG
 * option, so the attribute is correct even before bin/seed.sh has run.
 *
 * @param string $output Attribute string built by get_language_attributes().
 * @return string
 */
function even_seo_force_dutch_lang( $output ) {
	if ( is_admin() ) {
		return $output;
	}

	if ( preg_match( '/lang="[^"]*"/', $output ) ) {
		return preg_replace( '/lang="[^"]*"/', 'lang="nl-NL"', $output );
	}

	return trim( $output . ' lang="nl-NL"' );
}
add_filter( 'language_attributes', 'even_seo_force_dutch_lang' );

/**
 * Fallback descriptions for pages whose content is structural rather than
 * `the_content()` (see the content architecture table in AGENTS.md), so
 * get_the_excerpt() has nothing to trim.
 *
 * @return array<string,string> Page slug => description.
 */
function even_seo_fallback_descriptions() {
	return array(
		'contact' => __( 'Neem contact op met Eve-n voor teamontwikkeling en samenwerking binnen infrastructuurprojecten.', 'eve-n' ),
	);
}

/**
 * Plain-text meta description for the current front-end request.
 *
 * @return string
 */
function even_seo_description() {
	if ( is_front_page() ) {
		$raw = get_bloginfo( 'description' );
	} elseif ( is_home() ) {
		$page_id = (int) get_option( 'page_for_posts' );
		$raw     = $page_id ? get_the_excerpt( $page_id ) : get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$raw = get_the_excerpt();

		if ( '' === trim( wp_strip_all_tags( (string) $raw ) ) ) {
			$fallbacks = even_seo_fallback_descriptions();
			$slug      = get_post_field( 'post_name', get_queried_object_id() );
			$raw       = $fallbacks[ $slug ] ?? get_bloginfo( 'description' );
		}
	} else {
		$raw = get_bloginfo( 'description' );
	}

	$raw = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $raw ) ) );

	return wp_trim_words( $raw, 30, '…' );
}

/**
 * Canonical URL for the current front-end request.
 *
 * @return string
 */
function even_seo_canonical_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}

	if ( is_home() ) {
		$page_id = (int) get_option( 'page_for_posts' );
		return $page_id ? get_permalink( $page_id ) : home_url( '/' );
	}

	if ( is_singular() ) {
		return get_permalink();
	}

	return home_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) );
}

/**
 * Social-share image: the current page's featured image (or the posts page's,
 * on the blog index), falling back to the home page hero photo.
 *
 * @return string
 */
function even_seo_image_url() {
	$post_id = null;

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
	} elseif ( is_home() ) {
		$post_id = (int) get_option( 'page_for_posts' );
	}

	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'even-hero' );
		if ( $src ) {
			return $src[0];
		}
	}

	return get_theme_file_uri( 'assets/img/home-hero.webp' );
}

/**
 * Print the description, canonical, Open Graph and Twitter card tags.
 *
 * Replaces core's rel_canonical() (singular-only) with one that also covers
 * the front page and the blog index.
 */
function even_seo_head_tags() {
	$title       = wp_strip_all_tags( wp_get_document_title() );
	$description = even_seo_description();
	$canonical   = even_seo_canonical_url();
	$image       = even_seo_image_url();
	$type        = is_singular( 'post' ) ? 'article' : 'website';
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<link rel="canonical" href="<?php echo esc_url( $canonical ); ?>">

	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:locale" content="nl_NL">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $canonical ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php
}
remove_action( 'wp_head', 'rel_canonical' );
add_action( 'wp_head', 'even_seo_head_tags', 5 );

/**
 * JSON-LD Organization schema, printed on every front-end page. Contact
 * details verbatim from reference/catalog-contact.md / inc/contact-form.php.
 */
function even_seo_organization_schema() {
	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => 'EVE-N',
		'url'      => home_url( '/' ),
		'logo'     => get_theme_file_uri( 'assets/img/favicon/favicon-512x512.png' ),
		'contactPoint' => array(
			array(
				'@type'             => 'ContactPoint',
				'contactType'       => 'customer service',
				'name'              => 'Thomas Vilain',
				'email'             => 'T.vilain@eve-n.nl',
				'telephone'         => '+31-6-81440225',
				'areaServed'        => 'NL',
				'availableLanguage' => array( 'nl' ),
			),
			array(
				'@type'             => 'ContactPoint',
				'contactType'       => 'customer service',
				'name'              => 'Eveline Hinfelaar',
				'email'             => 'E.hinfelaar@eve-n.nl',
				'telephone'         => '+31-6-24656833',
				'areaServed'        => 'NL',
				'availableLanguage' => array( 'nl' ),
			),
		),
	);

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $schema ) );
}
add_action( 'wp_head', 'even_seo_organization_schema', 6 );

/**
 * robots.txt: allow everything but wp-admin, and point at the core XML
 * sitemap (wp-sitemap.xml, enabled by default since WP 5.5 — no plugin
 * needed).
 *
 * @param string $output Default robots.txt body.
 * @param bool   $public Whether the site is set to be publicly indexed.
 * @return string
 */
function even_seo_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}

	$output  = "User-agent: *\n";
	$output .= "Disallow: /wp-admin/\n";
	$output .= "Allow: /wp-admin/admin-ajax.php\n";
	$output .= "\n";
	$output .= 'Sitemap: ' . home_url( '/wp-sitemap.xml' ) . "\n";

	return $output;
}
add_filter( 'robots_txt', 'even_seo_robots_txt', 10, 2 );
