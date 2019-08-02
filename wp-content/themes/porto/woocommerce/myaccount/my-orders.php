<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page.
 *
 * @version     2.5.0
 * @deprecated  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$porto_woo_version = porto_get_woo_version_number();

$my_orders_columns = apply_filters(
	'woocommerce_my_account_my_orders_columns',
	array(
		'order-number'  => __( 'Order', 'porto' ),
		'order-date'    => __( 'Date', 'porto' ),
		'order-status'  => __( 'Status', 'porto' ),
		'order-total'   => __( 'Total', 'porto' ),
		'order-actions' => '&nbsp;',
	)
);

$customer_orders = get_posts(
	apply_filters(
		'woocommerce_my_account_my_orders_query',
		array(
			'numberposts' => $order_count,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types( 'view-orders' ),
			'post_status' => array_keys( wc_get_order_statuses() ),
		)
	)
);

if ( $customer_orders ) : ?>

<div class="featured-box align-left">
	<div class="box-content">

		<h2><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', __( 'Recent Orders', 'porto' ) ); ?></h2>

		<table class="shop_table responsive my_account_orders">

			<thead>
				<tr>
					<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
						<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach ( $customer_orders as $customer_order ) :
					$order = wc_get_order( $customer_order );
					if ( version_compare( $porto_woo_version, '2.5', '<' ) ) {
						$order->populate( $customer_order );
					}
					$item_count = $order->get_item_count();
					?>
					<tr class="order">
						<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
							<td class="<?php echo esc_attr( $column_id ); ?>">
								<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
									<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>
								<?php elseif ( 'order-number' === $column_id ) : ?>
									<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
										<?php echo esc_html_x( '#', 'hash before order number', 'porto' ) . $order->get_order_number(); ?>
									</a>
								<?php elseif ( 'order-date' === $column_id ) : ?>
									<time datetime="<?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>
								<?php elseif ( 'order-status' === $column_id ) : ?>
									<?php echo wc_get_order_status_name( $order->get_status() ); ?>
								<?php elseif ( 'order-total' === $column_id ) : ?>
									<?php echo sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'porto' ), $order->get_formatted_order_total(), $item_count ); ?>
								<?php elseif ( 'order-actions' === $column_id ) : ?>
									<?php
										$actions = array(
											'pay'    => array(
												'url'  => $order->get_checkout_payment_url(),
												'name' => __( 'Pay', 'porto' ),
											),
											'view'   => array(
												'url'  => $order->get_view_order_url(),
												'name' => __( 'View', 'porto' ),
											),
											'cancel' => array(
												'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
												'name' => __( 'Cancel', 'porto' ),
											),
										);

									if ( ! $order->needs_payment() ) {
										unset( $actions['pay'] );
									}

									if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
										unset( $actions['cancel'] );
									}

									if ( $actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order ) ) {
										foreach ( $actions as $key => $action ) {
											echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
										}
									}
									?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<?php endif; ?>
