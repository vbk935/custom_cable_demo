<?php
/**
 * Template Name: Search Listing
 *
 */
get_header(); ?>


<?php 
global $wpdb;
$search_text = $_POST['search_text'];
if($search_text == "")
{
	echo "Please provide search details!";
} else {
?>

<div class="product-list-sec">
	<h1 class="page-title">Search Results</h1>
	<ul class="col-4-list">
		<?php 
			$get_products = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'posts where post_type="product" and (post_title LIKE "%'.$search_text.'%" or post_content LIKE "%'.$search_text.'%" )');
			if(!empty($get_products))
			{
				foreach($get_products as $product)
				{				
					$product_id= $product->ID;
					$product_title = $product->post_title;
					$product_name = $product->post_name;
					$product_image = get_the_post_thumbnail_url($product->ID);
		?>
					<li><a href="<?php echo get_site_url()."/product/".$product_name; ?>">
						<div class="list-box">					
							<div class="tabl-col">
								<div class="table-c">
									<img src="<?php echo $product_image; ?>" alt="<?php echo $product_title; ?>">
								</div>
							</div>
							<h3><?php echo $product_title; ?></h3>					
						</div>
						</a>
					</li>
		<?php 
				} 
			} else {
				$prod_result = "none";
			}			
		?>
	</ul>
	<div class="order-search">	
		<?php
			$get_keywords = $wpdb->get_results('SELECT post_id FROM '.$wpdb->prefix.'postmeta where (meta_key="_yoast_wpseo_focuskw_text_input"  or meta_key ="_yoast_wpseo_focuskw") and meta_value LIKE "%'.$search_text.'%" ');
			
			$keywrds_arr = array();
			foreach($get_keywords as $keyword)
			{
				$post_id = $keyword->post_id;				
				if(!in_array($post_id, $keywrds_arr))
				{
					$get_post = get_post( $post_id );	
					$post_title = $get_post->post_title;
					$post_url = $get_post->post_name;
					$keywrds_arr[] = $post_id;
		?>
					<div class="order-inner">Page :  <a href='<?php echo get_site_url()."/".$post_url; ?>'><?php echo $post_title; ?></a></div>
		<?php
				}	
			}



		if ( is_user_logged_in() ) 
		{
			//Define statuses of that orders to get
				$order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');

				//Get Current User Id
				$customer_user_id = get_current_user_id(); // current user ID here for example

				// Getting current customer orders
				$customer_orders = wc_get_orders( array(
					'meta_key' => '_customer_user',
					'meta_value' => $customer_user_id,
					'post_status' => $order_statuses,
					'numberposts' => -1
				) );

				$orders = array();
				// Loop through each customer WC_Order objects
				foreach($customer_orders as $order ){

					// Order ID (added WooCommerce 3+ compatibility)
					$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
					
					// Iterating through current orders items				
					foreach($order->get_items() as $item_id => $item)
					{					
						$part_number = wc_get_order_item_meta( $item_id, 'Part Number', true );
						if($search_text == $part_number && !in_array($order_id,$orders))
						{
							$orders[] = $order_id;
							
		?>
							<div class="order-inner">Part Number : <?php echo $part_number; ?> <a href='".get_site_url()."/my-account/view-order/".$order_id."'>Order Id : <?php echo $order_id; ?></a></div>
		<?php
						}
					}					
				}	
		}
		?>
	</div>
		<?php 
		if($prod_result == "none" && count($orders) == 0 && count($keywrds_arr) == 0)
		{
			echo "No Results Found";
		}
	?>
</div>
<?php } ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
