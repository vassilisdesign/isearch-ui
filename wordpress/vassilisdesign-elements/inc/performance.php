<?php
/**
 * Loading speed: WebP copies of the large background images, the hero image preloaded from <head>,
 * no duplicate Commissioner font from Elementor, and the plugin's scripts kept out of cache-plugin
 * "delay until interaction" (the intro must run on load, not on the visitor's first tap).
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/* ---------- New uploads: sizes made as WebP ---------- */

/**
 * When a JPEG or PNG is uploaded, WordPress makes the thumbnails, tiles and wide sizes as WebP
 * instead (a 768px photo tile drops from about 1MB as PNG to under 100KB). The uploaded original is
 * kept as it is, so nothing is lost. Skipped when the server cannot write WebP.
 */
add_filter(
	'image_editor_output_format',
	function ( $formats ) {
		static $webp = null, $checking = false;
		if ( $checking ) { // wp_image_editor_supports() runs this filter again: answer without WebP while asking
			return $formats;
		}
		if ( null === $webp ) {
			$checking = true;
			$webp     = wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
			$checking = false;
		}
		if ( ! $webp ) {
			return $formats;
		}
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
		return $formats;
	}
);

// WebP at quality 88 (WordPress uses 82): no visible loss, even full screen in the lightbox.
add_filter(
	'wp_editor_set_quality',
	function ( $quality, $mime_type = '' ) {
		return 'image/webp' === $mime_type ? max( (int) $quality, 88 ) : $quality;
	},
	10,
	2
);

/* ---------- WebP background images ---------- */

/**
 * URL of a WebP copy of an image (made once, at most 1800px wide, then reused). Falls back to the
 * original when the server cannot write WebP. A 700KB PNG photo usually becomes 60-120KB.
 */
function vde_webp_url( $attachment_id, $size = 'vd-wide' ) {
	$attachment_id = (int) $attachment_id;
	$src           = wp_get_attachment_image_src( $attachment_id, $size );
	if ( ! $src ) {
		$src = wp_get_attachment_image_src( $attachment_id, 'full' );
	}
	if ( ! $src ) {
		return '';
	}
	$url  = $src[0];
	$mime = get_post_mime_type( $attachment_id );
	if ( ! in_array( $mime, array( 'image/jpeg', 'image/png' ), true ) || ! wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		return $url;
	}

	$original = get_attached_file( $attachment_id );
	if ( ! $original ) {
		return $url;
	}
	$file = trailingslashit( dirname( $original ) ) . wp_basename( $url ); // the file behind $url (a sub-size or the original)
	if ( ! file_exists( $file ) ) {
		$file = $original;
		$url  = wp_get_attachment_url( $attachment_id );
	}
	if ( ! preg_match( '/\.(jpe?g|png)$/i', $file ) ) {
		return $url; // already WebP (or another format WordPress made light)
	}
	$webp     = preg_replace( '/\.(jpe?g|png)$/i', '', $file ) . '-vd.webp';
	$webp_url = trailingslashit( dirname( $url ) ) . wp_basename( $webp );

	if ( file_exists( $webp ) && filemtime( $webp ) >= filemtime( $file ) ) {
		return $webp_url;
	}
	// Do not retry a failed conversion on every page view.
	if ( get_transient( 'vde_webp_fail_' . $attachment_id ) ) {
		return $url;
	}
	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) ) {
		set_transient( 'vde_webp_fail_' . $attachment_id, 1, DAY_IN_SECONDS );
		return $url;
	}
	$dims = $editor->get_size();
	if ( ! empty( $dims['width'] ) && $dims['width'] > 1800 ) {
		$editor->resize( 1800, null );
	}
	$editor->set_quality( 80 );
	$saved = $editor->save( $webp, 'image/webp' );
	if ( is_wp_error( $saved ) || empty( $saved['path'] ) ) {
		set_transient( 'vde_webp_fail_' . $attachment_id, 1, DAY_IN_SECONDS );
		return $url;
	}
	// Keep the WebP only if it is actually smaller.
	if ( filesize( $saved['path'] ) >= filesize( $file ) ) {
		wp_delete_file( $saved['path'] );
		set_transient( 'vde_webp_fail_' . $attachment_id, 1, WEEK_IN_SECONDS );
		return $url;
	}
	$made   = (array) get_post_meta( $attachment_id, '_vde_webp', true );
	$made[] = wp_basename( $saved['path'] );
	update_post_meta( $attachment_id, '_vde_webp', array_values( array_unique( array_filter( $made ) ) ) );
	return $webp_url;
}

