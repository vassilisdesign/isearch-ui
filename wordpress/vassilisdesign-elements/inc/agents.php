<?php
/**
 * AI agents: an Agentic Resource Discovery (ARD) manifest at /.well-known/ard.json pointing to an
 * agent skill that explains the studio, the portfolio and how to get in touch; real 404s (instead of
 * WordPress guessing a page) for other /.well-known/ addresses; and WebMCP annotations on the contact form.
 *
 * Lighthouse "Agentic Browsing" checks the manifest (ard-schema) and the form annotations (webmcp-*).
 *
 * @see https://agenticresourcediscovery.org/spec/
 * @see https://developer.chrome.com/docs/ai/webmcp/declarative-api
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

/** Request path relative to the site root, e.g. "/.well-known/ard.json". */
function vde_agents_request_path() {
	$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( '/' !== $base && 0 === strpos( $path, rtrim( $base, '/' ) ) ) {
		$path = substr( $path, strlen( rtrim( $base, '/' ) ) );
	}
	return '/' . ltrim( $path, '/' );
}

/** The ARD manifest. */
function vde_ard_manifest() {
	$host  = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$name  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$works = get_posts(
		array(
			'post_type'      => 'works',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'modified',
			'no_found_rows'  => true,
		)
	);
	$updated = $works ? get_post_modified_time( 'c', true, $works[0] ) : gmdate( 'c' );

	return array(
		'entries' => array(
			array(
				'identifier'            => 'urn:air:' . $host . ':skills:studio-portfolio',
				'displayName'           => sprintf( '%s design studio: portfolio and contact', $name ),
				'type'                  => 'text/markdown; profile="urn:air:agent-skills"',
				'url'                   => home_url( '/.well-known/agent-skills/studio-portfolio.md' ),
				'description'           => sprintf( 'How to find, read and cite the work of %s, an independent design studio in Greece (brand identity, packaging and labels, print and editorial, websites), and how to contact the studio about a project.', $name ),
				'representativeQueries' => array(
					'design studio in Greece for brand identity and packaging',
					'wine label and winery branding designers in Greece',
					'hotel branding and website design studio Greece',
					'examples of food packaging design by a Greek studio',
				),
				'capabilities'          => array( 'PortfolioBrowse', 'ContactStudio' ),
				'tags'                  => array( 'design studio', 'branding', 'packaging', 'label design', 'web design', 'Greece' ),
				'updatedAt'             => $updated,
			),
		),
	);
}

