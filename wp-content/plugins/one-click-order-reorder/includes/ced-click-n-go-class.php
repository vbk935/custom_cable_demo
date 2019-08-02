<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'Ced_Click_n_Go' ) ) {
	class Ced_Click_n_Go {
	
		/**
		 * Functionalities initializes over here
		 *
		 * @author Cedcommerce
		 * @access public
		 * @return void
		 */
		public function __construct() {
			global $woocommerce;
			register_deactivation_hook(__FILE__, array ( $this, CNG_PREFIX.'_admin_notices' ) );
			add_action( 'admin_enqueue_scripts', array ( $this, CNG_PREFIX.'_admin_enqueue' ) );
			add_action( 'wp_enqueue_scripts', array ( $this, CNG_PREFIX.'_enqueue' ) );
		
			//Adding filter to add a button on my-account page
			add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, CNG_PREFIX.'_add_button' ), 10, 2 );	
			add_action( 'wp_ajax_get_order_cart', array( $this, CNG_PREFIX.'_prefix_ajax_get_order_cart') );
			add_action( 'wp_ajax_nopriv_get_order_cart', array( $this, CNG_PREFIX.'_prefix_ajax_get_order_cart' ) );
			add_action( 'woocommerce_order_details_after_order_table', array( $this,CNG_PREFIX.'_add_edit_order_button' ), 10, 1 );
			add_action('after_setup_theme',array($this,'ced_ocor_close_know_more_email'));
			
			/**
			 * Get all products for an order.
			 */
			add_action ( 'wp_ajax_get_oreder_products', array( $this, CNG_PREFIX . '_get_oreder_products' ) );
			add_action ( 'wp_ajax_nopriv_get_oreder_products', array( $this, CNG_PREFIX . '_get_oreder_products' ) );
		
			/**
			 * Get same order and place again.
			 */
			add_action ( 'wp_ajax_get_same_order_cart', array( $this, CNG_PREFIX . '_prefix_ajax_get_same_order_cart' ) );
			add_action ( 'wp_ajax_nopriv_get_same_order_cart', array( $this, CNG_PREFIX . '_prefix_ajax_get_same_order_cart' ) );
			add_action('wp_ajax_ced_ocor_send_mail',array($this,'ced_ocor_send_mail'));
		}
		

		function ced_ocor_close_know_more_email()
		{
			if(isset($_GET["ced_ocor_close"]) && $_GET["ced_ocor_close"]==true)
			{
				unset($_GET["ced_ocor_close"]);
				if(!session_id())
					session_start();
				$_SESSION["ced_ocor_hide_email"]=true;
			}
		}

		function ced_ocor_send_mail()
		{
			if(isset($_POST["flag"]) && $_POST["flag"]==true && !empty($_POST["emailid"]))
			{
				$to = "support@cedcommerce.com";
				$subject = "Wordpress Org Know More";
				$message = 'This user of our woocommerce extension "One Click Order Re-Order" wants to know more about marketplace extensions.<br>';
				$message .= 'Email of user : '.$_POST["emailid"];
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$flag = wp_mail( $to, $subject, $message);	
				if($flag == 1)
				{
					echo json_encode(array('status'=>true,'msg'=>__('Soon you will receive the more details of this extension on the given mail.',"one-click-order-reorder")));
				}
				else
				{
					echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occured.Please try again.',"one-click-order-reorder")));
				}
			}
			else
			{
				echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occured.Please try again.',"one-click-order-reorder")));
			}
			wp_die();
		}
		/**
		 * Adds Re-Order button at Order's Detail Page
		 * 
		 * @name ced_cng_add_edit_order_button()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_cng_add_edit_order_button( $order ) {
		if(WC()->version<'3.0.0')
				{ 	if ( ! $order->has_status( 'completed' ) ) {
				?>
				<p>
					<a class="button ced_my_account_reorder" href="javascript:void(0);" data-order_id="<?php echo $order->id;?>">
						<?php _e( 'Re-Order', 'one-click-order-reorder' );?>
					</a>
				</p>
				<?php 

				$settings = get_option( 'ced_ocor_general_settings', false );
				if ( !empty( $settings ) ) {
					if ( $settings[ 'same_order_btn' ] == '1' ) {?>
						<p>
							<a class="button ced_my_account_place_same_order" href="javascript:void(0);" data-order_id="<?php echo $order->id;?>">
								<?php _e('Place Same Order','one-click-order-reorder');?>
							</a>
						</p>
					<?php 
					}
				}
				?>
			<?php
			}
}
		else {

			if ( ! $order->has_status( 'completed' ) ) {
				?>
				<p>
					<a class="button ced_my_account_reorder" href="javascript:void(0);" data-order_id="<?php echo $order->get_id();?>">
						<?php _e( 'Re-Order', 'one-click-order-reorder' );?>
					</a>
				</p>
				<?php 

				$settings = get_option( 'ced_ocor_general_settings', false );
				if ( !empty( $settings ) ) {
					if ( $settings[ 'same_order_btn' ] == '1' ) {?>
						<p>
							<a class="button ced_my_account_place_same_order" href="javascript:void(0);" data-order_id="<?php echo $order->get_id();?>">
								<?php _e('Place Same Order','one-click-order-reorder');?>
							</a>
						</p>
					<?php 
					}
				}
				?>
			<?php
			}
		}	
}
		
		/**
		 * Enqueues the scripts files
		 * 
		 * @access public
		 * @author CedCommerce
		 */
		function ced_cng_enqueue() {
			global $woocommerce, $wp_scripts;

			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-core' );
			$ajax_nonce = wp_create_nonce( "ced-cng-re-order-seurity-string" );
			wp_enqueue_style( CNG_PREFIX . '-style', CEDCOMMERCE_CNG_ORDER_URL . 'assets/css/style.min.css', '', CNG_VERSION, 'all' );
			wp_enqueue_script ( 
				CNG_PREFIX . '_cart', 
				CEDCOMMERCE_CNG_ORDER_URL . 'assets/js/ced_cng_cart.min.js', 
				array ( 'jquery', 'jquery-ui-draggable' ), 
				CNG_VERSION,
				true
			);
			
			$atbBtnText = __( 'Add to basket', CNG_TXTDOMAIN );
			$rfbBtnText = __( 'Remove from basket', CNG_TXTDOMAIN );
			$settings = get_option( 'ced_ocor_general_settings', false );
			if ( ! empty( $settings ) ) {
				if ( array_key_exists( 'atbBtnText', $settings ) and ! empty( $settings[ 'atbBtnText' ] ) ) {
					$atbBtnText = $settings[ 'atbBtnText' ];
				}

				if ( array_key_exists( 'rfbBtnText', $settings ) and ! empty( $settings[ 'rfbBtnText' ] ) ) {
					$rfbBtnText = $settings[ 'rfbBtnText' ];
				}
			}

			// Localize the script with new data
			$myaccount_page_id = get_option ( 'woocommerce_myaccount_page_id' );
			if ( $myaccount_page_id ) {
				$myaccount_page_url = get_permalink ( $myaccount_page_id );
			}
			$checkout_url = wc_get_page_permalink( 'checkout' );
			
			$ajax_nonce = wp_create_nonce( "ced-cng-ajax-seurity-nonce" );
			$translation_array = array (
				'ajaxurl' 				=> admin_url ( 'admin-ajax.php' ),
				'plugi_dir_url' 		=> CEDCOMMERCE_CNG_ORDER_URL,
				'cart_url' 				=> $woocommerce->cart->get_cart_url(),
				'account_url'			=> $myaccount_page_url,
				'checkouturl'			=> $checkout_url,
				'ajax_nonce'			=> $ajax_nonce,
				'product_not_exist' 	=> __( 'All products of this order is no longer exist in our store.', 'one-click-order-reorder' ),
				'exclude_products_head' => __( 'Exclude products from this order', 'one-click-order-reorder' ),
				'exc_basket_item_head' 	=> __( 'Exclude products from your basket', 'one-click-order-reorder' ),
				'exclude' 				=> __( 'Exclude', 'one-click-order-reorder' ),
				'image' 				=> __( 'Image', 'one-click-order-reorder' ),
				'out_of_stock_desc' 	=> __( 'This product is out of stock, so it would be excluded from order.', 'one-click-order-reorder' ),
				'exclude_desc' 			=> __( 'Click on this checkbox to exclude this product from reordering.', 'one-click-order-reorder' ),
				'product_name' 			=> __( 'Product name', 'one-click-order-reorder' ),
				'stock' 				=> __( 'Stock', 'one-click-order-reorder' ),
				'quantity' 				=> __( 'Quantity', 'one-click-order-reorder' ),
				'submit' 				=> __( 'Checkout', 'one-click-order-reorder' ),
				'atc' 					=> __( 'Add to cart', 'one-click-order-reorder' ),
				'close' 				=> __( 'Close', 'one-click-order-reorder' ),
				'no_items' 				=> __( 'No items found.', 'one-click-order-reorder' ),
				'atbBtnText'			=> $atbBtnText,
				'rfbBtnText'			=> $rfbBtnText
			);
			wp_localize_script ( CNG_PREFIX . '_cart', 'global_var', $translation_array );
			wp_enqueue_script ( CNG_PREFIX . '_cart' );
		}

		/**
		 * Enqueues the scripts files
		 * 
		 * @access public
		 * @author CedCommerce
		 */
		function ced_cng_admin_enqueue() {
			if ( ! is_admin() ) {
				return false;
			}

			$current_screen = get_current_screen();
			if ( empty( $current_screen ) ) {
				return ;
			}

			

			if ( $current_screen->id != 'woocommerce_page_wc-ocor-settings' ) {
				return;
			}
			

			wp_enqueue_style( 'ced-ocor-select2-css', plugins_url( 'woocommerce/assets/css/select2.css' ) );
			wp_enqueue_script( 'ced-ocor-select2', plugins_url( 'woocommerce/assets/js/select2/select2.min.js' ), array( 'jquery' ), CNG_VERSION, true ); 

			wp_enqueue_script('ced-ocor-custom-admin', CEDCOMMERCE_CNG_ORDER_URL.'assets/js/ced_cng_custom_admin.js', array('jquery'), CNG_VERSION, true );

			wp_localize_script('ced-ocor-custom-admin','ajax_url',admin_url('admin-ajax.php'));

			wp_enqueue_style( CNG_PREFIX . '_admin', CEDCOMMERCE_CNG_ORDER_URL . 'assets/css/cng-admin.min.css', '', CNG_VERSION, 'all' );

			wp_enqueue_script ( 
				CNG_PREFIX . '_admin', 
				CEDCOMMERCE_CNG_ORDER_URL . 'assets/js/ced_cng_admin.min.js', 
				array ( 'jquery' ), 
				CNG_VERSION,
				true
			);
			wp_enqueue_style('ced-ocor-custom-style-1',CEDCOMMERCE_CNG_ORDER_URL .'assets/css/ced_ocor_custom.css','',CNG_VERSION, 'all');
			
			
			$ajax_nonce 		= wp_create_nonce( "ced-cng-ajax-seurity-nonce" );
			$translation_array 	= array (
				'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
				'nonce_check'	=> $ajax_nonce
			);

			wp_localize_script ( CNG_PREFIX . '_admin', 'globals', $translation_array );
			wp_enqueue_script ( CNG_PREFIX . '_admin' );
			add_thickbox();
		}
		
		/**
		 * Adds a button next to each order of name "Re-Order".
		 *
		 * @param array $actions
		 * @param array $order
		 * @return array $actions
		 * @author CedCommerce
		 */
		function ced_cng_add_button( $actions, $order ) {
			$button_text = __ ( 'Re-Order', 'one-click-order-reorder' );
			if(WC()->version<'3.0.0')
				{

			$actions[ 'ced_my_account_reorder' ] = array (
				'url' => $order->id,
				'name' => apply_filters ( 'cng_change_button_text', $button_text ) 
			);
			}
			else{
				$actions[ 'ced_my_account_reorder' ] = array (
				'url' => $order->get_id(),
				'name' => apply_filters ( 'cng_change_button_text', $button_text ) 
			);


			}
			$settings = get_option( 'ced_ocor_general_settings', false );
			if ( !empty( $settings ) ) {
				if ( $settings[ 'same_order_btn' ] == '1' ) {
					$btn_text = __ ( 'Place same order', 'one-click-order-reorder' );
					if(WC()->version<'3.0.0')
				{	


						$actions[ 'ced_my_account_place_same_order' ] = array (
							'url' => $order->id,
							'name' => apply_filters ( 'cng_change_button_text', $btn_text )
						);
				}
				else{
					$actions[ 'ced_my_account_place_same_order' ] = array (
						'url' => $order->get_id(),
						'name' => apply_filters ( 'cng_change_button_text', $btn_text )
					);



				}
				}
			}
			
			return $actions;
		}
		
		/**
		 * Fetches all products from an order.
		 * @author CedCommerce
		 */
		function ced_cng_get_oreder_products() {
			$order_id 	= $_POST[ 'order_id' ];
			$order = new WC_Order ( trim ( $order_id ) );

			if(WC()->version < '3.0.0')
			{

				$order_idd =  $order->id;
				if ( empty ( $order_idd ) ) {
					exit( 'failed' );
				}
			}
			else
			{
				$order_idd =  $order->get_id();
				if ( empty ($order_idd ) ) {
					exit( 'failed' );
				}

			}


			$order_items = array();
			foreach( $order->get_items() as $product_info ) {
				$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $product_info ['product_id'] );
				$items 		= wc_get_product ( $product_id );
				
				if ( ! $items || empty ( $items ) ) {
					$order_items[ $product_id ][ 'availability' ] = 'not_exist';
					continue;
				} else if ( $items->post->post_status != 'publish' ) {
					$order_items[ $product_id ][ 'availability' ] = 'not_availale';
				} else {
					$order_items[ $product_id ][ 'availability' ] = 'available';
				}

				$order_items[ $product_id ][ 'title' ] 		= get_the_title( $product_id );
				$order_items[ $product_id ][ 'permalink' ] 	= get_the_permalink( $product_id );
				$order_items[ $product_id ][ 'qty' ] 		= ( int ) $product_info[ 'qty' ];
				$order_items[ $product_id ][ 'image' ] 		= $items->get_image();
				if ( ! $items->is_in_stock () ) {
					$order_items[ $product_id ][ 'stock' ] = 'out_of_stock';
				} else {
					$order_items[ $product_id ][ 'stock' ] = 'in_stock';
				}
			}
			
			echo json_encode( 
				array(
					'status' => 'ok',
					'prodcuts' => $order_items,
				)
			);
			die();
		}
		
		/**
		 * ced_cng_prefix_ajax_get_order_cart
		 *
		 * Handles Ajax Request and Fetches Prvious orders done By a user,
		 * after that adds to cart again of previous order
		 *
		 * @access public
		 * @author CedCommerce
		 */
		function ced_cng_prefix_ajax_get_order_cart() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'nonce_check' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$order_id = $_POST[ 'order_id' ];
			if ( WC ()->cart->get_cart_contents_count() ) {
				WC ()->cart->empty_cart ();
			}
			$error = array();
			$order = new WC_Order ( trim ( $order_id ) );
			if(WC()->version < '3.0.0')
			{
				$order_iddd =  $order->id;
				if ( empty ( $order_iddd ) ) {
					exit( 'failed' );
				}
			}
			else
			{
				$order_iddd =  $order->get_id();
				if ( empty ($order_iddd ) ) {
					exit( 'failed' );
				}

			}

			foreach ( $order->get_items() as $product_info ) {
				$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $product_info ['product_id'] );
				$qty = ( int ) $product_info ['qty'];
				$all_variations = array ();
				$variation_id = ( int ) $product_info[ 'variation_id' ];
			
				$cart_product_data = apply_filters ( 'woocommerce_order_again_cart_item_data', array (), $product_info, $order );
				foreach ( $product_info ['item_meta'] as $product_meta_name => $product_meta_value ) {
					if ( taxonomy_is_product_attribute( $product_meta_name ) ) {
						$all_variations [$product_meta_name] = $product_meta_value[0];
					} else {
						if ( meta_is_product_attribute( $product_meta_name, $product_meta_value[0], $product_id ) ) {
							$all_variations[ $product_meta_name ] = $product_meta_value[0];
						}
					}
				}
			
				// Add to cart validation
				if (! apply_filters ( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $all_variations, $cart_product_data )) {
					continue;
				}
			
				// Checks availability of products
				$array = wc_get_product( $product_id );
			
				// Add to cart order products
				$add_to_cart = WC ()->cart->add_to_cart ( $product_id, $qty, $variation_id, $all_variations, $cart_product_data );
			}
			// Checks for success or errors
			if ( $add_to_cart ) {
				// Message to be shown when items added to cart
				$success 	= __ ( 'The items are added to cart from your previous order .', 'one-click-order-reorder' );
				$notice 	= wc_add_notice ( apply_filters ( 'cng_added_to_cart_msg', $success ) );
				exit( 'success' );
			} else { 
				// Message to be shown when items not added to cart
				$error 		= __ ( 'Something went wrong, items couldn\'t added to cart ', 'one-click-order-reorder' );
				$notice 	= wc_add_notice ( apply_filters ( 'cng_atc_error', $error ), 'error' );
				exit( 'failed' );
			}
		}
		
		/**
		 * ced_cng_prefix_ajax_get_same_order_cart
		 * handles same order to be placed again
		 * @name ced_cng_prefix_ajax_get_same_order_cart
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 * @access public
		 * @return void
		 */
		function ced_cng_prefix_ajax_get_same_order_cart() {
			$check_ajax_nonce = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( ! $check_ajax_nonce ) {
				exit( 'failed' );
			}

			$order_id 			= $_POST[ 'order_id' ];
			$ajax_nonce 		= $_POST[ 'ajax_nonce' ];
			$excluded_products 	= $_POST[ 'excluded_products' ];
			$quantities 		= $_POST[ 'quantities' ];
			$error 				= false;
			$added_names 		= '';
			$failed_names 		= '';
			$order = new WC_Order( trim( $order_id ) );
			if ( WC ()->cart->get_cart_contents_count() ) {
				WC ()->cart->empty_cart();
			}

			foreach ( $order->get_items() as $product_info ) {
				$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $product_info[ 'product_id' ] );
				if ( !empty( $excluded_products ) ) {
					if ( in_array( $product_id, $excluded_products ) ) {
						continue;
					}
				}

				$qty = ! empty( $quantities ) ? ( int ) $quantities[ $product_id ] : ( int ) $product_info[ 'qty' ];
				$all_variations = array ();
				$variation_id = ( int ) $product_info[ 'variation_id' ];
				$cart_product_data = apply_filters ( 'woocommerce_order_again_cart_item_data', array(), $product_info, $order );
		
				foreach ( $product_info[ 'item_meta' ] as $product_meta_name => $product_meta_value ) {
					if ( taxonomy_is_product_attribute( $product_meta_name ) ) {
						$all_variations[ $product_meta_name ] = $product_meta_value[0];
					} else {
						if ( meta_is_product_attribute( $product_meta_name, $product_meta_value [0], $product_id ) ) {
							$all_variations[ $product_meta_name ] = $product_meta_value[0];
						}
					}
				}

				if ( ! apply_filters ( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $all_variations, $cart_product_data ) ) {
					continue;
				}

				$item_array = wc_get_product ( $product_id );

				$add_to_cart = WC ()->cart->add_to_cart ( $product_id, $qty, $variation_id, $all_variations, $cart_product_data );
				if( !$add_to_cart ) {
					$error = true;
				}
			}
			if ( !$error ) {
				// Message to be shown when items added to cart
				$success = __ ( 'The items are added to cart from your previous order. You can place this order now.', 'one-click-order-reorder' );
				$notice = wc_add_notice ( apply_filters ( 'cng_added_to_cart_msg', $success ) );
				exit( 'success' );
			}
		}
	}
	$_GLOBALS[ 'ced_click_n_go' ] = new Ced_Click_n_Go();
}
?>