// Remove the WebP copies together with the image.
add_action(
	'delete_attachment',
	function ( $attachment_id ) {
		$made     = (array) get_post_meta( $attachment_id, '_vde_webp', true );
		$original = get_attached_file( $attachment_id );
		if ( ! $original ) {
			return;
		}
		foreach ( array_filter( $made ) as $name ) {
			$path = trailingslashit( dirname( $original ) ) . wp_basename( $name );
			if ( '-vd.webp' === substr( $path, -8 ) && file_exists( $path ) ) {
				wp_delete_file( $path );
			}
		}
	}
);

// New uploads: WordPress makes the smaller sizes as WebP too (the original file is kept as uploaded).
add_filter(
	'image_editor_output_format',
	function ( $formats ) {
		if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
			$formats['image/jpeg'] = 'image/webp';
			$formats['image/png']  = 'image/webp';
		}
		return $formats;
	}
);

/* ---------- Hero image preloaded from <head> ---------- */

/** The image of the first VD Hero / VD Hero image widget on the page being viewed, if any. */
function vde_page_hero_image_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	$id = 0;
	if ( ! is_singular() ) {
		return $id;
	}
	$raw = get_post_meta( get_queried_object_id(), '_elementor_data', true );
	if ( ! $raw || ( false === strpos( $raw, '"vd-hero"' ) && false === strpos( $raw, '"vd-hero-image"' ) ) ) {
		return $id;
	}
	$data = is_array( $raw ) ? $raw : json_decode( $raw, true );
	$walk = function ( $elements ) use ( &$walk ) {
		foreach ( (array) $elements as $el ) {
			if ( isset( $el['widgetType'] ) && in_array( $el['widgetType'], array( 'vd-hero', 'vd-hero-image' ), true ) ) {
				return isset( $el['settings']['image']['id'] ) ? (int) $el['settings']['image']['id'] : -1;
			}
			if ( ! empty( $el['elements'] ) ) {
				$found = $walk( $el['elements'] );
				if ( $found ) {
					return $found;
				}
			}
		}
		return 0;
	};
	$id = (int) $walk( $data );
	return $id;
}

add_action(
	'wp_head',
	function () {
		if ( vde_is_elementor_preview() ) {
			return;
		}
		$id = vde_page_hero_image_id();
		if ( $id > 0 ) {
			$url = vde_webp_url( $id );
			if ( $url ) {
				printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $url ) );
			}
		}
	},
	1
);

/* ---------- Adobe Fonts: open the connections early ---------- */

/*
 * With an Adobe Fonts kit (Elementor Pro → Settings → Integrations), the page needs two Adobe servers before
 * the wordmark can show: p.typekit.net (the kit's @import) and use.typekit.net (the font files). Connecting
 * to both at the top of <head>, in parallel with everything else, saves one or two round trips on phones.
 * Nothing about the fonts, intro or design changes.
 */
add_action(
	'wp_head',
	function () {
		$kit = (string) get_option( 'elementor_typekit-kit-id', '' );
		if ( '' === trim( $kit ) || ! apply_filters( 'vde_preconnect_adobe_fonts', true ) ) {
			return;
		}
		echo '<link rel="preconnect" href="https://use.typekit.net" crossorigin>' . "\n";
		echo '<link rel="preconnect" href="https://p.typekit.net">' . "\n";
	},
	0
);

/* ---------- No duplicate Commissioner from Elementor (the plugin already serves it) ---------- */

add_filter(
	'style_loader_tag',
	function ( $tag, $handle ) {
		return in_array( $handle, array( 'elementor-gf-local-commissioner', 'elementor-gf-commissioner' ), true ) ? '' : $tag;
	},
	10,
	2
);

/* ---------- Keep the motion scripts out of "delay JavaScript until interaction" ---------- */

/*
 * Cache plugins (LiteSpeed "Delay JS", WP Rocket, ...) otherwise hold these back until the first tap or scroll:
 * the page then shows without its intro and replays it on that first touch.
 */
function vde_no_delay_attrs() {
	return ' data-no-optimize="1" data-no-defer="1" data-no-minify="1" nowprocket data-cfasync="false"';
}

add_filter(
	'script_loader_tag',
	function ( $tag, $handle ) {
		if ( in_array( $handle, array( 'vde-gsap', 'vde-scrolltrigger', 'vde-elements' ), true ) && false === strpos( $tag, 'data-no-optimize' ) ) {
			$tag = str_replace( '<script ', '<script' . vde_no_delay_attrs() . ' ', $tag );
		}
		return $tag;
	},
	10,
	2
);

// LiteSpeed's own exclusion lists, for versions that ignore the attributes.
add_filter(
	'litespeed_optm_js_defer_exc',
	function ( $list ) {
		return array_merge( (array) $list, array( 'vassilisdesign-elements', 'vd-intro' ) );
	}
);
add_filter(
	'litespeed_optimize_js_excludes',
	function ( $list ) {
		return array_merge( (array) $list, array( 'vassilisdesign-elements', 'vd-intro' ) );
	}
);

