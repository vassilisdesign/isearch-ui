<?php
/**
 * Works archive (/works/) and Sector pages. An Elementor Pro Theme Builder "Archive" template, if created, takes over.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) :
	$vd_title = is_tax() ? single_term_title( '', false ) : __( 'Works', 'vassilisdesign' );
	$vd_terms = get_terms( array( 'taxonomy' => 'work_sector', 'hide_empty' => true ) );
	?>
	<main id="content" class="vd-main vd-archive" tabindex="-1">
		<div class="vd-wrap vd-archive-head">
			<?php echo vd_display_title( $vd_title, 'h1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $vd_terms && ! is_wp_error( $vd_terms ) ) : ?>
				<nav class="vd-filter" aria-label="<?php esc_attr_e( 'Sectors', 'vassilisdesign' ); ?>">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>"<?php echo is_post_type_archive( 'works' ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'vassilisdesign' ); ?></a>
					<?php foreach ( $vd_terms as $vd_term ) : ?>
						<a href="<?php echo esc_url( get_term_link( $vd_term ) ); ?>"<?php echo is_tax( 'work_sector', $vd_term->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $vd_term->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
		<div class="vd-wrap">
			<?php if ( have_posts() ) : ?>
				<ul class="vd-tiles" role="list">
					<?php
					while ( have_posts() ) :
						the_post();
						echo vd_work_tile( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					endwhile;
					?>
				</ul>
				<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
			<?php else : ?>
				<p class="vd-empty"><?php esc_html_e( 'No Works yet.', 'vassilisdesign' ); ?></p>
			<?php endif; ?>
		</div>
	</main>
	<?php
endif;

get_footer();
