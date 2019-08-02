<?php
/**
 * WooCommerce Authorize.Net CIM Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net CIM Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net CIM Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-cim/
 *
 * @package   WC-Gateway-Authorize-Net-CIM/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Authorize.Net CIM eCheck Payment Gateway
 *
 * Handles all purchases with eChecks
 *
 * This is a direct check gateway
 *
 * @since 2.0.0
 */
class WC_Gateway_Authorize_Net_CIM_eCheck extends WC_Gateway_Authorize_Net_CIM {


	/** @var string the authorization message displayed at checkout */
	protected $authorization_message = '';

	/** @var string the authorization message displayed at checkout for subscriptions */
	protected $recurring_authorization_message = '';

	/** @var bool whether the authorization message should be displayed at checkout */
	protected $authorization_message_enabled;


	/**
	 * Initialize the gateway
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Authorize_Net_CIM::ECHECK_GATEWAY_ID,
			wc_authorize_net_cim(),
			array(
				'method_title'       => __( 'Authorize.Net CIM eCheck', 'woocommerce-gateway-authorize-net-cim' ),
				'method_description' => __( 'Allow customers to securely pay using their checking/savings accounts with Authorize.Net CIM.', 'woocommerce-gateway-authorize-net-cim' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_TOKEN_EDITOR,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_CUSTOMER_ID,
					self::FEATURE_ADD_PAYMENT_METHOD,
				 ),
				'payment_type'       => self::PAYMENT_TYPE_ECHECK,
				'environments'       => array( 'production' => __( 'Production', 'woocommerce-gateway-authorize-net-cim' ), 'test' => __( 'Test', 'woocommerce-gateway-authorize-net-cim' ) ),
				'shared_settings'    => $this->shared_settings_names,
			)
		);

		// display the authorization message at checkout
		if ( $this->is_authorization_message_enabled() && ! is_add_payment_method_page() ) {
			add_action( 'wc_' . $this->get_id() . '_payment_form_end',   array( $this, 'display_authorization_message' ) );
		}

		// adjust the recurring authorization message placeholders for subscriptions
		add_filter( 'wc_' . $this->get_id() . '_authorization_message_placeholders', array( $this, 'adjust_subscriptions_placeholders' ), 10, 2 );
	}


	/**
	 * Adds the authorization message fields to the gateway settings.
	 *
	 * @since 2.4.0
	 * @see SV_WC_Payment_Gateway::init_form_fields()
	 */
	public function init_form_fields() {

		$new_fields = array();

		parent::init_form_fields();

		foreach ( $this->form_fields as $id => $field ) {

			if ( 'enable_customer_decline_messages' === $id ) {

				$new_fields['authorization_message_enabled'] = array(
					'title'   => __( 'Authorization', 'woocommerce-gateway-authorize-net-cim' ),
					'label'   => esc_html__( 'Display an authorization confirmation message at checkout', 'woocommerce-gateway-authorize-net-cim' ),
					'type'    => 'checkbox',
					'default' => 'no',
				);

				$new_fields['authorization_message'] = array(
					'title'       => __( 'Authorization Message', 'woocommerce-gateway-authorize-net-cim' ),
					'description' => sprintf(
						/** translators: Placeholders: %1$s - <code> tag, %2$s - </code> tag */
						esc_html__( 'Use these tags to customize your message: %1$s{merchant_name}%2$s, %1$s{order_date}%2$s, and %1$s{order_total}%2$s', 'woocommerce-gateway-authorize-net-cim' ),
						'<code>',
						'</code>'
					),
					'type'    => 'textarea',
					'class'   => 'authorization-message-field',
					'default' => $this->get_default_authorization_message(),
				);

				if ( $this->get_plugin()->is_subscriptions_active() && $this->supports_tokenization() ) {

					$new_fields['recurring_authorization_message'] = array(
						'title'   => __( 'Recurring Authorization Message', 'woocommerce-gateway-authorize-net-cim' ),
						'description' => sprintf(
							/** translators: Placeholders: %1$s - <code> tag, %2$s - </code> tag */
							esc_html__( 'Use these tags to customize your message: %1$s{merchant_name}%2$s, %1$s{order_date}%2$s, and %1$s{order_total}%2$s', 'woocommerce-gateway-authorize-net-cim' ),
							'<code>',
							'</code>'
						),
						'type'    => 'textarea',
						'class'   => 'authorization-message-field',
						'default' => $this->get_default_recurring_authorization_message(),
					);
				}
			}

			$new_fields[ $id ] = $field;
		}

		$this->form_fields = $new_fields;
	}


	/**
	 * Adds some inline JS to show/hide the authorization message settings fields.
	 *
	 * @since 2.4.0
	 * @see WC_Settings_API::admin_options()
	 */
	public function admin_options() {

		parent::admin_options();

		// add inline javascript to show/hide any shared settings fields as needed
		ob_start();
		?>
			$( '#woocommerce_<?php echo sanitize_html_class( $this->get_id() ); ?>_authorization_message_enabled' ).change( function() {

				var enabled = $( this ).is( ':checked' );

				if ( enabled ) {
					$( '.authorization-message-field' ).closest( 'tr' ).show();
				} else {
					// show the fields
					$( '.authorization-message-field' ).closest( 'tr' ).hide();

					$( '#woocommerce_<?php echo sanitize_html_class( $this->get_id() ); ?>_authorization_message_enabled' ).change();
				}

			} ).change();
		<?php

		wc_enqueue_js( ob_get_clean() );

	}


