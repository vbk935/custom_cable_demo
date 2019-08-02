<div class="order-search-list">
<table class="table">
	<tr>
		<th>Order id</th>
		<th>Order date</th>
		<th>Billing Address</th>
		<th>Shipping Address</th>
		<th></th>
	</tr>
 <?php if (!is_user_can_search_order()) { ?>  
 	<tr>
		<td colspan="5">You have not permission to view order</td>
	</tr>
<?php }elseif ( $customer_orders ) { ?>    
	<?php
    foreach ( $customer_orders as $customer_order ) {
        $order = new WC_Order( $customer_order );
        $order->populate( $customer_order );
        $item_count = $order->get_item_count();
        ?>
        <tr>
            <td><?php echo $order->get_order_number() ?></td>
            <td><?php echo date( get_option( 'date_format' ), strtotime( $order->order_date )); ?></td>
            <td>
                <address>
                <?php  if ( ! $order->get_formatted_billing_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_billing_address();?>
                </address>
            </td>
            <td>
                <address>
                <?php if ( ! wc_ship_to_billing_address_only()&&$order->needs_shipping_address()&&get_option('woocommerce_calc_shipping')!=='no') : ?>
                <?php if ( ! $order->get_formatted_shipping_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_shipping_address(); ?>
                <?php endif; ?>
                </address>
            </td>
            <td>
                <?php
                $actions['view'] = array(
                    'url'  => $order->get_view_order_url(),
                    'name' => __( 'View', 'woocommerce' )
                );
    
                $actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );
                if ($actions) {
                    foreach ( $actions as $key => $action ) {
                        echo '<a href="'.esc_url( $action['url'] ).'" class="button '.sanitize_html_class( $key ).'">'.esc_html( $action['name'] ).'</a>';
                    }
                }
                ?>
            </td>
        </tr>
        <?php
    }
     ?>
 <?php  }else{?>
	 <tr>
		<td colspan="5">No result found</td>
	</tr>
	<?php } ?>
</table>

</div>