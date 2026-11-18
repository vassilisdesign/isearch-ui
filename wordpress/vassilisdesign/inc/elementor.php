<?php
/**
 * Elementor integration: widget category, theme widgets and the recommended performance settings.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'elementor/elements/categories_registered',
	function ( $manager ) {
		$manager->add_category(
			'vassilisdesign',
			array(
				'title' => 'VASSILISDESIGN',
				'icon'  => 'eicon-font',
			)
		);
	}
);

add_action(
	'elementor/widgets/register',
	function ( $widgets_manager ) {
		require_once VD_DIR . '/inc/widgets/widgets.php';
		foreach ( vd_widget_classes() as $class ) {
			$widgets_manager->register( new $class() );
		}
	}
);

/**
 * Elementor settings that keep the site light. Applied by the one-click setup
 * (Appearance → VASSILISDESIGN setup); each one can be changed back in Elementor → Settings.
 */
function vd_recommended_elementor_options() {
	return array(
		'elementor_disable_color_schemes'      => 'yes',     // Use the theme colours, not Elementor's defaults.
		'elementor_disable_typography_schemes' => 'yes',     // Use the theme font, not Elementor's defaults.
		'elementor_google_font'                => '1',       // Google Fonts allowed for Elementor's global fonts…
		'elementor_local_google_fonts'         => '1',       // …but only hosted on this server, never loaded from Google (GDPR).
		'elementor_font_display'               => 'swap',
		'elementor_css_print_method'           => 'external', // Cacheable CSS files.
		'elementor_load_fa4_shim'              => '',        // No Font Awesome 4 compatibility layer.
		'elementor_optimized_image_loading'    => '1',
		'elementor_lazy_load_background_images' => '1',
		'elementor_experiment-container'       => 'active',  // Flexbox containers (fewer DOM wrappers).
		'elementor_experiment-e_optimized_markup' => 'active', // Leaner widget markup.
		'elementor_experiment-e_optimized_css_files' => 'active', // Load only the CSS each page uses.
		'elementor_experiment-e_font_icon_svg' => 'active',  // Inline SVG icons instead of icon fonts.
	);
}

function vd_apply_elementor_options() {
	foreach ( vd_recommended_elementor_options() as $key => $value ) {
		update_option( $key, $value );
	}
	$cpt = (array) get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	update_option( 'elementor_cpt_support', array_values( array_unique( array_merge( $cpt, array( 'page', 'works' ) ) ) ) );

	// Disable Elementor's lightbox site-wide (the theme does not use it; saves a script).
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( $kit_id ) {
		$kit                              = (array) get_post_meta( $kit_id, '_elementor_page_settings', true );
		$kit['global_image_lightbox']     = '';
		$kit['container_width']           = array( 'unit' => 'px', 'size' => 1760, 'sizes' => array() );
		$kit['space_between_widgets']     = array( 'column' => '0', 'row' => '0', 'isLinked' => true, 'unit' => 'px', 'size' => 0 );
		$kit['page_title_selector']       = 'h1.entry-title';
		update_post_meta( $kit_id, '_elementor_page_settings', $kit );
	}
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}
