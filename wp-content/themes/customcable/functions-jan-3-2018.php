<?php

require get_stylesheet_directory() . '/functions/head.php';
require get_stylesheet_directory() . '/functions/main.php';
require get_stylesheet_directory() . '/functions/media.php';
require get_stylesheet_directory() . '/functions/page.php';
require get_stylesheet_directory() . '/functions/menus.php';
require get_stylesheet_directory() . '/functions/search.php';
require get_stylesheet_directory() . '/functions/taxonomy.php';
require get_stylesheet_directory() . '/functions/pagination.php';
require get_stylesheet_directory() . '/functions/text.php';
require get_stylesheet_directory() . '/functions/dashboard.php';
require get_stylesheet_directory() . '/functions/posts.php';
require get_stylesheet_directory() . '/functions/tinymce.php';
/*require get_stylesheet_directory() . '/functions/export.php';*/
// require get_stylesheet_directory() . '/functions/custom-add-to-cart.php';
require get_stylesheet_directory() . '/functions/scripts.php';
require get_stylesheet_directory() . '/functions/styles.php';


	//admin

require get_stylesheet_directory() . '/functions/admin-sea-spider.php';
require get_stylesheet_directory() . '/functions/woo.php';
    
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
add_filter( 'woocommerce_cart_item_name', 'add_sku_in_cart', 20, 3);

function add_sku_in_cart( $title, $values, $cart_item_key ) {
    $sku = $values['data']->get_sku();
    return $sku ? $title . sprintf(" (SKU: %s)", $sku) : $title;
}

function auto_update_specific_plugins ( $update, $item ) {
    // Array of plugin slugs to always auto-update
    $plugins = array ( 
        'csv-import-export',
        );
    if ( in_array( $item->slug, $plugins ) ) {
        return true; // Always update plugins in this array
    } else {
        return $update; // Else, use the normal API response to decide whether to update or not
    }
}
add_filter( 'auto_update_plugin', 'auto_update_specific_plugins', 10, 2 );


//function admin_default_page() {
  //return 'http://localhost/megladonmfg_local/products/';
//}

//add_filter('login_redirect', 'admin_default_page');  

//redirection after login for other users
$admin_url = home_url('wp-admin');


if ( !is_wp_error($user) )
{
    if (user_can($user,	 'administrator'))
    {
        wp_redirect($admin_url);
    }
    else
    {
        
    }
}

function shortcode_my_orders( $atts ) {
    extract( shortcode_atts( array(
        'order_count' => -1
    ), $atts ) );

    ob_start();
    wc_get_template( 'myaccount/my-orders.php', array(
        'current_user'  => get_user_by( 'id', get_current_user_id() ),
        'order_count'   => $order_count
    ) );
    return ob_get_clean();
}
add_shortcode('my_orders', 'shortcode_my_orders'); 




//To use Session Variables
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function myEndSession() {
    session_destroy ();
}

/* Remove srcset from image tag - to enable thumbnail on products list page in admin */
function disable_srcset( $sources ) {
    return false;
}

add_action( 'personal_options_update', 'save_custom_profile_fields' );
add_action( 'edit_user_profile_update', 'save_custom_profile_fields' );
function save_custom_profile_fields( $user_id ) {
    update_user_meta( $user_id, 'user_discount', $_POST['user_discount'], get_user_meta( $user_id, 'user_discount', true ) );
}
function get_breadcrumb() {
    echo '<a href="'.home_url().'" rel="nofollow">Home</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_category(' &bull; ');
            if (is_single()) {
                echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
                the_title();
            }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}

add_action( 'personal_options', 'add_profile_options');
function add_profile_options( $profileuser ) {
    $user_discount = get_user_meta($profileuser->ID, 'user_discount', true);
    ?><tr>
    <th scope="row">User Discount %</th>
    <td><input type="number" min="1" max="100" name="user_discount" id="userDiscount" value="<?php echo $user_discount; ?>" /></td>
</tr><?php
}


/**
 * Cart Override
 */

function iconic_add_engraving_text_to_cart_item( $cart_item_data, $product_id, $variation_id ) {

    // Static product
    if ($product_id != 2164)
        return;
    
    $part_number = filter_input( INPUT_GET, 'part_number' );
    $price = filter_input(INPUT_GET, 'price'); 
    
    if ( empty( $part_number ) ) {
        return $cart_item_data;
    }
 
    $cart_item_data['part_number'] = $part_number;
    $cart_item_data['custom_price'] = $price;

    return $cart_item_data;
}
 
add_filter( 'woocommerce_add_cart_item_data', 'iconic_add_engraving_text_to_cart_item', 10, 3 );


/**
 * Display engraving text in the cart.
 *
 * @param array $item_data
 * @param array $cart_item
 *
 * @return array
 */
function iconic_display_engraving_text_cart( $item_data, $cart_item ) {
    if ( empty( $cart_item['iconic-engraving'] ) ) {
        return $item_data;
    }

    $item_data[] = array(
        'key'     => __( 'Part Number', 'part_number' ),
        'value'   => wc_clean( $cart_item['part_number'] ),
        'display' => '',
    );

    return $item_data;
}

add_filter( 'woocommerce_get_item_data', 'iconic_display_engraving_text_cart', 10, 2 );


/**
 * Add engraving text to order.
 *
 * @param WC_Order_Item_Product $item
 * @param string                $cart_item_key
 * @param array                 $values
 * @param WC_Order              $order
 */
function iconic_add_engraving_text_to_order_items( $item, $cart_item_key, $values, $order ) {
    if ( empty( $values['part_number'] ) ) {
        return;
    }

    $item->add_meta_data( __( 'Part Number', 'part_number' ), $values['part_number'] );
}

add_action( 'woocommerce_checkout_create_order_line_item', 'iconic_add_engraving_text_to_order_items', 10, 4 );

/**
 * Custom price update
 */
add_action( 'woocommerce_before_calculate_totals', 'update_custom_price', 1, 1 );
function update_custom_price( $cart_object ) {
    foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {       
        // Version 2.x
        //$value['data']->price = $value['_custom_options']['custom_price'];
        // Version 3.x / 4.x
        $value['data']->set_price($value['custom_price']);
    }
}


?>