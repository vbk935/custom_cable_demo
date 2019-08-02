<?php
/**
 * Composited Simple Product Template
 * @version  2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

global $woocommerce_composite_products;

if ( $hide_product_title != 'yes' )
	wc_composite_get_template( 'composited-product/title.php', array(
		'title'      => $product->get_title(),
		'product_id' => $product->id,
		'quantity'   => $quantity_min == $quantity_max && $quantity_min > 1 && $product->sold_individually !== 'yes' ? $quantity_min : ''
	), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

if ( $hide_product_thumbnail != 'yes' )
	wc_composite_get_template( 'composited-product/image.php', array(
		'product_id' => $product->id
	), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

?><div class="details component_data" data-component_set="true" data-price="<?php echo $data['price_data']['price']; ?>" data-regular_price="<?php echo $data['price_data']['regular_price']; ?>" data-product_type="simple"><?php

	if ( $hide_product_description != 'yes' )
		wc_composite_get_template( 'composited-product/excerpt.php', array(
			'product_description' => $product->post->post_excerpt,
			'product_id' => $product->id
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

	?><div class="component_wrap"><?php

			if ( $per_product_pricing == 'yes' && $product->get_price() !== '' )
				wc_composite_get_template( 'composited-product/price.php', array(
					'product' => $product
				), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

			// Add-ons
			do_action( 'woocommerce_composite_product_add_to_cart', $product->id, $component_id, $product );

			// Availability
			$availability = $woocommerce_composite_products->helpers->get_composited_item_availability( $product, $quantity_min );

			if ( $availability['availability'] ) {
				echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability[ 'class' ] ) . '">' . esc_html( $availability[ 'availability' ] ) . '</p>', $availability[ 'availability' ] );
		    }

			if ( $product->is_in_stock() ) {

				?><div class="quantity_button"><?php

			 		wc_composite_get_template( 'composited-product/quantity.php', array(

						'quantity_min' => $quantity_min,
						'quantity_max' => $quantity_max,
						'component_id' => $component_id,
						'product'      => $product
					), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

			 	?></div><?php
			}
	?></div>
</div>

