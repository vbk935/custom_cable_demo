<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once plugin_dir_path( __FILE__ ) . 'class-wc-product-table-widget.php';

if ( class_exists( 'WC_Product_Table_Widget' ) ) {

	/**
	 * Product Table implementation of WooCommerce Rating Filter Widget.
	 *
	 * Based on version 2.6.0 of the WC_Widget_Rating_Filter class.
	 *
	 * @package   WooCommerce_Product_Table\Widgets
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 */
	class WC_Product_Table_Widget_Rating_Filter extends WC_Product_Table_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass		 = 'woocommerce widget_rating_filter';
			$this->widget_description	 = __( 'Display a list of star ratings to filter products in your product table.', 'woocommerce-product-table' );
			$this->widget_id			 = 'woocommerce_pt_rating_filter';
			$this->widget_name			 = __( 'Product Table: Filter by Rating', 'woocommerce-product-table' );
			$this->settings				 = array(
				'title' => array(
					'type' => 'text',
					'std' => __( 'Average rating', 'woocommerce-product-table' ),
					'label' => __( 'Title', 'woocommerce-product-table' ),
				),
			);

			add_filter( 'woocommerce_rating_filter_link', array( 'WC_Product_Table_Widget', 'unescape_commas' ) );

			parent::__construct();
		}

		/**
		 * Count products after other filters have occurred by adjusting the main query.
		 * @param  int $rating
		 * @return int
		 */
		protected function get_filtered_product_count( $rating ) {
			global $wpdb;

			$tax_query	 = parent::get_main_tax_query();
			$meta_query	 = parent::get_main_meta_query();

			// Unset current rating filter.
			foreach ( $tax_query as $key => $query ) {
				if ( ! empty( $query['rating_filter'] ) ) {
					unset( $tax_query[$key] );
					break;
				}
			}

			// Set new rating filter.
			if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {
				$product_visibility_terms	 = wc_get_product_visibility_term_ids();
				$tax_query[]				 = array(
					'taxonomy' => 'product_visibility',
					'field' => 'term_taxonomy_id',
					'terms' => $product_visibility_terms['rated-' . $rating],
					'operator' => 'IN',
					'rating_filter' => true,
				);
			} else {
				// WC < 3.0
				$meta_query[] = array(
					'key' => '_wc_average_rating',
					'value' => $rating,
					'compare' => ' >= ',
					'type' => 'DECIMAL',
					'rating_filter' => true
				);
			}

			$meta_query		 = new WP_Meta_Query( $meta_query );
			$tax_query		 = new WP_Tax_Query( $tax_query );
			$meta_query_sql	 = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql	 = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
			$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
			$sql .= " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' ";
			$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

			return absint( $wpdb->get_var( $sql ) );
		}

		/**
		 * widget function.
		 *
		 * @see WP_Widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			if ( ! parent::is_table_on_page() || ! is_singular() ) {
				return;
			}

			ob_start();

			$found			 = false;
			$rating_filter	 = filter_input( INPUT_GET, 'rating_filter', FILTER_SANITIZE_STRING );
			$rating_filter	 = $rating_filter ? array_filter( array_map( 'absint', explode( ',', $rating_filter ) ) ) : array();

			$this->widget_start( $args, $instance );

			echo '<ul>';

			for ( $rating = 5; $rating >= 1; $rating -- ) {
				$count = $this->get_filtered_product_count( $rating );
				if ( empty( $count ) ) {
					continue;
				}
				$found = true;

				if ( in_array( $rating, $rating_filter ) ) {
					$link_ratings = implode( ',', array_diff( $rating_filter, array( $rating ) ) );
				} else {
					$link_ratings = implode( ',', array_merge( $rating_filter, array( $rating ) ) );
				}

				$class		 = in_array( $rating, $rating_filter ) ? 'wc-layered-nav-rating chosen' : 'wc-layered-nav-rating';
				$link		 = apply_filters( 'woocommerce_rating_filter_link', $link_ratings ? add_query_arg( 'rating_filter', $link_ratings ) : remove_query_arg( 'rating_filter' ) );
				$rating_html = wc_get_star_rating_html( $rating );
				$count_html	 = esc_html( apply_filters( 'woocommerce_rating_filter_count', "({$count})", $count, $rating ) );

				printf( '<li class="%s"><a href="%s"><span class="star-rating">%s</span>%s</a></li>', esc_attr( $class ), esc_url( $link ), $rating_html, $count_html );
			}

			echo '</ul>';

			$this->widget_end( $args );

			if ( ! $found ) {
				ob_end_clean();
			} else {
				echo ob_get_clean();
			}
		}

	}

} // if WC_Product_Table_Widget