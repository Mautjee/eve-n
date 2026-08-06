<?php
/**
 * Single blog post. Ported from `blog-pagina.html`.
 *
 * Full-bleed hero: the featured image behind a dark wash, with the title and the
 * "Ondertitel" centred over it. Then one centred column from `the_content()`, so
 * everything the client writes in wp-admin comes through as authored.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$even_subtitle = even_get_subtitle();
	?>

	<main>
		<article <?php post_class( 'blog-post' ); ?>>

			<!-- ========== HERO ========== -->
			<header class="hero hero--post<?php echo has_post_thumbnail() ? '' : ' hero--post-plain'; ?>">
				<?php
				// An <img> rather than a CSS background, so the hero gets srcset. It is
				// the LCP element on this page, so it is neither lazy nor low priority.
				the_post_thumbnail(
					'even-hero',
					array(
						'class'         => 'hero__img',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
				<div class="hero-content">
					<h1><?php the_title(); ?></h1>
					<?php if ( '' !== $even_subtitle ) : ?>
						<p class="hero__subtitle"><?php echo esc_html( $even_subtitle ); ?></p>
					<?php endif; ?>
					<p class="screen-reader-text">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
							<?php echo esc_html( get_the_date( 'd-m-Y' ) ); ?>
						</time>
					</p>
				</div>
			</header>

			<!-- ========== ARTICLE BODY ========== -->
			<section class="section-white blob-decor">
				<div class="container">
					<div class="article-body">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before'         => '<nav class="blog-pagination" aria-label="' . esc_attr__( 'Paginanavigatie', 'eve-n' ) . '"><div class="nav-links">',
								'after'          => '</div></nav>',
								'next_or_number' => 'number',
							)
						);
						?>
					</div>
				</div>
			</section>

		</article>
	</main>

	<?php
endwhile;

get_footer();
