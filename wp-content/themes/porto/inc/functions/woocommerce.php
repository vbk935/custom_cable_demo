<?php
// get woocommerce version number
function porto_get_woo_version_number() {
	// If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	// Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file   = 'woocommerce.php';
	// If the plugin version number is set, return it
	if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
		return $plugin_folder[ $plugin_file ]['Version'];
	} else {
		// Otherwise return null
		return null;
	}
}
// remove actions
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// add actions
add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
add_action( 'woocommerce_after_shop_loop', 'porto_woocommerce_open_after_clearfix_div', 1 );
add_action( 'woocommerce_after_shop_loop', 'porto_woocommerce_close_after_clearfix_div', 999 );
add_action( 'woocommerce_before_shop_loop', 'porto_grid_list_toggle', 40 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
add_action( 'woocommerce_before_shop_loop_item_title', 'porto_loop_product_thumbnail', 10 );
add_action( 'woocommerce_archive_description', 'porto_woocommerce_category_image', 2 );
add_action( 'woocommerce_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_title_open', 1 );
add_action( 'woocommerce_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_title_close', 100 );
//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
add_action( 'porto_woocommerce_before_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_init_layout', 1 );
add_action( 'porto_woocommerce_before_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_category' );
add_action( 'woocommerce_shop_loop_item_title', 'porto_woocommerce_shop_loop_item_title' );
add_action( 'porto_before_content', 'porto_wc_print_notices', 10 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 26 );
add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
add_action( 'woocommerce_before_shop_loop_item_title', 'porto_woocommerce_sale_product_period', 20 );

add_action( 'woocommerce_checkout_before_terms_and_conditions', 'porto_woocommerce_add_js_composer_shortcodes', 2 );
function porto_woocommerce_add_js_composer_shortcodes() {
	// Fix for rendering Visual Composer shortcodes.
	if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
		WPBMap::addAllMappedShortcodes();
	}
}

add_filter( 'woocommerce_available_variation', 'porto_woocommerce_get_sale_end_date', 100, 3 );

add_action( 'porto_after_content_bottom', 'porto_woocommerce_output_related_products', 10 );
function porto_woocommerce_output_related_products() {
	if ( is_singular( 'product' ) ) {
		woocommerce_output_related_products();
	}
}

// add filters
add_filter( 'woocommerce_show_page_title', 'porto_woocommerce_show_page_title' );
add_filter( 'woocommerce_layered_nav_link', 'porto_layered_nav_link' );
add_filter( 'loop_shop_per_page', 'porto_loop_shop_per_page' );
add_filter( 'woocommerce_available_variation', 'porto_woocommerce_available_variation', 10, 3 );
add_filter( 'woocommerce_related_products_args', 'porto_remove_related_products', 10 );
add_filter( 'woocommerce_add_to_cart_fragments', 'porto_woocommerce_header_add_to_cart_fragment' );
//add_filter( 'woocommerce_show_admin_notice', 'porto_woocommerce_hide_update_notice', 10, 2 );

// message
function porto_wc_print_notices() {
	if ( is_singular( 'product' ) && function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
}

// rating
function porto_woocommerce_shop_loop_item_init_layout() {

	global $woocommerce_loop, $porto_woocommerce_loop;
	if ( ( isset( $porto_woocommerce_loop['widget'] ) && $porto_woocommerce_loop['widget'] ) || ! isset( $woocommerce_loop['addlinks_pos'] ) ) {
		return;
	}
	if ( in_array( $woocommerce_loop['addlinks_pos'], array( 'outimage_aq_onimage', 'outimage_aq_onimage2', 'awq_onimage', 'onimage2', 'onimage3' ) ) ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
	} else {
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
	}
}

// category
function porto_woocommerce_shop_loop_item_category() {
	global $product, $woocommerce_loop, $porto_settings;
	if ( class_exists( 'YITH_WCWL' ) && $porto_settings['product-wishlist'] && ( 'outimage_aq_onimage' == $woocommerce_loop['addlinks_pos'] || 'outimage_aq_onimage2' == $woocommerce_loop['addlinks_pos'] ) ) {
		echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
	}
	if ( ! isset( $porto_settings['product-categories'] ) || $porto_settings['product-categories'] ) {
		echo '<span class="category-list">' . wc_get_product_category_list( $product->get_id(), ', ', '' ) . '</span>';
	}
}

// change action position
add_action( 'woocommerce_share', 'porto_woocommerce_share' );
function porto_woocommerce_share() {
	global $porto_settings;
	$share = porto_get_meta_value( 'product_share' );
	if ( $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && $porto_settings['product-share'] ) ) ) {
		echo '<div class="product-share">';
			echo '<label>';
				esc_html_e( 'Share', 'porto' );
			echo '</label>';
			get_template_part( 'share' );
		echo '</div>';
	}
}
// hide woocommer page title
function porto_woocommerce_show_page_title( $args ) {
	return false;
}

function porto_woocommerce_open_before_clearfix_div() {
	global $porto_shop_filter_layout;
	if ( ( ! isset( $porto_shop_filter_layout ) || 'horizontal2' != $porto_shop_filter_layout ) && ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) ) {
		return;
	}
	echo '<div class="shop-loop-before clearfix">';
}

function porto_woocommerce_open_after_clearfix_div() {
	if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) {
		return;
	}
	global $porto_settings;
	$class_suffix = '';
	if ( isset( $porto_settings['product-infinite'] ) && 'infinite_scroll' == $porto_settings['product-infinite'] ) {
		$class_suffix = ' d-none';
	} elseif ( isset( $porto_settings['product-infinite'] ) && 'load_more' == $porto_settings['product-infinite'] ) {
		$class_suffix = ' load-more-wrap';
	}
	echo '<div class="shop-loop-after clearfix' . $class_suffix . '">';
}

function porto_woocommerce_close_before_clearfix_div() {
	global $porto_shop_filter_layout;
	if ( ( ! isset( $porto_shop_filter_layout ) || 'horizontal2' != $porto_shop_filter_layout ) && ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) ) {
		return;
	}
	echo '</div>';
}

function porto_woocommerce_close_after_clearfix_div() {
	if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() ) {
		return;
	}
	echo '</div>';
}

