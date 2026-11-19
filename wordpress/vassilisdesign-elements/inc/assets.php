<?php
/**
 * Front-end assets: the design system stylesheet (tokens, fonts, widgets, Works pages),
 * and the deferred motion scripts (GSAP, self-hosted).
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** True while the page is shown inside the Elementor editor (its preview frame). */
function vde_is_elementor_preview() {
	static $preview = null;
	if ( null === $preview ) {
		$preview = isset( $_GET['elementor-preview'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			|| ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() );
	}
	return $preview;
}

// Image sizes for Work tiles (4:5) and wide images.
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'vd-tile', 800, 1000, false );
		add_image_size( 'vd-wide', 1800, 1200, false );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$css_ver = VDE_VERSION . '.' . filemtime( VDE_DIR . 'assets/css/elements.css' );
		$js_ver  = VDE_VERSION . '.' . filemtime( VDE_DIR . 'assets/js/elements.js' );

		wp_enqueue_style( 'vde-elements', VDE_URI . 'assets/css/elements.css', array(), $css_ver );

		// Inside the Elementor editor nothing animates, so the motion libraries are not loaded there.
		if ( vde_is_elementor_preview() ) {
			wp_enqueue_script( 'vde-elements', VDE_URI . 'assets/js/elements.js', array(), $js_ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
			return;
		}

		wp_enqueue_script( 'vde-gsap', VDE_URI . 'assets/vendor/gsap.min.js', array(), '3.15.1', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_enqueue_script( 'vde-scrolltrigger', VDE_URI . 'assets/vendor/ScrollTrigger.min.js', array( 'vde-gsap' ), '3.15.1', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_enqueue_script( 'vde-elements', VDE_URI . 'assets/js/elements.js', array( 'vde-gsap', 'vde-scrolltrigger' ), $js_ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	},
	15
);

// Preload the Latin font file (the Greek file loads only when Greek text appears).
add_action(
	'wp_head',
	function () {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( VDE_URI . 'assets/fonts/commissioner-latin-wght-normal.woff2' ) );
	},
	1
);

/*
 * Runs before first paint and owns the page intro: hides the parts that animate in (vd-intro), starts the
 * CSS sequence as soon as the headline font is ready, at most 0.9s in (vd-play), and ends it. Nothing here
 * waits for the deferred scripts, so a cache plugin delaying JavaScript cannot hold the intro back.
 * Skipped for reduced motion; Work tiles are shown after 4s if the scroll scripts never run.
 */
add_action(
	'wp_head',
	function () {
		if ( vde_is_elementor_preview() ) {
			return; // Never hide content inside the Elementor editor.
		}
		?>
<script<?php echo vde_no_delay_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>(function(d,w){var c=d.documentElement.classList;c.add('vd-js');if(w.matchMedia&&w.matchMedia('(prefers-reduced-motion: reduce)').matches)return;c.add('vd-intro','vd-motion');try{if(sessionStorage.getItem('vd-wipe'))c.add('vd-is-wiping')}catch(e){}var on=0;function play(){if(on)return;on=1;c.add('vd-play');setTimeout(function(){c.remove('vd-intro','vd-is-wiping')},2400)}setTimeout(play,900);(function find(){if(on)return;var t=d.querySelector('.vd-wordmark,.vd-case-title');if(!t){if(d.readyState==='loading')return requestAnimationFrame(find);return play()}if(!d.fonts||!d.fonts.load)return play();var s=getComputedStyle(t);d.fonts.load(s.fontStyle+' '+s.fontWeight+' 1em '+s.fontFamily).then(play,play)})();setTimeout(function(){if(!w.__vd)c.remove('vd-motion')},4000)})(document,window);</script>
		<?php
	},
	2
);

// The design system's base styles are scoped to body.vd.
add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'vd';
		return $classes;
	}
);

// The page wipe overlay, printed once (the theme footer no longer needs to include it).
add_action(
	'wp_footer',
	function () {
		echo '<div class="vd-wipe" aria-hidden="true"></div>';
	},
	5
);
