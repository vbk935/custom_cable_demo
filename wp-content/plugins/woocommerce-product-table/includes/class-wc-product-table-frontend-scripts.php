<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the registering of the front-end scripts and stylesheets. Also creates the inline CSS (if required) for the product tables.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Frontend_Scripts {

	const SCRIPT_HANDLE	 = 'wc-product-table';
	const SCRIPT_VERSION	 = WC_Product_Table_Plugin::VERSION;

	public static function load_scripts() {
		// Register front-end styles and scripts
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_styles' ), 15 ); // after WooCommerce load_scripts()
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 15 ); // after WooCommerce load_scripts()
		add_filter( 'wc_product_table_multi_cart_button', array( __CLASS__, 'set_add_selected_to_cart_text' ), 5 );
	}

	public static function register_styles() {
		$suffix			 = WCPT_Util::get_script_suffix();
		$style_options	 = WCPT_Settings::get_setting_table_styling();

		// Photoswipe styles
		if ( ! wp_style_is( 'photoswipe', 'registered' ) ) {
			wp_register_style( 'photoswipe', WCPT_Util::get_wc_asset_url( 'css/photoswipe/photoswipe.css' ), array(), '4.1.1' );
		}
		if ( ! wp_style_is( 'photoswipe-default-skin', 'registered' ) ) {
			wp_register_style( 'photoswipe-default-skin', WCPT_Util::get_wc_asset_url( 'css/photoswipe/default-skin/default-skin.css' ), array( 'photoswipe' ), '4.1.1' );
		}

		// WCPT styles
		wp_register_style( 'jquery-data-tables', WCPT_Util::get_asset_url( "css/datatables/datatables{$suffix}.css" ), array(), '1.10.16' );
		wp_register_style( self::SCRIPT_HANDLE, WCPT_Util::get_asset_url( "css/wc-product-table{$suffix}.css" ), array( 'jquery-data-tables' ), self::SCRIPT_VERSION );

		wp_enqueue_style( self::SCRIPT_HANDLE );

		// Add custom styles (if enabled)
		if ( 'custom' === $style_options['use_theme'] ) {
			wp_add_inline_style( self::SCRIPT_HANDLE, self::build_custom_styles( $style_options ) );
		}
	}

	public static function register_scripts() {
		$suffix = WCPT_Util::get_script_suffix();

		// Block UI script
		if ( ! wp_script_is( 'jquery-blockui', 'registered' ) ) {
			wp_register_script( 'jquery-blockui', WCPT_Util::get_wc_asset_url( "js/jquery-blockui/jquery.blockUI{$suffix}.js" ), array( 'jquery' ) );
		}

		// Add to cart script
		if ( ! wp_script_is( 'wc-add-to-cart', 'registered' ) ) {
			wp_register_script( 'wc-add-to-cart', WCPT_Util::get_wc_asset_url( "js/frontend/add-to-cart{$suffix}.js" ), array( 'jquery' ), WC_VERSION, true );
		}

		// Photoswipe scripts
		if ( ! wp_script_is( 'photoswipe', 'registered' ) ) {
			wp_register_script( 'photoswipe', WCPT_Util::get_wc_asset_url( "js/photoswipe/photoswipe{$suffix}.js" ), array(), '4.1.1', true );
		}
		if ( ! wp_script_is( 'photoswipe-ui-default', 'registered' ) ) {
			wp_register_script( 'photoswipe-ui-default', WCPT_Util::get_wc_asset_url( "js/photoswipe/photoswipe-ui-default{$suffix}.js" ), array( 'photoswipe' ), '4.1.1', true );
		}

		// WCPT scripts
		wp_register_script( 'jquery-data-tables', WCPT_Util::get_asset_url( "js/datatables/datatables{$suffix}.js" ), array( 'jquery' ), '1.10.16', true );

		if ( apply_filters( 'wc_product_table_use_fitvids', true ) ) {
			wp_register_script( 'fitvids', WCPT_Util::get_asset_url( 'js/jquery-fitvids/jquery.fitvids.min.js' ), array( 'jquery' ), '1.1', true );
		}

		// We need to use a unique handle for our serialize object script to distinguish it from the built-in WordPress version.
		wp_register_script( 'jquery-serialize-object-macek', WCPT_Util::get_asset_url( "js/jquery-serialize-object/jquery.serialize-object{$suffix}.js" ), array( 'jquery' ), '2.5', true );


		wp_register_script( self::SCRIPT_HANDLE, WCPT_Util::get_asset_url( "js/wc-product-table{$suffix}.js" ), array( 'jquery-data-tables', 'jquery-blockui', 'jquery-serialize-object-macek' ), self::SCRIPT_VERSION, true );

		$script_obj = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( self::SCRIPT_HANDLE ),
			'wrapper_class' => esc_attr( WCPT_Util::get_wrapper_class() ),
			'multi_cart_button_class' => esc_attr( apply_filters( 'wc_product_table_multi_cart_class', WCPT_Util::get_button_class() ) ),
			'language' => apply_filters( 'wc_product_table_language_defaults', array(
				'info' => __( 'Showing _START_ to _END_ of _TOTAL_ products', 'woocommerce-product-table' ),
				'infoEmpty' => __( 'Showing 0 products', 'woocommerce-product-table' ),
				'infoFiltered' => __( '(_MAX_ products in total)', 'woocommerce-product-table' ),
				'lengthMenu' => __( 'Show _MENU_ products', 'woocommerce-product-table' ),
				'emptyTable' => __( 'No matching products found.', 'woocommerce-product-table' ),
				'zeroRecords' => __( 'No matching products found.', 'woocommerce-product-table' ),
				'search' => apply_filters( 'wc_product_table_search_label', __( 'Search:', 'woocommerce-product-table' ) ),
				'paginate' => array(
					'first' => __( 'First', 'woocommerce-product-table' ),
					'last' => __( 'Last', 'woocommerce-product-table' ),
					'next' => __( 'Next', 'woocommerce-product-table' ),
					'previous' => __( 'Previous', 'woocommerce-product-table' ),
				),
				'thousands' => _x( ',', 'thousands separator', 'woocommerce-product-table' ),
				'decimal' => _x( '.', 'decimal mark', 'woocommerce-product-table' ),
				'aria' => array(
					/* translators: ARIA text for sorting column in ascending order */
					'sortAscending' => __( ': activate to sort column ascending', 'woocommerce-product-table' ),
					/* translators: ARIA text for sorting column in descending order */
					'sortDescending' => __( ': activate to sort column descending', 'woocommerce-product-table' ),
				),
				'filterBy' => apply_filters( 'wc_product_table_filter_label', __( 'Filter:', 'woocommerce-product-table' ) ),
				'resetButton' => apply_filters( 'wc_product_table_reset_button', __( 'Reset', 'woocommerce-product-table' ) ),
				'multiCartButton' => esc_attr( apply_filters( 'wc_product_table_multi_cart_button', __( 'Add Selected To Cart', 'woocommerce-product-table' ) ) ),
				'multiCartNoSelection' => __( 'Please select one or more products.', 'woocommerce-product-table' )
			) )
		);

		if ( WCPT_Util::is_quick_view_pro_active() ) {
			$misc_settings = WCPT_Settings::get_setting_misc();

			if ( $misc_settings['quick_view_links'] ) {
				$script_obj['open_links_in_quick_view'] = true;
			}
		}

		wp_localize_script( self::SCRIPT_HANDLE, 'product_table_params', $script_obj );

		wp_enqueue_script( self::SCRIPT_HANDLE );
	}

	/**
	 * Register the scripts & styles for an individual product table.
	 *
	 * @param WC_Product_Table_Args $args
	 */
	public static function register_table_scripts( WC_Product_Table_Args $args ) {
		if ( $args->shortcodes ) {
			// Add fitVids for responsive video if we're displaying shortcodes.
			if ( apply_filters( 'wc_product_table_use_fitvids', true ) ) {
				wp_enqueue_script( 'fitvids' );
			}

			// Queue media element and playlist scripts/styles.
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-playlist' );
			add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
		}

		if ( in_array( 'add-to-cart', $args->columns ) ) {
			if ( 'dropdown' === $args->variations ) {
				wp_enqueue_script( 'wc-add-to-cart-variation' );
			}

			// Enqueue and localize add to cart script if not queued already.
			if ( $args->ajax_cart ) {
				wp_enqueue_script( 'wc-add-to-cart' );
			}

			// Make sure product add-on scripts are queued.
			if ( WCPT_Util::is_wc_product_addons_active() ) {
				wp_enqueue_script( 'jquery-tiptip', WCPT_Util::get_wc_asset_url( 'js/jquery-tiptip/jquery.tipTip.min.js' ), array( 'jquery' ), WC_VERSION, true );

				if ( isset( $GLOBALS['Product_Addon_Display'] ) && method_exists( $GLOBALS['Product_Addon_Display'], 'addon_scripts' ) ) {
					$GLOBALS['Product_Addon_Display']->addon_scripts();
				}
			}
		}

		// Enqueue Photoswipe for image lightbox.
		if ( in_array( 'image', $args->columns ) && $args->lightbox ) {
			wp_enqueue_style( 'photoswipe-default-skin' );
			wp_enqueue_script( 'photoswipe-ui-default' );

			if ( false === has_action( 'wp_footer', 'woocommerce_photoswipe' ) ) {
				add_action( 'wp_footer', array( __CLASS__, 'load_photoswipe_template' ) );
			}
		}

		// Load the quick view scripts if 'replace single product links with quick view' is enabled.
		if ( WCPT_Util::is_quick_view_pro_active() ) {
			$misc_settings = WCPT_Settings::get_setting_misc();

			if ( $misc_settings['quick_view_links'] ) {
				\Barn2\Plugin\WC_Quick_View_Pro\Frontend_Scripts::instance()->load();
			}
		}

		do_action( 'wc_product_table_load_table_scripts' );
	}

	public static function load_photoswipe_template() {
		wc_get_template( 'single-product/photoswipe.php' );
	}

	public static function set_add_selected_to_cart_text( $text ) {
		if ( $shortcode_defaults = WCPT_Settings::get_setting_table_defaults() ) {
			if ( ! empty( $shortcode_defaults['add_selected_text'] ) && WCPT_Settings::add_selected_to_cart_default_text() !== $shortcode_defaults['add_selected_text'] ) {
				$text = $shortcode_defaults['add_selected_text'];
			}
		}
		return $text;
	}

	private static function build_custom_styles( $options ) {
		$styles	 = array();
		$result	 = '';

		if ( ! empty( $options['border_outer'] ) ) {
			$styles[]	 = array(
				'selector' => 'table.wc-product-table.no-footer',
				'css' => 'border-bottom-width: 0;'
			);
			$styles[]	 = array(
				'selector' => 'table.wc-product-table',
				'css' => self::build_border_style( $options['border_outer'] )
			);
		}
		if ( ! empty( $options['border_header'] ) ) {
			$styles[]	 = array(
				'selector' => 'table.wc-product-table thead th',
				'css' => self::build_border_style( $options['border_header'], 'bottom' )
			);
			$styles[]	 = array(
				'selector' => 'table.wc-product-table tfoot th',
				'css' => self::build_border_style( $options['border_header'], 'top' )
			);
		}
		if ( ! empty( $options['border_cell'] ) ) {
			$cell_left_css = self::build_border_style( $options['border_cell'], array( 'left' ) );

			if ( $cell_left_css ) {
				$styles[]	 = array(
					'selector' => 'table.wc-product-table td, table.wc-product-table th',
					'css' => 'border-width: 0;'
				);
				$styles[]	 = array(
					'selector' => 'table.wc-product-table td, table.wc-product-table th',
					'css' => $cell_left_css
				);
				$styles[]	 = array(
					'selector' => 'table.wc-product-table td:first-child, table.wc-product-table td.control[style*="none"] + td, table.wc-product-table th:first-child',
					'css' => 'border-left: none !important;'
				);
			}

			$cell_top_css = self::build_border_style( $options['border_cell'], 'top' );

			if ( $cell_top_css ) {
				$styles[]	 = array(
					'selector' => 'table.wc-product-table td',
					'css' => $cell_top_css
				);
				$styles[]	 = array(
					'selector' => 'table.wc-product-table tbody tr:first-child td',
					'css' => 'border-top: none !important;'
				);
			}
		}
		if ( ! empty( $options['header_bg'] ) ) {
			$styles[]	 = array(
				'selector' => 'table.wc-product-table thead, table.wc-product-table tfoot',
				'css' => 'background-color: transparent;'
			);
			$styles[]	 = array(
				'selector' => 'table.wc-product-table th',
				'css' => self::build_background_style( $options['header_bg'] )
			);
		}
		if ( ! empty( $options['cell_bg'] ) ) {
			$styles[]	 = array(
				'selector' => 'table.wc-product-table tbody tr',
				'css' => 'background-color: transparent !important;'
			);
			$styles[]	 = array(
				'selector' => 'table.wc-product-table tbody td',
				'css' => self::build_background_style( $options['cell_bg'] )
			);
		}
		if ( ! empty( $options['header_font'] ) ) {
			$styles[] = array(
				'selector' => 'table.wc-product-table th',
				'css' => self::build_font_style( $options['header_font'] )
			);
		}
		if ( ! empty( $options['cell_font'] ) ) {
			$styles[] = array(
				'selector' => 'table.wc-product-table tbody td',
				'css' => self::build_font_style( $options['cell_font'] )
			);
		}

		foreach ( $styles as $style ) {
			if ( ! empty( $style['css'] ) ) {
				$result .= sprintf( '%1$s { %2$s } ', $style['selector'], $style['css'] );
			}
		}

		return trim( $result );
	}

	private static function build_background_style( $bg_color ) {
		if ( ! $bg_color ) {
			return '';
		}
		return sprintf( 'background-color: %s !important;', $bg_color );
	}

	private static function build_border_style( $option, $borders = 'all' ) {
		if ( ! is_array( $borders ) ) {
			$borders = array_filter( (array) $borders );
		}
		$result	 = '';
		$o		 = wp_parse_args( $option, array( 'size' => '', 'color' => '' ) );

		$border_size	 = is_numeric( $o['size'] ) ? $o['size'] . 'px' : '';
		$border_color	 = $o['color'];

		foreach ( $borders as $border ) {
			$border_edge = '';

			if ( in_array( $border, array( 'top', 'left', 'bottom', 'right' ) ) ) {
				$border_edge = $border . '-';
			}

			if ( $border_size ) {
				$result	 .= sprintf( 'border-%1$sstyle: solid !important;', $border_edge );
				$result	 .= sprintf( 'border-%1$swidth: %2$s !important;', $border_edge, $border_size );
			}
			if ( $border_color ) {
				$result .= sprintf( 'border-%1$scolor: %2$s !important;', $border_edge, $border_color );
			}
		}

		return $result;
	}

	private static function build_font_style( $option ) {
		$style = '';

		if ( isset( $option['size'] ) && is_numeric( $option['size'] ) ) {
			$style .= sprintf( 'font-size: %upx !important;', $option['size'] );
		}

		if ( ! empty( $option['color'] ) ) {
			$style .= sprintf( 'color: %s !important;', $option['color'] );
		}

		return $style;
	}
}
