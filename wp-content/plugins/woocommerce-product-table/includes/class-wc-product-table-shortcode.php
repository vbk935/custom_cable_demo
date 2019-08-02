<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class handles our product table shortcode.
 *
 * Example usage:
 *   [product_table
 *       columns="name,description,price,add-to-cart"
 *       category="t-shirts",
 *       tag="cool"]
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Shortcode {

	const SHORTCODE = 'product_table';

	public static function register_shortcode() {
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'do_shortcode' ) );
	}

	/**
	 * Handles our product table shortcode.
	 *
	 * @param array $atts The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {
		if ( ! self::can_do_shortocde() ) {
			return '';
		}

		// Fill-in missing attributes, and ensure back compat for old attribute names.
		$r = shortcode_atts( WC_Product_Table_Args::get_defaults(), wcpt_back_compat_args( $atts ), self::SHORTCODE );

		// Return the table as HTML
		$output = apply_filters( 'wc_product_table_shortcode_output', wc_get_product_table( $r ) );

		return $output;
	}

	private static function can_do_shortocde() {
		// Don't run in the search results.
		if ( is_search() && in_the_loop() && ! apply_filters( 'wc_product_table_run_in_search', false ) ) {
			return false;
		}

		return true;
	}

}
// class WC_Product_Table_Shortcode
