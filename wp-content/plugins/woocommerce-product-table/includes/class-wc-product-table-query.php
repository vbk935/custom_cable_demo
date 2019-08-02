<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for managing the product table query, retrieving the list of products (as an array of WP_Post objects), and finding the product totals.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Query {

	public $args;
	private $products				 = null;
	private $total_products			 = null;
	private $total_filtered_products = null;

	public function __construct( WC_Product_Table_Args $args ) {
		$this->args = $args;
	}

	public function get_products() {
		if ( is_array( $this->products ) ) {
			return $this->products;
		}

		// Build query args and retrieve the products for our table.
		$query = $this->run_product_query( $this->build_product_query() );

		// Convert posts to products and store the results.
		$products = ! empty( $query->posts ) ? array_filter( array_map( 'wc_get_product', $query->posts ) ) : array();
		$this->set_products( $products );

		return $this->products;
	}

	public function set_products( $products ) {
		if ( is_object( $products ) && isset( $products['products'] ) ) {
			// Support for wc_get_products function
			$products = $products['products'];
		} elseif ( ! is_array( $products ) ) {
			$products = null;
		}
		$this->products = $products;
	}

	public function get_total_products() {
		if ( is_numeric( $this->total_products ) ) {
			return $this->total_products;
		}

		$total_args	 = $this->build_product_totals_query();
		$total_query = $this->run_product_query( $total_args );

		$this->total_products = $this->check_within_product_limit( $total_query->post_count );

		return $this->total_products;
	}

	public function set_total_products( $total_products ) {
		$this->total_products = $total_products;
	}

	public function get_total_filtered_products() {
		if ( is_numeric( $this->total_filtered_products ) ) {
			// If we've already calculated it
			return $this->total_filtered_products;
		} elseif ( empty( $this->args->search_term ) && empty( $this->args->search_filters ) ) {
			// If we have no search term, the filtered total will be same as unfiltered
			$this->total_filtered_products = $this->get_total_products();
		} elseif ( is_array( $this->products ) ) {
			// If we already have products, then this must be the filtered list, so return count from this array
			$this->total_filtered_products = count( $this->products );
		} else {
			// Otherwise we need to calculate total by calling get_posts()
			$filtered_total_args				 = $this->build_product_totals_query();
			$filtered_total_args['tax_query']	 = $this->build_search_filters_tax_query( $filtered_total_args['tax_query'] );
			$filtered_total_query				 = $this->run_product_query( $filtered_total_args );

			$this->total_filtered_products = $this->check_within_product_limit( $filtered_total_query->post_count );
		}

		return $this->total_filtered_products;
	}

	public function set_total_filtered_products( $total_filtered_products ) {
		$this->total_filtered_products = $total_filtered_products;
	}

	private function build_base_product_query() {
		$query_args = array(
			'post_type' => 'product',
			'post_status' => $this->args->status,
			'tax_query' => $this->build_tax_query(),
			'meta_query' => $this->build_meta_query(),
			'no_found_rows' => true,
			'suppress_filters' => false // Ensure WPML and WC posts filters run on this query
		);

		if ( ! empty( $query_args['meta_query']['product_table_order_clause'] ) ) {
			// Use named order clause if we have one.
			$query_args['orderby'] = 'product_table_order_clause';
		} else {
			// Otherwise build order args.
			$query_args = array_merge( $query_args, $this->get_ordering_args() );
		}

		if ( $this->args->year ) {
			$query_args['year'] = $this->args->year;
		}
		if ( $this->args->month ) {
			$query_args['monthnum'] = $this->args->month;
		}
		if ( $this->args->day ) {
			$query_args['day'] = $this->args->day;
		}

		if ( $this->args->include ) {
			$query_args['post__in']				 = $this->args->include;
			$query_args['ignore_sticky_posts']	 = true;
		} elseif ( $this->args->exclude ) {
			$query_args['post__not_in'] = $this->args->exclude;
		}

		if ( $this->args->search_term ) {
			$query_args['s'] = $this->args->search_term;
		}

		return $query_args;
	}

	private function build_product_query() {
		$query_args				 = $this->build_base_product_query();
		$query_args['tax_query'] = $this->build_search_filters_tax_query( $query_args['tax_query'] );

		if ( $this->args->lazy_load ) {
			$query_args['posts_per_page']	 = $this->check_within_product_limit( $this->args->rows_per_page );
			$query_args['offset']			 = $this->args->offset;
		} else {
			$query_args['posts_per_page'] = $this->args->product_limit;
		}

		return apply_filters( 'wc_product_table_query_args', $query_args, $this );
	}

	private function build_product_totals_query() {
		$query_args						 = $this->build_base_product_query();
		$query_args['offset']			 = 0;
		$query_args['posts_per_page']	 = -1;
		$query_args['fields']			 = 'ids';

		return apply_filters( 'wc_product_table_query_args', $query_args, $this );
	}

	/**
	 * Get the ordering args for our product query.
	 *
	 * Note: for standard loading, DataTables will re-sort the results if the sort column is present in table.
	 *
	 * @return array The ordering args.
	 */
	private function get_ordering_args() {
		// Replace column name with correct sort_by item used by WP_Query.
		$orderby = str_replace(
			array( 'name', 'reviews' ),
			array( 'title', 'rating' ),
			$this->args->sort_by
		);
		$order	 = strtoupper( $this->args->sort_order );

		// Bail if we don't have a valid orderby arg.
		// Note! Custom field and SKU sorting is handled by build_meta_query()
		if ( ! in_array( $orderby, array( 'id', 'title', 'menu_order', 'rand', 'relevance', 'price', 'popularity', 'rating', 'date', 'modified' ) ) ) {
			return array();
		}

		// Use WC to get standard ordering args and add required query filters.
		$ordering = WC()->query->get_catalog_ordering_args( $orderby, $order );

		// Additional orderby options.
		if ( 'modified' === $orderby ) {
			$ordering['orderby'] = 'modified ID';
		}

		if ( empty( $ordering['meta_key'] ) ) {
			unset( $ordering['meta_key'] );
		}

		return $ordering;
	}

	private function build_tax_query() {
		$tax_query = array();

		if ( method_exists( WC()->query, 'get_tax_query' ) ) {
			$tax_query = WC()->query->get_tax_query( $tax_query, true );
		}

		// Category handling.
		if ( $this->args->category ) {
			$tax_query[] = $this->tax_query_item( $this->args->category, 'product_cat' );
		}
		if ( $this->args->exclude_category ) {
			$tax_query[] = $this->tax_query_item( $this->args->exclude_category, 'product_cat', 'NOT IN' );
		}

		// Tag handling.
		if ( $this->args->tag ) {
			$tax_query[] = $this->tax_query_item( $this->args->tag, 'product_tag' );
		}

		// Custom taxonomy/term handling.
		if ( $this->args->term ) {
			$term_query		 = array();
			$relation		 = 'OR';
			$term_taxonomy	 = false;

			if ( false !== strpos( $this->args->term, '+' ) ) {
				$term_array	 = explode( '+', $this->args->term );
				$relation	 = 'AND';
			} else {
				$term_array = explode( ',', $this->args->term );
			}

			// Custom terms are in format <taxonomy>:<term slug or id> or a list using just one taxonony, e.g. product_cat:term1,term2.
			foreach ( $term_array as $term ) {
				if ( '' === $term ) {
					continue;
				}
				// Split term around the colon and check valid
				$term_split = explode( ':', $term, 2 );

				if ( 1 === count( $term_split ) ) {
					if ( ! $term_taxonomy ) {
						continue;
					}
					$term = $term_split[0];
				} elseif ( 2 === count( $term_split ) ) {
					$term			 = $term_split[1];
					$term_taxonomy	 = $term_split[0];
				}
				$term_query[] = $this->tax_query_item( $term, $term_taxonomy );
			}

			$term_query = $this->maybe_add_relation( $term_query, $relation );

			// If no tax query, set the whole tax query to the custom terms query, otherwise append terms as inner query.
			if ( empty( $tax_query ) ) {
				$tax_query = $term_query;
			} else {
				$tax_query[] = $term_query;
			}
		}

		return apply_filters( 'wc_product_table_tax_query', $this->maybe_add_relation( $tax_query ), $this );
	}

	private function build_search_filters_tax_query( $tax_query = array() ) {
		if ( ! is_array( $tax_query ) ) {
			$tax_query = array();
		}

		if ( empty( $this->args->search_filters ) ) {
			return $tax_query;
		}

		$search_filters_query = array();

		// Add tax queries for search filter drop-downs.
		foreach ( $this->args->search_filters as $taxonomy => $term ) {
			// Search filters always use term IDs
			$search_filters_query[] = $this->tax_query_item( $term, $taxonomy, 'IN', 'term_id' );
		}

		$search_filters_query = $this->maybe_add_relation( $search_filters_query );

		// If no tax query, set the whole tax query to the filters query, otherwise append filters as inner query
		if ( empty( $tax_query ) ) {
			// If no tax query, set the whole tax query to the filters query.
			$tax_query = $search_filters_query;
		} elseif ( isset( $tax_query['relation'] ) && 'OR' === $tax_query['relation'] ) {
			// If tax query is an OR, nest it with the search filters query and join with AND.
			$tax_query = array( $tax_query, $search_filters_query, 'relation' => 'AND' );
		} else {
			// Otherwise append search filters and ensure it's AND.
			$tax_query[]			 = $search_filters_query;
			$tax_query['relation']	 = 'AND';
		}

		return $tax_query;
	}

	private function tax_query_item( $terms, $taxonomy, $operator = 'IN', $field = '' ) {
		$and_relation = 'AND' === $operator;

		if ( ! is_array( $terms ) ) {
			// Comma-delimited list = OR, plus-delimited list = AND
			if ( false !== strpos( $terms, '+' ) ) {
				$terms			 = explode( '+', $terms );
				$and_relation	 = true;
			} else {
				$terms = explode( ',', $terms );
			}
		}

		// If no field provided, work out whether we have term slugs or ids.
		if ( ! $field ) {
			$using_term_ids	 = count( $terms ) === count( array_filter( $terms, 'is_numeric' ) );
			$field			 = $using_term_ids && ! $this->args->numeric_terms ? 'term_id' : 'slug';
		}

		// There's a strange bug when using 'operator' => 'AND' for individual tax queries.
		// So we need to split these into separate 'IN' arrays joined by and outer relation => 'AND'
		if ( $and_relation && count( $terms ) > 1 ) {
			$result = array( 'relation' => 'AND' );

			foreach ( $terms as $term ) {
				$result[] = array(
					'taxonomy' => $taxonomy,
					'terms' => $term,
					'operator' => 'IN',
					'field' => $field
				);
			}

			return $result;
		} else {
			return array(
				'taxonomy' => $taxonomy,
				'terms' => $terms,
				'operator' => $operator,
				'field' => $field
			);
		}
	}

	private function build_meta_query() {
		// First, build the WooCommerce meta query.
		$meta_query = WC()->query->get_meta_query();

		if ( $this->args->cf ) {
			$custom_field_query	 = array();
			$relation			 = 'OR';

			// Comma-delimited = OR, plus-delimited = AND.
			if ( false !== strpos( $this->args->cf, '+' ) ) {
				$field_array = explode( '+', $this->args->cf );
				$relation	 = 'AND';
			} else {
				$field_array = explode( ',', $this->args->cf );
			}

			// Custom fields are in format <field_key>:<field_value>
			foreach ( $field_array as $field ) {
				// Split custom field around the colon and check valid
				$field_split = explode( ':', $field, 2 );

				if ( 2 === count( $field_split ) ) {
					// We have a field key and value
					$field_key	 = $field_split[0];
					$field_value = $field_split[1];
					$compare	 = '=';

					// If we're selecting based on an ACF field, field value could be stored as an array, so use RLIKE with a test for serialized array pattern
					if ( WCPT_Util::is_acf_active() ) {
						$compare	 = 'REGEXP';
						$field_value = sprintf( '^%1$s$|s:%2$u:"%1$s";', $field_value, strlen( $field_value ) );
					}

					$custom_field_query[] = array(
						'key' => $field_key,
						'value' => $field_value,
						'compare' => $compare
					);
				} elseif ( 1 === count( $field_split ) ) {
					// Field key only, so do an 'exists' check instead
					$custom_field_query[] = array(
						'key' => $field_split[0],
						'compare' => 'EXISTS'
					);
				}
			}

			$meta_query['product_table'] = $this->maybe_add_relation( $custom_field_query, $relation );
		}

		if ( WC_Product_Table_Columns::is_custom_field( $this->args->sort_by ) ) {
			// Sort by custom field.
			$field	 = WC_Product_Table_Columns::get_custom_field( $this->args->sort_by );
			$type	 = in_array( 'cf:' . $field, $this->args->date_columns ) ? 'DATE' : 'CHAR';

			$meta_query['product_table_order_clause'] = array(
				'key' => $field,
				'type' => apply_filters( 'wc_product_table_sort_by_custom_field_type', $type, $field )
			);
		} elseif ( 'sku' === $this->args->sort_by ) {
			// Sort by SKU.
			$numeric_skus	 = apply_filters( 'wc_product_table_use_numeric_skus', false );
			$order_by_clause = array(
				'key' => '_sku',
				'type' => $numeric_skus ? 'NUMERIC' : 'CHAR'
			);

			if ( $numeric_skus ) {
				$order_by_clause['value']	 = 0;
				$order_by_clause['compare']	 = '>=';
			}

			$meta_query['product_table_order_clause'] = $order_by_clause;
		}

		return apply_filters( 'wc_product_table_meta_query', $this->maybe_add_relation( $meta_query ), $this );
	}

	private function maybe_add_relation( $query, $relation = 'AND' ) {
		if ( count( $query ) > 1 && empty( $query['relation'] ) ) {
			$query['relation'] = $relation;
		}

		return $query;
	}

	private function run_product_query( $query_args ) {
		// Add our query hooks before running the query.
		$this->add_query_hooks();

		//@todo: Use 'wc_get_products' instead of WP_Query. We can't yet as price filter widget and other meta queries are not passed through.
		$query = new WP_Query( $query_args );

		// Remove the hooks to prevent them interfering with anything else.
		$this->remove_query_hooks();

		return $query;
	}

	private function add_query_hooks() {
		// Query optimisations.
		if ( apply_filters( 'wc_product_table_optimize_table_query', true, $this->args ) ) {
			add_filter( 'posts_fields', array( $this, 'filter_wp_posts_selected_columns' ), 10, 2 );
		}

		// Fix the meta query SQL for lazy load search by SKU.
		if ( $this->sku_query_hooks_required() ) {
			add_filter( 'posts_search', array( $this, 'search_by_sku_posts_search' ), 10, 2 );
			add_filter( 'posts_clauses', array( $this, 'search_by_sku_posts_clauses' ), 10, 2 );
		}

		// Post clauses for price filter widget.
		add_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ), 10, 2 );
	}

	private function remove_query_hooks() {
		if ( apply_filters( 'wc_product_table_optimize_table_query', true, $this->args ) ) {
			remove_filter( 'posts_fields', array( $this, 'filter_wp_posts_selected_columns' ), 10 );
		}

		if ( $this->sku_query_hooks_required() ) {
			remove_filter( 'posts_search', array( $this, 'search_by_sky_posts_search' ), 10 );
			remove_filter( 'posts_clauses', array( $this, 'search_by_sku_posts_clauses' ), 10 );
		}

		// We call WC()->query->get_catalogue_ordering_args() while building our product query, which adds various filters.
		// These can interfere with any subsequent queries while building table data, so we need to remove them.
		WC()->query->remove_ordering_args();

		remove_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ), 10, 2 );
	}

	/**
	 * Removes unnecessary columns from the table query if we're not displayed description or short-description.
	 */
	public function filter_wp_posts_selected_columns( $fields, $query ) {
		global $wpdb;

		if ( "{$wpdb->posts}.*" !== $fields ) {
			return $fields;
		}

		if ( array_diff( array( 'description', 'short-description' ), $this->args->columns ) ) {
			$posts_columns = array( 'ID', 'post_author', 'post_date', 'post_date_gmt', 'post_title',
				'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged',
				'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order',
				'post_type', 'post_mime_type', 'comment_count' );

			if ( in_array( 'description', $this->args->columns ) ) {
				$posts_columns[] = 'post_content';
			}
			if ( in_array( 'short-description', $this->args->columns ) ) {
				$posts_columns[] = 'post_excerpt';
				// We need the content as well, in case we need to auto-generate the excerpt from the content
				$posts_columns[] = 'post_content';
			}

			$fields = sprintf( implode( ', ', array_map( array( __CLASS__, 'array_map_prefix_column' ), $posts_columns ) ), $wpdb->posts );
		}

		return $fields;
	}

	public function search_by_sku_posts_search( $search, $query ) {
		global $wpdb;

		// Build SKU where clause.
		$sku_like	 = '%' . $wpdb->esc_like( $this->args->search_term ) . '%';
		$sku_like	 = $wpdb->prepare( '%s', $sku_like );
		$sku_where	 = "( wpt1.meta_key = '_sku' AND wpt1.meta_value LIKE $sku_like )";

		// Perform a match on the search SQL so we can inject our SKU meta query into it.
		$matches = array();

		if ( preg_match( "/^ AND \((.+)\) ( AND \({$wpdb->posts}.post_password = ''\) )?$/U", $search, $matches ) ) {
			$search = ' AND (' . $sku_where . ' OR (' . $matches[1] . ')) ';

			// Add the post_password = '' clause if found.
			if ( isset( $matches[2] ) ) {
				$search .= $matches[2];
			}
		}

		return $search;
	}

	public function search_by_sku_posts_clauses( $clauses, $query ) {
		global $wpdb;

		// Add the meta query groupby clause.
		if ( empty( $clauses['groupby'] ) ) {
			$clauses['groupby'] = "{$wpdb->posts}.ID";
		}

		// Add our meta query join. We always need to do a separate join as other post meta joins may be present.
		$clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS wpt1 ON ( {$wpdb->posts}.ID = wpt1.post_id )";

		return $clauses;
	}

	public function price_filter_post_clauses( $args, $wp_query ) {
		global $wpdb;

		// Requires lookup table added in 3.6.
		if ( version_compare( get_option( 'woocommerce_db_version', null ), '3.6', '<' ) ) {
			return $args;
		}

		if ( ! isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) ) {
			return $args;
		}

		$current_min_price	 = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
		$current_max_price	 = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : PHP_INT_MAX; // WPCS: input var ok, CSRF ok.

		/**
		 * Adjust if the store taxes are not displayed how they are stored.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class	 = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates	 = WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$current_min_price	 -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
				$current_max_price	 -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
			}
		}

		$args['join']	 = $this->append_product_sorting_table_join( $args['join'] );
		$args['where']	 .= $wpdb->prepare(
			' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
			$current_min_price,
			$current_max_price
		);
		return $args;
	}

	/**
	 * Join wc_product_meta_lookup to posts if not already joined.
	 *
	 * @param string $sql SQL join.
	 * @return string
	 */
	private function append_product_sorting_table_join( $sql ) {
		global $wpdb;

		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}

	private function sku_query_hooks_required() {
		return $this->args->lazy_load && $this->args->search_term;
	}

	private function check_within_product_limit( $count ) {
		return is_int( $this->args->product_limit ) && $this->args->product_limit > 0 ? min( $this->args->product_limit, $count ) : $count;
	}

	private static function array_map_prefix_column( $n ) {
		return '%1$s.' . $n;
	}

}
