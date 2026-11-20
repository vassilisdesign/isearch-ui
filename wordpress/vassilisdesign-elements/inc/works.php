<?php
/**
 * "Works" custom post type: the studio portfolio.
 *
 * Title = client or project name, Excerpt = short summary, Featured image = tile/cover image,
 * Editor = optional long description. Extra fields are stored as post meta (no ACF needed)
 * and exposed to the REST API and to Elementor Pro's "Post Custom Field" dynamic tag.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** Meta keys used by Works. Keys have no leading underscore so Elementor's dynamic tags can list them. */
function vde_work_fields() {
	return array(
		'vd_location' => __( 'Location', 'vassilisdesign-elements' ),
		'vd_services' => __( 'Services', 'vassilisdesign-elements' ),
		'vd_scope'    => __( 'Scope', 'vassilisdesign-elements' ),
		'vd_website'  => __( 'Live website', 'vassilisdesign-elements' ),
		'vd_gallery'  => __( 'Gallery', 'vassilisdesign-elements' ),
		'vd_cover_fit' => __( 'Cover display', 'vassilisdesign-elements' ),
	);
}

// Sectors are registered before Works so /works/sector/... URLs are not mistaken for Work attachments.
add_action(
	'init',
	function () {
		register_taxonomy(
			'work_sector',
			'works',
			array(
				'labels'            => array(
					'name'          => __( 'Sectors', 'vassilisdesign-elements' ),
					'singular_name' => __( 'Sector', 'vassilisdesign-elements' ),
					'add_new_item'  => __( 'Add new Sector', 'vassilisdesign-elements' ),
					'edit_item'     => __( 'Edit Sector', 'vassilisdesign-elements' ),
					'all_items'     => __( 'All Sectors', 'vassilisdesign-elements' ),
					'menu_name'     => __( 'Sectors', 'vassilisdesign-elements' ),
				),
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'works/sector',
					'with_front' => false,
				),
			)
		);
		// Services: a checklist like Sectors, so names are spelled the same on every Work.
		// Not public: no thin /service/ pages for search engines; the names show on the Work page and tiles.
		register_taxonomy(
			'work_service',
			'works',
			array(
				'labels'             => array(
					'name'          => __( 'Services', 'vassilisdesign-elements' ),
					'singular_name' => __( 'Service', 'vassilisdesign-elements' ),
					'add_new_item'  => __( 'Add new Service', 'vassilisdesign-elements' ),
					'edit_item'     => __( 'Edit Service', 'vassilisdesign-elements' ),
					'all_items'     => __( 'All Services', 'vassilisdesign-elements' ),
					'menu_name'     => __( 'Services', 'vassilisdesign-elements' ),
				),
				'hierarchical'       => true,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_admin_column'  => true,
				'show_in_rest'       => true,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'rewrite'            => false,
			)
		);
	},
	9
);

/** The Work's Services as one line ("Brand Identity, Web Design"). Falls back to the old text field. */
function vde_work_services( $post ) {
	$post  = get_post( $post );
	$terms = $post ? get_the_terms( $post, 'work_service' ) : false;
	if ( $terms && ! is_wp_error( $terms ) ) {
		return implode( ', ', wp_list_pluck( $terms, 'name' ) );
	}
	return $post ? (string) get_post_meta( $post->ID, 'vd_services', true ) : '';
}

/** The studio's standard services, added once. */
function vde_default_services() {
	return array(
		__( 'Brand Identity', 'vassilisdesign-elements' ),
		__( 'Logo Design', 'vassilisdesign-elements' ),
		__( 'Packaging', 'vassilisdesign-elements' ),
		__( 'Label Design', 'vassilisdesign-elements' ),
		__( 'Web Design', 'vassilisdesign-elements' ),
		__( 'Print & Editorial', 'vassilisdesign-elements' ),
		__( 'Stationery', 'vassilisdesign-elements' ),
		__( 'Signage & Wayfinding', 'vassilisdesign-elements' ),
		__( 'Art Direction', 'vassilisdesign-elements' ),
		__( 'Social Media', 'vassilisdesign-elements' ),
	);
}

