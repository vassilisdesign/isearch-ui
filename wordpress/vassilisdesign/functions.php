<?php
/**
 * VASSILISDESIGN child theme for Hello Elementor.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

define( 'VD_VERSION', '1.1.0' );
define( 'VD_DIR', get_stylesheet_directory() );
define( 'VD_URI', get_stylesheet_directory_uri() );

require VD_DIR . '/inc/setup.php';
require VD_DIR . '/inc/works.php';
require VD_DIR . '/inc/customizer.php';
require VD_DIR . '/inc/legal.php';
require VD_DIR . '/inc/template-functions.php';
require VD_DIR . '/inc/assets.php';
require VD_DIR . '/inc/performance.php';
require VD_DIR . '/inc/elementor.php';

if ( is_admin() ) {
	require VD_DIR . '/inc/admin-setup.php';
}
