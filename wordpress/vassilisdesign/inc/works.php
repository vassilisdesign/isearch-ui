<?php
/**
 * "Works" custom post type: the studio portfolio.
 *
 * Title = client or project name, Excerpt = short summary, Featured image = tile/cover image,
 * Editor = optional long description. Extra fields are stored as post meta (no ACF needed)
 * and exposed to the REST API and to Elementor Pro's "Post Custom Field" dynamic tag.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

/** Meta keys used by Works. Keys have no leading underscore so Elementor's dynamic tags can list them. */
function vd_work_fields() {
	return array(
		'vd_location' => __( 'Location', 'vassilisdesign' ),
		'vd_services' => __( 'Services', 'vassilisdesign' ),
		'vd_scope'    => __( 'Scope (one item per line)', 'vassilisdesign' ),
		'vd_gallery'  => __( 'Gallery', 'vassilisdesign' ),
		'vd_cover_fit' => __( 'Cover display', 'vassilisdesign' ),
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
					'name'          => __( 'Sectors', 'vassilisdesign' ),
					'singular_name' => __( 'Sector', 'vassilisdesign' ),
					'add_new_item'  => __( 'Add new Sector', 'vassilisdesign' ),
					'edit_item'     => __( 'Edit Sector', 'vassilisdesign' ),
					'all_items'     => __( 'All Sectors', 'vassilisdesign' ),
					'menu_name'     => __( 'Sectors', 'vassilisdesign' ),
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
	},
	9
);

add_action(
	'init',
	function () {
		register_post_type(
			'works',
			array(
				'labels'          => array(
					'name'                  => __( 'Works', 'vassilisdesign' ),
					'singular_name'         => __( 'Work', 'vassilisdesign' ),
					'menu_name'             => __( 'Works', 'vassilisdesign' ),
					'add_new'               => __( 'Add Work', 'vassilisdesign' ),
					'add_new_item'          => __( 'Add new Work', 'vassilisdesign' ),
					'edit_item'             => __( 'Edit Work', 'vassilisdesign' ),
					'new_item'              => __( 'New Work', 'vassilisdesign' ),
					'view_item'             => __( 'View Work', 'vassilisdesign' ),
					'view_items'            => __( 'View Works', 'vassilisdesign' ),
					'search_items'          => __( 'Search Works', 'vassilisdesign' ),
					'not_found'             => __( 'No Works found.', 'vassilisdesign' ),
					'not_found_in_trash'    => __( 'No Works found in Trash.', 'vassilisdesign' ),
					'all_items'             => __( 'All Works', 'vassilisdesign' ),
					'featured_image'        => __( 'Cover image', 'vassilisdesign' ),
					'set_featured_image'    => __( 'Set cover image', 'vassilisdesign' ),
					'remove_featured_image' => __( 'Remove cover image', 'vassilisdesign' ),
					'use_featured_image'    => __( 'Use as cover image', 'vassilisdesign' ),
					'item_published'        => __( 'Work published.', 'vassilisdesign' ),
					'item_updated'          => __( 'Work updated.', 'vassilisdesign' ),
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

		foreach ( array_keys( vd_work_fields() ) as $key ) {
			register_post_meta(
				'works',
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'vd_gallery' === $key ? 'vd_sanitize_id_list' : 'sanitize_textarea_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
);

/** Comma-separated attachment IDs. */
function vd_sanitize_id_list( $value ) {
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
		add_meta_box( 'vd-work-details', __( 'Work details', 'vassilisdesign' ), 'vd_render_work_metabox', 'works', 'normal', 'high' );
	}
);

function vd_render_work_metabox( $post ) {
	wp_nonce_field( 'vd_save_work', 'vd_work_nonce' );
	$location = get_post_meta( $post->ID, 'vd_location', true );
	$services = get_post_meta( $post->ID, 'vd_services', true );
	$scope    = get_post_meta( $post->ID, 'vd_scope', true );
	$gallery  = get_post_meta( $post->ID, 'vd_gallery', true );
	$fit      = get_post_meta( $post->ID, 'vd_cover_fit', true );
	?>
	<p class="description"><?php esc_html_e( 'Title = client or project name. Excerpt = the short summary shown on the Work page. Cover image = the tile on the homepage.', 'vassilisdesign' ); ?></p>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="vd_location"><?php esc_html_e( 'Location', 'vassilisdesign' ); ?></label></th>
			<td><input type="text" class="regular-text" id="vd_location" name="vd_location" value="<?php echo esc_attr( $location ); ?>" placeholder="<?php esc_attr_e( 'Thessaloniki, Greece', 'vassilisdesign' ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="vd_services"><?php esc_html_e( 'Services', 'vassilisdesign' ); ?></label></th>
			<td><input type="text" class="regular-text" id="vd_services" name="vd_services" value="<?php echo esc_attr( $services ); ?>" placeholder="<?php esc_attr_e( 'Identity, packaging, website', 'vassilisdesign' ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="vd_scope"><?php esc_html_e( 'Scope', 'vassilisdesign' ); ?></label></th>
			<td>
				<textarea class="large-text" rows="4" id="vd_scope" name="vd_scope" aria-describedby="vd_scope_help"><?php echo esc_textarea( $scope ); ?></textarea>
				<p class="description" id="vd_scope_help"><?php esc_html_e( 'One item per line. Shown in the red box on the Work page.', 'vassilisdesign' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="vd_cover_fit"><?php esc_html_e( 'Cover display', 'vassilisdesign' ); ?></label></th>
			<td>
				<select id="vd_cover_fit" name="vd_cover_fit">
					<option value="cover" <?php selected( $fit, 'cover' ); ?>><?php esc_html_e( 'Photo or screenshot: fill the frame', 'vassilisdesign' ); ?></option>
					<option value="contain" <?php selected( $fit, 'contain' ); ?>><?php esc_html_e( 'Packshot or logo on white: fit inside the frame', 'vassilisdesign' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Gallery', 'vassilisdesign' ); ?></th>
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
					<button type="button" class="button vd-gallery-choose"><?php esc_html_e( 'Choose images', 'vassilisdesign' ); ?></button>
					<button type="button" class="button-link vd-gallery-clear"><?php esc_html_e( 'Remove all', 'vassilisdesign' ); ?></button>
				</p>
				<p class="description"><?php esc_html_e( 'Images shown on the Work page, in this order. Drag to reorder inside the media window. Packshots on white blend into the page automatically.', 'vassilisdesign' ); ?></p>
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
		foreach ( array_keys( vd_work_fields() ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$raw   = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
				$value = 'vd_gallery' === $key ? vd_sanitize_id_list( $raw ) : sanitize_textarea_field( $raw );
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
		wp_enqueue_script( 'vd-admin-works', VD_URI . '/assets/js/admin-works.js', array( 'jquery' ), VD_VERSION, true );
		wp_localize_script(
			'vd-admin-works',
			'vdWorks',
			array(
				'title'  => __( 'Work gallery', 'vassilisdesign' ),
				'button' => __( 'Use these images', 'vassilisdesign' ),
			)
		);
		wp_add_inline_style( 'wp-admin', '.vd-gallery-preview{display:flex;flex-wrap:wrap;gap:8px;margin:0}.vd-gallery-preview li{margin:0}.vd-gallery-preview img{width:72px;height:72px;object-fit:cover;display:block}' );
	}
);

// Show the cover image in the Works list.
add_filter(
	'manage_works_posts_columns',
	function ( $cols ) {
		return array_slice( $cols, 0, 1, true ) + array( 'vd_cover' => __( 'Cover', 'vassilisdesign' ) ) + array_slice( $cols, 1, null, true );
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
