<?php
/**
 * Composite add-to-cart panel template
 * @version  2.5.0
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

global $woocommerce_composite_products;

?>
<div id="composite_form_<?php echo $product->id; ?>" class="cart composite_form <?php echo $navigation_style != 'progressive' ? 'multistep ' : ''; echo $added ? 'active' : ''; ?>" data-button_behaviour="<?php echo esc_attr( apply_filters( 'woocommerce_composite_button_behaviour', 'new', $product ) ); ?>" data-bto_selection_mode="<?php echo $selection_mode; ?>" data-bto_style="<?php echo $navigation_style; ?>" data-scenario_data="<?php echo esc_attr( json_encode( $product->get_composite_scenario_data() ) ); ?>" data-price_data="<?php echo esc_attr( json_encode( $product->get_composite_price_data() ) ); ?>" data-container-id="<?php echo $product->id; ?>" <?php echo $navigation_style == 'paged' ? 'style="display:none;"' : ''; ?>>

	<?php

	$components = $product->get_composite_data();

	if ( $navigation_style == 'paged' ) {

		?><div class="multipage_title"><?php

			$steps = count( $components ) + 1;

			wc_composite_get_template( 'single-product/component-title.php', array(
				'title' => sprintf( __( 'Step <span class="step">%d</span> of <span class="steps">%d</span> - Review' ), $steps, $steps )
			), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

		?></div>
		<div class="review">
			<dl><?php

			foreach ( $components as $component_id => $component_data ) {
				var_dump ($component_id);
				echo '<dt class="component_title_meta">' . apply_filters( 'woocommerce_composite_component_title', $component_data[ 'title' ] ) . ': </dt>';
				echo '<dd><span class="component_option_meta description_' . $component_id . '"></span>&nbsp;<strong class="component_quantity_meta quantity_' . $component_id . '"></strong></dd>';
				echo '<dd class="component_option_meta meta_' . $component_id . '"></dd>';
				echo '<dd class="component_option_price price_' . $component_id . '"></dd>';
			}

			?></dl>
		</div><?php
	}

	do_action('woocommerce_before_add_to_cart_button');

	?><div class="composite_wrap" style="<?php echo apply_filters( 'woocommerce_composite_button_behaviour', 'new', $product ) == 'new' ? '' : 'display:none'; ?>">
		<div class="composite_price"></div><?php

			// Availability
			$availability = $product->get_availability();

			if ( $availability[ 'availability' ] )
				echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . $availability[ 'class' ] . '">'.$availability[ 'availability' ] . '</p>', $availability[ 'availability' ] );

		?><div class="composite_button"><?php

			foreach ( $components as $component_id => $component_data ) {

				?><div class="form_data form_data_<?php echo $component_id; ?>">
					<input type="hidden" class="product_input" name="add-product-to-cart[<?php echo $component_id; ?>]" value="" />
					<input type="hidden" class="quantity_input" name="item_quantity[<?php echo $component_id; ?>]" value="" />
				</div><?php

			}

			if ( ! $product->is_sold_individually() )
				woocommerce_quantity_input( array ( 'min_value' => 1 ) );

			?><input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
			<button type="submit" class="single_add_to_cart_button composite_add_to_cart_button button alt"><?php echo apply_filters( 'single_add_to_cart_text', __( 'Add to cart', 'woocommerce' ), $product->product_type ); ?></button>
		</div>
	</div><?php

	do_action('woocommerce_after_add_to_cart_button');

?></div>

<?php do_action('woocommerce_after_add_to_cart_form');

if ( in_array( $navigation_style, array( 'paged', 'progressive' ) ) ) {

	?><div class="navigation navigation_<?php echo $product->id; echo $navigation_style != 'progressive' ? ' paged' : ' progressive'; ?>" data-container-id="<?php echo $product->id; ?>">
		<a class="page_button prev alt btn btn-danger" href="#button_prev"></a>
		<a class="page_button next alt btn btn-success" href="#button_next "></a>
	</div><?php
}
