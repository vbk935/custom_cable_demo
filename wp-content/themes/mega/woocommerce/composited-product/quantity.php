<?php
/**
 * Composited Product Quantity
 * @version  4.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( $quantity_min == $quantity_max ) {

	if ( $quantity_min == 1 ) {

			?><div class="quantity" style="display:none;"><input class="qty" type="hidden" name="quantity" value="1" /></div><?php

		} else {

			?><div class="quantity" style="display:none;"><input class="qty" type="hidden" name="quantity" value="<?php echo $quantity_min; ?>" /></div><?php
		}

} else {

 	woocommerce_quantity_input( array(
 		'min_value'   => $quantity_min,
		'max_value'   => $quantity_max,
 		'input_value' => isset( $_POST[ 'quantity_' . $component_id ] ) ? $_POST[ 'quantity_' . $component_id ] : max( $quantity_min, 1 )
 	), $product );

}