/** The agent skill (Markdown) the manifest points to; built from the live Works, services and sectors. */
function vde_agent_skill_markdown() {
	$name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$home = home_url( '/' );
	$out  = "---\nname: studio-portfolio\ndescription: Find, read and cite the design work of {$name}, an independent design studio in Greece, and contact the studio about a project.\n---\n\n";
	$out .= "# {$name}\n\n";
	$out .= "{$name} is an independent design studio in Greece. It creates brand identities and logos, packaging and labels, print and editorial design, websites and art direction for clients in Greece, the United States and the United Kingdom, including wineries, hotels, food producers and professional firms.\n\n";
	$out .= "Website: {$home}\n\n";

	$arch = get_post_type_archive_link( 'works' );
	$out .= "## Browse the portfolio\n\n";
	if ( $arch ) {
		$out .= "- All works: {$arch}\n";
	}
	foreach ( array( 'work_service' => 'By service', 'work_sector' => 'By sector' ) as $tax => $label ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $tax,
				'hide_empty' => true,
			)
		);
		if ( is_wp_error( $terms ) || ! $terms ) {
			continue;
		}
		$links = array();
		foreach ( $terms as $term ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$links[] = '[' . wp_specialchars_decode( $term->name, ENT_QUOTES ) . '](' . $link . ')';
			}
		}
		$out .= "- {$label}: " . implode( ', ', $links ) . "\n";
	}

	$works = get_posts(
		array(
			'post_type'      => 'works',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	if ( $works ) {
		$out .= "\n## Works\n\n";
		foreach ( $works as $work ) {
			$excerpt = trim( wp_strip_all_tags( get_the_excerpt( $work ) ) );
			$out    .= '- [' . wp_specialchars_decode( get_the_title( $work ), ENT_QUOTES ) . '](' . get_permalink( $work ) . ')' . ( $excerpt ? ': ' . $excerpt : '' ) . "\n";
		}
	}

	$out .= "\n## Contact the studio\n\n";
	$out .= "Use the contact form on the home page ({$home}#contact). Fields: name, email (required) and message. The form is also exposed to browser agents as the WebMCP tool `contactStudio`. Only send a message when the person you act for has asked for it and confirmed the text.\n\n";
	$out .= "## Citing\n\nRefer to the studio as {$name} and link the Work page you cite.\n";
	return $out;
}

/* Serve the manifest and the skill. */
add_action(
	'wp_loaded',
	function () {
		if ( is_admin() || wp_doing_ajax() || ! apply_filters( 'vde_agent_discovery', true ) ) {
			return;
		}
		$path = vde_agents_request_path();
		if ( '/.well-known/ard.json' === $path ) {
			status_header( 200 );
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Access-Control-Allow-Origin: *' );
			header( 'Cache-Control: public, max-age=3600' );
			header( 'X-Robots-Tag: noindex' );
			echo wp_json_encode( vde_ard_manifest(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			exit;
		}
		if ( '/.well-known/agent-skills/studio-portfolio.md' === $path ) {
			status_header( 200 );
			header( 'Content-Type: text/markdown; charset=utf-8' );
			header( 'Access-Control-Allow-Origin: *' );
			header( 'Cache-Control: public, max-age=3600' );
			header( 'X-Robots-Tag: noindex' );
			echo vde_agent_skill_markdown(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text
			exit;
		}
	}
);

/* Machine addresses (/.well-known/...) answer 404 instead of redirecting to a page WordPress guesses. */
add_filter(
	'do_redirect_guess_404_permalink',
	function ( $do ) {
		return 0 === strpos( vde_agents_request_path(), '/.well-known/' ) ? false : $do;
	}
);

/* Agents find the manifest from every page too. */
add_action(
	'wp_head',
	function () {
		if ( apply_filters( 'vde_agent_discovery', true ) ) {
			printf( '<link rel="ard" href="%s" type="application/json">' . "\n", esc_url( home_url( '/.well-known/ard.json' ) ) );
		}
	},
	2
);

/* ---------- WebMCP: the contact form as an agent tool ---------- */

/*
 * Chrome's WebMCP turns an annotated <form> into a tool an AI agent can fill in for the visitor; the
 * visitor still sees the form and, without toolautosubmit, presses Send. The fields keep their labels,
 * which become the tool's parameter descriptions. Elementor's hidden spam trap is described as "leave empty".
 */
add_filter(
	'elementor/widget/render_content',
	function ( $content, $widget ) {
		if ( 'form' !== $widget->get_name() || false === strpos( $content, '<form' ) || false !== strpos( $content, 'toolname=' ) ) {
			return $content;
		}
		$content = preg_replace(
			'/<form\b([^>]*?)\saria-label="[^"]*"/',
			'<form$1 aria-label="' . esc_attr__( 'Contact form', 'vassilisdesign-elements' ) . '"',
			$content,
			1
		);
		$content = preg_replace(
			'/<form\b/',
			'<form toolname="contactStudio" tooldescription="' . esc_attr( sprintf( 'Send a project enquiry to %s, a design studio in Greece. Fill in the name, email and message; the visitor reviews and sends it.', get_bloginfo( 'name' ) ) ) . '"',
			$content,
			1
		);
		// Spam trap: an input hidden with display:none and no label.
		$content = preg_replace(
			'/(<input\b[^>]*style="display:none !important;")/',
			'$1 toolparamdescription="Spam check. Always leave this empty." aria-hidden="true" tabindex="-1" autocomplete="off"',
			$content
		);
		return $content;
	},
	10,
	2
);
