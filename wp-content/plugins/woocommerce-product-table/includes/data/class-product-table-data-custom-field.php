<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the data for a custom field column.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Custom_Field extends Abstract_Product_Table_Data {

	private $field;
	private $image_size;
	private $date_format;
	private $is_date;
	private $acf_field_object;

	public function __construct( $product, $field, $links = '', $image_size = '', $date_format = '', $date_columns = array() ) {
		parent::__construct( $product, $links );

		$this->field		 = $field;
		$this->image_size	 = $image_size ? $image_size : 'thumbnail';
		$this->date_format	 = $date_format;
		$this->is_date		 = in_array( 'cf:' . $field, $date_columns );

		// We use the parent product (if any) for retrieving the ACF field.
		$this->acf_field_object = WCPT_Util::get_acf_field_object( $this->field, $this->get_parent_product_id() );
	}

	public function get_data() {
		$is_acf_date_picker = false;

		if ( $this->acf_field_object ) {
			// Advanced Custom Fields field.
			$is_acf_date_picker	 = in_array( $this->acf_field_object['type'], array( 'date_picker', 'date_time_picker' ) );
			$cf_value			 = $this->get_acf_value( $this->acf_field_object, $this->get_parent_product_id() );
		} else {
			// Normal custom field.
			$cf_value = get_post_meta( $this->get_parent_product_id(), $this->field, false );
		}

		// Flatten field.
		$cf_value = array_reduce( (array) $cf_value, array( $this, 'flatten_custom_field' ), '' );

		// Format as date if this is a date custom field and we have a date format.
		if ( $this->is_date && $this->date_format && ! $is_acf_date_picker ) {
			$format = apply_filters( 'wc_product_table_custom_field_stored_date_format', '', $this->field );

			// Convert to timestamp - we don't pass date_format here as that specifies the desired output format, not the input format.
			if ( $timestamp = $this->convert_to_timestamp( $cf_value, $format ) ) {
				// Format date using desired format.
				$cf_value = date( $this->date_format, $timestamp );
			}
		}

		// Format as link if custom field is a URL - link text defaults to URL, minus the 'http://'
		if ( 0 === strpos( $cf_value, 'http' ) && $link_url = filter_var( $cf_value, FILTER_VALIDATE_URL ) ) {
			$link_text	 = str_replace( array( 'http://', 'https://' ), '', $cf_value );
			$cf_value	 = sprintf( '<a href="%1$s">%2$s</a>', apply_filters( 'wc_product_table_url_custom_field_link', $link_url, $this->field, $this->product ), apply_filters( 'wc_product_table_url_custom_field_text', $link_text, $this->field, $this->product ) );
		}

		// @deprecated 1.3.3 - replaced by 'wc_product_table_data_custom_field'.
		$cf_value = apply_filters( 'wc_product_table_custom_field_value', $cf_value, $this->get_product_id(), $this->field );

		// Filter the result.
		$cf_value	 = apply_filters( 'wc_product_table_data_custom_field', $cf_value, $this->field, $this->product );
		$cf_value	 = apply_filters( 'wc_product_table_data_custom_field_' . $this->field, $cf_value, $this->product );

		return $cf_value;
	}

	public function get_sort_data() {
		if ( $this->is_date ) {
			$date	 = get_post_meta( $this->get_parent_product_id(), $this->field, true );
			$format	 = apply_filters( 'wc_product_table_custom_field_stored_date_format', '', $this->field );

			// Format the hidden date column for sorting
			if ( $timestamp = $this->convert_to_timestamp( $date, $format ) ) {
				return $timestamp;
			}

			// Need to return non-empty string to ensure all cells have a data-sort value.
			return '0';
		}
		return '';
	}

	private function get_acf_value( $field_obj, $product_id = false ) {
		if ( ! $field_obj || ! isset( $field_obj['value'] ) || '' === $field_obj['value'] || 'null' === $field_obj['value'] || empty( $field_obj['type'] ) ) {
			return '';
		}

		$cf_value = $field_obj['value'];

		switch ( $field_obj['type'] ) {
			case 'text':
			case 'number':
			case 'email':
			case 'password':
			case 'color_picker':
			case 'textarea':
			case 'wysiwyg':
			case 'google_map':
				$cf_value	 = get_field( $field_obj['name'], $product_id, true );
				break;
			case 'date_picker':
			case 'date_time_picker':
				if ( $timestamp	 = $this->convert_to_timestamp( $cf_value ) ) {
					// Use 'date_format' option if specified, otherwise use the 'return format' for the date field
					$date_format = $this->date_format ? $this->date_format : $field_obj['return_format'];
					$cf_value	 = date( $date_format, $timestamp );
				}
				break;
			case 'time_picker':
				if ( $timestamp = $this->convert_to_timestamp( $cf_value ) ) {
					$cf_value = date( $field_obj['return_format'], $timestamp );
				}
				break;
			case 'radio':
				if ( ! empty( $field_obj['choices'] ) && ( is_int( $cf_value ) || is_string( $cf_value ) ) && isset( $field_obj['choices'][$cf_value] ) ) {
					$cf_value = $field_obj['choices'][$cf_value];
				}
				break;
			case 'select':
			case 'checkbox':
				if ( ! empty( $field_obj['choices'] ) && ( is_string( $cf_value ) || is_int( $cf_value ) || is_array( $cf_value ) ) ) {
					$labels = array();

					foreach ( (array) $cf_value as $value ) {
						if ( isset( $field_obj['choices'][$value] ) ) {
							$labels[] = $field_obj['choices'][$value];
						} else {
							$labels[] = $value;
						}
					}
					$cf_value = $labels;
				}
				break;
			case 'true_false':
				$cf_value	 = $cf_value ? __( 'True', 'woocommerce-product-table' ) : __( 'False', 'woocommerce-product-table' );
				break;
			case 'file':
				$cf_value	 = wp_get_attachment_link( $cf_value, $this->image_size, false, true );
				break;
			case 'image':
				$cf_value	 = wp_get_attachment_link( $cf_value, $this->image_size );
				break;
			case 'page_link':
			case 'post_object':
			case 'relationship':
				$titles		 = array();

				foreach ( (array) $cf_value as $post_id ) {
					if ( array_intersect( array( 'all', 'name' ), $this->links ) ) {
						$titles[] = sprintf( '<a href="%1$s">%2$s</a>', get_permalink( $post_id ), get_the_title( $post_id ) );
					} else {
						$titles[] = get_the_title( $post_id );
					}
				}
				$cf_value	 = $titles;
				break;
			case 'taxonomy':
				$term_links	 = array();
				foreach ( (array) $cf_value as $term_id ) {
					if ( $term = get_term_by( 'id', $term_id, $field_obj['taxonomy'] ) ) {
						if ( array_intersect( array( 'all', 'terms' ), $this->links ) ) {
							$term_links[] = sprintf( '<a href="%1$s" rel="tag">%2$s</a>', esc_url( get_term_link( $term_id, $field_obj['taxonomy'] ) ), $term->name );
						} else {
							$term_links[] = $term->name;
						}
					}
				}
				$cf_value	 = $term_links;
				break;
			case 'user':
				$users		 = array();
				foreach ( (array) $cf_value as $user_id ) {
					if ( array_intersect( array( 'all', 'author' ), $this->links ) ) {
						$users[] = sprintf(
							'<a href="%1$s" rel="author">%2$s</a>', esc_url( get_author_posts_url( $user_id ) ), get_the_author_meta( 'display_name', $user_id )
						);
					} else {
						$users[] = get_the_author_meta( 'display_name', $user_id );
					}
				}
				$cf_value		 = $users;
				break;
			case 'repeater':
				$repeater_value	 = array();

				if ( have_rows( $field_obj['name'], $product_id ) ) {
					while ( have_rows( $field_obj['name'], $product_id ) ) {
						the_row();

						foreach ( $field_obj['sub_fields'] as $sub_field ) {
							$sub_field_value	 = $this->get_acf_value( get_sub_field_object( $sub_field['name'], false ), $product_id );
							$repeater_value[]	 = apply_filters( 'wc_product_table_acf_sub_field_value', $sub_field_value, $sub_field['name'], $field_obj['name'], $product_id );
						}
					}
				}
				$cf_value = apply_filters( 'wc_product_table_acf_repeater_field_value', $repeater_value );
				break;
			//@todo: Other layout field types?
		}

		return apply_filters( 'wc_product_table_acf_value', $cf_value, $field_obj, $product_id );
	}

	private function convert_to_timestamp( $date, $format = '' ) {
		if ( ! $date ) {
			return false;
		}

		if ( WCPT_Util::is_european_date_format( $format ) || apply_filters( 'wc_product_table_custom_field_is_eu_au_date', false, $this->field ) ) {
			$date = str_replace( '/', '-', $date );
		}

		return WCPT_Util::strtotime( $date );
	}

	private function flatten_custom_field( $carry, $item ) {
		if ( is_array( $item ) ) {
			if ( $carry ) {
				$carry .= parent::get_separator( 'custom_field_row' );
			}
			$carry .= array_reduce( $item, array( $this, 'flatten_custom_field' ), '' );
		} elseif ( '' !== $item && false !== $item ) {
			if ( $carry ) {
				$carry .= parent::get_separator( 'custom_field' );
			}
			$carry .= $item;
		}
		return $carry;
	}

}