// show grid/list toggle buttons
function porto_grid_list_toggle() {
	global $porto_woocommerce_loop;
	if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || ! woocommerce_products_will_display() || isset( $porto_woocommerce_loop['view'] ) ) {
		return;
	}
	?>
	<div class="gridlist-toggle">
		<a href="#" id="grid" title="<?php esc_attr_e( 'Grid View', 'porto' ); ?>"></a><a href="#" id="list" title="<?php esc_attr_e( 'List View', 'porto' ); ?>"></a>
	</div>
	<?php
	global $porto_settings, $porto_shop_filter_layout;
	if ( isset( $porto_shop_filter_layout ) && ( 'horizontal' === $porto_shop_filter_layout || 'horizontal2' === $porto_shop_filter_layout ) ) {
		woocommerce_result_count();
	}
	if ( isset( $porto_shop_filter_layout ) && 'horizontal2' === $porto_shop_filter_layout ) {
		//remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
	}
}
// get product count per page
function porto_loop_shop_per_page() {
	global $porto_settings;
	// replace it with theme option
	if ( $porto_settings['category-item'] ) {
		$per_page = explode( ',', $porto_settings['category-item'] );
	} else {
		$per_page = explode( ',', '12,24,36' );
	}
	$item_count = isset( $_GET['count'] ) && ! empty( $_GET['count'] ) ? $_GET['count'] : $per_page[0];
	return $item_count;
}
// add thumbnail image parameter
function porto_woocommerce_available_variation( $variations, $product, $variation ) {
	if ( has_post_thumbnail( $variation->get_id() ) ) {
		$attachment_id    = get_post_thumbnail_id( $variation->get_id() );
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
		$variations       = array_merge( $variations, array( 'image_thumb' => $image_thumb_link[0] ) );
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
		$variations       = array_merge( $variations, array( 'image_src' => $image_thumb_link[0] ) );
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'full' );
		$variations       = array_merge( $variations, array( 'image_link' => $image_thumb_link[0] ) );
	} elseif ( has_post_thumbnail() ) {
		$attachment_id    = get_post_thumbnail_id();
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
		$variations       = array_merge( $variations, array( 'image_thumb' => $image_thumb_link[0] ) );
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
		$variations       = array_merge( $variations, array( 'image_src' => $image_thumb_link[0] ) );
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'full' );
		$variations       = array_merge( $variations, array( 'image_link' => $image_thumb_link[0] ) );
	}
	return $variations;
}
// add sort order parameter
function porto_layered_nav_link( $link ) {
	if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) {
		$link = esc_url( str_replace( '#038;', '&', add_query_arg( 'orderby', $_GET['orderby'], $link ) ) );
	}
	if ( isset( $_GET['count'] ) && ! empty( $_GET['count'] ) ) {
		$link = esc_url( str_replace( '#038;', '&', add_query_arg( 'count', $_GET['count'], $link ) ) );
	}
	return $link;
}
// change product thumbnail in products list page
function porto_loop_product_thumbnail() {
	global $porto_settings, $porto_woocommerce_loop, $porto_settings_optimize;
	$id = get_the_ID();
	if ( isset( $porto_woocommerce_loop['image_size'] ) && $porto_woocommerce_loop['image_size'] ) {
		$size = $porto_woocommerce_loop['image_size'];
	} else {
		$size = 'shop_catalog';
	}
	$gallery          = get_post_meta( $id, '_product_image_gallery', true );
	$attachment_image = '';
	if ( ! empty( $gallery ) && $porto_settings['category-image-hover'] ) {
		$gallery = explode( ',', $gallery );

		$show_hover_img = get_post_meta( $id, 'product_image_on_hover', true );
		$show_hover_img = empty( $show_hover_img ) || ( 'yes' === $show_hover_img );
		if ( $show_hover_img ) {
			$first_image_id = $gallery[0];

			if ( isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] ) {
				$attachment_image = wp_get_attachment_image_src( $first_image_id, $size );
				if ( $attachment_image && is_array( $attachment_image ) && count( $attachment_image ) >= 3 ) {
					$thumb_classes = 'hover-image';
					$thumb_attr    = '';
					$placeholder   = porto_generate_placeholder( $attachment_image[1] . 'x' . $attachment_image[2] );
					if ( 'products-slider' == $porto_woocommerce_loop['view'] ) {
						$thumb_classes .= ' owl-lazy';
						$thumb_attr     = ' data-src="' . esc_url( $attachment_image[0] ) . '" src="' . esc_url( $placeholder[0] ) . '"';
					} else {
						$thumb_classes .= ' porto-lazyload';
						$thumb_attr     = ' data-original="' . esc_url( $attachment_image[0] ) . '" src="' . esc_url( $placeholder[0] ) . '"';
					}
					$alt_text         = get_post_meta( $first_image_id, '_wp_attachment_image_alt', true );
					$attachment_image = '<img ' . $thumb_attr . ' width="' . esc_attr( $attachment_image[1] ) . '" height="' . esc_attr( $attachment_image[2] ) . '" class="' . esc_attr( $thumb_classes ) . '" alt="' . esc_attr( $alt_text ) . '" />';
				}
			} else {
				$attachment_image = wp_get_attachment_image( $first_image_id, $size, false, array( 'class' => 'hover-image' ) );
			}
		}
	}

	$thumb_image = '';
	if ( isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] ) {
		$thumb_image = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
		if ( $thumb_image && is_array( $thumb_image ) && count( $thumb_image ) >= 3 ) {
			$thumb_classes = '';
			$thumb_attr    = '';
			$placeholder   = porto_generate_placeholder( $thumb_image[1] . 'x' . $thumb_image[2] );
			if ( 'products-slider' == $porto_woocommerce_loop['view'] ) {
				$thumb_classes .= 'owl-lazy';
				$thumb_attr     = ' data-src="' . esc_url( $thumb_image[0] ) . '" src="' . esc_url( $placeholder[0] ) . '"';
			} else {
				$thumb_classes .= 'porto-lazyload';
				$thumb_attr     = ' data-original="' . esc_url( $thumb_image[0] ) . '" src="' . esc_url( $placeholder[0] ) . '"';
			}
			$alt_text    = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
			$thumb_image = '<img ' . $thumb_attr . ' width="' . esc_attr( $thumb_image[1] ) . '" height="' . esc_attr( $thumb_image[2] ) . '" class="' . esc_attr( $thumb_classes ) . '" alt="' . esc_attr( $alt_text ) . '" />';
		}
	} else {
		$thumb_image = get_the_post_thumbnail( $id, $size, array( 'class' => '' ) );
	}

	if ( ! $thumb_image ) {
		if ( wc_placeholder_img_src() ) {
			$thumb_image = wc_placeholder_img( $size );
		}
	}

	echo '<div class="inner' . ( ( $attachment_image ) ? ' img-effect' : '' ) . '">';
	// show images
	echo porto_filter_output( $thumb_image );
	echo porto_filter_output( $attachment_image );
	echo '</div>';
}
// change product thumbnail in products widget
function porto_widget_product_thumbnail() {
	global $porto_settings;
	$id               = get_the_ID();
	$size             = 'widget-thumb-medium';
	$gallery          = get_post_meta( $id, '_product_image_gallery', true );
	$attachment_image = '';
	if ( ! empty( $gallery ) && $porto_settings['category-image-hover'] ) {
		$gallery = explode( ',', $gallery );

		$show_hover_img = get_post_meta( $id, 'product_image_on_hover', true );
		$show_hover_img = empty( $show_hover_img ) || ( 'yes' === $show_hover_img );
		if ( $show_hover_img ) {
			$first_image_id   = $gallery[0];
			$attachment_image = wp_get_attachment_image( $first_image_id, $size, false, array( 'class' => 'hover-image ' ) );
		}
	}
	$thumb_image = get_the_post_thumbnail( $id, $size, array( 'class' => '' ) );
	if ( ! $thumb_image ) {
		if ( wc_placeholder_img_src() ) {
			$thumb_image = wc_placeholder_img( $size );
		}
	}
	echo '<div class="inner' . ( ( $attachment_image ) ? ' img-effect' : '' ) . '">';
	// show images
	echo porto_filter_output( $thumb_image );
	echo porto_filter_output( $attachment_image );
	echo '</div>';
}
// remove related products
function porto_remove_related_products( $args ) {
	global $porto_settings;
	if ( isset( $porto_settings['product-related'] ) && ! $porto_settings['product-related'] ) {
		return array();
	}
	return $args;
}
// add ajax cart fragment
function porto_woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $porto_settings;
	$minicart_type = porto_get_minicart_type();
	if ( 'minicart-inline' == $minicart_type ) {
		$_cart_total = WC()->cart->get_cart_subtotal();
		/* translators: %s: Cart quantity */
		$fragments['#mini-cart .cart-subtotal'] = '<span class="cart-subtotal">' . sprintf( esc_html__( 'Cart %s', 'porto' ), $_cart_total ) . '</span>';
	} else {
		$_cart_qty                           = WC()->cart->cart_contents_count;
		$_cart_qty                           = ( $_cart_qty > 0 ? $_cart_qty : '0' );
		$fragments['#mini-cart .cart-items'] = '<span class="cart-items">' . ( (int) $_cart_qty ) . '</span>';
		/* translators: %s: Cart items */
		$fragments['#mini-cart .cart-items-text'] = '<span class="cart-items-text">' . sprintf( _n( '%d item', '%d items', $_cart_qty, 'porto' ), $_cart_qty ) . '</span>';
	}
	return $fragments;
}
// remove update notice
function porto_woocommerce_hide_update_notice( $flag, $notice ) {
	if ( 'update' === $notice ) {
		return false;
	}
	return $flag;
}
// ajax remove cart item
add_action( 'wp_ajax_porto_cart_item_remove', 'porto_cart_item_remove' );
add_action( 'wp_ajax_nopriv_porto_cart_item_remove', 'porto_cart_item_remove' );
function porto_cart_item_remove() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );
	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	$cart         = WC()->instance()->cart;
	$cart_id      = sanitize_text_field( $_POST['cart_id'] );
	$cart_item_id = $cart->find_product_in_cart( $cart_id );
	if ( $cart_item_id ) {
		$cart->set_quantity( $cart_item_id, 0 );
	}
	$cart_ajax = new WC_AJAX();
	$cart_ajax->get_refreshed_fragments();
	// phpcs:enable
	exit();
}
// refresh cart fragment
add_action( 'wp_ajax_porto_refresh_cart_fragment', 'porto_refresh_cart_fragment' );
add_action( 'wp_ajax_nopriv_porto_refresh_cart_fragment', 'porto_refresh_cart_fragment' );
function porto_refresh_cart_fragment() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );
	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	$cart_ajax = new WC_AJAX();
	$cart_ajax->get_refreshed_fragments();
	// phpcs:enable
	exit();

}
function porto_get_products_by_ids( $product_ids ) {
	$product_ids = explode( ',', $product_ids );
	$product_ids = array_map( 'trim', $product_ids );
	$args        = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'post__in'            => $product_ids,
	);
	// @codingStandardsIgnoreStart
	$args['tax_query'] = WC()->query->get_tax_query();
	// @codingStandardsIgnoreEnd
	$query = new WP_Query( $args );
	return $query;
}
function porto_get_rating_html( $product, $rating = null ) {
	if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' ) {
		return '';
	}
	if ( ! is_numeric( $rating ) ) {
		$rating = $product->get_average_rating();
	}
	$rating_html  = '<div class="star-rating" title="' . esc_attr( $rating ) . '">';
	$rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . esc_html( $rating ) . '</strong> ' . __( 'out of 5', 'woocommerce' ) . '</span>';
	$rating_html .= '</div>';
	return $rating_html;
}
// Wrap gravatar in reviews
add_action( 'woocommerce_review_before', 'porto_woo_review_display_gravatar_wrap_start', 9 );
add_action( 'woocommerce_review_before', 'porto_woo_review_display_gravatar_wrap_end', 11 );
function porto_woo_review_display_gravatar_wrap_start() {
	echo '<div class="img-thumbnail">';
}
function porto_woo_review_display_gravatar_wrap_end() {
	echo '</div>';
}
add_filter( 'woocommerce_review_gravatar_size', 'porto_woo_review_gravatar_size' );
function porto_woo_review_gravatar_size( $size ) {
	return '80';
}
// Quick View Html
add_action( 'wp_ajax_porto_product_quickview', 'porto_product_quickview' );
add_action( 'wp_ajax_nopriv_porto_product_quickview', 'porto_product_quickview' );
function porto_product_quickview() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	global $post, $product;
	$post    = get_post( (int) $_REQUEST['pid'] );
	$product = wc_get_product( $post->ID );
	if ( post_password_required() ) {
		echo get_the_password_form();
		die();
		return;
	}
	?>
	<div class="quickview-wrap quickview-wrap-<?php echo esc_attr( $post->ID ); ?> single-product">
		<div class="product product-summary-wrap">
			<div class="row">
				<div class="col-lg-6 summary-before">
					<?php
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>
				<div class="col-lg-6 summary entry-summary">
					<?php

					do_action( 'woocommerce_single_product_summary' );
					?>
					<script>
						<?php
						$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
						$assets_path          = esc_url( str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) ) . '/assets/';
						$frontend_script_path = $assets_path . 'js/frontend/';
						?>
						var wc_add_to_cart_variation_params = 
						<?php
						echo array2json(
							apply_filters(
								'wc_add_to_cart_variation_params',
								array(
									'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
									'i18n_no_matching_variations_text' => esc_js( __( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ) ),
									'i18n_make_a_selection_text' => esc_js( __( 'Select product options before adding this product to your cart.', 'woocommerce' ) ),
									'i18n_unavailable_text' => esc_js( __( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ) ),
								)
							)
						)
						?>
						;
						jQuery(document).ready(function($) {
							$.getScript('<?php echo porto_filter_output( $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js' ); ?>');
						});
					</script>
				</div><!-- .summary -->
			</div>
		</div>
	</div>
	<?php
	// phpcs:enable
	die();
}
function porto_woocommerce_category_image() {
	if ( is_product_category() ) {
		$term = get_queried_object();
		if ( $term ) {
			$image = get_metadata( $term->taxonomy, $term->term_id, 'category_image', true );
			if ( $image ) {
				echo '<img src="' . esc_url( $image ) . '" class="category-image" alt="' . esc_attr( $term->name ) . '" />';
			}
		}
	}
}
function porto_woocommerce_shop_loop_item_title_open() {
	global $porto_settings;
	$more_link   = apply_filters( 'the_permalink', get_permalink() );
	$more_target = '';
	if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
		if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
			if ( ! $porto_settings['catalog-cart'] ) {
				if ( $porto_settings['catalog-readmore'] && 'all' === $porto_settings['catalog-readmore-archive'] ) {
					$link = get_post_meta( get_the_id(), 'product_more_link', true );
					if ( $link ) {
						$more_link = $link;
					}
					$more_target = $porto_settings['catalog-readmore-target'] ? 'target="' . esc_attr( $porto_settings['catalog-readmore-target'] ) . '"' : '';
				}
			}
		}
	}
	?>
	<a class="product-loop-title" <?php echo porto_filter_output( $more_target ); ?> href="<?php echo esc_url( $more_link ); ?>">
	<?php
}
function porto_woocommerce_shop_loop_item_title_close() {
	?>
	</a>
	<?php
}
function porto_woocommerce_shop_loop_item_title() {
	echo '<h3 class="woocommerce-loop-product__title">';
	the_title();
	echo '</h3>';
}
function porto_woocommerce_single_excerpt() {
	global $post;
	if ( ! $post->post_excerpt ) {
		return;
	}
	?>
	<div class="description">
		<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
	</div>
	<?php
}