/*
 * LiteSpeed "Generate UCSS" keeps only the CSS a page uses when it is first scanned. States added later by
 * script (menu open, lightbox, reveals, intro) would be removed, so they are always kept.
 */
add_filter(
	'litespeed_ucss_whitelist',
	function ( $list ) {
		$keep = array( '.vdn', '.vdn-', '.is-open', '.is-collapsed', '.vdn-flip', '.vdn-lock', '.vd-lb', '.vd-lb-', '.vd-in', '.vd-play', '.vd-intro', '.vd-motion', '.vd-is-wiping', '.vd-js', '.vd-zoom' );
		return array_values( array_unique( array_merge( (array) $list, $keep ) ) );
	}
);


/* ---------- No jQuery Migrate on the public site ---------- */

/*
 * jQuery Migrate only helps very old plugins that still use removed jQuery functions; Elementor, Complianz
 * and this plugin do not. Kept in wp-admin. To bring it back: add_filter( 'vde_jquery_migrate', '__return_true' ).
 */
add_action(
	'wp_default_scripts',
	function ( $scripts ) {
		if ( is_admin() || ! isset( $scripts->registered['jquery'] ) || apply_filters( 'vde_jquery_migrate', false ) ) {
			return;
		}
		$scripts->registered['jquery']->deps = array_values( array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) ) );
	}
);

/* ---------- Cache warmer: pages cached before the first visitor ---------- */

/*
 * A page that is not in the LiteSpeed cache takes 1-1.7s to start; from the cache about 0.2s. The cache is
 * emptied when content changes, so the next visitor pays the wait. After a Work or page is saved (and once a
 * day) this opens the main pages itself, a few at a time in the background, so they are cached again.
 * Off with: add_filter( 'vde_cache_warmer', '__return_false' ).
 */
function vde_warm_urls() {
	$urls = array( home_url( '/' ) );
	$arch = get_post_type_archive_link( 'works' );
	if ( $arch ) {
		$urls[] = $arch;
	}
	foreach ( array( 'works', 'page' ) as $type ) {
		$ids = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'fields'         => 'ids',
				'orderby'        => 'menu_order date',
				'no_found_rows'  => true,
			)
		);
		foreach ( $ids as $id ) {
			$urls[] = get_permalink( $id );
		}
	}
	foreach ( array( 'work_sector', 'work_service' ) as $tax ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $tax,
				'hide_empty' => true,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					$urls[] = $link;
				}
			}
		}
	}
	return array_values( array_unique( array_filter( $urls ) ) );
}

function vde_schedule_warm( $delay = 90 ) {
	if ( ! apply_filters( 'vde_cache_warmer', true ) || wp_next_scheduled( 'vde_warm_cache' ) ) {
		return;
	}
	delete_option( 'vde_warm_queue' ); // start again from the top with a fresh list
	wp_schedule_single_event( time() + $delay, 'vde_warm_cache' );
}

add_action(
	'vde_warm_cache',
	function () {
		$queue = get_option( 'vde_warm_queue' );
		if ( ! is_array( $queue ) ) {
			$queue = vde_warm_urls();
		}
		$batch = array_splice( $queue, 0, 6 ); // a few per run, so a run never hits the PHP time limit
		foreach ( $batch as $url ) {
			wp_remote_get(
				$url,
				array(
					'timeout'     => 20,
					'redirection' => 0,
					'user-agent'  => 'Mozilla/5.0 (compatible; VDE-cache-warmer; +' . home_url( '/' ) . ')',
					'headers'     => array( 'Accept' => 'text/html' ),
					'cookies'     => array(), // as a first-time visitor, the version the cache keeps
				)
			);
		}
		if ( $queue ) {
			update_option( 'vde_warm_queue', $queue, false );
			wp_schedule_single_event( time() + 30, 'vde_warm_cache' );
		} else {
			delete_option( 'vde_warm_queue' );
		}
	}
);

add_action(
	'transition_post_status',
	function ( $new_status, $old_status, $post ) {
		if ( ( 'publish' === $new_status || 'publish' === $old_status ) && in_array( $post->post_type, array( 'works', 'page' ), true ) && ! wp_is_post_revision( $post ) ) {
			vde_schedule_warm();
		}
	},
	10,
	3
);

add_action(
	'init',
	function () {
		if ( apply_filters( 'vde_cache_warmer', true ) && ! wp_next_scheduled( 'vde_warm_cache_daily' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'vde_warm_cache_daily' );
		}
	}
);
add_action( 'vde_warm_cache_daily', 'vde_schedule_warm' );
