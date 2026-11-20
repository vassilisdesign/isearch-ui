<?php
/**
 * VD Navigation menu: a WordPress menu, horizontal or vertical, that becomes a menu button on tablet or
 * phone (a panel under the header or a full-screen overlay). Replaces heavier add-on menus: a few lines
 * of CSS in elements.css and a small script in elements.js, nothing else is loaded.
 *
 * @package vassilisdesign-elements
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/** Menu markup with a toggle button beside each item that has a submenu (keyboard and touch friendly). */
class VDE_Nav_Walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="vdn-sub">';
	}
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item    = $data_object;
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_sub = in_array( 'menu-item-has-children', $classes, true );
		$current = array_intersect( array( 'current-menu-item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_item', 'current_page_parent' ), $classes );
		$li      = array( 'vdn-item' );
		if ( $has_sub ) {
			$li[] = 'vdn-has-sub';
		}
		if ( $current ) {
			$li[] = 'vdn-current';
		}
		if ( ! empty( $item->classes[0] ) && ! in_array( $item->classes[0], array( 'menu-item' ), true ) && 0 !== strpos( $item->classes[0], 'menu-item' ) ) {
			$li[] = sanitize_html_class( $item->classes[0] ); // a CSS class set on the item in Appearance → Menus
		}
		$attrs = array(
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : ( '_blank' === $item->target ? 'noopener' : '' ),
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
		);
		$attr = '';
		foreach ( $attrs as $name => $value ) {
			if ( '' !== $value ) {
				$attr .= ' ' . $name . '="' . ( 'href' === $name ? esc_url( $value ) : esc_attr( $value ) ) . '"';
			}
		}
		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) {
			$attr .= ' aria-current="page"';
		}
		$title   = apply_filters( 'nav_menu_item_title', apply_filters( 'the_title', $item->title, $item->ID ), $item, $args, $depth ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core hooks.
		$output .= '<li class="' . esc_attr( implode( ' ', $li ) ) . '">';
		$output .= '<a class="vdn-link"' . $attr . '>' . wp_kses_post( $title ) . '</a>';
		if ( $has_sub ) {
			/* translators: %s: menu item name */
			$label   = sprintf( __( 'Show the %s submenu', 'vassilisdesign-elements' ), wp_strip_all_tags( $title ) );
			$icon    = ! empty( $args->vdn_sub_icon ) ? '<span class="vdn-sub-icon" aria-hidden="true">' . $args->vdn_sub_icon . '</span>' : '<span class="vdn-chevron" aria-hidden="true"></span>';
			$output .= '<button type="button" class="vdn-sub-toggle" aria-expanded="false" aria-label="' . esc_attr( $label ) . '">' . $icon . '</button>';
		}
	}
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}

class VDE_Widget_Nav_Menu extends VDE_Widget {
	public function get_name() {
		return 'vd-nav-menu';
	}
	public function get_title() {
		return __( 'VD Navigation menu', 'vassilisdesign-elements' );
	}
	public function get_icon() {
		return 'eicon-nav-menu';
	}
	public function get_keywords() {
		return array( 'menu', 'nav', 'navigation', 'header', 'hamburger' );
	}

	private function menus() {
		$out = array();
		foreach ( wp_get_nav_menus() as $menu ) {
			$out[ $menu->term_id ] = $menu->name;
		}
		return $out;
	}

