<?php
/**
 * VASSILISDESIGN Elementor widgets. Each widget outputs the design system's markup and relies on
 * the theme stylesheet only (no per-widget CSS or JS), so pages stay light.
 *
 * @package vassilisdesign
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

function vd_widget_classes() {
	return array(
		'VD_Widget_Hero',
		'VD_Widget_Section_Head',
		'VD_Widget_Works_Grid',
		'VD_Widget_Logo_Wall',
		'VD_Widget_Numbered_List',
		'VD_Widget_Studio',
		'VD_Widget_Client_Index',
		'VD_Widget_Contact_Intro',
		'VD_Widget_Work_Header',
		'VD_Widget_Work_Intro',
		'VD_Widget_Work_Gallery',
		'VD_Widget_Next_Work',
		'VD_Widget_Works_Archive',
	);
}

abstract class VD_Widget extends Widget_Base {
	public function get_categories() {
		return array( 'vassilisdesign' );
	}
	public function get_icon() {
		return 'eicon-font';
	}
	public function has_widget_inner_wrapper(): bool {
		return false;
	}
	protected function is_dynamic_content(): bool {
		return false;
	}
	/** Image markup from a MEDIA control value. */
	protected function media_img( $media, $size = 'large', $eager = false, $alt = '' ) {
		if ( ! empty( $media['id'] ) ) {
			return vd_img( $media['id'], $size, $eager, $alt );
		}
		if ( ! empty( $media['url'] ) ) {
			return sprintf( '<img class="vd-photo-img" src="%s" alt="%s" loading="%s" decoding="async">', esc_url( $media['url'] ), esc_attr( $alt ), $eager ? 'eager' : 'lazy' );
		}
		return '';
	}
	protected function start( $label ) {
		$this->start_controls_section( 'content', array( 'label' => $label ) );
	}
}

/* ---------- Hero ---------- */

