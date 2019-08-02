<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for an attribute column.
 *
 * @package	  WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Attribute extends Abstract_Product_Table_Data {

	private $attribute;
	private $lazy_load;
	private $search_column;
	private $show_links;
	private $show_links_text_attributes;

	public function __construct( $product, $attribute, $links = '', $lazy_load = false ) {
		parent::__construct( $product, $links );

		$this->attribute	 = $attribute;
		$this->lazy_load	 = $lazy_load;
		$this->search_column = 'att_' . $this->attribute;

		// Should the attributes be formatted as links?
		$this->show_links = array_intersect( array( 'all', 'attributes' ), $this->links );

		// Only show links for text attributes if not using lazy load.
		$this->show_links_text_attributes = $this->show_links && ! $this->lazy_load;
	}

	public function get_data() {
		$product_attribute			 = parent::get_product_attribute( $this->attribute, $this->product->get_attributes() );
		$product_id_for_attribute	 = $this->get_product_id();

		// If attribute not set for a variation product, check the parent variable product.
		if ( ! $product_attribute && 'variation' === $this->product->get_type() ) {
			$product_attribute			 = parent::get_product_attribute( $this->attribute, $this->parent_product->get_attributes() );
			$product_id_for_attribute	 = $this->get_parent_product_id();
		}

		// Bail if attribute not found.
		if ( false === $product_attribute ) {
			return '';
		}

		$result = '';

		if ( class_exists( 'WC_Product_Attribute' ) && $product_attribute instanceof WC_Product_Attribute ) {
			// Most product types.
			if ( $product_attribute->is_taxonomy() ) {
				$result = array_map( array( $this, 'format_taxonomy_attribute' ), wc_get_product_terms( $product_id_for_attribute, $product_attribute->get_name(), array( 'fields' => 'all' ) ) );
			} else {
				$result = array_map( array( $this, 'format_text_attribute' ), $product_attribute->get_options() );
			}
		} elseif ( is_scalar( $product_attribute ) ) {
			// E.g. for variation products the attribute value itself is stored (not as an object).
			$value = $product_attribute;

			if ( taxonomy_is_product_attribute( $this->attribute ) ) {
				$term	 = get_term_by( 'slug', $value, $this->attribute );
				$result	 = ! is_wp_error( $term ) && $term ? $this->format_taxonomy_attribute( $term ) : $value;
			} else {
				$result = $this->format_text_attribute( $value );
			}
		} elseif ( is_array( $product_attribute ) ) {
			// Back-compat WC < 3.0.
			if ( isset( $product_attribute['is_taxonomy'] ) && $product_attribute['is_taxonomy'] ) {
				$result = array_map( array( $this, 'format_taxonomy_attribute' ), wc_get_product_terms( $product_id_for_attribute, $product_attribute['name'], array( 'fields' => 'all' ) ) );
			} elseif ( isset( $product_attribute['value'] ) ) {
				$result = array_map( array( $this, 'format_text_attribute' ), wc_get_text_attributes( $product_attribute['value'] ) );
			}
		}

		if ( is_array( $result ) ) {
			$result = implode( parent::get_separator( 'attributes' ), $result );
		}

		/* @deprecated 2.2 - replaced by wc_product_table_data_attribute. */
		$result = apply_filters( 'wc_product_table_data_attributes', $result, $this->product );

		// Filter the result.
		$result	 = apply_filters( 'wc_product_table_data_attribute', $result, $this->attribute, $this->product );
		$result	 = apply_filters( 'wc_product_table_data_attribute_' . $this->attribute, $result, $this->product );

		return $result;
	}

	private function format_taxonomy_attribute( $attribute_term ) {
		return parent::format_term_data( $attribute_term, $this->show_links, $this->search_column );
	}

	private function format_text_attribute( $text_attribute ) {
		return $this->show_links_text_attributes ? sprintf( '<a href="#" data-column="%s">%s</a>', $this->search_column, $text_attribute ) : $text_attribute;
	}

}