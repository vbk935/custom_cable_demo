<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for a hidden filter column.
 *
 * @package	  WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Hidden_Filter extends Abstract_Product_Table_Data {

	private $filter_column;
	private $lazy_load;

	public function __construct( $product, $filter_column, $lazy_load = false ) {
		parent::__construct( $product );

		$this->filter_column = $filter_column;
		$this->lazy_load	 = $lazy_load;
	}

	public function get_data() {
		// We don't need any data if using lazy load, as filtering is handled by the server.
		if ( $this->lazy_load ) {
			return '';
		}

		$result = '';

		if ( $attribute = WC_Product_Table_Columns::get_product_attribute( $this->filter_column ) ) {
			// Attribute filter.
			// Bail if not a taxonomy (i.e. global) attribute.
			if ( ! taxonomy_is_product_attribute( $attribute ) ) {
				return $result;
			}

			$product_attribute			 = parent::get_product_attribute( $attribute, $this->product->get_attributes() );
			$product_id_for_attribute	 = $this->get_product_id();

			// If attribute not set for a variation product, check the parent variable product.
			if ( ! $product_attribute && 'variation' === $this->product->get_type() ) {
				$product_attribute			 = parent::get_product_attribute( $attribute, $this->parent_product->get_attributes() );
				$product_id_for_attribute	 = $this->get_parent_product_id();
			}

			// Bail if attribute not found.
			if ( false === $product_attribute ) {
				return '';
			}

			if ( class_exists( 'WC_Product_Attribute' ) && $product_attribute instanceof WC_Product_Attribute ) {
				// Most product types.
				$result = implode( ' ', wc_get_product_terms( $product_id_for_attribute, $product_attribute->get_name(), array( 'fields' => 'slugs' ) ) );
			} elseif ( is_scalar( $product_attribute ) ) {
				// E.g. for variation products the attribute slug itself is stored.
				$result = $product_attribute;
			} elseif ( is_array( $product_attribute ) && isset( $product_attribute['name'] ) ) {
				// Back-compat WC < 3.0.
				$result = implode( ' ', wc_get_product_terms( $product_id_for_attribute, $product_attribute['name'], array( 'fields' => 'slugs' ) ) );
			}
		} elseif ( $taxonomy = WC_Product_Table_Columns::get_column_taxonomy( $this->filter_column ) ) {
			// Taxonomy filter.
			$product_terms = wc_get_product_terms( $this->get_parent_product_id(), $taxonomy, array( 'fields' => 'all' ) );

			if ( ! $product_terms || is_wp_error( $product_terms ) ) {
				$product_terms = array();
			}

			// If tax is hierarchical, we need to add any ancestor terms for each term this product has
			if ( $product_terms && is_taxonomy_hierarchical( $taxonomy ) ) {
				$ancestors = array();

				// Get the ancestors term IDs for all terms for this product
				foreach ( $product_terms as $term ) {
					$ancestors = array_merge( $ancestors, get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );
				}

				// Remove duplicates
				$ancestors			 = array_unique( $ancestors );
				$product_term_ids	 = wp_list_pluck( $product_terms, 'term_id' );

				// If not already in term list, convert ancestor to WP_Term object and add to results
				foreach ( $ancestors as $ancestor_id ) {
					if ( ! in_array( $ancestor_id, $product_term_ids ) ) {
						$ancestor_term = get_term( $ancestor_id, $taxonomy );

						if ( $ancestor_term instanceof WP_Term ) {
							$product_terms[] = $ancestor_term;
						}
					}
				}
			}

			// Return as a space-separated list of term slugs.
			$result = implode( ' ', wp_list_pluck( $product_terms, 'slug' ) );
		}

		return $result;
	}

}