<?php
function wooc_extra_register_fields() {	
    ?>
    <p class="form-row form-row-first">
        <label for="reg_billing_first_name">
            <?php
            _e('First name', 'woocommerce');
            ?>
            <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php
        if (!empty($_POST['billing_first_name']))
            esc_attr_e($_POST['billing_first_name']);
        ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_last_name">
            <?php
            _e('Last name', 'woocommerce');
            ?>
            <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php
        if (!empty($_POST['billing_last_name']))
            esc_attr_e($_POST['billing_last_name']);
        ?>" />
    </p>
    <!-- <p class="form-row form-row-wide">
        <label for="reg_title">
            <?php //_e('Title', 'woocommerce'); ?>
            <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_title" id="reg_title" value="<?php //esc_attr_e($_POST['billing_title']); ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_company">
            <?php //_e('Company', 'woocommerce'); ?>
            <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_company" id="reg_company" value="<?php //esc_attr_e($_POST['billing_company']); ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_title">
			<?php //_e('Company Website', 'woocommerce'); ?>
            <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_company_website" id="reg_company_website" value="<?php //esc_attr_e($_POST['billing_company_website']); ?>" />
    </p> -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_phone">
            <?php _e('Phone', 'woocommerce'); ?>
            <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e($_POST['billing_phone']); ?>" />
    </p>
    <div class="clear"></div>
    <?php
}
add_action('woocommerce_register_form_start', 'wooc_extra_register_fields');
function wooc_extra_register_fields_bottom() {
    ?>
    <p class="form-row form-row-wide">
        By pressing register you accept our <a href="#" id="termAndConditionsId">terms and conditions</a>
    </p>
    <div class="clear"></div>
    <?php
}
add_action('woocommerce_register_form_end', 'wooc_extra_register_fields_bottom');
/**
 * Register fields Validating.
 */
function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {

    //if( (isset( $_POST['billing_title'] ) && empty( $_POST['billing_title'] ))|| (isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] )) || ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) || ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) || ( isset( $_POST['billing_company'] ) && empty( $_POST['billing_company'] ) ) || ( isset( $_POST['billing_company_website'] ) && empty( $_POST['billing_company_website'] ) ))
    if( (isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] )) || ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) || ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] )) )
    {
		$validation_errors->add( 'billing_first_name_error', __( 'Please fill all the required fields.', 'woocommerce' ) );
	}

    if(strlen($_POST['billing_phone']) > 16 || !is_numeric($_POST['billing_phone']))
	{
		$validation_errors->add('billing_phone_error', __('Invalid Phone Number.', 'woocommerce'));
	}

}

add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );

function user_autologout() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $approved_status = get_user_meta($user_id, 'wp-approve-user', true);
        if ($approved_status == 1) {
            return $redirect_url;
        } else {
            wp_logout();
            return get_permalink(woocommerce_get_page_id('myaccount')) . "?approved=false";
        }
    }
}
//add_action('woocommerce_registration_redirect', 'user_autologout', 2);
function show_underapproval() {
    if ((!empty($_GET['approved'])) && ($_GET['approved'] == 'false')) {
        ?>
        <ul class="woocommerce-message">
            <li>Your account is successfully created and is under review.</li>
        </ul>
        <?php
    }
}
//add_action('woocommerce_before_customer_login_form', 'show_underapproval', 2);
/**
 * Below code save extra fields.
 */
add_filter( 'wc_password_strength_meter_params', 'my_strength_meter_custom_strings' );
function my_strength_meter_custom_strings( $data ) {
    $data_new = array(
        'i18n_password_error'   => esc_attr__( 'Come on, enter a stronger password.', 'theme-domain' ),
        'i18n_password_hint'    => esc_attr__( 'Your password should be at least 8 characters long and must contain at least one lowercase letter, one capital letter and one number', 'theme-domain' )
    );

    return array_merge( $data, $data_new );
}
function wooc_save_extra_register_fields($customer_id) {
    if (isset($_POST['billing_phone'])) {
        // Phone input filed which is used in WooCommerce
        update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
    }
    if (isset($_POST['billing_first_name'])) {
        //First name field which is by default
        update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']));
        // First name field which is used in WooCommerce
        update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
    }
    if (isset($_POST['billing_last_name'])) {
        // Last name field which is by default
        update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']));
        // Last name field which is used in WooCommerce
        update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
    }
    /*if (isset($_POST['billing_company'])) {
        update_user_meta($customer_id, 'billing_company', sanitize_text_field($_POST['billing_company']));
    }
    if (isset($_POST['billing_title'])) {
        update_user_meta($customer_id, 'billing_title', sanitize_text_field($_POST['billing_title']));
    }
    if (isset($_POST['billing_company_website'])) {
        update_user_meta($customer_id, 'billing_company_website', sanitize_text_field($_POST['billing_company_website']));
    }*/
}
add_action('woocommerce_created_customer', 'wooc_save_extra_register_fields');
/* Show Extra User Fields */
function tm_additional_profile_fields($user) {
    $title = get_user_meta($user->ID, 'title', TRUE);
    $title_text = '';
    if (!empty($title)) {
        $title_text = $title;
    }
    $company_website = get_user_meta($user->ID, 'company_website', TRUE);
    $company_website_text = '';
    if (!empty($company_website)) {
        $company_website_text = $company_website;
    }
    $company_logo = get_user_meta($user->ID, 'company_logo', TRUE);
    $company_logo_value = '';
    if (!empty($company_logo)) {
        $company_logo_value = $company_logo;
    }
    $transaction_threshold = get_user_meta($user->ID, 'transaction_threshold', TRUE);
    $transaction_threshold_value = '';
    if (!empty($transaction_threshold)) {
        $transaction_threshold_value = $transaction_threshold;
    }
    $amount_threshold = get_user_meta($user->ID, 'amount_threshold', TRUE);
    $amount_threshold_value = '';
    if (!empty($amount_threshold)) {
        $amount_threshold_value = $amount_threshold;
    }
    ?>
    <h3>User information</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_title">Title</label></th>
            <td>
                <input value="<?php echo $title_text; ?>" name="title" type="text" class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="user_website">Company Website</label></th>
            <td>
                <input value="<?php echo $company_website_text; ?>" name="company_website" type="text" class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="company_logo">Company Logo</label></th>
            <td>
                <input value="<?php echo $company_logo_value;?>" name="company_logo" type="text" class="regular-text" style="width:500px"/>
            </td>
        </tr>
        <tr>
            <th><label for="account_level_threshold">Account Level Threshold</label></th>
            <td>
                <input value="<?php
                echo $transaction_threshold_value;
                ?>" name="transaction_threshold" type="text" class="regular-text"/>
                <p>(Transactions/month)</p>
            </td>
            <td>
                <input value="<?php
                echo $amount_threshold_value;
                ?>" name="amount_threshold" type="text" class="regular-text"/>
                <p>(Transaction amount)</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'tm_additional_profile_fields');
