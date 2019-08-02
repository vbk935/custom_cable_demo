<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the ID column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Id extends Abstract_Product_Table_Data {

	public function get_data() {
		$id = $this->get_product_id();

		if ( array_intersect( array( 'all', 'id' ), $this->links ) ) {
			$id = WCPT_Util::format_product_link( $this->product, $id );
		}

		return apply_filters( 'wc_product_table_data_id', $id, $this->product );
	}

	public function get_sort_data() {
		return $this->get_product_id();
	}

}