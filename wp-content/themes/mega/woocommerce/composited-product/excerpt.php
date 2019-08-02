<?php
/**
 * Composited Product Excerpt
 * @version  2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( $product_description )
	echo '<p>' . apply_filters( 'woocommerce_composited_product_excerpt', $product_description, $product_id ) . '</p>';
