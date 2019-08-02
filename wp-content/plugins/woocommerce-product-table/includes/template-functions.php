<?php
/**
 * Template functions for WooCommerce Product Table.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_product_table' ) ) {

	function wc_get_product_table( $args = array() ) {
		// Create and return the table as HTML
		$table = WC_Product_Table_Factory::create( $args );
		return $table->get_table( 'html' );
	}
}

if ( ! function_exists( 'wc_the_product_table' ) ) {

	function wc_the_product_table( $args = array() ) {
		echo wc_get_product_table( $args );
	}
}
