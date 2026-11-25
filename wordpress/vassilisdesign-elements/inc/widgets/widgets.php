<?php
/**
 * VASSILISDESIGN Elementor widgets. Each widget outputs the design system's markup and relies on
 * the theme stylesheet only (no per-widget CSS or JS), so pages stay light.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

function vde_widget_classes() {
	return array(
		'VDE_Widget_Hero',
		'VDE_Widget_Hero_Image',
		'VDE_Widget_Section_Head',
		'VDE_Widget_Works_Grid',
		'VDE_Widget_Logo_Wall',
		'VDE_Widget_Numbered_List',
		'VDE_Widget_Studio',
		'VDE_Widget_Client_Index',
		'VDE_Widget_Contact_Intro',
		'VDE_Widget_Work_Header',
		'VDE_Widget_Work_Intro',
		'VDE_Widget_Work_Gallery',
		'VDE_Widget_Next_Work',
		'VDE_Widget_Works_Archive',
		'VDE_Widget_Nav_Menu',
	);
}

/** An icon chosen in an Elementor Icons control (SVG or icon font), or the fallback markup when none is chosen. */
function vde_icon_html( $icon, $fallback = '' ) {
	if ( empty( $icon['value'] ) || ! class_exists( '\Elementor\Icons_Manager' ) ) {
		return $fallback;
	}
	ob_start();
	\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
	$html = trim( (string) ob_get_clean() );
	return '' === $html ? $fallback : $html;
}

