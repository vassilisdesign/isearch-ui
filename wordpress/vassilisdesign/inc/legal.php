<?php
/**
 * Legal details (Appearance → Customize → Legal details).
 *
 * Fill these in once: every page that still contains the bracketed placeholders from the legal
 * templates ([LEGAL NAME], [ΑΦΜ] and so on, in English and Greek) is completed automatically when
 * the Customizer is saved or the theme setup runs. Only placeholders are replaced, so any text you
 * edited yourself on those pages stays as it is.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

/** Field => array( label, default ). */
function vd_legal_fields() {
	return array(
		'vd_legal_name'     => array( __( 'Legal name (person or company)', 'vassilisdesign' ), '' ),
		'vd_legal_owner'    => array( __( 'Person responsible for content', 'vassilisdesign' ), '' ),
		'vd_legal_street'   => array( __( 'Street address', 'vassilisdesign' ), '' ),
		'vd_legal_postcode' => array( __( 'Postcode', 'vassilisdesign' ), '' ),
		'vd_legal_city'     => array( __( 'City', 'vassilisdesign' ), '' ),
		'vd_legal_vat'      => array( __( 'VAT number (ΑΦΜ)', 'vassilisdesign' ), '' ),
		'vd_legal_tax'      => array( __( 'Tax office (ΔΟΥ)', 'vassilisdesign' ), '' ),
		'vd_legal_gemi'     => array( __( 'GEMI number (ΓΕΜΗ), or "Not registered"', 'vassilisdesign' ), '' ),
		'vd_legal_court'    => array( __( 'Courts of (city)', 'vassilisdesign' ), '' ),
		'vd_legal_hosting'  => array( __( 'Hosting provider (name, country)', 'vassilisdesign' ), '' ),
		'vd_legal_mail'     => array( __( 'Email provider', 'vassilisdesign' ), 'Apple iCloud Mail (Apple Distribution International Ltd., Ireland)' ),
		'vd_legal_account'  => array( __( 'Accountant or accounting software (optional)', 'vassilisdesign' ), '' ),
		'vd_legal_logdays'  => array( __( 'Server log retention (days)', 'vassilisdesign' ), '14' ),
	);
}

function vd_legal( $key ) {
	$fields = vd_legal_fields();
	return trim( (string) get_theme_mod( $key, isset( $fields[ $key ] ) ? $fields[ $key ][1] : '' ) );
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'vd_legal',
			array(
				'title'       => __( 'Legal details', 'vassilisdesign' ),
				'description' => __( 'Used to complete the Privacy Policy, Terms of Use and Legal Notice (English and Greek). Saving fills in every placeholder that is still on those pages.', 'vassilisdesign' ),
				'priority'    => 31,
			)
		);
		foreach ( vd_legal_fields() as $key => $field ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $field[1],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					'label'   => $field[0],
					'section' => 'vd_legal',
					'type'    => 'text',
				)
			);
		}
	}
);

