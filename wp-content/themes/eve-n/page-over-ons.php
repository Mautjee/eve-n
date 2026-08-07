<?php
/**
 * Over Ons — the /over-ons/ page.
 *
 * Ported from `over-ons.html`. The hero and intro are editable from wp-admin,
 * exactly like Onze Werkwijze: the hero title is the page title, the hero
 * photograph is the featured image, and the intro paragraphs are the page
 * content — task-007 seeds them from `reference/catalog-over-ons.md`.
 *
 * The two person cards below are not editor content. Each one ties a name, a
 * portrait and a biography together as a single unit, which the block editor
 * cannot express for just two people, so they come from even_team_members()
 * in inc/team.php instead.
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

	<!-- ========== INTRO ========== -->
	<section class="section-white section-white--intro">
		<div class="container">
			<div class="intro-block">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

	<!-- ========== TEAM ========== -->
	<section class="section-white blob-decor">
		<div class="team-grid">
			<?php foreach ( even_team_members() as $even_member ) : ?>
				<div class="team-card">
					<?php
					even_seeded_image(
						$even_member['image'],
						'even-portrait',
						array(
							'class'    => 'team-card__img',
							'alt'      => $even_member['alt'],
							'loading'  => 'lazy',
							'decoding' => 'async',
						)
					);
					?>
					<h3 class="team-card__name"><?php echo esc_html( $even_member['name'] ); ?></h3>
					<?php foreach ( $even_member['bio'] as $even_paragraph ) : ?>
						<p class="team-card__bio"><?php echo esc_html( $even_paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

</main>

	<?php
endwhile;

get_footer();
