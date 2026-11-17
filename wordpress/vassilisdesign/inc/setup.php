<?php
/**
 * Theme setup: supports, menus, and Hello Elementor defaults.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_child_theme_textdomain( 'vassilisdesign', VD_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );

		// Tile (4:5) and wide images for Works.
		add_image_size( 'vd-tile', 800, 1000, false );
		add_image_size( 'vd-wide', 1800, 1200, false );

		register_nav_menus(
			array(
				'vd-primary' => __( 'Header menu', 'vassilisdesign' ),
				'vd-legal'   => __( 'Footer legal menu', 'vassilisdesign' ),
			)
		);
	},
	20
);

/*
 * Hello Elementor: the child theme ships its own reset, typography, header and footer,
 * so Hello's own CSS, skip link and page titles on Elementor pages are switched off.
 */
add_filter( 'hello_elementor_enqueue_style', '__return_false' );       // Hello reset.css.
add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' ); // Hello theme.css.
add_filter( 'hello_elementor_enable_skip_link', '__return_false' );    // Our header prints its own.
add_filter( 'hello_elementor_description_meta_tag', '__return_false' ); // Leave meta descriptions to an SEO plugin if used.

// Hide the automatic page title on pages built with Elementor (the design has its own headings).
add_filter(
	'hello_elementor_page_title',
	function ( $show ) {
		if ( is_singular() && class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
			if ( $document && $document->is_built_with_elementor() ) {
				return false;
			}
		}
		return $show;
	}
);

// Use the theme header and footer (template-parts/) rather than Hello's experimental dynamic ones.
add_filter( 'hello_elementor_header_footer', '__return_true' );

// In-page links (#studio, #contact) are never the "current" menu item, even on the homepage.
add_filter(
	'nav_menu_css_class',
	function ( $classes, $item ) {
		if ( false !== strpos( (string) $item->url, '#' ) ) {
			$classes = array_diff( $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor' ) );
		}
		return $classes;
	},
	10,
	2
);
add_filter(
	'nav_menu_link_attributes',
	function ( $atts, $item ) {
		if ( false !== strpos( (string) $item->url, '#' ) ) {
			unset( $atts['aria-current'] );
		}
		return $atts;
	},
	10,
	2
);

add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'vd';
		return $classes;
	}
);