/** Placeholder => value. Empty values are left as placeholders so nothing is published half-filled. */
function vd_legal_replacements() {
	$account_en = vd_legal( 'vd_legal_account' ) ? vd_legal( 'vd_legal_account' ) : 'our external accountant';
	$account_el = vd_legal( 'vd_legal_account' ) ? vd_legal( 'vd_legal_account' ) : 'εξωτερικός λογιστής';
	$court      = vd_legal( 'vd_legal_court' ) ? vd_legal( 'vd_legal_court' ) : vd_legal( 'vd_legal_city' );
	$map        = array(
		// Terms: court city first, so the general [CITY] below does not take it.
		'courts of [CITY]'                                   => $court ? 'courts of ' . $court : '',
		'δικαστήρια της [ΠΟΛΗ]'                              => $court ? 'δικαστήρια της ' . $court : '',
		'[LEGAL NAME]'                                       => vd_legal( 'vd_legal_name' ),
		'[ΕΠΩΝΥΜΙΑ]'                                         => vd_legal( 'vd_legal_name' ),
		'[FULL NAME]'                                        => vd_legal( 'vd_legal_owner' ),
		'[ΟΝΟΜΑΤΕΠΩΝΥΜΟ]'                                    => vd_legal( 'vd_legal_owner' ),
		'[STREET ADDRESS]'                                   => vd_legal( 'vd_legal_street' ),
		'[ΔΙΕΥΘΥΝΣΗ]'                                        => vd_legal( 'vd_legal_street' ),
		'[POSTCODE]'                                         => vd_legal( 'vd_legal_postcode' ),
		'[Τ.Κ.]'                                             => vd_legal( 'vd_legal_postcode' ),
		'[CITY]'                                             => vd_legal( 'vd_legal_city' ),
		'[ΠΟΛΗ]'                                             => vd_legal( 'vd_legal_city' ),
		'[VAT NUMBER]'                                       => vd_legal( 'vd_legal_vat' ),
		'[ΑΦΜ]'                                              => vd_legal( 'vd_legal_vat' ),
		'[TAX OFFICE]'                                       => vd_legal( 'vd_legal_tax' ),
		'[ΔΟΥ]'                                              => vd_legal( 'vd_legal_tax' ),
		'[GEMI NUMBER, if registered]'                       => vd_legal( 'vd_legal_gemi' ),
		'[ΑΡΙΘΜΟΣ ΓΕΜΗ, εφόσον υπάρχει]'                     => vd_legal( 'vd_legal_gemi' ),
		'[HOSTING PROVIDER NAME, ADDRESS, COUNTRY]'          => vd_legal( 'vd_legal_hosting' ),
		'[HOSTING PROVIDER, COUNTRY]'                        => vd_legal( 'vd_legal_hosting' ),
		'[ΠΑΡΟΧΟΣ ΦΙΛΟΞΕΝΙΑΣ, ΔΙΕΥΘΥΝΣΗ, ΧΩΡΑ]'              => vd_legal( 'vd_legal_hosting' ),
		'[ΠΑΡΟΧΟΣ ΦΙΛΟΞΕΝΙΑΣ, ΧΩΡΑ]'                         => vd_legal( 'vd_legal_hosting' ),
		'[EMAIL PROVIDER, for example Apple iCloud Mail]'    => vd_legal( 'vd_legal_mail' ),
		'[ΠΑΡΟΧΟΣ EMAIL, για παράδειγμα Apple iCloud Mail]'  => vd_legal( 'vd_legal_mail' ),
		'[ACCOUNTANT OR ACCOUNTING SOFTWARE]'                => $account_en,
		'[ΛΟΓΙΣΤΗΣ Ή ΛΟΓΙΣΜΙΚΟ]'                             => $account_el,
		'[14]'                                               => vd_legal( 'vd_legal_logdays' ),
	);
	return array_filter( $map, 'strlen' );
}

/** Completes the placeholders on all pages. Returns the titles of the pages that changed. */
function vd_fill_legal_pages() {
	$map     = vd_legal_replacements();
	$changed = array();
	if ( ! $map ) {
		return $changed;
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 200,
			's'              => '[',
			'search_columns' => array( 'post_content' ),
		)
	);
	foreach ( $pages as $page ) {
		$content = strtr( $page->post_content, $map );
		if ( $content !== $page->post_content ) {
			wp_update_post(
				array(
					'ID'           => $page->ID,
					'post_content' => $content,
				)
			);
			$changed[] = $page->post_title;
		}
	}
	return $changed;
}

/** Placeholders still left on any page (shown on the setup screen). */
function vd_legal_missing() {
	$left  = array();
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 200,
			's'              => '[',
			'search_columns' => array( 'post_content' ),
		)
	);
	foreach ( $pages as $page ) {
		if ( preg_match_all( '/\[(?:[A-ZΑ-ΩΆΈΉΊΌΎΏ][A-ZΑ-ΩΆΈΉΊΌΎΏ .,()a-zα-ωά-ώ]*|14)\]/u', $page->post_content, $m ) ) {
			$left[ $page->post_title ] = array_unique( $m[0] );
		}
	}
	return $left;
}

// Saving the Customizer completes the legal pages.
add_action(
	'customize_save_after',
	function () {
		if ( current_user_can( 'edit_pages' ) ) {
			vd_fill_legal_pages();
		}
	}
);
