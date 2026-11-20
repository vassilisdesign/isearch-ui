<?php
/**
 * Markup helpers shared by the theme templates and the Elementor widgets.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

function vde_arrow( $dir = 'right' ) {
	$d = 'down' === $dir ? 'M12 4v15m-6-6 6 6 6-6' : ( 'left' === $dir ? 'M20 12H5m6-6-6 6 6 6' : 'M4 12h15m-6-6 6 6-6 6' );
	return '<svg class="vd-arrow" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="' . $d . '" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
}

/** Lowercase display heading with the red full stop. */
function vde_display_title( $text, $tag = 'h2', $id = '' ) {
	$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'p' ), true ) ? $tag : 'h2';
	return sprintf(
		'<%1$s class="vd-display"%2$s>%3$s<span class="vd-stop" aria-hidden="true">.</span></%1$s>',
		$tag,
		$id ? ' id="' . esc_attr( $id ) . '"' : '',
		esc_html( $text )
	);
}

/** Text with line breaks preserved. */
function vde_lines( $text ) {
	return nl2br( esc_html( trim( (string) $text ) ) );
}

/** Ordered list of all published Work IDs (menu order, then newest). Cached per request. */
function vde_work_ids() {
	static $ids = null;
	if ( null === $ids ) {
		$ids = get_posts(
			array(
				'post_type'      => 'works',
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'fields'         => 'ids',
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'no_found_rows'  => true,
			)
		);
	}
	return $ids;
}

function vde_cover_fit( $post_id ) {
	return 'contain' === get_post_meta( $post_id, 'vd_cover_fit', true ) ? 'contain' : 'cover';
}

/** Portfolio <img>. Lazy by default; pass $eager for the first image on screen. */
function vde_img( $attachment_id, $size = 'large', $eager = false, $alt = null ) {
	if ( ! $attachment_id ) {
		return '';
	}
	$attrs = array(
		'class'    => 'vd-photo-img',
		'decoding' => 'async',
		'loading'  => $eager ? 'eager' : 'lazy',
	);
	if ( $eager ) {
		$attrs['fetchpriority'] = 'high';
	}
	if ( null !== $alt ) {
		$attrs['alt'] = $alt;
	}
	return wp_get_attachment_image( (int) $attachment_id, $size, false, $attrs );
}

/* ---------- Works ---------- */

/** Text shown on the red hover panel: the Excerpt, otherwise the start of the post content. */
function vde_work_hover_text( $post ) {
	$post = get_post( $post );
	if ( has_excerpt( $post ) ) {
		return wp_strip_all_tags( $post->post_excerpt );
	}
	$text = wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) );
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );
	return $text ? wp_trim_words( $text, 32, '…' ) : get_the_title( $post );
}

/**
 * One Work tile.
 *
 * @param WP_Post|int $post     The Work.
 * @param bool        $overlay  Red panel with the Work's text on hover (instead of the red block).
 * @param bool        $captions Title, services and location under the image.
 */
