<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the description column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Description extends Abstract_Product_Table_Data {

	private $description_length; // number of words
	private $shortcodes;

	public function __construct( $product, $description_length, $shortcodes = false ) {
		parent::__construct( $product );

		$this->description_length	 = $description_length;
		$this->shortcodes			 = $shortcodes;
	}

	public function get_data() {
		$description = wcpt_get_description( $this->product );

		// For variations, if no variation description is set fall back to the parent variable product description
		if ( ! $description && 'variation' === $this->product->get_type() && $parent = wcpt_get_parent( $this->product ) ) {
			$description = wcpt_get_description( $parent );
		}

		// Format the description and (optionally) process shortcodes
		$description = apply_filters( 'the_content', parent::maybe_strip_shortcodes( $description, $this->shortcodes ) );

		// Check length
		if ( $this->description_length > 0 ) {
			$description = wp_trim_words( $description, $this->description_length, ' &hellip;' ); // wp_trim_words() will also strip tags
		}

		return apply_filters( 'wc_product_table_data_description', $description, $this->product );
	}

}