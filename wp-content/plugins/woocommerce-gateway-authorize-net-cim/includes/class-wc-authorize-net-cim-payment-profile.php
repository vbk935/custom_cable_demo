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
 * Authorize.Net CIM Payment Profile
 *
 * Extends the default payment token class to provide CIM-specific functionality,
 * like billing/payment hashes for checking if an existing saved profile is a
 * duplicate of one being added, etc.
 *
 * @since 2.0.0
 */
class WC_Authorize_Net_CIM_Payment_Profile extends SV_WC_Payment_Gateway_Payment_Token {


	/** @var string customer profile ID associated with this payment profile */
	protected $customer_profile_id;

	/** @var string hash of the billing information for this payment profile */
	protected $billing_hash;

	/** @var string hash of the last four, card/account type, and billing zip code */
	protected $payment_hash;


	/**
	 * Construct the token
	 *
	 * @since 2.0.0
	 * @param string $payment_profile_id payment profile (token) ID
	 * @param array $data accepts order, billing_hash, and payment_hash elements
	 */
	public function __construct( $payment_profile_id, $data ) {

		$this->set_customer_profile_id( $data['customer_profile_id'] );

		// add payment-specific info to token from $order->payment if set
		if ( isset( $data['order'] ) && $data['order'] instanceof WC_Order ) {

			$data = array_merge( $data, $this->parse_data_from_order( $data['order'] ) );
		}

		// TODO: it's tough to understand when & how class members are set here vs. those in the $data array -- look to refactor this @MR

		if ( empty( $data['billing_hash'] ) ) {
			$data['billing_hash'] = $this->calculate_billing_hash( $data );
		} else {
			$this->set_billing_hash( $data['billing_hash'] );
		}

		if ( empty( $data['payment_hash'] ) ) {
			$data['payment_hash'] = $this->calculate_payment_hash( $data );
		} else {
			$this->set_payment_hash( $data['payment_hash'] );
		}

		// no need to save these with the token
		unset( $data['order'] );
		unset( $data['billing'] );

		parent::__construct( $payment_profile_id, $data );
	}


	/**
	 * Add payment-specific info to the data array used to build the token given
	 * an order. This is used when creating a customer profile or payment profile,
	 * as the response from Authorize.Net does not contain some useful information
	 * (like the card/account type) that improves the token display
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order
	 * @return array
	 */
	protected function parse_data_from_order( WC_Order $order ) {

		// defaults for both credit cards/eChecks
		$data = array(
			'default'             => true,
			'type'                => $order->payment->type,
			'last_four'           => $order->payment->last_four,
			'customer_profile_id' => $order->customer_id,
		);

		if ( 'credit_card' === $data['type'] ) {

			$data['card_type'] = isset( $order->payment->card_type ) ? $order->payment->card_type : SV_WC_Payment_Gateway_Helper::card_type_from_account_number( $order->payment->account_number );
			$data['exp_month'] = $order->payment->exp_month;
			$data['exp_year']  = $order->payment->exp_year;

		} elseif ( 'echeck' === $data['type'] ) {

			$data['account_type'] = $order->payment->account_type;
		}

		return $data;
	}


	/**
	 * Calculate the billing hash from an order or the billTo information provided
	 * by Authorize.Net. The exact hashed value is the md5 hash of a JSON-encoded
	 * array in format:
	 *
	 * {
	 *   'first_name' => 'First',
	 *   'last_name'  => 'Last',
	 *   'address'    => '123 Example St',
	 *   'city'       => 'Anywhere',
	 *   'state'      => 'HI',
	 *   'postcode'   => '90210',
	 *   'country'    => 'US',
	 *   'phone'      => '555-867-5309',
	 * }
	 *
	 * @since 2.0.0
	 * @param array $data billing information
	 * @return null|string null if billing info is blank, hash string otherwise
	 */
	protected function calculate_billing_hash( $data ) {


		// build from order
		if ( isset( $data['order'] ) && $data['order'] instanceof WC_Order ) {

			$billing = $this->get_billing_from_order( $data['order'] );

		} elseif ( ! empty( $data['billing'] ) ) {

			// build from response, this happens when a customer and/or payment
			// profile is added via the "add new payment method" flow
			// could also occur if the local data saved when the token was
			// created is deleted or becomes corrupted
			$billing = $data['billing'];

		} else {

			// can't calculate
			$billing = array();
		}

		return $this->billing_hash = ( ! empty( $billing ) ? md5( json_encode( $billing ) ) : null );
	}


	/**
	 * Update the billing hash for the profile given an order
	 *
	 * TODO: see note in the constructor about refactoring the interaction between class members (this one set in calculate_billing_hash()) and the $this->data array @MR
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order
	 */
	public function update_billing_hash( WC_Order $order ) {

		$this->data['billing_hash'] = $this->calculate_billing_hash( array( 'order' => $order ) );
	}


	/**
	 * Return true if the billing info saved for this profile matches that on the
	 * provided order. Primarily used to determine if we need to update the billing
	 * info on the token in Authorize.Net prior to processing a transaction.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order
	 * @return bool
	 */
	public function billing_matches_order( WC_Order $order ) {

		$billing = $this->get_billing_from_order( $order );

		return md5( json_encode( $billing ) ) === $this->get_billing_hash();
	}


