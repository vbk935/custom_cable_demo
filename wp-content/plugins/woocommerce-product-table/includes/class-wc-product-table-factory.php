<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Table factory class.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Factory {

	private static $tables		 = array();
	private static $current_id	 = 1;

	/**
	 * Create a new table based on the supplied args.
	 *
	 * @param array $args The args to use for the table.
	 * @return WC_Product_Table The product table object.
	 */
	public static function create( $args ) {
		// Merge in the default args, so our table ID reflects the full list of args, including settings page.
		$args	 = wp_parse_args( $args, WC_Product_Table_Args::get_defaults() );
		$id		 = self::generate_id( $args );

		$table				 = new WC_Product_Table( $id, $args );
		self::$tables[$id]	 = $table;

		return $table;
	}

	/**
	 * Fetch an existing table by ID.
	 *
	 * @param string $id The product table ID.
	 * @return WC_Product_Table The product table object.
	 */
	public static function fetch( $id ) {
		if ( empty( $id ) ) {
			return false;
		}

		$table = false;

		if ( isset( self::$tables[$id] ) ) {
			$table = self::$tables[$id];
		} elseif ( $table = WC_Product_Table_Cache::get_table( $id ) ) {
			self::$tables[$id] = $table;
		}

		return $table;
	}

	private static function generate_id( $args ) {
		$id = 'wcpt_' . substr( md5( serialize( $args ) ), 0, 16 ) . '_' . self::$current_id;
		self::$current_id ++;

		return $id;
	}

}