<?php
/**
 * Works archive (/works/) and Sector pages. An Elementor Pro Theme Builder "Archive" template
 * for Works (created by the theme setup) takes over when present.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) :
	?>
	<main id="content" class="vd-main vd-archive" tabindex="-1">
		<?php echo vd_works_archive(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</main>
	<?php
endif;

get_footer();
