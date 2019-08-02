<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the categories column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Categories extends Abstract_Product_Table_Data {

	public function get_data() {
		return apply_filters( 'wc_product_table_data_categories', $this->get_product_taxonomy_terms( 'categories' ), $this->product );
	}

}