<?php
/**
 * Composite Product Template
 * @version 2.5.0
 * @since  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_composite_products;

?>
<form method="post" enctype='multipart/form-data' ><?php

$loop 	= 0;
$steps 	= count( $components );
$added 	= true;

foreach ( $components as $component_id => $component_data ) {
	if ( ! isset( $_POST[ 'add-product-to-cart' ][ $component_id ] ) || $_POST[ 'add-product-to-cart' ][ $component_id ] === '' )
		$added = false;
}

foreach ( $components as $component_id => $component_data ) {

	$loop++;

	// Default Component Option
	if ( isset( $_POST[ 'add-product-to-cart' ][ $component_id ] ) && $_POST[ 'add-product-to-cart' ][ $component_id ] !== '' )
		$selected_value = $_POST[ 'add-product-to-cart' ][ $component_id ];
	elseif ( $component_data[ 'optional' ] != 'yes' && count( $component_data[ 'assigned_ids' ] ) == 1 )
		$selected_value = $component_data[ 'assigned_ids' ][0];
	else
		$selected_value = isset( $component_data[ 'default_id' ] ) ? $component_data[ 'default_id' ] : '';

	if ( $selected_value !== '' )
		$selected_product_data = $woocommerce_composite_products->helpers->show_composited_product( $selected_value, $component_id, $product );

	if ( $navigation_style == 'single' || $navigation_style == 'progressive' ) {


		wc_composite_get_template( 'single-product/component-single-page.php', array(
			'component_id'            => $component_id,
			'component_data'          => $component_data,
			'step'                    => $loop,
			'steps'                   => $steps,
			'selection_mode'          => $selection_mode,
			'navigation_style'        => $navigation_style,
			'selected_value'          => $selected_value,
			'selected_product_markup' => $selected_value !== '' ? $selected_product_data : '',
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

	} else {

		wc_composite_get_template( 'single-product/component-multi-page.php', array(
			'component_id'            => $component_id,
			'component_data'          => $component_data,
			'step'                    => $loop,
			'steps'                   => $steps,
			'added'                   => $added,
			'selection_mode'          => $selection_mode,
			'navigation_style'        => $navigation_style,
			'selected_value'          => $selected_value,
			'selected_product_markup' => $selected_value !== '' ? $selected_product_data : '',
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

	}
}

wc_composite_get_template( 'single-product/composite-add-to-cart.php', array(
	'product'          => $product,
	'added'            => $added,
	'navigation_style' => $navigation_style,
	'selection_mode'   => $selection_mode
), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

?>
</form>
