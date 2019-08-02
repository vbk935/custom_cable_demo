<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the dimensions column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Dimensions extends Abstract_Product_Table_Data {

	public function get_data() {
		$dimensions = $this->product->has_dimensions() ? wcpt_get_dimensions( $this->product ) : '';
		return apply_filters( 'wc_product_table_data_dimensions', $dimensions, $this->product );
	}

}