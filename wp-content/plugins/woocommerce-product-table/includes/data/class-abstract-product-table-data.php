<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract data class used to fetch data for a product in the table.
 *
 * @package   WooCommerce_Product_Table\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
abstract class Abstract_Product_Table_Data implements Product_Table_Data {

	protected $product;
	protected $links;
	protected $parent_product;

	public function __construct( $product, $links = '' ) {
		$this->product			 = $product;
		$this->links			 = $links ? (array) $links : array();
		$this->parent_product	 = WCPT_Util::maybe_get_parent( $this->product );
	}

	public function get_filter_data() {
		return '';
	}

	public function get_sort_data() {
		return '';
	}

	protected function get_product_id() {
		return wcpt_get_id( $this->product );
	}

	protected function get_parent_product() {
		return $this->parent_product;
	}

	protected function get_parent_product_id() {
		return wcpt_get_id( $this->get_parent_product() );
	}

	protected function get_product_taxonomy_terms( $column ) {
		$taxonomy = WC_Product_Table_Columns::get_column_taxonomy( $column );

		if ( ! $taxonomy ) {
			return '';
		}

		$item_type = 'terms';

		if ( in_array( $column, array( 'categories', 'tags' ) ) ) {
			$item_type = $column;
		} elseif ( taxonomy_is_product_attribute( $taxonomy ) ) {
			$item_type = 'attributes';
		}

		$terms = wc_get_product_terms( $this->get_parent_product_id(), $taxonomy, array( 'fields' => 'all' ) );

		if ( is_wp_error( $terms ) || ! $terms ) {
			return '';
		}

		$result	 = array();
		$links	 = array_intersect( array( 'all', $item_type ), $this->links );

		foreach ( $terms as $term ) {
			$result[] = self::format_term_data( $term, $links, $column );
		}

		if ( $links ) {
			$result = apply_filters( "term_links-{$taxonomy}", $result );
		}

		return $result ? implode( self::get_separator( $item_type ), $result ) : '';
	}

	protected static function format_term_data( $term, $show_links = false, $column = '' ) {
		$result = sprintf( '<span data-slug="%s">%s</span>', $term->slug, esc_html( $term->name ) );

		if ( $show_links && $column ) {
			$term_link = get_term_link( $term, $term->taxonomy );

			if ( ! is_wp_error( $term_link ) ) {
				$result = sprintf( '<a href="%s" data-column="%s" rel="tag">%s</a>', esc_url( $term_link ), WC_Product_Table_Columns::get_column_name( $column ), $result );
			}
		}

		return $result;
	}

	protected static function get_product_attribute( $attribute, $attributes ) {
		if ( ! $attributes ) {
			return false;
		}

		$sanitized_attribute = sanitize_title( $attribute );

		if ( isset( $attributes[$sanitized_attribute] ) ) {
			return $attributes[$sanitized_attribute];
		} elseif ( isset( $attributes['pa_' . $sanitized_attribute] ) ) {
			return $attributes['pa_' . $sanitized_attribute];
		}
		return false;
	}

	protected static function get_separator( $item_type ) {
		$sep = ', ';

		if ( 'custom_field_row' === $item_type ) {
			$sep = '<br/>';
		}

		return apply_filters( 'wc_product_table_separator', apply_filters( "wc_product_table_separator_{$item_type}", $sep ) );
	}

	protected static function maybe_strip_shortcodes( $text, $process_shortcodes = false ) {
		// Always strip our table shortcodes from content - processing a shortcode within a shortcode could cause an infinite loop.
		$text = preg_replace( sprintf( '/\[(?:%s|%s).*?\]/', WC_Product_Table_Shortcode::SHORTCODE, 'posts_table' ), '', $text );

		if ( $text && ! $process_shortcodes && ! apply_filters( 'wc_product_table_process_shortcodes', false ) ) {
			$text = strip_shortcodes( $text );
		}

		return $text;
	}

}