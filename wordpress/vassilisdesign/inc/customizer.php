<?php
/**
 * Studio details (Appearance → Customize → Studio details), used by the header, footer,
 * contact widget and legal pages.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

/** Defaults; edit them in the Customizer rather than here. */
function vd_studio_defaults() {
	return array(
		'vd_email'      => 'vassilisdesign@me.com',
		'vd_phone'      => '+30 694 769 0620',
		'vd_messaging'  => __( 'Viber and WhatsApp available', 'vassilisdesign' ),
		'vd_clock_tz'   => 'Europe/Athens',
		'vd_clock_name' => __( 'Greece', 'vassilisdesign' ),
		'vd_footer'     => __( "Independent design studio\nGreece", 'vassilisdesign' ),
		'vd_wordmark_1' => 'VASSILIS',
		'vd_wordmark_2' => 'DESIGN',
	);
}

function vd_opt( $key ) {
	$defaults = vd_studio_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'vd_studio',
			array(
				'title'    => __( 'Studio details', 'vassilisdesign' ),
				'priority' => 30,
			)
		);
		$fields = array(
			'vd_wordmark_1' => array( __( 'Wordmark, first line', 'vassilisdesign' ), 'text' ),
			'vd_wordmark_2' => array( __( 'Wordmark, second line', 'vassilisdesign' ), 'text' ),
			'vd_email'      => array( __( 'Email', 'vassilisdesign' ), 'email' ),
			'vd_phone'      => array( __( 'Phone', 'vassilisdesign' ), 'text' ),
			'vd_messaging'  => array( __( 'Messaging note', 'vassilisdesign' ), 'text' ),
			'vd_clock_name' => array( __( 'Clock label', 'vassilisdesign' ), 'text' ),
			'vd_footer'     => array( __( 'Footer text', 'vassilisdesign' ), 'textarea' ),
		);
		foreach ( $fields as $key => $f ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => vd_studio_defaults()[ $key ],
					'sanitize_callback' => 'email' === $f[1] ? 'sanitize_email' : 'sanitize_textarea_field',
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					'label'   => $f[0],
					'section' => 'vd_studio',
					'type'    => $f[1],
				)
			);
		}
	}
);
