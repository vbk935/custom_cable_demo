<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>
<?php
if ( woocommerce_product_loop() ) {

	if ( function_exists( 'wc_the_product_table' ) ) {
		/**
		 * The product table shortcode is added below this comment. You can add extra options to the
		 * shortcode, as you would in a normal page or post.
		 *
		 * Important: Make sure you use double quotes around your option names, e.g. filters="true"
		 *
		 * Some examples:
		 *
		 * [product_table columns="image,name,price,add-to-cart" filters="true" variations="dropdown"]
		 * [product_table columns="name,categories,price,add-to-cart" cart_button="checkbox"]
		 * [product_table columns="sku,name,reviews,price,add-to-cart" sort_by="price"]
		 */
		$shortcode = '[product_table]';

		/**
		 * Don't modify anything below here!
		 */
		$args	 = shortcode_parse_atts( str_replace( array( '[product_table', ']' ), '', $shortcode ) );
		$args	 = ! empty( $args ) && is_array( $args ) ? $args : array();

		if ( is_product_category() ) {
			// Product category archive
			$args['category'] = get_queried_object_id();
		} elseif ( is_product_tag() ) {
			// Product tag archive
			$args['tag'] = get_queried_object_id();
		} elseif ( is_product_taxonomy() ) {
			// Other product taxonomy archive
			$term			 = get_queried_object();
			$args['term']	 = "{$term->taxonomy}:{$term->term_id}";
		} elseif ( is_post_type_archive( 'product' ) && ( $search_term = get_query_var( 's' ) ) ) {
			// Product search results page
			$args['search_term'] = $search_term;
		}

		// Display the product table
		wc_the_product_table( $args );
	} else {
		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked wc_print_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );

		// Product Table plugin not active, so use the normal WooCommerce loop.
		woocommerce_product_loop_start();

		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 *
				 * @hooked WC_Structured_Data::generate_product_data() - 10
				 */
				do_action( 'woocommerce_shop_loop' );

				wc_get_template_part( 'content', 'product' );
			}
		}

		woocommerce_product_loop_end();

		/**
		 * Hook: woocommerce_after_shop_loop.
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
	}
} else {
	// No product loop available

	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
