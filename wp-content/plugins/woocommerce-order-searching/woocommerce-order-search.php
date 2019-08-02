<?php
/**
 * Plugin Name: WooCommerce Order Search
 * Version: 1.1.0
 * Plugin URI: 
 * Description: Allows to search orders in WooCommerce
 * Author: Chetan Khandla
 * Author URI: 
 * Requires at least: 3.8
 * Tested up to: 4.1.1
 *
 * Text Domain: wc-ost
 * Domain Path: /i18n/
 *
 * @author Nicola Mustone
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include Woo functions
require_once 'woo-includes/woo-functions.php';

// Check if WooCommerce is active
if ( ! is_woocommerce_active() ) {
    add_action( 'admin_notices', 'wc_customer_messages_wc_inactive' );
    function wc_customer_messages_wc_inactive() {
        echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Order Search requires WooCommerce in order to work properly. <a href="%s" target="_blank">Please install WooCommerce</a>.', 'wc-ost' ), 'http://wordpress.org/plugins/woocommerce/' ) . '</p></div>';
    }

    return;
}

class WC_OS {
    /**
     * __construct
     */
    public function __construct() {
        // Set up localization
        $this->load_plugin_textdomain();
		$this->cont_define();
		$this->includes();
		$this->hooks();
    }
	function cont_define(){
		define('PLUGIN_PATH',dirname(__FILE__));
	}
	
	public function includes(){
		include_once(PLUGIN_PATH."/woo-includes/wcos-settings.php");
	}
	public function hooks(){
		add_action('init',array($this,'init'));
		
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'filter_fields' ) );
		
		add_shortcode('order_search_form', array( $this, 'get_order_search_form'));
		add_shortcode('order_search_result', array( $this, 'get_order_search_result'));
		
		add_filter('page_template', array($this,'page_template'));
		
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ),999 );
		add_filter( 'request', array( $this, 'request_query' ) );
	}
	
	function request_query($vars){
		global $typenow, $wp_query;
		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			// Filter the orders by the posted customer.
			if ( isset( $_GET['_search_value'] ) && !empty($_GET['_search_value']) && !empty($_GET['_search_key'])) {
				$query_args = $this->search_orders(array('search_key' => $_REQUEST['_search_key'],'search_value' => $_REQUEST['_search_value']),array(),'query_args');
				$vars = array_merge($vars,$query_args);
			}	
		}
		return $vars;
	}
	
	function restrict_manage_posts(){
		global $typenow, $wp_query;
		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
		?>
        <input type="text" name="_search_value" size="15" value="<?php echo $_REQUEST['_search_value'] ?>" />
		<select id="dropdown_search_key" name="_search_key">
			<option value=""><?php _e( 'Show all orders', 'woocommerce' ) ?></option>
			<?php
            $order_search_keys = get_serach_fields();
			if(is_array($order_search_keys)){
				foreach($order_search_keys as $key=>$value){ 
					$selected = selected($key,$_REQUEST['_search_key'],false);
					printf('<option %3$s value="%1$s">%2$s</option>',$key,$value,$selected);
				}
            }
			?>
		</select>
        <?php
		}
		wc_enqueue_js( "
			jQuery('select#dropdown_search_key').css('width', '150px').ajaxChosen({
				method: 		'GET',
				url: 			'" . admin_url( 'admin-ajax.php' ) . "',
				dataType: 		'json',
				afterTypeDelay: 100,
				minTermLength: 	1,
				data:		{
					action: 	'woocommerce_json_search_customers',
					security: 	'" . wp_create_nonce( 'search-customers' ) . "',
					default:	'" . __( 'Show all customers', 'woocommerce' ) . "'
				}
			}, function (data) {

				var terms = {};

				$.each(data, function (i, val) {
					terms[i] = val;
				});

				return terms;
			});
		" );
	}
	public function init(){
		
	}
	
	
	public function page_template($template) {
		if(is_user_can_search_order()){
			if(is_page(get_option('woocommerce_order_search_page_id'))){
				$template = $this->locate_plugin_template(array('wcos-order-search-page.php'));
			}
		}
		return $template;
	}
	
	public function locate_plugin_template($template_names, $path = "", $args = array(), $load = false, $debug = false ){
		if ( !is_array($template_names) && $template_names )
			$template_names = array($template_names);
		elseif(!is_array($template_names))
			return false;
	
		$located = '';
	
		$plugin_dir = trailingslashit(PLUGIN_PATH."/templates/");
		if(!empty($path)){
			$plugin_dir .= trailingslashit($path);
		}
		
		$theme_dir = STYLESHEETPATH . '/order_search/';
		if(!empty($path)){
			$theme_dir .= trailingslashit($path);
		}

		$template_dir = TEMPLATEPATH . '/order_search/';
		if(!empty($path)){
			$template_dir .= trailingslashit($path);
		}
		foreach ( $template_names as $template_name ) {
			if ( !$template_name )
				continue;
			
			if ( file_exists($theme_dir . $template_name)) {
				$located = $template_dir . $template_name;
				break;
			} else if ( file_exists($template_dir . $template_name) ) {
				$located = $template_dir. $template_name;
				break;
			} else if ( file_exists( $plugin_dir.  $template_name) ) {
				$located = $plugin_dir . $template_name;
				break;
			}
		}
		
		if($debug){
			echo "<br>-------------------------<br /> template_names = "; print_r($template_names);
			echo "<br><br /> theme_file = ". $template_dir . $template_name;
			echo "<br><br /> theme_file = ". $template_dir. $template_name;
			echo "<br><br /> plugin_file = ". $plugin_dir . $template_name;
			echo "<br><br /> path = ".$path;
			echo "<br><br /> this_plugin_dir = ".$this_plugin_dir;
			echo "<br> located = ".$located."<br>--------------------------------<br />";
		}
	
		if ( $load && '' != $located )
			$this->pl_load_template( $located, $args);
		return $located;
	}
	
	function pl_load_template( $_template_file, $args = array(), $require_once = false ) {
		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		
		if ( is_array( $wp_query->query_vars ) )
			extract( $wp_query->query_vars, EXTR_SKIP );
		if(is_array($args))
			extract($args);
		if ( $require_once )
			require_once( $_template_file );
		else
			require( $_template_file );
	}
    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales are found in:
     *        WP_LANG_DIR/woocommerce-order-search-transaction/wc-ost-LOCALE.mo
     *        woocommerce-order-search-transaction/i18n/wc-ost-LOCALE.mo (which if not found falls back to:)
     *        WP_LANG_DIR/plugins/wc-ost-LOCALE.mo
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wc-ost' );

        load_textdomain( 'wc-ost', WP_LANG_DIR . '/woocommerce-order-search-transaction/wc-ost-' . $locale . '.mo' );
        load_textdomain( 'wc-ost', WP_LANG_DIR . '/plugins/wc-order-search-transaction-' . $locale . '.mo' );

        load_plugin_textdomain( 'wc-ost', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
    }

    /**
     * Add the meta _transaction_id in the search fields.
     *
     * @param array $fields
     * @return array
     */
    public function filter_fields( $fields ) {
        if ( ! in_array( '_transaction_id', $fields ) ) {
            array_push( $fields, '_transaction_id' );
        }

        return $fields;
    }
	
	
	function get_order_search_form( $form ) {
		$order_search_keys = apply_filters('order_search_keys',get_serach_fields()); 
		ob_start();
			$this->locate_plugin_template(array('wcos-order-search-form.php'),'',array('order_search_keys'=>$order_search_keys),true);
		return ob_get_clean();
	}
	
	function get_order_search_result( $args = array()) {
		/*$default = array(
			'numberposts' => -1,
			'post_type'   => wc_get_order_types( 'view-orders' ),
			'post_status' => array_keys( wc_get_order_statuses() )
		);
		if(!is_array($args)){
			$args = array();
		}
		$args = array_merge($default,$args);
		
		if($_REQUEST['search_key'] == "_all"){
			if(!empty($_REQUEST['search_value'])){
				$meta_search_ary = array('relation' => 'OR');
				$i=0;
				foreach(get_serach_fields() as $key=>$field){
					$meta_search_ary[$i]['key'] = $key;
					$meta_search_ary[$i]['value'] = $_REQUEST['search_value'];
					$meta_search_ary[$i]['compare'] = 'LIKE';
					$i++;
				}
				$args['meta_query']= $meta_search_ary;
				$args['post__in'] = array($_REQUEST['search_value']);
			}
			
		}else
		
		if($_REQUEST['search_key'] == "_order_id"){
			if(!empty($_REQUEST['search_value'])){
				$args['post__in'] = array($_REQUEST['search_value']);
				$args['orderby' ]= 'ID';
			}
			
		}else{
			
			if(!empty($_REQUEST['search_value'])){
				$args['meta_value']  = $_REQUEST['search_value'];
				$args['meta_key']   = $_REQUEST['search_key'];
				$args['orderby' ]= 'meta_value';
			}
		}
		
		$args = apply_filters( 'woocommerce_my_account_search_orders_query', $args ) ;*/
		$customer_orders = $this->search_orders($_REQUEST);
		ob_start();
			$this->locate_plugin_template(array('wcos-order-search-list.php'),'',array('customer_orders'=>$customer_orders),true);
		return ob_get_clean();
	}
	
	function search_orders($search_args, $args = array(),$return = ""){
		$default = array(
			'numberposts' => -1,
			'post_type'   => wc_get_order_types( 'view-orders' ),
			'post_status' => array_keys( wc_get_order_statuses() )
		);
		if(isset($args) && !is_array($args)){
			$args = array();
		}
		if(isset($query_args) && !is_array($query_args)){
			$query_args = array();
		}
		$query_args =array();
		if(isset($search_args['search_key']) && ($search_args['search_key'] == "_order_id")){
			if(isset($search_args['search_value']) && !empty($search_args['search_value'])){
				$query_args['meta_value'] = "";
				$query_args['meta_key'] = "";
				$query_args['post__in'] = array($search_args['search_value']);
				$query_args['orderby' ]= 'ID';
			}
		}else{
			if(isset($search_args['search_value']) && !empty($search_args['search_value'])){
				$query_args['meta_value']  = $search_args['search_value'];
				$query_args['meta_key']   = $search_args['search_key'];
				$query_args['orderby' ]= 'meta_value';
			}
		}
		
		$args = array_merge($default,$args,$query_args);
		if($return == 'query_args'){
			return $query_args;
		}
		
		
		$args = apply_filters( 'woocommerce_my_account_search_orders_query', $args ) ;
		return $customer_orders = get_posts($args );
	}
	
}

new WC_OS();