function porto_woocommerce_next_product( $in_same_cat = false, $excluded_categories = '' ) {
	porto_adjacent_post_link_product( $in_same_cat, $excluded_categories, false );
}
function porto_woocommerce_prev_product( $in_same_cat = false, $excluded_categories = '' ) {
	porto_adjacent_post_link_product( $in_same_cat, $excluded_categories, true );
}
function porto_adjacent_post_link_product( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
	if ( $previous && is_attachment() ) {
		$post = get_post( get_post()->post_parent );
	} else {
		$post = get_adjacent_post( $in_same_cat, $excluded_categories, $previous, 'product_cat' );
	}
	if ( $previous ) {
		$label = 'prev';
	} else {
		$label = 'next';
	}
	if ( $post ) {
		$product = wc_get_product( $post->ID );
		?>
		<div class="product-<?php echo porto_filter_output( $label ); ?>">
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
				<span class="product-link"></span>
				<span class="product-popup">
					<span class="featured-box">
						<span class="box-content">
							<span class="product-image">
								<span class="inner">
									<?php
									if ( has_post_thumbnail( $post->ID ) ) {
										echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
									} else {
										echo '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder" width="' . wc_get_image_size( 'shop_thumbnail_image_width' )['width'] . '" height="' . wc_get_image_size( 'shop_thumbnail_image_height' )['height'] . '" />';
									}
									?>
								</span>
							</span>
							<span class="product-details">
								<span class="product-title"><?php echo ( get_the_title( $post ) ) ? get_the_title( $post ) : $post->ID; ?></span>
							</span>
						</span>
					</span>
				</span>
			</a>
		</div>
		<?php
	} else {
		?>
		<div class="product-<?php echo porto_filter_output( $label ); ?>">
			<span class="product-link disabled"></span>
		</div>
		<?php
	}
}

add_action( 'woocommerce_init', 'porto_woocommerce_init' );
function porto_woocommerce_init() {
	global $porto_settings;
	// Hide product short description
	if ( isset( $porto_settings['catalog-enable'] ) && ! $porto_settings['product-short-desc'] ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	}

	add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_product_nav', 5 );
	// Catalog Mode
	if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
		if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
			if ( ! $porto_settings['catalog-price'] ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
				add_filter( 'woocommerce_get_price_html', 'porto_woocommerce_get_price_html_empty', 100, 2 );
				add_filter( 'woocommerce_cart_item_price', 'porto_woocommerce_get_price_empty', 100, 3 );
				add_filter( 'woocommerce_cart_item_subtotal', 'porto_woocommerce_get_price_empty', 100, 3 );
				add_filter( 'woocommerce_cart_subtotal', 'porto_woocommerce_get_price_empty', 100, 3 );
				add_filter( 'woocommerce_get_variation_price_html', 'porto_woocommerce_get_price_html_empty', 100, 2 );
			}
			if ( ! $porto_settings['catalog-cart'] ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_template_single_add_to_cart', 30 );
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
				if ( $porto_settings['catalog-readmore'] ) {
					add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_readmore_button', 30 );
				}
			}
			if ( ! $porto_settings['catalog-review'] ) {
				add_filter( 'pre_option_woocommerce_enable_review_rating', 'porto_woocommerce_disable_rating' );
				add_filter( 'woocommerce_product_tabs', 'porto_woocommerce_remove_reviews_tab', 98 );
				function porto_woocommerce_remove_reviews_tab( $tabs ) {
					unset( $tabs['reviews'] );
					return $tabs;
				}
			}
		}
	}
	// change product tabs position
	if ( isset( $porto_settings['product-tabs-pos'] ) && 'below' == $porto_settings['product-tabs-pos'] ) {
		if ( is_customize_preview() || ! porto_is_ajax() ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 25 );
		}
	}

	// add to cart sticky
	if ( isset( $porto_settings['product-sticky-addcart'] ) && $porto_settings['product-sticky-addcart'] ) {
		add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_product_sticky_addcart', 5 );
	}

	// define woocommerce functions if use previous version
	if ( ! function_exists( 'wc_product_class' ) ) :
		function wc_product_class( $arg = array() ) {
			return post_class( $arg );
		}
	endif;
	if ( ! function_exists( 'wc_get_product_class' ) ) :
		function wc_get_product_class( $arg = array() ) {
			return get_post_class( $arg );
		}
	endif;
}

function porto_woocommerce_open_product_images() {
	echo '<div class="product-summary-images summary-before container">';
}
function porto_woocommerce_close_product_images() {
	echo '</div>';
}
function porto_woocommerce_template_single_add_to_cart() {
	global $product;
	if ( 'variable' == $product->product_type ) {
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		do_action( 'woocommerce_' . $product->product_type . '_add_to_cart' );
	}
}
function porto_woocommerce_get_price_html_empty( $price, $product ) {
	return '';
}
function porto_woocommerce_get_price_empty( $price, $param2, $param3 ) {
	return '';
}
function porto_woocommerce_disable_rating( $false ) {
	return 'no';
}
function porto_woocommerce_readmore_button() {
	global $porto_settings;
	$more_link   = get_post_meta( get_the_id(), 'product_more_link', true );
	$more_target = $porto_settings['catalog-readmore-target'] ? 'target="' . esc_attr( $porto_settings['catalog-readmore-target'] ) . '"' : '';
	if ( ! $more_link ) {
		$more_link = apply_filters( 'the_permalink', get_permalink() );
	}
	?>
		<div class="cart">
			<a <?php echo porto_filter_output( $more_target ); ?> href="<?php echo esc_url( $more_link ); ?>" class="single_add_to_cart_button button readmore"><?php echo wp_kses_post( $porto_settings['catalog-readmore-label'] ); ?></a>
		</div>
	<?php
}
// ajax products archive display
add_filter( 'pre_option_woocommerce_shop_page_display', 'porto_shop_page_display_ajax' );
function porto_shop_page_display_ajax( $value ) {
	$params = array( 'count', 'orderby', 'min_price', 'max_price' );
	foreach ( $params as $param ) {
		if ( ! empty( $_GET[ $param ] ) ) {
			return '';
		}
	}
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $tax ) {
			$attribute = wc_sanitize_taxonomy_name( $tax->attribute_name );
			$taxonomy  = wc_attribute_taxonomy_name( $attribute );
			$name      = 'filter_' . $attribute;
			if ( ! empty( $_GET[ $name ] ) && taxonomy_exists( $taxonomy ) ) {
				return '';
			}
		}
	}
	$page_num = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 0;
	if ( $page_num ) {
		return '';
	}
	return $value;
}
add_filter( 'get_woocommerce_term_metadata', 'porto_woocommerce_term_metadata_ajax', 10, 4 );
function porto_woocommerce_term_metadata_ajax( $value, $object_id, $meta_key, $single ) {
	if ( 'display_type' === $meta_key ) {
		$params = array( 'count', 'orderby', 'min_price', 'max_price' );
		foreach ( $params as $param ) {
			if ( ! empty( $_GET[ $param ] ) ) {
				return 'products';
			}
		}
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$attribute = wc_sanitize_taxonomy_name( $tax->attribute_name );
				$taxonomy  = wc_attribute_taxonomy_name( $attribute );
				$name      = 'filter_' . $attribute;
				if ( ! empty( $_GET[ $name ] ) && taxonomy_exists( $taxonomy ) ) {
					return 'products';
				}
			}
		}
		$page_num = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 0;
		if ( $page_num ) {
			return 'products';
		}
	}
	return $value;
}
function porto_is_product_archive() {
	if ( is_archive() ) {
		$term = get_queried_object();
		if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
			switch ( $term->taxonomy ) {
				case in_array( $term->taxonomy, porto_get_taxonomies( 'product' ) ):
				case 'product_cat':
					return true;
					break;
				default:
					return false;
			}
		}
	}
	return false;
}