/** Old free-text service words mapped to the standard names ("websites" → Web Design). */
function vde_service_alias( $name ) {
	$key  = strtolower( trim( $name ) );
	$map  = array(
		'/^(brand(ing)?|(brand |visual )?identit(y|ies)|corporate identity)$/' => 'Brand Identity',
		'/^logos?( design)?$/'                                                  => 'Logo Design',
		'/^(web ?sites?|web( design)?|digital)$/'                               => 'Web Design',
		'/^labels?( design)?$/'                                                 => 'Label Design',
		'/^packaging( design)?$/'                                               => 'Packaging',
		'/^(print|editorial|brochures?|catalogues?|print (and|&) editorial)$/'  => 'Print & Editorial',
		'/^stationery$/'                                                        => 'Stationery',
		'/^(signage|wayfinding)$/'                                              => 'Signage & Wayfinding',
		'/^(art|creative) direction$/'                                          => 'Art Direction',
		'/^social( media)?$/'                                                   => 'Social Media',
	);
	foreach ( $map as $pattern => $standard ) {
		if ( preg_match( $pattern, $key ) ) {
			return __( $standard, 'vassilisdesign-elements' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- same strings as vde_default_services().
		}
	}
	return mb_convert_case( trim( $name ), MB_CASE_TITLE, 'UTF-8' );
}

// Once: add the standard Services, then move each Work's old "Services" text into the checklist.
add_action(
	'admin_init',
	function () {
		if ( get_option( 'vde_services_migrated' ) || ! current_user_can( 'manage_categories' ) || ! taxonomy_exists( 'work_service' ) ) {
			return;
		}
		update_option( 'vde_services_migrated', 1, false );
		$find = function ( $name ) {
			foreach ( get_terms( array( 'taxonomy' => 'work_service', 'hide_empty' => false ) ) as $term ) {
				if ( 0 === strcasecmp( html_entity_decode( $term->name ), $name ) ) {
					return (int) $term->term_id;
				}
			}
			$made = wp_insert_term( $name, 'work_service' );
			return is_wp_error( $made ) ? 0 : (int) $made['term_id'];
		};
		foreach ( vde_default_services() as $name ) {
			$find( $name );
		}
		$works = get_posts( array( 'post_type' => 'works', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
		foreach ( $works as $id ) {
			$old = trim( (string) get_post_meta( $id, 'vd_services', true ) );
			if ( '' === $old || wp_get_object_terms( $id, 'work_service', array( 'fields' => 'ids' ) ) ) {
				continue;
			}
			$ids = array();
			foreach ( preg_split( '/\s*[,\/·|]\s*|\s+(?:and|&)\s+/u', $old ) as $name ) {
				$name = trim( $name );
				if ( '' !== $name ) {
					$ids[] = $find( vde_service_alias( $name ) );
				}
			}
			$ids = array_values( array_filter( $ids ) );
			if ( $ids && ! is_wp_error( wp_set_object_terms( $id, $ids, 'work_service' ) ) ) {
				delete_post_meta( $id, 'vd_services' );
			}
		}
	}
);

add_action(
	'init',
	function () {
		register_post_type(
			'works',
			array(
				'labels'          => array(
					'name'                  => __( 'Works', 'vassilisdesign-elements' ),
					'singular_name'         => __( 'Work', 'vassilisdesign-elements' ),
					'menu_name'             => __( 'Works', 'vassilisdesign-elements' ),
					'add_new'               => __( 'Add Work', 'vassilisdesign-elements' ),
					'add_new_item'          => __( 'Add new Work', 'vassilisdesign-elements' ),
					'edit_item'             => __( 'Edit Work', 'vassilisdesign-elements' ),
					'new_item'              => __( 'New Work', 'vassilisdesign-elements' ),
					'view_item'             => __( 'View Work', 'vassilisdesign-elements' ),
					'view_items'            => __( 'View Works', 'vassilisdesign-elements' ),
					'search_items'          => __( 'Search Works', 'vassilisdesign-elements' ),
					'not_found'             => __( 'No Works found.', 'vassilisdesign-elements' ),
					'not_found_in_trash'    => __( 'No Works found in Trash.', 'vassilisdesign-elements' ),
					'all_items'             => __( 'All Works', 'vassilisdesign-elements' ),
					'featured_image'        => __( 'Cover image', 'vassilisdesign-elements' ),
					'set_featured_image'    => __( 'Set cover image', 'vassilisdesign-elements' ),
					'remove_featured_image' => __( 'Remove cover image', 'vassilisdesign-elements' ),
					'use_featured_image'    => __( 'Use as cover image', 'vassilisdesign-elements' ),
					'item_published'        => __( 'Work published.', 'vassilisdesign-elements' ),
					'item_updated'          => __( 'Work updated.', 'vassilisdesign-elements' ),
				),
				'public'          => true,
				'has_archive'     => true,
				'rewrite'         => array(
					'slug'       => 'works',
					'with_front' => false,
				),
				'menu_icon'       => 'dashicons-portfolio',
				'menu_position'   => 5,
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions', 'elementor' ),
				'show_in_rest'    => true,
				'capability_type' => 'post',
			)
		);

		foreach ( array_keys( vde_work_fields() ) as $key ) {
			register_post_meta(
				'works',
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'vd_gallery' === $key ? 'vde_sanitize_id_list' : ( 'vd_website' === $key ? 'esc_url_raw' : 'sanitize_textarea_field' ),
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
);

/** Comma-separated attachment IDs. */
function vde_sanitize_id_list( $value ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
	return implode( ',', $ids );
}

// Works are ordered by "Order" (page attributes), then newest first, everywhere they are listed.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->is_post_type_archive( 'works' ) || $query->is_tax( 'work_sector' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
			$query->set( 'posts_per_page', 24 );
		}
	}
);

/* ---------- Edit screen ---------- */

add_action(
	'add_meta_boxes_works',
	function () {
		add_meta_box( 'vd-work-details', __( 'Work details', 'vassilisdesign-elements' ), 'vde_render_work_metabox', 'works', 'normal', 'high' );
	}
);

function vde_render_work_metabox( $post ) {
	wp_nonce_field( 'vd_save_work', 'vd_work_nonce' );
	$location = get_post_meta( $post->ID, 'vd_location', true );
	$scope    = get_post_meta( $post->ID, 'vd_scope', true );
	$website  = get_post_meta( $post->ID, 'vd_website', true );
	$gallery  = get_post_meta( $post->ID, 'vd_gallery', true );
	$fit      = get_post_meta( $post->ID, 'vd_cover_fit', true );
	?>
	<p class="description"><?php esc_html_e( 'Title = client or project name. Excerpt = the short summary shown on the Work page. Cover image = the tile on the homepage. Tick the Sector and Services in the boxes on the right.', 'vassilisdesign-elements' ); ?></p>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="vd_location"><?php esc_html_e( 'Location', 'vassilisdesign-elements' ); ?></label></th>
			<td><input type="text" class="regular-text" id="vd_location" name="vd_location" value="<?php echo esc_attr( $location ); ?>" placeholder="<?php esc_attr_e( 'Thessaloniki, Greece', 'vassilisdesign-elements' ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="vd_scope"><?php esc_html_e( 'Scope', 'vassilisdesign-elements' ); ?></label></th>
			<td>
				<textarea class="large-text" rows="2" id="vd_scope" name="vd_scope" aria-describedby="vd_scope_help" placeholder="<?php esc_attr_e( 'Concept, design, art direction', 'vassilisdesign-elements' ); ?>"><?php echo esc_textarea( $scope ); ?></textarea>
				<p class="description" id="vd_scope_help"><?php esc_html_e( 'A short line on the studio\'s role. Shown in the red box on the Work page.', 'vassilisdesign-elements' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="vd_website"><?php esc_html_e( 'Live website', 'vassilisdesign-elements' ); ?></label></th>
			<td>
				<input type="text" inputmode="url" class="regular-text" id="vd_website" name="vd_website" value="<?php echo esc_attr( $website ); ?>" placeholder="https://" aria-describedby="vd_website_help">
				<p class="description" id="vd_website_help"><?php esc_html_e( 'Optional. Adds a "Visit website" link under the scope, opening in a new tab.', 'vassilisdesign-elements' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="vd_cover_fit"><?php esc_html_e( 'Cover display', 'vassilisdesign-elements' ); ?></label></th>
			<td>
				<select id="vd_cover_fit" name="vd_cover_fit">
					<option value="cover" <?php selected( $fit, 'cover' ); ?>><?php esc_html_e( 'Photo or screenshot: fill the frame', 'vassilisdesign-elements' ); ?></option>
					<option value="contain" <?php selected( $fit, 'contain' ); ?>><?php esc_html_e( 'Packshot or logo on white: fit inside the frame', 'vassilisdesign-elements' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Gallery', 'vassilisdesign-elements' ); ?></th>
			<td>
				<input type="hidden" id="vd_gallery" name="vd_gallery" value="<?php echo esc_attr( $gallery ); ?>">
				<ul class="vd-gallery-preview">
					<?php
					foreach ( array_filter( explode( ',', (string) $gallery ) ) as $id ) {
						echo '<li data-id="' . esc_attr( $id ) . '">' . wp_get_attachment_image( (int) $id, 'thumbnail' ) . '</li>';
					}
					?>
				</ul>
				<p>
					<button type="button" class="button vd-gallery-choose"><?php esc_html_e( 'Choose images', 'vassilisdesign-elements' ); ?></button>
					<button type="button" class="button-link vd-gallery-clear"><?php esc_html_e( 'Remove all', 'vassilisdesign-elements' ); ?></button>
				</p>
				<p class="description"><?php esc_html_e( 'Images shown on the Work page, in this order. Drag to reorder inside the media window. Packshots on white blend into the page automatically.', 'vassilisdesign-elements' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

add_action(
	'save_post_works',
	function ( $post_id ) {
		if ( ! isset( $_POST['vd_work_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['vd_work_nonce'] ), 'vd_save_work' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array_keys( vde_work_fields() ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$raw   = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
				if ( 'vd_gallery' === $key ) {
					$value = vde_sanitize_id_list( $raw );
				} elseif ( 'vd_website' === $key ) {
					$raw   = trim( $raw );
					$value = '' === $raw ? '' : esc_url_raw( preg_match( '#^[a-z][a-z0-9+.-]*://#i', $raw ) ? $raw : 'https://' . $raw, array( 'http', 'https' ) );
				} else {
					$value = sanitize_textarea_field( $raw );
				}
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || 'works' !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'vd-admin-works', VDE_URI . 'assets/js/admin-works.js', array( 'jquery' ), VDE_VERSION, true );
		wp_localize_script(
			'vd-admin-works',
			'vdWorks',
			array(
				'title'  => __( 'Work gallery', 'vassilisdesign-elements' ),
				'button' => __( 'Use these images', 'vassilisdesign-elements' ),
			)
		);
		wp_add_inline_style( 'wp-admin', '.vd-gallery-preview{display:flex;flex-wrap:wrap;gap:8px;margin:0}.vd-gallery-preview li{margin:0}.vd-gallery-preview img{width:72px;height:72px;object-fit:cover;display:block}' );
	}
);

// Show the cover image in the Works list.
add_filter(
	'manage_works_posts_columns',
	function ( $cols ) {
		return array_slice( $cols, 0, 1, true ) + array( 'vd_cover' => __( 'Cover', 'vassilisdesign-elements' ) ) + array_slice( $cols, 1, null, true );
	}
);
add_action(
	'manage_works_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'vd_cover' === $col ) {
			echo get_the_post_thumbnail( $post_id, array( 48, 48 ) );
		}
	},
	10,
	2
);
