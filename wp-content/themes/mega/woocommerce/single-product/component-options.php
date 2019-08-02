<?php
/**
 * Component Options Drop-Down Template
 * @version 2.5.0
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

global $woocommerce_composite_products;

?><div class="component_options" style="<?php echo $is_singular ? 'display:none;' : ''; ?>"><?php

	$component_options = apply_filters( 'woocommerce_composite_component_options_display', $component_options, $component_id, $product );

	if ( $selection_mode == 'thumbnails' )
		wc_composite_get_template( 'single-product/component-option-thumbnails.php', array(
			'product'           => $product,
			'component_id'      => $component_id,
			'title'             => $title,
			'quantity_min'      => $quantity_min,
			'quantity_max'      => $quantity_max,
			'component_options' => $component_options,
			'optional'          => $optional,
			'selected_value'    => $selected_value,
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

	?><select id="component_options_<?php echo $component_id; ?>" class="component_options_select" name="bto_selection_<?php echo $component_id; ?>" style="<?php echo $selection_mode == 'thumbnails' ? 'display:none;' : ''; ?>"><?php

		if ( ! $is_singular ) {
			?><option class="empty" value=""><?php echo __( 'Select an option&hellip;', 'woocommerce-composite-products' ); ?></option><?php
		}

		if ( $optional == 'yes' ) {
			?><option class="none" data-title="<?php echo __( 'None', 'woocommerce-composite-products' ); ?>" value="0" <?php echo selected( $selected_value, '0', false ); ?>><?php echo __( 'None', 'woocommerce-composite-products' ); ?></option><?php
		}

		foreach ( $component_options as $product_id ) {

			$composited_product = $product->get_composited_product( $component_id, $product_id );

			if ( ! $composited_product )
				continue;

			?><option data-title="<?php echo get_the_title( $product_id ); ?>" value="<?php echo $product_id; ?>" <?php echo selected( $selected_value, $product_id, false ); ?>><?php

				if ( $quantity_min == $quantity_max && $quantity_min > 1 )
					$quantity = ' &times; ' . $quantity_min;
				else
					$quantity = '';

				echo get_the_title( $product_id ) . $quantity;

				echo $product->get_composited_item_price_string( $component_id, $product_id );

			?></option><?php
		}
	?></select>
	<a class="clear_component_options btn btn-warning" style="visibility=hidden;" href="#clear_component"><?php
		echo __( 'Clear selection', 'woocommerce-composite-products' );
	?></a>
</div>
