<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
	<td class="product-name">
		<?php
			$is_visible        = $product && $product->is_visible();
			$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

			//echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'], $item, $is_visible );
			
			echo sprintf( '<a href="%s" class="%s">%s</a>', 'javascript:void(0)','productPopupOrder_'.$foo,$item['name']); 

			echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

			$product_id = $product->id;
			$get_configurable = get_post_meta( $product_id, 'configuration_combination', true );

			if(!empty($get_configurable))
			{
		?>
				<br>
				<a href="javascript:void;" data-toggle="modal" data-target="#myModal_<?php echo $item_id; ?>">View Configuration</a>		
		<?php
			}
			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );
			
			$part_number = wc_get_order_item_meta( $item_id, 'Part Number', true );

			$qty = wc_get_order_item_meta( $item_id, '_qty', true );

			$total = wc_get_order_item_meta( $item_id, '_line_total', true )/$qty;

			$product_details = wc_get_order_item_meta( $item_id, 'Products Details', true );

			echo sprintf( '<br><strong>Part Number: </strong><a href="%s" class="%s">%s</a>', 'javascript:void(0)','productPopupOrder_'.$foo,$part_number );

			
		
			$order->display_item_downloads( $item );
		
			// $item_part_number = unserialize($item['part_number']);			
			?>
			<div class="modal fade" id="myModal_<?php echo $item_id; ?>" role="dialog">
				<div class="modal-dialog">            
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><?php echo $item['name']; ?></h4>
						</div>
						<div class="modal-body">
							<table class="shop_attributes">
								<tbody>
									<?php	
										if(!empty($get_configurable))
										{
											foreach($item_part_number['part_number'] as $key=>$part_number)
											{				            	
												if($key != 0)
												{
													$main_term = get_term_by('id', $key, 'configuration');              
													$main_term_meta = get_option("taxonomy_term_$key");
													$sub_term = get_term_by('id', $part_number, 'configuration');           
													$subterm_meta = get_option("taxonomy_term_$part_number");                           
													if($subterm_meta['presenter_id'] == "changable")
													{
														echo "<tr><th>".$main_term->name ."</th><td class='product_weight'>". $item_part_number['value_input']."</td></tr>";
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

        <?php
			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
		?>
	</td>
	<td class="hidden">
		<?php 
			if(!empty($get_configurable))
			{	
				$order_metadata = wc_get_order_item_meta($item_id,'part_number');				
				foreach($order_metadata['part_number'] as $val)
				{
					if($val == "-")
					{
						$part_no[] = "-";
					}
					else 
					{
						$subterm_meta_val = get_option("taxonomy_term_$val");
						
						if($subterm_meta_val['presenter_id'] == "changable")
						{
							$part_no[] = $order_metadata['value_input'];
						}
						else 
						{				
							$part_no[] = $subterm_meta_val['unit_name'];		
						}
					}
				}	
				$partno = implode("",$part_no);
				echo $partno;
			}
			else 
			{
				echo "Not Applicable";
			}
		?>
	</td>
	<td class="product-total">
		<?php echo $order->get_formatted_line_subtotal( $item ); ?>
	</td>
</tr>
<?php if ( $show_purchase_note && $purchase_note ) : ?>
<tr class="product-purchase-note">
	<td colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
</tr>
<?php endif; ?>
<!-- Product Details Modal-->
<div id="productPopupModalOrder" class="modal fade productPopupModalOrder_<?php echo $foo; ?> configurationsummary" role="dialog">
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
		var foo = '<?php echo $foo ?>';
	$(".productPopupOrder_"+foo).click(function(){
		$('.productPopupModalOrder_'+foo).find('.custom-content').empty();
   			var product_details = '<?php echo $product_details ?>';
   			
   			htmlToAdd = '';
   			var productDetails = JSON.parse(product_details);
   			$.each(productDetails, function (i, value) {

   				var label = value.configName;
   				var value =  value.cguiComponentName;

   				htmlToAdd += '<div class="outer-dv"><div class="left-sec">'+ label +'</div><div class="right-sec">'+ value +'</div></div>';
   			});
   			var partNumber = '<?php echo $part_number; ?>';

   			htmlToAdd += '<div class="outer-dv"><div class="left-sec">Part Number</div> <div class="right-sec">'+ partNumber +'</div></div>';


   			/* PRICE */

   			var price = '<?php echo '$'.$total; ?>';

   			htmlToAdd += '<div class="outer-dv price-dv"><div class="left-sec">Unit Price</div> <div class="right-sec">'+ price +'</div></div>';

   		$('.productPopupModalOrder_'+foo).find('.custom-content').html(htmlToAdd);
		$('.productPopupModalOrder_'+foo).modal('show');
		return false;
	});
</script>