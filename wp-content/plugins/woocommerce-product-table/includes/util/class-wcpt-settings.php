<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility functions for the product table plugin settings.
 *
 * @package   WooCommerce_Product_Table\Util
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class WCPT_Settings {
	/* Option names for our plugin settings (i.e. the option keys used in wp_options) */

	const OPTION_TABLE_STYLING	 = 'wcpt_table_styling';
	const OPTION_TABLE_DEFAULTS	 = 'wcpt_shortcode_defaults';
	const OPTION_MISC				 = 'wcpt_misc_settings';

	/* The section name within the main WooCommerce Settings */
	const SECTION_SLUG = 'product-table';

	//@todo: Change these to 'options' - e.g. get_option_table_styles, get_option_shortcode_defaults etc
	public static function get_setting_table_styling() {
		return self::get_setting( self::OPTION_TABLE_STYLING, array( 'use_theme' => 'theme' ) );
	}

	public static function get_setting_table_defaults() {
		return self::get_setting( self::OPTION_TABLE_DEFAULTS, array() );
	}

	public static function get_setting_misc() {
		$defaults = array(
			'cache_expiry' => 6,
			'quick_view_links' => false,
			'addons_layout' => 'block',
			'addons_option_layout' => 'inline'
		);

		return self::get_setting( self::OPTION_MISC, $defaults );
	}

	public static function settings_to_table_args( $settings ) {
		if ( empty( $settings ) ) {
			return $settings;
		}

		// Custom filter option
		if ( isset( $settings['filters'] ) && 'custom' === $settings['filters'] ) {
			if ( empty( $settings['filters_custom'] ) ) {
				$settings['filters'] = WC_Product_Table_Args::$default_args['filters'];
			} else {
				$settings['filters'] = $settings['filters_custom'];
			}
		}

		// Custom sort by option
		if ( isset( $settings['sort_by'] ) && 'custom' === $settings['sort_by'] ) {
			if ( empty( $settings['sort_by_custom'] ) ) {
				$settings['sort_by'] = WC_Product_Table_Args::$default_args['sort_by'];
			} else {
				$settings['sort_by'] = $settings['sort_by_custom'];
			}
		}

		// Unset settings that don't map to shortcode args
		unset( $settings['filters_custom'] );
		unset( $settings['sort_by_custom'] );
		unset( $settings['add_selected_text'] );

		// Check for empty settings
		foreach ( array( 'columns', 'image_size', 'links' ) as $arg ) {
			if ( empty( $settings[$arg] ) ) {
				$settings[$arg] = WC_Product_Table_Args::$default_args[$arg];
			}
		}

		// Ensure int settings are valid
		foreach ( array( 'rows_per_page', 'description_length', 'product_limit' ) as $arg ) {
			if ( isset( $settings[$arg] ) ) {
				$settings[$arg] = (int) $settings[$arg];

				if ( 0 === $settings[$arg] || $settings[$arg] < -1 ) {
					$settings[$arg] = WC_Product_Table_Args::$default_args[$arg];
				}
			}
		}

		return $settings;
	}

	public static function table_args_to_settings( $args ) {
		if ( empty( $args ) ) {
			return $args;
		}

		foreach ( $args as $key => $value ) {
			if ( is_bool( $value ) ) {
				$args[$key] = $value ? 'yes' : 'no';
			}
		}

		return $args;
	}

	public static function add_selected_to_cart_default_text() {
		return __( 'Add Selected To Cart', 'woocommerce-product-table' );
	}

	private static function array_map_yes_no_to_boolean( $val ) {
		if ( 'yes' === $val ) {
			return true;
		} elseif ( 'no' === $val ) {
			return false;
		}
		return $val;
	}

	private static function get_setting( $option_name, $default = array() ) {
		$option_value = get_option( $option_name, $default );

		if ( is_array( $option_value ) ) {
			// Merge with defaults.
			if ( is_array( $default ) ) {
				$option_value = wp_parse_args( $option_value, $default );
			}

			// Convert 'yes'/'no' options to booleans.
			$option_value = array_map( array( __CLASS__, 'array_map_yes_no_to_boolean' ), $option_value );
		}

		return $option_value;
	}

}
