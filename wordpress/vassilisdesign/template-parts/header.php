<?php
/**
 * Site header (replaces Hello's). An Elementor Pro Theme Builder header, if created, takes over automatically.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

$vd_w1 = vd_opt( 'vd_wordmark_1' );
$vd_w2 = vd_opt( 'vd_wordmark_2' );
?>
<a class="vd-skip" href="#content"><?php esc_html_e( 'Skip to content', 'vassilisdesign' ); ?></a>
<header class="vd-header">
	<div class="vd-wrap vd-header-inner">
		<a class="vd-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php echo esc_html( $vd_w1 ); ?><span class="vd-brand-break"></span><?php echo esc_html( $vd_w2 ); ?><span class="vd-stop">.</span>
		</a>
		<nav class="vd-nav" aria-label="<?php esc_attr_e( 'Main', 'vassilisdesign' ); ?>">
			<?php
			if ( has_nav_menu( 'vd-primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'vd-primary',
						'container'      => false,
						'items_wrap'     => '<ul class="vd-menu" role="list">%3$s</ul>',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			} else {
				$vd_home = home_url( '/' );
				printf(
					'<ul class="vd-menu" role="list"><li><a href="%1$s">%2$s</a></li><li><a href="%3$s">%4$s</a></li><li><a href="%5$s">%6$s</a></li></ul>',
					esc_url( get_post_type_archive_link( 'works' ) ),
					esc_html__( 'Work', 'vassilisdesign' ),
					esc_url( $vd_home . '#studio' ),
					esc_html__( 'Studio', 'vassilisdesign' ),
					esc_url( $vd_home . '#contact' ),
					esc_html__( 'Contact', 'vassilisdesign' )
				);
			}
			?>
		</nav>
		<?php if ( vd_opt( 'vd_clock_name' ) ) : ?>
			<p class="vd-clock"><?php echo esc_html( vd_opt( 'vd_clock_name' ) ); ?> <time data-vd-clock data-tz="<?php echo esc_attr( vd_opt( 'vd_clock_tz' ) ); ?>"></time></p>
		<?php endif; ?>
		<?php
		// Language switch, shown only when Polylang is active.
		if ( function_exists( 'pll_the_languages' ) ) {
			$vd_langs = pll_the_languages( array( 'raw' => 1, 'hide_current' => 1, 'hide_if_empty' => 0 ) );
			foreach ( (array) $vd_langs as $vd_lang ) {
				printf(
					'<a class="vd-lang" href="%1$s" hreflang="%2$s" lang="%2$s" aria-label="%3$s">%4$s</a>',
					esc_url( $vd_lang['url'] ),
					esc_attr( $vd_lang['locale'] ? str_replace( '_', '-', $vd_lang['locale'] ) : $vd_lang['slug'] ),
					esc_attr( $vd_lang['name'] ),
					esc_html( 'el' === $vd_lang['slug'] ? 'ΕΛ' : strtoupper( $vd_lang['slug'] ) )
				);
			}
		}
		?>
	</div>
</header>
