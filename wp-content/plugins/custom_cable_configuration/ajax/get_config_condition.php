<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
$parentId = $_POST['parentId'];
$check_parent = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."configuration_conditions where group_id ='".$parentId."'");
if(empty($check_parent))
{
	echo json_encode(array('success'=>false));
}
else 
{
	echo json_encode(array('success'=>true));
}
?>