add_filter( 'woocommerce_get_stock_html', 'porto_woocommerce_stock_html', 10, 2 );
function porto_woocommerce_stock_html( $availability_html, $product ) {
	if ( $product->is_type( 'simple' ) ) {
		return '';
	}
	return $availability_html;
}

add_action( 'woocommerce_product_meta_start', 'porto_woocommerce_add_stock_html', 10 );
function porto_woocommerce_add_stock_html() {
	global $product;
	if ( $product->is_type( 'simple' ) ) {
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<span class="product-stock ' . esc_attr( $availability['class'] ) . '">' . esc_html__( 'Availability', 'porto' ) . ': <span class="stock">' . esc_html( $availability['availability'] ) . '</span></span>';

		echo apply_filters( 'porto_woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}
}

add_action( 'wp', 'porto_woocommerce_init_layout' );
function porto_woocommerce_init_layout() {
	// woocommerce single product layout
	global $porto_settings, $porto_product_layout, $porto_shop_filter_layout;
	if ( class_exists( 'Woocommerce' ) && ( is_singular( 'product' ) || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) ) {
		$porto_product_layout = get_post_meta( get_the_ID(), 'product_layout', true );
		$porto_product_layout = ( ! $porto_product_layout && isset( $porto_settings['product-single-content-layout'] ) ) ? $porto_settings['product-single-content-layout'] : $porto_product_layout;
		if ( ! $porto_product_layout ) {
			$porto_product_layout = 'default';
		}
	}
	if ( class_exists( 'Woocommerce' ) && ( is_product_taxonomy() || is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) ) {
		$porto_shop_filter_layout = false;
		$term                     = get_queried_object();
		if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
			$porto_shop_filter_layout = get_metadata( $term->taxonomy, $term->term_id, 'filter_layout', true );
		}
		if ( ! $porto_shop_filter_layout ) {
			$porto_shop_filter_layout = $porto_settings['product-archive-filter-layout'];
		}
	}

	// show/hide review and price
	if ( isset( $porto_settings['product-review'] ) && ! $porto_settings['product-review'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}
	if ( isset( $porto_settings['product-price'] ) && ! $porto_settings['product-price'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}

	// product layout
	if ( $porto_product_layout ) {
		if ( 'extended' === $porto_product_layout ) {
			if ( is_singular( 'product' ) ) {
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
				add_action( 'porto_after_content_top', 'woocommerce_show_product_sale_flash', 40 );
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				add_action( 'porto_after_content_top', 'porto_woocommerce_open_product_images', 39 );
				add_action( 'porto_after_content_top', 'woocommerce_show_product_images', 50 );
				add_action( 'porto_after_content_top', 'porto_woocommerce_close_product_images', 51 );
			}
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 6 );
			add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_template_single_custom_block', 11 );
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
			add_action( 'woocommerce_after_variations_form', 'woocommerce_single_variation', 10 );
		} elseif ( 'grid' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
			add_action( 'woocommerce_before_add_to_cart_button', 'porto_woocommerce_sale_product_period', 15 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 26 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		} elseif ( 'full_width' === $porto_product_layout ) {
			add_action( 'woocommerce_single_product_summary', 'porto_woocommerce_template_single_custom_block', 1 );
			if ( ! isset( $porto_settings['product-tabs-pos'] ) || 'below' != $porto_settings['product-tabs-pos'] ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 50 );
			}
		} elseif ( 'sticky_info' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_sharing', 9 );
			add_action( 'woocommerce_after_single_product_summary', 'porto_woocommerce_template_single_custom_block', 8 );
		} elseif ( 'sticky_both_info' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_product_nav', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'woocommerce_template_single_rating', 10 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'woocommerce_template_single_sharing', 6 );
			add_action( 'porto_woocommerce_before_single_product_summary', 'porto_woocommerce_product_nav', 7 );
			add_action( 'porto_woocommerce_single_product_summary2', 'woocommerce_template_single_add_to_cart', 10 );
			add_action( 'woocommerce_before_add_to_cart_button', 'porto_woocommerce_sale_product_period', 15 );
		} elseif ( 'centered_vertical_zoom' === $porto_product_layout ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', 'porto_woocommerce_sale_product_period', 15 );
			add_action( 'porto_woocommerce_single_product_summary2', 'woocommerce_template_single_add_to_cart', 10 );
			add_action( 'porto_woocommerce_single_product_summary2', 'porto_woocommerce_template_single_custom_block', 5 );
			add_action( 'woocommerce_before_add_to_cart_button', 'porto_woocommerce_sale_product_period', 15 );
		} elseif ( 'left_sidebar' === $porto_product_layout ) {
			remove_action( 'porto_after_content_bottom', 'porto_woocommerce_output_related_products', 10 );
			add_action( 'woocommerce_after_single_product_summary', 'porto_woocommerce_output_related_products', 10 );
		}
	}

	// horizontal filter
	if ( $porto_shop_filter_layout && 'horizontal' == $porto_shop_filter_layout ) {
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_before_shop_loop', 'porto_grid_list_toggle', 40 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
		add_action( 'porto_before_content', 'porto_woocommerce_open_before_clearfix_div', 80 );
		add_action( 'porto_before_content', 'porto_woocommerce_output_horizontal_filter', 82 );
		add_action( 'porto_before_content', 'woocommerce_catalog_ordering', 84 );
		add_action( 'porto_before_content', 'porto_grid_list_toggle', 86 );
		add_action( 'porto_before_content', 'woocommerce_pagination', 88 );
		add_action( 'porto_before_content', 'porto_woocommerce_close_before_clearfix_div', 90 );
	} elseif ( $porto_shop_filter_layout && 'horizontal2' == $porto_shop_filter_layout ) {
		add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_output_horizontal_filter', 25 );
	}

	// archive page infinite scroll
	if ( isset( $porto_settings['product-infinite'] ) && $porto_settings['product-infinite'] ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
		if ( $porto_shop_filter_layout && 'horizontal' == $porto_shop_filter_layout ) {
			remove_action( 'porto_before_content', 'woocommerce_pagination', 88 );
		}
	}
}

function porto_woocommerce_template_single_custom_block() {
	global $porto_product_layout;
	$block_slug = get_post_meta( get_the_ID(), 'product_custom_block', true );
	if ( $block_slug ) {
		echo '<div class="single-product-custom-block">';
			echo do_shortcode( '[porto_block name="' . esc_attr( $block_slug ) . '"]' );
		echo '</div>';
	}
}

// product custom tabs
add_filter( 'woocommerce_product_tabs', 'porto_woocommerce_custom_tabs' );
add_filter( 'woocommerce_product_tabs', 'porto_woocommerce_global_tab' );
function porto_woocommerce_custom_tabs( $tabs ) {
	global $porto_settings;
	$custom_tabs_count = isset( $porto_settings['product-custom-tabs-count'] ) ? $porto_settings['product-custom-tabs-count'] : '2';
	if ( $custom_tabs_count ) {
		for ( $i = 0; $i < $custom_tabs_count; $i++ ) {
			$index               = $i + 1;
			$custom_tab_title    = get_post_meta( get_the_id(), 'custom_tab_title' . $index, true );
			$custom_tab_priority = (int) get_post_meta( get_the_id(), 'custom_tab_priority' . $index, true );
			if ( ! $custom_tab_priority ) {
				$custom_tab_priority = 40 + $i;
			}
			$custom_tab_content = get_post_meta( get_the_id(), 'custom_tab_content' . $index, true );
			if ( $custom_tab_title && $custom_tab_content ) {
				$tabs[ 'custom_tab' . $index ] = array(
					'title'    => wp_kses_post( $custom_tab_title ),
					'priority' => $custom_tab_priority,
					'callback' => 'porto_woocommerce_custom_tab_content',
					'content'  => $custom_tab_content,
				);
			}
		}
	}
	return $tabs;
}
function porto_woocommerce_global_tab( $tabs ) {
	global $porto_settings;
	$custom_tab_title    = $porto_settings['product-tab-title'];
	$custom_tab_content  = '[porto_block name="' . $porto_settings['product-tab-block'] . '"]';
	$custom_tab_priority = ( isset( $porto_settings['product-tab-priority'] ) && $porto_settings['product-tab-priority'] ) ? $porto_settings['product-tab-priority'] : 60;
	if ( $custom_tab_title && $custom_tab_content ) {
		$tabs['global_tab'] = array(
			'title'    => wp_kses_post( $custom_tab_title ),
			'priority' => $custom_tab_priority,
			'callback' => 'porto_woocommerce_custom_tab_content',
			'content'  => $custom_tab_content,
		);
	}
	return $tabs;
}
function porto_woocommerce_custom_tab_content( $key, $tab ) {
	echo do_shortcode( $tab['content'] );
}
// woocommerce multilingual compatibility
add_filter( 'wcml_multi_currency_is_ajax', 'porto_multi_currency_ajax' );
function porto_multi_currency_ajax( $actions ) {
	$actions[] = 'porto_product_quickview';
	return $actions;
}

/* Register/Login */
/**
* Add new register fields for WooCommerce registration.
*
* @return string Register fields HTML.
*/
function porto_wooc_extra_register_start_fields() {
	global $porto_settings;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) :
		?>
		<p class="form-row form-row-first">
			<label for="reg_billing_first_name"><?php esc_html_e( 'First Name', 'porto' ); ?><span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php echo ! empty( $_POST['billing_first_name'] ) ? esc_attr( $_POST['billing_first_name'] ) : ''; ?>" />
		</p>
		<p class="form-row form-row-last">
			<label for="reg_billing_last_name"><?php esc_html_e( 'Last Name', 'porto' ); ?><span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php echo ! empty( $_POST['billing_last_name'] ) ? esc_attr( $_POST['billing_last_name'] ) : ''; ?>" />
		</p>
		<div class="clear"></div>
		<?php
	endif;
}
add_action( 'woocommerce_register_form_start', 'porto_wooc_extra_register_start_fields' );
/**
* Validate the extra register fields.
*
* @param string $username             Current username.
* @param string $email                 Current email.
* @param object $validation_errors     WP_Error object.
*
* @return void
*/
function porto_wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	global $porto_settings;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) {
		if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
			$validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'porto' ) );
		}
		if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
			$validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'porto' ) );
		}
	}
}
add_action( 'woocommerce_register_post', 'porto_wooc_validate_extra_register_fields', 10, 3 );
/**
* Save the extra register fields.
*
* @paramint $customer_id Current customer ID.
*
* @return void
*/
function porto_wooc_save_extra_register_fields( $customer_id ) {
	global $porto_settings;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) {
		if ( isset( $_POST['billing_first_name'] ) ) {
			// WordPress default first name field.
			update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
			// WooCommerce billing first name.
			update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
		}
		if ( isset( $_POST['billing_last_name'] ) ) {
			// WordPress default last name field.
			update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
			// WooCommerce billing last name.
			update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
		}
	}
}
add_action( 'woocommerce_created_customer', 'porto_wooc_save_extra_register_fields' );
// Confirm password field on the register form under My Accounts.
add_filter( 'woocommerce_registration_errors', 'registration_errors_validation', 10, 3 );
function registration_errors_validation( $reg_errors, $sanitized_user_login, $user_email ) {

	global $porto_settings, $woocommerce;
	if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] && 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
		extract( $_POST );
		if ( strcmp( $password, $confirm_password ) !== 0 ) {
			return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'porto' ) );
		}
		return $reg_errors;
	}
	return $reg_errors;
}
/* End - Register/Login */
/* Cart & Checkout - Start */
function porto_cart_version() {
	global $porto_settings;
	$cart_ver = ( isset( $porto_settings['cart-version'] ) && $porto_settings['cart-version'] ) ? $porto_settings['cart-version'] : 'v1';
	return apply_filters( 'porto_filter_cart_version', $cart_ver );
}
add_action( 'init', 'porto_wc_cart_page' );
function porto_wc_cart_page() {
	global $porto_settings;
	if ( porto_cart_version() == 'v2' ) {
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 20 );

		add_action( 'woocommerce_before_cart_totals', 'porto_shipping_calculator', 1 );
	}
}
function porto_shipping_calculator() {
	if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
		do_action( 'woocommerce_cart_totals_before_shipping' );
		wc_cart_totals_shipping_html();
		do_action( 'woocommerce_cart_totals_after_shipping' );
	} elseif ( WC()->cart->needs_shipping() ) {
		woocommerce_shipping_calculator();
	}
}
add_filter( 'body_class', 'porto_body_class' );
function porto_body_class( $classes ) {
	if ( is_cart() && porto_cart_version() == 'v2' ) {
		$classes[] = 'cart-v2';
	} elseif ( is_checkout() && porto_checkout_version() == 'v2' ) {
		$classes[] = 'checkout-v2';
	}
	// login popup
	global $porto_settings;
	if ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) {
		$classes[] = 'login-popup';
	}
	return $classes;
}
function porto_checkout_version() {
	global $porto_settings;
	$checkout_ver = ( isset( $porto_settings['checkout-version'] ) && $porto_settings['checkout-version'] ) ? $porto_settings['checkout-version'] : 'v1';
	return apply_filters( 'porto_filter_checkout_version', $checkout_ver );
}
add_action( 'init', 'porto_wc_checkout_page' );
function porto_wc_checkout_page() {
	global $porto_settings;
	if ( porto_checkout_version() == 'v2' ) {
		add_action( 'woocommerce_review_order_before_payment', 'porto_woocommerce_review_order_before_payment' );
	}
}
function porto_woocommerce_review_order_before_payment() {
	echo '</div><div class="col-lg-6">';
}
/* End - Cart & Checkout */

