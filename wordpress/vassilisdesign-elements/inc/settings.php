<?php
/**
 * Studio details (Appearance → Customize → Studio details), used by the header, footer,
 * contact widget and legal pages. Stored in the option 'vde_settings' (kept if the theme changes).
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** Defaults; edit them in the Customizer rather than here. */
function vde_studio_defaults() {
	return array(
		'vd_email'      => 'vassilisdesign@me.com',
		'vd_phone'      => '+30 694 769 0620',
		'vd_messaging'  => __( 'Viber and WhatsApp available', 'vassilisdesign-elements' ),
		'vd_clock_tz'   => 'Europe/Athens',
		'vd_clock_name' => __( 'Greece', 'vassilisdesign-elements' ),
		'vd_footer'     => __( "Independent design studio\nGreece", 'vassilisdesign-elements' ),
		'vd_wordmark_1' => 'VASSILIS',
		'vd_wordmark_2' => 'DESIGN',
	);
}

function vde_opt( $key ) {
	$defaults = vde_studio_defaults();
	$saved = (array) get_option( 'vde_settings', array() );
	return isset( $saved[ $key ] ) ? $saved[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'vd_studio',
			array(
				'title'    => __( 'Studio details', 'vassilisdesign-elements' ),
				'priority' => 30,
			)
		);
		$fields = array(
			'vd_wordmark_1' => array( __( 'Wordmark, first line', 'vassilisdesign-elements' ), 'text' ),
			'vd_wordmark_2' => array( __( 'Wordmark, second line', 'vassilisdesign-elements' ), 'text' ),
			'vd_email'      => array( __( 'Email', 'vassilisdesign-elements' ), 'email' ),
			'vd_phone'      => array( __( 'Phone', 'vassilisdesign-elements' ), 'text' ),
			'vd_messaging'  => array( __( 'Messaging note', 'vassilisdesign-elements' ), 'text' ),
			'vd_clock_name' => array( __( 'Clock label', 'vassilisdesign-elements' ), 'text' ),
			'vd_footer'     => array( __( 'Footer text', 'vassilisdesign-elements' ), 'textarea' ),
		);
		foreach ( $fields as $key => $f ) {
			$wp_customize->add_setting(
				'vde_settings[' . $key . ']',
				array(
					'type'              => 'option',
					'default'           => vde_studio_defaults()[ $key ],
					'sanitize_callback' => 'email' === $f[1] ? 'sanitize_email' : 'sanitize_textarea_field',
				)
			);
			$wp_customize->add_control(
				'vde_settings[' . $key . ']',
				array(
					'label'   => $f[0],
					'section' => 'vd_studio',
					'type'    => $f[1],
				)
			);
		}
	}
);

/**
 * One-time move of Studio and Legal details from the theme's Customizer storage (theme 1.x)
 * into the plugin's own option, so nothing has to be typed again.
 */
add_action(
	'init',
	function () {
		if ( false !== get_option( 'vde_settings', false ) ) {
			return;
		}
		$saved = array();
		foreach ( array_merge( array_keys( vde_studio_defaults() ), array_keys( vde_legal_fields() ) ) as $key ) {
			$value = get_theme_mod( $key, null );
			if ( null !== $value && '' !== $value ) {
				$saved[ $key ] = $value;
			}
		}
		add_option( 'vde_settings', $saved );
	},
	1
);
