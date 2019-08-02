<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the weight column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Weight extends Abstract_Product_Table_Data {

	public function get_data() {
		$weight = $this->product->has_weight() ? wc_format_localized_decimal( $this->product->get_weight() ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) : '';
		return apply_filters( 'wc_product_table_data_weight', $weight, $this->product );
	}

}