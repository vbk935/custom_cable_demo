<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for storing and validating the product table arguments.
 * Parses an array of args into the corresponding properties.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Args {

	// The original args array
	private $args = array();

	/* Table params */
	public $columns;
	public $headings; // built from columns
	public $widths;
	public $auto_width;
	public $priorities;
	public $column_breakpoints;
	public $responsive_control;
	public $responsive_display;
	public $wrap;
	public $show_footer;
	public $search_on_click;
	public $filters;
	public $show_quantity;
	public $variations;
	public $cart_button;
	public $ajax_cart;
	public $scroll_offset;
	public $description_length;
	public $links;
	public $lazy_load;
	public $cache;
	public $image_size;
	public $lightbox;
	public $shortcodes;
	public $button_text;
	public $date_format;
	public $date_columns;
	public $no_products_message;
	public $no_products_filtered_message;
	public $paging_type;
	public $page_length;
	public $search_box;
	public $totals;
	public $pagination;
	public $reset_button;
	public $add_selected_button;


	/* Query params */
	public $rows_per_page;
	public $product_limit;
	public $sort_by;
	public $sort_order;
	public $status;
	public $category;
	public $exclude_category;
	public $tag;
	public $term;
	public $numeric_terms;
	public $cf;
	public $year;
	public $month;
	public $day;
	public $exclude;
	public $include;
	public $search_term;

	/* Internal params */
	public $show_hidden;

	/* Lazy load params */
	public $offset			 = 0;
	public $search_filters	 = array();

	/**
	 * @var array The default table parameters
	 */
	public static $default_args			 = array(
		'columns' => 'name, short-description, price, add-to-cart', // any from STANDARD_COLS, plus any attribute (eg. att:pa_colour), taxonomy (eg. tax:product_vendor) or custom field (eg. cf:my_field)
		'widths' => '',
		'auto_width' => true,
		'priorities' => '',
		'column_breakpoints' => '',
		'responsive_control' => 'inline', // inline or column
		'responsive_display' => 'child_row', // child_row, child_row_visible, or modal
		'wrap' => true,
		'show_footer' => false,
		'search_on_click' => true,
		'filters' => false,
		'show_quantity' => false,
		'variations' => false,
		'cart_button' => 'button', // button, button_checkbox, checkbox
		'ajax_cart' => true,
		'scroll_offset' => 15,
		'description_length' => 15, // number of words
		'links' => 'all', // allowed: all, none, or any combination of id, sku, name, image, tags, categories, terms, attributes
		'lazy_load' => false,
		'cache' => false,
		'image_size' => '70x70',
		'lightbox' => true,
		'shortcodes' => false,
		'button_text' => '',
		'date_format' => '',
		'date_columns' => '',
		'no_products_message' => '',
		'no_products_filtered_message' => '',
		'paging_type' => 'simple_numbers',
		'page_length' => 'top',
		'search_box' => 'top',
		'totals' => 'bottom',
		'pagination' => 'bottom',
		'reset_button' => true,
		'add_selected_button' => 'top',
		'rows_per_page' => 25,
		'product_limit' => 500,
		'sort_by' => 'menu_order',
		'sort_order' => '', // no default set - @see parse_args
		'status' => 'publish',
		'category' => '', // list of slugs or IDs
		'exclude_category' => '', // list of slugs or IDs
		'tag' => '', // list of slugs or IDs
		'term' => '', // list of terms of the form <taxonomy>:<term>
		'numeric_terms' => false, // set to true if using categories, tags or terms with numeric slugs
		'cf' => '', // list of custom fields of the form <field_key>:<field_value>
		'year' => '', // four digit year, e.g. 2011
		'month' => '', // two digit month, e.g. 12
		'day' => '', // two digit day, e.g. 03
		'exclude' => '', // list of post IDs
		'include' => '', // list of post IDs
		'search_term' => '',
		'show_hidden' => false
	);
	private static $standard_columns	 = array(
		'id', 'sku', 'name', 'description', 'short-description', 'date', 'categories', 'tags', 'image', 'reviews', 'stock', 'weight', 'dimensions', 'price', 'add-to-cart', 'button'
	);
	private static $column_replacements	 = array(
		'ID' => 'id',
		'SKU' => 'sku',
		'title' => 'name',
		'content' => 'description',
		'excerpt' => 'short-description',
		'category' => 'categories',
		'rating' => 'reviews'
	);

	public function __construct( array $args = array() ) {
		$this->set_args( $args );
	}

	public function get_args() {
		return $this->args;
	}

	public function set_args( array $args ) {
		// Lazy load args need to be merged in
		$hidden = array(
			'offset' => $this->offset,
			'search_filters' => $this->search_filters
		);

		// Update args
		$this->args = array_merge( $hidden, $this->args, $args );

		// Parse/validate args & update properties
		$this->parse_args( $this->args );
	}

	public function is_multi_add_to_cart() {
		return in_array( $this->cart_button, array( 'checkbox', 'button_checkbox' ) ) && in_array( 'add-to-cart', $this->columns );
	}

	public static function get_defaults() {
		return wp_parse_args( WCPT_Settings::settings_to_table_args( WCPT_Settings::get_setting_table_defaults() ), self::$default_args );
	}

	private function parse_args( array $args ) {
		$defaults = self::get_defaults();

		// Merge in default args.
		$args = wp_parse_args( $args, $defaults );

		// Define custom validation callbacks.
		$sanitize_list				 = array(
			'filter' => FILTER_CALLBACK,
			'options' => 'WCPT_Util::sanitize_list_arg'
		);
		$sanitize_numeric_list		 = array(
			'filter' => FILTER_CALLBACK,
			'options' => 'WCPT_Util::sanitize_numeric_list_arg'
		);
		$sanitize_string_or_array	 = array(
			'filter' => FILTER_CALLBACK,
			'options' => 'WCPT_Util::sanitize_string_or_array_arg',
			'flags' => FILTER_REQUIRE_ARRAY
		);
		$sanitize_string_or_bool	 = array(
			'filter' => FILTER_CALLBACK,
			'options' => 'WCPT_Util::sanitize_string_or_bool_arg'
		);

		// Setup validation array.
		$validation = array(
			'columns' => $sanitize_string_or_array,
			'widths' => $sanitize_list,
			'auto_width' => FILTER_VALIDATE_BOOLEAN,
			'priorities' => $sanitize_numeric_list,
			'column_breakpoints' => $sanitize_list,
			'responsive_control' => FILTER_SANITIZE_STRING,
			'responsive_display' => FILTER_SANITIZE_STRING,
			'wrap' => FILTER_VALIDATE_BOOLEAN,
			'show_footer' => FILTER_VALIDATE_BOOLEAN,
			'search_on_click' => FILTER_VALIDATE_BOOLEAN,
			'filters' => $sanitize_string_or_bool,
			'show_quantity' => FILTER_VALIDATE_BOOLEAN,
			'variations' => $sanitize_string_or_bool,
			'cart_button' => FILTER_SANITIZE_STRING,
			'ajax_cart' => FILTER_VALIDATE_BOOLEAN,
			'scroll_offset' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['scroll_offset']
				)
			),
			'description_length' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['description_length'],
					'min_range' => -1
				)
			),
			'links' => $sanitize_string_or_bool,
			'lazy_load' => FILTER_VALIDATE_BOOLEAN,
			'cache' => FILTER_VALIDATE_BOOLEAN,
			'image_size' => $sanitize_list,
			'lightbox' => FILTER_VALIDATE_BOOLEAN,
			'shortcodes' => FILTER_VALIDATE_BOOLEAN,
			'button_text' => FILTER_SANITIZE_STRING,
			'date_format' => FILTER_SANITIZE_STRING,
			'date_columns' => $sanitize_list,
			'no_products_message' => FILTER_SANITIZE_STRING,
			'no_products_filtered_message' => FILTER_SANITIZE_STRING,
			'paging_type' => FILTER_SANITIZE_STRING,
			'page_length' => $sanitize_string_or_bool,
			'search_box' => $sanitize_string_or_bool,
			'totals' => $sanitize_string_or_bool,
			'pagination' => $sanitize_string_or_bool,
			'reset_button' => FILTER_VALIDATE_BOOLEAN,
			'add_selected_button' => FILTER_SANITIZE_STRING,
			'rows_per_page' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['rows_per_page'],
					'min_range' => -1
				)
			),
			'product_limit' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['product_limit'],
					'min_range' => -1,
					'max_range' => 5000,
				)
			),
			'sort_by' => FILTER_SANITIZE_STRING,
			'sort_order' => FILTER_SANITIZE_STRING,
			'status' => $sanitize_list,
			'category' => $sanitize_list,
			'exclude_category' => $sanitize_list,
			'tag' => $sanitize_list,
			'term' => $sanitize_list,
			'numeric_terms' => FILTER_VALIDATE_BOOLEAN,
			'cf' => array(
				'filter' => FILTER_CALLBACK,
				'options' => 'WCPT_Util::sanitize_list_arg_allow_space'
			),
			'year' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['year'],
					'min_range' => 1
				)
			),
			'month' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['month'],
					'min_range' => 1,
					'max_range' => 12
				)
			),
			'day' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => $defaults['day'],
					'min_range' => 1,
					'max_range' => 31
				)
			),
			'exclude' => $sanitize_numeric_list,
			'include' => $sanitize_numeric_list,
			'search_term' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
			),
			// Internal params
			'show_hidden' => FILTER_VALIDATE_BOOLEAN,
			// Lazy load params
			'offset' => array(
				'filter' => FILTER_VALIDATE_INT,
				'options' => array(
					'default' => 0,
					'min_range' => 0,
				)
			),
			'search_filters' => $sanitize_string_or_array
		);

		// Sanitize/validate all args.
		$args = filter_var_array( $args, $validation );

		// Set properties from the sanitized args.
		WCPT_Util::set_object_vars( $this, $args );

		// Fill in any blanks.
		foreach ( array( 'columns', 'status', 'image_size', 'sort_by', 'links' ) as $arg ) {
			if ( empty( $this->$arg ) ) {
				$this->$arg = $defaults[$arg];
			}
		}

		// Make sure boolean args are definitely booleans - sometimes filter_var_array doesn't convert them properly
		foreach ( array_filter( $validation, array( __CLASS__, 'array_filter_validate_boolean' ) ) as $arg => $val ) {
			$this->$arg = ( $this->$arg === true || $this->$arg === 'true' ) ? true : false;
		}

		// Convert list based args to arrays. filters, links, category, tag, term, and cf are handled separately.
		foreach ( array( 'columns', 'widths', 'priorities', 'column_breakpoints', 'status', 'include', 'exclude', 'exclude_category', 'date_columns' ) as $arg ) {
			$this->$arg = WCPT_Util::string_list_to_array( $this->$arg );
		}

		// Validate and parse the columns and headings to use in products table
		if ( $parsed_columns = self::parse_columns_arg( $this->columns ) ) {
			$this->columns	 = $parsed_columns['columns'];
			$this->headings	 = $parsed_columns['headings'];
		} else {
			$this->columns	 = WCPT_Util::string_list_to_array( $defaults['columns'] );
			$this->headings	 = array();
		}

		// Column widths
		if ( $this->widths ) {
			$this->widths = WCPT_Util::array_pad_and_slice( $this->widths, count( $this->columns ), 'auto' );
		}

		// Responsive options
		if ( $this->priorities ) {
			$this->priorities = WCPT_Util::array_pad_and_slice( $this->priorities, count( $this->columns ), 'default' );
		}
		if ( ! in_array( $this->responsive_control, array( 'inline', 'column' ) ) ) {
			$this->responsive_control = $defaults['responsive_control'];
		}
		if ( ! in_array( $this->responsive_display, array( 'child_row', 'child_row_visible', 'modal' ) ) ) {
			$this->responsive_display = $defaults['responsive_display'];
		}
		if ( $this->column_breakpoints ) {
			$this->column_breakpoints = WCPT_Util::array_pad_and_slice( $this->column_breakpoints, count( $this->columns ), 'default' );
		}

		// Variations
		if ( true === $this->variations ) {
			$this->variations = 'dropdown';
		} elseif ( ! in_array( $this->variations, array( 'dropdown', 'separate' ) ) ) {
			$this->variations = false;
		}

		// Separate variations not currently supported for lazy load
		if ( 'separate' === $this->variations && $this->lazy_load ) {
			$this->variations = 'dropdown';
		}

		// Filters dropdowns
		$this->filters = self::parse_filters_arg( $this->filters, $this->variations, $this->columns );

		// Cart button
		if ( ! in_array( $this->cart_button, array( 'button', 'button_checkbox', 'checkbox' ) ) ) {
			$this->cart_button = $defaults['cart_button'];
		}

		// Add selected button
		if ( ! in_array( $this->add_selected_button, array( 'top', 'bottom', 'both' ) ) ) {
			$this->add_selected_button = $defaults['add_selected_button'];
		}

		// Text for 'button' column button
		if ( ! $this->button_text ) {
			$this->button_text = __( 'Show details', 'woocommerce-product-table' );
		}

		// Display options (page length, etc)
		foreach ( array( 'page_length', 'search_box', 'totals', 'pagination' ) as $display_option ) {
			if ( ! in_array( $this->$display_option, array( 'top', 'bottom', 'both', false ), true ) ) {
				$this->$display_option = $defaults[$display_option];
			}
		}

		// Links - used to control whether certain data items are links or plain text
		$this->links = is_string( $this->links ) ? strtr( strtolower( $this->links ), self::$column_replacements ) : $this->links;

		if ( true === $this->links || 'all' === $this->links ) {
			$this->links = array( 'all' );
		} elseif ( false === $this->links || 'none' === $this->links ) {
			$this->links = array();
		} else {
			$this->links = array_intersect( explode( ',', $this->links ), array( 'sku', 'name', 'image', 'categories', 'tags', 'terms', 'attributes' ) );
		}

		// Paging type
		if ( ! in_array( $this->paging_type, array( 'numbers', 'simple', 'simple_numbers', 'full', 'full_numbers' ) ) ) {
			$this->paging_type = $defaults['paging_type'];
		}

		// Image size
		$this->image_size	 = str_replace( array( ' ', ',' ), array( '', 'x' ), $this->image_size );
		$size_arr			 = explode( 'x', $this->image_size );
		$size_numeric_count	 = count( array_filter( $size_arr, 'is_numeric' ) );

		if ( 1 === $size_numeric_count ) {
			// One number, so use for both width and height
			$this->image_size = array( $size_arr[0], $size_arr[0] );
		} elseif ( 2 === $size_numeric_count ) {
			// Width and height specified
			$this->image_size = $size_arr;
		} // otherwise assume it's a text-based image size, e.g. 'thumbnail'

		$this->set_image_column_width();

		// Lightbox - disable if Photoswipe not available
		if ( ! WCPT_Util::doing_lazy_load() ) {
			$this->lightbox = $this->lightbox && wp_script_is( 'photoswipe-ui-default', 'registered' );
		}

		// Validate date columns - only custom fields or taxonomies allowed
		if ( $this->date_columns ) {
			$this->date_columns = array_filter( $this->date_columns, array( __CLASS__, 'array_filter_custom_field_or_taxonomy' ) );
		}

		// Sort by
		$this->sort_by = strtr( $this->sort_by, self::$column_replacements );

		// If sorting by attribute, make sure it uses the full attribute name.
		if ( $sort_att = WC_Product_Table_Columns::get_product_attribute( $this->sort_by ) ) {
			$this->sort_by = 'att:' . WCPT_Util::get_attribute_name( $sort_att );
		}

		// Sort order - set default if not specified or invalid
		$this->sort_order = strtolower( $this->sort_order );

		if ( ! in_array( $this->sort_order, array( 'asc', 'desc' ) ) ) {
			// Default to descending if sorting by date, ascending for everything else
			$this->sort_order = in_array( $this->sort_by, array_merge( array( 'date', 'modified' ), $this->date_columns ) ) ? 'desc' : 'asc';
		}

		// Search term
		if ( ! WCPT_Util::is_valid_search_term( $this->search_term ) ) {
			$this->search_term = '';
		}

		// Product limit
		// @deprecated 2.1 - Replaced with 'wc_product_table_max_product_limit'
		$this->product_limit = apply_filters( 'wc_product_table_max_posts_limit', $this->product_limit, $this );

		$this->product_limit = apply_filters( 'wc_product_table_max_product_limit', $this->product_limit, $this );

		// Description length & rows per page - can be positive int or -1
		foreach ( array( 'description_length', 'rows_per_page', 'product_limit' ) as $arg ) {
			// Sanity check in case filter set an invalid value
			if ( ! is_int( $this->$arg ) || $this->$arg < -1 ) {
				$this->$arg = $defaults[$arg];
			}
			if ( 0 === $this->$arg ) {
				$this->$arg = -1;
			}
		}

		// Ignore product limit if lazy loading and the default product limit is used.
		if ( $this->lazy_load && $defaults['product_limit'] === $this->product_limit ) {
			$this->product_limit = -1;
		}

		// If enabling shortcodes, display the full content
		if ( $this->shortcodes ) {
			$this->description_length = -1;
		}

		// If auto width disabled, we must use the inline +/- control otherwise control column is always shown
		if ( ! $this->auto_width ) {
			$this->responsive_control = 'inline';
		}

		do_action( 'wc_product_table_parse_args', $this );
	}

	public static function parse_columns_arg( $raw_columns ) {
		$columns	 = array();
		$headings	 = array();

		if ( ! is_array( $raw_columns ) ) {
			$raw_columns = WCPT_Util::string_list_to_array( $raw_columns );
		}

		foreach ( $raw_columns as $raw_column ) {
			$prefix	 = strtok( $raw_column, ':' );
			$col	 = false;

			if ( in_array( $prefix, array( 'cf', 'att', 'tax' ) ) ) {
				// Custom field, product attribute or taxonomy column.
				$suffix = trim( strtok( ':' ) );

				if ( ! $suffix ) {
					continue; // no custom field, attribute, or taxonomy specified
				} elseif ( 'att' === $prefix ) {
					$suffix = WCPT_Util::get_attribute_name( $suffix );
				} elseif ( 'tax' === $prefix && ! taxonomy_exists( $suffix ) ) {
					continue; // invalid taxonomy
				}

				$col = $prefix . ':' . $suffix;
			} else {
				// Standard column - search & replace common typos in column names
				$check = strtr( $prefix, self::$column_replacements );

				if ( in_array( $check, self::$standard_columns ) ) {
					$col = $check;
				}
			}

			// Support for custom columns.
			if ( ! $col && has_filter( 'wc_product_table_custom_table_data_' . $prefix ) ) {
				$col = $prefix;
			}

			// Only add column if valid and not added already (duplicate columns not allowed)
			if ( $col && ! in_array( $col, $columns ) ) {
				$columns[]	 = $col;
				$headings[]	 = strtok( '' ); // fetch rest of heading (even if it includes a ':')
			}
		}

		return $columns ? array(
			'columns' => $columns,
			'headings' => $headings
			) : false;
	}

	public static function parse_filters_arg( $raw_filters, $variations, $columns ) {
		if ( ! $raw_filters ) {
			return false;
		}

		$filter_columns	 = $raw_filters;
		$result			 = array();

		if ( true === $filter_columns ) {
			// If filters=true, filters are based on the table contents.
			$filter_columns = $variations ? str_replace( 'add-to-cart', 'attributes', $columns ) : $columns;
		}

		if ( is_string( $filter_columns ) ) {
			$filter_columns = explode( ',', WCPT_Util::sanitize_list_arg( $filter_columns ) );
		}

		if ( is_array( $filter_columns ) ) {
			foreach ( $filter_columns as $filter ) {
				if ( in_array( $filter, array( 'categories', 'tags' ) ) ) {
					// Categories or tags filter
					$result[] = $filter;
				} elseif ( 'attributes' === $filter ) {
					// 'attributes' keyword - replace with all available product attributes
					$result = array_merge( $result, preg_replace( '/^/', 'att:', wc_get_attribute_taxonomy_names() ) );
				} elseif ( WC_Product_Table_Columns::is_custom_taxonomy( $filter ) ) {
					// Custom taxonomy filter
					$result[] = $filter;
				} elseif ( $att = WC_Product_Table_Columns::get_product_attribute( $filter ) ) {
					// Product attribute filter
					$attribute_name = WCPT_Util::get_attribute_name( $att );

					// Only global attributes (i.e. taxonomies) are allowed as a filter
					if ( taxonomy_is_product_attribute( $attribute_name ) ) {
						$result[] = 'att:' . $attribute_name;
					}
				}
			}
		}

		return $result ? array_unique( $result ) : false;
	}

	private function set_image_column_width() {

		if ( false === ( $image_col = array_search( 'image', $this->columns ) ) ) {
			return;
		}

		if ( $this->widths && isset( $this->widths[$image_col] ) && 'auto' !== $this->widths[$image_col] ) {
			return;
		}

		if ( $image_col_width = WCPT_Util::get_image_size_width( $this->image_size ) ) {
			if ( ! $this->widths ) {
				$this->widths = array_fill( 0, count( $this->columns ), 'auto' );
			}
			$this->widths[$image_col] = $image_col_width . 'px';
		}
	}

	private static function array_filter_validate_boolean( $var ) {
		return $var === FILTER_VALIDATE_BOOLEAN;
	}

	private static function array_filter_custom_field_or_taxonomy( $column ) {
		return WC_Product_Table_Columns::is_custom_field( $column ) || WC_Product_Table_Columns::is_custom_taxonomy( $column );
	}

}
