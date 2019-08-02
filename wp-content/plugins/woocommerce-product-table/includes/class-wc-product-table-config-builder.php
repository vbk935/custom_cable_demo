<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for creating the product table config script.
 *
 * @package   WooCommerce_Product_Table
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_Product_Table_Config_Builder {

	/**
	 * @var string The table ID.
	 */
	public $id;
	/**
	 * @var WC_Product_Table_Args The table args.
	 */
	public $args;
	/**
	 * @var WC_Product_Table_Columns The table columns.
	 */
	public $columns;

	public function __construct( $id, WC_Product_Table_Args $args, WC_Product_Table_Columns $columns ) {
		$this->id		 = $id;
		$this->args		 = $args;
		$this->columns	 = $columns;
	}

	/**
	 * Build config for the table, to add as inline script to current page.
	 *
	 * @return array The table config
	 */
	public function get_config() {
		$config = array(
			'pageLength'		 => $this->args->rows_per_page,
			'pagingType'		 => $this->args->paging_type,
			'serverSide'		 => $this->args->lazy_load,
			'autoWidth'			 => $this->args->auto_width,
			'clickFilter'		 => $this->args->search_on_click,
			'scrollOffset'		 => $this->args->scroll_offset,
			'resetButton'		 => $this->args->reset_button,
			'multiAddToCart'	 => $this->args->is_multi_add_to_cart(),
			'multiCartLocation'	 => $this->args->add_selected_button,
			'variations'		 => $this->args->variations,
			'ajaxCart'			 => $this->args->ajax_cart
		);

		$config['lengthMenu'] = array( 10, 25, 50, 100 );

		if ( $this->args->rows_per_page > 0 && ! in_array( $this->args->rows_per_page, $config['lengthMenu'] ) ) {
			// Remove any default page lengths that are too close to 'rows_per_page'
			$config['lengthMenu'] = array_filter( $config['lengthMenu'], array( $this, 'array_filter_length_menu' ) );

			// Add 'rows_per_page' to length menu and sort
			array_push( $config['lengthMenu'], $this->args->rows_per_page );
			sort( $config['lengthMenu'] );
		}

		// Add show all to menu
		if ( ! $this->args->lazy_load || -1 === $this->args->rows_per_page ) {
			$config['lengthMenu']		 = array( $config['lengthMenu'], $config['lengthMenu'] );
			$config['lengthMenu'][0][]	 = -1;
			$config['lengthMenu'][1][]	 = _x( 'All', 'show all products option', 'woocommerce-product-table' );
		}

		// Set responsive control column
		$responsive_details = array();

		if ( 'column' === $this->args->responsive_control ) {
			$responsive_details		 = array( 'type' => 'column' );
			$config['columnDefs'][]	 = array( 'className' => 'control', 'orderable' => false, 'targets' => 0 );
		}

		foreach ( $this->args->columns as $column ) {
			$cell_class = WC_Product_Table_Columns::get_column_class( $column );

			if ( 'date' === $column ) {
				// If date column used and date format contains no spaces, make sure we 'nowrap' this column
				$date_format = $this->args->date_format ? $this->args->date_format : get_option( 'date_format' );

				if ( false === strpos( $date_format, ' ' ) ) {
					$cell_class .= ' nowrap';
				}
			}
			$config['columnDefs'][] = array( 'className' => $cell_class, 'targets' => $this->columns->column_index( $column ) );
		}

		// Always make SKU numeric - DataTables will use alphanumeric sorting if not numberic.
		if ( in_array( 'sku', $this->args->columns ) ) {
			$sku_type				 = apply_filters( 'wc_product_table_use_numeric_skus', false ) ? 'html-num' : 'html';
			$config['columnDefs'][]	 = array( 'type' => $sku_type, 'targets' => $this->columns->column_index( 'sku' ) );
		}

		// Set responsive display function
		if ( $this->args->responsive_display !== 'child_row' ) {
			$responsive_details = array_merge( $responsive_details, array( 'display' => $this->args->responsive_display ) );
		}
		if ( $responsive_details ) {
			$config['responsive'] = array( 'details' => $responsive_details );
		}

		// Set custom messages
		if ( $this->args->no_products_message ) {
			$config['language']['emptyTable'] = $this->args->no_products_message;
		}
		if ( $this->args->no_products_filtered_message ) {
			$config['language']['zeroRecords'] = $this->args->no_products_filtered_message;
		}

		// Set initial search term
		$config['search']['search'] = $this->args->search_term;

		// DOM option
		$dom_top		 = '';
		$dom_bottom		 = '';
		$display_options = array(
			'l'	 => 'page_length',
			'f'	 => 'search_box',
			'i'	 => 'totals',
			'p'	 => 'pagination'
		);

		foreach ( $display_options as $letter => $option ) {
			if ( 'top' === $this->args->$option || 'both' === $this->args->$option ) {
				$dom_top .= $letter;
			}
			if ( 'bottom' === $this->args->$option || 'both' === $this->args->$option ) {
				$dom_bottom .= $letter;
			}
		}

		$dom_top		 = '<"wc-product-table-above wc-product-table-controls"' . $dom_top . '>';
		$dom_bottom		 = $dom_bottom || $this->args->is_multi_add_to_cart() ? '<"wc-product-table-below wc-product-table-controls"' . $dom_bottom . '>' : '';
		$config['dom']	 = sprintf( '<"%s"%st%s>', esc_attr( WCPT_Util::get_wrapper_class() ), $dom_top, $dom_bottom );

		/* @deprecated 2.1.4 - Replaced by wc_product_table_data_config */
		$config = apply_filters( 'wc_product_table_inline_config', $config, $this->id, $this->args );

		$config = apply_filters( 'wc_product_table_data_config', $config, $this->args, $this->columns );

		return $config ? $config : false;
	}

	public function get_filters() {
		if ( ! $this->args->filters ) {
			return false;
		}

		$chosen_attributes	 = wcpt_get_layered_nav_chosen_attributes();
		$filters			 = array();

		foreach ( $this->args->filters as $filter ) {
			if ( ! ( $tax = WC_Product_Table_Columns::get_column_taxonomy( $filter ) ) ) {
				continue;
			}

			if ( ! ( $terms = $this->get_terms_for_filter( $tax ) ) ) {
				continue;
			}

			// Set the heading (used as the default filter option).
			$heading = $this->columns->get_column_heading( array_search( $filter, $this->columns->get_columns() ), $filter );
			$heading = apply_filters( 'wc_product_table_filter_heading_' . WC_Product_Table_Columns::unprefix_column( $filter ), $heading, $this->id, $this->args );

			$filters[$tax] = array(
				'heading'		 => $heading,
				'terms'			 => $terms,
				'class'			 => sanitize_html_class( apply_filters( 'wc_product_table_search_filter_class', '', WC_Product_Table_Columns::unprefix_column( $filter ) ) ),
				'column'		 => WC_Product_Table_Columns::get_column_name( $filter ),
				'search-column'	 => WC_Product_Table_Columns::get_column_name( 'hf:' . $filter )
			);

			// Set the selected option if a filter widget is currently active.
			if ( ! empty( $chosen_attributes[$tax]['terms'] ) ) {
				// Get the first selected term as we only allow a single selection in the filters
				$filters[$tax]['selected'] = reset( $chosen_attributes[$tax]['terms'] );
			}
		}

		$filters = apply_filters( 'wc_product_table_data_filters', $filters, $this->args );
		return $filters ? $filters : false;
	}

	private function get_terms_for_filter( $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}

		$terms		 = false;
		$term_args	 = array(
			'taxonomy'		 => $taxonomy,
			'fields'		 => 'all',
			'hide_empty'	 => true,
			'hierarchical'	 => true,
			'orderby'		 => 'name',
			'order'			 => 'ASC'
		);

		if ( 'pa_' === substr( $taxonomy, 0, 3 ) ) {
			// Attribute filter
			$orderby = wc_attribute_orderby( $taxonomy );

			switch ( $orderby ) {
				case 'name' :
					$term_args['orderby']	 = 'name';
					$term_args['menu_order'] = false;
					break;
				case 'id' :
					$term_args['orderby']	 = 'id';
					$term_args['order']		 = 'ASC';
					$term_args['menu_order'] = false;
					break;
				case 'menu_order' :
					$term_args['menu_order'] = 'ASC';
					break;
			}

			$terms = WCPT_Util::get_terms( apply_filters( 'wc_product_table_search_filter_get_terms_args', $term_args, $taxonomy, $this->args ) );

			if ( $terms ) {
				switch ( $orderby ) {
					case 'name_num' :
						usort( $terms, '_wc_get_product_terms_name_num_usort_callback' );
						break;
					case 'parent' :
						usort( $terms, '_wc_get_product_terms_parent_usort_callback' );
						break;
				}
			}
		} elseif ( 'product_cat' === $taxonomy ) {
			// Product category filter
			if ( $exclude = WCPT_Util::convert_to_term_ids( $this->args->exclude_category, 'product_cat' ) ) {
				// If we're excluding a category from table, remove this and all descendant terms from the category search filter
				$term_args['exclude_tree'] = $exclude;
			}
			if ( $this->args->category && $category_ids = WCPT_Util::convert_to_term_ids( $this->args->category, 'product_cat' ) ) {
				// If we're including a specific category (or categories), find all descendents and include them in term query
				$include_ids = WCPT_Util::get_all_term_children( $category_ids, 'product_cat', true );

				// Remove any excludes as exclude_tree is ingnored when we set include
				if ( $exclude ) {
					$include_ids = array_diff( $include_ids, $exclude );
				}
				$term_args['include'] = $include_ids;
			}
		} elseif ( $this->args->term && 'product_tag' !== $taxonomy ) {
			// Filter is for a custom taxonomy - we may need to restrict terms if 'term' option set
			$custom_terms		 = explode( ',', str_replace( '+', ',', $this->args->term ) );
			$current_taxonomy	 = false;
			$terms_in_tax		 = array();

			foreach ( $custom_terms as $tax_term ) {
				// Split term around the colon and check valid
				$term_split = explode( ':', $tax_term, 2 );

				if ( 2 === count( $term_split ) ) {
					if ( $taxonomy !== $term_split[0] ) {
						continue;
					}
					$current_taxonomy	 = $term_split[0];
					$terms_in_tax[]		 = $term_split[1];
				} elseif ( 1 === count( $term_split ) && $taxonomy === $current_taxonomy ) {
					$terms_in_tax[] = $term_split[0];
				}
			}
			if ( $term_ids = WCPT_Util::convert_to_term_ids( $terms_in_tax, $taxonomy ) ) {
				$term_args['include'] = WCPT_Util::get_all_term_children( $term_ids, $taxonomy, true );
			}
		}

		if ( false === $terms ) {
			$terms = WCPT_Util::get_terms( apply_filters( 'wc_product_table_search_filter_get_terms_args', $term_args, $taxonomy, $this->args ) );
		}

		if ( empty( $terms ) ) {
			return $terms;
		}

		// Filter the terms.
		$terms	 = apply_filters( 'wc_product_table_search_filter_terms', $terms, $taxonomy, $this->args );
		$terms	 = apply_filters( 'wc_product_table_search_filter_terms_' . $taxonomy, $terms, $this->args );

		// Re-key array and convert WP_Term objects to arrays.
		$result = array_map( 'get_object_vars', array_values( $terms ) );

		// Build term hierarchy so we can create the nested filter items.
		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$result = $this->build_term_tree( $result );
		}

		// Just return term name, slug and child terms for the filter.
		$result = WCPT_Util::list_pluck_array( $result, array( 'name', 'slug', 'children' ) );

		//@deprecated 2.2 - replaced by wc_product_table_search_filter_terms_<taxonomy>.
		$result = apply_filters( 'wc_product_table_filter_terms_' . $taxonomy, $result, $this->id, $this->args );

		return $result;
	}

	private function build_term_tree( array &$terms, $parent_id = 0 ) {
		$branch = array();

		foreach ( $terms as $i => $term ) {
			if ( isset( $term['parent'] ) && $parent_id == $term['parent'] ) {
				$children = $this->build_term_tree( $terms, $term['term_id'] );

				if ( $children ) {
					$term['children'] = $children;
				}
				$branch[] = $term;
				unset( $terms[$i] );
			}
		}

		// If we're at the top level branch (parent = 0) and there are terms remaining, we need to
		// loop through each and build the tree for that term.
		if ( 0 === $parent_id && $terms ) {
			$remaining_term_ids = wp_list_pluck( $terms, 'term_id' );

			foreach ( $terms as $term ) {
				if ( ! isset( $term['parent'] ) ) {
					continue;
				}
				// Only build tree if term won't be 'picked up' by its parent term.
				if ( ! in_array( $term['parent'], $remaining_term_ids ) ) {
					$branch = array_merge( $branch, $this->build_term_tree( $terms, $term['parent'] ) );
				}
			}
		}

		return $branch;
	}

	private function array_filter_length_menu( $length ) {
		$diff = abs( $length - $this->args->rows_per_page );
		return $diff / $length > 0.2 || $diff > 4;
	}

}