<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the caching for product tables.
 *
 * There are 2 types of caching used:
 *  - Table caching: this is used for lazy load tables where we need to initially create the table,
 * then later fetch the table by ID to fetch the actual products.
 *  - Data caching: this is used to cache the data in a table, and is enabled or disabled using
 * the 'cache' option in the shortcode, or from the plugin settings.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Cache {

	const TABLE_CACHE_EXPIRY = DAY_IN_SECONDS;

	public $id;
	public $args;
	public $query;

	public function __construct( $id, WC_Product_Table_Args $args, WC_Product_Table_Query $query ) {
		$this->id	 = $id;
		$this->args	 = $args;
		$this->query = $query;
	}

	/**
	 * @deprecated 2.2.3 - Replaced by get_table().
	 */
	public static function load_from_cache( $id ) {
		return self::get_table( $id );
	}

	public static function get_table( $id ) {
		$table_cache = get_transient( $id );
		$table		 = false;

		if ( $table_cache && isset( $table_cache['args'] ) ) {
			$table = new WC_Product_Table( $id, $table_cache['args'] );

			if ( isset( $table_cache['total_posts'] ) ) {
				$table->query->set_total_products( $table_cache['total_posts'] );
			}
			if ( isset( $table_cache['total_filtered_posts'] ) ) {
				$table->query->set_total_filtered_products( $table_cache['total_filtered_posts'] );
			}
		}

		return $table;
	}

	public function add_table() {
		if ( ! $this->table_caching_enabled() ) {
			return;
		}

		$table_cache = array( 'args' => $this->args->get_args() );
		set_transient( $this->get_table_cache_key(), $table_cache, self::TABLE_CACHE_EXPIRY );
	}

	public function update_table( $update_totals = false ) {
		if ( ! $this->table_caching_enabled() ) {
			return;
		}

		if ( $table_cache = get_transient( $this->id ) ) {
			// Existing table found, so update it.
			$table_cache['args'] = $this->args->get_args();

			if ( $update_totals ) {
				$table_cache['total_posts']			 = $this->query->get_total_products();
				$table_cache['total_filtered_posts'] = $this->query->get_total_filtered_products();
			}

			set_transient( $this->get_table_cache_key(), $table_cache, self::TABLE_CACHE_EXPIRY );
		} else {
			// No existing table in cache, so add it.
			$this->add_table();
		}
	}

	public function get_data() {
		if ( ! $this->data_caching_enabled() ) {
			return false;
		}

		if ( $data = get_transient( $this->get_data_cache_key() ) ) {
			// Check for old cached data abd delete if found.
			if ( $this->cache_contains_legacy_data( $data ) ) {
				$this->delete_data();
				return false;
			}

			return $data;
		}
		return false;
	}

	public function update_data( $data ) {
		if ( $this->data_caching_enabled() ) {
			// Limit maximum size of cacheable data to prevent storing very large transients
			if ( count( $data ) <= 1000 ) {
				$misc_setings	 = WCPT_Settings::get_setting_misc();
				$cache_expiry	 = ( ! empty( $misc_setings['cache_expiry'] ) ? $misc_setings['cache_expiry'] : 6 ) * HOUR_IN_SECONDS;

				set_transient( $this->get_data_cache_key(), $data, apply_filters( 'wc_product_table_data_cache_expiry', $cache_expiry, $this ) );
			}
		} else {
			// Flush cache if not using
			$this->delete_data();
		}
	}

	public function delete_data() {
		delete_transient( $this->get_data_cache_key() );
	}

	private function table_caching_enabled() {
		return $this->args->lazy_load;
	}

	private function data_caching_enabled() {
		$caching_data = apply_filters( 'wc_product_table_use_data_cache', $this->args->cache, $this );

		// Disable caching if filter widgets are active
		if ( WCPT_Util::get_layered_nav_params( $this->args->lazy_load ) ) {
			$caching_data = false;
		}

		// Disable caching if WC Password Protected Categories is active, as product list will vary if there are protected or private categories.
		if ( WCPT_Util::is_wc_ppc_active() ) {
			$caching_data = false;
		}

		return $caching_data;
	}

	private function get_table_cache_key() {
		return $this->id;
	}

	private function get_data_cache_key() {
		$key = $this->id . '_data';

		// For lazy load, cache each page of data based on offset
		if ( $this->args->lazy_load ) {
			$key .= '_' . $this->args->offset;
		}
		return $key;
	}

	private function cache_contains_legacy_data( $data ) {
		if ( empty( $data[0] ) ) {
			return false;
		}
		$first_row = $data[0]->to_array();

		if ( ! empty( $first_row['cells'] ) && array_filter( array_keys( $first_row['cells'] ), array( __CLASS__, 'is_legacy_hidden_column' ) ) ) {
			return true;
		}
		return false;
	}

	private static function is_legacy_hidden_column( $column ) {
		return false !== strpos( $column, '_hfilter' ) || false !== strpos( $column, '_hsort' );
	}

}