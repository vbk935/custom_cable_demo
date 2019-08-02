<?php
/*
Plugin Name: WooCustomers
Plugin Script: woocustomers.php
Plugin URI: http://woocustomers.com
Description: Customer profiles for woocommerce Custoemrs, both guests and registered users.
Version: 1.0-beta
License: GPL V3
Author: Design with Purpose
Text Domain: woocustomers
Author URI: http://woocustomers.com


Copyright 2015 [Design with purpose](http://woocustomers.com)

Commercial users are requested to, but not required to contribute, promotion, 
know-how, or money to plug-in development or to woocustomers.dev. 

You should have received a copy of the GNU General Public License.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

class woocustomers{
	 
	//Start
	function __construct() {
		include(dirname( __FILE__ ) ."/woocustomers_list_table.php");
		add_action('admin_init', array($this, 'admin_init') );
		add_action('admin_enqueue_scripts', array($this, 'admin_head') );
		add_action('admin_menu', array($this, 'listings_menus'));
		add_action( 'admin_bar_menu', array($this, 'modify_admin_bar') );
		
		add_action( 'woocommerce_checkout_order_processed', array($this,"woocustomers_new_data"), 10, 2);
		add_action( 'before_delete_post', array($this,"woocustomers_delete_order"), 10, 1);

	}
	
	function admin_init(){
		$this->woocustomers_load_constants();
		if(isset($_POST["regenerate_customer"]) && is_numeric($_POST["regenerate_customer"]))
			$this->woocustomers_build_data();
	}
	
	function woocustomers_load_constants(){
		global $wpdb;
		
		if ( !defined( 'WOOCUSTOMERS_TABLE_BASE' ) )
			define( 'WOOCUSTOMERS_TABLE_BASE', "woocustomers" );
		if ( !defined( 'WOOCUSTOMERS_ORDER_TABLE_BASE' ) )
			define( 'WOOCUSTOMERS_ORDER_TABLE_BASE', "woocustomers_orders" );

		define( 'WOOCUSTOMERS_TABLE', $wpdb->prefix.WOOCUSTOMERS_TABLE_BASE );
		define( 'WOOCUSTOMERS_ORDER_TABLE', $wpdb->prefix.WOOCUSTOMERS_ORDER_TABLE_BASE );
		
		$wooc_ver = get_option("woocustomers_version");
		if(!$wooc_ver){
			update_option("woocustomers_version", true);
			$this->woocustomers_build_data();
		}
	}
	
	
	// Admin Search Bar
		
	function modify_admin_bar(){
		if ( !is_admin())
        return;
		global $wp_admin_bar, $wpdb;
			$wcaba_search_term = $_POST['search'];			
		$wcaba_go_button = '<input type="submit" value="' . __( 'GO', 'woocommerce-admin-bar-addition' ) . '" class="button button-primary wcaba-search-go" /></form>';

		/** Docs/Codex search form */
		$wp_admin_bar->add_menu( array(
			'id'=> 1,
			'parent'=> 'top-secondary',
			'title'  => '
				<style>.woocustomers_admin_search input{height:30px!important;padding:0 10px!important}.woocustomers_admin_search .button,.woocustomers_admin_search .button:hover{color:transparent!important;background:0 0;position:absolute!important;right:9px;border:none;z-index:2;box-shadow:none;cursor:pointer}.woocustomers_admin_search:after{content:"\f179";font:normal normal normal 16px/1 dashicons;color:#A6A6A6;position:absolute;right:18px;top:8px}.woocustomers_admin_search:hover:after{color:#0074a2}</style>
				<form method="post" action="'.admin_url().'admin.php?page=woocustomers" class="woocustomers_admin_search">
			<input type="hidden" name="search_customers" value="1">
			<input type="text" placeholder="' . esc_attr( 'Search Customers', 'woocommerce-admin-bar-addition' ) . '" onblur="this.value=(this.value==\'\') ? \'' . $wcaba_search_term . '\' : this.value;" onfocus="this.value=(this.value==\'' . $wcaba_search_term . '\') ? \'\' : this.value;" value="' . $wcaba_search_term . '" name="search" value="' . esc_attr( 'woocustomers Docs', 'woocommerce-admin-bar-addition' ) . '" class="text wcaba-search-input" />' . $wcaba_go_button,
			'href'   => false,
			'meta'   => array(
				'target' => '',
				'title'  => _x( 'Search woocustomers', 'Translators: For the tooltip', 'woocommerce-admin-bar-addition' )
			)
		));
	}
	
	function admin_head($hook){
		if ( 'woocommerce_page_woocustomers' != $hook ) {
			return;
		}
		wp_enqueue_script('woocustomers', plugin_dir_url( __FILE__ ) . 'js/woocustomers.js', array('jquery'),'', true);
		wp_enqueue_style( 'woocustomers-css',  plugin_dir_url( __FILE__ ) . 'css/woocustomers.min.css' );
	
	}
	
	function listings_menus(){
		$hook = add_submenu_page( 'woocommerce', 'woocustomers', 'Customers', 'manage_woocommerce', 'woocustomers', array($this,'archive_page')); 
	    add_action( "load-$hook", array($this,'add_options') );
	}
 
	function add_options() {
	  global $myListTable;
	  $option = 'per_page';
      $postsperpage = $_POST['wp_screen_options']['value'];
      if($postsperpage <= 0 || $postsperpage == ""){
        $postsperpage = 25;
      }
	  $args = array(
			 'label' => 'woocustomer',
			 'default' => $postsperpage,
			 'option' => 'woocustomers_per_page'
			 );
	  add_screen_option( $option, $args );
	  $myListTable = new woocustomers_List_Table();
	}	

	
	function archive_page(){
		if(@isset($_GET["cust_id"])){
			$this->single_user_page();
			return;
		}
		$myListTable = new woocustomers_List_Table();
		?><div class="wrap">
			<div id="woocustomers">
			  <div class="top-title" style="display: block;">
				<h2>Customer Profiles
				<form method="post" class="regenerate_customer" style="width: 100%;display: inline;">
					<input type="hidden" name="regenerate_customer" value="1">
					<button style="display: inline-block;width: 15%;" id="gen_data" name="gen_data">
						Regenerate all data
	                    <div class="generating" style="display: none;">
	                         <img style="display: inline-block;" src="./images/loading.gif" />
	                    </div>
					</button>
				</form>
				</h2>
			</div>
			<form method="post" id="woocustomers_search_form">
				<input type="hidden" name="search_customers" value="1">
			  <div class="filter-customers">
				<div class="search-filter">
				  <input class="search" placeholder="Search Customer" type="text" name="search" value="<?php echo @$_POST["search"]; ?>">
				  <button class="customer-search">Search</button>
				</div>
			  </div>
			</form>
  <?php
		//wordpress functions which display the records
		$myListTable->prepare_items(); 
		$myListTable->display(); 
		echo '</div></div>'; 
	}
	
	function single_user_page(){
		global $woocommerce, $wpdb;
		$cust_id = $_GET["cust_id"];
							
		$user_meta = array(); 
		$customer = $this->get_customers($cust_id);
		$user = get_user_by( "email", $customer["email"]);
		$orders = $wpdb->get_results("SELECT * FROM ".WOOCUSTOMERS_ORDER_TABLE." WHERE email ='".$customer["email"]."'");
		if(!$user){
			$user = new stdClass();
			$user->error = true;
			if($orders){
				global $woocommerce;
				foreach($orders as $order){
					$order_meta = get_post_meta($order->order_id);
					$order_detail = new WC_Order($order->order_id);
					$order_meta["order_total"] = $order_detail->get_total();
					$order_meta["last_date"] = $order_detail->order_date;
					break;
				}
			}
		}else{
			$user_meta = get_user_meta( $user );
		}
		$user_billing_address_1 = (empty($user_meta)? $order_meta["_billing_address_1"][0]:$user_meta["billing_address_1"][0]);
		$user_city = (empty($user_meta)? $order_meta["_billing_city"][0]:$user_meta["billing_city"][0]);
		$user_country = (empty($user_meta)? $order_meta["_billing_country"][0]:$user_meta["billing_country"][0]);
		$user_address = $user_billing_address_1.', '.$user_city.', '.$user_country;
		
			?>
			<div id="woocustomers">
				<a href="admin.php?page=woocustomers" class="back-to-profiles"> back to customer profiles</a>
			  <div class="customer-details-strip">
				<div class="gravatar"><?php echo get_avatar($customer["email"]); ?></div>
				<div class="customer">
				  <div class="customer-full-name"><?php echo $customer["first_name"].' '.$customer["last_name"]; ?></div>
				  <?php if($user->error == false ):?>
				  <div class="customer-since">Customer Since: &nbsp; <?php echo ((@$user->user_registered && (strlen($user->user_registered) > 0))? date("d M Y", strtotime($user->user_registered)):"NA"); ?></div>
				  <?php endif; ?>
				  <div class="role-icon"><?php echo ((@$user->roles)? "C":"G"); ?></div>
				  <div class="role"><?php echo ((@$user->roles)? $user->roles[0]:"Guest"); ?></div>
				</div>
				<div class="key-details clearfix">
				  <div class="total-purchase-box clearfix">
					<div class="total-purchase value"><?php echo $customer["total_purchases"]." Purchase"; if($customer["total_purchases"] > 1){echo "s";} ?></div>
					<div class="total-purchase label">Total Purchases</div>
				  </div>
				  <div class="total-spend-box clearfix">
					<div class="total-spend value"><?php echo get_woocommerce_currency_symbol(), $customer["total_spent"]; ?></div>
					<div class="total-spend label">Total Spend</div>
				  </div>
				  <div class="last-order-box clearfix">
					<div class="last-order value"><?php echo date("d M Y", strtotime($customer["last_date"])); ?></div>
					<div class="last-order label">Last Order</div>
				  </div>
				</div>
			  </div>
			  <div id="tabs" class="white-box clearfix">
				<div class="tab-menu clearfix">
				  <a href="#details" class="menu-item active">Details</a>
				  <a href="#orders" class="menu-item menu-item-2">Orders</a>
				</div>
				<div id="details" class="details-tab tab clearfix">
					<div class="contact-details clearfix single" id="wooC_form_1">
					  <div>
						<div class="title clearfix">
						  <h3>Contact Details</h3>
						</div>
						<div class="customer-details company clearfix">
						  <div class="label">Company</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_company"][0]:$user_meta["billing_company"][0]); ?></div>	
						</div>
						<div class="customer-details first-name clearfix">
						  <div class="label">First Name</div>
						  <div class="detail"><?php echo $customer["first_name"]; ?></div>
						</div>
						<div class="customer-details last-name clearfix">
						  <div class="label">Last Name</div>
						  <div class="detail"><?php echo $customer["last_name"]; ?></div>
						</div>
						<div class="customer-details email clearfix">
						  <div class="label">Email</div>
						  <div class="detail"><a href="mailto:<?php echo $customer["email"]; ?>"><?php echo $customer["email"]; ?></a></div>
						</div>
						<div class="customer-details phone clearfix">
						  <div class="label">Phone</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_phone"][0]:$user_meta["billing_phone"][0]); ?></div>
						</div>
					  </div>
				  </div>
				  <div class="billing-details clearfix single" id="wooC_form_2">
					  <div>
						<div class="title clearfix">
						  <h3>Billing Address</h3>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Address 1</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_address_1"][0]:$user_meta["billing_address_1"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Address 2</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_address_2"][0]:$user_meta["billing_address_2"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">City</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_city"][0]:$user_meta["billing_city"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Postcode</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_postcode"][0]:$user_meta["billing_postcode"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">State/County</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_state"][0]:$user_meta["billing_state"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Country</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_billing_country"][0]:$user_meta["billing_country"][0]); ?></div>
						</div>
					  </div>
				  </div>
				  <div class="shipping-details clearfix single" id="wooC_form_3">
					  <div>
						<div class="title clearfix">
						  <h3>Shipping Address</h3>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Address 1</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_shipping_address_1"][0]:$user_meta["shipping_address_1"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Address 2</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_shipping_address_2"][0]:$user_meta["shipping_address_2"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">City</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_shipping_city"][0]:$user_meta["shipping_city"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Postcode</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_shipping_postcode"][0]:$user_meta["shipping_postcode"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">State/County</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_shipping_state"][0]:$user_meta["shipping_state"][0]); ?></div>
						</div>
						<div class="customer-details clearfix">
						  <div class="label">Country</div>
						  <div class="detail"><?php echo (empty($user_meta)? $order_meta["_shipping_country"][0]:$user_meta["shipping_country"][0]); ?></div>
						</div>
					  </div>
				  </div>
				  <div class="map-wrapper">
			<?php	if( !$user_billing_address_1 ) {; ?>
					<div class="no-address"> <div>No Billing Address</div> </div>
				<?php } else {	?>
					<a target="blank" href="https://www.google.com/maps?t=m&amp;q=<?php echo $user_address; ?>" class="zoom-disable">&nbsp;</a>
					<iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps?t=m&amp;q=<?php echo $user_address; ?>&amp;output=embed"></iframe>
				<?php } ?>
				</div>
				</div>
				<div id="orders" class="orders-tab tab clearfix">
				  <div class="order-labels clearfix">
					<div class="label-order-number">Order Number</div>
					<div class="label-date">Date</div>
					<div class="label-products">Products</div>
					<div class="label-order-totals">Order Total</div>
				  </div>
				  
				<?php 
				  foreach($orders as $raw_order){
					$order = new wc_order($raw_order->order_id);
					$items = $order->get_items();
					$last_date = get_the_time('Y-m-d', $raw_order->order_id);
					?>
					  <div class="order-details clearfix">
						<div class="order-status <?php echo $order->status ?>"></div>
						<div class="order-details-line clearfix">
						  <div class="order-number"><a href="<?php echo admin_url().'post.php?post='.$raw_order->order_id; ?>&action=edit">Order: #<?php echo $raw_order->order_id ?></a></div>
						  <div class="order-date"><?php echo date("d M Y", strtotime($last_date)); ?></div>
						  <div class="total-products">Total of <?php echo $order->get_item_count(); ?> products</div>
						  <div class="sub-total"><?php echo get_woocommerce_currency_symbol(), $order->get_total(); ?></div>
						</div>
						<?php foreach($items as $item){ ?>
						<div class="order-item">x<?php echo $item ["item_meta"]["_qty"][0].' '.$item["name"]; ?></div>
							<div class="item-sub-total"><?php echo get_woocommerce_currency_symbol(), $item["line_total"]; ?></div>
						<?php } ?>
					  </div>
					<?php } ?>
				</div>
			  </div>
			</div>
	<?php }	
	
	
	//useful funcitons
	function get_customers($cust_id = false){
		global $wpdb;
		if($cust_id && is_numeric($cust_id)){
			return $wpdb->get_row("select * from ".WOOCUSTOMERS_TABLE." WHERE id=".$cust_id, ARRAY_A);
		}else{
			return $wpdb->get_results("select * from ".WOOCUSTOMERS_TABLE, ARRAY_A);
		}
	}
	
	
	function build_order($email, $order_id){
		global $wpdb;
		//check to see if we have this order recorded
		$old_order = $wpdb->get_row("select * from ".WOOCUSTOMERS_ORDER_TABLE." WHERE order_id =".$order_id);
		if($old_order){
			//in case for some reason order ownership is changed
			if($old_order->email != $email){
				$wpdb->query("UPDATE ".WOOCUSTOMERS_ORDER_TABLE." SET email = ".$email." WHERE order_id = ".$order_id);
			}
		}else{
			$wpdb->query("INSERT INTO ".WOOCUSTOMERS_ORDER_TABLE." (order_id, email) VALUES (".$order_id.", '".$email."')");
		}
	}
	
	function woocustomers_delete_order($order_id){
		global $wpdb,$post_type;   
		if ( $post_type != 'shop_order' ) return;
	}
	
	function build_user($order_id, $total_spent, $last_date, $total_purchases = false){
		$email = get_post_meta( $order_id, '_billing_email', true );
		global $wpdb;
		
		$user_info = $wpdb->get_row("select * from ".WOOCUSTOMERS_TABLE." WHERE email ='".$email."'");
		$user_id = 0;
		if($user_info){
			$user_id = $user_info->id;
			if(!$total_purchases)
				$total_purchases = $user_info->total_purchases + 1;
			$total_spent = $user_info->total_spent + $total_spent;
			//make sure this order is actually new. May have moved from processing to complete
			if($last_date != $user_info->last_date)
				$wpdb->query("UPDATE ".WOOCUSTOMERS_TABLE." SET total_purchases = ".$total_purchases.", total_spent = '".$total_spent."', last_date = '".$last_date."' WHERE email = '".$email."'");
		}else{ 
			//is registered?
			$is_user = get_user_by( "email", $email);
			if(@$is_user && @$is_user->ID){
				$user_info = get_userdata($is_user->ID);
				$first = $user_info->first_name;
				$last = $user_info->last_name;
			}else{
				$first = get_post_meta( $order_id, '_billing_first_name', true );
				$last = get_post_meta( $order_id, '_billing_last_name', true );
			}
			$total_purchases = 1;
			$query = "INSERT INTO ".WOOCUSTOMERS_TABLE." (first_name, last_name, email, total_purchases, total_spent, last_date) VALUES ('".$first."', '".$last."', '".$email."', '".$total_purchases."', '".$total_spent."', '".$last_date."')"; 
			
			$check = $wpdb->query($query);
			$user_id = $wpdb->insert_id;
		}
		return $user_id;
	}
    
     function get_posts_fields( $args = array() ) {
  $valid_fields = array(
    'ID'=>'%d', 'post_author'=>'%d',
    'post_type'=>'%s', 
    'post_mime_type'=>'%s',
    'post_title'=>false, 'post_name'=>'%s', 
    'post_date'=>'%s', 'post_modified'=>'%s',
    'menu_order'=>'%d', 'post_parent'=>'%d', 
    'post_excerpt'=>false, 'post_content'=>false,
    'post_status'=>'%s', 'comment_status'=>false, 'ping_status'=>false,
    'to_ping'=>false, 'pinged'=>false, 'comment_count'=>'%d'
  );
  $defaults = array(
    'post_type' => 'post',
    'post_status' => array('publish', 'open'),
    'orderby' => 'post_date',
    'order' => 'DESC',
    'posts_per_page' => get_option('posts_per_page'),
  );
  global $wpdb;
  $args = wp_parse_args($args, $defaults);
  $where = "";
  foreach ( $valid_fields as $field => $can_query ) {
    if($field == "post_status")
    {
        continue;
    }
    if ( isset($args[$field]) && $can_query ) {
      if ( $where != "" )  $where .= " AND ";
      $where .= $wpdb->prepare( $field . " = " . $can_query, $args[$field] );
    }
  }
  if ( isset($args['search']) && is_string($args['search']) ) {
      if ( $where != "" )  $where .= " AND ";
      
        $where .= $wpdb->prepare("post_title LIKE %s", "%" . $args['search'] . "%");
     
  }
  
  if ( isset($args['post_status']) && is_string($args['post_status']) ) {
        if (strpos($args['post_status'],'%') !== false) {
            if ( $where != "" ) { $where .= " AND "; }
            $search_param = explode(',', $args['post_status']);
              if( count($search_param) > 0 ){
                    for($i =0; $i< count($search_param); $i++){ 
                        
                    if($i == 0 ){
                        $where .= $wpdb->prepare("post_status LIKE %s", "" . $search_param[$i] . "");
                    }else{
                        $where .= $wpdb->prepare(" or post_status LIKE %s", "" . $search_param[$i] . "");
                    }
                }
              }else{ 
                $where .= $wpdb->prepare("post_title LIKE %s", "" . $args['post_status'] . "");
              } 
              
        }
      
  }
  
  if ( isset($args['include']) ) {
     if ( is_string($args['include']) ) $args['include'] = explode(',', $args['include']); 
     if ( is_array($args['include']) ) {
      $args['include'] = array_map('intval', $args['include']); 
      if ( $where != "" )  $where .= " OR ";
      $where .= "ID IN (" . implode(',', $args['include'] ). ")";
    }
  }
  if ( isset($args['exclude']) ) {
     if ( is_string($args['exclude']) ) $args['exclude'] = explode(',', $args['exclude']); 
     if ( is_array($args['exclude']) ) {
      $args['exclude'] = array_map('intval', $args['exclude']);
      if ( $where != "" ) $where .= " AND "; 
      $where .= "ID NOT IN (" . implode(',', $args['exclude'] ). ")";
    }
  }
  extract($args);
  $iscol = false;
  if ( isset($fields) ) { 
    if ( is_string($fields) ) $fields = explode(',', $fields);
    if ( is_array($fields) ) {
      $fields = array_intersect($fields, array_keys($valid_fields)); 
      if( count($fields) == 1 ) $iscol = true;
      $fields = implode(',', $fields);
    }
  }
  if ( empty($fields) ) $fields = '*';
  if ( ! in_array($orderby, $valid_fields) ) $orderby = 'post_date';
  if ( ! in_array( strtoupper($order), array('ASC','DESC')) ) $order = 'DESC';
  if ( ! intval($posts_per_page) && $posts_per_page != -1)
     $posts_per_page = $defaults['posts_per_page'];
  if ( $where == "" ) $where = "1";
  $q = "SELECT $fields FROM $wpdb->posts WHERE " . $where;
   $q .= " ORDER BY $orderby $order";
  if ( $posts_per_page != -1) $q .= " LIMIT $posts_per_page";
  
  return $iscol ? $wpdb->get_col($q) : $wpdb->get_results($q);
}
	
	function woocustomers_new_data($order_id, $posted){
		global $woocommerce, $wpdb;
		
		$order = new WC_Order($order_id);
		$order_total = $order->get_total();
		$last_date = $order->post->post_date;
		$email = get_post_meta( $order_id, '_billing_email', true );
		$order_customer = get_post_meta( $order_id, '_customer_user', true );
		$shop_manager = get_user_by("email", $email);
		
		
		//do not store info for certain users
		if($shop_manager && (($shop_manager->roles[0] == "shop_manager") || ($shop_manager->roles[0] == "administrator")))
			return;
		//manage customer add
		
		$user_info = $wpdb->get_row("select * from ".WOOCUSTOMERS_TABLE." WHERE email = '".$email."'");
		if($user_info){
			$total_purchases = 1 + $user_info->total_purchases;
			$total_spent = $order_total + $user_info->total_spent;
		}else{
			$total_purchases = 1;
			$total_spent = $order_total;
		}
		
		//save user data
		$user_id = $this->build_user($order_id, $total_spent, $last_date,$total_purchases);
		//check to see if we have this order recorded
		$this->build_order($email, $order_id);
	} 
	
	function woocustomers_build_data(){
		global $wpdb, $wpdb, $woocustomers;
		$wpdb->query("TRUNCATE TABLE ".$wpdb->prefix.WOOCUSTOMERS_TABLE_BASE);
		$wpdb->query("TRUNCATE TABLE ".$wpdb->prefix.WOOCUSTOMERS_ORDER_TABLE_BASE);
		
		if( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) ){
			//woo 2.2 
            
            $args = array(
			  'post_type' => 'shop_order',
			  'post_status' => '%wc-completed%',
			  'posts_per_page' => -1,
			  'fields' => array('ID')
			);
            
            $orders = $this->get_posts_fields($args);
            
		}else{
			/*woo 2.1*/                       
           $args = array(
			  'post_type' => 'shop_order',
			  'post_status' => '%publish%,%wc-completed%',
			  'posts_per_page' => -1,
			  'fields' => array('ID')
			);
            
            $orders = $this->get_posts_fields($args);
            
		}
