<?php

/**
 * Provides backwards compatibility functions for older versions of WordPress and WooCommerce.
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

if ( ! function_exists( 'wcpt_back_compat_args' ) ) {

	/**
	 * Maintain support for old attribute names.
	 *
	 * @param array $args The array of product table attributes
	 * @return array The updated attributes with old ones replaced with their new equivalent
	 */
	function wcpt_back_compat_args( $args ) {

		if ( empty( $args ) ) {
			return $args;
		}

		$compat = array(
			'add-to-cart' => 'cart_button',
			'add_to_cart' => 'cart_button',
			'display_page_length' => 'page_length',
			'display_totals' => 'totals',
			'display_pagination' => 'pagination',
			'display_search_box' => 'search_box',
			'display_reset_button' => 'reset_button',
			'show_quantities' => 'show_quantity'
		);

		foreach ( $compat as $old => $new ) {
			if ( isset( $args[$old] ) ) {
				$args[$new] = $args[$old];
				unset( $args[$old] );
			}
		}

		return $args;
	}

}
// WP < 4.2
if ( ! function_exists( 'wp_scripts' ) ) {

	function wp_scripts() {
		global $wp_scripts;
		if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
			$wp_scripts = new WP_Scripts();
		}
		return $wp_scripts;
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_parent' ) ) {

	function wcpt_get_parent( $product ) {
		$parent = false;

		if ( method_exists( $product, 'get_parent_id' ) && ( $parent_id = $product->get_parent_id() ) ) {
			$parent = wc_get_product( $parent_id );
		} elseif ( property_exists( $product, 'parent' ) ) {
			$parent = $product->parent;
		}
		return $parent ? $parent : false;
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_parent_id' ) ) {

	function wcpt_get_parent_id( $product ) {
		$parent_id = false;

		if ( method_exists( $product, 'get_parent_id' ) ) {
			$parent_id = $product->get_parent_id();
		} elseif ( method_exists( $product, 'get_parent' ) ) {
			$parent_id = $product->get_parent();
		}
		return $parent_id;
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_name' ) ) {

	function wcpt_get_name( $product ) {
		if ( ! $product ) {
			return '';
		}
		$name = '';

		if ( method_exists( $product, 'get_name' ) ) {
			$name = $product->get_name();
		} elseif ( method_exists( $product, 'get_title' ) ) {
			$name = $product->get_title();
		} else {
			$name = get_the_title( wcpt_get_id( $product ) );
		}

		if ( 'variation' === $product->get_type() ) {
			// Get the name of the parent product
			$parent_name = wcpt_get_name( wcpt_get_parent( $product ) );

			// Name contains attributes if the parent name is different to the variation's name
			$contains_attributes = $name !== $parent_name;

			// Older versions of WC used 'Variation #1234 of...' as the product name, so use parent instead
			if ( stristr( $name, 'Variation' ) ) {
				$name				 = $parent_name;
				$contains_attributes = false;
			}

			if ( ! $contains_attributes ) {
				$name	 = $product->get_title();
				$labels	 = array();

				foreach ( $product->get_variation_attributes() as $atttribute_name => $value ) {
					if ( ! $value ) {
						continue;
					}

					$atttribute_name = str_replace( 'attribute_', '', $atttribute_name );

					// If this is a term slug, get the term's nice name
					if ( taxonomy_exists( $atttribute_name ) ) {
						$term = get_term_by( 'slug', $value, $atttribute_name );
						if ( ! is_wp_error( $term ) && ! empty( $term->name ) ) {
							$value = $term->name;
						}
					} else {
						$value = ucwords( str_replace( '-', ' ', $value ) );
					}

					$labels[] = rawurldecode( $value );
				}

				if ( $labels ) {
					$name .= ' - ' . implode( ', ', $labels );
				}
			}
		}

		return $name;
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_description' ) ) {

	function wcpt_get_description( $product ) {
		if ( method_exists( $product, 'get_description' ) ) {
			return $product->get_description();
		} else {
			$post = WCPT_Util::get_post( $product );
			return $post->post_content;
		}
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_dimensions' ) ) {

	function wcpt_get_dimensions( $product ) {
		if ( function_exists( 'wc_format_dimensions' ) ) {
			return wc_format_dimensions( $product->get_dimensions( false ) );
		}
		return $product->get_dimensions();
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_stock_status' ) ) {

	function wcpt_get_stock_status( $product ) {
		if ( method_exists( $product, 'get_stock_status' ) ) {
			return $product->get_stock_status();
		}
		return $product->stock_status;
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_min_purchase_quantity' ) ) {

	function wcpt_get_min_purchase_quantity( $product ) {
		if ( method_exists( $product, 'get_min_purchase_quantity' ) ) {
			return $product->get_min_purchase_quantity();
		}
		return 1;
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_get_max_purchase_quantity' ) ) {

	function wcpt_get_max_purchase_quantity( $product ) {
		return $product->backorders_allowed() ? '' : $product->get_stock_quantity();
	}

}

// WC < 3.0
if ( ! function_exists( 'wcpt_woocommerce_quantity_input' ) ) {

	function wcpt_woocommerce_quantity_input( $product ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			if ( ! $product->is_sold_individually() ) {
				woocommerce_quantity_input( array(
					'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
					'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
					'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
				) );
			}
		} else {
			woocommerce_quantity_input( array(
				'min_value' => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity()
			) );
		}
	}

}

// WC < 2.6.5
if ( ! function_exists( 'wcpt_add_to_cart_message' ) ) {

	function wcpt_add_to_cart_message( $added, $show_qty = false, $return = false ) {
		if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
			// 1 arg - need to pass the product IDs as array values in WC < 2.6
			wc_add_to_cart_message( array_keys( $added ) );
		} elseif ( version_compare( WC_VERSION, '2.6.5', '<' ) ) {
			// 2 args
			wc_add_to_cart_message( $added, $show_qty );
		} else {
			// 3 args
			wc_add_to_cart_message( $added, $show_qty, false );
		}

		if ( $return ) {
			$message = implode( ' ', wc_get_notices( 'success' ) );
			wc_clear_notices();
			return $message;
		}
	}

}

// WC < 2.6
if ( ! function_exists( 'wcpt_get_layered_nav_chosen_attributes' ) ) {

	function wcpt_get_layered_nav_chosen_attributes() {
		global $_chosen_attributes;

		return method_exists( 'WC_Query', 'get_layered_nav_chosen_attributes' ) ? WC_Query::get_layered_nav_chosen_attributes() : ( $_chosen_attributes ? $_chosen_attributes : array() );
	}

}

// WC < 2.5
if ( ! function_exists( 'wcpt_get_id' ) ) {

	function wcpt_get_id( $product ) {
		if ( method_exists( $product, 'get_id' ) ) {
			return $product->get_id();
		}
		return $product->id;
	}

}

// WC < 2.5
if ( ! function_exists( 'wcpt_product_supports' ) ) {

	function wcpt_product_supports( $product, $feature ) {
		return method_exists( $product, 'supports' ) && $product->supports( $feature );
	}

}
