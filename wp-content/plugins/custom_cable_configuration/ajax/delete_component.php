<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
$delete_id = $_POST['delete_id'];
$delete = $wpdb->delete($wpdb->prefix . 'components', array( 'component_id' => $delete_id ) );        
echo $delete;
?>