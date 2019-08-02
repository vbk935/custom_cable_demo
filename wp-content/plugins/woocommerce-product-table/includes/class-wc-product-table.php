<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main WC_Product_Table class.
 *
 * Responsible for creating the products table from the specified args and returning the
 * complete table as a Html_Data_Table instance.
 *
 * The main functions provided are get_table() and get_data().
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table {

	public $id;

	/* Helper classes */
	public $args;
	public $query;
	public $hooks;
	public $data_table;
	private $columns;
	private $data_factory;
	private $config_builder;
	private $cache;

	/* Internal flags */
	private $table_initialised	 = false;
	private $data_added			 = false;

	const CONTROL_COLUMN_DATA_SOURCE = 'control';

	public function __construct( $id, $args = array() ) {
		$this->id = $id;

		// Initialize helpers
		$this->args				 = new WC_Product_Table_Args( $args );
		$this->query			 = new WC_Product_Table_Query( $this->args );
		$this->columns			 = new WC_Product_Table_Columns( $this->args );
		$this->data_factory		 = new WC_Product_Table_Data_Factory( $this->args );
		$this->hooks			 = new WC_Product_Table_Hook_Manager( $this->args );
		$this->config_builder	 = new WC_Product_Table_Config_Builder( $this->id, $this->args, $this->columns );
		$this->cache			 = new WC_Product_Table_Cache( $this->id, $this->args, $this->query );
		$this->data_table		 = new Html_Data_Table();
	}

	/**
	 * Retrieves the data table containing the list of products based on the arguments
	 * supplied on construction.
	 *
	 * The table returned includes the table headings, attributes and data.
	 *
	 * If the output is 'object' the returned object is an Html_Data_Table instance.
	 * If the output is 'html', the table returned will be a string containing the <table> element
	 * at its root, and include the <thead>, <tfoot> and <tbody> elements.
	 * If the output is 'array', the table returned will be an array containing the following keys:
	 * 'thead', 'tbody', 'tfoot' and 'attributes'.
	 * If the output is 'json', the return value will be a JSON-encoded string in the same format
	 * as 'array'.
	 *
	 * @param string $output The output format - object, html, array or json. Default 'object'.
	 * @return object|array|string The product table in the requested format.
	 */
	public function get_table( $output = 'object' ) {

		if ( ! $this->table_initialised ) {
			// Add table to cache.
			$this->cache->add_table();

			// Reset the table
			$this->data_table->reset();

			do_action( 'wc_product_table_before_get_table', $this );

			// Register scripts for this table.
			WC_Product_Table_Frontend_Scripts::register_table_scripts( $this->args );

			// Add attriutes and table headings.
			$this->add_attributes();
			$this->add_headings();

			// Fetch the data.
			$this->fetch_data();

			// @deprecated 2.1.3 - Replaced by 'wc_product_table_after_get_table'.
			do_action( 'wc_product_table_get_table', $this );
			do_action( 'wc_product_table_after_get_table', $this );

			$this->table_initialised = true;
		}

		$result = $this->data_table;

		if ( 'html' === $output ) {
			$result = $this->data_table->to_html();

			// Include any 'add to cart' messages above table if we're not using AJAX.
			if ( ! $this->args->ajax_cart && method_exists( 'WC_Shortcodes', 'shop_messages' ) ) {
				$result = WC_Shortcodes::shop_messages() . $result;
			}
		} elseif ( 'array' === $output ) {
			$result = $this->data_table->to_array();
		} elseif ( 'json' === $output ) {
			$result = $this->data_table->to_json();
		}

		return apply_filters( 'wc_product_table_get_table_output', $result, $output, $this );
	}

	/**
	 * Retrieves the data table containing the list of products based on the specified arguments.
	 *
	 * The table returned includes only the table data itself (i.e. the rows), and doesn't include the header, footer, etc.
	 *
	 * If the output is 'object' the returned object will be an Html_Data_Table instance.
	 * If the output is 'html', the data returned will be a string containing a list of <tr> elements,
	 * one for each product found.
	 * If the output is 'array', the data returned will be an array of rows, one for each product found.
	 * if the output is 'json', the data returned will be a JSON-encoded string in the same format
	 * as 'array'.
	 *
	 * @return object|array|string The product table data in the requested format.
	 */
	public function get_data( $output = 'object' ) {
		// Fetch the data.
		$this->fetch_data();

		$result = $this->data_table;

		// Build the output.
		if ( 'html' === $output ) {
			$result = $this->data_table->to_html( true );
		} elseif ( 'array' === $output ) {
			$result = $this->data_table->to_array( true );
		} elseif ( 'json' === $output ) {
			$result = $this->data_table->to_json( true );
		}

		return apply_filters( 'wc_product_table_get_data_output', $result, $output, $this );
	}

	/**
	 * Update the table with new arguments specified in $args. Previously posts data is preserved where possible,
	 * to prevent additional DB calls.
	 *
	 * @param array $new_args The new args. Does not need to be a complete list (will be merged with current properties)
	 */
	public function update( array $new_args ) {
		$this->table_initialised = false;
		$this->data_added		 = false;

		// Work out what changed
		$args_changed = array_keys( WCPT_Util::array_diff_assoc( $new_args, get_object_vars( $this->args ) ) );

		if ( array_intersect( $args_changed, array( 'status', 'year', 'month', 'day', 'category', 'tag', 'term', 'cf', 'exclude', 'include', 'exclude_category' ) ) ) {
			// If any of the post paramaters are updated, reset posts array and totals
			$this->query->set_products( null );
			$this->query->set_total_products( null );
			$this->query->set_total_filtered_products( null );
		} elseif ( array_intersect( $args_changed, array( 'rows_per_page', 'product_limit', 'offset', 'sort_by', 'sort_order' ) ) ) {
			// If just the table paramaters are updated, reset posts but not totals
			$this->query->set_products( null );
		}

		// If our search term or search filters changed from last time, reset products and filtered total, but leave the overall total.
		if ( array_intersect( $args_changed, array( 'search_term', 'search_filters' ) ) ) {
			$this->query->set_products( null );
			$this->query->set_total_filtered_products( null );
		}

		// Don't use cache if lazy loading and query params have been modified (e.g. rows_per_page, sort_by, etc)
		// We don't check offset here as we cache each page of results separately using offset in the cache key
		if ( $this->args->lazy_load && array_intersect( $args_changed, array( 'status', 'year', 'month', 'day', 'category', 'tag', 'term', 'cf', 'exclude', 'include', 'exclude_category', 'rows_per_page', 'product_limit', 'sort_by', 'sort_order', 'search_term', 'search_filters' ) ) ) {
			$new_args['cache'] = false;
		}

		// Next we update the args - this will update the args object in all helper classes as objects are stored by reference.
		$this->args->set_args( $new_args );

		do_action( 'wc_product_table_args_updated', $this );
	}

	private function fetch_data() {
		if ( $this->data_added || ! $this->can_fetch_data() ) {
			return;
		}

		// Reset the table data
		$this->data_table->reset_data();

		if ( $data = $this->cache->get_data() ) {
			$this->data_table->set_data( $data );
		} else {
			// No cache found or caching disabled...
			do_action( 'wc_product_table_before_get_data', $this );

			// Register the data hooks.
			$this->hooks->register();

			// Add all products to the table.
			$this->add_products_to_table( $this->query->get_products() );

			// Reset hooks.
			$this->hooks->reset();

			// Update the cache.
			$this->cache->update_data( $this->data_table->get_data() );
			$this->cache->update_table( true );

			// @deprecated 2.1.3 - Replaced by 'wc_product_table_after_get_data'.
			do_action( 'wc_product_table_get_data', $this );
			do_action( 'wc_product_table_after_get_data', $this );
		}

		$this->data_added = true;
	}

	private function can_fetch_data() {
		if ( ! $this->args->lazy_load ) {
			return true;
		} else {
			return $this->args->lazy_load && defined( 'DOING_AJAX' ) && DOING_AJAX;
		}
	}

	/**
	 * Add the products (array of post objects) to the table.
	 *
	 * @param array $products An array of WC_Product objects
	 */
	private function add_products_to_table( $products ) {
		// Bail if no products to add
		if ( ! $products ) {
			return;
		}

		// To make sure the post and product globals are reset, we store them here and set it back after our product loop.
		$old_global_post	 = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : false;
		$old_global_product	 = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : false;

		// Get required columns to walk through
		$cols = $this->columns->get_all_columns();

		// Loop through array of WC_Products and add data for each.
		foreach ( $products as $product ) {

			// If it's a variable product and we're displaying separate variations, process variations and continue to next product.
			if ( 'separate' === $this->args->variations && 'variable' === $product->get_type() ) {
				$this->add_products_to_table( WCPT_Util::get_available_variations( $product ) );
				continue;
			}

			$this->setup_postdata_for_product( $product );

			$this->data_table->new_row( $this->get_row_attributes( $product ) );

			// Add an empty cell if we're using the control column for responsive child rows
			if ( 'column' === $this->args->responsive_control ) {
				$this->data_table->add_data( '', false, self::CONTROL_COLUMN_DATA_SOURCE );
			}

			// Add the data for this product
			array_walk( $cols, array( $this, 'add_product_data' ), $product );
		}

		// Reset main WP query as we called setup_postdata
		if ( $old_global_post ) {
			$GLOBALS['post'] = $old_global_post;
		}
		if ( $old_global_product ) {
			$GLOBALS['product'] = $old_global_product;
		}

		wp_reset_postdata();
	}

	private function add_product_data( $column, $key, $product ) {
		$data	 = '';
		$atts	 = false;

		// Get data object for this column
		if ( $data_obj = $this->data_factory->create( $column, $product ) ) {
			$data	 = $data_obj->get_data();
			$atts	 = array_filter(
				array(
					'data-sort' => $data_obj->get_sort_data(),
					'data-filter' => $data_obj->get_filter_data()
				),
				array( __CLASS__, 'array_filter_empty_attribute' )
			);
		} else {
			// Back compat: data object not found (e.g. custom column) so run filter
			$data	 = apply_filters( 'wc_product_table_custom_data_' . $column, '', WCPT_Util::get_post( $product ), $product );
			$atts	 = apply_filters( 'wc_product_table_custom_data_atts_' . $column, false, $product );
		}

		//@deprecated 2.2 - Replaced by individual column filters.
		$data = apply_filters( 'wc_product_table_cell_data_' . WC_Product_Table_Columns::unprefix_column( $column ), $data, $product );

		$this->data_table->add_data( $data, $atts, WC_Product_Table_Columns::get_column_data_source( $column ) );
	}

	private function add_attributes() {
		// Set table attributes.
		$table_class = trim( 'wc-product-table woocommerce ' . ( $this->args->wrap ? '' : 'nowrap ' ) . apply_filters( 'wc_product_table_custom_class', '', $this ) );

		$this->data_table->add_attribute( 'id', $this->id );
		$this->data_table->add_attribute( 'class', $table_class );

		// This is required otherwise tables can expand beyond their container.
		$this->data_table->add_attribute( 'width', '100%' );

		// Add the table config as JSON encoded data.
		$this->data_table->add_attribute( 'data-config', htmlspecialchars( self::json_encode_config( $this->config_builder->get_config() ) ) );
		$this->data_table->add_attribute( 'data-filters', htmlspecialchars( wp_json_encode( $this->config_builder->get_filters() ) ) );

		// Set table ordering during initialisation - default to no ordering (i.e. use post order returned from WP_Query).
		$order_attr = '[]';

		// If column is sortable, set initial sort order for DataTables.
		if ( 'menu_order' !== $this->args->sort_by && $this->columns->is_sortable( $this->args->sort_by ) ) {
			$sort_index = $this->columns->column_index( $this->args->sort_by );

			if ( false !== $sort_index ) {
				// 'sort_order' has to be in double quotes (@see https://datatables.net/manual/options).
				$order_attr = sprintf( '[[%u, "%s"]]', $sort_index, $this->args->sort_order );
			}
		}
		$this->data_table->add_attribute( 'data-order', $order_attr );
	}

	private function add_headings() {
		// Add the control column for responsive layouts if required (the column that contains the + / - icon)
		if ( 'column' === $this->args->responsive_control ) {
			$this->add_heading( '', array( 'data-data' => self::CONTROL_COLUMN_DATA_SOURCE ), self::CONTROL_COLUMN_DATA_SOURCE );
		}

		// Add column headings
		foreach ( $this->columns->get_columns() as $i => $column ) {
			$data_source = WC_Product_Table_Columns::get_column_data_source( $column );

			$column_atts = array(
				'class' => $this->columns->get_column_header_class( $i, $column ),
				'data-name' => WC_Product_Table_Columns::get_column_name( $column ), // used to easily pick out column in JS, e.g. dataTable.column( 'sku:name' ).
				'data-orderable' => $this->columns->is_sortable( $column ),
				'data-searchable' => $this->columns->is_searchable( $column ),
				'data-width' => $this->columns->get_column_width( $i, $column ),
				'data-priority' => $this->columns->get_column_priority( $i, $column )
			);

			if ( $this->args->lazy_load ) {
				// Data source required only for lazy load - used to identify the column from JSON data.
				$column_atts['data-data'] = $data_source;
			}

			$this->add_heading( $this->columns->get_column_heading( $i, $column ), $column_atts, $data_source );
		}

		// Add hidden columns
		foreach ( $this->columns->get_hidden_columns() as $column ) {
			$data_source = WC_Product_Table_Columns::get_column_data_source( $column );

			$column_atts = array(
				'data-name' => WC_Product_Table_Columns::get_column_name( $column ),
				'data-searchable' => $this->columns->is_searchable( $column ),
				'data-visible' => $this->args->show_hidden
			);

			if ( $this->args->lazy_load ) {
				// Data source required only for lazy load.
				$column_atts['data-data'] = $data_source;
			}

			$this->add_heading( $column, $column_atts, $data_source );
		}
	}

	private function add_heading( $heading, $attributes, $key ) {
		$this->data_table->add_header( $heading, $attributes, $key );

		if ( $this->args->show_footer ) {
			$this->data_table->add_footer( $heading, false, $key ); // attributes not needed in footer.
		}
	}

	private function get_row_attributes( $product ) {
		$id = wcpt_get_id( $product );

		$classes = array(
			'product-row',
			'product-' . $id,
			wcpt_get_stock_status( $product )
		);

		if ( $product->is_on_sale() ) {
			$classes[] = 'sale';
		}
		if ( $product->is_featured() ) {
			$classes[] = 'featured';
		}
		if ( $product->is_downloadable() ) {
			$classes[] = 'downloadable';
		}
		if ( $product->is_virtual() ) {
			$classes[] = 'virtual';
		}
		if ( $product->is_sold_individually() ) {
			$classes[] = 'sold-individually';
		}
		if ( $product->is_taxable() ) {
			$classes[] = 'taxable';
		}
		if ( $product->is_shipping_taxable() ) {
			$classes[] = 'shipping-taxable';
		}
		if ( $product->is_purchasable() ) {
			$classes[] = 'purchasable';
		}
		if ( $product->get_type() ) {
			$classes[] = 'product-type-' . $product->get_type();
		}

		$row_attributes = array(
			'id' => 'product-row-' . $id,
			'class' => implode( ' ', apply_filters( 'wc_product_table_row_class', $classes, $product ) )
		);

		return apply_filters( 'wc_product_table_row_attributes', $row_attributes, $product );
	}

	private function setup_postdata_for_product( $product ) {
		$product_post = get_post( $product->get_id() );

		// Set global post object, so that any code referring to 'global $post' (e.g. get_the_content) works correctly.
		$GLOBALS['post'] = $product_post;

		// Setup global post data (id, authordata, etc) and global product.
		setup_postdata( $product_post );
	}

	private static function array_filter_empty_attribute( $value ) {
		return ( '' === $value ) ? false : true;
	}

	private static function json_encode_config( $config ) {
		$json_config = wp_json_encode( $config );

		// Ensure JS functions are defined as a function, not a string, in the encoded json
		return preg_replace( '#"(jQuery\.fn.*)"#U', '$1', $json_config );
	}
}

// class WC_Product_Table
