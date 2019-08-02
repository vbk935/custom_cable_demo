<?php
/**
 * Composited Variable Product Template
 * @version  2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

global $woocommerce, $woocommerce_composite_products;

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

?><div class="details component_data" data-component_set="" data-price="0" data-regular_price="0" data-product_type="variable" data-product_variations="<?php echo esc_attr( json_encode( $data['product_variations'] ) ); ?>"><?php

	if ( $hide_product_description != 'yes' )
		wc_composite_get_template( 'composited-product/excerpt.php', array(
			'product_description' => $product->post->post_excerpt,
			'product_id' => $product->id
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

	$attributes				= $product->get_variation_attributes();
	$selected_attributes	= $product->get_variation_default_attributes();

	?><table class="variations" cellspacing="0">
		<tbody><?php
		$loop = 0;
		foreach ( $attributes as $name => $options ) {
			$loop++;
			?><tr class="attribute-options">
				<td class="label">
					<label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_composite_attribute_label( $name ); ?> <abbr class="required" title="required">*</abbr></label>
				</td>
				<td class="value">
					<select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>">
						<option value=""><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option>
						<?php
						if ( is_array( $options ) ) {

							$selected_value = '';

							if ( isset( $_POST[ 'bto_attribute_' . sanitize_title( $name ) ][ $component_id ] ) && $_POST[ 'bto_attribute_' . sanitize_title( $name ) ][ $component_id ] !== '' )
								$selected_value = $_POST[ 'bto_attribute_' . sanitize_title( $name ) ][ $component_id ];
							else
								$selected_value = ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) ? $selected_attributes[ sanitize_title( $name ) ] : '';

							// Get terms if this is a taxonomy - ordered
							if ( taxonomy_exists( sanitize_title( $name ) ) ) {

								$orderby = wc_composite_attribute_order_by( sanitize_title( $name ) );

								switch ( $orderby ) {
									case 'name' :
										$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
									break;
									case 'id' :
										$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false, 'hide_empty' => false );
									break;
									case 'menu_order' :
										$args = array( 'menu_order' => 'ASC', 'hide_empty' => false );
									break;
								}

								$terms = get_terms( sanitize_title( $name ), $args );

								foreach ( $terms as $term ) {
									if ( ! in_array( $term->slug, $options ) )
										continue;

									echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $selected_value, $term->slug, false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
								}
							} else {

								foreach ( $options as $option ) {
									echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
								}
							}
						}
					?></select><?php
					if ( sizeof( $attributes ) == $loop ) {
						echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'woocommerce' ) . '</a>';
					}
				?></td>
			</tr><?php
			}
		?></tbody>
	</table><?php

	// Add-ons
	do_action( 'woocommerce_composite_product_add_to_cart', $product->id, $component_id, $product );

	?><div class="single_variation_wrap component_wrap" style="display:none;">

		<div class="single_variation"></div>
		<div class="variations_button">
			<input type="hidden" name="variation_id" value="" /><?php

		 		wc_composite_get_template( 'composited-product/quantity.php', array(
					'quantity_min' => $quantity_min,
					'quantity_max' => $quantity_max,
					'component_id' => $component_id,
					'product'      => $product
				), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

		 ?></div>
	</div>
</div>
