<?php
/**
 * Plugin Name: WooCommerce Authorize.Net CIM Gateway
 * Plugin URI: http://www.woocommerce.com/products/authorize-net-cim/
 * Description: Adds the Authorize.Net CIM Payment Gateway to your WooCommerce site, allowing customers to securely save their credit card or bank account to their account for use with single purchases, pre-orders, subscriptions, and more!
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com/
 * Version: 2.7.1
 * Text Domain: woocommerce-gateway-authorize-net-cim
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2017, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Authorize-Net-CIM
 * @author      SkyVerge
 * @category    Payment-Gateways
 * @copyright   Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 178481:8b61524fe53add7fdd1a8d1b00b9327d
 * WC requires at least: 2.5.5
 * WC tested up to: 3.2.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '8b61524fe53add7fdd1a8d1b00b9327d', '178481' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.7.2', __( 'WooCommerce Authorize.Net CIM Gateway', 'woocommerce-gateway-authorize-net-cim' ), __FILE__, 'init_woocommerce_gateway_authorize_net_cim', array(
	'is_payment_gateway'   => true,
	'minimum_wc_version'   => '2.5.5',
	'minimum_wp_version'   => '4.1',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_gateway_authorize_net_cim() {

/**
 * # WooCommerce Authorize.Net CIM Gateway Main Plugin Class
 *
 * ## Plugin Overview
 *
 * This plugin adds Authorize.Net CIM as a payment gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.
 *
 * ## Features
 *
 * + Credit Card Authorization
 * + Credit Card Charge
 * + Credit Card Auth Capture
 * + Credit Card refund/void
 * + eCheck Charge
 *
 * ## Admin Considerations
 *
 * + A 'Capture Charge' order action link is added that allows the admin to capture a previously authorized charge for
 * an order
 *
 * ## Frontend Considerations
 *
 * Both the payment fields on checkout (and checkout->pay) and the My cards section on the My Account page are template
 * files for easy customization.
 *
 * ## Database
 *
 * ### Global Settings
 *
 * + `woocommerce_authorize_net_cim_settings` - the serialized gateway settings array
 * + `woocommerce_authorize_net_cim_echeck_settings` - the serialized eCheck gateway settings array
 *
 * ### Options table
 *
 * + `wc_authorize_net_cim_version` - the current plugin version, set on install/upgrade
 *
 * ### Credit Card Order Meta
 *
 * + `_wc_authorize_net_cim_environment` - the environment the transaction was created in, one of 'test' or 'production'
 * + `_wc_authorize_net_cim_trans_id` - the credit card transaction ID returned by Authorize.Net
 * + `_wc_authorize_net_cim_trans_date` - the credit card transaction date
 * + `_wc_authorize_net_cim_account_four` - the last four digits of the card used for the order
 * + `_wc_authorize_net_cim_card_type` - the card type used for the transaction, if known
 * + `_wc_authorize_net_cim_card_expiry_date` - the expiration date for the card used for the order
 * + `_wc_authorize_net_cim_authorization_code` - the authorization code returned by Authorize.Net
 * + `_wc_authorize_net_cim_charge_captured` - indicates if the transaction was captured, either `yes` or `no`
 *
 * ### eCheck Order Meta
 * + `_wc_authorize_net_cim_echeck_environment` - the environment the transaction was created in, one of 'test' or 'production'
 * + `_wc_authorize_net_cim_echeck_trans_id` - the credit card transaction ID returned by Authorize.Net
 * + `_wc_authorize_net_cim_echeck_trans_date` - the credit card transaction date
 * + `_wc_authorize_net_cim_echeck_account_four` - the last four digits of the card used for the order
 * + `_wc_authorize_net_cim_echeck_account_type` - the bank account type used for the transaction, if known, either `checking` or `savings`
 *
 * ### User Meta
 *
 * + `wc_authorize_net_cim_customer_profile_id` -
 * + `wc_authorize_net_cim_shipping_address_id` -
 * + `wc_authorize_net_cim_shipping_address_id_test` -
 * + `wc_authorize_net_cim_shipping_address_hash` -
 * + `wc_authorize_net_cim_shipping_address_hash_test` -
 * + `_wc_authorize_net_cim_credit_card_payment_tokens` -
 * + `_wc_authorize_net_cim_echeck_payment_tokens` -
 *
 * @since 2.0.0
 */
