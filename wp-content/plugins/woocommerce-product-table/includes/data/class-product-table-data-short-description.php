<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for the short description column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Short_Description extends Abstract_Product_Table_Data {

	private $process_shortcodes;

	public function __construct( $product, $process_shortcodes = false ) {
		parent::__construct( $product );

		$this->process_shortcodes = $process_shortcodes;
	}

	public function get_data() {
		$post				 = WCPT_Util::get_post( $this->get_parent_product() );
		$short_description	 = parent::maybe_strip_shortcodes( $post->post_excerpt, $this->process_shortcodes );

		if ( $short_description ) {
			$short_description = apply_filters( 'woocommerce_short_description', $short_description );
		}

		return apply_filters( 'wc_product_table_data_short_description', $short_description, $this->product );
	}

}