function vde_work_tile( $post, $overlay = false, $captions = true, $ratio = '', $class = '' ) {
	$post  = get_post( $post );
	$fit   = vde_cover_fit( $post->ID );
	$thumb = get_post_thumbnail_id( $post );
	$title = get_the_title( $post );
	ob_start();
	?>
	<li class="vd-tile<?php echo $class ? ' ' . esc_attr( $class ) : ''; ?>"<?php echo $ratio ? ' style="--vd-ar:4 / ' . esc_attr( $ratio ) . '"' : ''; ?>>
		<a class="vd-tile-link" href="<?php echo esc_url( get_permalink( $post ) ); ?>"<?php echo $captions ? '' : ' aria-label="' . esc_attr( $title ) . '"'; ?>>
			<span class="vd-tile-media vd-photo<?php echo 'contain' === $fit ? ' vd-fit-contain' : ''; ?>">
				<?php echo vde_img( $thumb, 'vd-tile', false, '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $overlay ) : ?>
					<span class="vd-red-block vd-red-full" aria-hidden="true"></span>
					<span class="vd-tile-hover-text" aria-hidden="true"><span><?php echo esc_html( vde_work_hover_text( $post ) ); ?></span></span>
				<?php else : ?>
					<span class="vd-red-block" aria-hidden="true"></span>
				<?php endif; ?>
			</span>
			<?php if ( $captions ) : ?>
				<span class="vd-tile-client"><?php echo esc_html( $title ); ?></span>
				<?php if ( $services = get_post_meta( $post->ID, 'vd_services', true ) ) : ?>
					<span class="vd-tile-meta"><?php echo esc_html( $services ); ?></span>
				<?php endif; ?>
				<?php if ( $location = get_post_meta( $post->ID, 'vd_location', true ) ) : ?>
					<span class="vd-tile-meta"><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</a>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Grid of Work tiles.
 *
 * Args: count, sector (slug), ids (hand-picked Work IDs, in order), columns (desktop; empty = the
 * default 4-column tiles), gap (px; empty = default spacing), overlay (red cover with text on hover),
 * captions (text under the image), staggered (varying heights per column; off = uniform).
 */
function vde_works_grid( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'count'     => 8,
			'sector'    => '',
			'ids'       => array(),
			'columns'   => '',
			'gap'       => '',
			'overlay'   => false,
			'captions'  => true,
			'staggered' => true,
			'm_count'   => 0,
			'm_cols'    => 0,
			'm_equal'   => false,
		)
	);
	$query = array(
		'post_type'      => 'works',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $args['count'],
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	$ids = array_filter( array_map( 'absint', (array) $args['ids'] ) );
	if ( $ids ) {
		$query['post__in']       = $ids;
		$query['orderby']        = 'post__in';
		$query['posts_per_page'] = count( $ids );
	} elseif ( $args['sector'] ) {
		$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'work_sector',
				'field'    => 'slug',
				'terms'    => $args['sector'],
			),
		);
	}
	$posts = get_posts( $query );
	if ( ! $posts ) {
		return current_user_can( 'edit_posts' )
			? '<p class="vd-empty">' . esc_html__( 'No Works yet. Add them under Works → Add Work.', 'vassilisdesign-elements' ) . '</p>'
			: '';
	}
	$classes = array( 'vd-tiles' );
	$style   = array();
	$cols    = (int) $args['columns'];
	if ( $cols ) {
		$classes[] = 'vd-tiles-cols';
		$classes[] = $args['staggered'] ? 'vd-tiles-stagger' : 'vd-tiles-grid';
		$style[]   = '--vd-grid-cols:' . max( 1, min( 8, $cols ) );
	}
	if ( '' !== (string) $args['gap'] && null !== $args['gap'] ) {
		$classes[] = 'vd-tiles-gap';
		$style[]   = '--vd-grid-gap:' . max( 0, (int) $args['gap'] ) . 'px';
	}
	if ( $args['overlay'] ) {
		$classes[] = 'vd-tiles-overlay';
	}
	// Phones (under 720px): own column count, equal heights, and a limit on how many Works show.
	if ( (int) $args['m_cols'] ) {
		$classes[] = 'vd-tiles-mcols';
		$style[]   = '--vd-m-cols:' . max( 1, min( 4, (int) $args['m_cols'] ) );
	}
	if ( $args['m_equal'] ) {
		$classes[] = 'vd-tiles-mequal';
	}
	// Staggered: each column gets its own image height, repeated row after row.
	$ratios = array( '5.2', '6.2', '4.6', '5.7', '5', '6.6', '4.9', '5.9' );
	$tiles  = '';
	foreach ( array_values( $posts ) as $i => $post ) {
		$ratio  = ( $cols && $args['staggered'] ) ? $ratios[ $i % $cols ] : '';
		$hide   = ( (int) $args['m_count'] > 0 && $i >= (int) $args['m_count'] ) ? 'vd-tile-mhide' : '';
		$tiles .= vde_work_tile( $post, (bool) $args['overlay'], (bool) $args['captions'], $ratio, $hide );
	}
	return sprintf(
		'<ul class="%s" role="list"%s>%s</ul>',
		esc_attr( implode( ' ', $classes ) ),
		$style ? ' style="' . esc_attr( implode( ';', $style ) ) . '"' : '',
		$tiles
	);
}

/**
 * Works archive: title, Sector filter and tiles from the current archive query.
 * Outside a Works archive (for example in the Elementor editor) it shows the latest Works.
 */
