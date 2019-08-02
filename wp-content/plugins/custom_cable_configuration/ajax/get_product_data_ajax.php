<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
//to get all the configured products and to compare with the current configuring element each time
if($_POST['action'] == "session_des"){
	unset($_SESSION['part_number']);
}


/* product id */
if(isset($_POST['action']) &&  $_POST['action'] == 'product')
{
	$pid = $_POST['product_id']; 
	$tid = $_POST['term_id']; 	
	$configured_product_meta_data=get_post_meta( $pid, 'configuration_combination',true);		
	$unserialize_configured_meta =  unserialize($configured_product_meta_data);
	$dataPrs = $unserialize_configured_meta[$tid];	
	
	$foo = 1;
	$postProductData = array();
	foreach ($dataPrs as $key => $value) {
		$postProductData[] = array(
			'curval' => $foo,
			'group_id' => $tid,
			'product_type' => $key,
			'product_value' => $value,
			'product_value_input' => '',
			'product_display_type' => ''
			);
		$valueProduct[]=$value; 
		$coordinateData = array(
			'right-connector' => array(
				'coordinate_x' => '650',
				'coordinate_y' => '100' 
				),
			'left-connector' => array(
				'coordinate_x' => '122',
				'coordinate_y' => '100' 
				),
			'wire' => array(
				'coordinate_x' => '226',
				'coordinate_y' => '100' 
				),
			'right-boot' => array(
				'coordinate_x' => '699',
				'coordinate_y' => '100' 
				),
			'left-boot' => array(
				'coordinate_x' => '226',
				'coordinate_y' => '100' 
				)
			);
		//boot 600,170 ,wire 300,105
		$return_array = array();


		$t_id = $value;
		$p_id = $key;
		$g_id = $tid;
		$term_meta = get_option( "taxonomy_term_$t_id" );


		$part_image = $term_meta['image'] ? $term_meta['image'] : '';						

		/* Get postion */

		$canvasData = $wpdb->get_results( "SELECT position FROM ".$wpdb->prefix."canvas_setting WHERE group_id = ".$g_id." AND primary_parent_id = ".$p_id."" );
		$position = $canvasData[0]->position;												
		if(!empty($part_image)  &&  !empty($position))
		{
			$return_array['image'] = $part_image;	
			$return_array['position'] = $position;
			$term_name = get_term_by( 'id', $key, 'configuration' );	
			$return['tname'] = $term_name->name;
		}
		

		if($return_array)
		{
			$return_multiple_array[]= $return_array;
		}

		$foo++;
	}
	
	
	// Get Conditions Data	
	foreach ($postProductData as  $prodata) 
	{
		$selected_val[] =  $prodata['product_value'];
	}	
	
	$conductionData = $wpdb->get_results( "SELECT primary_child_id,conductions FROM ".$wpdb->prefix."configuration_conditions WHERE group_id = ".$tid."" , ARRAY_A);
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
	
	$check_arr = [];
	foreach($combined_data as $combine_value)
	{	
		$check = array_intersect($selected_val,$combine_value['primary']);		
		$check_count = count($check);
		$primary_count = count($combine_value['primary']);
		
		if(!empty($check) && ($check_count == $primary_count))
		{				
			foreach($combine_value['conductions'] as $cond_val)
			{
				$check_arr[] = $cond_val;
			}
		} 
	}	
}

$_SESSION['product_configuration']=$term_meta_data;
$_SESSION['part_number'] = $dataPrs;



$finalPartNodata = '';
//echo "<pre>"; print_r($_SESSION['part_number']);
foreach($_SESSION['part_number'] as $part_key=> $part_number)
{
	$main_term = get_term_by('id', $part_key, 'configuration');
	$term_meta = get_option( "taxonomy_term_$part_number" );
	$config_price[] = $term_meta['config_price'];
	if($part_number == "-")
	{
		$finalPartNodata[] = "-";
	}
	else if($term_meta['presenter_id'] == "changable")
	{		
		$finalPartNodata[] = $value_input;
	}
	else
	{
		$finalPartNodata[] = $term_meta['unit_name'];
	}
}
$finalPartNodataUnit ='';
foreach ($finalPartNodata as  $value)
{
	//echo $value;echo "****";
		$finalPartNodataUnit .= $value;
		
}
$config_price_sum = array_sum($config_price);
$_SESSION['config_price_sum'] = $config_price_sum;
$action = site_url().'/cart';
echo json_encode(array('success'=>true,'final_pass'=>$final_pass,'valueProduct'=>$valueProduct,'conductionData'=>$dataC?$dataC:'','conductionDataValue'=>$dataCv?$dataCv:'','return_array'=>$return_multiple_array,'pagecount'=>$foo,'product_id'=>$product_id,'product_configuration'=>$term_meta_data,'matched_product_id'=>$pid,'check_arr'=>$check_arr,'config_price'=> $_SESSION['config_price_sum'],'part_number'=>$finalPartNodataUnit,'action'=>$action));
?>
