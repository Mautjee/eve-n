<?php
/**
 * Onze Werkwijze — the /onze-werkwijze/ page.
 *
 * Ported from `onze-werkwijze.html`. Everything on the page is editable from
 * wp-admin: the hero title is the page title, the hero photograph is the
 * featured image, and the body is the page content. The copy lives in the page,
 * not here — task-007 seeds it from `reference/catalog-onze-werkwijze.md`.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

<main>

	<!-- ========== HERO ========== -->
	<?php even_page_hero(); ?>

	<!-- ========== BODY ========== -->
	<section class="page-body blob-decor">
		<div class="page-body__inner entry-content">
			<?php the_content(); ?>
		</div>
	</section>

</main>

	<?php
endwhile;

get_footer();
