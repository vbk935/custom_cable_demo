<?php
/**
 * Composited Product Title
 * @version  2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

?>
<h4 class="bto_product_title product_title"><?php
	$quantity = $quantity !== '' ? ' &times; ' . $quantity : '';
	echo apply_filters( 'woocommerce_composited_product_title', $title, $product_id ) . $quantity;
?></h4>
