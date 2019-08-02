<?php
/**
 * Composited Product Image
 * @version  2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( has_post_thumbnail( $product_id ) ) {

	?><div class="composited_product_images"><?php

			$image_title = esc_attr( get_the_title( get_post_thumbnail_id( $product_id ) ) );
			$image_link  = wp_get_attachment_url( get_post_thumbnail_id( $product_id ) );
			$image       = get_the_post_thumbnail( $product_id, apply_filters( 'woocommerce_composited_large_thumbnail_size', 'shop_thumbnail' ), array(
				'title' => $image_title
				) );

			echo apply_filters( 'woocommerce_composited_product_image_html', sprintf( '<a href="%s" class="composited_product_image zoom" title="%s" data-rel="prettyPhoto">%s</a>', $image_link, $image_title, $image ), $product_id );

	?></div><?php
}