add_action('edit_user_profile', 'tm_additional_profile_fields');
/**
 * Save additional profile fields.
 */
function tm_save_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    if (empty($_POST['title'])) {
        //return false;
    }
    if (empty($_POST['company_website'])) {
        //return false;
    }
    if (empty($_POST['company_logo'])) {
      //  return false;
    }
    if (empty($_POST['transaction_threshold'])) {
        //return false;
    }
    if (empty($_POST['amount_threshold'])) {
        //return false;
    } 
    update_user_meta($user_id, 'title', $_POST['title']);
    update_user_meta($user_id, 'company_website', $_POST['company_website']);
    update_user_meta($user_id, 'company_logo', $_POST['company_logo']);
    update_user_meta($user_id, 'transaction_threshold', $_POST['transaction_threshold']);
    update_user_meta($user_id, 'amount_threshold', $_POST['amount_threshold']);
}
add_action('personal_options_update', 'tm_save_profile_fields');
add_action('edit_user_profile_update', 'tm_save_profile_fields');
/* User Authentication */
function check_user_approved() {
    if (is_account_page()) {
        if (empty($_GET['approved'])) {
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $user_meta = get_user_meta($user_id, 'wp-approve-user', TRUE);
                if (empty($user_meta)) {
                    ?>
                    <script>
                        window.location = '<?php
                        echo get_bloginfo('url');
                        ?>';
                    </script>
                    <?php
                }
            }
        }
    }
}
//add_action('wp_head', 'check_user_approved');
//adding item in WooCommerce session
add_filter('woocommerce_add_cart_item_data','wdm_add_item_data',1,2);
if(!function_exists('wdm_add_item_data'))
{
    function wdm_add_item_data($cart_item_data,$product_id)
    {
        /*Here, We are adding item in WooCommerce session with, wdm_user_custom_data_value name*/
        global $woocommerce;
        session_start();
        if (isset($_SESSION['part_number'])) {
            $_SESSION['wdm_user_custom_data'] = array('part_number'=>$_SESSION['part_number'],'value_input'=>$_SESSION['value_input']);
            $option = $_SESSION['wdm_user_custom_data'];
            $new_value = array('wdm_user_custom_data_value' => $option);
        }
        if(empty($option))
            return $cart_item_data;
        else
        {
            if(empty($cart_item_data))
                return $new_value;
            else
                return array_merge($cart_item_data,$new_value);
        }
        //Unset our custom session variable, as it is no longer needed.
        //unset($_SESSION['wdm_user_custom_data']);
    }
}
//extract custom data from woocommerce session and insert it into cart object.
add_filter('woocommerce_get_cart_item_from_session', 'wdm_get_cart_items_from_session', 1, 3 );
if(!function_exists('wdm_get_cart_items_from_session'))
{
    function wdm_get_cart_items_from_session($item,$values,$key)
    {
        if (array_key_exists( 'wdm_user_custom_data_value', $values ) )
        {
            $item['wdm_user_custom_data_value'] = $values['wdm_user_custom_data_value'];
        }
        //echo "<pre>"; print_r($item['wdm_user_custom_data_value']); die("here");
        return $item;
    }
}
/*add_filter('woocommerce_cart_item_price','wdm_add_user_custom_option_from_session_into_cart',1,3);
if(!function_exists('wdm_add_user_custom_option_from_session_into_cart'))
{
 function wdm_add_user_custom_option_from_session_into_cart($product_name, $values, $cart_item_key )
    {
        //code to add custom data on Cart & checkout Page
        if(count($values['wdm_user_custom_data_value']) > 0)
        {
            echo "<pre>"; print_r($values['wdm_user_custom_data_value']);
        }
        else
        {
            return $product_name;
        }
    }
} */
//add custom data as metadata to order items
add_action('woocommerce_add_order_item_meta','wdm_add_values_to_order_item_meta',1,2);
if(!function_exists('wdm_add_values_to_order_item_meta'))
{
  function wdm_add_values_to_order_item_meta($item_id, $values)
  {
    global $woocommerce,$wpdb;
    $user_custom_values = $values['wdm_user_custom_data_value'];
    if(!empty($user_custom_values))
    {
        wc_add_order_item_meta($item_id,'part_number',$user_custom_values);
    }
}
}
//remove custom data if product removed from cart
add_action('woocommerce_before_cart_item_quantity_zero','wdm_remove_user_custom_data_options_from_cart',1,1);
if(!function_exists('wdm_remove_user_custom_data_options_from_cart'))
{
    function wdm_remove_user_custom_data_options_from_cart($cart_item_key)
    {
        global $woocommerce;
        // Get cart
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach( $cart as $key => $values)
        {
            if ( $values['wdm_user_custom_data_value'] == $cart_item_key )
                unset( $woocommerce->cart->cart_contents[ $key ] );
        }
    }
}
//Overwrite View Order Detail Page Template
function action_woocommerce_order_details_after_order_table( $order ) {
    $order_details = wc_get_order( $order );
    $items = $order_details->get_items();
    $order_item_id = key($items);
    $order_metadata = wc_get_order_item_meta($order_item_id,'part_number');
    foreach($order_metadata['part_number'] as $val)
    {
        if($val == "-")
        {
            $part_no[] = "-";
        }
        else
        {
            $subterm_meta_val = get_option("taxonomy_term_$val");
            if($subterm_meta_val['presenter_id'] == "changable")
            {
                $part_no[] = $order_metadata['value_input'];
            }
            else
            {
                $part_no[] = $subterm_meta_val['unit_name'];
            }
        }
    }
    $partno = implode("",$part_no);
    echo "<h2>Product Configuration Details</h2>";
    echo "<h4>Part Number: ".$partno."</h4>";
    echo '<table class="shop_table order_details"><thead><tr><th class="product-name">Product</th><th class="product-total">Total</th></tr></thead><tbody>';
    foreach($order_metadata['part_number'] as $key=>$part_number)
    {
        if($key != 0)
        {
            $main_term = get_term_by('id', $key, 'configuration');
            $main_term_meta = get_option("taxonomy_term_$key");
            $sub_term = get_term_by('id', $part_number, 'configuration');
            $subterm_meta = get_option("taxonomy_term_$part_number");
            if($subterm_meta['presenter_id'] == "changable")
            {
                echo '<tr class="order_item"><td class="product-name">'.$main_term->name.'</td><td class="product-total">'.$order_metadata['value_input'].'</td></tr>';
            }
            else if($main_term_meta['hide_config'] != 1)
            {
                $name = $main_term->name . " : " . $sub_term->name;
                echo '<tr class="order_item"><td class="product-name">'.$main_term->name.'</td><td class="product-total">'.$sub_term->name.'</td></tr>';
            }
        }
    }
    echo '</table>';
};
//add_action( 'woocommerce_order_details_after_order_table', 'action_woocommerce_order_details_after_order_table', 10, 1 );
// Add new tab for Product Configuration on single product page.
add_filter( 'woocommerce_product_tabs', 'woo_custom_product_tabs' );
function woo_custom_product_tabs( $tabs ) {
    global $product;
    $product_meta = get_post_meta($product->id, 'configuration_combination', true);
    if(!empty($product_meta))
    {
        $tabs['product_config_tab'] = array(
            'title'     => __( 'Product Configuration', 'woocommerce' ),
            'priority'  => 100,
            'callback'  => 'woo_product_config_tab_content'
            );
        return $tabs;
    }
}
add_filter( 'wc_add_to_cart_message', 'wc_custom_add_to_cart_message' );
function wc_custom_add_to_cart_message() {
    echo '<style>.woocommerce-message {display: none !important;}.woocommerce-password-hint{display:none;}</style>';
}
// New Tab content
function woo_product_config_tab_content() {
    global $post;
    $post_id = $post->ID;
    $configuration_combination_raw = get_post_meta( $post_id, 'configuration_combination', true );
    if (!empty($configuration_combination_raw))
    {
        $configuration_combination = unserialize($configuration_combination_raw);
    }
    $per_unit_raw = get_post_meta($post->ID, 'per_unit', TRUE);
    if (!empty($per_unit_raw))
    {
        $per_unit = unserialize($per_unit_raw);
    }
    if(isset($configuration_combination) and (!empty($configuration_combination)))
    {
        $main_category_id = '';
        $child_data = '';
        echo '<table class="shop_attributes"><tbody>';
        $i = 1;
        foreach ($configuration_combination as $id => $data)
        {
            $main_category_id = $id;
            $main_category = get_term_by('id', $id, 'configuration');
            $child_data = $data;
            foreach($child_data as $child_key => $child_val)
            {
                $p_term = get_term_by('id', $child_key, 'configuration');
                $c_term = get_term_by('id', $child_val, 'configuration');
                if($i%2 == 0)
                {
                    echo '<tr class="alt">';
                }
                else
                {
                    echo '<tr>';
                }
                echo '<th>'.$p_term->name.'</th>';
                echo '<td class="product_weight">'.$c_term->name.'</td>';
                echo '</tr>';
                $i++;
            }
        }
        echo '</tbody></table>';
    }
}
// To remove sidebar from single product page
//add_action('template_redirect', 'remove_sidebar_shop');
function remove_sidebar_shop()
{
    if ( is_product('add-page-i.d-here') )
    {
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');
    }
}
//To add configuration below title on single product page
function wpse116660_wc_add_2nd_title()
{
    global $post;
    $post_id = $post->ID;
    $configuration_combination_raw = get_post_meta( $post_id, 'configuration_combination', true );
    if (!empty($configuration_combination_raw))
    {
        $configuration_combination = unserialize($configuration_combination_raw);
    }
    $main_key = key($configuration_combination);
    $get_main_term = get_term_by('id',$main_key, 'configuration');
    echo $get_main_term->name;
}
add_action( 'woocommerce_single_product_summary', 'wpse116660_wc_add_2nd_title', 6 );
//Hide Free Price Text
add_filter( 'woocommerce_variable_free_price_html',  'hide_free_price_notice' );
add_filter( 'woocommerce_free_price_html',           'hide_free_price_notice' );
add_filter( 'woocommerce_variation_free_price_html', 'hide_free_price_notice' );
function hide_free_price_notice( $price )
{
  return '';
}
//Remove add to cart and add configure button on products list page.
add_filter( 'woocommerce_loop_add_to_cart_link', 'sfws_add_product_link' );
add_action( 'woocommerce_checkout_order_processed', 'is_express_delivery',  1, 1  );
function is_express_delivery( $order_id ){
       $order = wc_get_order( $order_id );
       $items = $order->get_items();
       $value_input = array();
       $line = 1;
       foreach ($items as $key => $value) {
              $productId = $value['product_id'];
              $product = wc_get_product( $productId );
              $uom = array_shift( wc_get_product_terms( $product->id, 'pa_uom', array( 'fields' => 'names' ) ) );
              $unspsc = array_shift( wc_get_product_terms( $product->id, 'pa_unspsc', array( 'fields' => 'names' ) ) );
              $manufacture = array_shift( wc_get_product_terms( $product->id, 'pa_manufacture', array( 'fields' => 'names' ) ) );
              $description = $product->post->post_content;
              $qty = $value['qty'];
              $price = number_format((float) $value['line_total'], 2, '.', '');
              $partNumber = unserialize($value['part_number']);
              $part_no = array();
              $value_input = $partNumber['value_input'];
              foreach($partNumber['part_number'] as $val)
              {
                if($val == "-")
                {
                 $foo = 1;
                 $part_no[] = "-";
             }
             else
             {
              $subterm_meta_val = get_option("taxonomy_term_$val");
              if($subterm_meta_val['presenter_id'] == "changable")
              {
                $part_no[] = $order_metadata['value_input'];
            }
            else
            {
                $part_no[] = $subterm_meta_val['unit_name'];
            }
        }
        }
        $partno = implode("",$part_no);
        if($foo == 1){
            $dashDiff = explode("-", $partno);
            $final_part_number = $dashDiff[0]?$dashDiff[0].'-'.$value_input.$dashDiff[1]:$partno;
        }else{
            $final_part_number = $partno;
        }

        $final_part_number = $final_part_number?$final_part_number:'';
        $PartNumber[] = $final_part_number;
        $UOM[] = $uom;
        $Line[] = $line;
        $Qty[] = $qty;
        $Price[] = $price;
        $Description[] = $description;
        $UNSPSC[] = $unspsc;
        $ManufacturerName[] = $manufacture;
        $line++;
      }
        $method = 'test';
             // Get cURL resource
        $user_id = get_current_user_id();
        $url = get_user_meta( $user_id, '_returnURL' );
        $url = $url[0]?$url[0]:'https://staging.catalogconnect.com/abcsupplier/suppliers/demo/receivecart.asp';
        //$url = 'http://112.196.26.253/megladonmfg/wp-content/plugins/custom_cable_configuration/api/enterprise_connect_api.php';

        $fields = array(
            'PartNumber' => $PartNumber,
            'UOM' => $UOM,
            'Line' => $Line,
            'Qty' => $Qty,
            'Price' => $Price,
            'Description' => $Description,
            'UNSPSC' => $UNSPSC,
            'ManufacturerName' => $ManufacturerName,
            'method' => 'test'
            );
        $fields_string = http_build_query($fields);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $fields_string.'&method=test',
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
            "postman-token: befa74a8-01db-4cc5-16d4-0943260732e2"
            ),
          ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
 }