//print_r($orders);
		$total_purchases = 0;
		$total_spent = 0;
		$last_date = "";
		if($orders){
			foreach($orders as $order){
			     //print_r($order);
				$raw_order = new WC_Order($order); //get orderdetails
                
				$order_date = get_the_time('Y-m-d', $order);
				if( $order_date > $last_date) { $last_date = $order_date; }
				$total_spent = $raw_order->get_total(); 
				$order_customer = get_post_meta( $order, '_customer_user', true );
				$email = get_post_meta( $order, '_billing_email', true );
				$shop_manager = get_user_by("email", $email);
				
				if($shop_manager && (($shop_manager->roles[0] == "shop_manager") || ($shop_manager->roles[0] == "administrator"))){
				}else{
					//save user info
					$user_id = $this->build_user($order, $total_spent, $last_date);
					//save order
					$this->build_order($email, $order);
				}
			
			}
		}
		
	}
	
	//activate
	function woocustomers_activate(){
		ob_start();
		woocustomers_install_table();
		$catch_all = ob_get_clean(); 
	}
	

}
register_activation_hook( __FILE__, array("woocustomers",'woocustomers_activate')  );



global $woocustomers;

$woocustomers = new woocustomers();


function woocustomers_install_table($blog_id = false){
	global $wpdb, $woocustomers;
	
	$table_name = $wpdb->prefix."woocustomers";
	$order_table = $wpdb->prefix."woocustomers_orders";
	
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")!=$table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			first_name varchar(120) DEFAULT NULL,
			last_name varchar(120) DEFAULT NULL,
			email varchar(120) DEFAULT NULL,
			total_purchases varchar(80) DEFAULT NULL,
			total_spent FLOAT DEFAULT NULL,
			last_date DATETIME DEFAULT NULL,
			created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY `email` (`email`)
		);";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);
		
	}
	
	if ($wpdb->get_var("SHOW TABLES LIKE '$order_table'")!=$order_table){
		$sql2 = "CREATE TABLE " . $order_table . " (
			order_id int(9) DEFAULT NULL,
			email varchar(120) DEFAULT NULL,
			PRIMARY KEY  (order_id)
		);
		";
        
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql2);
	}
	
	//$woocustomers->woocustomers_build_data();
}

} // END if wooCommerce active
	
