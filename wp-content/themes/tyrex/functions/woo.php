<?php


add_theme_support( 'woocommerce' );
//define( 'WOOCOMMERCE_USE_CSS', false );


// https://gist.github.com/woogist/6379275  
// Measurement Price Calculator sample code for adding a custom unit
add_filter( 'woocommerce_catalog_settings', 'add_woocommerce_dimension_unit_league' );
 /**
 * This adds the new unit to the WooCommerce admin
 */
function add_woocommerce_dimension_unit_league( $settings ) {
  foreach ( $settings as &$setting ) {
		if ( 'woocommerce_dimension_unit' == $setting['id'] ) {
			$setting['options']['league'] = __( 'League' );  // new unit
		}
	}
	return $settings;
}



//  https://gist.github.com/woogist/6380158
//  WooCommerce Measurement Price Calculator custom unit normalize
add_filter( 'wc_measurement_price_calculator_normalize_table', 'wc_measurement_price_calculator_normalize_table' );
/**
 * Add conversions for any custom units to the appropriate standard unit
 */
function wc_measurement_price_calculator_normalize_table( $normalize_table ) {
	// 1 league = 18,228.3465 ft
	$normalize_table['league'] = array( 'factor' => 18228.3465, 'unit' => 'ft' );	
	return $normalize_table;
}


//https://gist.github.com/woogist/6380491
//  WooCommerce Measurement Price Calculator custom unit convert
add_filter( 'wc_measurement_price_calculator_conversion_table', 'wc_measurement_price_calculator_conversion_table' );
/**
 * This converts standard units to all other compatible units
 */
function wc_measurement_price_calculator_conversion_table( $conversion_table ) {
	// 1 ft = 1/18228.3465 leagues
	$conversion_table['ft']['league'] = array( 'factor' => 18228.3465, 'inverse' => true );
	// 1 m = 0.000179985601 leagues
	$conversion_table['m']['league'] = array( 'factor' => 0.000179985601 );
	return $conversion_table;
}
  ?>