<?php
/**
 * Fallback template. Replaced in phase 4 by the real blog index.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main>
	<section class="section-white">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class(); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</article>
					<?php
				endwhile;
			else :
				?>
				<p><?php esc_html_e( 'Nog geen berichten.', 'eve-n' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
