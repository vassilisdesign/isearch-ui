<?php
/**
 * Application Passwords on LiteSpeed/Apache hosting: some servers drop the "Authorization" header before
 * PHP sees it, so WordPress never receives the login and the REST API answers "not logged in".
 * This puts the header back from the raw request headers. WordPress still checks every login itself, and
 * Application Passwords only work over HTTPS.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $_SERVER['HTTP_AUTHORIZATION'] ) && empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) && empty( $_SERVER['PHP_AUTH_USER'] ) && function_exists( 'getallheaders' ) ) {
	foreach ( (array) getallheaders() as $vde_name => $vde_value ) {
		if ( 'authorization' === strtolower( (string) $vde_name ) && is_string( $vde_value ) && 0 === stripos( $vde_value, 'basic ' ) ) {
			$_SERVER['HTTP_AUTHORIZATION'] = $vde_value; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- read by WordPress core's own Basic auth check.
			$vde_pair = base64_decode( substr( $vde_value, 6 ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( false !== $vde_pair && false !== strpos( $vde_pair, ':' ) ) {
				list( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) = explode( ':', $vde_pair, 2 );
			}
			break;
		}
	}
	unset( $vde_name, $vde_value, $vde_pair );
}

/*
 * Connection check for setting up access: /wp-json/vde/v1/auth-check reports only whether a login header
 * reached PHP and how WordPress judged it (yes/no and an error code). It never returns the key itself.
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'vde/v1',
			'/auth-check',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => function () {
					$raw = false;
					if ( function_exists( 'getallheaders' ) ) {
						foreach ( (array) getallheaders() as $name => $value ) {
							if ( 'authorization' === strtolower( (string) $name ) ) {
								$raw = true;
							}
						}
					}
					$status = isset( $GLOBALS['wp_rest_application_password_status'] ) ? $GLOBALS['wp_rest_application_password_status'] : null;
					return array(
						'header_in_server_vars'  => ! empty( $_SERVER['HTTP_AUTHORIZATION'] ),
						'header_in_redirect_var' => ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ),
						'header_in_raw_headers'  => $raw,
						'php_auth_user_set'      => ! empty( $_SERVER['PHP_AUTH_USER'] ),
						'app_passwords_on'       => function_exists( 'wp_is_application_passwords_available' ) && wp_is_application_passwords_available(),
						'logged_in'              => is_user_logged_in(),
						'wordpress_says'         => is_wp_error( $status ) ? $status->get_error_code() : ( true === $status ? 'accepted' : 'no attempt' ),
						'server_api'             => PHP_SAPI,
					);
				},
			)
		);
	}
);
