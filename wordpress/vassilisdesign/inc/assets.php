<?php
/**
 * Front-end assets: one stylesheet, self-hosted fonts, and deferred motion scripts.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		$css_ver = VD_VERSION . '.' . filemtime( VD_DIR . '/assets/css/vd.css' );
		$js_ver  = VD_VERSION . '.' . filemtime( VD_DIR . '/assets/js/vd.js' );

		wp_enqueue_style( 'vd', VD_URI . '/assets/css/vd.css', array(), $css_ver );

		// Motion: GSAP core + ScrollTrigger (self-hosted), deferred, in the footer.
		wp_enqueue_script( 'vd-gsap', VD_URI . '/assets/vendor/gsap.min.js', array(), '3.15.1', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_enqueue_script( 'vd-scrolltrigger', VD_URI . '/assets/vendor/ScrollTrigger.min.js', array( 'vd-gsap' ), '3.15.1', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_enqueue_script( 'vd', VD_URI . '/assets/js/vd.js', array( 'vd-gsap', 'vd-scrolltrigger' ), $js_ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	},
	20
);

// Preload the Latin font file (the Greek file loads only when Greek text appears).
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( VD_URI . '/assets/fonts/commissioner-latin-wght-normal.woff2' )
		);
	},
	1
);

/*
 * Runs before first paint: marks JS as available, prepares the intro (skipped for reduced motion)
 * and never leaves content hidden (4-second failsafe if the scripts do not run).
 */
add_action(
	'wp_head',
	function () {
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return; // Never hide content inside the Elementor editor.
		}
		?>
<script>(function(d,w){var h=d.documentElement;h.classList.add('vd-js');if(w.matchMedia&&w.matchMedia('(prefers-reduced-motion: reduce)').matches)return;h.classList.add('vd-intro');try{if(sessionStorage.getItem('vd-wipe'))h.classList.add('vd-is-wiping')}catch(e){}setTimeout(function(){if(!w.__vd)h.classList.remove('vd-intro','vd-is-wiping')},4000)})(document,window);</script>
		<?php
	},
	2
);

// Make the page itself the scroll target for "Back to top".
add_filter(
	'language_attributes',
	function ( $output ) {
		return $output . ' id="top"';
	}
);
