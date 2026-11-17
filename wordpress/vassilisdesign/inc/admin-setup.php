<?php
/**
 * One-click setup: Appearance → VASSILISDESIGN setup.
 *
 * Applies the recommended Elementor settings, creates the Sectors, the legal pages and the homepage,
 * and builds the header and footer menus. Every step skips anything that already exists, so it is
 * safe to run more than once. Nothing runs until the button is pressed.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'VASSILISDESIGN setup', 'vassilisdesign' ), __( 'VASSILISDESIGN setup', 'vassilisdesign' ), 'manage_options', 'vd-setup', 'vd_render_setup_page' );
	}
);

/** Plugin checklist shown on the setup page. */
function vd_setup_checks() {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	return array(
		array( 'Hello Elementor (parent theme)', (bool) wp_get_theme( 'hello-elementor' )->exists(), true ),
		array( 'Elementor', is_plugin_active( 'elementor/elementor.php' ), true ),
		array( 'Elementor Pro (contact form, optional Theme Builder)', is_plugin_active( 'elementor-pro/elementor-pro.php' ), true ),
		array( 'Complianz (cookie consent and cookie policy)', is_plugin_active( 'complianz-gdpr/complianz-gpdr.php' ) || is_plugin_active( 'complianz-gdpr-premium/complianz-gpdr-premium.php' ), true ),
		array( 'Polylang (only if the site is bilingual)', is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ), false ),
	);
}

function vd_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = get_transient( 'vd_setup_log' );
	delete_transient( 'vd_setup_log' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'VASSILISDESIGN setup', 'vassilisdesign' ); ?></h1>

		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><strong><?php esc_html_e( 'Setup finished.', 'vassilisdesign' ); ?></strong></p><ul style="list-style:disc;padding-left:1.5em">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Plugins', 'vassilisdesign' ); ?></h2>
		<table class="widefat striped" style="max-width:760px">
			<tbody>
			<?php foreach ( vd_setup_checks() as $check ) : ?>
				<tr>
					<td><?php echo esc_html( $check[0] ); ?></td>
					<td><?php echo $check[1] ? '<span style="color:#008a20">&#10003; ' . esc_html__( 'Active', 'vassilisdesign' ) . '</span>' : ( $check[2] ? '<span style="color:#d63638">' . esc_html__( 'Install and activate', 'vassilisdesign' ) . '</span>' : esc_html__( 'Not installed (optional)', 'vassilisdesign' ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description" style="max-width:760px"><?php esc_html_e( 'No add-on packs are needed (Essential Addons, Premium Addons, UAE, Unlimited Elements, Sticky Header Effects): the theme widgets and Elementor Pro cover everything, and each add-on would add CSS and JavaScript to every page.', 'vassilisdesign' ); ?></p>

		<h2><?php esc_html_e( 'What the setup does', 'vassilisdesign' ); ?></h2>
		<ol style="max-width:760px">
			<li><?php esc_html_e( 'Applies Elementor performance settings: no Google Fonts, no default colours or fonts, external CSS files, leaner markup, CSS loaded per page, SVG icons, lightbox off, Works editable with Elementor.', 'vassilisdesign' ); ?></li>
			<li><?php esc_html_e( 'Creates the four Sectors for Works.', 'vassilisdesign' ); ?></li>
			<li><?php esc_html_e( 'Creates the Privacy Policy, Terms of Use and Legal Notice pages (English) and sets the WordPress privacy page. The Cookie Policy is created by the Complianz wizard.', 'vassilisdesign' ); ?></li>
			<li><?php esc_html_e( 'Creates the Home page from the Elementor template and sets it as the front page.', 'vassilisdesign' ); ?></li>
			<li><?php esc_html_e( 'Creates the header and footer menus.', 'vassilisdesign' ); ?></li>
		</ol>
		<p><?php esc_html_e( 'Existing pages, menus and terms with the same names are left untouched.', 'vassilisdesign' ); ?></p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="vd_run_setup">
			<?php wp_nonce_field( 'vd_run_setup' ); ?>
			<?php submit_button( __( 'Run setup', 'vassilisdesign' ), 'primary large' ); ?>
		</form>
	</div>
	<?php
}

add_action(
	'admin_post_vd_run_setup',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'vassilisdesign' ) );
		}
		check_admin_referer( 'vd_run_setup' );
		set_transient( 'vd_setup_log', vd_run_setup(), 300 );
		wp_safe_redirect( admin_url( 'themes.php?page=vd-setup' ) );
		exit;
	}
);