// woocommerce vendor start
if ( class_exists( 'WC_Vendors' ) ) {
	add_action( 'woocommerce_after_shop_loop_item_title', 'porto_wpvendors_product_seller_name', 2 );
	remove_action( 'woocommerce_product_meta_start', array( 'WCV_Vendor_Cart', 'sold_by_meta' ), 10, 2 );
	add_action( 'woocommerce_product_meta_start', 'porto_wc_vendors_sold_by_meta', 25, 2 );
	remove_action( 'woocommerce_after_shop_loop_item', array( 'WCV_Vendor_Shop', 'template_loop_sold_by' ), 9 );

	add_filter( 'user_contactmethods', 'porto_add_to_author_profile', 10, 1 );
	function porto_add_to_author_profile( $contactmethods ) {

		$contactmethods['phone_number'] = __( 'Phone Number', 'porto' );
		$contactmethods['facebook_url'] = __( 'Facebook Profile URL', 'porto' );
		$contactmethods['gplus_url']    = __( 'Google Plus Profil URL', 'porto' );
		$contactmethods['twitter_url']  = __( 'Twitter Profile URL', 'porto' );
		$contactmethods['linkedin_url'] = __( 'Linkedin Profile URL', 'porto' );
		$contactmethods['youtube_url']  = __( 'Youtube Profile URL', 'porto' );
		$contactmethods['flickr_url']   = __( 'Flickr Profile URL', 'porto' );

		return $contactmethods;
	}

	function porto_wpvendors_product_seller_name() {

		global $porto_settings;
		$product_id = get_the_ID();
		$author     = WCV_Vendors::get_vendor_from_product( $product_id );

		$vendor_display_name = WC_Vendors::$pv_options->get_option( 'vendor_display_name' );

		switch ( $vendor_display_name ) {
			case 'display_name':
				$vendor      = get_userdata( $author );
				$vendor_name = $vendor->display_name;
				break;
			case 'user_login':
				$vendor      = get_userdata( $author );
				$vendor_name = $vendor->user_login;
				break;
			default:
				$vendor_name = '';
				$vendor_name = WCV_Vendors::is_vendor( $author ) ? WCV_Vendors::get_vendor_shop_name( $author ) : get_bloginfo( 'name' );

		}

		$sold_by = ( WCV_Vendors::is_vendor( $author ) ) ? sprintf( '<a href="%s">%s</a>', WCV_Vendors::get_vendor_shop_page( $author ), $vendor_name ) : get_bloginfo( 'name' );
		if ( $porto_settings['porto_wcvendors_shop_soldby'] ) {
			echo '<p class="product-seller-name">' . apply_filters( 'wcvendors_sold_by_in_loop', __( 'By', 'porto' ) ) . ' <span>' . wp_kses_post( $sold_by ) . '</span> </p>';
		}

	}

	function porto_wc_vendors_sold_by_meta() {
		global $porto_settings;
		$author_id           = get_the_author_meta( 'ID' );
		$vendor_display_name = WC_Vendors::$pv_options->get_option( 'vendor_display_name' );

		switch ( $vendor_display_name ) {
			case 'display_name':
				$vendor      = get_userdata( $author_id );
				$vendor_name = $vendor->display_name;
				break;
			case 'user_login':
				$vendor      = get_userdata( $author_id );
				$vendor_name = $vendor->user_login;
				break;
			default:
				$vendor_name = '';
				$vendor_name = ( WCV_Vendors::is_vendor( $author_id ) ) ? WCV_Vendors::get_vendor_shop_name( $author_id ) : get_bloginfo( 'name' );
		}

		$sold_by = WCV_Vendors::is_vendor( $author_id ) ? sprintf( '<a href="%s">%s</a>', WCV_Vendors::get_vendor_shop_page( $author_id ), $vendor_name ) : get_bloginfo( 'name' );

		if ( $porto_settings['porto_wcvendors_product_soldby'] ) {
			echo '<ul class="list-item-details"><li class="list-item-details"><span class="data-type">' . esc_html__( 'Sold by : ', 'porto' ) . '</span><span class="value">' . esc_html( $sold_by ) . '</span></li></ul>';
		}
	}

	add_action( 'show_user_profile', 'porto_user_profile_fields' );
	add_action( 'edit_user_profile', 'porto_user_profile_fields' );
	function porto_user_profile_fields( $user ) {
		$r = get_user_meta( $user->ID, 'picture', true );
		?>
		<!-- Artist Photo Gallery -->
		<h3><?php esc_html_e( 'Public Profile - Gallery', 'porto' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row">Picture</th>
				<td><input type="file" name="picture" value="" /></td>
			</tr>
			<tr>
				<td>
					<?php
					if ( $r && isset( $r['url'] ) ) {
						$r = $r['url'];
						echo "<img width='200px' src='$r' alt='profile' />";
					}
					?>
				</td>
			</tr>
		</table> 
		<?php
	}
	add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
	add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

	function save_extra_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false; }
		$_POST['action'] = 'wp_handle_upload';
		$r               = wp_handle_upload( $_FILES['picture'] );
		update_user_meta( $user_id, 'picture', $r, get_user_meta( $user_id, 'picture', true ) );
	}
	add_action( 'user_edit_form_tag', 'make_form_accept_uploads' );
	function make_form_accept_uploads() {
		echo ' enctype="multipart/form-data"';
	}
	// Woocommerce Vendor Function More Product
	function porto_more_seller_product() {
		global $product, $porto_woocommerce_loop;
		if ( empty( $product ) || ! $product->exists() ) {
			return;
		}
		global $post;
		if ( ! WCV_Vendors::is_vendor( $post->post_author ) ) {
			return;
		}
		$meta_query                        = WC()->query->get_meta_query();
		$args                              = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
			'posts_per_page'      => 4,
			'author'              => get_the_author_meta( 'ID' ),
			'meta_query'          => $meta_query,
			'orderby'             => 'desc',
		);
		$products                          = new WP_Query( $args );
		$porto_woocommerce_loop['columns'] = isset( $porto_settings['product-related-cols'] ) ? $porto_settings['product-related-cols'] : $porto_settings['product-cols'];
		if ( ! $porto_woocommerce_loop['columns'] ) {
			$porto_woocommerce_loop['columns'] = 4;
		}
		if ( $products->have_posts() ) :
			?>
			<div class="related products">
				<h2 class="slider-title"><span class="inline-title"><?php esc_html_e( 'More from this seller&hellip;', 'porto' ); ?></span></h2>

				<?php woocommerce_product_loop_start(); ?>
					<?php
					while ( $products->have_posts() ) :
						$products->the_post();
						?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; // end of the loop. ?>

				</div>

				<?php woocommerce_product_loop_end(); ?>

			<?php
		endif;
		wp_reset_postdata();
	}
}

