<?php
/**
 * Plugin Name:       VASSILISDESIGN Elements
 * Plugin URI:        https://vassilisdesign.gr
 * Description:       Works portfolio (custom post type, Sectors, fields), the VD Elementor widgets, Works page layouts, studio and legal details, and the one-click site setup for VASSILISDESIGN.
 * Version:           2.8.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            VASSILISDESIGN
 * Author URI:        https://vassilisdesign.gr
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       vassilisdesign-elements
 * Domain Path:       /languages
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

define( 'VDE_VERSION', '2.8.0' );
define( 'VDE_FILE', __FILE__ );
define( 'VDE_DIR', plugin_dir_path( __FILE__ ) );
define( 'VDE_URI', plugin_dir_url( __FILE__ ) );

/*
 * Theme 1.x still contains everything this plugin provides. Running both would register the Works
 * and the widgets twice, so the plugin waits until the theme is updated to 2.0.
 */
function vde_old_theme_active() {
	$theme = wp_get_theme( get_stylesheet() );
	return 'vassilisdesign' === get_stylesheet() && version_compare( (string) $theme->get( 'Version' ), '2.0.0', '<' );
}

if ( vde_old_theme_active() ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-warning"><p><strong>VASSILISDESIGN Elements</strong>: ' . esc_html__( 'update the VASSILISDESIGN theme to version 2.0 (Appearance → Themes → Add New → Upload Theme). The plugin starts automatically after the update; your Works and settings are kept.', 'vassilisdesign-elements' ) . '</p></div>';
		}
	);
	return;
}

add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'vassilisdesign-elements', false, dirname( plugin_basename( VDE_FILE ) ) . '/languages' );
	}
);

require VDE_DIR . 'inc/works.php';
require VDE_DIR . 'inc/settings.php';
require VDE_DIR . 'inc/legal.php';
require VDE_DIR . 'inc/template-functions.php';
require VDE_DIR . 'inc/assets.php';
require VDE_DIR . 'inc/templates.php';
require VDE_DIR . 'inc/elementor.php';

if ( is_admin() ) {
	require VDE_DIR . 'inc/admin-setup.php';
}

// Works URLs (/works/, /works/sector/...) need fresh rewrite rules after activation.
register_activation_hook(
	__FILE__,
	function () {
		update_option( 'vde_flush_rewrites', 1 );
	}
);
add_action(
	'init',
	function () {
		if ( get_option( 'vde_flush_rewrites' ) ) {
			delete_option( 'vde_flush_rewrites' );
			flush_rewrite_rules();
		}
	},
	99
);
