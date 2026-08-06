<?php
/**
 * Turn flat heading/body content into accordion items.
 *
 * The Projecten page has no repeater or custom fields for its five project
 * cards — the client adds a project by adding an `<h2>` and a paragraph in
 * the normal editor. This walks the rendered page content and groups
 * everything between one top-level `<h2>` and the next into a single
 * accordion item, so page-projecten.php only has to loop over the result.
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * Split rendered post content into accordion items keyed on top-level `<h2>`s.
 *
 * Content before the first heading (if any) belongs to no item and is
 * dropped. A page with no headings returns an empty array.
 *
 * @param string $content Rendered HTML, e.g. from `apply_filters( 'the_content', get_the_content() )`.
 * @return array<int, array{title: string, body: string}>
 */
function even_accordion_items_from_content( $content ) {
	$content = trim( (string) $content );
	if ( '' === $content ) {
		return array();
	}

	$dom = new DOMDocument();

	$previous_errors = libxml_use_internal_errors( true );
	// The XML PI forces DOMDocument to treat the string as UTF-8 without
	// mangling multibyte characters; it is parsed as a PI, not a body node.
	$dom->loadHTML( '<?xml encoding="utf-8"?><body>' . $content . '</body>' );
	libxml_use_internal_errors( $previous_errors );

	$body = $dom->getElementsByTagName( 'body' )->item( 0 );
	if ( ! $body ) {
		return array();
	}

	$items   = array();
	$current = null;

	foreach ( $body->childNodes as $node ) {
		if ( ! $node instanceof DOMElement ) {
			continue;
		}

		if ( 'h2' === $node->tagName ) {
			if ( null !== $current ) {
				$items[] = $current;
			}
			$current = array(
				'title' => trim( $node->textContent ),
				'body'  => '',
			);
			continue;
		}

		if ( null === $current ) {
			continue;
		}

		$current['body'] .= $dom->saveHTML( $node );
	}

	if ( null !== $current ) {
		$items[] = $current;
	}

	return $items;
}
