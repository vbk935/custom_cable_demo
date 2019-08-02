<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
$selected_val = $_POST['selected_val'];
$group_id = $_POST['group_id'];

$conductionData = $wpdb->get_results( "SELECT primary_child_id,conductions FROM ".$wpdb->prefix."configuration_conditions WHERE group_id = ".$group_id."" , ARRAY_A);

foreach($conductionData as $data)
{
	$primaryData = json_decode($data['primary_child_id']);
	$conductionsData = json_decode($data['conductions']);
	$p_arr = [];
	$c_arr = [];
	foreach($primaryData as $p_data)
	{
		$p_arr[] = $p_data;
	}

	foreach($selected_val as $val)
	{
		if(in_array($val,$p_arr))		
		{	
			$c_arr = [];
			foreach($conductionsData as $c_data)
			{	

				foreach ($c_data as $subValue)
				{				
					$c_arr[] = $subValue;
				}		
			}			
		}				
	}
	if(!empty($c_arr))
	{
		$combine['primary'] = $p_arr;
		$combine['conductions'] = $c_arr;
		$combined_data[] = $combine;	
	}
}


usort($combined_data, function($a, $b){
    $aCount = count($a['primary']);
    $bCount = count($b['primary']);
    if ($aCount  == $bCount) {
        return 0;
    }   
    return ($aCount > $bCount) ? -1 : 1;

});

$check_arr=[];
//echo "<pre>";
foreach($combined_data as $combine_value)
{	
	/*echo "Selected Val";
	print_r($selected_val);
	echo "primary";
	print_r($combine_value['primary']);*/
	$check = array_intersect($selected_val,$combine_value['primary']);
	/*echo "check";
	print_r($check);*/
	if(!empty($check)){
		$check_arr[] = $combine_value['conductions'];
	}		
}
echo json_encode($check_arr);exit;
//print_r($check_arr);
?>
