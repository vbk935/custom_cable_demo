<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the button column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Button extends Abstract_Product_Table_Data {

	private $button_text;

	public function __construct( $product, $button_text ) {
		parent::__construct( $product );

		$this->button_text = $button_text;
	}

	public function get_data() {
		$button_class = trim( 'product-details-button ' . WCPT_Util::get_button_class() );

		if ( apply_filters( 'wc_product_table_wrap_button_text', false ) ) {
			$button_class .= ' wrap';
		}

		$button = WCPT_Util::format_product_link( $this->product, $this->button_text, $button_class );

		return apply_filters( 'wc_product_table_data_button', $button, $this->product );
	}

}
