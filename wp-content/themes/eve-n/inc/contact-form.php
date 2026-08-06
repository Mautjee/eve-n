<?php
/**
 * Contact form: admin-post handler and the redirect-back notice it leaves.
 *
 * No form plugin — four fields don't justify one. Submission goes through
 * `admin-post.php` (works for logged-out visitors via the `_nopriv_` hook),
 * gets validated and mailed server-side, then redirects back to the referring
 * page. Since there is no session to carry state across that redirect,
 * errors and the previously-typed values are parked in a short-lived
 * transient keyed by a one-time token in the redirect URL, and consumed once
 * by `even_contact_get_notice()`.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

const EVEN_CONTACT_ACTION       = 'even_contact_submit';
const EVEN_CONTACT_NONCE_FIELD  = 'even_contact_nonce';
const EVEN_CONTACT_HONEYPOT     = 'even_contact_hp';
const EVEN_CONTACT_NOTICE_TTL   = 5 * MINUTE_IN_SECONDS;

/**
 * Who receives every contact form submission, verbatim from
 * reference/catalog-contact.md.
 *
 * @return string[]
 */
function even_contact_recipients() {
	return array( 'T.vilain@eve-n.nl', 'E.hinfelaar@eve-n.nl' );
}

/**
 * Park a redirect-back notice (status, field errors, previously-typed
 * values) in a transient and return the token it's stored under.
 *
 * @param string               $status Notice status: 'error' or 'nonce'.
 * @param array<string,string> $errors  Field key => Dutch error message.
 * @param array<string,string> $values  Field key => submitted value.
 * @return string
 */
function even_contact_store_notice( $status, $errors = array(), $values = array() ) {
	$token = wp_generate_uuid4();

	set_transient(
		'even_contact_' . $token,
		array(
			'status' => $status,
			'errors' => $errors,
			'values' => $values,
		),
		EVEN_CONTACT_NOTICE_TTL
	);

	return $token;
}

/**
 * Read and consume the notice left by a previous submission, if any.
 *
 * Reads `$_GET`, not form input — this only ever reflects the theme's own
 * redirect, never raw request data.
 *
 * @return array{status: string, errors: array<string,string>, values: array<string,string>}|null
 */
function even_contact_get_notice() {
	if ( ! isset( $_GET['even_contact'] ) ) {
		return null;
	}

	$status = sanitize_key( wp_unslash( $_GET['even_contact'] ) );

	if ( 'success' === $status ) {
		return array(
			'status' => 'success',
			'errors' => array(),
			'values' => array(),
		);
	}

	$token = isset( $_GET['even_contact_token'] ) ? sanitize_key( wp_unslash( $_GET['even_contact_token'] ) ) : '';

	if ( '' === $token ) {
		return array(
			'status' => $status,
			'errors' => array(),
			'values' => array(),
		);
	}

	$notice = get_transient( 'even_contact_' . $token );
	delete_transient( 'even_contact_' . $token );

	if ( ! is_array( $notice ) ) {
		return array(
			'status' => $status,
			'errors' => array(),
			'values' => array(),
		);
	}

	return wp_parse_args(
		$notice,
		array(
			'status' => $status,
			'errors' => array(),
			'values' => array(),
		)
	);
}

/**
 * Redirect back to where the form was submitted from, carrying the given
 * query args, and stop execution.
 *
 * @param array<string,string> $args Query args to append.
 */
function even_contact_redirect_back( $args ) {
	$url = wp_get_referer();

	if ( ! $url ) {
		$url = even_page_url( 'contact' );
	}

	$url = remove_query_arg( array( 'even_contact', 'even_contact_token' ), $url );

	wp_safe_redirect( add_query_arg( $args, $url ) );
	exit;
}

/**
 * Handle the contact form POST.
 */
function even_contact_handle_submission() {
	if ( ! isset( $_POST[ EVEN_CONTACT_NONCE_FIELD ] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ EVEN_CONTACT_NONCE_FIELD ] ) ), EVEN_CONTACT_ACTION )
	) {
		even_contact_redirect_back( array( 'even_contact' => 'nonce' ) );
	}

	// Honeypot: real visitors never see or fill this field. Pretend success
	// so the bot has no signal to iterate on, but never mail or store it.
	if ( ! empty( $_POST[ EVEN_CONTACT_HONEYPOT ] ) ) {
		even_contact_redirect_back( array( 'even_contact' => 'success' ) );
	}

	$values = array(
		'naam'      => isset( $_POST['naam'] ) ? sanitize_text_field( wp_unslash( $_POST['naam'] ) ) : '',
		'email'     => isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '',
		'onderwerp' => isset( $_POST['onderwerp'] ) ? sanitize_text_field( wp_unslash( $_POST['onderwerp'] ) ) : '',
		'bericht'   => isset( $_POST['bericht'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bericht'] ) ) : '',
	);

	$errors = array();

	if ( '' === $values['naam'] ) {
		$errors['naam'] = __( 'Vul uw naam in.', 'eve-n' );
	}

	if ( '' === $values['email'] ) {
		$errors['email'] = __( 'Vul uw e-mailadres in.', 'eve-n' );
	} elseif ( ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'Vul een geldig e-mailadres in.', 'eve-n' );
	}

	if ( '' === $values['onderwerp'] ) {
		$errors['onderwerp'] = __( 'Vul een onderwerp in.', 'eve-n' );
	}

	if ( '' === $values['bericht'] ) {
		$errors['bericht'] = __( 'Vul een bericht in.', 'eve-n' );
	}

	if ( $errors ) {
		$token = even_contact_store_notice( 'error', $errors, $values );
		even_contact_redirect_back(
			array(
				'even_contact'       => 'error',
				'even_contact_token' => $token,
			)
		);
	}

	$subject = sprintf(
		/* translators: %s: subject line entered by the visitor */
		__( 'Contactformulier eve-n.nl: %s', 'eve-n' ),
		$values['onderwerp']
	);

	$message = sprintf(
		"Naam: %s\nE-mail: %s\nOnderwerp: %s\n\nBericht:\n%s",
		$values['naam'],
		$values['email'],
		$values['onderwerp'],
		$values['bericht']
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %1$s <%2$s>', $values['naam'], $values['email'] ),
	);

	$sent = wp_mail( even_contact_recipients(), $subject, $message, $headers );

	if ( ! $sent ) {
		$token = even_contact_store_notice(
			'mail_failed',
			array(),
			$values
		);
		even_contact_redirect_back(
			array(
				'even_contact'       => 'mail_failed',
				'even_contact_token' => $token,
			)
		);
	}

	even_contact_redirect_back( array( 'even_contact' => 'success' ) );
}
add_action( 'admin_post_' . EVEN_CONTACT_ACTION, 'even_contact_handle_submission' );
add_action( 'admin_post_nopriv_' . EVEN_CONTACT_ACTION, 'even_contact_handle_submission' );