function sfws_add_product_link( $link ) {
    global $product;
    $product_id = $product->id;
    $product_meta = get_post_meta($product_id,'configuration_combination', true);
    if(empty($product_meta))
    {
        $simpleURL =  $product->add_to_cart_url();
        if(empty($product->regular_price))
        {
            echo '<a rel="nofollow" href="'.$simpleURL.'" data-quantity="1" data-product_id="'.$product_id.'" data-product_sku="'.$product->sku.'" class="button product_type_simple ajax_add_to_cart">Read more</a>';
        }
        else
        {
            echo '<a rel="nofollow" href="'.$simpleURL.'" data-quantity="1" data-product_id="'.$product_id.'" data-product_sku="'.$product->sku.'" class="button product_type_simple add_to_cart_button ajax_add_to_cart">Add to cart</a>';
        }
    }
    else
    {
     $configuration_combination_raw = get_post_meta( $product_id, 'configuration_combination', true );
     if (!empty($configuration_combination_raw))
     {
        $configuration_combination = unserialize($configuration_combination_raw);
    }
    $config_id = key($configuration_combination);
            // Add Config Page ID here. Change this with live ID
    $page_obj = get_page_by_path('configure-product');
    $config_page_ID = $page_obj->ID;
    echo  '<a href="'. get_permalink($config_page_ID).'?config='.$config_id.'&productId='.$product_id.'&action=selectPartno" class="btn btn-primary">Configure</a>';
}
}
// Outputing a custom button in Single product pages (you need to set the button link)
function single_product_custom_button( ) {
    global $product;
    // WooCommerce compatibility
    if ( method_exists( $product, 'get_id' ) ) {
        $product_id = $product->get_id();
    } else {
        $product_id = $product->id;
    }
    $product_meta = get_post_meta($product_id, 'configuration_combination', true);
    if(!empty($product_meta))
    //if($product->regular_price == 0)
    {
        $configuration_combination_raw = get_post_meta( $product_id, 'configuration_combination', true );
        if (!empty($configuration_combination_raw))
        {
            $configuration_combination = unserialize($configuration_combination_raw);
        }
        $config_id = key($configuration_combination);
        // Add Config Page ID here. Change this with live ID
        $page_obj = get_page_by_path('configure-product');
        $config_page_ID = $page_obj->ID;
        echo  '<a href="'. get_permalink($config_page_ID).'?config='.$config_id.'&productId='.$product_id.'&action=selectPartno" class="btn btn-primary">Configure</a>';
    }
}
// Exclude a certain product attribute on the shop page
function so_39753734_remove_attributes( $attributes ) {
    if( isset( $attributes['pa_unspsc'] ) ){
        unset( $attributes['pa_unspsc'] );
    }
    if( isset( $attributes['pa_uom'] ) ){
        unset( $attributes['pa_uom'] );
    }
    return $attributes;
}
//add_filter( 'woocommerce_get_product_attributes', 'so_39753734_remove_attributes' ); // deprecated
add_filter( 'woocommerce_product_get_attributes', 'so_39753734_remove_attributes' );
// Replace add-to-cart button in Single product pages
add_action( 'woocommerce_single_product_summary', 'removing_addtocart_buttons', 1 );
function removing_addtocart_buttons()
{
    global $product;
    // WooCommerce compatibility
    if ( method_exists( $product, 'get_id' ) )
    {
        $product_id = $product->get_id();
    }
    else
    {
        $product_id = $product->id;
    }
    $product_meta = get_post_meta($product_id, 'configuration_combination', true);
    if(!empty($product_meta))
    //if($product->regular_price == 0)
    {
        //Removing the add-to-cart button
        remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
        remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
        remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
        remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
        //Adding a custom replacement button
        add_action( 'woocommerce_simple_add_to_cart', 'single_product_custom_button', 30 );
        add_action( 'woocommerce_grouped_add_to_cart', 'single_product_custom_button', 30 );
        add_action( 'woocommerce_variable_add_to_cart', 'single_product_custom_button', 30 );
        add_action( 'woocommerce_external_add_to_cart', 'single_product_custom_button', 30 );
        add_action( 'woocommerce_single_product_summary', 'single_product_custom_button', 30 );
        add_action( 'woocommerce_single_variation', 'single_product_custom_button', 20 );
    }
}
//To Remove Sidebar from Shop Page
function so_32165017_conditionally_remove_sidebar(){
    if( is_product_category() || is_shop()){
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
    }
}
//add_action( 'woocommerce_before_main_content', 'so_32165017_conditionally_remove_sidebar' );
// Admin Notice to show error messages
function custom_admin_error_notice() { ?>
<div class="notice notice-error" style="display: none;"><p></p></div>
<?php }
add_action('admin_notices', 'custom_admin_error_notice');
// Admin Notice to show success messages
function custom_admin_success_notice() { ?>
<div class="notice notice-success" style="display: none;"><p></p></div>
<?php }
add_action('admin_notices', 'custom_admin_success_notice');
//Update Custom Price for a product while add to cart
function calculate_gift_wrap_fee( $cart_object ) {
    if( !WC()->session->__isset( "reload_checkout" )) {
        /* custom price */
        $custom_price =  $_SESSION['config_price_sum'];
        $pro_id = $_SESSION['matched_product_id'];
        $config_price_upper_sum  = '';
        $config_price_data  = '';
         // echo '<pre>';
         // print_r($cart_object->cart_contents);
        foreach ( $cart_object->cart_contents as $key => $value ) {
            if($value['data']->price == 0){
                if($value['data']->id == $pro_id && !isset($value['line_total']) )
                {
                    $value['data']->set_price($custom_price);
                }else{
                    if(!isset($value['line_total']) && $value['line_total'] == 0){
                        wc()->cart->remove_cart_item($key);
                        continue;
                    }
                    $config_price_upper  = array();
                        $product_id = $value['product_id'];
                        //Save part data and value of lengthinput;
                        $value_input = '';
                        foreach($value['wdm_user_custom_data_value']['part_number'] as $part_number_key => $part_number)
                        {
                           $term_meta = get_option( "taxonomy_term_$part_number" );
                            $finalPresConfig = $term_meta['presenter_id']; //lengthinput changable
                            $finalUnitConfig = $term_meta['is_unit_type']; // Check is unit checked or not
                            if($finalPresConfig == 'changable'){
                                $value_input = $value['wdm_user_custom_data_value']['value_input'];
                            }elseif($finalUnitConfig){
                                $item_value = $part_number;
                                $per_unit_meta = get_post_meta( $value['product_id'], 'per_unit',true);
                                $per_unit_meta_data_arr = unserialize($per_unit_meta);
                                if(array_key_exists($item_value,$per_unit_meta_data_arr))
                                {
                                    $config_item_price = $per_unit_meta_data_arr[$item_value];
                                }

                            }else{
                                $term_metas = get_option( "taxonomy_term_$part_number" );
                                // print_r($term_metas);
                                $config_price_upper[] = $term_metas['config_price'];
                            }
                        }
                  //  }
                    //Sum of the price
                    $config_price_upper_sum = array_sum($config_price_upper);
                    //Calculation of the price
                    $config_price_data = $config_price_upper_sum + ($value_input * $config_item_price);
                    $value['data']->set_price($config_price_data);
                }
              //  die;
                // Finally, destroy the session.
                //session_destroy();
            }
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'calculate_gift_wrap_fee', 99 );
/*function custom_wc_add_fee() {
    $user_id = wp_get_current_user()->id;
    $discount = get_user_meta($user_id,'user_discount');
    $cart_total = WC()->cart->subtotal*$discount[0]/100;
    WC()->cart->add_fee( 'Discount', -$cart_total );
}
add_action( 'woocommerce_cart_calculate_fees','custom_wc_add_fee' );*/

function woo_add_cart_fee() {
    global $woocommerce;
    $extra_shipping_cost = 0;
    //Loop through the cart to find out the extra costs
    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
        //Get the product info
        $_product = $values['data'];
        //Get the custom field value
        $custom_shipping_cost = get_post_meta($_product->id, 'custom_shipping_cost', true);
        //Adding together the extra costs
        $extra_shipping_cost = $extra_shipping_cost + $custom_shipping_cost;
    }
    //Lets check if we actually have a fee, then add it
    if ($extra_shipping_cost) {
        $woocommerce->cart->add_fee( __('Shipping Cost', 'woocommerce'), $extra_shipping_cost );
    }
}
add_action( 'woocommerce_before_calculate_totals', 'woo_add_cart_fee');
//Remove Dashboard and downloads link from woo commerce accounts page
function custom_my_account_menu_items( $items ) {
    unset($items['downloads']);
    unset($items['dashboard']);
    unset($items['customer-logout']);
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );
//Override WooCommerce Template
add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
function woo_adon_plugin_template( $template, $template_name, $template_path ) {
 global $woocommerce;
 $_template = $template;
 if ( ! $template_path )
    $template_path = $woocommerce->template_url;
$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/template/woocommerce/';
    // Look within passed path within the theme - this is priority
$template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
      )
    );
    // Modification: Get the template from this plugin, if it exists
if( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;
    // Return what we found
if ( ! $template )
    $template = $_template;
return $template;
}
add_action( 'template_redirect', 'bbloomer_add_product_to_cart' );
function bbloomer_add_product_to_cart() {
                // select ID
    $product_id = 851;
                //check if product already in cart
    if ( WC()->cart->get_cart_contents_count() == 0 ) {
                        // if no products in cart, add it
        WC()->cart->add_to_cart( $product_id );
    }
}
//Add Cart button in menu
add_filter('wp_nav_menu_items','sk_wcmenucart', 10, 2);
function sk_wcmenucart($menu, $args) {
    // Check if WooCommerce is active and add a new item to a menu assigned to Primary Navigation Menu location
    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'main-menu' !== $args->theme_location )
        return $menu;
  ob_start();
  global $woocommerce;
  $cart_url = esc_url( wc_get_cart_url() );
  //$cart_url = $woocommerce->cart->get_cart_url();
  $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
  $cart_contents_count = $woocommerce->cart->cart_contents_count;
        //$cart_contents = sprintf(_n('%d item', '%d items', $cart_contents_count, 'your-theme-slug'), $cart_contents_count);
  $cart_total = $woocommerce->cart->get_cart_total();
  // if ( is_user_logged_in() )
  // {
     $menu_item = '<li class="right"><a class="wcmenucart-contents" href="'. $cart_url .'" >';
     $menu_item .= '<i class="fa fa-shopping-cart"></i> ';
     $menu_item .= 'View Cart';
     $menu_item .= '<span class="cart-contents-count">('.$cart_contents_count.')</span>';
     $menu_item .= '</a></li>';
 // }
 echo $menu_item;
 $social = ob_get_clean();
 return $menu . $social;
}
//Update cart qty when ever cart is changed
add_action('wp_ajax_cart_count', 'custom_cart_count');
add_action('wp_ajax_nopriv_cart_count', 'custom_cart_count');
function custom_cart_count() {
    echo WC()->cart->cart_contents_count;
    wp_die();
}
function my_header_add_to_cart_fragment( $fragments ) {
    ob_start();
    $count = WC()->cart->cart_contents_count;
    ?>
    <a class="wcmenucart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><i class="fa fa-shopping-cart"></i> View Cart <span class="cart-contents-count">(<?php echo esc_html( $count ); ?>)</span>
  </a>
  <?php
  $fragments['a.wcmenucart-contents'] = ob_get_clean();
  return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );
