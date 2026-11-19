<?php
/**
 * Single Work. An Elementor Pro Theme Builder "Single" template for Works, if created, takes over.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) :
	while ( have_posts() ) :
		the_post();
		?>
		<main id="content" class="vd-main vd-case" tabindex="-1">
			<?php
			echo vde_work_header(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo vde_work_intro(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo vde_work_gallery(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo vde_work_elementor_content( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- printed by the intro already; here for safety.

			echo vde_next_work(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</main>
		<?php
	endwhile;
endif;

get_footer();
