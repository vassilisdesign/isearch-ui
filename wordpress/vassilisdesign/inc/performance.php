<?php
/**
 * Performance and privacy defaults: removes WordPress and Elementor output the site does not use,
 * and keeps all fonts local (no requests to Google, which also matters for GDPR).
 *
 * Each block is independent; remove any you need.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

/* ---------- WordPress core ---------- */

add_action(
	'init',
	function () {
		// Emoji detection script and styles.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );

		// oEmbed discovery and host JS (the site does not embed or get embedded).
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );

		// Header clutter.
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'feed_links', 2 ); // No blog, so no feed links.
		remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
	}
);

// Comments are not used anywhere on the site.
add_action(
	'init',
	function () {
		foreach ( array( 'post', 'page', 'works' ) as $type ) {
			remove_post_type_support( $type, 'comments' );
			remove_post_type_support( $type, 'trackbacks' );
		}
	},
	100
);
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );

add_action(
	'wp_enqueue_scripts',
	function () {
		// Block editor styles only where blocks are actually used (legal pages are written with blocks).
		if ( ! is_singular() || ! has_blocks( get_queried_object_id() ) ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
			wp_dequeue_style( 'global-styles' );
			wp_dequeue_style( 'classic-theme-styles' );
		}
		// Hello's own header/footer styles and menu script: the theme ships its own header and footer.
		wp_dequeue_style( 'hello-elementor-header-footer' );
		wp_dequeue_script( 'hello-theme-frontend' );
		// Dashicons only for logged-in users (admin bar).
		if ( ! is_user_logged_in() ) {
			wp_dequeue_style( 'dashicons' );
		}
		// jQuery Migrate is not needed by anything on the front end.
		if ( ! is_admin() && wp_script_is( 'jquery', 'registered' ) ) {
			$jq = wp_scripts()->registered['jquery'];
			if ( $jq && $jq->deps ) {
				$jq->deps = array_diff( $jq->deps, array( 'jquery-migrate' ) );
			}
		}
	},
	100
);

// Remove global styles' inline SVG filters (duotone) output in the footer.
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

// Slow down the Heartbeat API in the admin (saves server work), and stop it on the front end.
add_filter(
	'heartbeat_settings',
	function ( $settings ) {
		$settings['interval'] = 60;
		return $settings;
	}
);
add_action(
	'init',
	function () {
		if ( ! is_admin() ) {
			wp_deregister_script( 'heartbeat' );
		}
	},
	1
);

/* ---------- Elementor ---------- */

/**
 * True when the current page renders Elementor content: a page or Work built with Elementor,
 * the Elementor preview, or an Elementor Pro Theme Builder template (header, footer, single, archive)
 * that applies to this page.
 */
function vd_page_uses_elementor() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$elementor = \Elementor\Plugin::$instance;
	if ( $elementor->preview->is_preview_mode() ) {
		return true;
	}
	if ( is_singular() ) {
		$document = $elementor->documents->get( get_queried_object_id() );
		if ( $document && $document->is_built_with_elementor() ) {
			return true;
		}
	}
	if ( function_exists( 'elementor_location_exits' ) ) {
		foreach ( array( 'header', 'footer', 'single', 'archive' ) as $location ) {
			if ( elementor_location_exits( $location, true ) ) {
				return true;
			}
		}
	}
	return false;
}

// Pages without Elementor content (legal pages, Works archive, Work pages) skip Elementor's CSS (~55 KB).
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! class_exists( '\Elementor\Plugin' ) || vd_page_uses_elementor() ) {
			return;
		}
		wp_dequeue_style( 'elementor-frontend' );
		wp_dequeue_style( 'elementor-post-' . (int) get_option( 'elementor_active_kit' ) );
		wp_dequeue_style( 'elementor-icons' );
	},
	999
);

// No Google Fonts from Elementor: the theme self-hosts Commissioner (privacy + speed).
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		$elementor = \Elementor\Plugin::$instance;
		if ( $elementor->preview->is_preview_mode() || ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) ) {
			return;
		}
		// Elementor's icon font is not used by the theme's widgets or the form.
		wp_dequeue_style( 'elementor-icons' );
	},
	100
);
