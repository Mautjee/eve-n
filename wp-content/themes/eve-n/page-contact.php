<?php
/**
 * Contact page: the /contact/ page — form and the two contact-person blocks.
 *
 * Ported from contact.html. Structure is fixed rather than the_content() (see
 * the content architecture table in AGENTS.md): the form's markup, labels and
 * the two contact people are all structural, not free-form copy.
 *
 * The form itself posts to admin-post.php and is handled by
 * inc/contact-form.php. This template only renders the fields, the honeypot
 * and nonce, and — after the redirect back — whatever notice and
 * previously-typed values that handler left behind.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

get_header();

$even_notice = even_contact_get_notice();
$even_errors = $even_notice['errors'] ?? array();
$even_values = wp_parse_args(
	$even_notice['values'] ?? array(),
	array(
		'naam'      => '',
		'email'     => '',
		'onderwerp' => '',
		'bericht'   => '',
	)
);
?>

<main>

	<!-- ========== HERO ========== -->
	<?php even_page_hero(); ?>

	<!-- ========== CONTACT FORM ========== -->
	<section class="section-white">
		<div class="container">

			<?php if ( $even_notice && 'success' === $even_notice['status'] ) : ?>
				<p class="form-notice form-notice--success" role="status">
					<?php esc_html_e( 'Bedankt voor uw bericht! We nemen zo snel mogelijk contact met u op.', 'eve-n' ); ?>
				</p>
			<?php elseif ( $even_notice && 'error' === $even_notice['status'] ) : ?>
				<p class="form-notice form-notice--error" role="alert">
					<?php esc_html_e( 'Controleer de gemarkeerde velden en probeer het opnieuw.', 'eve-n' ); ?>
				</p>
			<?php elseif ( $even_notice ) : ?>
				<p class="form-notice form-notice--error" role="alert">
					<?php esc_html_e( 'Er ging iets mis bij het verzenden van uw bericht. Probeer het opnieuw.', 'eve-n' ); ?>
				</p>
			<?php endif; ?>

			<form class="contact-layout" id="even-contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="<?php echo esc_attr( EVEN_CONTACT_ACTION ); ?>">
				<?php wp_nonce_field( EVEN_CONTACT_ACTION, EVEN_CONTACT_NONCE_FIELD ); ?>

				<?php // Honeypot: left blank by visitors, filled in by bots that fill every field they can see in markup. ?>
				<p class="screen-reader-text">
					<label for="even-contact-hp"><?php esc_html_e( 'Laat dit veld leeg', 'eve-n' ); ?></label>
					<input type="text" id="even-contact-hp" name="<?php echo esc_attr( EVEN_CONTACT_HONEYPOT ); ?>" tabindex="-1" autocomplete="off">
				</p>

				<div class="contact-form">
					<label class="screen-reader-text" for="even-contact-naam"><?php esc_html_e( 'Naam', 'eve-n' ); ?></label>
					<input
						type="text"
						id="even-contact-naam"
						name="naam"
						placeholder="<?php esc_attr_e( 'Naam', 'eve-n' ); ?>"
						value="<?php echo esc_attr( $even_values['naam'] ); ?>"
						required
						<?php echo isset( $even_errors['naam'] ) ? 'aria-invalid="true" aria-describedby="even-contact-naam-error"' : ''; ?>
					>
					<?php if ( isset( $even_errors['naam'] ) ) : ?>
						<p class="form-field-error" id="even-contact-naam-error"><?php echo esc_html( $even_errors['naam'] ); ?></p>
					<?php endif; ?>

					<label class="screen-reader-text" for="even-contact-email"><?php esc_html_e( 'Email', 'eve-n' ); ?></label>
					<input
						type="email"
						id="even-contact-email"
						name="email"
						placeholder="<?php esc_attr_e( 'Email', 'eve-n' ); ?>"
						value="<?php echo esc_attr( $even_values['email'] ); ?>"
						required
						<?php echo isset( $even_errors['email'] ) ? 'aria-invalid="true" aria-describedby="even-contact-email-error"' : ''; ?>
					>
					<?php if ( isset( $even_errors['email'] ) ) : ?>
						<p class="form-field-error" id="even-contact-email-error"><?php echo esc_html( $even_errors['email'] ); ?></p>
					<?php endif; ?>

					<label class="screen-reader-text" for="even-contact-onderwerp"><?php esc_html_e( 'Onderwerp', 'eve-n' ); ?></label>
					<input
						type="text"
						id="even-contact-onderwerp"
						name="onderwerp"
						placeholder="<?php esc_attr_e( 'Onderwerp', 'eve-n' ); ?>"
						value="<?php echo esc_attr( $even_values['onderwerp'] ); ?>"
						required
						<?php echo isset( $even_errors['onderwerp'] ) ? 'aria-invalid="true" aria-describedby="even-contact-onderwerp-error"' : ''; ?>
					>
					<?php if ( isset( $even_errors['onderwerp'] ) ) : ?>
						<p class="form-field-error" id="even-contact-onderwerp-error"><?php echo esc_html( $even_errors['onderwerp'] ); ?></p>
					<?php endif; ?>
				</div>

				<div class="contact-form">
					<label class="screen-reader-text" for="even-contact-bericht"><?php esc_html_e( 'Text', 'eve-n' ); ?></label>
					<textarea
						id="even-contact-bericht"
						name="bericht"
						placeholder="<?php esc_attr_e( 'Text', 'eve-n' ); ?>"
						required
						<?php echo isset( $even_errors['bericht'] ) ? 'aria-invalid="true" aria-describedby="even-contact-bericht-error"' : ''; ?>
					><?php echo esc_textarea( $even_values['bericht'] ); ?></textarea>
					<?php if ( isset( $even_errors['bericht'] ) ) : ?>
						<p class="form-field-error" id="even-contact-bericht-error"><?php echo esc_html( $even_errors['bericht'] ); ?></p>
					<?php endif; ?>
				</div>
			</form>

			<?php // Sits outside <form> to match the design's centring; `form=""` still binds it to the submit. ?>
			<button type="submit" form="even-contact-form" class="btn btn--fill" style="margin-top: 24px;"><?php esc_html_e( 'Send', 'eve-n' ); ?></button>
		</div>
	</section>

	<!-- ========== CONTACT PERSONS ========== -->
	<section class="section-white blob-decor blob-decor--left" style="text-align: left;">
		<div class="container">
			<div class="contact-persons">
				<?php
				$even_contact_people = array(
					array(
						'name'  => 'Thomas Vilain',
						'email' => 'T.vilain@eve-n.nl',
						'phone' => '+31 6 81 44 02 25',
						'image' => 'contact-thomas.webp',
					),
					array(
						'name'  => 'Eveline Hinfelaar',
						'email' => 'E.hinfelaar@eve-n.nl',
						'phone' => '+31 6 24 65 68 33',
						'image' => 'contact-eveline.webp',
					),
				);

				foreach ( $even_contact_people as $even_person ) :
					?>
					<div class="contact-person">
						<img
							class="contact-person__img"
							src="<?php echo esc_url( even_theme_image_url( $even_person['image'] ) ); ?>"
							alt="<?php echo esc_attr( $even_person['name'] ); ?>"
							width="120" height="150" loading="lazy"
						>
						<div>
							<p class="contact-person__name"><?php echo esc_html( $even_person['name'] ); ?></p>
							<p class="contact-person__detail"><a href="mailto:<?php echo esc_attr( $even_person['email'] ); ?>"><?php echo esc_html( $even_person['email'] ); ?></a></p>
							<p class="contact-person__detail"><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $even_person['phone'] ) ); ?>"><?php echo esc_html( $even_person['phone'] ); ?></a></p>
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
