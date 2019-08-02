<?php
require('../../../../wp-load.php');
global $wpdb;
if(!empty($_REQUEST)){
	if($_POST['action'] == 'add'){
		$groupId = $_POST['groupId'];
		$parent_config_id = $_POST['parent_config_id'];
		$position = $_POST['position'];
		$insert = $wpdb->insert($wpdb->prefix . 'canvas_setting', array(
			'group_id' => $groupId, 
			'primary_parent_id' => $parent_config_id,
			'position' => $position
			));
		echo $insert;
	}
	if($_POST['action'] == 'update'){
		$groupId = $_POST['groupId'];
		$parent_config_id = $_POST['parent_config_id'];
		$position = $_POST['position'];
		$canvas_id = $_POST['canvas_id'];
		$checkDataExist = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."canvas_setting WHERE group_id = ".$groupId." AND primary_parent_id = ".$parent_config_id." AND position = ".$position." AND canvas_id != ".$canvas_id."" );
		if($checkDataExist){
			echo '';
		}else{
			$update = $wpdb->update($wpdb->prefix . 'canvas_setting', 
				array(
					'group_id' => $groupId, 
					'primary_parent_id' => $parent_config_id,
					'position' => $position
					), 
				array('canvas_id' => $canvas_id)
				);
			echo $update;
		}
		// //echo $wpdb->last_query;
	}
	if($_REQUEST['action'] == 'delete'){
		$delete_id = $_REQUEST['delete_id'];
		$delete = $wpdb->delete($wpdb->prefix . 'canvas_setting', array( 'canvas_id' => $delete_id ) );
		//echo $wpdb->last_query;
		echo $delete; 
	}
	
}
?>