class VD_Widget_Hero extends VD_Widget {
	public function get_name() {
		return 'vd-hero';
	}
	public function get_title() {
		return __( 'VD Hero', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Hero', 'vassilisdesign' ) );
		$this->add_control( 'line1', array( 'label' => __( 'Wordmark line 1', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'VASSILIS' ) );
		$this->add_control( 'line2', array( 'label' => __( 'Wordmark line 2', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'DESIGN' ) );
		$this->add_control( 'label', array( 'label' => __( 'Accessible name', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'VASSILISDESIGN, independent design studio' ) );
		$this->add_control( 'lead', array( 'label' => __( 'Positioning', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Independent design studio.\nDistinctive identities.\nMeaningful design." ) );
		$this->add_control( 'meta_a', array( 'label' => __( 'Column 1', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Brand identity\nPackaging and labels\nEditorial and print\nWeb design\nDigital communication" ) );
		$this->add_control( 'meta_b', array( 'label' => __( 'Column 2', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Based in Greece\nClients in Greece, the US and the UK" ) );
		$this->add_control( 'vertical', array( 'label' => __( 'Vertical text', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Independent design studio\nGreece and the United States" ) );
		$this->add_control( 'corner', array( 'label' => __( 'Rotated corner text', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'GR / US' ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'vassilisdesign' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control( 'image_alt', array( 'label' => __( 'Image description', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'Wine labels designed by VASSILISDESIGN' ) );
		$this->add_control(
			'fit',
			array(
				'label'   => __( 'Image display', 'vassilisdesign' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'contain',
				'options' => array(
					'contain' => __( 'Packshot on white: fit', 'vassilisdesign' ),
					'cover'   => __( 'Photo: fill', 'vassilisdesign' ),
				),
			)
		);
		$this->add_control( 'cta', array( 'label' => __( 'Arrow button link', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => '#work' ) );
		$this->add_control( 'cta_label', array( 'label' => __( 'Arrow button label', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'See selected work' ) );
		$this->end_controls_section();
	}
	protected function render() {
		$s     = $this->get_settings_for_display();
		$chars = function ( $word ) {
			$out = '';
			foreach ( preg_split( '//u', (string) $word, -1, PREG_SPLIT_NO_EMPTY ) as $ch ) {
				$out .= '<span class="vd-ch">' . esc_html( $ch ) . '</span>';
			}
			return $out;
		};
		?>
		<section class="vd-hero vd-wrap" aria-label="<?php echo esc_attr( $s['label'] ); ?>">
			<h1 class="vd-wordmark" aria-label="<?php echo esc_attr( $s['label'] ); ?>">
				<span aria-hidden="true">
					<span class="vd-wm-line"><?php echo $chars( $s['line1'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="vd-wm-line"><?php echo $chars( $s['line2'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="vd-ch vd-stop">.</span></span>
				</span>
			</h1>
			<p class="vd-hero-lead"><?php echo vd_lines( $s['lead'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php if ( $s['corner'] ) : ?>
				<p class="vd-hero-corner" aria-hidden="true"><?php echo esc_html( $s['corner'] ); ?></p>
			<?php endif; ?>
			<p class="vd-hero-meta vd-hero-meta-a"><?php echo vd_lines( $s['meta_a'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<p class="vd-hero-meta vd-hero-meta-b"><?php echo vd_lines( $s['meta_b'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php if ( $s['vertical'] ) : ?>
				<p class="vd-hero-vertical"><?php echo vd_lines( $s['vertical'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<div class="vd-hero-media">
				<div class="vd-hero-photo vd-photo<?php echo 'contain' === $s['fit'] ? ' vd-fit-contain' : ''; ?>">
					<?php echo $this->media_img( $s['image'], 'full', true, $s['image_alt'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="vd-hero-band" aria-hidden="true"></div>
				<?php if ( $s['cta'] ) : ?>
					<a class="vd-hero-cta vd-square" href="<?php echo esc_url( $s['cta'] ); ?>" aria-label="<?php echo esc_attr( $s['cta_label'] ); ?>"><?php echo vd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* ---------- Section heading ---------- */

class VD_Widget_Section_Head extends VD_Widget {
	public function get_name() {
		return 'vd-section-head';
	}
	public function get_title() {
		return __( 'VD Section heading', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Heading', 'vassilisdesign' ) );
		$this->add_control( 'intro', array( 'label' => __( 'Intro (left)', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control( 'title', array( 'label' => __( 'Title (right, shown lowercase with a red full stop)', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'Selected work' ) );
		$this->add_control(
			'tag',
			array(
				'label'   => __( 'Heading level', 'vassilisdesign' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="vd-section-head vd-wrap">';
		if ( $s['intro'] ) {
			echo '<p class="vd-section-intro"><span class="vd-dot" aria-hidden="true"></span>' . esc_html( $s['intro'] ) . '</p>';
		}
		echo vd_display_title( $s['title'], $s['tag'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
}

/* ---------- Works grid ---------- */

class VD_Widget_Works_Grid extends VD_Widget {
	public function get_name() {
		return 'vd-works-grid';
	}
	public function get_title() {
		return __( 'VD Works grid', 'vassilisdesign' );
	}
	protected function is_dynamic_content(): bool {
		return true;
	}
	protected function register_controls() {
		$this->start( __( 'Works', 'vassilisdesign' ) );
		$this->add_control( 'count', array( 'label' => __( 'Number of Works', 'vassilisdesign' ), 'type' => Controls_Manager::NUMBER, 'default' => 10, 'min' => 1, 'max' => 48, 'description' => __( 'Tip: a multiple of the Works per row fills every row.', 'vassilisdesign' ) ) );
		$options = array( '' => __( 'All sectors', 'vassilisdesign' ) );
		$terms   = get_terms( array( 'taxonomy' => 'work_sector', 'hide_empty' => false ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		$this->add_control(
			'source',
			array(
				'label'   => __( 'Show', 'vassilisdesign' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'    => __( 'All Works (by Order)', 'vassilisdesign' ),
					'sector' => __( 'Works in one Sector', 'vassilisdesign' ),
					'pick'   => __( 'Works I choose', 'vassilisdesign' ),
				),
			)
		);
		unset( $options[''] );
		$this->add_control(
			'sector',
			array(
				'label'     => __( 'Sector', 'vassilisdesign' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array( '' => __( 'Choose a Sector', 'vassilisdesign' ) ) + $options,
				'condition' => array( 'source' => 'sector' ),
			)
		);
		$works = array();
		foreach ( get_posts( array( 'post_type' => 'works', 'post_status' => 'publish', 'posts_per_page' => 200, 'orderby' => 'title', 'order' => 'ASC' ) ) as $work ) {
			$works[ $work->ID ] = $work->post_title;
		}
		$this->add_control(
			'works',
			array(
				'label'       => __( 'Works (in this order)', 'vassilisdesign' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $works,
				'default'     => array(),
				'condition'   => array( 'source' => 'pick' ),
			)
		);
		$this->add_control( 'all_link', array( 'label' => __( '"All work" link text (empty to hide)', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'All work' ) );
		$this->add_control(
			'order_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Order Works with the "Order" field on each Work (lowest first).', 'vassilisdesign' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'vassilisdesign' ) ) );
		$this->add_control(
			'columns',
			array(
				'label'       => __( 'Works per row (desktop)', 'vassilisdesign' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '5',
				'options'     => array( '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'description' => __( 'Tablets show 3 per row, phones 2.', 'vassilisdesign' ),
			)
		);
		$this->add_control(
			'full',
			array(
				'label'        => __( 'Full width (no side margins)', 'vassilisdesign' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control( 'gap', array( 'label' => __( 'Space between Works (px, empty = default)', 'vassilisdesign' ), 'type' => Controls_Manager::NUMBER, 'default' => '', 'min' => 0, 'max' => 80 ) );
		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Red cover with text on hover', 'vassilisdesign' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Shows the Work\'s Excerpt, or the start of its content.', 'vassilisdesign' ),
			)
		);
		$this->add_control(
			'captions',
			array(
				'label'        => __( 'Title and details under each image', 'vassilisdesign' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'staggered',
			array(
				'label'        => __( 'Staggered heights', 'vassilisdesign' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'On: each column has its own image height. Off: all images the same size.', 'vassilisdesign' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$s      = $this->get_settings_for_display();
		$raw    = (array) ( $this->get_raw_data()['settings'] ?? array() );
		$sector = isset( $raw['sector'] ) ? (string) $raw['sector'] : ''; // Read raw: Elementor blanks settings whose condition is not met.
		$source = isset( $raw['source'] ) ? $s['source'] : ( '' !== $sector ? 'sector' : 'all' ); // Widgets saved before "Show" existed keep their Sector.
		$full   = 'yes' === $s['full'];
		$grid   = vd_works_grid(
			array(
				'count'     => $s['count'],
				'sector'    => 'sector' === $source ? $sector : '',
				'ids'       => 'pick' === $source ? (array) $s['works'] : array(),
				'columns'   => $s['columns'],
				'gap'       => $s['gap'],
				'overlay'   => 'yes' === $s['overlay'],
				'captions'  => 'yes' === $s['captions'],
				'staggered' => 'yes' === $s['staggered'],
			)
		);
		echo $full ? '<div class="vd-bleed">' . $grid . '</div>' : '<div class="vd-wrap">' . $grid . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $s['all_link'] ) {
			printf( '<div class="vd-wrap"><p class="vd-all-link"><a class="vd-text-link" href="%s">%s %s</a></p></div>', esc_url( get_post_type_archive_link( 'works' ) ), esc_html( $s['all_link'] ), vd_arrow() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/* ---------- Logo wall ---------- */

class VD_Widget_Logo_Wall extends VD_Widget {
	public function get_name() {
		return 'vd-logo-wall';
	}
	public function get_title() {
		return __( 'VD Logo wall', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Logos', 'vassilisdesign' ) );
		$this->add_control( 'logos', array( 'label' => __( 'Logos (on white, any size)', 'vassilisdesign' ), 'type' => Controls_Manager::GALLERY, 'default' => array() ) );
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['logos'] ) ) {
			return;
		}
		echo '<div class="vd-wrap"><ul class="vd-logos" role="list">';
		foreach ( $s['logos'] as $logo ) {
			$alt = ! empty( $logo['id'] ) ? get_post_meta( $logo['id'], '_wp_attachment_image_alt', true ) : '';
			if ( ! $alt && ! empty( $logo['id'] ) ) {
				$alt = get_the_title( $logo['id'] );
			}
			echo '<li class="vd-logo-cell"><span class="vd-logo-frame">' . $this->media_img( $logo, 'medium_large', false, $alt ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</ul></div>';
	}
}

/* ---------- Numbered list ---------- */

class VD_Widget_Numbered_List extends VD_Widget {
	public function get_name() {
		return 'vd-numbered-list';
	}
	public function get_title() {
		return __( 'VD Numbered list', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Items', 'vassilisdesign' ) );
		$r = new Repeater();
		$r->add_control( 'title', array( 'label' => __( 'Title', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'vassilisdesign' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['items'] ) ) {
			return;
		}
		echo '<div class="vd-wrap"><ol class="vd-steps" role="list">';
		foreach ( array_values( $s['items'] ) as $i => $item ) {
			printf(
				'<li class="vd-step"><span class="vd-step-n" aria-hidden="true">%s</span><h3 class="vd-step-title">%s</h3><p class="vd-step-text">%s</p></li>',
				esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ),
				esc_html( $item['title'] ),
				esc_html( $item['text'] )
			);
		}
		echo '</ol></div>';
	}
}

/* ---------- Studio ---------- */

class VD_Widget_Studio extends VD_Widget {
	public function get_name() {
		return 'vd-studio';
	}
	public function get_title() {
		return __( 'VD Studio', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Studio', 'vassilisdesign' ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'Studio' ) );
		$this->add_control( 'red_title', array( 'label' => __( 'Red box label', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'What we hold to' ) );
		$this->add_control( 'red_list', array( 'label' => __( 'Red box list (one per line)', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Typography first\nDetail and proportion\nBuilt for production\nDesign and technology" ) );
		$this->add_control( 'side_title', array( 'label' => __( 'Side label', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'Industries' ) );
		$this->add_control( 'side_list', array( 'label' => __( 'Side list (one per line)', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Wine\nHospitality and tourism\nFood and beverage\nProfessional services" ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'vassilisdesign' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control( 'text', array( 'label' => __( 'Text (first paragraph is shown large)', 'vassilisdesign' ), 'type' => Controls_Manager::WYSIWYG ) );
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="vd-studio-grid vd-wrap">
			<?php echo vd_display_title( $s['title'], 'h2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="vd-studio-red">
				<p class="vd-studio-red-title"><?php echo esc_html( $s['red_title'] ); ?></p>
				<p class="vd-studio-red-list"><?php echo vd_lines( $s['red_list'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<div class="vd-studio-side">
				<p class="vd-red-label"><?php echo esc_html( $s['side_title'] ); ?></p>
				<p class="vd-studio-side-list"><?php echo vd_lines( $s['side_list'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<div class="vd-studio-media">
				<span class="vd-studio-circle" aria-hidden="true"></span>
				<div class="vd-studio-photo vd-photo vd-fit-contain"><?php echo $this->media_img( $s['image'], 'large', false, '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
			<div class="vd-studio-copy"><?php echo wp_kses_post( $s['text'] ); ?></div>
			<span class="vd-plus" aria-hidden="true"></span>
			<span class="vd-dots" aria-hidden="true"></span>
		</div>
		<?php
	}
}

/* ---------- Client index ---------- */

class VD_Widget_Client_Index extends VD_Widget {
	public function get_name() {
		return 'vd-client-index';
	}
	public function get_title() {
		return __( 'VD Client index', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Clients', 'vassilisdesign' ) );
		$r = new Repeater();
		$r->add_control( 'title', array( 'label' => __( 'Sector', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'names', array( 'label' => __( 'Clients (one per line)', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 10 ) );
		$this->add_control(
			'groups',
			array(
				'label'       => __( 'Groups', 'vassilisdesign' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['groups'] ) ) {
			return;
		}
		echo '<div class="vd-wrap vd-client-groups">';
		foreach ( $s['groups'] as $group ) {
			$names = array_filter( array_map( 'trim', explode( "\n", (string) $group['names'] ) ) );
			echo '<div class="vd-client-group"><h3 class="vd-client-sector">' . esc_html( $group['title'] ) . '</h3><ul class="vd-client-list" role="list">';
			foreach ( $names as $name ) {
				echo '<li>' . esc_html( $name ) . '</li>';
			}
			echo '</ul></div>';
		}
		echo '</div>';
	}
}

/* ---------- Contact intro ---------- */

class VD_Widget_Contact_Intro extends VD_Widget {
	public function get_name() {
		return 'vd-contact-intro';
	}
	public function get_title() {
		return __( 'VD Contact intro', 'vassilisdesign' );
	}
	protected function register_controls() {
		$this->start( __( 'Contact', 'vassilisdesign' ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'Start a project' ) );
		$this->add_control( 'lead', array( 'label' => __( 'Text', 'vassilisdesign' ), 'type' => Controls_Manager::TEXTAREA, 'default' => 'Tell us about your brand, product or website. We reply to every enquiry.' ) );
		$this->add_control( 'direct', array( 'label' => __( 'Line above the contact details', 'vassilisdesign' ), 'type' => Controls_Manager::TEXT, 'default' => 'Or write to us directly' ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Email and phone come from Appearance → Customize → Studio details.', 'vassilisdesign' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="vd-contact-intro">';
		echo vd_display_title( $s['title'], 'h2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $s['lead'] ) {
			echo '<p class="vd-contact-lead">' . esc_html( $s['lead'] ) . '</p>';
		}
		echo '<p class="vd-contact-direct">' . ( $s['direct'] ? esc_html( $s['direct'] ) . '<br>' : '' ) . vd_contact_lines() . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
}

/* ---------- Single Work (for Elementor Pro Theme Builder) ---------- */

abstract class VD_Work_Widget extends VD_Widget {
	protected function is_dynamic_content(): bool {
		return true;
	}
	protected function register_controls() {
		$this->start( __( 'Work', 'vassilisdesign' ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Shows the current Work. Use inside a Theme Builder "Single" template for Works.', 'vassilisdesign' ),
			)
		);
		$this->end_controls_section();
	}
	/** The current Work, or the latest one while previewing a template in the editor. */
	protected function work() {
		$post = get_post();
		if ( $post && 'works' === $post->post_type ) {
			return $post;
		}
		$latest = get_posts( array( 'post_type' => 'works', 'posts_per_page' => 1 ) );
		return $latest ? $latest[0] : null;
	}
}

class VD_Widget_Work_Header extends VD_Work_Widget {
	public function get_name() {
		return 'vd-work-header';
	}
	public function get_title() {
		return __( 'VD Work header', 'vassilisdesign' );
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vd_work_header( $w ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

class VD_Widget_Work_Intro extends VD_Work_Widget {
	public function get_name() {
		return 'vd-work-intro';
	}
	public function get_title() {
		return __( 'VD Work cover and summary', 'vassilisdesign' );
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vd_work_intro( $w ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

class VD_Widget_Work_Gallery extends VD_Work_Widget {
	public function get_name() {
		return 'vd-work-gallery';
	}
	public function get_title() {
		return __( 'VD Work gallery', 'vassilisdesign' );
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vd_work_gallery( $w ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

class VD_Widget_Works_Archive extends VD_Widget {
	public function get_name() {
		return 'vd-works-archive';
	}
	public function get_title() {
		return __( 'VD Works archive', 'vassilisdesign' );
	}
	protected function is_dynamic_content(): bool {
		return true;
	}
	protected function register_controls() {
		$this->start( __( 'Works archive', 'vassilisdesign' ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Title, Sector filter and all Works of the current archive. Use inside a Theme Builder "Archive" template for Works.', 'vassilisdesign' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		echo vd_works_archive(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

class VD_Widget_Next_Work extends VD_Work_Widget {
	public function get_name() {
		return 'vd-next-work';
	}
	public function get_title() {
		return __( 'VD Next Work', 'vassilisdesign' );
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vd_next_work( $w ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
