<?php
/**
 * Small helpers for the header and footer. Studio details come from the VASSILISDESIGN Elements
 * plugin (Appearance → Customize → Studio details); the defaults below are used if it is inactive.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

function vdt_opt( $key ) {
	if ( function_exists( 'vde_opt' ) ) {
		return vde_opt( $key );
	}
	$defaults = array(
		'vd_email'      => get_option( 'admin_email' ),
		'vd_phone'      => '',
		'vd_messaging'  => '',
		'vd_clock_tz'   => 'Europe/Athens',
		'vd_clock_name' => '',
		'vd_footer'     => get_bloginfo( 'description' ),
		'vd_wordmark_1' => get_bloginfo( 'name' ),
		'vd_wordmark_2' => '',
	);
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

function vdt_lines( $text ) {
	return nl2br( esc_html( trim( (string) $text ) ) );
}

function vdt_arrow( $dir = 'right' ) {
	if ( function_exists( 'vde_arrow' ) ) {
		return vde_arrow( $dir );
	}
	$d = 'down' === $dir ? 'M12 4v15m-6-6 6 6 6-6' : 'M4 12h15m-6-6 6 6-6 6';
	return '<svg class="vd-arrow" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="' . $d . '" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';
}

function vdt_contact_lines() {
	if ( function_exists( 'vde_contact_lines' ) ) {
		return vde_contact_lines();
	}
	$email = vdt_opt( 'vd_email' );
	return $email ? '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>' : '';
}
