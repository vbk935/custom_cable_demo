<?php
require('../../../../wp-load.php');
if(isset($_REQUEST['action']) && $_REQUEST['action'] =='partno'){
	$givenPartNumber = $_REQUEST['part_no'];
	function getConfiguredProducts(){
		$configured_products = get_posts(array(
			'post_type'   => 'product',
			'numberposts' => -1,
			'orderby'=>'id',
			'order'=>'DESC',
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'     => 'configuration_combination',
					'value'   => '',
					'compare' => '!='
				),
				array(
					'key'     => 'configuration_combination',
                'compare' => 'EXISTS' // doesn't do anything, just a reminder
            ),
			),
			'fields'=>'ids',
			'suppress_filters' => true,
		));
		foreach($configured_products as $key)
		{
			$configured_product_meta_data=get_post_meta( $key, 'configuration_combination',true);
			$configured_product_meta_data_arr[$key] = unserialize($configured_product_meta_data);
		}
		return $configured_product_meta_data_arr;
	}
	$configData=getConfiguredProducts();
	$items = array();
  //Get the unit name
	foreach($configData as  $configDataProductKey => $configDataProduct)
	{  
		foreach ($configDataProduct as $configDataProductValueKey => $configDataProductValue) {
			$finalPartNodata = array();
			foreach ($configDataProductValue as $key => $child_id) {
				$term_meta = get_option( "taxonomy_term_$child_id" ); 
				$finalPartNodata[$configDataProductValueKey] .= $term_meta['unit_name'];
			}
			foreach ($finalPartNodata as $productKey => $finalPartNodataValue) {
				if($finalPartNodataValue){
					$existValue =  strpos($finalPartNodataValue,$givenPartNumber); //Product id
					if($existValue === 0 || $existValue > 0){
						$prdData['id'] = $productKey;
						$prdData['product_id'] = $configDataProductKey;
						$prdData['part_no'] = $finalPartNodataValue;
						$dataProduct[] = $prdData;
					}
				}
			}
		}
	}
	foreach ($dataProduct as  $prdId) {
		$product = wc_get_product( $prdId['product_id'] );
		$terms_configuration['id'] =  get_term($prdId['id'], 'configuration' );
		$terms_configuration['part_no'] =  $prdId['part_no'];
		$terms_configuration['product_id'] =  $prdId['product_id'];
		$terms_configuration['product_name'] =   $product->get_title();
		$terms_configuration_data[] = $terms_configuration;
	}
	echo  json_encode($terms_configuration_data);
}
if($_REQUEST['action'] == 'orderData'){
	$order_id = $_REQUEST['order_id'];
	$order = wc_get_order( $order_id );
	foreach ( $order->get_items()  as $item_id => $item) {
		
		// Compatibility for woocommerce 3+
    $product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $item['product_id'] : $item->get_product_id();

		$part_number = wc_get_order_item_meta( $item_id, 'Part Number', true );
		$qty = wc_get_order_item_meta( $item_id, '_qty', true );
		$total = wc_get_order_item_meta( $item_id, '_line_total', true )/$qty;
		$product_details = wc_get_order_item_meta( $item_id, 'Products Details', true );
		$product_label = wc_get_order_item_meta( $item_id, 'Product Label', true );
		$product_weight = wc_get_order_item_meta( $item_id, 'Product Weight', true );	
		$res[] = array(
					'product_id' 			=>	$product_id,
					'part_number'			=>	$part_number,
					'price'						=>	$total,
					'product_label'		=>	$product_label,
					'product_weight'	=>	$product_weight, 
					'myJson'					=>	stripslashes($product_details),
				);
		
	}
	echo json_encode($res);
	die();
}

if($_REQUEST['action'] == 'checkLogin') {
	$flag = 0;
	if (is_user_logged_in() )
		$flag = 1;
	$response = array(
		'login' => $flag
	);
	wp_send_json($response);
	die();
}

if($_REQUEST['action'] == 'get_all_orders') {
	$orderid_arr = array();
	$user_id = wp_get_current_user()->id;
    $customer_orders = get_posts( array(
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'post_type'   => 'shop_order',
        'post_status' => array_keys( wc_get_order_statuses() ),
        'numberposts' => -1
    ));
    foreach($customer_orders as $orders)
    {
        $partno = array();
        $get_order = wc_get_order($orders->ID);
        $items = $get_order->get_items();
        $date_created = date_create($orders->post_date);
        $date_created = date_format($date_created, 'Y-m-d');
        
        $total_amount = number_format((float) $get_order->get_total(), 2, '.', '');  // Outputs -> 105.00
        $total = $total_amount.' for '.$get_order->get_item_count().' item';

        // $total = number_format((float) $get_order->get_total(), 2, '.', '');
        $status = ucfirst($get_order->get_status());

        foreach($items as $key => $item){
            $order_meta = wc_get_order_item_meta($key,'Part Number');
            if(!empty($order_meta))
            {
               echo "<input type='hidden' data-q='***' name='partno_orderid' id='".$order_meta."' value='".$orders->ID."' data-date='". $date_created ."' data-status='".$status."' data-total='".$total."'>";
               $partno_arr[] =  $order_meta;
            }
        }
        $orderid = (string)$orders->ID;
        $orderid_arr[] = $orderid;
    }

    wp_send_json($orderid_arr);
    die();
}
?>