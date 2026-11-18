<?php
/**
 * Theme stylesheet (header, footer, text pages, Complianz). It loads after the plugin's
 * design system (tokens, fonts, base styles), which it builds on.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		$deps = wp_style_is( 'vde-elements', 'registered' ) ? array( 'vde-elements' ) : array();
		wp_enqueue_style( 'vd-theme', VD_URI . '/assets/css/theme.css', $deps, VD_VERSION . '.' . filemtime( VD_DIR . '/assets/css/theme.css' ) );
	},
	20
);

// Make the page itself the scroll target for "Back to top".
add_filter(
	'language_attributes',
	function ( $output ) {
		return $output . ' id="top"';
	}
);
