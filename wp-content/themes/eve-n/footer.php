<?php
/**
 * Site footer: the full-width teal bar closing every page.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- #content -->

<!-- ========== FOOTER ========== -->
<footer class="site-footer">
	<div class="container">
		<span>
			<?php
			printf(
				/* translators: %1$s: year, %2$s: site name */
				esc_html__( '%1$s %2$s', 'eve-n' ),
				'&copy; ' . esc_html( wp_date( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
