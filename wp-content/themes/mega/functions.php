<?php
	require get_stylesheet_directory() . '/functions/main.php';
	require get_stylesheet_directory() . '/functions/media.php';
	require get_stylesheet_directory() . '/functions/page.php';
	require get_stylesheet_directory() . '/functions/menus.php';
	require get_stylesheet_directory() . '/functions/search.php';
	require get_stylesheet_directory() . '/functions/taxonomy.php';
	require get_stylesheet_directory() . '/functions/pagination.php';
	require get_stylesheet_directory() . '/functions/text.php';
	require get_stylesheet_directory() . '/functions/dashboard.php';
	require get_stylesheet_directory() . '/functions/posts.php';
	require get_stylesheet_directory() . '/functions/tinymce.php';


	require get_stylesheet_directory() . '/functions/scripts.php';
	require get_stylesheet_directory() . '/functions/styles.php';
	
	
	//admin
	
	require get_stylesheet_directory() . '/functions/admin-sea-spider.php';
	require get_stylesheet_directory() . '/functions/woo.php';
	
	
add_filter( 'woocommerce_cart_item_name', 'add_sku_in_cart', 20, 3);

function add_sku_in_cart( $title, $values, $cart_item_key ) {
    $sku = $values['data']->get_sku();
    return $sku ? $title . sprintf(" (SKU: %s)", $sku) : $title;
}
?>