abstract class VDE_Widget extends Widget_Base {
	public function get_categories() {
		return array( 'vassilisdesign-elements' );
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
			return vde_img( $media['id'], $size, $eager, $alt );
		}
		if ( ! empty( $media['url'] ) ) {
			return sprintf( '<img class="vd-photo-img" src="%s" alt="%s" loading="%s" decoding="async">', esc_url( $media['url'] ), esc_attr( $alt ), $eager ? 'eager' : 'lazy' );
		}
		return '';
	}
	/**
	 * Style tab: font and colour controls for each part of the widget, scoped to this widget only, separately per device.
	 * Items: array( 'text', key, label, selector ) adds Typography + Color;
	 *        array( 'color', key, label, selector, css property ) adds one colour.
	 */
	protected function add_style_controls( array $items ) {
		if ( ! $items ) {
			return;
		}
		$this->start_controls_section(
			'vd_style',
			array(
				'label' => __( 'Fonts and colours', 'vassilisdesign-elements' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		/*
		 * Every font setting and colour can differ per device (the screen icons next to each setting).
		 * Desktop values apply everywhere until a tablet or mobile value is set.
		 */
		$responsive_type = array();
		foreach ( array( 'font_family', 'font_weight', 'text_transform', 'font_style', 'text_decoration' ) as $field ) {
			$responsive_type[ $field ] = array( 'responsive' => true );
		}
		foreach ( $items as $i => $item ) {
			$selector = implode( ', ', array_map( function ( $part ) {
				return '{{WRAPPER}} ' . trim( $part );
			}, explode( ',', $item[3] ) ) );
			if ( 'text' === $item[0] ) {
				$this->add_control( $item[1] . '_heading', array( 'label' => $item[2], 'type' => Controls_Manager::HEADING, 'separator' => $i ? 'before' : 'none' ) );
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'           => $item[1] . '_typography',
						'selector'       => $selector,
						'fields_options' => $responsive_type,
					)
				);
				$this->add_responsive_control( $item[1] . '_color', array( 'label' => __( 'Colour', 'vassilisdesign-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( $selector => 'color: {{VALUE}} !important;' ) ) );
			} else {
				$this->add_responsive_control( $item[1], array( 'label' => $item[2], 'type' => Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => array( $selector => $item[4] . ': {{VALUE}} !important;' ) ) );
			}
		}
		$this->end_controls_section();
	}
	/**
	 * Image placement like a container's background image: Display size, Position and Repeat,
	 * each responsive. The key 'fit' keeps the value saved by earlier versions (contain / cover).
	 */
	protected function add_image_bg_controls() {
		$sel = '{{WRAPPER}} .vd-photo-bg';
		$this->add_responsive_control(
			'fit',
			array(
				'label'     => __( 'Display size', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'contain',
				'options'   => array(
					'cover'   => __( 'Cover', 'vassilisdesign-elements' ),
					'contain' => __( 'Contain', 'vassilisdesign-elements' ),
					'auto'    => __( 'Auto', 'vassilisdesign-elements' ),
					'initial' => __( 'Custom', 'vassilisdesign-elements' ),
				),
				'separator' => 'before',
				'selectors' => array( $sel => 'background-size: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'fit_width',
			array(
				'label'      => __( 'Width', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 2000 ), '%' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => '%', 'size' => 100 ),
				'condition'  => array( 'fit' => 'initial' ),
				'selectors'  => array( $sel => 'background-size: {{SIZE}}{{UNIT}} auto;' ),
			)
		);
		$this->add_responsive_control(
			'bg_position',
			array(
				'label'     => __( 'Position', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array(
					'center center' => __( 'Center Center', 'vassilisdesign-elements' ),
					'center left'   => __( 'Center Left', 'vassilisdesign-elements' ),
					'center right'  => __( 'Center Right', 'vassilisdesign-elements' ),
					'top center'    => __( 'Top Center', 'vassilisdesign-elements' ),
					'top left'      => __( 'Top Left', 'vassilisdesign-elements' ),
					'top right'     => __( 'Top Right', 'vassilisdesign-elements' ),
					'bottom center' => __( 'Bottom Center', 'vassilisdesign-elements' ),
					'bottom left'   => __( 'Bottom Left', 'vassilisdesign-elements' ),
					'bottom right'  => __( 'Bottom Right', 'vassilisdesign-elements' ),
					'initial'       => __( 'Custom', 'vassilisdesign-elements' ),
				),
				'selectors' => array( $sel => 'background-position: {{VALUE}};' ),
			)
		);
		foreach ( array( 'x' => __( 'X Position', 'vassilisdesign-elements' ), 'y' => __( 'Y Position', 'vassilisdesign-elements' ) ) as $axis => $label ) {
			$this->add_responsive_control(
				'bg_' . $axis . 'pos',
				array(
					'label'      => $label,
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%', 'px' ),
					'range'      => array( '%' => array( 'min' => -100, 'max' => 200 ), 'px' => array( 'min' => -800, 'max' => 800 ) ),
					'default'    => array( 'unit' => '%', 'size' => 50 ),
					'condition'  => array( 'bg_position' => 'initial' ),
					'selectors'  => array( $sel => 'background-position-' . $axis . ': {{SIZE}}{{UNIT}};' ),
				)
			);
		}
		$this->add_responsive_control(
			'bg_repeat',
			array(
				'label'     => __( 'Repeat', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no-repeat',
				'options'   => array(
					'no-repeat' => __( 'No-repeat', 'vassilisdesign-elements' ),
					'repeat'    => __( 'Repeat', 'vassilisdesign-elements' ),
					'repeat-x'  => __( 'Repeat-x', 'vassilisdesign-elements' ),
					'repeat-y'  => __( 'Repeat-y', 'vassilisdesign-elements' ),
				),
				'selectors' => array( $sel => 'background-repeat: {{VALUE}};' ),
			)
		);
	}

	/** The image as a background layer (black and white), described for screen readers. */
	protected function image_bg( $media, $alt = '', $preload = false ) {
		$url = '';
		$id  = ! empty( $media['id'] ) ? (int) $media['id'] : 0;
		if ( $id ) {
			$url = vde_webp_url( $id ); // light WebP copy, made once
		}
		if ( ! $url && ! empty( $media['url'] ) ) {
			$url = $media['url'];
		}
		if ( ! $url ) {
			return '';
		}
		$out = '';
		if ( $preload && ( ! $id || vde_page_hero_image_id() !== $id ) ) { // already preloaded from <head> when it is this page's hero
			$out .= '<link rel="preload" as="image" href="' . esc_url( $url ) . '" fetchpriority="high">';
		}
		$a11y = $alt ? ' role="img" aria-label="' . esc_attr( $alt ) . '"' : ' aria-hidden="true"';
		return $out . '<div class="vd-photo-bg"' . $a11y . ' style="background-image:url(\'' . esc_url( $url ) . '\')"></div>';
	}
	protected function start( $label ) {
		$this->start_controls_section( 'content', array( 'label' => $label ) );
	}
}

/* ---------- Hero ---------- */

class VDE_Widget_Hero extends VDE_Widget {
	public function get_name() {
		return 'vd-hero';
	}
	public function get_title() {
		return __( 'VD Hero', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Hero', 'vassilisdesign-elements' ) );
		$this->add_control( 'line1', array( 'label' => __( 'Wordmark line 1', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'VASSILIS' ) );
		$this->add_control( 'line2', array( 'label' => __( 'Wordmark line 2', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'DESIGN' ) );
		$this->add_control( 'label', array( 'label' => __( 'Accessible name', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'VASSILISDESIGN, independent design studio' ) );
		if ( function_exists( 'vde_wordmark_svg_ready' ) && vde_wordmark_svg_ready() ) {
			$this->add_control( 'wordmark_svg', array( 'label' => __( 'SVG wordmark (no font download)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
			$this->add_responsive_control(
				'wordmark_svg_h',
				array(
					'label'      => __( 'SVG wordmark letter height', 'vassilisdesign-elements' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'em' ),
					'range'      => array( 'em' => array( 'min' => 0.4, 'max' => 1.2, 'step' => 0.01 ) ),
					'selectors'  => array( '{{WRAPPER}} .vd-wordmark' => '--vd-wm-h: {{SIZE}}em;' ),
					'condition'  => array( 'wordmark_svg' => 'yes' ),
				)
			);
		}
		$this->add_control( 'lead', array( 'label' => __( 'Positioning', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Independent design studio.\nDistinctive identities.\nMeaningful design." ) );
		$this->add_control( 'meta_a', array( 'label' => __( 'Column 1', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Brand identity\nPackaging and labels\nEditorial and print\nWeb design\nDigital communication" ) );
		$this->add_control( 'meta_b', array( 'label' => __( 'Column 2', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Based in Greece\nClients in Greece, the US and the UK" ) );
		$this->add_control( 'vertical', array( 'label' => __( 'Vertical text', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Independent design studio\nGreece and the United States" ) );
		$this->add_control( 'corner', array( 'label' => __( 'Rotated corner text', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'GR / US' ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'vassilisdesign-elements' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control( 'image_alt', array( 'label' => __( 'Image description', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'Wine labels designed by VASSILISDESIGN' ) );
		$this->add_image_bg_controls();
		$this->add_control( 'cta', array( 'label' => __( 'Arrow button link', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => '#work' ) );
		$this->add_control( 'cta_label', array( 'label' => __( 'Arrow button label', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'See selected work' ) );
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'wordmark', __( 'Wordmark', 'vassilisdesign-elements' ), '.vd-wordmark' ),
				array( 'color', 'stop', __( 'Wordmark full stop', 'vassilisdesign-elements' ), '.vd-wordmark .vd-stop', 'color' ),
				array( 'text', 'lead', __( 'Positioning', 'vassilisdesign-elements' ), '.vd-hero-lead' ),
				array( 'text', 'meta', __( 'Columns', 'vassilisdesign-elements' ), '.vd-hero-meta' ),
				array( 'color', 'meta_line', __( 'Column lines', 'vassilisdesign-elements' ), '.vd-hero-meta', 'border-top-color' ),
				array( 'text', 'vertical', __( 'Vertical text', 'vassilisdesign-elements' ), '.vd-hero-vertical' ),
				array( 'color', 'vertical_line', __( 'Vertical text line', 'vassilisdesign-elements' ), '.vd-hero-vertical', 'border-left-color' ),
				array( 'text', 'corner', __( 'Rotated corner text', 'vassilisdesign-elements' ), '.vd-hero-corner' ),
				array( 'color', 'band', __( 'Red band', 'vassilisdesign-elements' ), '.vd-hero-band', 'background-color' ),
				array( 'color', 'cta_bg', __( 'Arrow button', 'vassilisdesign-elements' ), '.vd-hero-cta', 'background-color' ),
				array( 'color', 'cta_icon', __( 'Arrow', 'vassilisdesign-elements' ), '.vd-hero-cta', 'color' ),
			)
		);
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
			<div class="vd-hero-text">
			<h1 class="vd-wordmark" aria-label="<?php echo esc_attr( $s['label'] ); ?>">
				<span aria-hidden="true">
					<?php if ( function_exists( 'vde_wordmark_svg_ready' ) && vde_wordmark_svg_ready() && 'no' !== ( $s['wordmark_svg'] ?? 'yes' ) ) : ?>
					<span class="vd-wm-line"><span class="vd-wm-in vd-wm-img"><?php echo vde_wordmark_svg( 'wordmark-line1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin file ?></span></span>
					<span class="vd-wm-line"><span class="vd-wm-in vd-wm-img"><?php echo vde_wordmark_svg( 'wordmark-line2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin file ?></span></span>
					<?php else : ?>
					<span class="vd-wm-line"><span class="vd-wm-in"><?php echo $chars( $s['line1'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
					<span class="vd-wm-line"><span class="vd-wm-in"><?php echo $chars( $s['line2'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="vd-ch vd-stop">.</span></span></span>
					<?php endif; ?>
				</span>
			</h1>
			<p class="vd-hero-lead"><?php echo vde_lines( $s['lead'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<?php if ( $s['corner'] ) : ?>
				<p class="vd-hero-corner" aria-hidden="true"><?php echo esc_html( $s['corner'] ); ?></p>
			<?php endif; ?>
			<p class="vd-hero-meta vd-hero-meta-a"><?php echo vde_lines( $s['meta_a'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<p class="vd-hero-meta vd-hero-meta-b"><?php echo vde_lines( $s['meta_b'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php if ( $s['vertical'] ) : ?>
				<p class="vd-hero-vertical"><?php echo vde_lines( $s['vertical'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<div class="vd-hero-media">
				<div class="vd-hero-photo vd-photo vd-photo-frame">
					<?php echo $this->image_bg( $s['image'], $s['image_alt'], true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="vd-hero-band" aria-hidden="true"></div>
				<?php if ( $s['cta'] ) : ?>
					<a class="vd-hero-cta vd-square" href="<?php echo esc_url( $s['cta'] ); ?>" aria-label="<?php echo esc_attr( $s['cta_label'] ); ?>"><?php echo vde_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* ---------- Hero image (image + red band + arrow, full width of its container) ---------- */

class VDE_Widget_Hero_Image extends VDE_Widget {
	public function get_name() {
		return 'vd-hero-image';
	}
	public function get_title() {
		return __( 'VD Hero image', 'vassilisdesign-elements' );
	}
	public function get_icon() {
		return 'eicon-image';
	}
	protected function register_controls() {
		$this->start( __( 'Image', 'vassilisdesign-elements' ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'vassilisdesign-elements' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_control( 'image_alt', array( 'label' => __( 'Image description', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_image_bg_controls();
		$this->add_control( 'show_cta', array( 'label' => __( 'Arrow button', 'vassilisdesign-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes', 'separator' => 'before' ) );
		$this->add_control( 'cta', array( 'label' => __( 'Arrow button link', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => '#work', 'condition' => array( 'show_cta' => 'yes' ) ) );
		$this->add_control( 'cta_label', array( 'label' => __( 'Arrow button label (for screen readers)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'See selected work', 'condition' => array( 'show_cta' => 'yes' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'vassilisdesign-elements' ) ) );
		$var = function ( $name ) {
			return array( '{{WRAPPER}} .vd-hero-image' => '--vdhi-' . $name . ': {{SIZE}}{{UNIT}};' );
		};
		$this->add_responsive_control(
			'height',
			array(
				'label'          => __( 'Height', 'vassilisdesign-elements' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px', 'vh', 'vw' ),
				'range'          => array( 'px' => array( 'min' => 200, 'max' => 1400 ), 'vh' => array( 'min' => 20, 'max' => 120 ), 'vw' => array( 'min' => 20, 'max' => 160 ) ),
				'default'        => array( 'unit' => 'px', 'size' => 720 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 620 ),
				'mobile_default' => array( 'unit' => 'vw', 'size' => 118 ),
				'selectors'      => $var( 'h' ),
			)
		);
		$percent = function ( $label, $name, $desktop, $mobile ) use ( $var ) {
			$this->add_responsive_control(
				$name,
				array(
					'label'          => $label,
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'range'          => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
					'default'        => array( 'unit' => '%', 'size' => $desktop ),
					'tablet_default' => array( 'unit' => '%', 'size' => $desktop ),
					'mobile_default' => array( 'unit' => '%', 'size' => $mobile ),
					'selectors'      => $var( str_replace( '_', '-', $name ) ),
				)
			);
		};
		$percent( __( 'Image: space on the left', 'vassilisdesign-elements' ), 'left', 14, 22 );
		$percent( __( 'Image: space on the right', 'vassilisdesign-elements' ), 'right', 20, 0 );
		$percent( __( 'Red band: distance from top', 'vassilisdesign-elements' ), 'band_top', 36, 40 );
		$percent( __( 'Red band: height', 'vassilisdesign-elements' ), 'band_h', 44, 40 );
		$this->add_control( 'motion', array( 'label' => __( 'Reveal animation', 'vassilisdesign-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes', 'separator' => 'before', 'description' => __( 'The red band sweeps in and the image rises when the element comes into view.', 'vassilisdesign-elements' ) ) );
		$this->add_control( 'parallax', array( 'label' => __( 'Movement on scroll', 'vassilisdesign-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		$this->end_controls_section();

		$this->add_style_controls(
			array(
				array( 'color', 'band', __( 'Red band', 'vassilisdesign-elements' ), '.vd-hi-band', 'background-color' ),
				array( 'color', 'frame', __( 'Image background', 'vassilisdesign-elements' ), '.vd-hi-photo', 'background-color' ),
				array( 'color', 'cta_bg', __( 'Arrow button', 'vassilisdesign-elements' ), '.vd-hi-cta', 'background-color' ),
				array( 'color', 'cta_bg_hover', __( 'Arrow button on hover', 'vassilisdesign-elements' ), '.vd-hi-cta:hover', 'background-color' ),
				array( 'color', 'cta_icon', __( 'Arrow', 'vassilisdesign-elements' ), '.vd-hi-cta', 'color' ),
			)
		);
	}
	protected function render() {
		$s   = $this->get_settings_for_display();
		$alt = $s['image_alt'] ? $s['image_alt'] : ( ! empty( $s['image']['id'] ) ? get_post_meta( $s['image']['id'], '_wp_attachment_image_alt', true ) : '' );
		?>
		<div class="vd-hero-image" data-vd-motion="<?php echo esc_attr( $s['motion'] ); ?>" data-vd-parallax="<?php echo esc_attr( $s['parallax'] ); ?>">
			<div class="vd-hi-photo vd-photo vd-photo-frame">
				<?php echo $this->image_bg( $s['image'], (string) $alt ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="vd-hi-band" aria-hidden="true"></div>
			<?php if ( 'yes' === $s['show_cta'] && $s['cta'] ) : ?>
				<a class="vd-hi-cta vd-square" href="<?php echo esc_url( $s['cta'] ); ?>" aria-label="<?php echo esc_attr( $s['cta_label'] ); ?>"><?php echo vde_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
}

/* ---------- Section heading ---------- */

class VDE_Widget_Section_Head extends VDE_Widget {
	public function get_name() {
		return 'vd-section-head';
	}
	public function get_title() {
		return __( 'VD Section heading', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Heading', 'vassilisdesign-elements' ) );
		$this->add_control( 'intro', array( 'label' => __( 'Intro (left)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control( 'title', array( 'label' => __( 'Title (right, shown lowercase with a red full stop)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'Selected work' ) );
		$this->add_control(
			'tag',
			array(
				'label'   => __( 'Heading level', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3' ),
			)
		);
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'intro', __( 'Intro text', 'vassilisdesign-elements' ), '.vd-section-intro' ),
				array( 'color', 'dot', __( 'Intro square', 'vassilisdesign-elements' ), '.vd-dot', 'background-color' ),
				array( 'text', 'title', __( 'Title', 'vassilisdesign-elements' ), '.vd-display' ),
				array( 'color', 'stop', __( 'Title full stop', 'vassilisdesign-elements' ), '.vd-display .vd-stop', 'color' ),
			)
		);
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="vd-section-head vd-wrap">';
		if ( $s['intro'] ) {
			echo '<p class="vd-section-intro"><span class="vd-dot" aria-hidden="true"></span>' . esc_html( $s['intro'] ) . '</p>';
		}
		echo vde_display_title( $s['title'], $s['tag'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
}

/* ---------- Works grid ---------- */

class VDE_Widget_Works_Grid extends VDE_Widget {
	public function get_name() {
		return 'vd-works-grid';
	}
	public function get_title() {
		return __( 'VD Works grid', 'vassilisdesign-elements' );
	}
	protected function is_dynamic_content(): bool {
		return true;
	}
	protected function register_controls() {
		$this->start( __( 'Works', 'vassilisdesign-elements' ) );
		$this->add_control( 'count', array( 'label' => __( 'Number of Works', 'vassilisdesign-elements' ), 'type' => Controls_Manager::NUMBER, 'default' => 10, 'min' => 1, 'max' => 48, 'description' => __( 'Tip: a multiple of the Works per row fills every row.', 'vassilisdesign-elements' ) ) );
		$options = array( '' => __( 'All sectors', 'vassilisdesign-elements' ) );
		$terms   = get_terms( array( 'taxonomy' => 'work_sector', 'hide_empty' => false ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		$this->add_control(
			'source',
			array(
				'label'   => __( 'Show', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'    => __( 'All Works (by Order)', 'vassilisdesign-elements' ),
					'sector' => __( 'Works in one Sector', 'vassilisdesign-elements' ),
					'pick'   => __( 'Works I choose', 'vassilisdesign-elements' ),
				),
			)
		);
		unset( $options[''] );
		$this->add_control(
			'sector',
			array(
				'label'     => __( 'Sector', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array( '' => __( 'Choose a Sector', 'vassilisdesign-elements' ) ) + $options,
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
				'label'       => __( 'Works (in this order)', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $works,
				'default'     => array(),
				'condition'   => array( 'source' => 'pick' ),
			)
		);
		$this->add_control( 'all_link', array( 'label' => __( '"All work" link text (empty to hide)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'All work' ) );
		$this->add_control(
			'order_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Order Works with the "Order" field on each Work (lowest first).', 'vassilisdesign-elements' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'vassilisdesign-elements' ) ) );
		$this->add_control(
			'columns',
			array(
				'label'       => __( 'Works per row (desktop)', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '5',
				'options'     => array( '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'description' => __( 'Tablets show 3 per row, phones 2.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control(
			'full',
			array(
				'label'        => __( 'Full width (no side margins)', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control( 'gap', array( 'label' => __( 'Space between Works (px, empty = default)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::NUMBER, 'default' => '', 'min' => 0, 'max' => 80 ) );
		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Red cover with text on hover', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'Shows the Work\'s Excerpt, or the start of its content.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control(
			'captions',
			array(
				'label'        => __( 'Title and details under each image', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'staggered',
			array(
				'label'        => __( 'Staggered heights', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'On: each column has its own image height. Off: all images the same size.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control( 'phone_heading', array( 'label' => __( 'Phones', 'vassilisdesign-elements' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'mobile_count', array( 'label' => __( 'Works on phones (0 = all)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::NUMBER, 'default' => 4, 'min' => 0, 'max' => 48 ) );
		$this->add_control(
			'mobile_columns',
			array(
				'label'   => __( 'Works per row on phones', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
			)
		);
		$this->add_control( 'mobile_equal', array( 'label' => __( 'Equal heights on phones', 'vassilisdesign-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'client', __( 'Work title', 'vassilisdesign-elements' ), '.vd-tile-client' ),
				array( 'color', 'client_hover', __( 'Work title on hover', 'vassilisdesign-elements' ), '.vd-tile-link:hover .vd-tile-client', 'color' ),
				array( 'text', 'meta', __( 'Details under the title', 'vassilisdesign-elements' ), '.vd-tile-meta' ),
				array( 'text', 'hover', __( 'Text on hover', 'vassilisdesign-elements' ), '.vd-tile-hover-text > span' ),
				array( 'color', 'cover', __( 'Red cover / red block', 'vassilisdesign-elements' ), '.vd-red-block', 'background-color' ),
				array( 'text', 'link', __( '"All work" link', 'vassilisdesign-elements' ), '.vd-all-link a' ),
				array( 'color', 'link_line', __( '"All work" underline', 'vassilisdesign-elements' ), '.vd-all-link a', '--vd-link-line' ),
			)
		);
	}
	protected function render() {
		$s      = $this->get_settings_for_display();
		$raw    = (array) ( $this->get_raw_data()['settings'] ?? array() );
		$sector = isset( $raw['sector'] ) ? (string) $raw['sector'] : ''; // Read raw: Elementor blanks settings whose condition is not met.
		$source = isset( $raw['source'] ) ? $s['source'] : ( '' !== $sector ? 'sector' : 'all' ); // Widgets saved before "Show" existed keep their Sector.
		$full   = 'yes' === $s['full'];
		$grid   = vde_works_grid(
			array(
				'count'     => $s['count'],
				'sector'    => 'sector' === $source ? $sector : '',
				'ids'       => 'pick' === $source ? (array) $s['works'] : array(),
				'columns'   => $s['columns'],
				'gap'       => $s['gap'],
				'overlay'   => 'yes' === $s['overlay'],
				'captions'  => 'yes' === $s['captions'],
				'staggered' => 'yes' === $s['staggered'],
				'm_count'   => isset( $s['mobile_count'] ) ? (int) $s['mobile_count'] : 0,
				'm_cols'    => isset( $s['mobile_columns'] ) ? (int) $s['mobile_columns'] : 2,
				'm_equal'   => isset( $s['mobile_equal'] ) && 'yes' === $s['mobile_equal'],
			)
		);
		echo $full ? '<div class="vd-bleed">' . $grid . '</div>' : '<div class="vd-wrap">' . $grid . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $s['all_link'] ) {
			printf( '<div class="vd-wrap"><p class="vd-all-link"><a class="vd-text-link" href="%s">%s %s</a></p></div>', esc_url( get_post_type_archive_link( 'works' ) ), esc_html( $s['all_link'] ), vde_arrow() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/* ---------- Logo wall ---------- */

class VDE_Widget_Logo_Wall extends VDE_Widget {
	public function get_name() {
		return 'vd-logo-wall';
	}
	public function get_title() {
		return __( 'VD Logo wall', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Logos', 'vassilisdesign-elements' ) );
		$this->add_control( 'logos', array( 'label' => __( 'Logos (on white, any size)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::GALLERY, 'default' => array() ) );
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'vassilisdesign-elements' ) ) );
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Logos per row', 'vassilisdesign-elements' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 12,
				'default'        => 6,
				'tablet_default' => 4,
				'mobile_default' => 2,
				'selectors'      => array( '{{WRAPPER}} .vd-logos' => '--vdl-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'padding',
			array(
				'label'       => __( 'Space around each logo', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%', 'px' ),
				'range'       => array( '%' => array( 'min' => 0, 'max' => 40 ), 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'     => array( 'unit' => '%', 'size' => 17 ),
				'description' => __( 'Larger space = smaller logos.', 'vassilisdesign-elements' ),
				'selectors'   => array( '{{WRAPPER}} .vd-logos' => '--vdl-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'ratio',
			array(
				'label'     => __( 'Cell shape', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3 / 2',
				'options'   => array(
					'1 / 1' => __( 'Square (1:1)', 'vassilisdesign-elements' ),
					'4 / 3' => '4:3',
					'3 / 2' => '3:2',
					'2 / 1' => __( 'Wide (2:1)', 'vassilisdesign-elements' ),
				),
				'selectors' => array( '{{WRAPPER}} .vd-logos' => '--vdl-ratio: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'lines_style', array( 'label' => __( 'Lines', 'vassilisdesign-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control(
			'line_size',
			array(
				'label'      => __( 'Line thickness', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 8 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1 ),
				'description' => __( '0 hides the lines.', 'vassilisdesign-elements' ),
				'selectors'  => array( '{{WRAPPER}} .vd-logos' => '--vdl-line: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'lines', array( 'label' => __( 'Line colour', 'vassilisdesign-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .vd-logos' => '--vdl-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'top_line', array( 'label' => __( 'Top line colour', 'vassilisdesign-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .vd-logos' => '--vdl-top: {{VALUE}};' ) ) );
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

class VDE_Widget_Numbered_List extends VDE_Widget {
	public function get_name() {
		return 'vd-numbered-list';
	}
	public function get_title() {
		return __( 'VD Numbered list', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Items', 'vassilisdesign-elements' ) );
		$r = new Repeater();
		$r->add_control( 'title', array( 'label' => __( 'Title', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA ) );
		$r->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://… or /page/', 'vassilisdesign-elements' ),
				'description' => __( 'Optional. The whole row becomes clickable.', 'vassilisdesign-elements' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->add_control(
			'arrow_icon',
			array(
				'label'       => __( 'Icon on linked rows', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'separator'   => 'before',
				'description' => __( 'Empty = the studio arrow.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Icon size', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 64 ) ),
				'selectors'  => array( '{{WRAPPER}} .vd-step-arrow' => '--vd-icon: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'number', __( 'Numbers', 'vassilisdesign-elements' ), '.vd-step-n' ),
				array( 'text', 'title', __( 'Titles', 'vassilisdesign-elements' ), '.vd-step-title' ),
				array( 'color', 'title_hover', __( 'Linked title on hover', 'vassilisdesign-elements' ), '.vd-step-linked:hover .vd-step-title, .vd-step-linked:focus-within .vd-step-title', 'color' ),
				array( 'text', 'text', __( 'Text', 'vassilisdesign-elements' ), '.vd-step-text' ),
				array( 'color', 'lines', __( 'Lines', 'vassilisdesign-elements' ), '.vd-steps, .vd-step', 'border-color' ),
				array( 'color', 'arrow', __( 'Arrow', 'vassilisdesign-elements' ), '.vd-step-arrow', 'color' ),
			)
		);
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['items'] ) ) {
			return;
		}
		echo '<div class="vd-wrap"><ol class="vd-steps" role="list">';
		$arrow = vde_icon_html( isset( $s['arrow_icon'] ) ? $s['arrow_icon'] : array(), vde_arrow() );
		foreach ( array_values( $s['items'] ) as $i => $item ) {
			$title = esc_html( $item['title'] );
			$url   = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '';
			if ( $url ) {
				// The link sits on the title; its ::after covers the whole row, so the row is one big click target.
				$key = 'link_' . $i;
				$this->add_link_attributes( $key, $item['link'] );
				$this->add_render_attribute( $key, 'class', 'vd-step-link' );
				$title = '<a ' . $this->get_render_attribute_string( $key ) . '><span class="vd-step-label">' . $title . '</span></a>';
			}
			printf(
				'<li class="vd-step%s"><span class="vd-step-n" aria-hidden="true">%s</span><h3 class="vd-step-title">%s</h3><p class="vd-step-text">%s</p>%s</li>',
				$url ? ' vd-step-linked' : '',
				esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ),
				$title, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
				esc_html( $item['text'] ),
				$url ? '<span class="vd-step-arrow" aria-hidden="true">' . $arrow . '</span>' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		echo '</ol></div>';
	}
}

/* ---------- Studio ---------- */

class VDE_Widget_Studio extends VDE_Widget {
	public function get_name() {
		return 'vd-studio';
	}
	public function get_title() {
		return __( 'VD Studio', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Studio', 'vassilisdesign-elements' ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'Studio' ) );
		$this->add_control( 'red_title', array( 'label' => __( 'Red box label', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'What we hold to' ) );
		$this->add_control( 'red_list', array( 'label' => __( 'Red box list (one per line)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Typography first\nDetail and proportion\nBuilt for production\nDesign and technology" ) );
		$this->add_control( 'side_title', array( 'label' => __( 'Side label', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'Industries' ) );
		$this->add_control( 'side_list', array( 'label' => __( 'Side list (one per line)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => "Wine\nHospitality and tourism\nFood and beverage\nProfessional services" ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'vassilisdesign-elements' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control( 'image_alt', array( 'label' => __( 'Image description', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_image_bg_controls();
		$this->add_control( 'text', array( 'label' => __( 'Text (first paragraph is shown large)', 'vassilisdesign-elements' ), 'type' => Controls_Manager::WYSIWYG, 'separator' => 'before' ) );
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'title', __( 'Title', 'vassilisdesign-elements' ), '.vd-display' ),
				array( 'color', 'stop', __( 'Title full stop', 'vassilisdesign-elements' ), '.vd-display .vd-stop', 'color' ),
				array( 'color', 'red_bg', __( 'Red box', 'vassilisdesign-elements' ), '.vd-studio-red', 'background-color' ),
				array( 'text', 'red_title', __( 'Red box label', 'vassilisdesign-elements' ), '.vd-studio-red-title' ),
				array( 'text', 'red_list', __( 'Red box list', 'vassilisdesign-elements' ), '.vd-studio-red-list' ),
				array( 'text', 'side_title', __( 'Side label', 'vassilisdesign-elements' ), '.vd-red-label' ),
				array( 'text', 'side_list', __( 'Side list', 'vassilisdesign-elements' ), '.vd-studio-side-list' ),
				array( 'text', 'intro', __( 'First paragraph', 'vassilisdesign-elements' ), '.vd-studio-copy p:first-child' ),
				array( 'text', 'body', __( 'Other paragraphs', 'vassilisdesign-elements' ), '.vd-studio-copy p:not(:first-child)' ),
				array( 'color', 'circle', __( 'Circle', 'vassilisdesign-elements' ), '.vd-studio-circle', 'background-color' ),
				array( 'color', 'frame', __( 'Image background', 'vassilisdesign-elements' ), '.vd-studio-photo', 'background-color' ),
			)
		);
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="vd-studio-grid vd-wrap">
			<?php echo vde_display_title( $s['title'], 'h2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="vd-studio-red">
				<p class="vd-studio-red-title"><?php echo esc_html( $s['red_title'] ); ?></p>
				<p class="vd-studio-red-list"><?php echo vde_lines( $s['red_list'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<div class="vd-studio-side">
				<p class="vd-red-label"><?php echo esc_html( $s['side_title'] ); ?></p>
				<p class="vd-studio-side-list"><?php echo vde_lines( $s['side_list'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<div class="vd-studio-media">
				<span class="vd-studio-circle" aria-hidden="true"></span>
				<div class="vd-studio-photo vd-photo vd-photo-frame"><?php echo $this->image_bg( $s['image'], isset( $s['image_alt'] ) ? (string) $s['image_alt'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
			<div class="vd-studio-copy"><?php echo wp_kses_post( $s['text'] ); ?></div>
			<span class="vd-plus" aria-hidden="true"></span>
			<span class="vd-dots" aria-hidden="true"></span>
		</div>
		<?php
	}
}

/* ---------- Client index ---------- */

class VDE_Widget_Client_Index extends VDE_Widget {
	public function get_name() {
		return 'vd-client-index';
	}
	public function get_title() {
		return __( 'VD Client index', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Clients', 'vassilisdesign-elements' ) );
		$r = new Repeater();
		$r->add_control( 'title', array( 'label' => __( 'Sector', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control(
			'names',
			array(
				'label'       => __( 'Clients (one per line)', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 10,
				'description' => __( 'To link a name, add | and the address after it, e.g. Porto Carras | https://portocarras.com', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control(
			'groups',
			array(
				'label'       => __( 'Groups', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(),
			)
		);
		$this->add_control(
			'new_tab',
			array(
				'label'        => __( 'Open links in a new tab', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'sector', __( 'Sector titles', 'vassilisdesign-elements' ), '.vd-client-sector' ),
				array( 'color', 'sector_line', __( 'Sector title line', 'vassilisdesign-elements' ), '.vd-client-sector', 'border-bottom-color' ),
				array( 'text', 'names', __( 'Client names', 'vassilisdesign-elements' ), '.vd-client-list li' ),
				array( 'color', 'name_lines', __( 'Lines between names', 'vassilisdesign-elements' ), '.vd-client-list li', 'border-bottom-color' ),
				array( 'color', 'link_hover', __( 'Linked name on hover', 'vassilisdesign-elements' ), '.vd-client-list a:hover, .vd-client-list a:focus-visible', 'color' ),
			)
		);
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['groups'] ) ) {
			return;
		}
		$target = ( isset( $s['new_tab'] ) && 'yes' === $s['new_tab'] ) ? ' target="_blank" rel="noopener"' : '';
		echo '<div class="vd-wrap vd-client-groups">';
		foreach ( $s['groups'] as $group ) {
			$names = array_filter( array_map( 'trim', explode( "\n", (string) $group['names'] ) ) );
			echo '<div class="vd-client-group"><h3 class="vd-client-sector">' . esc_html( $group['title'] ) . '</h3><ul class="vd-client-list" role="list">';
			foreach ( $names as $name ) {
				$url = '';
				if ( false !== strpos( $name, '|' ) ) {
					list( $name, $url ) = array_map( 'trim', explode( '|', $name, 2 ) );
					if ( $url && ! preg_match( '#^([a-z][a-z0-9+.-]*:|/|\#)#i', $url ) ) { // add https:// to bare addresses; esc_url() drops unsafe schemes
						$url = 'https://' . $url;
					}
					$url = esc_url( $url );
				}
				if ( $url ) {
					echo '<li><a href="' . $url . '"' . $target . '>' . esc_html( $name ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<li>' . esc_html( $name ) . '</li>';
				}
			}
			echo '</ul></div>';
		}
		echo '</div>';
	}
}

/* ---------- Contact intro ---------- */

class VDE_Widget_Contact_Intro extends VDE_Widget {
	public function get_name() {
		return 'vd-contact-intro';
	}
	public function get_title() {
		return __( 'VD Contact intro', 'vassilisdesign-elements' );
	}
	protected function register_controls() {
		$this->start( __( 'Contact', 'vassilisdesign-elements' ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'Start a project' ) );
		$this->add_control( 'lead', array( 'label' => __( 'Text', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => 'Tell us about your brand, product or website. We reply to every enquiry.' ) );
		$this->add_control( 'direct', array( 'label' => __( 'Line above the contact details', 'vassilisdesign-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'Or write to us directly' ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Email and phone come from Appearance → Customize → Studio details.', 'vassilisdesign-elements' ),
			)
		);
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'title', __( 'Title', 'vassilisdesign-elements' ), '.vd-display' ),
				array( 'color', 'stop', __( 'Title full stop', 'vassilisdesign-elements' ), '.vd-display .vd-stop', 'color' ),
				array( 'text', 'lead', __( 'Text', 'vassilisdesign-elements' ), '.vd-contact-lead' ),
				array( 'text', 'direct', __( 'Line above contact details', 'vassilisdesign-elements' ), '.vd-contact-direct' ),
				array( 'text', 'links', __( 'Email and phone', 'vassilisdesign-elements' ), '.vd-contact-direct a' ),
			)
		);
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="vd-contact-intro">';
		echo vde_display_title( $s['title'], 'h2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $s['lead'] ) {
			echo '<p class="vd-contact-lead">' . esc_html( $s['lead'] ) . '</p>';
		}
		echo '<p class="vd-contact-direct">' . ( $s['direct'] ? esc_html( $s['direct'] ) . '<br>' : '' ) . vde_contact_lines() . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
}

/* ---------- Single Work (for Elementor Pro Theme Builder) ---------- */

abstract class VDE_Work_Widget extends VDE_Widget {
	protected function is_dynamic_content(): bool {
		return true;
	}
	protected function register_controls() {
		$this->start( __( 'Work', 'vassilisdesign-elements' ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Shows the current Work. Use inside a Theme Builder "Single" template for Works.', 'vassilisdesign-elements' ),
			)
		);
		$this->end_controls_section();
		$this->extra_controls();
		$this->add_style_controls( $this->style_items() );
	}
	/** Widget-specific settings (layout, lightbox), added after the note. */
	protected function extra_controls() {}
	protected function style_items() {
		return array();
	}
	/** On/off switch for opening images full size. */
	protected function add_lightbox_control() {
		$this->add_control(
			'lightbox',
			array(
				'label'        => __( 'Open full image on click', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
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

class VDE_Widget_Work_Header extends VDE_Work_Widget {
	public function get_name() {
		return 'vd-work-header';
	}
	public function get_title() {
		return __( 'VD Work header', 'vassilisdesign-elements' );
	}
	protected function style_items() {
		return array(
			array( 'text', 'back', __( '"All work" link', 'vassilisdesign-elements' ), '.vd-case-back' ),
			array( 'text', 'meta_label', __( 'Detail labels', 'vassilisdesign-elements' ), '.vd-case-meta dt' ),
			array( 'text', 'meta_value', __( 'Detail values', 'vassilisdesign-elements' ), '.vd-case-meta dd' ),
			array( 'color', 'meta_hover', __( 'Sector / Service on hover', 'vassilisdesign-elements' ), '.vd-case-meta dd a:hover, .vd-case-meta dd a:focus-visible', 'color' ),
			array( 'color', 'meta_line', __( 'Detail lines', 'vassilisdesign-elements' ), '.vd-case-meta > div', 'border-left-color' ),
			array( 'text', 'index', __( 'Project number', 'vassilisdesign-elements' ), '.vd-case-index' ),
			array( 'text', 'title', __( 'Title', 'vassilisdesign-elements' ), '.vd-case-title' ),
			array( 'color', 'stop', __( 'Title full stop', 'vassilisdesign-elements' ), '.vd-case-title .vd-stop', 'color' ),
			array( 'color', 'red_bg', __( 'Red box', 'vassilisdesign-elements' ), '.vd-case-red', 'background-color' ),
			array( 'text', 'red_title', __( 'Red box label', 'vassilisdesign-elements' ), '.vd-case-red-title' ),
			array( 'text', 'facts', __( 'Scope text', 'vassilisdesign-elements' ), '.vd-case-facts li' ),
			array( 'text', 'site', __( '"Visit website" link', 'vassilisdesign-elements' ), '.vd-case-site' ),
		);
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vde_work_header( $w ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

class VDE_Widget_Work_Intro extends VDE_Work_Widget {
	public function get_name() {
		return 'vd-work-intro';
	}
	public function get_title() {
		return __( 'VD Work cover and summary', 'vassilisdesign-elements' );
	}
	protected function style_items() {
		return array(
			array( 'text', 'label', __( 'Label', 'vassilisdesign-elements' ), '.vd-case-label' ),
			array( 'text', 'summary', __( 'Summary', 'vassilisdesign-elements' ), '.vd-case-summary' ),
			array( 'text', 'body', __( 'Description', 'vassilisdesign-elements' ), '.vd-case-text .vd-prose' ),
			array( 'color', 'cover_bg', __( 'Cover background', 'vassilisdesign-elements' ), '.vd-case-poster', 'background-color' ),
		);
	}
	protected function extra_controls() {
		$this->start_controls_section( 'cover', array( 'label' => __( 'Cover image', 'vassilisdesign-elements' ) ) );
		$this->add_control(
			'cover_ratio',
			array(
				'label'       => __( 'Shape', 'vassilisdesign-elements' ),
				'description' => __( 'On phones the whole image is shown.', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '16 / 9',
				'options'   => array(
					'16 / 9' => '16:9',
					'3 / 2'  => '3:2',
					'4 / 3'  => '4:3',
					'1 / 1'  => '1:1',
					'21 / 9' => '21:9',
				),
				'selectors' => array( '{{WRAPPER}} .vd-case-poster' => 'aspect-ratio: {{VALUE}};' ),
			)
		);
		$this->add_lightbox_control();
		$this->end_controls_section();
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vde_work_intro( $w, 'yes' === $this->get_settings_for_display( 'lightbox' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

class VDE_Widget_Work_Gallery extends VDE_Work_Widget {
	public function get_name() {
		return 'vd-work-gallery';
	}
	public function get_title() {
		return __( 'VD Work gallery', 'vassilisdesign-elements' );
	}
	protected function extra_controls() {
		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'vassilisdesign-elements' ) ) );
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Images per row', 'vassilisdesign-elements' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5' ),
				'selectors'      => array( '{{WRAPPER}} .vd-gallery' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Space between images', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ), 'vw' => array( 'min' => 0, 'max' => 10, 'step' => 0.1 ) ),
				'selectors'  => array( '{{WRAPPER}} .vd-gallery' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'shape',
			array(
				'label'       => __( 'Image shape', 'vassilisdesign-elements' ),
				'description' => __( 'On phones each image is shown whole.', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'auto',
				'options'   => array(
					'auto'   => __( 'As uploaded', 'vassilisdesign-elements' ),
					'1 / 1'  => '1:1',
					'4 / 5'  => '4:5',
					'3 / 4'  => '3:4',
					'4 / 3'  => '4:3',
					'3 / 2'  => '3:2',
					'16 / 9' => '16:9',
				),
				'selectors_dictionary' => array( 'auto' => 'var(--vd-ar, auto)' ),
				'selectors' => array( '{{WRAPPER}} .vd-gallery-item' => '--vd-gshape: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'fit',
			array(
				'label'     => __( 'Fit', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => __( 'Fill the shape (may crop)', 'vassilisdesign-elements' ),
					'contain' => __( 'Show the whole image', 'vassilisdesign-elements' ),
				),
				'condition' => array( 'shape!' => 'auto' ),
				'selectors' => array( '{{WRAPPER}} .vd-gallery' => '--vd-g-fit: {{VALUE}};' ),
			)
		);
		$this->add_lightbox_control();
		$this->end_controls_section();
	}
	protected function style_items() {
		return array(
			array( 'color', 'item_bg', __( 'Image background', 'vassilisdesign-elements' ), '.vd-gallery-item', 'background-color' ),
		);
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vde_work_gallery( $w, 'yes' === $this->get_settings_for_display( 'lightbox' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

class VDE_Widget_Works_Archive extends VDE_Widget {
	public function get_name() {
		return 'vd-works-archive';
	}
	public function get_title() {
		return __( 'VD Works archive', 'vassilisdesign-elements' );
	}
	protected function is_dynamic_content(): bool {
		return true;
	}
	protected function register_controls() {
		$this->start( __( 'Works archive', 'vassilisdesign-elements' ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Title, Sector and Service filters and all Works of the current archive. Use inside a Theme Builder "Archive" template for Works.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control(
			'sort',
			array(
				'label'       => __( 'Sort Works by', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'menu',
				'separator'   => 'before',
				'options'     => array(
					'menu'          => __( 'Custom order (the Order field in each Work)', 'vassilisdesign-elements' ),
					'date_desc'     => __( 'Newest first', 'vassilisdesign-elements' ),
					'date_asc'      => __( 'Oldest first', 'vassilisdesign-elements' ),
					'title_asc'     => __( 'Title A to Z', 'vassilisdesign-elements' ),
					'title_desc'    => __( 'Title Z to A', 'vassilisdesign-elements' ),
					'modified_desc' => __( 'Recently updated first', 'vassilisdesign-elements' ),
					'rand'          => __( 'Random', 'vassilisdesign-elements' ),
				),
				'description' => __( 'Custom order: set "Order" in each Work (Page attributes); lower numbers come first. Random changes when the page cache is refreshed.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control(
			'filters',
			array(
				'label'   => __( 'Filter buttons', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => array(
					'both'     => __( 'Sectors and Services', 'vassilisdesign-elements' ),
					'sectors'  => __( 'Sectors only', 'vassilisdesign-elements' ),
					'services' => __( 'Services only', 'vassilisdesign-elements' ),
					'none'     => __( 'None', 'vassilisdesign-elements' ),
				),
			)
		);
		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Text on hover', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'A red cover with the Work\'s Excerpt appears over the image, as in the homepage grid.', 'vassilisdesign-elements' ),
			)
		);
		$this->add_control(
			'show_title',
			array(
				'label'        => __( 'Show the title', 'vassilisdesign-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();
		$this->add_style_controls(
			array(
				array( 'text', 'title', __( 'Title', 'vassilisdesign-elements' ), '.vd-display' ),
				array( 'color', 'stop', __( 'Title full stop', 'vassilisdesign-elements' ), '.vd-display .vd-stop', 'color' ),
				array( 'text', 'filter_label', __( 'Filter labels (Sectors, Services)', 'vassilisdesign-elements' ), '.vd-filter-label' ),
				array( 'text', 'filter', __( 'Filter buttons', 'vassilisdesign-elements' ), '.vd-filter a' ),
				array( 'color', 'filter_active', __( 'Filter button: current', 'vassilisdesign-elements' ), '.vd-filter a[aria-current], .vd-filter a:hover', 'color' ),
				array( 'text', 'client', __( 'Work title', 'vassilisdesign-elements' ), '.vd-tile-client' ),
				array( 'color', 'client_hover', __( 'Work title on hover', 'vassilisdesign-elements' ), '.vd-tile-link:hover .vd-tile-client', 'color' ),
				array( 'text', 'meta', __( 'Details under the title', 'vassilisdesign-elements' ), '.vd-tile-meta' ),
				array( 'text', 'hover', __( 'Text on hover', 'vassilisdesign-elements' ), '.vd-tile-hover-text > span' ),
				array( 'color', 'cover', __( 'Red cover / red block', 'vassilisdesign-elements' ), '.vd-red-block', 'background-color' ),
			)
		);
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		echo vde_works_archive( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'sort'       => isset( $s['sort'] ) ? $s['sort'] : 'menu',
				'filters'    => isset( $s['filters'] ) ? $s['filters'] : 'both',
				'show_title' => ! isset( $s['show_title'] ) || 'yes' === $s['show_title'],
				'overlay'    => ! isset( $s['overlay'] ) || 'yes' === $s['overlay'],
			)
		);
	}
}

class VDE_Widget_Next_Work extends VDE_Work_Widget {
	public function get_name() {
		return 'vd-next-work';
	}
	public function get_title() {
		return __( 'VD Next Work', 'vassilisdesign-elements' );
	}
	protected function style_items() {
		return array(
			array( 'text', 'label', __( '"Next project" label', 'vassilisdesign-elements' ), '.vd-case-next-label' ),
			array( 'text', 'client', __( 'Next Work title', 'vassilisdesign-elements' ), '.vd-case-next-client' ),
			array( 'color', 'stop', __( 'Title full stop', 'vassilisdesign-elements' ), '.vd-case-next-client .vd-stop', 'color' ),
			array( 'color', 'line', __( 'Top line', 'vassilisdesign-elements' ), '.vd-case-next-link', 'border-top-color' ),
		);
	}
	protected function render() {
		$w = $this->work();
		if ( $w ) {
			echo vde_next_work( $w ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
