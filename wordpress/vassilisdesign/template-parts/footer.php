<?php
/**
 * Site footer (replaces Hello's). An Elementor Pro Theme Builder footer, if created, takes over automatically.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="vd-footer">
	<div class="vd-wrap vd-footer-grid">
		<a class="vd-footer-mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php echo esc_html( vd_opt( 'vd_wordmark_1' ) ); ?><br><?php echo esc_html( vd_opt( 'vd_wordmark_2' ) ); ?><span class="vd-stop">.</span>
		</a>
		<p class="vd-footer-col"><?php echo vd_lines( vd_opt( 'vd_footer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<p class="vd-footer-col"><?php echo vd_contact_lines(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<nav class="vd-footer-col vd-footer-legal" aria-label="<?php esc_attr_e( 'Legal', 'vassilisdesign' ); ?>">
			<?php
			if ( has_nav_menu( 'vd-legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'vd-legal',
						'container'      => false,
						'items_wrap'     => '<ul role="list">%3$s</ul>',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			} elseif ( get_privacy_policy_url() ) {
				echo '<ul role="list"><li>' . get_the_privacy_policy_link() . '</li></ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<?php
			// Re-opens the Complianz consent banner. The class is Complianz's documented trigger.
			if ( defined( 'cmplz_version' ) || class_exists( 'COMPLIANZ' ) ) :
				?>
				<button type="button" class="vd-link-button cmplz-show-banner"><?php esc_html_e( 'Cookie settings', 'vassilisdesign' ); ?></button>
			<?php endif; ?>
		</nav>
		<p class="vd-footer-rights">&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></p>
		<a class="vd-footer-top vd-square" href="#top" aria-label="<?php esc_attr_e( 'Back to top', 'vassilisdesign' ); ?>"><?php echo vd_arrow( 'down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
	</div>
</footer>
<div class="vd-wipe" aria-hidden="true"></div>