/** Returns a list of what was done. */
function vd_run_setup() {
	$log = array();

	// 1. Elementor settings.
	if ( did_action( 'elementor/loaded' ) ) {
		vd_apply_elementor_options();
		$log[] = __( 'Elementor performance settings applied.', 'vassilisdesign' );
	} else {
		$log[] = __( 'Elementor is not active: settings skipped.', 'vassilisdesign' );
	}

	// 2. Sectors.
	foreach ( array( 'Wine and packaging', 'Hospitality and tourism', 'Restaurants and food', 'Corporate and professional services' ) as $name ) {
		if ( ! term_exists( $name, 'work_sector' ) ) {
			wp_insert_term( $name, 'work_sector' );
			/* translators: %s: sector name */
			$log[] = sprintf( __( 'Sector created: %s', 'vassilisdesign' ), $name );
		}
	}

	// 3. Legal pages.
	$pages = array(
		'privacy-policy' => array( 'Privacy Policy', 'privacy-en.html' ),
		'terms-of-use'   => array( 'Terms of Use', 'terms-en.html' ),
		'legal-notice'   => array( 'Legal Notice', 'legal-notice-en.html' ),
	);
	$ids   = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		$content  = vd_setup_file( 'legal/' . $page[1] );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			// WordPress creates an unpublished sample "Privacy Policy" on install: replace it once.
			if ( 'privacy-policy' === $slug && 'draft' === $existing->post_status && '' !== $content ) {
				wp_update_post(
					array(
						'ID'           => $existing->ID,
						'post_status'  => 'publish',
						'post_content' => vd_fill_placeholders( $content ),
					)
				);
				$log[] = __( 'WordPress sample privacy page replaced with the Privacy Policy and published.', 'vassilisdesign' );
			}
			continue;
		}
		if ( '' === $content ) {
			continue;
		}
		$ids[ $slug ] = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page[0],
				'post_name'    => $slug,
				'post_content' => vd_fill_placeholders( $content ),
			)
		);
		/* translators: %s: page title */
		$log[] = sprintf( __( 'Page created: %s', 'vassilisdesign' ), $page[0] );
	}
	if ( ! empty( $ids['privacy-policy'] ) ) {
		update_option( 'wp_page_for_privacy_policy', (int) $ids['privacy-policy'] );
	}

	// 4. Home page from the Elementor template.
	$home = get_page_by_path( 'home' );
	if ( ! $home ) {
		$template = json_decode( vd_setup_file( 'home-en.json' ), true );
		if ( $template && ! empty( $template['content'] ) ) {
			$privacy = ! empty( $ids['privacy-policy'] ) ? get_permalink( $ids['privacy-policy'] ) : home_url( '/privacy-policy/' );
			$data    = wp_json_encode( $template['content'] );
			$data    = str_replace( array( '{{privacy_url}}', '{{email}}' ), array( esc_url( $privacy ), sanitize_email( vd_opt( 'vd_email' ) ) ), $data );
			$home_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => 'Home',
					'post_name'   => 'home',
				)
			);
			if ( $home_id && ! is_wp_error( $home_id ) ) {
				update_post_meta( $home_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $home_id, '_elementor_template_type', 'wp-page' );
				update_post_meta( $home_id, '_wp_page_template', 'elementor_header_footer' );
				update_post_meta( $home_id, '_elementor_data', wp_slash( $data ) );
				if ( defined( 'ELEMENTOR_VERSION' ) ) {
					update_post_meta( $home_id, '_elementor_version', ELEMENTOR_VERSION );
				}
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $home_id );
				$log[] = __( 'Home page created from the Elementor template and set as the front page. Add the hero image, logos and studio image in Elementor.', 'vassilisdesign' );
			}
		}
	}

	// 5. Menus.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['vd-primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Header' );
		if ( ! is_wp_error( $menu_id ) ) {
			$home_url = home_url( '/' );
			$items    = array(
				array( 'Work', get_post_type_archive_link( 'works' ) ),
				array( 'Studio', $home_url . '#studio' ),
				array( 'Contact', $home_url . '#contact' ),
			);
			foreach ( $items as $item ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $item[0],
						'menu-item-url'    => $item[1],
						'menu-item-type'   => 'custom',
						'menu-item-status' => 'publish',
					)
				);
			}
			$locations['vd-primary'] = $menu_id;
			$log[]                   = __( 'Header menu created.', 'vassilisdesign' );
		}
	}
	if ( empty( $locations['vd-legal'] ) ) {
		$menu_id = wp_create_nav_menu( 'Legal' );
		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( array( 'privacy-policy', 'terms-of-use', 'legal-notice', 'cookie-policy-eu' ) as $slug ) {
				$page = get_page_by_path( $slug );
				if ( $page ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-object-id' => $page->ID,
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
				}
			}
			$locations['vd-legal'] = $menu_id;
			$log[]                 = __( 'Footer legal menu created. After the Complianz wizard, add its Cookie Policy page to this menu.', 'vassilisdesign' );
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'permalink_structure', get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : '/%postname%/' );
	flush_rewrite_rules();
	$log[] = __( 'Permalinks refreshed.', 'vassilisdesign' );

	return $log;
}

function vd_setup_file( $relative ) {
	$path = VD_DIR . '/setup/' . $relative;
	return is_readable( $path ) ? (string) file_get_contents( $path ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}

/** Replaces {{tokens}} in the legal page templates with the Customizer studio details. */
function vd_fill_placeholders( $content ) {
	return strtr(
		$content,
		array(
			'{{site_name}}' => get_bloginfo( 'name' ),
			'{{site_url}}'  => home_url( '/' ),
			'{{email}}'     => sanitize_email( vd_opt( 'vd_email' ) ),
			'{{phone}}'     => vd_opt( 'vd_phone' ),
			'{{date}}'      => wp_date( get_option( 'date_format' ) ),
		)
	);
}