// add color attribute type
add_filter( 'product_attributes_type_selector', 'porto_add_product_attribute_color', 10, 1 );
function porto_add_product_attribute_color( $attrs ) {
	return array_merge( $attrs, array( 'color' => __( 'Color', 'porto' ) ) );
}
add_action( 'woocommerce_product_option_terms', 'porto_add_product_attribute_color_variation', 10, 2 );
function porto_add_product_attribute_color_variation( $attribute_taxonomy, $i ) {
	if ( 'color' !== $attribute_taxonomy->attribute_type ) {
		return;
	}

	global $product_object;
	if ( ! $product_object && isset( $_POST['post_id'] ) && isset( $_POST['product_type'] ) ) {
		$product_id     = absint( $_POST['post_id'] );
		$product_type   = ! empty( $_POST['product_type'] ) ? wc_clean( $_POST['product_type'] ) : 'simple';
		$classname      = WC_Product_Factory::get_product_classname( $product_id, $product_type );
		$product_object = new $classname( $product_id );
	}
	if ( $product_object ) {
		$attributes = $product_object->get_attributes( 'edit' );
		if ( ! array_key_exists( 'pa_' . $attribute_taxonomy->attribute_name, $attributes ) ) {
			return;
		}
		$options = $attributes[ 'pa_' . $attribute_taxonomy->attribute_name ]->get_options();
	} else {
		$options = array();
	}
	?>
	<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'woocommerce' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
		<?php
		$args      = array(
			'orderby'    => 'name',
			'hide_empty' => 0,
		);
		$all_terms = get_terms( 'pa_' . $attribute_taxonomy->attribute_name, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
		if ( $all_terms ) {
			foreach ( $all_terms as $term ) {
				$options = ! empty( $options ) ? $options : array();
				echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( in_array( $term->term_id, $options ), true, false ) . '>' . esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
			}
		}
		?>
	</select>
	<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
	<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
	<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'woocommerce' ); ?></button>
	<?php
}

add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'porto_woocommerce_dropdown_variation_attribute_options_html', 10, 2 );
function porto_woocommerce_dropdown_variation_attribute_options_html( $select_html, $args ) {
	global $porto_settings;
	if ( isset( $porto_settings['product_variation_display_mode'] ) && 'select' === $porto_settings['product_variation_display_mode'] ) {
		return $select_html;
	}
	$args             = wp_parse_args(
		apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
		array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		)
	);
	$options          = $args['options'];
	$product          = $args['product'];
	$attribute        = $args['attribute'];
	$name             = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id               = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class            = $args['class'];
	$show_option_none = $args['show_option_none'] ? true : false;

	$is_color             = false;
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $tax ) {
			if ( 'color' === $tax->attribute_type && wc_attribute_taxonomy_name( $tax->attribute_name ) === $attribute ) {
				$is_color = true;
				break;
			}
		}
	}

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html = '';
	if ( ! empty( $options ) ) {
		$swatch_options = $product->get_meta( 'swatch_options', true );
		$key            = md5( sanitize_title( $attribute ) );

		$html .= '<ul class="filter-item-list" name="' . esc_attr( $name ) . '">';
		if ( $product ) {
			$select_html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			$select_html .= '<option value=""></option>';

			if ( taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						$attribute_terms[] = array(
							'id'      => md5( $term->slug ),
							'slug'    => $term->slug,
							'label'   => $term->name,
							'term_id' => $term->term_id,
						);
					}
				}
			} else {
				foreach ( $options as $term ) {
					$attribute_terms[] = array(
						'id'    => ( md5( sanitize_title( strtolower( $term ) ) ) ),
						'slug'  => esc_html( $term ),
						'label' => esc_html( $term ),
					);
				}
			}

			$is_image = false;
			if ( isset( $swatch_options[ $key ] ) && isset( $swatch_options[ $key ]['type'] ) ) {
				if ( ! $is_color ) {
					$is_color = 'color' == $swatch_options[ $key ]['type'];
				}
				$is_image = 'image' == $swatch_options[ $key ]['type'];
			}
			if ( $is_image ) {
				$image_size = isset( $swatch_options[ $key ]['size'] ) ? $swatch_options[ $key ]['size'] : 'swatches_image_size';
			}

			foreach ( $attribute_terms as $term ) {
				$color_value = '';
				if ( isset( $term['term_id'] ) ) {
					$color_value = get_term_meta( $term['term_id'], 'color_value', true );
				}

				if ( ( ! isset( $color_value ) || ! $color_value ) && isset( $swatch_options[ $key ] ) && isset( $swatch_options[ $key ]['attributes'][ $term['id'] ]['color'] ) ) {
					$color_value = $swatch_options[ $key ]['attributes'][ $term['id'] ]['color'];
				}
				$current_attribute_image_src = '';
				if ( $is_image && isset( $swatch_options[ $key ]['attributes'][ $term['id'] ]['image'] ) ) {
					$current_attribute_image_id = $swatch_options[ $key ]['attributes'][ $term['id'] ]['image'];
					if ( $current_attribute_image_id ) {
						$current_attribute_image_src = wp_get_attachment_image_src( $current_attribute_image_id, $image_size );
						$current_attribute_image_src = $current_attribute_image_src[0];
					}
				}

				if ( $is_color ) {
					$a_class      = 'filter-color';
					$option_attrs = ' data-color="' . esc_attr( $color_value ) . '"';
					$a_attrs      = ' style="background-color: ' . esc_attr( $color_value ) . '"';
				} elseif ( $is_image ) {
					$a_class      = 'filter-item filter-image';
					$option_attrs = ' data-image="' . esc_url( $current_attribute_image_src ) . '"';
					if ( $current_attribute_image_src ) {
						$a_attrs = ' style="background-image: url(' . esc_url( $current_attribute_image_src ) . ')"';
					} else {
						$a_attrs = '';
					}
				} else {
					$a_class      = 'filter-item';
					$option_attrs = '';
					$a_attrs      = '';
				}

				$select_html .= '<option' . $option_attrs . ' value="' . esc_attr( $term['slug'] ) . '" ' . selected( sanitize_title( $args['selected'] ), $term['slug'], false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '</option>';

				$html     .= '<li>';
					$html .= '<a href="#" class="' . $a_class . '" data-value="' . esc_attr( $term['slug'] ) . '" ' . ( sanitize_title( $args['selected'] ) == $term['slug'] ? ' class="active"' : '' ) . $a_attrs . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term['label'] ) ) . '</a>';
				$html     .= '</li>';
			}
			$select_html .= '</select>';
		}
		$html .= '</ul>';
	}
	return $html . $select_html;
}

