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
add_theme_support( 'post-thumbnails', array( 'post', 'page', 'movie', 'product' ) );

/**
 * START: WP Redirect non-loggedin users visiting specific pages
 */
// add_action( 'template_redirect', 'redirect_to_specific_page' );

function redirect_to_specific_page() {

    if ( is_page('configure-product') && ! is_user_logged_in() ) {
        wp_redirect(home_url('my-account')); 
        exit;
    }
}
/**
 * END: WP Redirect non-loggedin users visiting specific pages
 */

/**
 * START: WP Redirect all requests to wp-login to custom login
 */
add_action('init','possibly_redirect');

function possibly_redirect(){

    if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('logout', ''))) {
        global $pagenow;

        // Logout the user if logout requested
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
            wp_logout();
        }

        if( 'wp-login.php' == $pagenow ) {
           wp_redirect(home_url('my-account'));
           exit();
        }
    }
}
// if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
//    die("Boom!! Gotchaa"); // We're on the login page!
// }
/**
 * START: WP Redirect all requests to wp-login to custom login
 */

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
    
    // Allow adding normally
    $part_number = 'N.A';
    $price = -1; 
    $products_additional_details = ''; 
    $product_label = '';
    $product_weight = -1;
    $product_discount = 0;

    // Static product
    if ($product_id == 2164) {
        // Allow adding normally
        $part_number = filter_input( INPUT_POST, 'part_number' );
        $price = filter_input(INPUT_POST, 'price'); 
        $products_additional_details = $_POST['products_additional_details']; 
        $product_label = filter_input( INPUT_POST, 'product_label');
        $product_weight = filter_input( INPUT_POST, 'product_weight');
        $per_product_discount = filter_input(INPUT_POST, 'per_product_discount');
    }        

    if ( empty( $part_number ) ) {
        return $cart_item_data;
    }
 
    $cart_item_data['custom_fields']['part_number'] = $part_number;
    $cart_item_data['custom_fields']['per_product_discount'] = $per_product_discount;
    $cart_item_data['product_weight'] = $product_weight;
    $cart_item_data['product_label'] = $product_label;
    $cart_item_data['custom_products_additional_details'] = $products_additional_details;
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
        'key'     => __( 'part_number', 'part_number' ),
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
   
    if ( empty( $values['custom_fields']['part_number'] ) ) {
         $item->add_meta_data( __( 'Part Number', 'part_number' ), 'N/A' );
    }

    // Run only incase partnumber is passed, i.e., the product is a canvas generated product
    // Else no need of any metadata
    if ($values['custom_price'] != -1 ) {
        $item->add_meta_data( __( 'Part Number', 'part_number' ), $values['custom_fields']['part_number'] );
        $item->add_meta_data( __( 'Products Details', 'products_additional_details' ), $values['custom_products_additional_details'] );
        $item->add_meta_data( __( 'Product Label', 'product_label' ), $values['product_label'] );
        $item->add_meta_data( __( 'Per Product Discount', 'per_product_discount' ), $values['custom_fields']['per_product_discount'] );
    }
    if ($values['product_weight'] != -1){
        $item->add_meta_data( __( 'Product Weight', 'product_weight' ), $values['product_weight'] );
    }    
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
        if ($value['custom_price'] != -1) {
            $value['data']->set_price($value['custom_price']);
            
            // Override Product name
            $value['data']->set_name($value['product_label']);
        }
        
    }
}

/**
 * Redirect users after add to cart.
 */
// define the woocommerce_add_to_cart_redirect callback 
function filter_woocommerce_add_to_cart_redirect( $wc_get_cart_url ) { 
    // make filter magic happen here... 
    $cart_url = WC_CART::get_cart_url();
    // if (is_user_logged_in()){
    $redirect_url = $cart_url;
    // }
    // else {
    //     $redirect_url =  home_url('my-account');//wp_login_url( $cart_url ); 
    // }
    return $redirect_url;
}; 
         
// add the filter 
add_filter( 'woocommerce_add_to_cart_redirect', 'filter_woocommerce_add_to_cart_redirect', 10, 1 );

/**
 * Override login url link to a custom link
 */
add_filter( 'login_url', 'my_login_page', 10, 3 );
function my_login_page( $login_url, $redirect, $force_reauth ) {
    return home_url( '/my-account/?redirect_to=' . $redirect );
}


add_action( 'woocommerce_admin_order_data_after_shipping_address', 'misha_editable_order_meta_shipping' );
 
function misha_editable_order_meta_shipping( $order ){
 
    $shippingdate = get_post_meta( $order->get_order_number(), 'shippingdate', true );
 
    ?>
    <div class="address">
        <p<?php if( empty($shippingdate) ) echo ' class="none_set"' ?>>
            <strong>Shipping date:</strong>
            <?php echo ( !empty( $shippingdate ) ) ? $shippingdate : 'Anytime.' ?>
        </p>
    </div>
    <div class="edit_address"><?php
        woocommerce_wp_text_input( array( 
            'id' => 'shippingdate',
            'label' => 'Shipping date', 
            'wrapper_class' => 'form-field-wide',
            'class' => 'date-picker',
            'style' => 'width:100%',
            'value' => $shippingdate,
            'description' => 'This is the day, when the customer would like to receive his order.'
        ) );
    ?></div><?php
}
 
add_action( 'woocommerce_process_shop_order_meta', 'misha_save_shipping_details' );
 
function misha_save_shipping_details( $ord_id ){
    update_post_meta( $ord_id, 'shippingdate', wc_clean( $_POST[ 'shippingdate' ] ) );
}

// Send new user registration email to admin
add_action('woocommerce_created_customer', 'admin_email_on_registration', 10 , 1);
function admin_email_on_registration( $customer_id) {
    wp_new_user_notification( $customer_id );
}
?>
