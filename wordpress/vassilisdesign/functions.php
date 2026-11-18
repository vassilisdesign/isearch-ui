<?php
/**
 * VASSILISDESIGN child theme for Hello Elementor: header, footer, text pages and performance defaults.
 * Works, widgets, fonts and the design system come from the VASSILISDESIGN Elements plugin.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

define( 'VD_VERSION', '2.0.0' );
define( 'VD_DIR', get_stylesheet_directory() );
define( 'VD_URI', get_stylesheet_directory_uri() );

require VD_DIR . '/inc/helpers.php';
require VD_DIR . '/inc/setup.php';
require VD_DIR . '/inc/assets.php';
require VD_DIR . '/inc/performance.php';

// The theme is designed to run with the plugin.
add_action(
	'admin_notices',
	function () {
		if ( defined( 'VDE_VERSION' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p><strong>VASSILISDESIGN</strong>: ' . esc_html__( 'install and activate the VASSILISDESIGN Elements plugin (Plugins → Add New → Upload Plugin). It provides the Works, the VD widgets, the fonts and the design system.', 'vassilisdesign' ) . '</p></div>';
	}
);
