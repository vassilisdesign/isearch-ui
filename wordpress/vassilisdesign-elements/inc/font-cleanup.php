<?php
/**
 * One-time cleanup: every Elementor font setting that uses Roboto becomes Commissioner (the studio font),
 * on desktop, tablet and mobile, in pages, templates and Site Settings. Sizes, weights and everything else
 * stay as they are. A copy of each changed item's data is kept (meta _vde_roboto_backup) so it can be undone.
 * Once no element uses Roboto, Elementor stops loading its ~160 font rules and font file.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** Replace Roboto in any "...font_family" setting (including _tablet / _mobile) inside Elementor data. */
function vde_replace_roboto( &$data ) {
	$changed = 0;
	if ( ! is_array( $data ) ) {
		return 0;
	}
	foreach ( $data as $key => &$value ) {
		if ( is_array( $value ) ) {
			$changed += vde_replace_roboto( $value );
		} elseif ( is_string( $key ) && false !== strpos( $key, 'font_family' ) && is_string( $value ) && 'roboto' === strtolower( trim( $value ) ) ) {
			$value = 'Commissioner';
			++$changed;
		}
	}
	return $changed;
}

add_action(
	'admin_init',
	function () {
		if ( get_option( 'vde_roboto_replaced' ) || ! current_user_can( 'edit_theme_options' ) || ! did_action( 'elementor/loaded' ) ) {
			return;
		}
		update_option( 'vde_roboto_replaced', time(), false );
		global $wpdb;
		$rows    = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_elementor_data','_elementor_page_settings') AND meta_value LIKE '%Roboto%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$changed = array();
		foreach ( $rows as $row ) {
			$is_json = '_elementor_data' === $row->meta_key;
			$data    = $is_json ? json_decode( $row->meta_value, true ) : maybe_unserialize( $row->meta_value );
			if ( ! is_array( $data ) ) {
				continue;
			}
			if ( ! vde_replace_roboto( $data ) ) {
				continue;
			}
			add_post_meta( (int) $row->post_id, '_vde_roboto_backup', array( 'key' => $row->meta_key, 'value' => $row->meta_value, 'time' => time() ) );
			if ( $is_json ) {
				update_post_meta( (int) $row->post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
			} else {
				update_post_meta( (int) $row->post_id, '_elementor_page_settings', $data );
			}
			$changed[] = (int) $row->post_id;
		}
		if ( $changed && class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache(); // regenerate CSS without Roboto
		}
		update_option( 'vde_roboto_replaced_posts', array_values( array_unique( $changed ) ), false );
	}
);
