<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the AJAX requests for product tables.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Ajax_Handler {

	public static function register_ajax_events() {
		$ajax_events = array(
			'wcpt_load_products' => 'load_products',
			'wcpt_add_to_cart' => 'add_to_cart',
			'wcpt_add_to_cart_multi' => 'add_to_cart_multi'
		);

		foreach ( $ajax_events as $action => $handler ) {
			add_action( 'wp_ajax_nopriv_' . $action, array( __CLASS__, $handler ) );
			add_action( 'wp_ajax_' . $action, array( __CLASS__, $handler ) );
		}
	}

	public static function load_products() {
		$table_id	 = filter_input( INPUT_POST, 'table_id', FILTER_SANITIZE_STRING );
		$table		 = WC_Product_Table_Factory::fetch( $table_id );

		if ( ! $table ) {
			wp_die( 'Error: product table could not be loaded.' );
		}

		// Build the args to update
		$new_args					 = array();
		$new_args['rows_per_page']	 = filter_input( INPUT_POST, 'length', FILTER_VALIDATE_INT );
		$new_args['offset']			 = filter_input( INPUT_POST, 'start', FILTER_VALIDATE_INT );

		$columns	 = filter_input( INPUT_POST, 'columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$search		 = filter_input( INPUT_POST, 'search', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$order		 = filter_input( INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$main_order	 = ! empty( $order[0] ) ? $order[0] : array();

		// Set sort column and direction
		if ( isset( $main_order['column'] ) ) {
			$order_col_index = filter_var( $main_order['column'], FILTER_VALIDATE_INT );

			if ( false !== $order_col_index && isset( $columns[$order_col_index]['data'] ) ) {
				$new_args['sort_by'] = filter_var( $columns[$order_col_index]['data'], FILTER_SANITIZE_STRING );
			}
			if ( ! empty( $main_order['dir'] ) ) {
				$new_args['sort_order'] = filter_var( $main_order['dir'], FILTER_SANITIZE_STRING );
			}
		}

		$new_args['search_term']	 = '';
		$new_args['search_filters']	 = array();

		// Set search term
		if ( ! empty( $search['value'] ) ) {
			$search_term = filter_var( $search['value'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );

			// Don't search unless they've typed at least 3 characters.
			if ( WCPT_Util::is_valid_search_term( $search_term ) ) {
				$new_args['search_term'] = $search_term;
			}
		}

		// Set search filters
		if ( ! empty( $columns ) ) {
			foreach ( $columns as $column ) {
				if ( empty( $column['data'] ) || empty( $column['search']['value'] ) ) {
					continue;
				}

				$column_name = WC_Product_Table_Columns::is_hidden_filter_column( $column['data'] ) ? WC_Product_Table_Columns::get_hidden_filter_column( $column['data'] ) : $column['data'];

				if ( $taxonomy = WC_Product_Table_Columns::get_column_taxonomy( $column_name ) ) {
					$term = get_term_by( 'slug', $column['search']['value'], $taxonomy );

					if ( $term instanceof WP_Term ) {
						$new_args['search_filters'][$taxonomy] = $term->term_id;
					}
				}
			}
		}

		// Merge layered nav params (if passed) into $_GET so WooCommerce picks them up.
		if ( $layered_nav_params = WCPT_Util::get_layered_nav_params( true ) ) {
			$_GET = array_merge( $_GET, $layered_nav_params );
		}

		// Retrieve the new table and convert to array
		$table->update( $new_args );

		// Build output
		$output['draw']				 = filter_input( INPUT_POST, 'draw', FILTER_VALIDATE_INT );
		$output['recordsFiltered']	 = $table->query->get_total_filtered_products();
		$output['recordsTotal']		 = $table->query->get_total_products();

		$table_data	 = $table->get_data( 'array' );
		$data		 = array();

		if ( is_array( $table_data ) ) {
			// We don't need the cell attributes, so flatten data and append row attributes under the key '__attributes'.
			foreach ( $table_data as $row ) {
				$data[] = array_merge( array(
					'__attributes' => $row['attributes']
					), wp_list_pluck( $row['cells'], 'data' )
				);
			}
		}

		$output['data'] = $data;

		wp_send_json( $output );
	}

	public static function add_to_cart() {
		ob_start();

		$product_id		 = apply_filters( 'woocommerce_add_to_cart_product_id', filter_input( INPUT_POST, 'product_id', FILTER_VALIDATE_INT ) );
		$quantity		 = filter_input( INPUT_POST, 'quantity', FILTER_VALIDATE_FLOAT, array( 'options' => array( 'default' => 1, 'min_range' => 0 ) ) );
		$variation_id	 = filter_input( INPUT_POST, 'variation_id', FILTER_VALIDATE_INT );
		$variations		 = $variation_id ? WCPT_Util::extract_attributes( $_POST ) : false;

		if ( WC_Product_Table_Cart_Handler::add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wcpt_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			// Return fragments
			$data = self::get_refreshed_fragments();
		} else {
			// If there was an error adding to the cart
			$data = array(
				'error' => true,
				'error_message' => self::format_errors()
			);
		}

		wp_send_json( $data );
	}

	public static function add_to_cart_multi() {
		ob_start();

		$products		 = WC_Product_Table_Cart_Handler::get_multi_cart_data();
		$cart_message	 = '';

		if ( $added = WC_Product_Table_Cart_Handler::add_to_cart_multi( $products ) ) {
			foreach ( $added as $product_id => $quantity ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			}

			// Return fragments
			$data = self::get_refreshed_fragments();

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wcpt_add_to_cart_message( $added, true );
			} else {
				$view_cart_link	 = sprintf( '<a href="%s" class="added_to_cart wc-forward">%s</a>', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'woocommerce-product-table' ) );
				$cart_message	 .= sprintf( '<p class="cart-success">%s</p>%s', wcpt_add_to_cart_message( $added, true, true ), $view_cart_link );
			}

			if ( $cart_message ) {
				$data['cart_message'] = $cart_message;
			}
		} else {
			// If there was an error adding to the cart
			$data = array(
				'error' => true,
				'error_message' => self::format_errors()
			);
		}

		wp_send_json( $data );
	}

	private static function format_errors() {

		$errors = wc_get_notices( 'error' );
		wc_clear_notices();

		if ( empty( $errors ) ) {
			$errors = array( __( 'There was an error adding to the cart. Please try again.', 'woocommerce-product-table' ) );
		}

		$error_message	 = '';
		$error_fmt		 = apply_filters( 'wc_product_table_cart_error_format', '<p class="cart-error">%s</p>' );

		if ( is_array( $errors ) ) {
			foreach ( $errors as $error ) {
				$error_message .= sprintf( $error_fmt, $error );
			}
		}

		return $error_message;
	}

	private static function get_refreshed_fragments() {
		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
				'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
		);

		return $data;
	}

	private static function col_search_term( $col ) {
		$term = $col['search']['value'];
		if ( $col['search']['regex'] ) {
			$term = str_replace( array( '(^|, )', '(, |$)', '\\' ), '', $term );
		}

		return $term;
	}

}

// class WC_Product_Table_Ajax_Handler
