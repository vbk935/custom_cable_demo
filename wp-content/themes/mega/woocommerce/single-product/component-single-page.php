<?php
/**
 * Single Page Component Template
 * @version 2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

global $product, $woocommerce_composite_products;

?>
<div class="component product <?php echo $navigation_style == 'progressive' ? 'multistep progressive ' : ''; echo $step == 1 ? 'active first ' : ( $navigation_style == 'progressive' ? 'blocked ' : '' ); echo $step == $steps ? 'last' : ''; ?>" data-nav_title="<?php echo esc_attr( apply_filters( 'woocommerce_composite_component_title', $component_data[ 'title' ] ) ); ?>" data-item-id="<?php echo $component_id; ?>" data-container-id="<?php echo $product->id; ?>">

	<?php if ( $navigation_style == 'progressive' ) echo '<div class="block_component"></div>'; ?>

	<div class="component_title_description"><?php

		wc_composite_get_template( 'single-product/component-title.php', array(
			'title' => apply_filters( 'woocommerce_composite_component_title', $component_data[ 'title' ] )
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

		if ( $component_data[ 'description' ] != '' )
			wc_composite_get_template('single-product/component-description.php', array(
				'description' => apply_filters( 'woocommerce_composite_component_description', $component_data[ 'description' ] )
			), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

	?></div>
	<div class="component_selections"><?php

		wc_composite_get_template( 'single-product/component-options.php', array(
			'product'             => $product,
			'component_id'        => $component_id,
			'title'               => $component_data[ 'title' ],
			'quantity_min'        => $component_data[ 'quantity_min' ],
			'quantity_max'        => $component_data[ 'quantity_max' ],
			'component_options'   => $component_data[ 'assigned_ids' ],
			'optional'            => $component_data[ 'optional' ],
			'is_singular'         => $component_data[ 'optional' ] != 'yes' && count( $component_data[ 'assigned_ids' ] ) == 1,
			'selected_value'      => $selected_value,
			'per_product_pricing' => $product->per_product_pricing,
			'selection_mode'      => $selection_mode
		), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

		?><div class="component_content" data-product_id="<?php echo $component_id; ?>">
			<div class="component_summary">
				<div class="product content"><?php

					echo $selected_product_markup;

				?></div>
			</div>
		</div>
	</div>
</div>
<?php

?>