//Update message on checkout page when cart is empty.
function filter_woocommerce_update_order_review_fragments( $array ) {
    // make filter magic happen here...
    if ( WC()->cart->is_empty() ) {
        $array = array(
            'form.woocommerce-checkout' => '<div class="woocommerce-info">' . __( 'Checkout is not available whilst your cart is empty.', 'woocommerce' ) . '</div><p class="cart-empty">'. __( 'Your cart is currently empty.', 'woocommerce' ).'<p class="return-to-shop"><a class="button wc-backward" href="'. get_site_url() .'/products">'.__( 'Return To Products', 'woocommerce' ) .' </a></p>'
            );
    }
    return $array;
}
add_filter( 'woocommerce_update_order_review_fragments', 'filter_woocommerce_update_order_review_fragments', 10, 1 );


// Woocommerce Edit Profile Page - Custom Fields

//add_action( 'user_profile_update_errors','wooc_validate_custom_field', 10, 1 );
// or
add_action( 'woocommerce_save_account_details_errors','wooc_validate_custom_field', 10, 1 );
function wooc_validate_custom_field( $args )
{
	/*if(empty($_POST['billing_title']))
	{
		$args->add( 'error', __( 'Title is required', 'woocommerce' ),'');
	}*/
	if (empty($_POST['billing_phone']))
    {
        $args->add( 'error', __( 'Phone Number is required', 'woocommerce' ),'');
    }
    else
    {
		if(strlen($_POST['billing_phone']) > 16 || !is_numeric($_POST['billing_phone']))
		{
			$args->add( 'error', __( 'Invalid Phone Number', 'woocommerce' ),'');
		}
	}

	/*if (empty($_POST['billing_company']))
    {
        $args->add( 'error', __( 'Company is required', 'woocommerce' ),'');
    }
    if (empty($_POST['billing_company_website']))
    {
        $args->add( 'error', __( 'Company Website is required', 'woocommerce' ),'');
    }
    else
    {
		$url = $_POST['billing_company_website'];
		$url = strpos($url, 'http') !== 0 ? "http://$url" : $url;
		if (filter_var($url, FILTER_VALIDATE_URL) === FALSE)
		{
			$args->add( 'error', __( 'Please enter a valid url.', 'woocommerce' ),'');
		}
	}*/
}


