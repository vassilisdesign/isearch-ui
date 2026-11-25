<?php
/**
 * SVG wordmark from the Media Library: the VD Hero can show an uploaded SVG logo instead of the
 * typed wordmark. The SVG is printed inline (no extra request, so it appears with the page), cleaned of
 * anything that could run code, and its CSS class names are made unique so two logos never clash.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** Inline markup for an SVG attachment, or '' when it is not a readable SVG. */
function vde_inline_svg_attachment( $attachment_id, $class = '' ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id || 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
		return '';
	}
	$file = get_attached_file( $attachment_id );
	if ( ! $file || ! is_readable( $file ) || filesize( $file ) > 300 * KB_IN_BYTES ) {
		return '';
	}
	$svg = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! preg_match( '/<svg\b.*<\/svg>/is', $svg, $m ) ) {
		return '';
	}
	$svg = $m[0];

	// Nothing that can run code or load other files.
	$svg = preg_replace( '#<(script|foreignObject|iframe|object|embed|image|use)\b.*?(</\1>|/>)#is', '', $svg );
	$svg = preg_replace( '/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg );
	$svg = preg_replace( '/(href|xlink:href)\s*=\s*("|\')\s*(javascript|data):[^"\']*\2/i', '', $svg );
	$svg = preg_replace( '/<!--.*?-->/s', '', $svg );

	// Unique class names and ids (Illustrator uses .st0, .st1 ... in every file).
	$prefix = 'vdsvg' . $attachment_id . '-';
	$svg    = preg_replace_callback(
		'/<style\b[^>]*>(.*?)<\/style>/is',
		function ( $s ) use ( $prefix ) {
			return str_replace( $s[1], preg_replace( '/\.(-?[_a-zA-Z][\w-]*)/', '.' . $prefix . '$1', $s[1] ), $s[0] );
		},
		$svg
	);
	$svg = preg_replace_callback(
		'/\sclass="([^"]*)"/',
		function ( $c ) use ( $prefix ) {
			return ' class="' . trim( preg_replace( '/(\S+)/', $prefix . '$1', $c[1] ) ) . '"';
		},
		$svg
	);
	$svg = preg_replace( '/\sid="([^"]*)"/', ' id="' . $prefix . '$1"', $svg );

	// Our attributes on the root element: decorative (the heading carries the name), never focusable.
	$svg = preg_replace( '/<svg\b/', '<svg class="vd-wm-svg ' . esc_attr( $class ) . '" aria-hidden="true" focusable="false" role="presentation"', $svg, 1 );
	return $svg;
}
