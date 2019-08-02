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
 * Handle the payment tokenization related functionality.
 *
 * @since 2.2.0
 */
class WC_Authorize_Net_CIM_Payment_Profile_Handler extends SV_WC_Payment_Gateway_Payment_Tokens_Handler {


	/**
	 * Return a custom payment token class instance
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway_Payment_Tokens_Handler::build_token()
	 * @return bool
	 */
	public function build_token( $token, $data ) {

		return new WC_Authorize_Net_CIM_Payment_Profile( $token, $data );
	}


	/**
	 * When retrieving payment profiles via the Auth.net API, it returns both
	 * credit/debit card *and* eCheck payment profiles from a single call. Overriding
	 * the core framework update method ensures that eChecks aren't saved to
	 * the credit card token meta entry, and vice versa.
	 *
	 * @since 2.2.0
	 * @param int $user_id WP user ID
	 * @param array $tokens array of tokens
	 * @param string $environment_id optional environment id, defaults to plugin current environment
	 * @return string updated user meta id
	 */
	public function update_tokens( $user_id, $tokens, $environment_id = null ) {

		foreach ( $tokens as $token_id => $token ) {

			if ( ( $this->get_gateway()->is_credit_card_gateway() && ! $token->is_credit_card() ) || ( $this->get_gateway()->is_echeck_gateway() && ! $token->is_echeck() ) ) {
				unset( $tokens[ $token_id ] );
			}
		}

		return parent::update_tokens( $user_id, $tokens, $environment_id );
	}



	/** Admin methods *****************************************************************************/


	/**
	 * Get the admin token editor instance.
	 *
	 * @since 2.2.0
	 * @return \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor
	 */
	public function get_token_editor() {
		return new WC_Authorize_Net_CIM_Payment_Profile_Editor( $this->get_gateway() );
	}


	/** Conditional methods ***********************************************************************/


	/**
	 * Add additional attributes to be used when merging local token data into
	 * remote tokens, as Authorize.Net doesn't include certain things like
	 * expiration dates or card types when fetching tokens from the API, but that
	 * info is saved with the token locally when it's first created
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway_Payment_Tokens_Handler::get_merge_attributes()
	 * @return array merge attributes
	 */
	protected function get_merge_attributes() {

		return array_merge( parent::get_merge_attributes(), array( 'billing_hash', 'payment_hash' ) );
	}
}
