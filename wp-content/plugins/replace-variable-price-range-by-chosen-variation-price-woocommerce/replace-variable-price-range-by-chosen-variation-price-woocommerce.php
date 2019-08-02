<?php
/*
 * Plugin Name: Replace the Variable Price range by the chosen variation price in WooCommerce
 * Plugin URI: https://bobysuh.com
 * Description: Replace the Variable Price range by the chosen variation price in WooCommerce.
 * Author: Laura Díaz
 * Version: 2.0
 * Author URI: https://diariodeunafriki.com
 * Text Domain: replace-variable-price-range-by-chosen-variation-price-woocommerce
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 3.5.5
 * License: GPLv2+
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function replace_variable_price_range_by_chosen_variation_price_woocommerce() {
    load_plugin_textdomain( 'replace-variable-price-range-by-chosen-variation-price-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'replace_variable_price_range_by_chosen_variation_price_woocommerce' );
add_action( 'woocommerce_before_single_product', 'check_if_variable_first' );
function check_if_variable_first(){
    if ( is_product() ) {
        global $post;
        $product = wc_get_product( $post->ID );
        if ( $product->is_type( 'variable' ) ) {
            // removing the price of variable products
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

// Change location of
add_action( 'woocommerce_single_product_summary', 'custom_wc_template_single_price', 10 );
function custom_wc_template_single_price(){
    global $product;

// Variable product only
if($product->is_type('variable')):

    // Main Price
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    // Sale Price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    if ( $price !== $saleprice && $product->is_on_sale() ) {
        $price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
    }

    ?>
    <style>
        div.woocommerce-variation-price,
        div.woocommerce-variation-availability,
        div.hidden-variable-price {
            height: 0px !important;
            overflow:hidden;
            position:relative;
            line-height: 0px !important;
            font-size: 0% !important;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        $('select').blur( function(){
            //Correct bug, I put 0
            if( 0 != $('input.variation_id').val()){
                $('p.price').html($('div.woocommerce-variation-price > span.price').html()).append('<p class="availability">'+$('div.woocommerce-variation-availability').html()+'</p>');
                console.log($('input.variation_id').val());
            } else {
                $('p.price').html($('div.hidden-variable-price').html());
                if($('p.availability'))
                    $('p.availability').remove();
                console.log('NULL');
            }
        });
    });
    </script>
    <?php

    echo '<p class="price">'.$price.'</p>
    <div class="hidden-variable-price" >'.$price.'</div>';

endif;
}

        }
    }
}
