<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the stock column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Stock extends Abstract_Product_Table_Data {

	public function get_data() {
		$availability = $this->product->get_availability();

		if ( empty( $availability['availability'] ) && $this->product->is_in_stock() ) {
			$availability['availability'] = __( 'In stock', 'woocommerce-product-table' );
		}
		$stock = '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

		return apply_filters( 'wc_product_table_data_stock', $stock, $this->product );
	}

}