class WC_Authorize_Net_CIM extends SV_WC_Payment_Gateway_Plugin {


	/** string version number */
	const VERSION = '2.7.1';

	/** @var WC_Authorize_Net_CIM single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'authorize_net_cim';

	/** string plugin text domain, DEPRECATED as of 2.1.0 */
	const TEXT_DOMAIN = 'woocommerce-gateway-authorize-net-cim';

	/** string the gateway class name */
	const CREDIT_CARD_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_CIM_Credit_Card';

	/** string the gateway id */
	const CREDIT_CARD_GATEWAY_ID = 'authorize_net_cim_credit_card';

	/** string the gateway class name */
	const ECHECK_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_CIM_eCheck';

	/** string the gateway id */
	const ECHECK_GATEWAY_ID = 'authorize_net_cim_echeck';


	/**
	 * Setup main plugin class
	 *
	 * @since 1.0
	 * @return \WC_Authorize_Net_CIM
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-gateway-authorize-net-cim',
				'gateways'    => array(
					self::CREDIT_CARD_GATEWAY_ID => self::CREDIT_CARD_GATEWAY_CLASS_NAME,
					self::ECHECK_GATEWAY_ID      => self::ECHECK_GATEWAY_CLASS_NAME,
				),
				'dependencies'       => array( 'SimpleXML', 'xmlwriter', 'dom' ),
				'require_ssl'        => true,
				'supports'           => array(
					self::FEATURE_CAPTURE_CHARGE,
					self::FEATURE_MY_PAYMENT_METHODS,
					self::FEATURE_CUSTOMER_ID,
				),
				'display_php_notice' => true, // @since 2.6.3
			)
		);

		// Load gateway files after woocommerce is loaded
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'includes' ), 11 );

		// display the admin Shipping Address ID user field
		add_action( 'wc_payment_gateway_' . $this->get_id() . '_user_profile', array( $this, 'display_shipping_address_id_field' ), 15 );

		// save the admin Shipping Address ID user field
		add_action( 'personal_options_update',  array( $this, 'save_shipping_address_id_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_shipping_address_id_field' ) );
	}


	/**
	 * Loads API and Gateway classes
	 *
	 * @since 1.0
	 */
	public function includes() {

		// gateway classes
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-authorize-net-cim.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-authorize-net-cim-credit-card.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-authorize-net-cim-echeck.php' );

		// profile classes
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-payment-profile.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-payment-profile-handler.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-shipping-address.php' );

		if ( is_admin() ) {

			require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-payment-profile-editor.php' );

		// require checkout billing fields for non-US stores, as all European card processors require the billing fields
		// in order to successfully process transactions
		} elseif ( ! strncmp( get_option( 'woocommerce_default_country' ), 'US:', 3 ) ) {

			// remove blank arrays from the state fields, otherwise it's hidden
			add_action( 'woocommerce_states', array( $this, 'tweak_states' ), 1 );

			//  require the billing fields
			add_filter( 'woocommerce_get_country_locale', array( $this, 'require_billing_fields' ), 100 );
		}
	}


	/** Frontend methods ******************************************************/


	/**
	 * Before requiring all billing fields, the state array has to be removed of blank arrays, otherwise
	 * the field is hidden
	 *
	 * @see WC_Countries::__construct()
	 *
	 * @since 2.0.0
	 * @param array $countries the available countries
	 * @return array the available countries
	 */
	public function tweak_states( $countries ) {

		foreach ( $countries as $country_code => $states ) {

			if ( is_array( $countries[ $country_code ] ) && empty( $countries[ $country_code ] ) ) {
				$countries[ $country_code ] = null;
			}
		}

		return $countries;
	}


	/**
	 * Require all billing fields to be entered when the merchant is using a European payment processor
	 *
	 * @since 2.0.0
	 * @param array $locales array of countries and locale-specific address field info
	 * @return array the locales array with billing info required
	 */
	public function require_billing_fields( $locales ) {

		foreach ( $locales as $country_code => $fields ) {

			if ( isset( $locales[ $country_code ]['state']['required'] ) ) {
				$locales[ $country_code ]['state']['required'] = true;
				$locales[ $country_code ]['state']['label']    = $this->get_state_label( $country_code );
			}
		}

		return $locales;
	}


