<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
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
 * @version 2.6.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$order = wc_get_order( $order_id );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
?>
<style>
.loaderO {
	border: 7px solid #f3f3f3;
	border-radius: 58%;
	border-top: 7px solid #3498db;
	width: 40px;
	height: 40px;
	-webkit-animation: spin 2s linear infinite;
	animation: spin 2s linear infinite;
	position: absolute;
	right: 20px;
	margin-top: -45px;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<h2><?php _e( 'Order Details', 'woocommerce' ); ?></h2>
<a class="button reorder" onclick="reorderDataOrDe(<?php echo $order_id ?>);return false;" href="javascipt:void(0);">Re-Order</a>
<div class="loaderO" style="display: none"></div>
<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-part-number hidden"><?php _e( 'Part Number', 'woocommerce' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$foo= 0;
			foreach( $order->get_items() as $item_id => $item ) {
				/*$items = $order->get_items(); 
				$order_item_id = key($items);	*/
				$product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
				wc_get_template( 'order/order-details-item.php', array(
					'order'			     => $order,
					'foo'			     => $foo,
					'item_id'		     => $item_id,
					'item'			     => $item,
					'show_purchase_note' => $show_purchase_note,
					'purchase_note'	     => $product ? get_post_meta( $product->id, '_purchase_note', true ) : '',
					'product'	         => $product,
				) );
				$foo++;
			}
		?>
		<?php do_action( 'woocommerce_order_items_table', $order ); ?>
	</tbody>
	<tfoot>
		<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {				
				?>
				<tr>
					<th scope="row"><?php echo $total['label']; ?></th>		
					<td class="hidden"></td>			
					<td><?php echo $total['value']; ?></td>
				</tr>
				<?php
			}
		?>
	</tfoot>
</table>
<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
<?php if ( $show_customer_details ) : ?>
	<?php wc_get_template( 'order/order-details-customer.php', array( 'order' =>  $order ) ); ?>
<?php endif; ?>
<script type="text/javascript">
// 	function reorderDataOrDe(orderId) { 
// 		jQuery('.loaderO').show();
// 		jQuery('.reorder').hide();
// 		jQuery.ajax({
// 			type: "POST",
// 			url: "<?php //echo plugins_url().'/custom_cable_configuration/'?>ajax/ajax.php",
// 			dataType: "json",
// 			data: {order_id: orderId,action:'orderData'},
// 			success: function(response) {
// 				//console.log("outer Success", response);
// 				var length = response.length-1;
// 				var BASE_PATH = "<?php //echo get_site_url()  ?>";
// 				var url = "<?php //echo get_site_url()  ?>/configure-product";
				
// 				$.each(response, function (i, value) {
// 					/* Add to cart */

// 					var part_number = value.part_number;
// 					var myJSON =  value.myJson;
// 	   				var price =  value.price;
// 	   				var product_label = value.product_label;
// 	   				var product_weight = value.product_weight;

// 	   				var cartAdd = $.ajax({
// 	   					type: "POST",
// 	   					url: url,
// 	   					dataType: 'json',
// 	   					data: {
// 	   						"post_type": "product",
// 	   						"add-to-cart": 2164,
// 	   						"part_number": part_number,
// 	   						"price": price,
// 	   						"product_label": product_label,
//             				"product_weight": product_weight,
// 	   						"products_additional_details": myJSON
// 	   					},
// 	   					success: function(data) {
// 	   						if(length == i){
// 	   							window.location = BASE_PATH+'/cart';
// 	   						}
// 	   						console.log('success',data);
// 	   					},
// 	   					error: function(error) {
// 	   						if(length == i){
// 	   							window.location = BASE_PATH+'/cart';
// 	   						}
// 	   						console.log('error',error);
// 	   					},
	   					
// 	   				});
	   			
// 	   			});
	   			
// 			},
// 			error: function(e) {
// 				console.log("Error", e);
// 			}
// 		});
	
// 	return false;
// }
</script>

<script type="text/javascript">
	var BASE_PATH = "<?php echo get_site_url();  ?>";
	function reorderDataOrDe(orderId) { 
		
		var calls = [];
		jQuery('.loaderO').show();
		jQuery('.reorder').hide();
		jQuery.ajax({
			type: "POST",
			url: "<?php echo plugins_url(); ?>/custom_cable_configuration/ajax/ajax.php",
			dataType: "json",
			data: {
				order_id: orderId,
				action:'orderData'
			},
			success: function(response) {
				//console.log("outer Success", response);
				var length = response.length-1;
				var url = "<?php echo get_site_url();  ?>/configure-product";
				
				for (var i=0; i<response.length; i++) {
					calls.push(i);
				}
				// Hack to redirect when last loop terminates
				$.each(response, function (i, value) {
					/* Add to cart */
					//console.log("Begin step ", i, "value=", value);
					var part_number = value.part_number;
					var myJSON =  value.myJson;
   				var price =  value.price;
   				var product_label = value.product_label;
   				var product_weight = value.product_weight;
   				var product_id = parseInt(value.product_id);
   				//console.log("FOrm data sent", part_number, myJSON, price, product_label, product_weight);
	   				var cartAdd = $.ajax({
	   					type: "POST",
	   					url: url,
	   					dataType: 'json',
	   					data: {
	   						"post_type": "product",
	   						"add-to-cart": product_id,
	   						"part_number": part_number,
	   						"price": price,
	   						"product_label": product_label,
            				"product_weight": product_weight,
	   						"products_additional_details": myJSON
	   					},
	   					success: function(data) {
	   						calls.length--;
	   						if(length == i){
	   							// window.location = BASE_PATH+'/cart';
	   						}
	   						//console.log('success',data);
	   						redirectIfLast(calls);
	   					},
	   					error: function(error) {
	   						calls.length--;
	   						if(length == i){
	   							// window.location = BASE_PATH+'/cart';
	   						}
	   						//console.log('error',error);
	   						redirectIfLast(calls);
	   					},
	   					
	   				});
	   			
	   			});
	   			
			},
			error: function(e) {
				//console.log("Error, outer==", e);
			}
		});
	
	return false;
}

function redirectIfLast(calls) {
		if (calls.length != 0)
			return;
		window.location = BASE_PATH+'/cart';
}
</script>
