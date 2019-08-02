<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets data for a custom taxonomy column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Custom_Taxonomy extends Abstract_Product_Table_Data {

	private $taxonomy;
	private $date_format;
	private $column;
	private $is_date;

	public function __construct( $product, $taxonomy, $links = '', $date_format = '', $date_columns = array() ) {
		parent::__construct( $product, $links );

		$this->taxonomy		 = $taxonomy;
		$this->date_format	 = $date_format;
		$this->column		 = 'tax:' . $taxonomy;
		$this->is_date		 = in_array( $this->column, $date_columns );
	}

	public function get_data() {
		$result = $this->get_product_taxonomy_terms( $this->column );

		// If taxonomy is a date and there's only 1 term, format value in required date format.
		if ( $this->is_date && $this->date_format && ( false === strpos( $result, parent::get_separator( 'terms' ) ) ) ) {
			if ( $timestamp = $this->convert_to_timestamp( $result ) ) {
				$result = date( $this->date_format, $timestamp );
			}
		}

		/* @deprecated 2.2 - replaced by wc_product_table_data_taxonomy. */
		$result = apply_filters( 'wc_product_table_data_terms', $result, $this->product );

		// Filter the result.
		$result	 = apply_filters( 'wc_product_table_data_taxonomy', $result, $this->taxonomy, $this->product );
		$result	 = apply_filters( 'wc_product_table_data_taxonomy_' . $this->taxonomy, $result, $this->product );

		return $result;
	}

	public function get_sort_data() {
		if ( $this->is_date ) {
			$date		 = false;
			$date_terms	 = wc_get_product_terms( $this->get_parent_product_id(), $this->taxonomy, array( 'fields' => 'names' ) );

			if ( is_array( $date_terms ) && 1 === count( $date_terms ) ) {
				$date = reset( $date_terms );
			}

			// Format the hidden date column for sorting
			if ( $timestamp = $this->convert_to_timestamp( $date ) ) {
				return $timestamp;
			}

			// Need to return non-empty string to ensure all cells have a data-sort value.
			return '0';
		}
		return '';
	}

	private function convert_to_timestamp( $date ) {
		if ( ! $date ) {
			return false;
		}

		if ( apply_filters( 'wc_product_table_taxonomy_is_eu_au_date', false, $this->taxonomy ) ) {
			$date = str_replace( '/', '-', $date );
		}

		return WCPT_Util::strtotime( $date );
	}

}
