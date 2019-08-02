<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;  
$delete_id = $_POST['delete_id'];
$delete = $wpdb->delete($wpdb->prefix . 'cable_pricing', array( 'id' => $delete_id ) );        
echo $delete;
?>