<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides functions for compatibility and integration with different themes.
 *
 * @package   WooCommerce_Product_Table\Compat
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WCPT_Theme_Compat {

	public static function register_theme_compat_hooks() {
		$theme = strtolower( get_template() );

		if ( in_array( $theme, array( 'jupiter', 'salient' ) ) || apply_filters( 'wc_product_table_enable_side_quantity_buttons', false ) ) {
			add_filter( 'wc_product_table_add_to_cart_class', array( __CLASS__, 'quantity_buttons_on_side_class' ) );
		} elseif ( 'uncode' === $theme ) {
			add_filter( 'add_to_cart_class', array( __CLASS__, 'uncode_child_add_to_cart_class' ) );
		} elseif ( 'kallyas' === $theme ) {
			add_filter( 'add_to_cart_fragments', array( __CLASS__, 'kallyas_ensure_valid_add_to_cart_fragments' ), 20 );
		} elseif ( 'x' === $theme ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'x_remove_legacy_mediaelement_styles' ) );
		}

		if ( function_exists( 'wp_add_inline_script' ) && in_array( $theme, array( 'avada', 'enfold', 'flatsome', 'jupiter', 'salient', 'xstore' ) ) ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, $theme . '_inline_script' ), 50 );
		}
	}

	public static function quantity_buttons_on_side_class( $classes ) {
		$classes[] = 'plus-minus-on-side';
		return $classes;
	}

	public static function kallyas_ensure_valid_add_to_cart_fragments( $fragments ) {
		if ( ! isset( $fragments['zn_added_to_cart'] ) ) {
			$fragments['zn_added_to_cart'] = '';
		}
		return $fragments;
	}

	public static function uncode_child_add_to_cart_class( $class ) {
		return $class . ' single_add_to_cart_button';
	}

	public static function x_remove_legacy_mediaelement_styles() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_dequeue_style( 'wp-mediaelement' );
		wp_deregister_style( 'wp-mediaelement' );
		wp_register_style( 'wp-mediaelement', "/wp-includes/js/mediaelement/wp-mediaelement$suffix.css" );
	}

	public static function avada_inline_script() {
		$script = "
			( function( $ ) {
				// Remove Avada styling for select elements in product tables
				$( '.wc-product-table' ).on( 'load.wcpt', function() {
					$( this ).find( '.avada-select-parent' ).children( 'select' ).unwrap().siblings( '.select-arrow' ).remove();
				} );
				$( '.wc-product-table' ).on( 'responsive-display.wcpt draw.wcpt', function() {
					if ( !$( this ).hasClass( 'loading' ) && typeof avadaAddQuantityBoxes === 'function' ) {
						avadaAddQuantityBoxes();
					}
				});
			})( jQuery );";
		wp_add_inline_script( WC_Product_Table_Frontend_Scripts::SCRIPT_HANDLE, trim( $script ) );
	}

	public static function enfold_inline_script() {
		$script = "
			( function( $ ) {
				var wcptOnQuantityButtonClick = function( event ) {
					// Prevent any other handlers changing the quantity
					event.stopImmediatePropagation();

					var clicked = $( this );
					var isMinus = clicked.is( '.minus' );

					clicked.closest( '.quantity' ).find( '.qty' ).val( function( i, value ) {
						var qty = $( this );
						value = parseFloat( value );
						var step = parseFloat( qty.prop( 'step' ) ), min = parseFloat( qty.prop( 'min' ) ), max = parseFloat( qty.prop( 'max' ) );

						value = !isNaN( value ) ? value : 1;
						step = !isNaN( step ) ? step : 1;
						min = !isNaN( min ) ? min : 1;
						max = !isNaN( max ) ? max : 9999;

						step = isMinus ? -1 * step : step;
						value = value + step;

						if ( isMinus ) {
							return Math.max( value, min );
						} else {
							return Math.min( value, max );
						}
					} ).trigger( 'change' );
				};

				var wcptOnDrawAddQuantityButtons = function( event ) {
					$( this ).find( '.cart div.quantity:not(.buttons_added)' )
						.addClass( 'buttons_added' )
						.children( '.qty' )
						.before( '<input type=\"button\" value=\"-\" class=\"minus\">' )
						.after( '<input type=\"button\" value=\"+\" class=\"plus\">' );
				};

				$( '.wc-product-table' )
					.on( 'click', '.quantity .plus, .quantity .minus', wcptOnQuantityButtonClick )
					.on( 'responsive-display.wcpt draw.wcpt', wcptOnDrawAddQuantityButtons );
			})( jQuery );";

		wp_add_inline_script( WC_Product_Table_Frontend_Scripts::SCRIPT_HANDLE, trim( $script ) );
	}

	public static function flatsome_inline_script() {
		$script = "
			( function( $ ) {
				$( '.wc-product-table' ).on( 'responsive-display.wcpt draw.wcpt', function() {
					if ( $.fn.addQty ) {
						$( this )
							.find( '.cart .quantity' )
							.remove( '.plus', '.minus' )
							.addQty();
					}
				});
			})( jQuery );";
		wp_add_inline_script( WC_Product_Table_Frontend_Scripts::SCRIPT_HANDLE, trim( $script ) );
	}

	public static function jupiter_inline_script() {
		$script = "
			( function( $ ) {
				$( '.wc-product-table' ).on( 'responsive-display.wcpt', function( e, table, childRow ) {
					childRow.find( 'div.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type=\"button\" value=\"+\" class=\"plus\" />' ).prepend( '<input type=\"button\" value=\"-\" class=\"minus\" />' );
				});
			})( jQuery );";
		wp_add_inline_script( WC_Product_Table_Frontend_Scripts::SCRIPT_HANDLE, trim( $script ) );
	}

	public static function salient_inline_script() {
		$script = "
			( function( $ ) {
				$( '.wc-product-table' ).on( 'responsive-display.wcpt draw.wcpt', function() {
					$( '.wc-product-table div.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type=\"button\" value=\"+\" class=\"plus\" />' ).prepend( '<input type=\"button\" value=\"-\" class=\"minus\" />' );
				});
			})( jQuery );";
		wp_add_inline_script( WC_Product_Table_Frontend_Scripts::SCRIPT_HANDLE, trim( $script ) );
	}

	public static function xstore_inline_script() {
		$script = "
			( function( $ ) {
				$( '.wc-product-table' ).on( 'draw.wcpt', function() {
					$( this ).find( 'div.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<span value=\"+\" class=\"plus\" ></span>' ).prepend( '<span value=\"-\" class=\"minus\"></span>' );
				});
			})( jQuery );";
		wp_add_inline_script( WC_Product_Table_Frontend_Scripts::SCRIPT_HANDLE, trim( $script ) );
	}
}
