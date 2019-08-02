<?php

/**
 * The main plugin file for WooCommerce Product Table.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Product Table
 * Plugin URI:		  https://barn2.co.uk/wordpress-plugins/woocommerce-product-table/
 * Description:       Display and purchase WooCommerce products from a searchable and sortable table. Filter by anything.
 * Version:           2.4.1
 * Author:            Barn2 Media
 * Author URI:        https://barn2.co.uk
 * Text Domain:       woocommerce-product-table
 * Domain Path:       /languages
 *
 * WC requires at least: 2.6
 * WC tested up to: 3.6
 *
 * Copyright:		  Barn2 Media Ltd
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */
// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main plugin class for WooCommerce Product Table.
 *
 * Implemented as a singleton.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class WC_Product_Table_Plugin {

	const NAME	 = 'WooCommerce Product Table';
	const VERSION	 = '2.4.1';
	const FILE	 = __FILE__;

	/* Our plugin license */

	private $license;

	/* The singleton instance */
	private static $_instance = null;

	public function __construct() {
		$this->define_constants();
		$this->includes();

		// Create plugin license & updater
		$this->license = new Barn2_Plugin_License( self::FILE, self::NAME, self::VERSION, 'wcpt' );
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function load() {
		add_action( 'plugins_loaded', array( $this, 'init_hooks' ) );
	}

	private function define_constants() {
		if ( ! defined( 'WCPT_INCLUDES_DIR' ) ) {
			define( 'WCPT_INCLUDES_DIR', plugin_dir_path( self::FILE ) . 'includes/' );
		}
		if ( ! defined( 'WCPT_PLUGIN_BASENAME' ) ) {
			define( 'WCPT_PLUGIN_BASENAME', plugin_basename( self::FILE ) );
		}
	}

	private function includes() {
		// License
		require_once WCPT_INCLUDES_DIR . 'license/class-b2-plugin-license.php';

		// Core
		require_once WCPT_INCLUDES_DIR . 'util/class-wcpt-util.php';
		require_once WCPT_INCLUDES_DIR . 'util/class-wcpt-settings.php';
		require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-args.php';
		require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-columns.php';

		// Front-end
		if ( $this->is_front_end() ) {
			require_once WCPT_INCLUDES_DIR . 'lib/class-html-data-table.php';
			require_once WCPT_INCLUDES_DIR . 'lib/class-wp-scoped-hooks.php';

			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-config-builder.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-hook-manager.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-query.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-cache.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-factory.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-cart-handler.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-shortcode.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-ajax-handler.php';
			require_once WCPT_INCLUDES_DIR . 'class-wc-product-table-frontend-scripts.php';
			require_once WCPT_INCLUDES_DIR . 'template-functions.php';

			// Data classes
			require_once WCPT_INCLUDES_DIR . 'data/interface-product-table-data.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-abstract-product-table-data.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-add-to-cart.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-attribute.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-button.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-categories.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-custom-field.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-custom-taxonomy.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-date.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-description.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-dimensions.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-hidden-filter.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-id.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-image.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-name.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-price.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-reviews.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-short-description.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-sku.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-stock.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-tags.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-product-table-data-weight.php';
			require_once WCPT_INCLUDES_DIR . 'data/class-wc-product-table-data-factory.php';

			// Back compat functions
			require_once WCPT_INCLUDES_DIR . 'compat/back-compat-functions.php';
		}

		// Admin
		if ( is_admin() ) {
			require_once WCPT_INCLUDES_DIR . 'lib/class-wc-settings-additional-field-types.php';
			require_once WCPT_INCLUDES_DIR . 'admin/class-wc-product-table-admin-controller.php';
			require_once WCPT_INCLUDES_DIR . 'admin/class-wc-product-table-admin-settings-page.php';
			require_once WCPT_INCLUDES_DIR . 'admin/class-wc-product-table-admin-tinymce.php';
		}
	}

	public function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );

		if ( WCPT_Util::is_wc_active() && $this->license->is_valid() ) {
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_action( 'after_setup_theme', array( $this, 'theme_compat' ), 20 );
			add_action( 'after_setup_theme', array( $this, 'back_compat_template_functions' ), 20 ); // After WC loads wc-template-functions.php
		}
	}

	public function init() {
		$this->load_textdomain();

		// Initialise admin.
		if ( is_admin() ) {
			$this->admin = new WC_Product_Table_Admin_Controller( $this->license );
		}

		// Initialise plugin if valid and WC active.
		if ( WCPT_Util::is_wc_active() && $this->license->is_valid() ) {
			if ( is_admin() ) {
				WC_Product_Table_Admin_TinyMCE::setup();
			}

			if ( $this->is_front_end() ) {
				WC_Product_Table_Shortcode::register_shortcode();
				WC_Product_Table_Frontend_Scripts::load_scripts();
				WC_Product_Table_Ajax_Handler::register_ajax_events();
				WC_Product_Table_Cart_Handler::handle_cart();
			}
		}
	}

	public function register_widgets() {
		// Don't register if running WC < 2.6 as widgets not supported.
		if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '2.6', '<' ) ) {
			return;
		}

		// Widget includes
		require_once WCPT_INCLUDES_DIR . 'widgets/class-wc-product-table-widget.php';
		require_once WCPT_INCLUDES_DIR . 'widgets/class-wcpt-widget-layered-nav-filters.php';
		require_once WCPT_INCLUDES_DIR . 'widgets/class-wcpt-widget-layered-nav.php';
		require_once WCPT_INCLUDES_DIR . 'widgets/class-wcpt-widget-price-filter.php';
		require_once WCPT_INCLUDES_DIR . 'widgets/class-wcpt-widget-rating-filter.php';

		$widget_classes = array(
			'WC_Product_Table_Widget_Layered_Nav_Filters',
			'WC_Product_Table_Widget_Layered_Nav',
			'WC_Product_Table_Widget_Price_Filter',
			'WC_Product_Table_Widget_Rating_Filter'
		);

		// Register the product table widgets
		array_map( 'register_widget', array_filter( $widget_classes, 'class_exists' ) );
	}

	public function theme_compat() {
		require_once WCPT_INCLUDES_DIR . 'compat/class-wcpt-theme-compat.php';
		WCPT_Theme_Compat::register_theme_compat_hooks();
	}

	public function back_compat_template_functions() {
		require_once WCPT_INCLUDES_DIR . 'compat/back-compat-template-functions.php';
	}

	public function has_valid_license() {
		return $this->license->is_valid();
	}

	private function is_front_end() {
		return ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	private function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-product-table', false, dirname( WCPT_PLUGIN_BASENAME ) . '/languages' );
	}

}

/**
 * Helper function to return the main plugin instance.
 *
 * @return WC_Product_Table_Plugin The singleton instance
 */
if ( ! function_exists( 'wc_product_table' ) ) {

	function wc_product_table() {
		return WC_Product_Table_Plugin::instance();
	}

}

// Load the plugin
WC_Product_Table_Plugin::instance()->load();
