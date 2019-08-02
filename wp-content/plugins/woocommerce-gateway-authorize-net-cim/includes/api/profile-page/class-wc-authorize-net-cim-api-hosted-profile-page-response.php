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
 * @package   WC-Gateway-Authorize-Net-CIM/API/Response
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Authorize.Net CIM API Hosted Profile Page Response Class
 *
 * Parses the hosted profile page response
 *
 * @since 2.0.0
 */
class WC_Authorize_Net_CIM_API_Hosted_Profile_Page_Response extends WC_Authorize_Net_CIM_API_Response {


	/**
	 * Return the page token for generating the hosted profile page
	 *
	 * @since 2.0.0
	 * @return null|string page token
	 */
	public function get_page_token() {

		return ! empty( $this->response_xml->token ) ? (string) $this->response_xml->token : null;
	}


	/**
	 * Determine if this was a test request.
	 *
	 * @since 2.1.2
	 * @return bool
	 */
	public function is_test_request() {

		// TODO: If \WC_Authorize_Net_CIM_API_Hosted_Profile_Page_Request is ever used for anything other
		// than to check if CIM is available, then actually check something here.
		return false;
	}
}
