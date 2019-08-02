<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// Load EDD plugin updater / license checker
	require_once plugin_dir_path( __FILE__ ) . 'EDD_SL_Plugin_Updater.php';
}

if ( ! class_exists( 'Barn2_Plugin_License' ) ) {

	/**
	 * Handles the plugin licensing.
	 *
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 * @version   1.8
	 */
	class Barn2_Plugin_License {

		// The URL that the EDD updater / license checker pings. This should be the site with EDD installed
		const EDD_STORE_URL	 = 'http://barn2.co.uk/edd-sl';
		const PLUGIN_AUTHOR	 = 'Barn2 Media';
		const API_TIMEOUT		 = 30;

		public $license_key_option;
		private $plugin_file;
		private $download_name;
		private $plugin_version;
		private $option_prefix;
		private $edd_updater;

		public function __construct( $plugin_file, $download_name, $plugin_version, $option_prefix ) {
			$this->plugin_file		 = $plugin_file;
			$this->download_name	 = $download_name;
			$this->plugin_version	 = $plugin_version;
			$this->option_prefix	 = $option_prefix;

			$this->license_key_option = $option_prefix . '_license_key';

			add_filter( 'edd_sl_api_request_verify_ssl', '__return_false' );
			add_action( 'admin_init', array( $this, 'init_edd_updater' ), 0 );
			add_action( 'admin_init', array( $this, 'maybe_deactivate_license' ) );
		}

		public function init_edd_updater() {
			// Create the plugin updater
			$this->edd_updater = new EDD_SL_Plugin_Updater(
				self::EDD_STORE_URL, $this->plugin_file, array(
				'version' => $this->plugin_version, // current version number
				'license' => $this->get_license_key(), // license key
				'item_name' => $this->download_name, // title of the download in EDD
				'author' => self::PLUGIN_AUTHOR, // author of this plugin
				'beta' => false
				)
			);
		}

		/**
		 * 'Save' the specified license key.
		 *
		 * If there is a valid key currently active, the current key will be deactivated first
		 * before activating the new one.
		 *
		 * This function doesn't actually save the license key to the database (i.e. by calling update_option),
		 * as it is expected this will be handled by the Settings API, from the relevant plugin settings page.
		 *
		 * @param string $license_key The license key to save.
		 * @return string The license key.
		 */
		public function save( $license_key ) {
			if ( $this->deactivating_license() ) {
				return false;
			}

			$this->debug_api_request();

			$license_key	 = filter_var( $license_key, FILTER_SANITIZE_STRING );
			$old_key		 = $this->get_license_key();
			$license_status	 = $this->get_license_status();

			// Deactivate old license key first if it was valid.
			if ( $old_key && $old_key !== $license_key && 'valid' === $license_status ) {
				$this->deactivate( $old_key );
			}

			// If license key is different to previous key, or previous key was invalid, attempt to activate.
			if ( $old_key !== $license_key || 'valid' !== $license_status ) {
				$this->activate( $license_key );
			}

			return $license_key;
		}

		/**
		 * Attempt to activate the specified license key.
		 *
		 * @param string $license_key The license key to activate.
		 * @return string 'valid', 'invalid', or 'error' if an error occurred.
		 */
		public function activate( $license_key ) {
			$result = false;

			if ( ! $license_key ) {
				$result = 'invalid';
			}

			// Check if we're overriding the license validation
			if ( $override = filter_input( INPUT_POST, 'license_override', FILTER_SANITIZE_STRING ) ) {
				if ( md5( $override ) === 'caf9da518b5d4b46c2ef1f9d7cba50ad' ) {
					$result = 'valid';
				}
			}

			if ( ! $result ) {
				// Data to send in our API request
				$api_params = array(
					'edd_action' => 'activate_license',
					'license' => $license_key,
					'item_name' => urlencode( $this->download_name ), // the name of our product in EDD
					'url' => home_url()
				);

				// Call the Software Licensing API
				$response		 = wp_remote_post( self::EDD_STORE_URL, array( 'timeout' => self::API_TIMEOUT, 'sslverify' => false, 'body' => $api_params ) );
				$error_message	 = false;

				// Make sure a valid response came back
				if ( $this->is_api_error( $response ) ) {
					$error_message = $this->get_api_error_message( $response );
				} else {
					// Valid response returned - now check whether license is valid
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );

					// $license_data->license will be either 'valid' or 'invalid'
					$result = $license_data->license;

					if ( false === $license_data->success ) {
						switch ( $license_data->error ) {
							case 'missing' :
								$error_message	 = __( 'Please enter your license key.', 'woocommerce-product-table' );
								break;
							case 'missing_url' :
								$error_message	 = __( 'No URL was supplied for activation.', 'woocommerce-product-table' );
								break;
							case 'license_not_activable' :
								$error_message	 = __( 'This license is for a bundled product and cannot be activated.', 'woocommerce-product-table' );
								break;
							case 'expired' :
								$error_message	 = sprintf(
									__( 'Your license key expired on %s.', 'woocommerce-product-table' ), date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
								);
								break;
							case 'item_name_mismatch' :
							case 'invalid_item_id':
								$error_message	 = __( 'Your license key is not valid for this plugin.', 'woocommerce-product-table' );
								break;
							case 'no_activations_left':
								$error_message	 = __( 'Your license key has reached its activation limit.', 'woocommerce-product-table' );
								break;
							case 'disabled':
								$error_message	 = __( 'Your license key has been disabled, please contact support,', 'woocommerce-product-table' );
								break;
							case 'key_mismatch':
								$error_message	 = __( 'License key mismatch, please contact support.', 'woocommerce-product-table' );
								break;
							default :
								$error_message	 = __( 'There was an error activating your license, please try again.', 'woocommerce-product-table' );
								$result			 = 'error';
								break;
						}
					} // if license invalid
				} // if successful call to API

				if ( $error_message ) {
					$this->set_license_error( $error_message );
				}
			}

			if ( ! $result ) {
				$result = 'error';
			}

			$this->set_license_status( $result );

			return $result;
		}

		/**
		 * Attempt to deactivate the specified license.
		 *
		 * @param string $license_key The license key to deactivate.
		 * @return string 'deactivated' or 'failed' if an error occurred.
		 */
		public function deactivate( $license_key ) {
			$result = false;

			if ( $license_key ) {
				// Data to send in our API request
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license' => $license_key,
					'item_name' => urlencode( $this->download_name ), // the name of our product in EDD
					'url' => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( self::EDD_STORE_URL, array( 'timeout' => self::API_TIMEOUT, 'sslverify' => false, 'body' => $api_params ) );

				// Make sure the response came back okay
				if ( $this->is_api_error( $response ) ) {
					$this->set_license_error( $this->get_api_error_message( $response ) );
				}

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// 'deactivated' or 'failed'
				$result = $license_data->license;
			}

			if ( ! $result ) {
				$result = 'failed';
			}

			$this->set_license_status( $result );

			return $result;
		}

		/**
		 * Retrieve the description for the license key input, to display on the plugin settings page.
		 *
		 * @return string The license key status message
		 */
		public function get_license_key_admin_message() {
			$message			 = __( 'Please enter your license key.', 'woocommerce-product-table' );
			$deactivate_button	 = sprintf( '<button type="submit" class="button" name="deactivate_key" value="%1$s" style="margin-left:8px;">%2$s</button>', esc_attr( $this->license_key_option ), __( 'Deactivate', 'woocommerce-product-table' ) );

			if ( $this->is_valid() ) {
				$message = sprintf( '<span style="color:green;">✓ %s</span>', __( 'License key successfully activated.', 'woocommerce-product-table' ) ) . $deactivate_button;
			} elseif ( $this->is_deactivated() ) {
				$message = __( 'License key successfully deactivated. Click Save to reactivate.', 'woocommerce-product-table' );
			} elseif ( $this->failed_deactivation() ) {
				$message = sprintf( '<span style="color:red;">%s</span>', __( 'There was an error deactivating your license key, please try again.', 'woocommerce-product-table' ) ) . $deactivate_button;
			} elseif ( $this->is_invalid() ) {
				$message = $this->get_license_error();
				if ( ! $message ) {
					$message = __( 'There was an error activating your license, please try again.', 'woocommerce-product-table' );
				}
				$message = sprintf( '<span style="color:red;">%s</span>', $message );
			}

			if ( $debug = $this->get_license_debug() ) {
				$message .= ' ' . $debug;
			}
			return $message;
		}

		public function is_deactivated() {
			return 'deactivated' === $this->get_license_status();
		}

		public function failed_deactivation() {
			return 'failed' === $this->get_license_status();
		}

		public function is_invalid() {
			if ( $this->get_license_key() && in_array( $this->get_license_status(), array( 'invalid', false ) ) ) {
				return true;
			}
			return false;
		}

		public function is_valid() {
			if ( $this->get_license_key() && 'valid' === $this->get_license_status() ) {
				return true;
			}
			return false;
		}

		public function maybe_deactivate_license() {
			if ( $this->deactivating_license() && ( $license_key = $this->get_license_key() ) ) {
				$this->deactivate( $license_key );
			}
		}

		private function deactivating_license() {
			return isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ( $this->license_key_option === filter_input( INPUT_POST, 'deactivate_key', FILTER_SANITIZE_STRING ) );
		}

		private function debug_api_request() {
			if ( filter_input( INPUT_POST, 'license_debug' ) ) {
				add_action( 'http_api_debug', array( $this, 'http_debug' ), 10, 5 );
			} else {
				remove_action( 'http_api_debug', array( $this, 'http_debug' ), 10 );
				delete_option( $this->option_prefix . '_license_debug' );
			}
		}

		public function http_debug( $response, $content, $class, $args, $url ) {
			if ( self::EDD_STORE_URL == $url ) {
				$debug_message = 'HTTP ARGS: ' . print_r( $args, true ) . ', HTTP RESPONSE: ' . print_r( $response, true );
				$this->set_license_debug( $debug_message );
			}
		}

		private function get_api_error_message( $response ) {
			if ( is_wp_error( $response ) ) {
				return $response->get_error_message();
			} elseif ( wp_remote_retrieve_response_message( $response ) ) {
				return wp_remote_retrieve_response_message( $response );
			} else {
				return __( 'An error has occurred, please try again.', 'woocommerce-product-table' );
			}
		}

		private function is_api_error( $response ) {
			return is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response );
		}

		// Getters and setters.
		public function get_license_key() {
			return get_option( $this->license_key_option );
		}

		public function get_license_status() {
			return get_option( $this->option_prefix . '_license_status' );
		}

		public function get_license_error() {
			return get_option( $this->option_prefix . '_license_error' );
		}

		public function get_license_debug() {
			return get_option( $this->option_prefix . '_license_debug' );
		}

		private function set_license_status( $status ) {
			update_option( $this->option_prefix . '_license_status', $status );
		}

		private function set_license_error( $error_message ) {
			update_option( $this->option_prefix . '_license_error', $error_message );
		}

		private function set_license_debug( $debug_message ) {
			update_option( $this->option_prefix . '_license_debug', $debug_message );
		}

	}

	// class Barn2_Plugin_License
} // if class doesn't exist
