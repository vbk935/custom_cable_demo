<?php
$addon_class = array(
	'wc-pao-addon',
	'wc-pao-addon-' . sanitize_title( $name ),
	'product-addon' // legacy v2
);

if ( 1 == $required ) {
	$addon_class[] = 'wc-pao-required-addon';
}

$addon_type = ! empty( $addon['type'] ) ? $addon['type'] : '';

if ( $addon_type ) {
	$addon_class[] = $addon_type;

	if ( 'select' === $addon_type ) {
		// Compat with add-ons v3 and above.
		$addon_class[] = 'multiple_choice';
	}
}

if ( ! $description ) {
	$addon_class[] = 'no-labels';
}
?>
<div class="<?php echo implode( ' ', array_map( 'esc_attr', $addon_class ) ); ?>">

	<?php do_action( 'wc_product_addon_start', $addon ); ?>

	<?php if ( $name ) : ?>
		<h3 class="wc-pao-addon-name addon-name"><?php echo wptexturize( $name ); ?> <?php if ( 1 == $required ) { echo '<abbr class="required" title="' . __( 'Required field', 'woocommerce-product-table' ) . '">*</abbr>'; } ?></h3>
	<?php endif; ?>

	<?php if ( $description ) : ?>
		<?php echo '<div class="wc-pao-addon-description addon-description">' . wpautop( wptexturize( $description ) ) . '</div>'; ?>
	<?php endif; ?>

	<?php
	do_action( 'wc_product_addon_options', $addon );
