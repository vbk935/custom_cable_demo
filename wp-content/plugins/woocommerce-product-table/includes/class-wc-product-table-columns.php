<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for managing the product table columns.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Columns {

	/**
	 * @var WC_Product_Table_Args The table args.
	 */
	public $args;
	/**
	 * @var array Global column defaults.
	 */
	private static $column_defaults = false;

	public function __construct( WC_Product_Table_Args $args ) {
		$this->args = $args;
	}

	public function get_all_columns() {
		return array_merge( $this->get_columns(), $this->get_hidden_columns() );
	}

	public function get_columns() {
		return $this->args->columns;
	}

	public function get_hidden_columns() {
		$hidden = array();

		if ( $this->args->filters ) {
			$hidden = preg_replace( '/^/', 'hf:', $this->args->filters );
		}

		return $hidden;
	}

	public function column_index( $column, $incude_hidden = false ) {
		$cols	 = $incude_hidden ? $this->get_all_columns() : $this->get_columns();
		$index	 = array_search( $column, $cols );
		$index	 = is_int( $index ) ? $index : false; // sanity check

		if ( false !== $index ) {
			if ( 'column' === $this->args->responsive_control ) {
				$index ++;
			}
		}
		return $index;
	}

	public function column_indexes( $columns, $include_hidden = false ) {
		return array_map( array( $this, 'column_index' ), $columns, array_fill( 0, count( $columns ), $include_hidden ) );
	}

	public function get_column_header_class( $index, $column ) {
		$class = array( self::get_column_class( $column ) );

		if ( 0 === $index && 'inline' === $this->args->responsive_control ) {
			$class[] = 'all';
		} elseif ( is_int( $index ) && isset( $this->args->column_breakpoints[$index] ) && 'default' !== $this->args->column_breakpoints[$index] ) {
			$class[] = $this->args->column_breakpoints[$index];
		}
		if ( 'add-to-cart' === $column && $this->args->is_multi_add_to_cart() && ! $this->args->variations && ! $this->args->show_quantity && ! $this->get_column_heading( $index, $column ) ) {
			$class[] = 'checkbox-only';
		}
		return implode( ' ', apply_filters( 'wc_product_table_column_class_' . self::unprefix_column( $column ), $class ) );
	}

	public function get_column_heading( $index, $column ) {
		$heading		 = '';
		$standard_cols	 = self::column_defaults();
		$unprefixed_col	 = self::unprefix_column( $column );

		if ( isset( $standard_cols[$column]['heading'] ) ) {
			$heading = $standard_cols[$column]['heading'];
		} elseif ( $tax = self::get_custom_taxonomy( $column ) ) {
			if ( $tax_obj = get_taxonomy( $tax ) ) {
				$heading = $tax_obj->label;
			}
		} elseif ( $att = self::get_product_attribute( $column ) ) {
			$heading = ucfirst( WCPT_Util::get_attribute_label( $att ) );
		} else {
			$heading = trim( ucwords( str_replace( array( '_', '-' ), ' ', $unprefixed_col ) ) );
		}

		$heading = apply_filters( 'wc_product_table_column_heading_' . $unprefixed_col, $heading );

		if ( is_int( $index ) && ! empty( $this->args->headings[$index] ) ) {
			$heading = 'blank' === $this->args->headings[$index] ? '' : $this->args->headings[$index];
		}

		return $heading;
	}

	public function get_column_priority( $index, $column ) {
		$standard_cols = self::column_defaults();

		$priority	 = isset( $standard_cols[$column]['priority'] ) ? $standard_cols[$column]['priority'] : '';
		$priority	 = apply_filters( 'wc_product_table_column_priority_' . self::unprefix_column( $column ), $priority );

		if ( is_int( $index ) && isset( $this->args->priorities[$index] ) ) {
			$priority = $this->args->priorities[$index];
		}
		return $priority;
	}

	public function get_column_width( $index, $column ) {
		$width = apply_filters( 'wc_product_table_column_width_' . self::unprefix_column( $column ), '' );

		if ( is_int( $index ) && isset( $this->args->widths[$index] ) ) {
			$width = $this->args->widths[$index];
		}
		if ( 'auto' === $width ) {
			$width = '';
		} elseif ( is_numeric( $width ) ) {
			$width = $width . '%';
		}
		return $width;
	}

	public function is_searchable( $column ) {
		$searchable = true;

		if ( 'image' === $column ) {
			$searchable = false;
		}

		// Only allow filtering if column is searchable.
		if ( $searchable ) {
			$searchable	 = apply_filters( 'wc_product_table_column_searchable', $searchable, self::unprefix_column( $column ) );
			$searchable	 = apply_filters( 'wc_product_table_column_searchable_' . self::unprefix_column( $column ), $searchable );
		}

		return $searchable;
	}

	public function is_sortable( $column ) {
		$sortable = false;

		if ( ! $this->args->lazy_load && ! in_array( $column, array( 'add-to-cart', 'button', 'image' ) ) ) {
			$sortable = true;
		}
		if ( $this->args->lazy_load && ( in_array( $column, array( 'id', 'name', 'date', 'price', 'reviews', 'sku' ) ) || self::is_custom_field( $column ) ) ) {
			$sortable = true;
		}

		// Only allow filtering if column is sortable.
		if ( $sortable ) {
			$sortable	 = apply_filters( 'wc_product_table_column_sortable', $sortable, self::unprefix_column( $column ) );
			$sortable	 = apply_filters( 'wc_product_table_column_sortable_' . self::unprefix_column( $column ), $sortable );
		}

		return $sortable;
	}

	public static function get_column_taxonomy( $column ) {
		if ( 'categories' === $column ) {
			return 'product_cat';
		} elseif ( 'tags' === $column ) {
			return 'product_tag';
		} elseif ( $att = self::get_product_attribute( $column ) ) {
			if ( taxonomy_is_product_attribute( $att ) ) {
				return $att;
			}
		} elseif ( $tax = self::get_custom_taxonomy( $column ) ) {
			return $tax;
		}
		return false;
	}

	public static function is_custom_field( $column ) {
		return $column && 'cf:' === substr( $column, 0, 3 ) && strlen( $column ) > 3;
	}

	public static function get_custom_field( $column ) {
		if ( self::is_custom_field( $column ) ) {
			return substr( $column, 3 );
		}
		return false;
	}

	public static function is_custom_taxonomy( $column ) {
		$is_tax = $column && 'tax:' === substr( $column, 0, 4 ) && strlen( $column ) > 4;
		return $is_tax && taxonomy_exists( substr( $column, 4 ) );
	}

	public static function get_custom_taxonomy( $column ) {
		if ( self::is_custom_taxonomy( $column ) ) {
			return substr( $column, 4 );
		}
		return false;
	}

	public static function is_hidden_filter_column( $column ) {
		return $column && 'hf:' === substr( $column, 0, 3 ) && strlen( $column ) > 3;
	}

	public static function get_hidden_filter_column( $column ) {
		if ( self::is_hidden_filter_column( $column ) ) {
			return substr( $column, 3 );
		}
		return false;
	}

	public static function is_product_attribute( $column ) {
		return $column && 'att:' === substr( $column, 0, 4 );
	}

	public static function get_product_attribute( $column ) {
		if ( self::is_product_attribute( $column ) ) {
			return substr( $column, 4 );
		}
		return false;
	}

	public static function unprefix_column( $column ) {
		if ( false !== ( $str = strstr( $column, ':' ) ) ) {
			$column = substr( $str, 1 );
		}
		return $column;
	}

	public static function get_column_class( $column ) {
		return WCPT_Util::sanitize_class_name( 'col-' . self::unprefix_column( $column ) );
	}

	public static function get_column_data_source( $column ) {
		// '.' not allowed in data source
		return str_replace( '.', '', $column );
	}

	public static function get_column_name( $column ) {
		// ':' not allowed in column name as not compatible with DataTables API.
		return str_replace( ':', '_', $column );
	}

	/**
	 * Get the default column headings and responsive priorities.
	 *
	 * @return array The column defaults
	 */
	private static function column_defaults() {

		// Lazy load column defaults but only do it once
		if ( ! self::$column_defaults ) {

			// Priority values are used to determine visiblity at small screen sizes (1 = highest priority).
			self::$column_defaults = apply_filters( 'wc_product_table_column_defaults', array(
				'id'				 => array( 'heading' => __( 'ID', 'woocommerce-product-table' ), 'priority' => 8 ),
				'sku'				 => array( 'heading' => __( 'SKU', 'woocommerce-product-table' ), 'priority' => 6 ),
				'name'				 => array( 'heading' => __( 'Name', 'woocommerce-product-table' ), 'priority' => 1 ),
				'description'		 => array( 'heading' => __( 'Description', 'woocommerce-product-table' ), 'priority' => 12 ),
				'short-description'	 => array( 'heading' => __( 'Summary', 'woocommerce-product-table' ), 'priority' => 11 ),
				'date'				 => array( 'heading' => __( 'Date', 'woocommerce-product-table' ), 'priority' => 14 ),
				'categories'		 => array( 'heading' => __( 'Categories', 'woocommerce-product-table' ), 'priority' => 9 ),
				'tags'				 => array( 'heading' => __( 'Tags', 'woocommerce-product-table' ), 'priority' => 10 ),
				'image'				 => array( 'heading' => __( 'Image', 'woocommerce-product-table' ), 'priority' => 4 ),
				'stock'				 => array( 'heading' => __( 'Stock', 'woocommerce-product-table' ), 'priority' => 7 ),
				'reviews'			 => array( 'heading' => __( 'Reviews', 'woocommerce-product-table' ), 'priority' => 13 ),
				'weight'			 => array( 'heading' => __( 'Weight', 'woocommerce-product-table' ), 'priority' => 15 ),
				'dimensions'		 => array( 'heading' => __( 'Dimensions', 'woocommerce-product-table' ), 'priority' => 16 ),
				'price'				 => array( 'heading' => __( 'Price', 'woocommerce-product-table' ), 'priority' => 3 ),
				'add-to-cart'		 => array( 'heading' => __( 'Buy', 'woocommerce-product-table' ), 'priority' => 2 ),
				'button'			 => array( 'heading' => __( 'Details', 'woocommerce-product-table' ), 'priority' => 5 )
				) );
		}

		return self::$column_defaults;
	}

}