function vde_works_archive() {
	global $wp_query;
	$is_archive = is_post_type_archive( 'works' ) || is_tax( 'work_sector' );
	$query      = $is_archive ? $wp_query : new WP_Query(
		array(
			'post_type'      => 'works',
			'post_status'    => 'publish',
			'posts_per_page' => 24,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
	$title      = is_tax( 'work_sector' ) ? single_term_title( '', false ) : __( 'Works', 'vassilisdesign-elements' );
	$terms      = get_terms( array( 'taxonomy' => 'work_sector', 'hide_empty' => true ) );
	ob_start();
	?>
	<div class="vd-wrap vd-archive-head">
		<?php echo vde_display_title( $title, 'h1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
			<nav class="vd-filter" aria-label="<?php esc_attr_e( 'Sectors', 'vassilisdesign-elements' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>"<?php echo is_post_type_archive( 'works' ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'vassilisdesign-elements' ); ?></a>
				<?php foreach ( $terms as $term ) : ?>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo is_tax( 'work_sector', $term->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $term->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
	<div class="vd-wrap vd-archive-body">
		<?php if ( $query->have_posts() ) : ?>
			<ul class="vd-tiles" role="list">
				<?php
				foreach ( $query->posts as $work ) {
					echo vde_work_tile( $work ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</ul>
			<?php
			if ( $is_archive ) {
				the_posts_pagination( array( 'mid_size' => 1 ) );
			}
			?>
		<?php else : ?>
			<p class="vd-empty"><?php esc_html_e( 'No Works yet.', 'vassilisdesign-elements' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

function vde_scope_items( $post_id ) {
	return array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $post_id, 'vd_scope', true ) ) ) );
}

/** Work page header: meta columns, index number, title and red scope box. */
function vde_work_header( $post = null, $show_back = true ) {
	$post    = get_post( $post );
	$ids     = vde_work_ids();
	$pos     = array_search( $post->ID, $ids, true );
	$sectors = get_the_terms( $post, 'work_sector' );
	$scope   = vde_scope_items( $post->ID );
	$website = esc_url( (string) get_post_meta( $post->ID, 'vd_website', true ), array( 'http', 'https' ) );
	$meta    = array(
		__( 'Sector', 'vassilisdesign-elements' )   => $sectors && ! is_wp_error( $sectors ) ? implode( ', ', wp_list_pluck( $sectors, 'name' ) ) : '',
		__( 'Services', 'vassilisdesign-elements' ) => get_post_meta( $post->ID, 'vd_services', true ),
		__( 'Location', 'vassilisdesign-elements' ) => get_post_meta( $post->ID, 'vd_location', true ),
	);
	ob_start();
	?>
	<header class="vd-case-head vd-wrap">
		<?php if ( $show_back ) : ?>
			<a class="vd-case-back" href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>"><?php esc_html_e( 'All work', 'vassilisdesign-elements' ); ?></a>
		<?php endif; ?>
		<dl class="vd-case-meta">
			<?php foreach ( array_filter( $meta ) as $label => $value ) : ?>
				<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<?php if ( false !== $pos ) : ?>
			<p class="vd-case-index"><span class="screen-reader-text"><?php esc_html_e( 'Project', 'vassilisdesign-elements' ); ?> </span><?php echo esc_html( str_pad( (string) ( $pos + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p>
		<?php endif; ?>
		<h1 class="vd-case-title"><span class="vd-line"><span><?php echo esc_html( get_the_title( $post ) ); ?><span class="vd-stop" aria-hidden="true">.</span></span></span></h1>
		<?php if ( $scope || $website ) : ?>
			<div class="vd-case-red">
				<p class="vd-case-red-title"><?php esc_html_e( 'Scope', 'vassilisdesign-elements' ); ?></p>
				<div class="vd-case-red-body">
					<?php if ( $scope ) : ?>
						<ul class="vd-case-facts" role="list">
							<?php foreach ( $scope as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $website ) : ?>
						<a class="vd-case-site" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Visit website', 'vassilisdesign-elements' ); ?><span aria-hidden="true"> ↗</span><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'vassilisdesign-elements' ); ?></span></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</header>
	<?php
	return ob_get_clean();
}

/** Cover image, summary and long description. */
function vde_work_intro( $post = null ) {
	$post  = get_post( $post );
	$thumb = get_post_thumbnail_id( $post );
	ob_start();
	if ( $thumb ) :
		?>
		<figure class="vd-case-hero vd-wrap">
			<div class="vd-case-poster vd-photo vd-is-color<?php echo 'contain' === vde_cover_fit( $post->ID ) ? ' vd-fit-contain' : ''; ?>">
				<?php echo vde_img( $thumb, 'vd-wide', true, get_the_title( $post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<span class="vd-plus" aria-hidden="true"></span>
			<span class="vd-dots" aria-hidden="true"></span>
		</figure>
		<?php
	endif;
	$elementor = vde_work_uses_elementor( $post->ID );
	if ( has_excerpt( $post ) || ( trim( $post->post_content ) && ! $elementor ) ) :
		?>
		<section class="vd-case-block vd-wrap">
			<h2 class="vd-case-label"><?php esc_html_e( 'About the project', 'vassilisdesign-elements' ); ?></h2>
			<div class="vd-case-text">
				<?php if ( has_excerpt( $post ) ) : ?>
					<p class="vd-case-summary"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
				<?php endif; ?>
				<?php
				// Long description from the editor, unless the Work is built with Elementor (that content follows below).
				if ( trim( $post->post_content ) && ! $elementor ) {
					echo '<div class="vd-prose">' . apply_filters( 'the_content', $post->post_content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</section>
		<?php
	endif;
	echo vde_work_elementor_content( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return ob_get_clean();
}

/** True when the Work is built with Elementor or is open in the Elementor editor right now. */
function vde_work_uses_elementor( $post_id ) {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$elementor = \Elementor\Plugin::$instance;
	if ( isset( $elementor->preview ) && $elementor->preview->is_preview_mode( $post_id ) ) {
		return true;
	}
	$document = $elementor->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * The Work's own Elementor content. The Elementor editor needs this spot (the_content) to place its editing
 * area: without it, opening a Work in Elementor never finishes loading. Printed once per Work, so a Work
 * that contains this widget cannot render itself again and again.
 */
function vde_work_elementor_content( $post ) {
	static $printed = array();
	$post = get_post( $post );
	if ( ! $post || isset( $printed[ $post->ID ] ) || ! vde_work_uses_elementor( $post->ID ) ) {
		return '';
	}
	$printed[ $post->ID ] = true;
	$GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- the_content() reads the global post.
	setup_postdata( $post );
	ob_start();
	the_content();
	$html = trim( (string) ob_get_clean() );
	return '' === $html ? '' : '<div class="vd-work-content">' . $html . '</div>';
}

/** Work gallery: images at their natural proportions, in full colour. */
function vde_work_gallery( $post = null ) {
	$post = get_post( $post );
	$ids  = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post->ID, 'vd_gallery', true ) ) ) );
	if ( ! $ids ) {
		return '';
	}
	$out = '';
	foreach ( $ids as $id ) {
		$alt  = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$out .= '<figure class="vd-gallery-item">' . vde_img( $id, 'vd-wide', false, $alt ? $alt : get_the_title( $post ) ) . '</figure>';
	}
	return '<div class="vd-gallery vd-wrap">' . $out . '</div>';
}

/** Link to the next Work (wraps around). */
function vde_next_work( $post = null ) {
	$post = get_post( $post );
	$ids  = vde_work_ids();
	if ( count( $ids ) < 2 ) {
		return '';
	}
	$pos  = array_search( $post->ID, $ids, true );
	$next = get_post( $ids[ ( false === $pos ? 0 : $pos + 1 ) % count( $ids ) ] );
	ob_start();
	?>
	<nav class="vd-case-next vd-wrap" aria-label="<?php esc_attr_e( 'Next project', 'vassilisdesign-elements' ); ?>">
		<a class="vd-case-next-link" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
			<span class="vd-case-next-label"><?php esc_html_e( 'Next project', 'vassilisdesign-elements' ); ?></span>
			<span class="vd-case-next-client"><?php echo esc_html( get_the_title( $next ) ); ?><span class="vd-stop" aria-hidden="true">.</span></span>
			<span class="vd-case-next-thumb vd-photo<?php echo 'contain' === vde_cover_fit( $next->ID ) ? ' vd-fit-contain' : ''; ?>">
				<?php echo vde_img( get_post_thumbnail_id( $next ), 'vd-tile', false, '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="vd-red-block" aria-hidden="true"></span>
			</span>
		</a>
	</nav>
	<?php
	return ob_get_clean();
}

/** Contact lines from the Customizer. */
function vde_contact_lines() {
	$email = vde_opt( 'vd_email' );
	$phone = vde_opt( 'vd_phone' );
	$out   = array();
	if ( $email ) {
		$out[] = '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>';
	}
	if ( $phone ) {
		$out[] = '<a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>';
	}
	if ( vde_opt( 'vd_messaging' ) ) {
		$out[] = esc_html( vde_opt( 'vd_messaging' ) );
	}
	return implode( '<br>', $out );
}
