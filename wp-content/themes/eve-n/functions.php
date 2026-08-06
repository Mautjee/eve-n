<?php
/**
 * EVE-N theme setup.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

define( 'EVEN_VERSION', '0.1.0' );

require_once get_theme_file_path( 'inc/links.php' );
require_once get_theme_file_path( 'inc/hero.php' );

/**
 * Theme supports and menu registration.
 */
function even_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Hoofdmenu', 'eve-n' ),
		)
	);

	// Blog card thumbnails and full-bleed hero images, sized from the XD design.
	add_image_size( 'even-card', 800, 520, true );
	add_image_size( 'even-hero', 1920, 600, true );
}
add_action( 'after_setup_theme', 'even_setup' );

/**
 * Front-end assets.
 */
function even_assets() {
	$css = get_theme_file_path( 'assets/css/main.css' );
	$js  = get_theme_file_path( 'assets/js/main.js' );

	wp_enqueue_style(
		'even-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'even-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array( 'even-fonts' ),
		file_exists( $css ) ? filemtime( $css ) : EVEN_VERSION
	);

	wp_enqueue_script(
		'even-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		file_exists( $js ) ? filemtime( $js ) : EVEN_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'even_assets' );

/**
 * Preconnect to the Google Fonts hosts so the stylesheet request starts earlier.
 *
 * @param array  $urls           URLs to print.
 * @param string $relation_type  Link relation.
 * @return array
 */
function even_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'even_resource_hints', 10, 2 );

/**
 * The header logo: the site's custom logo when one is set, otherwise the mark
 * exported from the XD design.
 */
function even_logo() {
	$custom = get_theme_mod( 'custom_logo' );

	if ( $custom ) {
		echo wp_get_attachment_image(
			$custom,
			'full',
			false,
			array(
				'class' => 'nav-logo__img',
				'alt'   => esc_attr( get_bloginfo( 'name' ) ),
			)
		);
		return;
	}

	printf(
		'<img src="%1$s" alt="%2$s" width="28" height="28">',
		esc_url( get_theme_file_uri( 'assets/img/logo.webp' ) ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

/**
 * The five nav links from the XD design, used until a "Hoofdmenu" is assigned
 * in wp-admin. Keyed by page slug so the links resolve once the pages exist.
 *
 * @return array<string,string>
 */
function even_default_nav_items() {
	return array(
		'onze-werkwijze' => __( 'Werkwijze', 'eve-n' ),
		'projecten'      => __( 'Projecten', 'eve-n' ),
		'over-ons'       => __( 'Over ons', 'eve-n' ),
		'blog'           => __( 'Blog', 'eve-n' ),
		'contact'        => __( 'Contact', 'eve-n' ),
	);
}

/**
 * Render the primary menu, falling back to the design's hard-coded links.
 *
 * WordPress emits `<ul><li><a>`; the stylesheet's flex rules sit on the element
 * carrying the class, so the class always goes on the `<ul>` (container => '').
 *
 * @param array $args Overrides: menu_class, menu_id, aria_label.
 */
function even_nav_menu( $args ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'nav-links';
	$menu_id    = isset( $args['menu_id'] ) ? $args['menu_id'] : '';
	$aria_label = isset( $args['aria_label'] ) ? $args['aria_label'] : __( 'Menu', 'eve-n' );

	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => $menu_class,
				'menu_id'        => $menu_id,
				'container'      => '',
				'depth'          => 1,
				'items_wrap'     => '<ul id="%1$s" class="%2$s" aria-label="' . esc_attr( $aria_label ) . '">%3$s</ul>',
			)
		);
		return;
	}

	printf(
		'<ul%1$s class="%2$s" aria-label="%3$s">',
		$menu_id ? ' id="' . esc_attr( $menu_id ) . '"' : '',
		esc_attr( $menu_class ),
		esc_attr( $aria_label )
	);

	foreach ( even_default_nav_items() as $slug => $label ) {
		$page      = get_page_by_path( $slug );
		$url       = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
		$is_active = $page && is_page( $page->ID );

		printf(
			'<li><a href="%1$s"%2$s>%3$s</a></li>',
			esc_url( $url ),
			$is_active ? ' class="active" aria-current="page"' : '',
			esc_html( $label )
		);
	}

	echo '</ul>';
}

/**
 * The stylesheet styles the current link with `.active`; WordPress marks the
 * parent `<li>` instead. Copy the state onto the anchor.
 *
 * @param array    $atts Anchor attributes.
 * @param WP_Post  $item Menu item.
 * @return array
 */
function even_nav_link_active_class( $atts, $item ) {
	$current = array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current_page_parent' );

	if ( ! empty( $item->classes ) && array_intersect( $current, (array) $item->classes ) ) {
		$atts['class']        = trim( ( isset( $atts['class'] ) ? $atts['class'] . ' ' : '' ) . 'active' );
		$atts['aria-current'] = 'page';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'even_nav_link_active_class', 10, 2 );