add_action( 'woocommerce_save_account_details', 'my_woocommerce_save_account_details' );
function my_woocommerce_save_account_details( $user_id ) {
	/*if (isset($_POST['billing_title']))
    {
        update_user_meta($user_id, 'billing_title', sanitize_text_field($_POST['billing_title']));
    }*/
    if (isset($_POST['billing_first_name']))
    {
        update_user_meta($user_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
    }
    if (isset($_POST['billing_last_name']))
    {
        update_user_meta($user_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
    }
    if (isset($_POST['billing_phone']))
    {
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
    }
    /*if (isset($_POST['billing_company']))
    {
        update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['billing_company']));
    }
    if (isset($_POST['billing_company_website']))
    {
        update_user_meta($user_id, 'billing_company_website', sanitize_text_field($_POST['billing_company_website']));
    }*/
    /*if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $uploadedfile = $_FILES['company_logo'];
    $upload_overrides = array( 'test_form' => false );
    add_filter('upload_dir', 'my_upload_dir');
    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
    remove_filter('upload_dir', 'my_upload_dir');
    if ( $movefile )
    {
        $company_logo = $uploadedfile['name'];
        update_user_meta($user_id, 'company_logo', sanitize_text_field($company_logo));
        //return "File is valid, and was successfully uploaded.";
       // var_dump( $movefile);
    }
    else
    {
        return "Possible file upload attack!";
    } */
}
// Woocommerce Edit Profile Page - Custom Fields


function my_upload_dir($upload)
{
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
   // $target_dir = "/ultimatemember/".$user_id."/";
    $upload['subdir'] = '/ultimatemember/' . $user_id . $upload['subdir'];
    $upload['path']   = $upload['basedir'] . $upload['subdir'];
    $upload['url']    = $upload['baseurl'] . $upload['subdir'];
    return $upload;
}

//Remove password strength meter on edit account page.
function wc_ninja_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}
add_action( 'wp_print_scripts', 'wc_ninja_remove_password_strength', 100 );

//Remove Woocommerce password strength settings from edit account page.
function reduce_woocommerce_min_strength_requirement( $strength ) {
    return 0;
}
add_filter( 'woocommerce_min_password_strength', 'reduce_woocommerce_min_strength_requirement' );


//add_action('um_submit_form_errors_hook_','um_custom_validate_username', 999, 1);
function um_custom_validate_username( $args ) {
	global $ultimatemember;
	$url = $args['company_website'];
	$url = strpos($url, 'http') !== 0 ? "http://$url" : $url;
	if(filter_var($url, FILTER_VALIDATE_URL)) {
    //valid
	} else {
		//not valid
		$ultimatemember->form->add_error( 'company_website', 'Your username must not contain the word admin.' );
	}
	/*if ( isset( $args['user_login'] ) && strstr( $args['user_login'], 'admin' ) ) {
		$ultimatemember->form->add_error( 'user_login', 'Your username must not contain the word admin.' );
	}*/
}


//Allow Svg uploads
function add_svg_to_upload_mimes( $upload_mimes ) {
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );

//Add same product to woocommerce cart without changing the quantity
function namespace_force_individual_cart_items( $cart_item_data, $product_id ) {
  $unique_cart_item_key = md5( microtime() . rand() );
  $cart_item_data['unique_key'] = $unique_cart_item_key;

  return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'namespace_force_individual_cart_items', 10, 2 );


// Add Woocommerce Login /Logout Links
// add_filter( 'wp_nav_menu_items', 'add_loginout_link', 10, 2 );
function add_loginout_link( $items, $args ) {
    if (is_user_logged_in() && $args->theme_location == 'main-menu') {
		$items .= '<li><a href="' . get_permalink( woocommerce_get_page_id( 'myaccount' ) ) . '">My Account</a></li>';
        $items .= '<li><a href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'">Log Out</a></li>';
    }
    elseif (!is_user_logged_in() && $args->theme_location == 'main-menu') {
        $items .= '<li><a href="' . get_permalink( woocommerce_get_page_id( 'myaccount' ) ) . '">Log In</a></li>';
    }
    return $items;
}


//Customizes the page to redirect users to after having connected with Social Login
function oa_social_login_set_redirect_url ($url, $user_data)
{
    // $user_data is an object that represents the current user
    // The format is similar to the data returned by $user_data = get_userdata ($userid);
    // https://codex.wordpress.org/Function_Reference/get_userdata

    // Redirects users to http(s)://www.your-wordpress-blog.com/members/%user_login%
    echo "<pre>"; print_r($user_data);
    print_r($user_data->ID);
    $user_id = $user_data->ID;
    $update = update_user_meta( $user_id, 'wcemailverified', true );
    echo $update;

    return  get_site_url(null, '/members/' . $user_data->user_login);
}

// Applies the redirection filter to users that register using Social Login
add_filter('oa_social_login_filter_registration_redirect_url', 'oa_social_login_set_redirect_url', 10, 2);

// Applies the redirection filter to users that login using Social Login
add_filter('oa_social_login_filter_login_redirect_url', 'oa_social_login_set_redirect_url', 10, 2);


function wc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user
	$role = $user->roles[0];
	$dashboard = admin_url();
	$myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
	if( $role == 'administrator' ) {
		//Redirect administrators to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'shop-manager' ) {
		//Redirect shop managers to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'editor' ) {
		//Redirect editors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'author' ) {
		//Redirect authors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'customer' || $role == 'subscriber' ) {
		//Redirect customers and subscribers to the "My Account" page
		$redirect = home_url('/products/');
	} else {
		//Redirect any other role to the previous visited page or, if not available, to the home
		$redirect = wp_get_referer() ? wp_get_referer() : home_url();
	}
	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );


