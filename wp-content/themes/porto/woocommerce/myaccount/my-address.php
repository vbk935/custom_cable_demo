<?php
/**
 * My Addresses
 *
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$customer_id = get_current_user_id();

$porto_woo_version = porto_get_woo_version_number();


if ( version_compare( $porto_woo_version, '2.6', '>=' ) ) {
	$shipping_enabled = wc_shipping_enabled();
} else {
	$shipping_enabled = get_option( 'woocommerce_calc_shipping' ) !== 'no';
}

if ( ! wc_ship_to_billing_address_only() && $shipping_enabled ) {
	$page_title    = apply_filters( 'woocommerce_my_account_my_address_title', __( 'My Addresses', 'porto' ) );
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'porto' ),
			'shipping' => __( 'Shipping address', 'porto' ),
		),
		$customer_id
	);
} else {
	$page_title    = apply_filters( 'woocommerce_my_account_my_address_title', __( 'My Address', 'porto' ) );
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'porto' ),
		),
		$customer_id
	);
}

$oldcol = 1;
$col    = 1;
?>

<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
<div class="featured-box align-left">
	<div class="box-content">
		<h2 class="page-title m-b"><?php echo esc_html( $page_title ); ?></h2>
<?php endif; ?>

<p class="myaccount_address m-b-none">
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', __( 'The following addresses will be used on the checkout page by default.', 'porto' ) ); ?>
</p>

<?php
if ( ! wc_ship_to_billing_address_only() && $shipping_enabled ) {
	echo '<div class="u-columns woocommerce-Addresses col2-set addresses">';}
?>

<?php foreach ( $get_addresses as $name => $title ) : ?>

	<div class="u-column<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> col-<?php echo ( ( $oldcol = $oldcol * -1 ) < 0 ) ? 1 : 2; ?> woocommerce-Address address">
		<header class="woocommerce-Address-title title">
			<h3><?php echo esc_html( $title ); ?></h3>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit"><?php esc_html_e( 'Edit', 'porto' ); ?></a>
		</header>

		<address>
		<?php
			$address = wc_get_account_formatted_address( $name );
			echo ! $address ? esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ) : wp_kses_post( $address );
		?>
		</address>
	</div>

<?php endforeach; ?>

<?php
if ( ! wc_ship_to_billing_address_only() && $shipping_enabled ) {
	echo '</div>';}
?>

<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
	</div>
</div>
	<?php
endif;
