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

			// Works built with Elementor show their Elementor content here.
			if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor() ) {
				the_content();
			}

			echo vde_next_work(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</main>
		<?php
	endwhile;
endif;

get_footer();