function oa_social_login_filter_redirect_for_new_users ($url, $user_data)
{
//Force the url to something else
$url = home_url('/products/');

//New users will be redirected here
return $url;
}
add_filter('oa_social_login_filter_registration_redirect_url', 'oa_social_login_filter_redirect_for_new_users', 10, 2);

//Redirection for existing users
function oa_social_login_filter_redirect_for_existing_users ($url, $user_data)
{
//Force the url to something else
$url = home_url('/products/');

//Returning users will be redirected here
return $url;
}
add_filter('oa_social_login_filter_login_redirect_url', 'oa_social_login_filter_redirect_for_existing_users', 10, 2);

//Add Custom Search button in menu
add_filter('wp_nav_menu_items','custom_search', 10, 2);
function custom_search($menu, $args) {
    // Check if WooCommerce is active and add a new item to a menu assigned to Primary Navigation Menu location
    if ( 'menu2' !== $args->theme_location )
        return $menu;
  ob_start();

     $menu_item = '<form name="custom_search" id="custom_search" method="POST" action="'.get_site_url().'/search-results/">';
     $menu_item .= '<input type="text" name="search_text" id="search_text" placeholder="Search Here" required>';
     $menu_item .= '<button type="submit" name="search" value="" ><i class="fa fa-search"></i></button>';
     $menu_item .= '</form>';

     
 echo $menu_item;
 $social = ob_get_clean();
 return $menu . $social;
}

/* Remove Display name from edit account page */
add_filter('woocommerce_save_account_details_required_fields', 'wc_save_account_details_required_fields' );
function wc_save_account_details_required_fields( $required_fields ){
    unset( $required_fields['account_display_name'] );
    return $required_fields;
}
?>