	/**
	 * Helper to get the correct array format for hashing the billing info from
	 * a given order
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order
	 * @return array
	 */
	protected function get_billing_from_order( WC_Order $order ) {

		return array(
			'first_name' => SV_WC_Order_Compatibility::get_prop( $order, 'billing_first_name' ),
			'last_name'  => SV_WC_Order_Compatibility::get_prop( $order, 'billing_last_name' ),
			'company'    => SV_WC_Order_Compatibility::get_prop( $order, 'billing_company' ),
			'address'    => trim( SV_WC_Order_Compatibility::get_prop( $order, 'billing_address_1' ) . ' ' . SV_WC_Order_Compatibility::get_prop( $order, 'billing_address_2' ) ),
			'city'       => SV_WC_Order_Compatibility::get_prop( $order, 'billing_city' ),
			'state'      => SV_WC_Order_Compatibility::get_prop( $order, 'billing_state' ),
			'postcode'   => SV_WC_Order_Compatibility::get_prop( $order, 'billing_postcode' ),
			'country'    => SV_WC_Order_Compatibility::get_prop( $order, 'billing_country' ),
			'phone'      => SV_WC_Order_Compatibility::get_prop( $order, 'billing_phone' ),
		);
	}


	/**
	 * Calculate the payment hash from an order for a payment profile that is being
	 * added to Authorize.Net -- note this cannot be generated as a result of
	 * retrieving existing tokens from the API because the card type is not returned.
	 * The exact hashed value is the md5 hash of a JSON-encoded array in format:
	 *
	 * {
	 *   'last_four' => '1234',
	 *   'type'      => 'visa',
	 *   'postcode'  => '90210',
	 * }
	 *
	 * This limited set of information is designed to allow us to reasonably detect
	 * when a user is entering a duplicate profile, while not being so overly-
	 * specific that we miss detecting duplicates in the first place. We've deemed
	 * the likelihood of a user having two payment methods, with the same last four,
	 * same type (e.g. visa), and same billing postcode to be fairly minimal,
	 * especially given that a customer profile is limited to a maximum of 10
	 * payment profiles.
	 *
	 * @since 2.0.0
	 * @param array $data payment information
	 * @return null|string null if billing info is blank, hash string otherwise
	 */
	protected function calculate_payment_hash( $data ) {

		// set payment type
		if ( 'credit_card' === $data['type'] ) {
			$type = isset( $data['card_type'] ) ? $data['card_type'] : null;
		} else {
			$type = isset( $data['account_type'] ) ? $data['account_type'] : null;
		}

		// set billing postcode
		if ( empty( $data['billing']['postcode'] ) ) {

			$billing_postcode = ( isset( $data['order'] ) && $data['order'] instanceof WC_Order ) ? SV_WC_Order_Compatibility::get_prop( $data['order'], 'billing_postcode' ) : get_user_meta( get_current_user_id(), 'billing_postcode', true );

		} else {

			$billing_postcode = $data['billing']['postcode'];
		}

		// no way to calculate hash if required info is missing
		if ( empty( $data['last_four'] ) || empty( $type ) || empty( $billing_postcode ) ) {

			return $this->payment_hash = null;
		}

		$payment = array(
			'last_four' => $data['last_four'],
			'type'      => $type,
			'postcode'  => $billing_postcode,
		);

		return $this->payment_hash = md5( json_encode( $payment ) );
	}


	/**
	 * Returns true if the payment information for the given order is a duplicate
	 * of an existing profile within Authorize.Net. A duplicate profile indicates
	 * that we should first remove it, then re-add the submitted profile.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order
	 * @return bool
	 */
	public function is_duplicate_of( WC_Order $order ) {

		$entered_payment = array(
			'last_four' => $order->payment->last_four,
			'type'      => 'credit_card' === $order->payment->type ? $order->payment->card_type : $order->payment->account_type,
			'postcode'  => SV_WC_Order_Compatibility::get_prop( $order, 'billing_postcode' ),
		);

		$entered_payment_hash = md5( json_encode( $entered_payment ) );

		return $entered_payment_hash === $this->get_payment_hash();
	}


	/** Getters/Setters *******************************************************/


	/**
	 * Return the customer profile ID
	 *
	 * @since 2.0.0
	 * @return string customer profile ID
	 */
	public function get_customer_profile_id() {

		return $this->customer_profile_id;
	}


	/**
	 * Set the customer profile ID
	 *
	 * @since 2.0.0
	 * @param string $customer_profile_id customer profile ID
	 */
	public function set_customer_profile_id( $customer_profile_id ) {

		$this->customer_profile_id = $customer_profile_id;
	}


	/**
	 * Return the billing hash
	 *
	 * @since 2.0.0
	 * @return string billing hash
	 */
	public function get_billing_hash() {

		return $this->billing_hash;
	}


	/**
	 * Set the billing hash
	 *
	 * @since 2.0.0
	 * @param string $hash billing hash
	 */
	public function set_billing_hash( $hash ) {

		$this->billing_hash = $hash;
	}


	/**
	 * Return the payment hash
	 *
	 * @since 2.0.0
	 * @return string hash
	 */
	public function get_payment_hash() {

		return $this->payment_hash;
	}


	/**
	 * Set the payment hash
	 *
	 * @since 2.0.0
	 * @param string $hash payment hash
	 */
	public function set_payment_hash( $hash ) {

		$this->payment_hash = $this->data['payment_hash'] = $hash;
	}


}
