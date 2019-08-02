<?php
require('../../../../wp-load.php');
global $wpdb;
if(!empty($_REQUEST)){
	if($_POST['action'] == 'add'){
		$title = $_POST['title'];
		$groupId = $_POST['groupId'];
		$parentCategoryId = $_POST['parent_category_id'];
		$pageNumber = $_POST['pageNumber'];
		$childConfigId = $_POST['child_config_id'];
		$parentChildIds = $_POST['child_child'];
		$config_type = explode(",",$_POST['config_type']);
		$config_value = explode(",",$_POST['config_value']);				
		$primary_conditions_arr = array_combine($config_type,$config_value);

		$parentChildIds = json_encode($parentChildIds);
		$primary_conditions = json_encode($primary_conditions_arr);

		
		$select_query = "SELECT * FROM ".$wpdb->prefix."configuration_conditions WHERE group_id = ".$groupId." ";
		$checkDataExist = $wpdb->get_results( $select_query, ARRAY_A);
				
		if($checkDataExist)
		{	
			$i=0;					
			$count_pc_arr = count($primary_conditions_arr);
			foreach($checkDataExist as $data_exists)
			{
				$p_data = json_decode($data_exists['primary_child_id']);
				$val_arr=[];
				foreach($p_data as $key=>$val)
				{
					$val_arr[] = $val;					
				}				
				$count_val_arr = count($val_arr);
				/*echo "val_arr";
				print_r($val_arr);				
				echo "val count =".$count_val_arr;
				echo "<br>primary_conditions_arr";
				print_r($primary_conditions_arr);
				echo "pcount =".$count_pc_arr; */
				if($count_pc_arr == $count_val_arr)
				{
					
					$diff1 = array_diff($val_arr,$primary_conditions_arr);
					$diff2 = array_diff($primary_conditions_arr,$val_arr);
					/*echo "in if";
					echo "diff1";
					print_r($diff1);
					echo "diff2";
					print_r($diff2); */
					if(empty($diff1) || empty($diff2))
					{
						$i = 0;
						break; 
					}
					elseif(!empty($diff1) || !empty($diff2))
					{
						$i = 1;						
					}
				}	
				else
				{
					$i = 1;
					//echo "in else";
				}			
			}
		}
		
		//echo "i=".$i."****";
		if(empty($checkDataExist) || ($checkDataExist && $i==1))
		{			
			$insert = $wpdb->insert($wpdb->prefix . 'configuration_conditions', array(
				'group_id' => $groupId, 
				'primary_parent_id' => $parentCategoryId,
				'primary_child_id' => $primary_conditions, 			
				'conductions' => $parentChildIds,
				'pageNumber' => $pageNumber,
				'title'=>$title
				));

			echo $insert;
		}
		else
		{
			echo '';
		} 

	}
	if($_POST['action'] == 'update'){		
		$title = $_POST['title'];
		$groupId = $_POST['groupId'];
		$parentCategoryId = $_POST['parent_category_id'];
		$pageNumber = $_POST['pageNumber'];
		$childConfigId = $_POST['child_config_id'];
		$parentChildIds = $_POST['child_child'];
		$cid = $_POST['cid'];
		$config_type = explode(",",$_POST['config_type']);
		$config_value = explode(",",$_POST['config_value']);				
		$primary_conditions_arr = array_combine($config_type,$config_value);

		$parentChildIds = json_encode($parentChildIds);
		$primary_conditions = json_encode($primary_conditions_arr);

		$select_query = "SELECT * FROM ".$wpdb->prefix."configuration_conditions WHERE group_id = ".$groupId." AND cid != ".$cid." ";
		$checkDataExist = $wpdb->get_results( $select_query, ARRAY_A);
		
		if($checkDataExist){
			$i=0;
			$count_pc_arr = count($primary_conditions_arr);
			foreach($checkDataExist as $data_exists)
			{
				$p_data = json_decode($data_exists['primary_child_id']);
				$val_arr = [];
				foreach($p_data as $key=>$val)
				{					
					$val_arr[] = $val;	
				}
				$count_val_arr = count($val_arr);
				if($count_pc_arr == $count_val_arr)
				{
					$diff1 = array_diff($val_arr,$primary_conditions_arr);
					$diff2 = array_diff($primary_conditions_arr,$val_arr);
					if(empty($diff1) || empty($diff2))
					{
						$i = 0;
						break; 
					}
					elseif(!empty($diff1) || !empty($diff2))
					{
						$i = 1;						
					}
				}
				else
				{
					$i = 1;
					//echo "in else";
				}				
			}			
		}
		if(empty($checkDataExist) || ($checkDataExist && $i==1))
		{
			$update = $wpdb->update($wpdb->prefix . 'configuration_conditions', 
				array(
					'group_id' => $groupId, 
					'primary_parent_id' => $parentCategoryId,
					'primary_child_id' => $primary_conditions, 
					'conductions' => $parentChildIds,
					'pageNumber' => $pageNumber,
					'title' => $title
					), 
				array('cid' => $cid)
				);
			echo $update;
		}
		else
		{			
			echo '';
		}
	}
	if($_REQUEST['action'] == 'delete'){
		$delete_id = $_REQUEST['delete_id'];
		$delete = $wpdb->delete($wpdb->prefix . 'configuration_conditions', array( 'cid' => $delete_id ) );
		//echo $wpdb->last_query;
		echo $delete;
	}
}
?>
