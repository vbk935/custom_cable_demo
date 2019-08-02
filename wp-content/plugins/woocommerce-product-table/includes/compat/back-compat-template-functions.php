<?php
/**
 * Provides backwards compatibility for template functions in older versions of WooCommerce.
 *
 * @package   WooCommerce_Product_Table\Compat
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_star_rating_html' ) ) {

	/**
	 * Get HTML for star rating.
	 *
	 * @since  3.1.0
	 * @param  float $rating Rating being shown.
	 * @param  int   $count  Total number of ratings.
	 * @return string
	 */
	function wc_get_star_rating_html( $rating, $count = 0 ) {
		$html = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%">';

		if ( 0 < $count ) {
			/* translators: 1: rating 2: rating count */
			$html .= sprintf( _n( 'Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'woocommerce-product-table' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>', '<span class="rating">' . esc_html( $count ) . '</span>' );
		} else {
			/* translators: %s: rating */
			$html .= sprintf( esc_html__( 'Rated %s out of 5', 'woocommerce-product-table' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>' );
		}

		$html .= '</span>';

		return apply_filters( 'woocommerce_get_star_rating_html', $html, $rating, $count );
	}
}

if ( ! function_exists( 'wc_get_stock_html' ) ) {

	/**
	 * Get HTML to show product stock.
	 * @since  3.0.0
	 * @param  WC_Product $product
	 * @return string
	 */
	function wc_get_stock_html( $product ) {
		$availability		 = $product->get_availability();
		$availability_html	 = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

		return apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}
}

if ( ! function_exists( 'wc_query_string_form_fields' ) ) {

	/**
	 * Outputs hidden form inputs for each query string variable.
	 * @since 3.0.0
	 * @param array $values Name value pairs.
	 * @param array $exclude Keys to exclude.
	 * @param string $current_key Current key we are outputting.
	 */
	function wc_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
		if ( is_null( $values ) ) {
			$values = $_GET;
		}
		$html = '';

		foreach ( $values as $key => $value ) {
			if ( in_array( $key, $exclude, true ) ) {
				continue;
			}
			if ( $current_key ) {
				$key = $current_key . '[' . $key . ']';
			}
			if ( is_array( $value ) ) {
				$html .= wc_query_string_form_fields( $value, $exclude, $key, true );
			} else {
				$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
			}
		}

		if ( $return ) {
			return $html;
		} else {
			echo $html;
		}
	}
}

if ( ! function_exists( 'woocommerce_product_loop' ) ) {

	/**
	 * Should the WooCommerce loop be displayed?
	 *
	 * This will return true if we have posts (products) or if we have subcats to display.
	 *
	 * @since 3.4.0
	 * @return bool
	 */
	function woocommerce_product_loop() {
		return have_posts() || ( function_exists( 'woocommerce_get_loop_display_mode' ) && 'products' !== woocommerce_get_loop_display_mode() );
	}
}