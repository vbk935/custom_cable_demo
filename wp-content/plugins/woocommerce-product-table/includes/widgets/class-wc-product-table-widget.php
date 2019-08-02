<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Widget' ) ) {
	include_once WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-widget.php';
}

if ( class_exists( 'WC_Widget' ) ) {

	/**
	 * Abstract widget class extended by the Product Table widgets.
	 *
	 * @package   WooCommerce_Product_Table\Widgets
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 */
	abstract class WC_Product_Table_Widget extends WC_Widget {

		public function __construct() {
			parent::__construct();

			add_filter( 'body_class', array( __CLASS__, 'body_class' ), 99 );
		}

		public static function body_class( $classes ) {
			// Add .woocommerce to body class if product table used on page, so filter widgets pick up correct styles in certain themes (Genesis, Total, etc).
			if ( ! in_array( 'woocommerce', $classes ) && WCPT_Util::is_table_on_page() ) {
				$classes[] = 'woocommerce';
			}

			return $classes;
		}

		public static function is_table_on_page() {
			return WCPT_Util::is_table_on_page();
		}

		public static function unescape_commas( $link ) {
			return str_replace( '%2C', ',', $link );
		}

		protected static function get_main_tax_query() {
			global $wp_the_query;
			return isset( $wp_the_query->tax_query, $wp_the_query->tax_query->queries ) ? $wp_the_query->tax_query->queries : array();
		}

		protected static function get_main_meta_query() {
			global $wp_the_query;
			return isset( $wp_the_query->query_vars['meta_query'] ) ? $wp_the_query->query_vars['meta_query'] : array();
		}

		/**
		 * Return the currently viewed taxonomy name.
		 * @return string
		 */
		protected function get_current_taxonomy() {
			return is_tax() ? get_queried_object()->taxonomy : '';
		}

		/**
		 * Return the currently viewed term ID.
		 * @return int
		 */
		protected function get_current_term_id() {
			return absint( is_tax() ? get_queried_object()->term_id : 0 );
		}

		/**
		 * Return the currently viewed term slug.
		 * @return int
		 */
		protected function get_current_term_slug() {
			return absint( is_tax() ? get_queried_object()->slug : 0 );
		}

	}
	// class WC_Product_Table_Widget
} // if WC_Widget exists
