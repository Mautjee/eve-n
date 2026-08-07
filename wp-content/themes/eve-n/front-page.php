<?php
/**
 * Front page: the composed home page.
 *
 * Ported from index.html. Six stacked sections — hero, Onze Werkwijze, Over
 * Ons, Blog, Projecten, Contact CTA — in the order of reference/home.png.
 *
 * The copy is hard-coded for v1: the layout alternates photo side, background
 * treatment and button style per section, which the block editor cannot
 * express without a page builder. See the content architecture table in
 * AGENTS.md. Every other page draws its text from the editor.
 *
 * The section photos are side-loaded into the media library by
 * bin/seed.php (task-008) and resolved here by key — see inc/media.php.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main>

	<!-- ========== HERO (home variant) ========== -->
	<section class="hero hero--home">
		<?php
		// An <img> rather than a CSS background, so the hero gets srcset. It
		// is the LCP element on this page, so it is neither lazy nor low
		// priority.
		even_seeded_image(
			'home-hero',
			'even-hero',
			array(
				'class'         => 'hero__img',
				'sizes'         => '100vw',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decoding'      => 'sync',
			)
		);
		?>
		<div class="hero-content">
			<div class="hero-logo">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo.webp' ) ); ?>" alt="<?php esc_attr_e( 'EVE-N logo', 'eve-n' ); ?>" width="60" height="60">
				<h1 class="logo-text">EVE-N</h1>
			</div>
			<p class="hero-subtitle">Bouwen aan succes; teamontwikkeling en samenwerking binnen infrastructuurprojecten</p>
		</div>
		<div class="hero-scroll" aria-hidden="true">&#x2304;</div>
	</section>

	<!-- ========== ONZE WERKWIJZE (teal panel + photo) ========== -->
	<section class="section-teal" id="werkwijze">
		<div class="section-teal__photo">
			<?php
			even_seeded_image(
				'home-werkwijze',
				'even-hero',
				array(
					'class'    => 'section-teal__photo-img',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
			?>
		</div>
		<div class="section-teal__content">
			<h2>Onze Werkwijze</h2>
			<p>De infrastructuur kent grote uitdagingen! Samenwerken binnen deze complexe opgaves is cruciaal om effectief en succesvol te zijn. En dat moet je samen goed organiseren! Succesvolle samenwerking vraagt om m&eacute;&eacute;r dan structuur alleen. Het draait ook om het ontwikkelen van samenwerkingsvaardigheden, het versterken van onderling vertrouwen en het bouwen aan een (h)echt team. Eve-n begeleidt dit proces met oog voor de mens &eacute;n het project. Samen maken we van samenwerking een kracht &ndash; voor projecten die niet alleen effici&euml;nt, maar ook met plezier worden gerealiseerd.</p>
			<a href="<?php echo esc_url( even_page_url( 'onze-werkwijze' ) ); ?>" class="btn btn--ghost"><?php esc_html_e( 'Read More', 'eve-n' ); ?></a>
		</div>
	</section>

	<!-- ========== OVER ONS (white section) ========== -->
	<section class="section-white blob-decor" id="over-ons">
		<div class="container">
			<h2>Over Ons</h2>
			<p>Eve-n is opgericht in 2013 door Eveline Hinfelaar MBA. Inmiddels bestaat ons team uit twee ervaren experts in teamontwikkeling en samenwerking binnen de infrastructuursector.</p>
			<p>Met jarenlange ervaring bij uiteenlopende opdrachtgevers brengen we in kaart wat nodig is om teams sterker te maken en resultaten te behalen. Wij geloven dat effectieve verandering maatwerk vereist &ndash; op elk niveau van de organisatie, van de werkvloer tot het management. Onze aanpak is dan ook altijd zorgvuldig afgestemd op de mensen, de context en de doelen van het project.</p>
			<a href="<?php echo esc_url( even_page_url( 'over-ons' ) ); ?>" class="btn btn--fill"><?php esc_html_e( 'Read More', 'eve-n' ); ?></a>
		</div>
	</section>

	<!-- ========== BLOG (teal panel + photo, reversed) ========== -->
	<section class="section-teal" id="blog" style="flex-direction: row-reverse;">
		<div class="section-teal__photo">
			<?php
			even_seeded_image(
				'home-blog',
				'even-hero',
				array(
					'class'    => 'section-teal__photo-img',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
			?>
		</div>
		<div class="section-teal__content">
			<h2>Blog</h2>
			<p>We geloven sterk in het delen van kennis &ndash; want goede samenwerking begint met begrijpen wat werkt.</p>
			<p>Dat doen we door het schrijven van blogs over thema's die we in onze dagelijkse praktijk tegen komen.</p>
			<a href="<?php echo esc_url( even_page_url( 'blog' ) ); ?>" class="btn btn--fill" style="background: var(--cyan-dark);"><?php esc_html_e( 'Read More', 'eve-n' ); ?></a>
		</div>
	</section>

	<!-- ========== PROJECTEN (white section with cards) ========== -->
	<section class="section-white blob-decor" id="projecten">
		<div class="container">
			<h2>Projecten</h2>
			<div class="card-grid">
				<?php
				/*
				 * Placeholder copy: the real project write-ups are still pending from
				 * the client. Task-004 makes the Projecten page itself editable.
				 */
				$even_home_projects = array(
					array(
						'title' => 'Project 1',
						'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint',
					),
					array(
						'title' => 'Project 1',
						'body'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint',
					),
				);

				foreach ( $even_home_projects as $even_project ) :
					?>
					<article class="card">
						<h3 class="card__title"><?php echo esc_html( $even_project['title'] ); ?></h3>
						<p class="card__body"><?php echo esc_html( $even_project['body'] ); ?></p>
						<span class="card__icon" aria-hidden="true">&gt;</span>
						<?php // Mobile only: the cards render as closed accordion rows (see phone-home.png). ?>
						<button type="button" class="card__icon card__icon--plus" style="display: none;" aria-label="<?php esc_attr_e( 'Project uitklappen', 'eve-n' ); ?>" aria-expanded="false">+</button>
					</article>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</section>

	<!-- ========== CONTACT CTA (photo bg) ========== -->
	<section class="cta-photo">
		<?php
		even_seeded_image(
			'home-contact-cta',
			'even-hero',
			array(
				'class'    => 'cta-photo__img',
				'sizes'    => '100vw',
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
		?>
		<p>Benieuwd wat wij voor jouw team kunnen betekenen?</p>
		<p>Neem gerust contact met ons op &ndash; we denken graag met je mee!</p>
		<a href="<?php echo esc_url( even_page_url( 'contact' ) ); ?>" class="btn btn--fill"><?php esc_html_e( 'Contact', 'eve-n' ); ?></a>
	</section>

</main>

<?php
get_footer();
