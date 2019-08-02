<?php
/**
 * Plugin Name: Add Short Description to Woocommerce Product Loop 
 * Plugin URI: http://shop.megladonmfg.com
 * Description: Add product short descriptions to the loop in product archive pages (requires WooCommerce to be activated)
 * Version: 1.0
 * Author: Michael Bobrowski
 * Author URI: http://megladonmfg.com
 *
 */
 
 function shortdescription_in_product_archives() {
      
    the_excerpt();

     
}

add_action( 'woocommerce_short_description', $post->product->post_excerpt );

$_product->post->post_excerpt;

add_action( 'woocommerce_after_shop_loop_item_title', 'shortdescription_in_product_archives', 40 );

?>



