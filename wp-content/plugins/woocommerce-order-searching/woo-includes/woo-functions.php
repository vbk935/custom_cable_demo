<?php
if ( ! function_exists( 'user_can_search_order' ) ) {
	function is_user_can_search_order() {
		global $current_user, $wpdb;
		if(!is_user_logged_in()){
			return false;
		}
		$allowed_roles =  get_option('woocommerce_order_search_allowd_role',array('administrator'));
		
		$role = $wpdb->prefix . 'capabilities';
		$current_user->role = array_keys($current_user->$role);
		$role = $current_user->role;
		if(is_array($allowed_roles)){
			foreach($allowed_roles as $allowed_role)
				if(in_array($allowed_role,$role))
					return true;
		}
		return false;
	}
}

if ( ! function_exists( 'get_serach_fields' ) ) {
	function get_serach_fields($fields = '') {
		$all_fields = array(
				//'_all'					=>'All',
				'_order_id'				=>'Order id',
				'_billing_company' 		=>'Billing company',
				'_billing_address_1'	=>'Billing address 1',
				'_billing_address_2'	=>'Billing address 2',
				'_billing_city'			=>'Billing city',
				'_billing_postcode'		=>'Billing postcode',
				'_billing_country'		=>'Billing country',
				'_billing_state'		=>'Billing state',
				'_billing_email'		=>'Billing email',
				'_billing_phone'		=>'Billing phone',
				'_shipping_address_1'	=>'Shipping address 1',
				'_shipping_address_2'	=>'Shipping address 2',
				'_shipping_city'		=>'Shipping city',
				'_shipping_postcode'	=>'Shipping postcode',
				'_shipping_country'		=>'Shipping country',
				'_shipping_state'		=>'Shipping state'
			);
		if($fields == 'all'){
			return $all_fields;
		}else{
			$selected_field =  get_option('woocommerce_search_allowed_keys');
			$selected_ary = array();
			if(is_array($selected_field) && !empty($selected_field)){
				foreach($selected_field as $key){
					$selected_ary[$key] = $all_fields[$key];
				}
			}
			return $selected_ary;
		}
	}
}

add_action('woocommerce_admin_field_multiselect_text','get_serach_fields_multi_select');
if ( ! function_exists( 'get_serach_fields_multi_select' ) ) {
	
	function get_serach_fields_multi_select($value) {
		$selections = (array) get_option( $value['id'] );
		if(empty($selections)){
			$selections = $value['default'];
		}
		
		if ( ! empty( $value['options'] ) ) {
			$options = $value['options'];
		} else {
			$options = array();
		}

		asort( $options );
		?><tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp">
				<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php _e( 'Choose option&hellip;', 'woocommerce' ); ?>" title="<?php _e( $value['title'], 'woocommerce' ) ?>" class="chosen_select">
					<?php
						if ( $options ) {
							foreach ( $options as $key => $val ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $selections ), true, false ).'>' . $val . '</option>';
							}
						}
					?>
				</select> <?php echo isset( $description ) ? $description : ''; ?> </br><a class="select_all button" href="#"><?php _e( 'Select all', 'woocommerce' ); ?></a> <a class="select_none button" href="#"><?php _e( 'Select none', 'woocommerce' ); ?></a>
			</td>
		</tr><?php
	}
}

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WC_Dependencies' ) )
	require_once 'class-wc-dependencies.php';

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		return WC_Dependencies::woocommerce_active_check();
	}
}

/**
 * Queue updates for the WooUpdater
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	function woothemes_queue_update( $file, $file_id, $product_id ) {
		global $woothemes_queued_updates;

		if ( ! isset( $woothemes_queued_updates ) )
			$woothemes_queued_updates = array();

		$plugin             = new stdClass();
		$plugin->file       = $file;
		$plugin->file_id    = $file_id;
		$plugin->product_id = $product_id;

		$woothemes_queued_updates[] = $plugin;
	}
}

/**
 * Load installer for the WooThemes Updater.
 * @return $api Object
 */
if ( ! class_exists( 'WooThemes_Updater' ) && ! function_exists( 'woothemes_updater_install' ) ) {
	function woothemes_updater_install( $api, $action, $args ) {
		$download_url = 'http://woodojo.s3.amazonaws.com/downloads/woothemes-updater/woothemes-updater.zip';

		if ( 'plugin_information' != $action ||
			false !== $api ||
			! isset( $args->slug ) ||
			'woothemes-updater' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'WooThemes Updater';
		$api->version = '1.0.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}

	add_filter( 'plugins_api', 'woothemes_updater_install', 10, 3 );
	
}




/**
 * WooUpdater Installation Prompts
 */
if ( ! class_exists( 'WooThemes_Updater' ) && ! function_exists( 'woothemes_updater_notice' ) ) {

	/**
	 * Display a notice if the "WooThemes Updater" plugin hasn't been installed.
	 * @return void
	 */
	function woothemes_updater_notice() {
		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
		if ( in_array( 'woothemes-updater/woothemes-updater.php', $active_plugins ) ) return;

		$slug = 'woothemes-updater';
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'woothemes-updater/woothemes-updater.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_woothemes-updater/woothemes-updater.php' ) );

		$message = '<a href="' . esc_url( $install_url ) . '">Install the WooThemes Updater plugin</a> to get updates for your WooThemes plugins.';
		$is_downloaded = false;
		$plugins = array_keys( get_plugins() );
		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'woothemes-updater.php' ) !== false ) {
				$is_downloaded = true;
				$message = '<a href="' . esc_url( admin_url( $activate_url ) ) . '">Activate the WooThemes Updater plugin</a> to get updates for your WooThemes plugins.';
			}
		}
		echo '<div class="updated fade"><p>' . $message . '</p></div>' . "\n";
	}

	//add_action( 'admin_notices', 'woothemes_updater_notice' );
}

/**
 * Prevent conflicts with older versions
 */
if ( ! class_exists( 'WooThemes_Plugin_Updater' ) ) {
	class WooThemes_Plugin_Updater { function init() {} }
}