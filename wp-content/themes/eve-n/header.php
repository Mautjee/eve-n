<?php
/**
 * Site header: top bar, desktop nav, mobile menu.
 *
 * Ported from the duplicated markup in the seven static HTML pages. The nav is
 * driven by the "Hoofdmenu" menu so the links stay editable from wp-admin.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Naar de inhoud', 'eve-n' ); ?></a>

<!-- ========== HEADER / NAV ========== -->
<header class="site-header">
	<div class="container">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo" aria-label="<?php esc_attr_e( 'Home', 'eve-n' ); ?>">
			<?php even_logo(); ?>
		</a>

		<?php
		even_nav_menu(
			array(
				'menu_class'      => 'nav-links',
				'container'       => 'nav',
				'container_class' => '',
				'aria_label'      => __( 'Hoofdmenu', 'eve-n' ),
			)
		);
		?>

		<button class="hamburger" aria-label="<?php esc_attr_e( 'Menu openen', 'eve-n' ); ?>" aria-expanded="false" aria-controls="mobile-menu">
			<span></span><span></span><span></span>
		</button>
	</div>
</header>

<!-- Mobile menu -->
<?php
even_nav_menu(
	array(
		'menu_class'      => 'mobile-menu',
		'menu_id'         => 'mobile-menu',
		'container'       => '',
		'aria_label'      => __( 'Mobiel menu', 'eve-n' ),
	)
);
?>

<div id="content">
