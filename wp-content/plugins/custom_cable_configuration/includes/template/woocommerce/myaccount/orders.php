<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
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

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>
<style>
.loadOrder {
	border: 7px solid #f3f3f3;
	border-radius: 58%;
	border-top: 7px solid #3498db;
	width: 20px;
	height: 20px;
	-webkit-animation: spin 2s linear infinite;
	animation: spin 2s linear infinite;
	position: absolute;
	right: 13%;
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
	<table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php
			$foo = 1;
			 foreach ( $customer_orders->orders as $customer_order ) :
				$order      = wc_get_order( $customer_order );
				$item_count = $order->get_item_count();
				
				?>
				<tr class="order">
					<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
						<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
								<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

							<?php elseif ( 'order-number' === $column_id ) : ?>
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									<?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); ?>
								</a>

							<?php elseif ( 'order-date' === $column_id ) : ?>
								<time datetime="<?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>

							<?php elseif ( 'order-status' === $column_id ) : ?>
								<?php echo wc_get_order_status_name( $order->get_status() ); ?>

							<?php elseif ( 'order-total' === $column_id ) : ?>
								<?php echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ); ?>

							<?php elseif ( 'order-actions' === $column_id ) : ?>
								<?php
									$actions = array(
										'pay'    => array(
											'url'  => $order->get_checkout_payment_url(),
											'name' => __( 'Pay', 'woocommerce' )
										),
										'view'   => array(
											'url'  => $order->get_view_order_url(),
											'name' => __( 'View', 'woocommerce' )
										),
										'cancel' => array(
											'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
											'name' => __( 'Cancel', 'woocommerce' )
										)
									);

									if ( ! $order->needs_payment() ) {
										unset( $actions['pay'] );
									}

									if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
										unset( $actions['cancel'] );
									}

									if ( $actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order ) ) {
											echo '<a class="button reorder_'.$foo.'" onclick="reorderDataOr('.$order->get_order_number().','.$foo.');return false;" href="javascipt:void(0);">Re-Order</a>';
											echo '<div class="loadOrder loaderO_'.$foo.'" style="display: none"></div>';
										foreach ( $actions as $key => $action ) {
										
											echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
											
										}
									}
								?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php  $foo++; 
			endforeach; ?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( $current_page !== intval( $customer_orders->max_num_pages ) ) : ?>
				<a class="woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo get_site_url() ?>/products">
			<?php _e( 'Go Products', 'woocommerce' ) ?>
		</a>
		<?php _e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
<script type="text/javascript">
	var BASE_PATH = "<?php echo get_site_url();  ?>";
	function reorderDataOr(orderId,key) { 
		
		var calls = [];
		jQuery('.loaderO_'+key).show();
		jQuery('.reorder_'+key).hide();
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