	/**
	 * Displays the authorization message.
	 *
	 * @since 2.4.0
	 */
	public function display_authorization_message() {

		/**
		 * Filters the authorization message HTML displayed at checkout.
		 *
		 * @since 2.4.0
		 * @param string $html the message HTML
		 * @param \WC_Gateway_Authorize_Net_CIM_eCheck $gateway the gateway object
		 */
		$html = apply_filters( 'wc_' . $this->get_id() . '_authorization_message_html', '<p class="wc-' . $this->get_id_dasherized() . '-authorization-message">' . $this->get_authorization_message() . '</p>', $this );

		echo wp_kses_post( $html );
	}


	/**
	 * Return the default values for this payment method, used to pre-fill
	 * an authorize.net valid test account number when in testing mode
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_payment_method_defaults()
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {

			$defaults['routing-number'] = '121000248';
			$defaults['account-number'] = '8675309';
		}

		return $defaults;
	}


	/**
	 * Gets the authorization message displayed at checkout.
	 *
	 * @since 2.4.0
	 * @return string
	 */
	public function get_authorization_message() {

		if ( $this->supports_subscriptions() && ( WC_Subscriptions_Cart::cart_contains_subscription() || WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment ) ) {

			if ( $this->recurring_authorization_message ) {
				$raw_message = $this->recurring_authorization_message;
			} else {
				$raw_message = $this->get_default_recurring_authorization_message();
			}

		} else {

			if ( $this->authorization_message ) {
				$raw_message = $this->authorization_message;
			} else {
				$raw_message = $this->get_default_authorization_message();
			}
		}


		/**
		 * Filters the authorization message displayed at checkout, before replacing the placeholders.
		 *
		 * @since 2.4.0
		 * @param string $message the raw authorization message text
		 * @param \WC_Gateway_Authorize_Net_CIM_eCheck $gateway the gateway object
		 */
		$raw_message = apply_filters( 'wc_' . $this->get_id() . '_raw_authorization_message', $raw_message, $this );

		$order_total = ( $order = wc_get_order( $this->get_checkout_pay_page_order_id() ) ) ? $order->get_total() : WC()->cart->total;

		/**
		 * Filters the authorization message placeholders.
		 *
		 * @since 2.4.0
		 * @param array $placeholders the authorization message placeholders
		 * @param \WC_Gateway_Authorize_Net_CIM_eCheck $gateway the gateway object
		 */
		$placeholders = apply_filters( 'wc_' . $this->get_id() . '_authorization_message_placeholders', array(
			'{merchant_name}' => get_bloginfo( 'name' ),
			'{order_total}'   => wc_price( $order_total ),
			'{order_date}'    => date_i18n( wc_date_format() ),
		), $this );

		$message = str_replace( array_keys( $placeholders ), $placeholders, $raw_message );

		/**
		 * Filters the authorization message displayed at checkout.
		 *
		 * @since 2.4.0
		 * @param string $message the authorization message text
		 * @param \WC_Gateway_Authorize_Net_CIM_eCheck $gateway the gateway object
		 */
		return apply_filters( 'wc_' . $this->get_id() . '_authorization_message', $message, $this );
	}


	/**
	 * Adjust the recurring authorization message placeholders for subscriptions.
	 *
	 * Mainly changing the authorization date to match if on the Change Payment screen.
	 *
	 * @since 2.4.0
	 * @param array $placeholders the authorization message placeholders
	 * @param \WC_Gateway_Authorize_Net_CIM_eCheck $gateway the gateway object
	 * @return array
	 */
	public function adjust_subscriptions_placeholders( $placeholders, $gateway ) {
		global $wp;

		if ( ! $gateway->supports_subscriptions() || ! WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment ) {
			return $placeholders;
		}

		$subscription = wcs_get_subscription( absint( $wp->query_vars['order-pay'] ) );

		$placeholders['{order_date}']  = $subscription->get_date_to_display( 'next_payment' );

		return $placeholders;
	}


	/**
	 * Gets the default authorization message.
	 *
	 * @since 2.4.0
	 * @return string
	 */
	protected function get_default_authorization_message() {

		return sprintf(
			/** translators: Placeholders: %1$s - the {merchant_name} placeholder, %2$s - the {order_date} placeholder, %3$s - the {order_total} placeholder */
			__( 'By clicking the button below, I authorize %1$s to charge my bank account on %2$s for the amount of %3$s.', 'woocommerce-gateway-authorize-net-cim' ),
			'{merchant_name}',
			'{order_date}',
			'{order_total}'
		);
	}


	/**
	 * Gets the default recurring authorization message.
	 *
	 * @since 2.4.0
	 * @return string
	 */
	protected function get_default_recurring_authorization_message() {

		return sprintf(
			/** translators: Placeholders: %1$s - the {merchant_name} placeholder, %2$s - the {order_total} placeholder, %3$s - the {order_date} placeholder */
			__( 'By clicking the button below, I authorize %1$s to charge my bank account for the amount of %2$s on %3$s, then according to the above recurring totals thereafter.', 'woocommerce-gateway-authorize-net-cim' ),
			'{merchant_name}',
			'{order_total}',
			'{order_date}'
		);
	}


	/**
	 * Determines if the authorization message should be displayed at checkout.
	 *
	 * @since 2.4.0
	 * @return bool
	 */
	public function is_authorization_message_enabled() {

		/**
		 * Filters whether the authorization message should be displayed at checkout.
		 *
		 * @since 2.4.0
		 * @param bool $enabled
		 */
		return (bool) apply_filters( 'wc_' . $this->get_id() . '_authorization_message_enabled', 'yes' === $this->authorization_message_enabled );
	}


}