	/**
	 * Gets a label for states that don't have one set by WooCommerce.
	 *
	 * @since 2.6.1
	 *
	 * @param string $country_code the 2-letter country code for the billing country
	 * @return string the label for the "billing state" field at checkout
	 */
	protected function get_state_label( $country_code ) {

		switch( $country_code ) {

			case 'AF':
			case 'AT':
			case 'BI':
			case 'KR':
			case 'PL':
			case 'PT':
			case 'LK':
			case 'SE':
			case 'VN':
				$label = __( 'Province', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'AX':
			case 'YT':
				$label = __( 'Island', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'DE':
				$label = __( 'State', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'EE':
			case 'NO':
				$label = __( 'County', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'FI':
			case 'IL':
			case 'LB':
				$label = __( 'District', 'woocommerce-gateway-authorize-net-cim' );
			break;

			default:
				$label = __( 'Region', 'woocommerce-gateway-authorize-net-cim' );
		}

		return $label;
	}


	/** Admin methods ******************************************************/


	/**
	 * Returns the "Configure Credit Cards" or "Configure eCheck" plugin action links that go
	 * directly to the gateway settings page
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway_Plugin::get_settings_url()
	 * @param string $gateway_id the gateway identifier
	 * @return string plugin configure link
	 */
	public function get_settings_link( $gateway_id = null ) {

		return sprintf( '<a href="%s">%s</a>',
			$this->get_settings_url( $gateway_id ),
			self::CREDIT_CARD_GATEWAY_ID === $gateway_id ? __( 'Configure Credit Cards', 'woocommerce-gateway-authorize-net-cim' ) : __( 'Configure eChecks', 'woocommerce-gateway-authorize-net-cim' )
		);
	}


	/**
	 * Render a notice for the user to select their desired export format
	 *
	 * @since 1.3.3
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$settings = get_option( 'woocommerce_authorize_net_cim_credit_card_settings' );

		// install notice
		if ( empty( $settings ) && ! $this->get_admin_notice_handler()->is_notice_dismissed( 'install-notice' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf( __( 'Thanks for installing the WooCommerce Authorize.Net CIM Gateway! To start accepting payments, %sset your Authorize.Net API credentials%s. Need help? See the %sdocumentation%s.', 'woocommerce-gateway-authorize-net-cim' ),
					'<a href="' . $this->get_settings_url() . '">', '</a>',
					'<a target="_blank" href="' . $this->get_documentation_url() . '">', '</a>'
				), 'install-notice', array( 'notice_class' => 'updated' )
			);
		}

		$gateway = $this->get_gateway( self::CREDIT_CARD_GATEWAY_ID );

		// bail if gateway is not available, as proper credentials are needed first
		if ( ! $gateway->is_available() ) {
			return;
		}

		// check if CIM feature is enabled on customer's authorize.net account
		if ( ! get_option( 'wc_authorize_net_cim_feature_enabled' ) ) {

			if ( $gateway->is_cim_feature_enabled() ) {
				update_option( 'wc_authorize_net_cim_feature_enabled', true );
			} else {

				if ( ! $this->get_admin_notice_handler()->is_notice_dismissed( 'cim-add-on-notice' ) ) {
					$this->get_admin_notice_handler()->add_admin_notice(
						sprintf( __( 'The CIM Add-On is not enabled on your Authorize.Net account. Please %scontact Authorize.Net%s to enable CIM. You will be unable to process transactions until CIM is enabled. ', 'woocommerce-gateway-authorize-net-cim' ), '<a href="http://support.authorize.net" target="_blank">', '</a>' ),
						'cim-add-on-notice' );
				}
			}
		}

		if ( $gateway->is_accept_js_enabled() && isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) {

			$message = '';

			if ( ! $gateway->get_client_key() ) {
				$message = sprintf( __( "%s: A valid Client Key is required to use Accept.js at checkout.", 'woocommerce-gateway-authorize-net-cim' ), '<strong>' . $this->get_plugin_name() . '</strong>' );
			} elseif ( ! wc_checkout_is_https() ) {
				$message = sprintf( __( "%s: SSL is required to use Accept.js at checkout.", 'woocommerce-gateway-authorize-net-cim' ), '<strong>' . $this->get_plugin_name() . '</strong>' );
			}

			if ( $message ) {
				$this->get_admin_notice_handler()->add_admin_notice( $message, 'accept-js-status', array(
					'dismissible'  => false,
					'notice_class' => 'error',
				) );
			}
		}
	}


	/**
	 * Displays the admin Shipping Address ID user field.
	 *
	 * @since 2.6.3
	 *
	 * @param \WP_User $user user object
	 */
	public function display_shipping_address_id_field( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$unique_meta_key = '';
		$environments    = array();

		foreach ( $this->get_gateways() as $gateway ) {
			$environments[] = $gateway->get_environment();
		}

		$environments = array_unique( $environments );

		foreach ( $this->get_gateways() as $gateway ) {

			$meta_key = sprintf( 'wc_%s_shipping_address_id', $this->get_id() );

			if ( ! $gateway->is_production_environment() ) {
				$meta_key .= '_' . $gateway->get_environment();
			}

			// If a field with this meta key has already been set, skip this gateway
			if ( $meta_key === $unique_meta_key ) {
				continue;
			}

			$label = __( 'Shipping Address ID', 'woocommerce-gateway-authorize-net-cim' );

			if ( count( $environments ) > 1 ) {
				$label .= ' (' . $gateway->get_environment_name() . ')';
			}

			$value = get_user_meta( $user->ID, $meta_key, true );

			?>

			<tr>
				<th><label for="<?php esc_attr_e( $meta_key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<input class="regular-text" name="<?php esc_attr_e( $meta_key ); ?>" value="<?php esc_attr_e( $value ); ?>" type="text" /><br/>
					<span class="description"><?php esc_html_e( 'The shipping profile ID for the user. Only edit this if necessary.', 'woocommerce-gateway-authorize-net-cim' ); ?></span>
				</td>
			</tr>

			<?php

			$unique_meta_key = $meta_key;
		}
	}


	/**
	 * Saves the admin Shipping Address ID user field.
	 *
	 * @since 2.6.3
	 *
	 * @param int $user_id user profile ID
	 */
	public function save_shipping_address_id_field( $user_id ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		foreach ( $this->get_gateways() as $gateway ) {

			$field_name = sprintf( 'wc_%s_shipping_address_id', $this->get_id() );

			if ( ! $gateway->is_production_environment() ) {
				$field_name .= '_' . $gateway->get_environment();
			}

			if ( isset( $_POST[ $field_name ] ) ) {
				update_user_meta( $user_id, $field_name, wc_clean( $_POST[ $field_name ] ) );
			}
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Authorize.Net CIM Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.4.0
	 * @see wc_authorize_net_cim()
	 * @return WC_Authorize_Net_CIM
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Gets the plugin documentation url
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/authorize-net-cim/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 2.0.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Authorize.Net CIM Gateway', 'woocommerce-gateway-authorize-net-cim' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/** Lifecycle methods *****/


	/**
	 * Upgrade to the currently installed version
	 *
	 * @since 2.0.0
	 * @param string $installed_version currently installed version
	 */
	public function upgrade( $installed_version ) {

		// upgrade to 2.0.0
		if ( version_compare( $installed_version, '2.0.0', '<' ) ) {

			$this->log( 'Starting upgrade to 2.0.0' );


			/** Upgrade settings */

			$old_cc_settings        = get_option( 'woocommerce_authorize_net_cim_settings' );
			$old_echeck_settings    = get_option( 'woocommerce_authorize_net_cim_echeck_settings' );

			if ( $old_cc_settings ) {

				// prior to 2.0.0, there was no settings for tokenization (always on) and enable_customer_decline_messages.
				// eCheck settings were inherited from the credit card gateway by default

				// credit card
				$new_cc_settings = array(
					'enabled'                          => ( isset( $old_cc_settings['enabled'] ) && 'yes' === $old_cc_settings['enabled'] ) ? 'yes' : 'no',
					'title'                            => ( ! empty( $old_cc_settings['title'] ) ) ? $old_cc_settings['title'] : 'Credit Card',
					'description'                      => ( ! empty( $old_cc_settings['description'] ) ) ? $old_cc_settings['description'] : 'Pay securely using your credit card.',
					'enable_csc'                       => ( isset( $old_cc_settings['require_cvv'] ) && 'yes' === $old_cc_settings['require_cvv'] ) ? 'yes' : 'no',
					'transaction_type'                 => ( isset( $old_cc_settings['transaction_type'] ) && 'auth_capture' === $old_cc_settings['transaction_type'] ) ? 'charge' : 'authorization',
					'card_types'                       => ( ! empty( $old_cc_settings['card_types'] ) ) ? $old_cc_settings['card_types'] : array( 'VISA', 'MC', 'AMEX', 'DISC' ),
					'tokenization'                     => 'yes',
					'environment'                      => ( isset( $old_cc_settings['test_mode'] ) && 'yes' === $old_cc_settings['test_mode'] ) ? 'test' : 'production',
					'inherit_settings'                 => 'no',
					'api_login_id'                     => ( ! empty( $old_cc_settings['api_login_id'] ) ) ? $old_cc_settings['api_login_id'] : '',
					'api_transaction_key'              => ( ! empty( $old_cc_settings['api_transaction_key'] ) ) ? $old_cc_settings['api_transaction_key'] : '',
					'test_api_login_id'                => ( ! empty( $old_cc_settings['test_api_login_id'] ) ) ? $old_cc_settings['test_api_login_id'] : '',
					'test_api_transaction_key'         => ( ! empty( $old_cc_settings['test_api_transaction_key'] ) ) ? $old_cc_settings['test_api_transaction_key'] : '',
					'enable_customer_decline_messages' => 'no',
					'debug_mode'                       => ( ! empty( $old_cc_settings['debug_mode'] ) ) ? $old_cc_settings['debug_mode'] : 'off',
				);

				// eCheck
				$new_echeck_settings = array(
					'enabled'                          => ( isset( $old_echeck_settings['enabled'] ) && 'yes' === $old_echeck_settings['enabled'] ) ? 'yes' : 'no',
					'title'                            => ( ! empty( $old_echeck_settings['title'] ) ) ? $old_echeck_settings['title'] : 'eCheck',
					'description'                      => ( ! empty( $old_echeck_settings['description'] ) ) ? $old_echeck_settings['description'] : 'Pay securely using your checking account.',
					'tokenization'                     => 'yes',
					'environment'                      => $new_cc_settings['environment'],
					'inherit_settings'                 => 'yes',
					'api_login_id'                     => '',
					'api_transaction_key'              => '',
					'test_api_login_id'                => '',
					'test_api_transaction_key'         => '',
					'enable_customer_decline_messages' => 'no',
					'debug_mode'                       => $new_cc_settings['debug_mode'],
				);

				// save new settings, remove old ones
				update_option( 'woocommerce_authorize_net_cim_credit_card_settings', $new_cc_settings );
				update_option( 'woocommerce_authorize_net_cim_echeck_settings', $new_echeck_settings );
				delete_option( 'woocommerce_authorize_net_cim_settings' );

				$this->log( 'Settings upgraded.' );
			}


			/** Update meta key for customer profile ID and shipping profile ID */

			global $wpdb;

			// old key: _wc_authorize_net_cim_profile_id
			// new key: wc_authorize_net_cim_customer_profile_id
			// note that we don't know on a per-user basis what environment the customer ID was set in, so we assume production, just to be safe
			$rows = $wpdb->update( $wpdb->usermeta, array( 'meta_key' => 'wc_authorize_net_cim_customer_profile_id' ), array( 'meta_key' => '_wc_authorize_net_cim_profile_id' ) );

			$this->log( sprintf( '%d users updated for customer profile ID.', $rows ) );

			// old key: _wc_authorize_net_cim_shipping_profile_id
			// new key: wc_authorize_net_cim_shipping_address_id
			$rows = $wpdb->update( $wpdb->usermeta, array( 'meta_key' => 'wc_authorize_net_cim_shipping_address_id' ), array( 'meta_key' => '_wc_authorize_net_cim_shipping_profile_id' ) );

			$this->log( sprintf( '%d users updated for shipping address ID', $rows ) );


			/** Update meta values for order payment method & recurring payment method */

			// meta key: _payment_method
			// old value: authorize_net_cim
			// new value: authorize_net_cim_credit_card
			// note that the eCheck method has not changed from 1.x to 2.x
			$rows = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'authorize_net_cim_credit_card' ), array( 'meta_key' => '_payment_method', 'meta_value' => 'authorize_net_cim' ) );

			$this->log( sprintf( '%d orders updated for payment method meta', $rows ) );

			// meta key: _recurring_payment_method
			// old value: authorize_net_cim
			// new value: authorize_net_cim_credit_card
			$rows = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'authorize_net_cim_credit_card' ), array( 'meta_key' => '_recurring_payment_method', 'meta_value' => 'authorize_net_cim' ) );

			$this->log( sprintf( '%d orders updated for recurring payment method meta', $rows ) );


			/** Convert payment profiles stored in legacy format to framework payment token format */

			$this->log( 'Starting payment profile upgrade.' );

			$user_ids = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '_wc_authorize_net_cim_payment_profiles'" );

			if ( $user_ids ) {

				// iterate through each user with a payment profile
				foreach ( $user_ids as $user_id ) {

					$customer_profile_id = get_user_meta( $user_id, 'wc_authorize_net_cim_customer_profile_id', true );

					$payment_profiles = get_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', true );

					$cc_tokens = $echeck_tokens = array();

					// iterate through each payment profile
					foreach ( $payment_profiles as $profile_id => $profile ) {

						// bail if corrupted
						if ( ! $profile_id || empty( $profile['type'] ) ) {
							continue;
						}

						// parse expiry date
						if ( ! empty( $profile['exp_date'] ) && SV_WC_Helper::str_exists( $profile['exp_date'], '/' ) ) {
							list( $exp_month, $exp_year ) = explode( '/', $profile['exp_date'] );
						} else {
							$exp_month = $exp_year = '';
						}

						if ( 'Bank Account' === $profile['type'] ) {

							// eCheck tokens
							$echeck_tokens[ $profile_id ] = array(
								'type'                => 'echeck',
								'last_four'           => ! empty( $profile['last_four'] ) ? $profile['last_four'] : '',
								'customer_profile_id' => $customer_profile_id,
								'billing_hash'        => '',
								'payment_hash'        => '',
								'default'             => ( ! empty( $profile['active'] ) && $profile['active'] ),
								'exp_month'           => $exp_month,
								'exp_year'            => $exp_year,
							);

						} else {

							// parse card type
							switch ( $profile['type'] ) {
								case 'Visa':             $card_type = 'visa';   break;
								case 'American Express': $card_type = 'amex';   break;
								case 'MasterCard':       $card_type = 'mc';     break;
								case 'Discover':         $card_type = 'disc';   break;
								case 'Diners Club':      $card_type = 'diners'; break;
								case 'JCB':              $card_type = 'jcb';    break;
								default:                 $card_type = '';
							}

							// credit card tokens
							$cc_tokens[ $profile_id ] = array(
								'type'                => 'credit_card',
								'last_four'           => ! empty( $profile['last_four'] ) ? $profile['last_four'] : '',
								'customer_profile_id' => $customer_profile_id,
								'billing_hash'        => '',
								'payment_hash'        => '',
								'default'             => ( ! empty( $profile['active'] ) && $profile['active'] ),
								'card_type'           => $card_type,
								'exp_month'           => $exp_month,
								'exp_year'            => $exp_year,
							);
						}
					}

					// update credit card tokens
					if ( ! empty( $cc_tokens ) ) {
						update_user_meta( $user_id, '_wc_authorize_net_cim_credit_card_payment_tokens', $cc_tokens );
					}

					// update eCheck tokens
					if ( ! empty( $echeck_tokens ) ) {
						update_user_meta( $user_id, '_wc_authorize_net_cim_echeck_payment_tokens', $echeck_tokens );
					}

					// save the legacy payment profiles in case we need them later
					update_user_meta( $user_id, '_wc_authorize_net_cim_legacy_tokens', $payment_profiles );
					delete_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles' );

					$this->log( sprintf( 'Converted payment profile for user ID: %s', $user_id) ) ;
				}
			}

			$this->log( 'Completed payment profile upgrade.' );

			$this->log( 'Completed upgrade for 2.0.0' );
		}

		// TODO: remove _wc_authorize_net_cim_legacy_tokens meta in a future version @MR 2015-07
	}


} // end WC_Authorize_Net_CIM


/**
 * Returns the One True Instance of Authorize.Net CIM
 *
 * @since 1.4.0
 * @return WC_Authorize_Net_CIM
 */
function wc_authorize_net_cim() {
	return WC_Authorize_Net_CIM::instance();
}

// fire it up!
wc_authorize_net_cim();

} // init_woocommerce_gateway_authorize_net_cim()