add_filter( 'woocommerce_layered_nav_term_html', 'porto_woocommerce_layed_nav_color_html', 10, 4 );
function porto_woocommerce_layed_nav_color_html( $term_html, $term, $link, $count ) {
	$term_html   = '';
	$color_value = get_term_meta( $term->term_id, 'color_value', true );
	$attrs       = '';
	if ( $color_value ) {
		$attrs = ' class="filter-color" style="background-color: ' . esc_attr( $color_value ) . '"';
	}

	// fix for yith ajax navigation
	if ( isset( $_GET['product_cat'] ) ) {
		$link = add_query_arg( array( 'product_cat' => $_GET['product_cat'] ), remove_query_arg( 'product_cat', $link ) );
	}

	if ( $count > 0 ) {
		$link      = str_replace( '#038;', '&', $link );
		$link      = str_replace( '&&', '&', $link );
		$term_html = '<a href="' . esc_url( $link ) . '"' . $attrs . '>' . esc_html( $term->name ) . '</a>';
	} else {
		$link      = false;
		$term_html = '<span' . $attrs . '>' . esc_html( $term->name ) . '</span>';
	}
	if ( $color_value ) {
		$before_html = ob_get_clean();
		$before_html = str_replace( '"woocommerce-widget-layered-nav-list"', '"woocommerce-widget-layered-nav-list filter-item-list"', $before_html );
		ob_start();
		echo porto_filter_output( $before_html );
	}
	return $term_html;
}

add_filter( 'yith_woocommerce_reset_filter_link', 'porto_yith_woocommerce_reset_filter_link' );
function porto_yith_woocommerce_reset_filter_link( $link ) {
	if ( ! isset( $_GET['source_id'] ) && is_product_category() ) {
		global $wp_query;
		return esc_url( get_term_link( $wp_query->get_queried_object() ) );
	}
	return $link;
}

// add to wishlist
add_filter( 'yith_wcwl_positions', 'porto_add_wishlist_button_position', 10, 1 );
function porto_add_wishlist_button_position( $position ) {
	$position['add-to-cart'] = array(
		'hook'     => 'woocommerce_after_add_to_cart_button',
		'priority' => 31,
	);
	return $position;
}

// horizontal filter
function porto_woocommerce_output_horizontal_filter() {
	global $porto_shop_filter_layout;
	if ( isset( $porto_shop_filter_layout ) && 'horizontal' === $porto_shop_filter_layout ) {
		if ( porto_is_ajax() && isset( $_COOKIE['porto_horizontal_filter'] ) && 'opened' == $_COOKIE['porto_horizontal_filter'] ) {
			$class = ' opened';
		} else {
			$class = '';
		}
		echo '<span class="porto-product-filters-toggle' . $class . '"><span>' . esc_html__( 'Filters:', 'porto' ) . '</span><a href="#">&nbsp;</a></span>';
	} elseif ( isset( $porto_shop_filter_layout ) && 'horizontal2' === $porto_shop_filter_layout ) {
		echo '<div class="porto-product-filters style2 mobile-sidebar">';
			echo '<div class="sidebar-toggle"><i class="fa"></i></div>';
				echo '<div class="porto-product-filters-body">';
				dynamic_sidebar( 'woo-category-filter-sidebar' );
			echo '</div>';
		echo '</div>';
		unset( $porto_shop_filter_layout );
	}
}

// sale product period
function porto_woocommerce_sale_product_period() {
	global $product;
	if ( $product->is_on_sale() ) {
		$is_single   = is_singular( 'product' ) && is_single( $product->get_id() );
		$extra_class = '';
		if ( $is_single && $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();
			$date_diff  = '';
			$sale_date  = '';
			foreach ( $variations as $variation ) {
				$new_date = get_post_meta( $variation['variation_id'], '_sale_price_dates_to', true );
				if ( ! $new_date || ( $date_diff && $date_diff != $new_date ) ) {
					$date_diff = false;
				} elseif ( $new_date ) {
					if ( false !== $date_diff ) {
						$date_diff = $new_date;
					}
					$sale_date = $new_date;
				}
				if ( false === $date_diff && $sale_date ) {
					break;
				}
			}
			if ( $date_diff ) {
				$date_diff = date( 'Y/m/d H:i:s', (int) $date_diff );
			} elseif ( $sale_date ) {
				$extra_class .= ' for-some-variations';
				$date_diff    = date( 'Y/m/d H:i:s', (int) $sale_date );
			}
		} else {
			$date_diff = $product->get_date_on_sale_to();
			if ( $date_diff ) {
				$date_diff = $product->get_date_on_sale_to()->date( 'Y/m/d H:i:s' );
			}
		}
		if ( $date_diff ) {
			echo '<div class="sale-product-daily-deal' . $extra_class . '"' . ( $extra_class ? ' style="display: none"' : '' ) . '>';
				echo '<h5 class="daily-deal-title">' . esc_html__( 'Offer Ends In:', 'porto' ) . '</h5>';
			if ( $is_single ) {
				echo do_shortcode( '[porto_countdown datetime="' . $date_diff . '" countdown_opts="sday,shr,smin,ssec" string_days="' . esc_attr__( 'Day', 'porto' ) . '" string_days2="' . esc_attr__( 'Days', 'porto' ) . '" string_hours="' . esc_attr__( 'Hour', 'porto' ) . '" string_hours2="' . esc_attr__( 'Hours', 'porto' ) . '" string_minutes="' . esc_attr__( 'Minute', 'porto' ) . '" string_minutes2="' . esc_attr__( 'Minutes', 'porto' ) . '" string_seconds="' . esc_attr__( 'Second', 'porto' ) . '" string_seconds2="' . esc_attr__( 'Seconds', 'porto' ) . '"]' );
			} else {
				echo do_shortcode( '[porto_countdown datetime="' . $date_diff . '" countdown_opts="sday,shr,smin,ssec" string_days="' . esc_attr__( 'Day', 'porto' ) . '" string_days2="' . esc_attr__( 'Days', 'porto' ) . '" string_hours=":" string_hours2=":" string_minutes=":" string_minutes2=":" string_seconds="" string_seconds2=""]' );
			}
			echo '</div>';
		}
	}
}

// get sale end date
function porto_woocommerce_get_sale_end_date( $vars, $product, $variation ) {
	if ( $variation->is_on_sale() ) {
		$date_diff = $variation->get_date_on_sale_to();
		if ( $date_diff ) {
			$date_diff                     = $date_diff->date( 'Y/m/d H:i:s' );
			$vars['porto_date_on_sale_to'] = $date_diff;
		}
	}
	return $vars;
}

// account login popup
add_action( 'wp_ajax_porto_account_login_popup', 'porto_account_login_popup' );
add_action( 'wp_ajax_nopriv_porto_account_login_popup', 'porto_account_login_popup' );
function porto_account_login_popup() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	global $porto_settings;
	if ( ! is_checkout() && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) {
		$is_facebook_login = porto_nextend_facebook_login();
		$is_google_login   = porto_nextend_google_login();
		$is_twitter_login  = porto_nextend_twitter_login();
		echo '<div id="login-form-popup" class="lightbox-content">';
		echo wc_get_template_part( 'myaccount/form-login' );
		if ( ( $is_facebook_login || $is_google_login || $is_twitter_login ) && get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' && ! is_user_logged_in() ) {
			echo wc_get_template_part( 'myaccount/login-social' );
		}
		echo '</div>';
		die();
	}
	// phpcs:enable
}
add_action( 'wp_ajax_porto_account_login_popup_login', 'porto_account_login_popup_login' );
add_action( 'wp_ajax_nopriv_porto_account_login_popup_login', 'porto_account_login_popup_login' );
function porto_account_login_popup_login() {

	$nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
	$result      = false;
	if ( wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
		try {
			$creds = array(
				'user_login'    => trim( $_POST['username'] ),
				'user_password' => $_POST['password'],
				'remember'      => isset( $_POST['rememberme'] ),
			);

			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

			if ( $validation_error->get_error_code() ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => '<strong>' . esc_html__(
							'Error:',
							'woocommerce'
						) . '</strong> ' . $validation_error->get_error_message(),
					)
				);
				die();
			}

			if ( empty( $creds['user_login'] ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => '<strong>' . esc_html__(
							'Error:',
							'woocommerce'
						) . '</strong> ' . esc_html__(
							'Username is required.',
							'woocommerce'
						),
					)
				);
				die();
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login
			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );
			if ( ! is_wp_error( $user ) ) {
				$result = true;
			}
		} catch ( Exception $e ) {
		}
	}
	if ( $result ) {
		echo json_encode(
			array(
				'loggedin' => true,
				'message'  => esc_html__(
					'Login successful, redirecting...',
					'porto'
				),
			)
		);
	} else {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => esc_html__(
					'Wrong username or password.',
					'porto'
				),
			)
		);
	}
	die();
}
add_action( 'wp_ajax_porto_account_login_popup_register', 'porto_account_login_popup_register' );
add_action( 'wp_ajax_nopriv_porto_account_login_popup_register', 'porto_account_login_popup_register' );
function porto_account_login_popup_register() {

	$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
	$nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? $_POST['woocommerce-register-nonce'] : $nonce_value;
	$result      = true;

	if ( wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
		$username = 'no' === get_option( 'woocommerce_registration_generate_username' ) ? $_POST['username'] : '';
		$password = 'no' === get_option( 'woocommerce_registration_generate_password' ) ? $_POST['password'] : '';
		$email    = $_POST['email'];

		try {
			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );

			if ( $validation_error->get_error_code() ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $validation_error->get_error_message(),
					)
				);
				die();
			}

			$new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

			if ( is_wp_error( $new_customer ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $new_customer->get_error_message(),
					)
				);
				die();
			}

			if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) {
				wc_set_customer_auth_cookie( $new_customer );
			}
		} catch ( Exception $e ) {
			$result = false;
		}
	}
	if ( $result ) {
		echo json_encode(
			array(
				'loggedin' => true,
				'message'  => esc_html__(
					'Register successful, redirecting...',
					'porto'
				),
			)
		);
	} else {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => esc_html__(
					'Register failed.',
					'porto'
				),
			)
		);
	}
	die();
}

