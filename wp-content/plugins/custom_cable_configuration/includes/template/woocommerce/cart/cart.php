<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>
<div class="cart-tableDiv">
<table class="shop_table shop_table_responsive cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<!--<th class="product-thumbnail">&nbsp;</th>-->
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<!--<th class="product-description"><?php _e( 'Description', 'woocommerce' ); ?></th>-->
			<th class="product-partnumber"><?php _e( 'Part Number', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		$foo = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$curr_part_number = $cart_item['custom_fields']['part_number'];
			$products_additional_details = $cart_item['custom_products_additional_details'];

			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-remove">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
								esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								__( 'Remove this item', 'woocommerce' ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() )
							), $cart_item_key );
						?>
					</td>

					<!--<td class="product-thumbnail">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
							}
						?>
					</td>-->

					<td class="product-name" data-title="<?php _e( 'Product', 'woocommerce' ); ?>">
						<?php
							if ( ! $product_permalink ) {
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							} else {
								
								if ($product_id == 2164)
										echo sprintf( '<a href="%s" class="%s">%s</a>', 'javascript:void(0)','productPopup_'.$foo, $_product->get_title()); 
								else
										echo sprintf( '<a href="%s" class="%s">%s</a>', get_permalink( $product_id ) ,'', $_product->get_title());
							}
							echo wc_get_formatted_cart_item_data( $cart_item );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
							}
							$product_id = $cart_item['product_id'];
							//$get_configurable = get_post_meta( $product_id, 'configuration_combination', true );
							$get_configurable = "";
							if(!empty($get_configurable))
							{								
								//print_r($cart_item['wdm_user_custom_data_value']['part_number']);
								//print_r($cart_item['wdm_user_custom_data_value']['value_input']);
						?>
						<a href="javascript:void(0)" data-toggle="modal" data-target="#myModal_<?php echo $cart_item['product_id']; ?>">View Configuration</a>						
						<?php } ?>
						<div class="modal fade" id="myModal_<?php echo $cart_item['product_id']; ?>" role="dialog">
				<div class="modal-dialog">            
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><?php echo $_product->get_title(); ?></h4>
						</div>
						<div class="modal-body">
							<table class="shop_attributes">
								<tbody>
									<?php	
										if(!empty($get_configurable))
										{
											foreach($cart_item['wdm_user_custom_data_value']['part_number'] as $key=>$part_number)
											{				            	
												if($key != 0)
												{
													$main_term = get_term_by('id', $key, 'configuration');              
													$main_term_meta = get_option("taxonomy_term_$key");
													$sub_term = get_term_by('id', $part_number, 'configuration');           
													$subterm_meta = get_option("taxonomy_term_$part_number");                           
													if($subterm_meta['presenter_id'] == "changable")
													{
														echo "<tr><th>".$main_term->name ."</th><td class='product_weight'>".$cart_item['wdm_user_custom_data_value']['value_input']."</td></tr>";
													} 
													else if($main_term_meta['hide_config'] != 1)
													{
														echo "<tr><th>".$main_term->name . "</th><td class='product_weight'>" . $sub_term->name."</td></tr>";                        
													}
												}
											}
										} 
									?>
								</tbody>
							</table>
						</div>	              
					</div>	             
				</div>
			</div>
					</td>
					<!--<td class="product-description" data-title="<?php _e( 'Product', 'woocommerce' ); ?>">
						<?php
							$item = $cart_item['data'];
							echo $item->post->post_excerpt?$item->post->post_excerpt:'Not Applicable';
						?>
					</td>-->
					<td class="product-partnumber" data-title="<?php _e( 'Part Number', 'woocommerce' ); ?>">
							<!-- Part Number -->
						<?= sprintf( '<a href="%s" class="%s">%s</a>', 'javascript:void(0)','productPopup_'.$foo,$curr_part_number ); ?>

						
					</td>
					<td class="product-price" data-title="<?php _e( 'Price', 'woocommerce' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>

					<td class="product-quantity" data-title="<?php _e( 'Quantity', 'woocommerce' ); ?>">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
					</td>

					<td class="product-subtotal" data-title="<?php _e( 'Total', 'woocommerce' ); ?>">
						<?php

							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
				</tr>
				<!-- Product Details Modal-->
				<div id="productPopupModal" class="modal fade productPopupModal_<?php echo $foo; ?> configurationsummary" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Configuration Summary</h4>
							</div>
							<div class="modal-body">
								<div class="custom-content"></div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>

					</div>
				</div>
				<!-- Product Details Modal-->

				<script type="text/javascript">
					var foo = '<?php echo $foo; ?>';
					( function(){
						$(".productPopup_"+foo).click(function(){
							$('.productPopupModal_'+foo).find('.custom-content').empty();
							var products_additional_details = '<?php echo $products_additional_details ?>';
							htmlToAdd = '';
							var productDetails = JSON.parse(products_additional_details);
							$.each(productDetails, function (i, value) {

								var label = value.configName;
								var value =  value.cguiComponentName;

								htmlToAdd += '<div class="outer-dv"><div class="left-sec">'+ label +'</div><div class="right-sec">'+ value +'</div></div>';
							});

							/* Get and display the part number in popup */

							var partNumber = '<?php echo $curr_part_number; ?>';

							htmlToAdd += '<div class="outer-dv"><div class="left-sec">Part Number</div> <div class="right-sec">'+ partNumber +'</div></div>';


							/* PRICE */

							var price =  '<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>';

							htmlToAdd += '<div class="outer-dv price-dv"><div class="left-sec">Unit Price</div> <div class="right-sec">'+ price +'</div></div>';

							$('.productPopupModal_'+foo).find('.custom-content').html(htmlToAdd);

							$('.productPopupModal_'+foo).modal('show');
							return true;

						});

					}());
				</script>
				<?php
			}
			$foo++;
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="6" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">

						<label for="coupon_code"><?php _e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<input type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update Cart', 'woocommerce' ); ?>" />

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>
</div>
<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<div class="cart-collaterals">

	<?php do_action( 'woocommerce_cart_collaterals' ); ?>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

<!-- Include the popup js file -->
<?php //wp_enqueue_script( 'script', plugins_url() . '/custom_cable_configuration/js/popup.js', array ( 'jquery' ), 1.1, true); ?>


