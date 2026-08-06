<?php
/**
 * Projecten — the /projecten/ page.
 *
 * Ported from `reviews.html` — the export named this page after the mockup
 * ("reviews"), but the site calls it Projecten. The hero title is the page
 * title. The five project cards below it are not a repeater field: each is a
 * plain `<h2>` in the page content, and everything up to the next `<h2>`
 * becomes that card's expandable body (see inc/accordion.php). A client adds
 * a project by adding a heading and a paragraph in the normal editor.
 *
 * Reuses the `.accordion` component's existing JS (assets/js/main.js) and CSS
 * (assets/css/main.css) rather than introducing a second implementation.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$even_content = get_the_content();
	$even_content = apply_filters( 'the_content', $even_content );
	$even_content = str_replace( ']]>', ']]&gt;', $even_content );

	$even_accordion_items = even_accordion_items_from_content( $even_content );
	?>

<main>

	<section class="section-white" style="text-align: left;">
		<div class="container">
			<h1 class="heading-accent"><?php the_title(); ?></h1>

			<?php if ( $even_accordion_items ) : ?>
				<div class="accordion">
					<?php foreach ( $even_accordion_items as $even_index => $even_item ) : ?>
						<?php $even_panel_id = 'accordion-panel-' . ( $even_index + 1 ); ?>
						<div class="accordion__item">
							<button type="button" class="accordion__header" aria-expanded="false" aria-controls="<?php echo esc_attr( $even_panel_id ); ?>">
								<span class="accordion__header-text"><?php echo esc_html( $even_item['title'] ); ?></span>
								<span class="accordion__toggle" aria-hidden="true">+</span>
							</button>
							<div class="accordion__body" id="<?php echo esc_attr( $even_panel_id ); ?>">
								<div class="accordion__body-inner">
									<?php
									// Already-filtered editor markup — the same trust level as the_content().
									echo $even_item['body']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

</main>

	<?php
endwhile;

get_footer();
