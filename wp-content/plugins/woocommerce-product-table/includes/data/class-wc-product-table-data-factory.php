<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Factory class to get the product table data object for a given column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Data_Factory {

	/**
	 * The full list of table args.
	 *
	 * @var WC_Product_Table_Args
	 */
	private $args;

	public function __construct( $args ) {
		$this->args = $args;
	}

	public function create( $column, $product ) {

		/**
		 * Support for custom columns in the product table.
		 * This filter should return an object implementing the Product_Table_Data interface.
		 *
		 * @see Product_Table_Data
		 */
		$data_object = apply_filters( 'wc_product_table_custom_table_data_' . $column, false, $product, $this->args );

		if ( $data_object instanceof Product_Table_Data ) {
			return $data_object;
		}

		switch ( $column ) {
			case 'id':
			case 'sku':
			case 'name':
			case 'categories':
			case 'tags':
			case 'weight':
			case 'dimensions':
			case 'stock':
			case 'price':
			case 'reviews':
				$data_class = 'Product_Table_Data_' . $column;
				if ( class_exists( $data_class ) ) { // case-insensitive
					$data_object = new $data_class( $product, $this->args->links );
				}
				break;
			case 'image';
				$data_object = new Product_Table_Data_Image( $product, $this->args->links, $this->args->image_size, $this->args->lightbox );
				break;
			case 'date';
				$data_object = new Product_Table_Data_Date( $product, $this->args->date_format );
				break;
			case 'short-description':
				$data_object = new Product_Table_Data_Short_Description( $product, $this->args->shortcodes );
				break;
			case 'description':
				$data_object = new Product_Table_Data_Description( $product, $this->args->description_length, $this->args->shortcodes );
				break;
			case 'add-to-cart':
				$data_object = new Product_Table_Data_Add_To_Cart( $product, $this->args->variations, $this->args->show_quantity, $this->args->cart_button, $this->args->is_multi_add_to_cart() );
				break;
			case 'button':
				$data_object = new Product_Table_Data_Button( $product, $this->args->button_text );
				break;
			default:
				if ( $attribute	 = WC_Product_Table_Columns::get_product_attribute( $column ) ) {
					// Attribute column.
					$data_object = new Product_Table_Data_Attribute( $product, $attribute, $this->args->links, $this->args->lazy_load );
				} elseif ( $taxonomy = WC_Product_Table_Columns::get_custom_taxonomy( $column ) ) {
					// Custom taxonomy column.
					$data_object = new Product_Table_Data_Custom_Taxonomy( $product, $taxonomy, $this->args->links, $this->args->date_format, $this->args->date_columns );
				} elseif ( $field = WC_Product_Table_Columns::get_custom_field( $column ) ) {
					// Custom field column.
					$data_object = new Product_Table_Data_Custom_Field( $product, $field, $this->args->links, $this->args->image_size, $this->args->date_format, $this->args->date_columns );
				} elseif ( $filter = WC_Product_Table_Columns::get_hidden_filter_column( $column ) ) {
					// Hidden filter column.
					$data_object = new Product_Table_Data_Hidden_Filter( $product, $filter, $this->args->lazy_load );
				}
				break;
		}

		return $data_object;
	}

}
