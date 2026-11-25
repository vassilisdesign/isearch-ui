<?php
/**
 * SVG wordmark: the "VASSILIS DESIGN." lines drawn from outlined SVGs (exported from Illustrator in
 * Zuume Black) instead of the Adobe Fonts kit. Same look, no font download, nothing render-blocking.
 *
 * Files in assets/wordmark/: wordmark-line1.svg, wordmark-line2.svg (with the full stop as a
 * class="vd-stop" shape) and wordmark-footer.svg. While all hero files are present the VD Hero uses
 * them (switch "SVG wordmark" off to go back to text), the footer heading "VASSILISDESIGN." is drawn
 * the same way, and the Adobe Fonts kit is no longer loaded.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** Inline SVG for a wordmark file, or '' when the file is missing. */
function vde_wordmark_svg( $slug ) {
	static $cache = array();
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}
	$file = VDE_DIR . 'assets/wordmark/' . sanitize_file_name( $slug ) . '.svg';
	$svg  = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( $svg ) {
		$svg = preg_replace( '/^.*?(<svg\b)/s', '$1', $svg ); // drop the XML prolog and comments
		$svg = preg_replace( '/<svg\b/', '<svg class="vd-wm-svg" aria-hidden="true" focusable="false"', $svg, 1 );
	}
	$cache[ $slug ] = $svg;
	return $svg;
}

/** True when the hero wordmark can be drawn from SVG. */
function vde_wordmark_svg_ready() {
	return apply_filters( 'vde_svg_wordmark', '' !== vde_wordmark_svg( 'wordmark-line1' ) && '' !== vde_wordmark_svg( 'wordmark-line2' ) );
}

/* Footer heading "VASSILISDESIGN." drawn from the footer SVG; the text stays for screen readers and search. */
add_filter(
	'elementor/widget/render_content',
	function ( $content, $widget ) {
		if ( 'heading' !== $widget->get_name() || ! vde_wordmark_svg_ready() ) {
			return $content;
		}
		$svg = vde_wordmark_svg( 'wordmark-footer' );
		if ( '' === $svg || ! preg_match( '/>\s*VASSILISDESIGN\s*<span[^>]*>\.<\/span>\s*</i', $content ) ) {
			return $content;
		}
		return preg_replace(
			'/>\s*VASSILISDESIGN\s*<span[^>]*>\.<\/span>\s*</i',
			'><span class="vd-wm-footer">' . $svg . '<span class="screen-reader-text">VASSILISDESIGN.</span></span><',
			$content,
			1
		);
	},
	10,
	2
);

/* No Adobe Fonts kit once the wordmark is SVG (Zuume is used only there). */
add_filter(
	'style_loader_src',
	function ( $src ) {
		return ( is_string( $src ) && false !== strpos( $src, 'use.typekit.net' ) && ! is_admin() && vde_wordmark_svg_ready() ) ? false : $src;
	},
	99
);
add_filter(
	'vde_preconnect_adobe_fonts',
	function ( $on ) {
		return vde_wordmark_svg_ready() ? false : $on;
	}
);
