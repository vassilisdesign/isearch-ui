<?php
/**
 * Works page layouts. The plugin's templates are used unless the active theme has its own
 * single-works.php / archive-works.php, and an Elementor Pro Theme Builder template for Works
 * takes over inside them when present.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'template_include',
	function ( $template ) {
		if ( is_singular( 'works' ) ) {
			$file = 'single-works.php';
		} elseif ( is_post_type_archive( 'works' ) || is_tax( 'work_sector' ) ) {
			$file = 'archive-works.php';
		} else {
			return $template;
		}
		$theme = locate_template( array( $file ) );
		return $theme ? $theme : VDE_DIR . 'templates/' . $file;
	},
	20
);