	protected function register_controls() {
		$menus = $this->menus();
		$this->start( __( 'Menu', 'vassilisdesign-elements' ) );
		if ( $menus ) {
			$this->add_control(
				'menu',
				array(
					'label'       => __( 'Menu', 'vassilisdesign-elements' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => $menus,
					'default'     => (string) array_key_first( $menus ),
					'description' => __( 'Edit the links in Appearance → Menus.', 'vassilisdesign-elements' ),
				)
			);
		} else {
			$this->add_control(
				'no_menu',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'There are no menus yet. Create one in Appearance → Menus, then reload the editor.', 'vassilisdesign-elements' ),
				)
			);
		}
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'vassilisdesign-elements' ),
					'vertical'   => __( 'Vertical', 'vassilisdesign-elements' ),
				),
			)
		);
		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'    => array( 'title' => __( 'Left', 'vassilisdesign-elements' ), 'icon' => 'eicon-h-align-left' ),
					'center'        => array( 'title' => __( 'Centre', 'vassilisdesign-elements' ), 'icon' => 'eicon-h-align-center' ),
					'flex-end'      => array( 'title' => __( 'Right', 'vassilisdesign-elements' ), 'icon' => 'eicon-h-align-right' ),
					'space-between' => array( 'title' => __( 'Spread', 'vassilisdesign-elements' ), 'icon' => 'eicon-h-align-stretch' ),
				),
				'default'   => 'flex-end',
				'selectors' => array( '{{WRAPPER}} .vdn' => '--vdn-align: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'pointer',
			array(
				'label'   => __( 'Hover effect', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => array(
					'underline' => __( 'Line grows under the link', 'vassilisdesign-elements' ),
					'colour'    => __( 'Colour only', 'vassilisdesign-elements' ),
					'none'      => __( 'None', 'vassilisdesign-elements' ),
				),
			)
		);
		$this->add_control(
			'depth',
			array(
				'label'   => __( 'Submenus', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => array(
					'0' => __( 'Show', 'vassilisdesign-elements' ),
					'1' => __( 'Top level only', 'vassilisdesign-elements' ),
				),
			)
		);
		$this->add_control(
			'sub_icon',
			array(
				'label'       => __( 'Submenu arrow icon', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => array( 'depth' => '0' ),
			)
		);
		$this->add_control(
			'nav_label',
			array(
				'label'       => __( 'Name for screen readers', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Main menu', 'vassilisdesign-elements' ),
				'label_block' => true,
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'mobile', array( 'label' => __( 'Menu button (tablet and phone)', 'vassilisdesign-elements' ) ) );
		$this->add_control(
			'collapse',
			array(
				'label'   => __( 'Show a menu button on', 'vassilisdesign-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tablet',
				'options' => array(
					'tablet' => __( 'Tablet and phone', 'vassilisdesign-elements' ),
					'mobile' => __( 'Phone only', 'vassilisdesign-elements' ),
					'none'   => __( 'Never (always show the links)', 'vassilisdesign-elements' ),
				),
			)
		);
		$this->add_control(
			'panel',
			array(
				'label'     => __( 'Menu opens as', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dropdown',
				'options'   => array(
					'dropdown'   => __( 'Panel under the header, full width', 'vassilisdesign-elements' ),
					'fullscreen' => __( 'Full screen', 'vassilisdesign-elements' ),
				),
				'condition' => array( 'collapse!' => 'none' ),
			)
		);
		$this->add_control(
			'toggle_show',
			array(
				'label'     => __( 'Button shows', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'icon',
				'options'   => array(
					'icon' => __( 'Lines icon', 'vassilisdesign-elements' ),
					'text' => __( 'Text', 'vassilisdesign-elements' ),
					'both' => __( 'Text and lines', 'vassilisdesign-elements' ),
				),
				'condition' => array( 'collapse!' => 'none' ),
			)
		);
		$this->add_control(
			'toggle_text',
			array(
				'label'     => __( 'Button text', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Menu', 'vassilisdesign-elements' ),
				'condition' => array( 'collapse!' => 'none' ),
			)
		);
		$this->add_control(
			'close_text',
			array(
				'label'     => __( 'Button text when open', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Close', 'vassilisdesign-elements' ),
				'condition' => array( 'collapse!' => 'none', 'toggle_show!' => 'icon' ),
			)
		);
		$this->add_control(
			'open_icon',
			array(
				'label'       => __( 'Button icon', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'description' => __( 'Empty = two lines that turn into a cross.', 'vassilisdesign-elements' ),
				'condition'   => array( 'collapse!' => 'none', 'toggle_show!' => 'text' ),
			)
		);
		$this->add_control(
			'close_icon',
			array(
				'label'       => __( 'Button icon when open', 'vassilisdesign-elements' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => array( 'collapse!' => 'none', 'toggle_show!' => 'text' ),
			)
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon size', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 64 ) ),
				'selectors'  => array( '{{WRAPPER}} .vdn' => '--vdn-icon: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'collapse!' => 'none', 'toggle_show!' => 'text' ),
			)
		);
		$this->add_responsive_control(
			'panel_align',
			array(
				'label'     => __( 'Links in the open menu', 'vassilisdesign-elements' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array( 'title' => __( 'Left', 'vassilisdesign-elements' ), 'icon' => 'eicon-text-align-left' ),
					'center'     => array( 'title' => __( 'Centre', 'vassilisdesign-elements' ), 'icon' => 'eicon-text-align-center' ),
					'flex-end'   => array( 'title' => __( 'Right', 'vassilisdesign-elements' ), 'icon' => 'eicon-text-align-right' ),
				),
				'selectors' => array( '{{WRAPPER}} .vdn' => '--vdn-panel-align: {{VALUE}};' ),
				'condition' => array( 'collapse!' => 'none' ),
			)
		);
		$this->end_controls_section();

		// Spacing.
		$this->start_controls_section( 'spacing', array( 'label' => __( 'Spacing', 'vassilisdesign-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Space between links', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ), 'em' => array( 'min' => 0, 'max' => 6, 'step' => 0.1 ), 'vw' => array( 'min' => 0, 'max' => 10, 'step' => 0.1 ) ),
				'selectors'  => array( '{{WRAPPER}} .vdn' => '--vdn-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Space around each link', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .vdn-menu > .vdn-item > .vdn-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => __( 'Space inside the open menu', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'vw' ),
				'selectors'  => array( '{{WRAPPER}} .vdn' => '--vdn-panel-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'condition'  => array( 'collapse!' => 'none' ),
			)
		);
		$this->add_responsive_control(
			'toggle_size',
			array(
				'label'      => __( 'Menu button size', 'vassilisdesign-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .vdn' => '--vdn-toggle: {{SIZE}}{{UNIT}};' ),
				'condition'  => array( 'collapse!' => 'none' ),
			)
		);
		$this->end_controls_section();

		$this->add_style_controls(
			array(
				array( 'text', 'link', __( 'Links', 'vassilisdesign-elements' ), '.vdn-menu > .vdn-item > .vdn-link' ),
				array( 'color', 'link_hover', __( 'Link on hover', 'vassilisdesign-elements' ), '.vdn-menu > .vdn-item > .vdn-link:hover, .vdn-menu > .vdn-item > .vdn-link:focus-visible', 'color' ),
				array( 'color', 'link_current', __( 'Current page link', 'vassilisdesign-elements' ), '.vdn-menu > .vdn-current > .vdn-link', 'color' ),
				array( 'color', 'line', __( 'Hover line', 'vassilisdesign-elements' ), '.vdn', '--vdn-line' ),
				array( 'text', 'sub', __( 'Submenu links', 'vassilisdesign-elements' ), '.vdn-sub .vdn-link' ),
				array( 'color', 'sub_hover', __( 'Submenu link on hover', 'vassilisdesign-elements' ), '.vdn-sub .vdn-link:hover, .vdn-sub .vdn-link:focus-visible', 'color' ),
				array( 'color', 'sub_bg', __( 'Submenu background', 'vassilisdesign-elements' ), '.vdn', '--vdn-sub-bg' ),
				array( 'text', 'toggle_label', __( 'Menu button text', 'vassilisdesign-elements' ), '.vdn-toggle-text' ),
				array( 'color', 'toggle_color', __( 'Menu button lines', 'vassilisdesign-elements' ), '.vdn-toggle', 'color' ),
				array( 'color', 'toggle_bg', __( 'Menu button background', 'vassilisdesign-elements' ), '.vdn-toggle', 'background-color' ),
				array( 'color', 'panel_bg', __( 'Open menu background', 'vassilisdesign-elements' ), '.vdn', '--vdn-panel-bg' ),
			)
		);
	}

	/** Width (px) below which the menu button replaces the links, from Elementor's own breakpoints. */
	private function breakpoint( $device ) {
		$fallback = 'tablet' === $device ? 1024 : 767;
		if ( ! class_exists( '\Elementor\Plugin' ) || empty( \Elementor\Plugin::$instance->breakpoints ) ) {
			return $fallback;
		}
		$bp = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints( $device );
		return $bp ? (int) $bp->get_value() : $fallback;
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$menu = ! empty( $s['menu'] ) ? (int) $s['menu'] : 0;
		if ( ! $menu || ! wp_get_nav_menu_object( $menu ) ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				echo '<p class="vdn-empty">' . esc_html__( 'Choose a menu in the widget settings.', 'vassilisdesign-elements' ) . '</p>';
			}
			return;
		}
		$items = wp_nav_menu(
			array(
				'menu'        => $menu,
				'container'   => false,
				'menu_class'  => 'vdn-menu',
				'menu_id'     => 'vdn-' . $this->get_id(),
				'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'depth'       => (int) $s['depth'],
				'walker'      => new VDE_Nav_Walker(),
				'vdn_sub_icon' => vde_icon_html( isset( $s['sub_icon'] ) ? $s['sub_icon'] : array() ),
				'fallback_cb' => false,
				'echo'        => false,
			)
		);
		if ( ! $items ) {
			return;
		}
		$collapse = in_array( $s['collapse'], array( 'tablet', 'mobile' ), true ) ? $s['collapse'] : 'none';
		$panel_id = 'vdn-panel-' . $this->get_id();
		$classes  = array(
			'vdn',
			'vdn--' . ( 'vertical' === $s['layout'] ? 'vertical' : 'horizontal' ),
			'vdn--pointer-' . sanitize_html_class( $s['pointer'] ),
		);
		if ( 'none' !== $collapse ) {
			$classes[] = 'vdn--' . ( 'fullscreen' === $s['panel'] ? 'fullscreen' : 'dropdown' );
		}
		$show = $s['toggle_show'];
		// The breakpoint follows Elementor's Site Settings, so it is written next to the menu.
		if ( 'none' !== $collapse ) {
			$scope = '.elementor-element-' . $this->get_id() . ' .vdn';
			printf(
				'<style>@media (max-width:%1$dpx){%2$s{--vdn-collapsed:1}%2$s .vdn-toggle{display:inline-flex}%2$s .vdn-panel{display:none}%2$s.is-open .vdn-panel{display:block}}</style>',
				(int) $this->breakpoint( $collapse ),
				$scope // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from the element ID.
			);
		}
		?>
		<nav class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-label="<?php echo esc_attr( $s['nav_label'] ); ?>" data-bp="<?php echo 'none' === $collapse ? 0 : (int) $this->breakpoint( $collapse ); ?>">
			<?php if ( 'none' !== $collapse ) : ?>
				<button type="button" class="vdn-toggle vdn-toggle--<?php echo esc_attr( $show ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>"<?php echo 'icon' === $show ? ' aria-label="' . esc_attr( $s['toggle_text'] ) . '"' : ''; ?>>
					<?php if ( 'icon' !== $show ) : ?>
						<span class="vdn-toggle-text" data-open="<?php echo esc_attr( $s['toggle_text'] ); ?>" data-close="<?php echo esc_attr( $s['close_text'] ); ?>"><?php echo esc_html( $s['toggle_text'] ); ?></span>
					<?php endif; ?>
					<?php
					if ( 'text' !== $show ) :
						$open_icon  = vde_icon_html( isset( $s['open_icon'] ) ? $s['open_icon'] : array() );
						$close_icon = vde_icon_html( isset( $s['close_icon'] ) ? $s['close_icon'] : array() );
						if ( $open_icon ) :
							?>
							<span class="vdn-icon vdn-icon-open" aria-hidden="true"><?php echo $open_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor icon markup. ?></span>
							<span class="vdn-icon vdn-icon-close" aria-hidden="true"><?php echo $close_icon ? $close_icon : $open_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php else : ?>
							<span class="vdn-lines" aria-hidden="true"><span></span><span></span></span>
							<?php
						endif;
					endif;
					?>
				</button>
			<?php endif; ?>
			<div class="vdn-panel" id="<?php echo esc_attr( $panel_id ); ?>">
				<?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by the walker with escaping. ?>
			</div>
		</nav>
		<?php
	}
}
