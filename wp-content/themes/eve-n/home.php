<?php
/**
 * Blog index — the posts page. Ported from `blogs.html`.
 *
 * Hero with the page title, the intro block from the "Blog" page's own content,
 * then the two-column card grid over the main query, four cards per page.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();

$even_posts_page = (int) get_option( 'page_for_posts' );
$even_title      = $even_posts_page ? get_the_title( $even_posts_page ) : __( 'Blog', 'eve-n' );
$even_intro      = $even_posts_page
	? trim( apply_filters( 'the_content', get_post_field( 'post_content', $even_posts_page ) ) )
	: '';
?>

<main>

	<!-- ========== HERO ========== -->
	<section class="hero hero--page hero--blog">
		<?php
		// An <img> rather than a CSS background, so the hero gets srcset. It is the
		// LCP element on this page, so it is neither lazy nor low priority.
		if ( $even_posts_page && has_post_thumbnail( $even_posts_page ) ) {
			echo get_the_post_thumbnail(
				$even_posts_page,
				'even-hero',
				array(
					'class'         => 'hero__img',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
				)
			);
		}
		?>
		<div class="hero-content">
			<h1><?php echo esc_html( $even_title ); ?></h1>
		</div>
	</section>

	<?php if ( '' !== $even_intro ) : ?>
	<!-- ========== INTRO ========== -->
	<section class="section-white section-white--intro">
		<div class="container">
			<div class="intro-block">
				<?php
				// Already through the_content filters, exactly as the_content() would
				// print it — escaping here would strip embeds the client added.
				echo $even_intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ========== BLOG CARDS ========== -->
	<section class="section-white section-white--cards blob-decor">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="blog-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						$even_subtitle = even_get_subtitle();
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
							<a class="blog-card__link" href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="blog-card__img">
										<?php
										the_post_thumbnail(
											'even-card',
											array(
												'class'   => 'blog-card__img-el',
												'loading' => 'lazy',
											)
										);
										?>
									</div>
								<?php else : ?>
									<div class="blog-card__img blog-card__img--empty" aria-hidden="true"></div>
								<?php endif; ?>

								<div class="blog-card__rule"></div>

								<p class="blog-card__date">
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
										<?php echo esc_html( get_the_date( 'd-m-Y' ) ); ?>
									</time>
								</p>

								<h2 class="blog-card__title"><?php the_title(); ?></h2>

								<?php if ( '' !== $even_subtitle ) : ?>
									<p class="blog-card__subtitle"><?php echo esc_html( $even_subtitle ); ?></p>
								<?php endif; ?>
							</a>
						</article>
						<?php
					endwhile;
					?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'class'              => 'blog-pagination',
						'mid_size'           => 1,
						'prev_text'          => __( 'Vorige', 'eve-n' ),
						'next_text'          => __( 'Volgende', 'eve-n' ),
						'screen_reader_text' => __( 'Berichtennavigatie', 'eve-n' ),
						'aria_label'         => __( 'Berichten', 'eve-n' ),
					)
				);
				?>
			<?php else : ?>
				<p><?php esc_html_e( 'Nog geen berichten.', 'eve-n' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

</main>

<?php
get_footer();
