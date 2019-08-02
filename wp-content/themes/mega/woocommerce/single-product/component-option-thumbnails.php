<?php
/**
 * Component Option Thumbnails Template
 * @version 2.5.0
 * @since  2.2.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

?><label class="select_label"><?php echo __( 'Select an option&hellip;', 'woocommerce-composite-products' ); ?></label>
<div id="component_option_thumbnails_<?php echo $component_id; ?>" class="component_option_thumbnails"><?php

	if ( $optional == 'yes' ) {
		?>
		<div class="component_option_thumbnail_container col-md-3">
			<div id="component_option_thumbnail_0" class="component_option_thumbnail component_option_thumbnail_none <?php echo $selected_value == '0' ? 'selected' : ''; ?>" data-val="0">
				<div class="image" title="<?php echo __( 'None', 'woocommerce-composite-products' ); ?>">
					<span></span>
				</div>
			</div>
			<div class="thumbnail_description">
				<label class="thumbnail_label">
					<?php echo __( 'None', 'woocommerce-composite-products' ); ?>
				</label>
			</div>
		</div><?php
	}

	foreach ( $component_options as $product_id ) {

		$composited_product = $product->get_composited_product( $component_id, $product_id );

		if ( ! $composited_product )
			continue;

		if ( $quantity_min == $quantity_max && $quantity_min > 1 )
			$quantity = ' &times; ' . $quantity_min;
		else
			$quantity = '';

		?><div class="component_option_thumbnail_container col-md-3">
			<div id="component_option_thumbnail_<?php echo $product_id; ?>" class="component_option_thumbnail <?php echo $selected_value == $product_id ? 'selected' : ''; ?>" data-val="<?php echo $product_id; ?>">
				<div class="image" title="<?php echo get_the_title( $product_id ) . $quantity . $product->get_composited_item_price_string( $component_id, $product_id ); ?>">
					<?php echo get_the_post_thumbnail( $product_id, apply_filters( 'woocommerce_composited_large_thumbnail_size', 'shop_thumbnail' ) ) ?>
					<?php //echo get_the_post_thumbnail( $product_id, 'list-thumb-shop' , array( 'class' => 'img-responsive' ) ) ?>
				</div>
			</div>
			<div class="thumbnail_description">
				<label class="thumbnail_label">
					<?php echo get_the_title( $product_id ) . $quantity; ?>
				</label>
				<p class="thumbnail_price price"><?php
					$product->add_composited_product_filters( $component_id, $product_id );
					echo $composited_product->get_price_html();
					$product->remove_composited_product_filters(); ?>
				</p>
			</div>
		</div><?php
	}
	?><div class="css_clear_component_options"></div>
</div>

