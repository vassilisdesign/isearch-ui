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
