<?php
//@session_start();
require('../../../../wp-load.php');
if(isset($_REQUEST['action'])){
  $search_text =  $_REQUEST['order_id'];

  // Check if user is loggedin or not
  if (! is_user_logged_in()) {
      echo 3;
      die();
  }
  
  if(empty($search_text)){
    $customer_orders = get_posts( array(
      'numberposts' => -1,
      'meta_key'    => '_customer_user',
      'meta_value'  => get_current_user_id(),
      'post_type'   => wc_get_order_types(),
      'post_status' => array_keys( wc_get_order_statuses() ),
      ) );
    if(empty($customer_orders)){
       echo 3; // Return 3  for blank record
     }else{
      foreach ($customer_orders as $value) {
        $ID = $value->ID;
        $posts = get_post( $ID );
        $order = wc_get_order( $ID );
        $items = $order->get_items();
       // print_r($order); 
        $status = $order->get_status();
        $date = date( 'Y-m-d', strtotime( $order->order_date ) );
        $toal_amount = number_format((float) $order->get_total(), 2, '.', '');  // Outputs -> 105.00
        $total = $toal_amount.' for '.$order->get_item_count().' item';
        $final_order =  array(
         $posts,
         $item_id
         );
        $orderData[] = array('order_id'=>$posts->ID,'date'=>$date,'status'=>ucfirst($status),'total'=>$total,'reorder'=>'Re-Order');
      }
      echo json_encode($orderData);
    }

  }else{
    $posts = get_post( $search_text );
    if($posts){
      $order = wc_get_order( $search_text );
      if($order){
        $items = $order->get_items(); 
       $status = $order->get_status();
        $date = date( 'Y-m-d', strtotime( $order->order_date ) );
        $toal_amount = number_format((float) $order->get_total(), 2, '.', '');  // Outputs -> 105.00
        $total = $toal_amount.' for '.$order->get_item_count().' item';
        $final_order =  array(
         $posts,
         $item_id
         );
        $orderData[] = array('order_id'=>$posts->ID,'date'=>$date,'status'=>ucfirst($status),'total'=>$total,'reorder'=>'Re-Order');
        echo json_encode($orderData);
      }else{
       echo 3; // Return 3  for blank record
     }
   }else{
        echo 3;  // Return 3  for blank record
      }
    }
  }
  ?>