// shortcodes ajax action
add_action( 'wp_ajax_porto_woocommerce_shortcodes_products', 'porto_woocommerce_shortcodes_products' );
add_action( 'wp_ajax_nopriv_porto_woocommerce_shortcodes_products', 'porto_woocommerce_shortcodes_products' );
function porto_woocommerce_shortcodes_products() {
	//check_ajax_referer( 'porto-nonce', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	$atts  = '';
	$atts .= ' ids="' . esc_attr( $_POST['ids'] ) . '"';
	if ( $_POST['category'] ) {
		$atts .= ' category="' . esc_attr( $_POST['category'] ) . '"';
	}
	$atts .= ' columns="' . esc_attr( $_POST['columns'] ) . '"';
	$atts .= ' count="' . esc_attr( $_POST['count'] ) . '"';
	$atts .= ' orderby="' . esc_attr( $_POST['orderby'] ) . '"';
	$atts .= ' pagination_style="' . esc_attr( $_POST['pagination_style'] ) . '"';
	if ( isset( $_POST['product-page'] ) ) {
		$_GET['product-page'] = esc_attr( $_POST['product-page'] );
	}
	if ( isset( $_POST['view'] ) ) {
		$atts .= ' view="' . esc_attr( $_POST['view'] ) . '"';
	}
	if ( isset( $_POST['navigation'] ) ) {
		$atts .= ' navigation="' . esc_attr( $_POST['navigation'] ) . '"';
	}
	if ( isset( $_POST['nav_pos'] ) ) {
		$atts .= ' nav_pos="' . esc_attr( $_POST['nav_pos'] ) . '"';
	}
	if ( isset( $_POST['nav_pos2'] ) ) {
		$atts .= ' nav_pos2="' . esc_attr( $_POST['nav_pos2'] ) . '"';
	}
	if ( isset( $_POST['show_nav_hover'] ) ) {
		$atts .= ' show_nav_hover="' . esc_attr( $_POST['show_nav_hover'] ) . '"';
	}
	if ( isset( $_POST['pagination'] ) ) {
		$atts .= ' pagination="' . esc_attr( $_POST['pagination'] ) . '"';
	}
	if ( isset( $_POST['dots_pos'] ) ) {
		$atts .= ' dots_pos="' . esc_attr( $_POST['dots_pos'] ) . '"';
	}
	echo '<div class="porto-products-response">';
	echo do_shortcode( '[porto_products' . $atts . ']' );
	if ( $_POST['category'] && isset( $_POST['category_description'] ) ) {
		$term = get_term_by( 'slug', $_POST['category'], 'product_cat', 'ARRAY_A' );
		if ( $term && isset( $term['description'] ) ) {
			WPBMap::addAllMappedShortcodes();
			echo '<div class="category-description">';
			echo do_shortcode( $term['description'] );
			echo '</div>';
		}
	}
	echo '</div>';

	// phpcs:enable
	die();
}

// shortcodes image size
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
add_action( 'woocommerce_before_subcategory_title', 'porto_woocommerce_subcategory_thumbnail', 10 );

if ( ! function_exists( 'porto_woocommerce_subcategory_thumbnail' ) ) :
	function porto_woocommerce_subcategory_thumbnail( $category ) {

		global $porto_woocommerce_loop;
		$dimensions = false;
		if ( isset( $porto_woocommerce_loop['image_size'] ) && $porto_woocommerce_loop['image_size'] ) {
			$small_thumbnail_size = $porto_woocommerce_loop['image_size'];
		} else {
			$small_thumbnail_size = 'woocommerce_thumbnail';
		}
		$small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', $small_thumbnail_size );
		$thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );

			if ( isset( $porto_woocommerce_loop['image_size'] ) && $porto_woocommerce_loop['image_size'] ) {
				$dimensions = array(
					'width'  => $image[1],
					'height' => $image[2],
				);
			}

			$image        = $image[0];
			$image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $small_thumbnail_size ) : false;
			$image_sizes  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $small_thumbnail_size ) : false;

		} else {
			$image        = wc_placeholder_img_src();
			$image_srcset = false;
			$image_sizes  = false;
		}

		if ( $image ) {
			if ( ! $dimensions ) {
				$dimensions = wc_get_image_size( $small_thumbnail_size );
			}
			// Prevent esc_url from breaking spaces in urls for image embeds.
			// Ref: https://core.trac.wordpress.org/ticket/23605.
			$image = str_replace( ' ', '%20', $image );

			// Add responsive image markup if available.
			if ( $image_srcset && $image_sizes ) {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" />';
			} else {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
			}
		}
	}
endif;

// add variable product attributes & quantity input on shop pages
add_filter( 'woocommerce_loop_add_to_cart_link', 'porto_woocommerce_display_quantity_input_on_shop_page', 10, 2 );
add_action( 'woocommerce_after_shop_loop_item', 'porto_woocommerce_display_variation_on_shop_page' );

function porto_woocommerce_display_quantity_input_on_shop_page( $html, $product ) {

	global $porto_settings, $porto_woocommerce_loop;
	$availability = $product->get_availability();
	$stock_status = $availability['class'];
	if ( ( 'quantity' == $porto_settings['category-addlinks-pos'] || ( isset( $porto_woocommerce_loop['addlinks_pos'] ) && 'quantity' == $porto_woocommerce_loop['addlinks_pos'] ) ) && $product->is_type( 'simple' ) && $product->is_purchasable() && 'out-of-stock' !== $stock_status ) {
		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);
	}
	return $html;
}

function porto_woocommerce_display_variation_on_shop_page() {

	global $product, $porto_settings;
	if ( isset( $porto_settings['show_swatch'] ) && $porto_settings['show_swatch'] && $product->is_type( 'variable' ) ) {
		$attributes                 = $product->get_variation_attributes();
		$woocommerce_taxonomies     = wc_get_attribute_taxonomies();
		$woocommerce_taxonomy_infos = array();
		foreach ( $woocommerce_taxonomies as $tax ) {
			$woocommerce_taxonomy_infos[ wc_attribute_taxonomy_name( $tax->attribute_name ) ] = $tax;
		}

		$swatch_options = $product->get_meta( 'swatch_options', true );

		foreach ( $attributes as $key => $attr ) {
			if ( array_key_exists( $key, $woocommerce_taxonomy_infos ) && isset( $woocommerce_taxonomy_infos[ $key ]->attribute_type ) ) {
				if ( 'color' != $woocommerce_taxonomy_infos[ $key ]->attribute_type ) {
					if ( $swatch_options ) {
						$swatch_key = md5( sanitize_title( $key ) );
						if ( ! isset( $swatch_options[ $swatch_key ] ) || ( 'color' != $swatch_options[ $swatch_key ]['type'] && 'image' != $swatch_options[ $swatch_key ]['type'] ) ) {
							unset( $attributes[ $key ] );
						}
					} else {
						unset( $attributes[ $key ] );
					}
				}
			}
		}

		wc_get_template(
			'single-product/add-to-cart/variable.php',
			array(
				'available_variations' => $product->get_available_variations(),
				'attributes'           => $attributes,
				'selected_attributes'  => $product->get_default_attributes(),
				'no_add_to_cart'       => true,
			)
		);
	}
}

if ( ! function_exists( 'porto_woocommerce_product_sticky_addcart' ) ) :
	/**
	 *
	 */
	function porto_woocommerce_product_sticky_addcart() {
		global $porto_settings;
		if ( ! is_product() || ! isset( $porto_settings['product-sticky-addcart'] ) || ! $porto_settings['product-sticky-addcart'] ) {
			return;
		}

		global $product;
		$attachment_id = method_exists( $product, 'get_image_id' ) ? $product->get_image_id() : get_post_thumbnail_id();
		$availability  = $product->get_availability();
		$average       = $product->get_average_rating();

		echo '<div class="sticky-product hide"><div class="container">';
			echo '<div class="sticky-image">';
				echo wp_get_attachment_image( $attachment_id, 'thumbnail' );
			echo '</div>';
			echo '<div class="sticky-detail">';
				echo '<div class="product-name-area">';
					echo '<h2 class="product-name">' . get_the_title() . '</h2>';
					echo woocommerce_template_single_price();
				echo '</div>';
				echo '<div class="star-rating" title="' . esc_attr( $average ) . '">';
					echo '<span style="width:' . ( ( $average / 5 ) * 100 ) . '%"></span>';
				echo '</div>';
				echo '<div class="availability"><span>' . ( 'out-of-stock' == $availability['class'] ? esc_html__( 'Out of stock', 'porto' ) : esc_html__( 'In stock', 'porto' ) ) . '</span></div>';
			echo '</div>';
			echo '<div class="add-to-cart">';
				echo '<button type="submit" class="single_add_to_cart_button button">' . esc_html__( 'Add to cart', 'woocommerce' ) . '</button>';
			echo '</div>';
		echo '</div></div>';
	}
endif;

if ( ! function_exists( 'porto_woocommerce_add_to_cart_notification_html' ) ) :
	function porto_woocommerce_add_to_cart_notification_html() {
		?>
		<div class="after-loading-success-message">
			<div class="background-overlay"></div>
			<div class="loader success-message-container">
				<div class="msg-box">
					<div class="msg"><?php _e( "You've just added this product to the cart", 'porto' ); ?>:<p class="product-name text-color-primary"></p></div>
				</div>
				<button class="button btn-primay viewcart" data-link=""><?php esc_html_e( 'Go to cart page', 'porto' ); ?></button>
				<button class="button btn-primay continue_shopping"><?php esc_html_e( 'Continue', 'porto' ); ?></button>
			</div>
		</div>
		<?php
	}
endif;
