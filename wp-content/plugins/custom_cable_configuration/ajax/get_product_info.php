<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
//to get all the configured products and to compare with the current configuring element each time
$prodata=$_POST;
if($_POST['action'] == "session_des" || $prodata['curval'] == 1){
	unset($_SESSION['part_number']);
}
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


$selected_val = $prodata['selected_val'];
$conductionData = $wpdb->get_results( "SELECT primary_child_id,conductions FROM ".$wpdb->prefix."configuration_conditions WHERE group_id = ".$prodata['group_id']."" , ARRAY_A);

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

	//if(!empty($check) && ($check_count == $primary_count)){		
	if(!empty($check) && ($check_count == $primary_count)){	
			
		foreach($combine_value['conductions'] as $cond_val)
		{
			$check_arr[] = $cond_val;
		}
		//break;
	} 
}

// conduction data for filter
if((isset($prodata['product_display_type']) && $prodata['product_display_type'] != ""))
{		

	//Empty part number details from session after dash
	if($_SESSION['matched_product_id'] != "" || $_SESSION['matched_product_id'] != null)
	{
		$_SESSION['matched_product_id'] = "";
		$_SESSION['config_price_sum'] = ""; 
		
		if(in_array("-",$_SESSION['part_number']))
		{
			$n=array_keys($_SESSION['part_number']); 
			$count=array_search("-",$n); 
			$_SESSION['part_number']=array_slice($_SESSION['part_number'],0,$count,true);			
		}		
	}
	$child_id=$prodata['product_value'];
	$group_id = $prodata['group_id'];
	//print_r($_SESSION['part_number'][$prodata['product_type']]);
	$final_configured_data_array=getConfiguredProducts();
	$_SESSION['product_configuration_part_number'] = array();
	//print_r($final_configured_data_array);
	foreach($final_configured_data_array as  $key => $final_configured_product)
	{
		foreach ($final_configured_product as $key1 => $value) {
			$comp = array();
			if($key1 == $group_id){
				//echo "value"; print_r($value);
				//echo "part_number"; print_r($_SESSION['part_number']);
				$comp = array_diff($_SESSION['part_number'], $value);							
				if(empty($comp))
				{
					$product_id = $key;
					$_SESSION['matched_product_id'] = $key;
					$_SESSION['excerpt'] = get_the_excerpt( $key );					
					foreach($_SESSION['part_number'] as $part_number)
					{
						$term_meta = get_option( "taxonomy_term_$part_number" );
						$config_price[] = $term_meta['config_price'];
					}
					$config_price_sum = array_sum($config_price);
					$_SESSION['config_price_sum'] = $config_price_sum;
				}
				else
				{
					$product_id ='' ;	
					//$_SESSION['matched_product_id'] = "";
				}
			}	
		}
	}	
	$value_input = $prodata['product_value_input'];
	
	$_SESSION['value_input'] = $value_input;
	$_SESSION['display_type'] = $prodata['product_display_type'];
	$_SESSION['part_number'][0] = '-';	
	$_SESSION['part_number'][$prodata['product_type']] = $prodata['product_value'];	
	$final_pass="yes";
	$action = site_url().'/cart';
}
else
{	
	if($prodata['curval']==$prodata['total_parts'])
	{						
		foreach($_SESSION['part_number'] as $pno_key => $pno_value)
		{
			if($pno_value == "-")
			{
				break;
			}
			$part_number_array[$pno_key] = $pno_value;			
		}
		
		$child_id=$prodata['product_value'];
		$group_id = $prodata['group_id'];
		$final_configured_data_array=getConfiguredProducts();
		$_SESSION['product_configuration_part_number'] = array();
		foreach($final_configured_data_array as  $key => $final_configured_product)
		{
			foreach ($final_configured_product as $key1 => $value) {
				$comp = array();
				if($key1 == $group_id){
					//$comp = array_diff($_SESSION['part_number'], $value);
					$comp = array_diff($part_number_array, $value);
					if(empty($comp)){
						$product_id = $key;
						$_SESSION['matched_product_id'] = $key;
						$_SESSION['excerpt'] = get_the_excerpt( $key );						
						foreach($_SESSION['part_number'] as $part_number)
						{
							$term_meta = get_option( "taxonomy_term_$part_number" );
							$config_price[] = $term_meta['config_price'];	
						}
						$config_price_sum = array_sum($config_price);
						$_SESSION['config_price_sum'] = $config_price_sum;					
					}else{
						$product_id ='' ;										
					}					
				}	
			}
		}
		$_SESSION['part_number'][$prodata['product_type']] = $prodata['product_value'];	
		
		if($_SESSION['value_input'] != "")
		{									
			$per_unit_meta = get_post_meta( $_SESSION['matched_product_id'], 'per_unit',true);
			$per_unit_meta_data_arr = unserialize($per_unit_meta);
			foreach($_SESSION['part_number'] as $pno)
			{
				if(array_key_exists($pno,$per_unit_meta_data_arr))
				{
					$config_item_price = $per_unit_meta_data_arr[$pno];					
				}						
			}			
			$config_price = $_SESSION['config_price_sum'] + ($_SESSION['value_input'] * $config_item_price);
			$_SESSION['config_price_sum'] = $config_price;
		}	
		$value_input = $prodata['product_value_input'];
		
		$_SESSION['value_input'] = $value_input;		
		$_SESSION['display_type'] = $prodata['product_display_type'];
		print_r($_SESSION['display_type'])		;
		$final_pass="yes";
		$action = site_url().'/cart';
	}
	else 
	{		
		if($prodata['curval'] == 1){ 
			$_SESSION['part_number'] = array();
			$_SESSION['matched_product_id'] = "";
			$_SESSION['value_input'] = "";
			$_SESSION['config_price_sum'] = ""; 
			$_SESSION['excerpt'] = "";
		}
		$child_id=$prodata['product_info']['value'];	
		$term_meta = get_option( "taxonomy_term_$child_id" );
		$parent_type=$prodata['product_info']['type'];
		$group_id = $prodata['group_id'];
		$final_pass="no";
		if(in_array($prodata['product_value'],$_SESSION['part_number']))
		{
			$n=array_keys($_SESSION['part_number']); 
			$count=array_search($prodata['product_type'],$n); 
			$_SESSION['part_number']=array_slice($_SESSION['part_number'],0,$count+1,true);			
		}
		$value_input = $prodata['product_value_input'];
		$_SESSION['value_input'] = $value_input;
		$_SESSION['part_number'][$prodata['product_type']] = $prodata['product_value'];
		$_SESSION['matched_product_id'] = "";
		$_SESSION['config_price_sum'] = ""; 
		$_SESSION['excerpt'] = "";
	}	
}
$_SESSION['product_configuration']=$term_meta_data;
$finalPartNodata = '';
//echo "<pre>"; print_r($_SESSION['part_number']);
foreach($_SESSION['part_number'] as $part_key=> $part_number)
{
	$main_term = get_term_by('id', $part_key, 'configuration');
	$term_meta = get_option( "taxonomy_term_$part_number" );
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
$lastUnitName = $finalPartNodata[$prodata['total_parts']];
echo json_encode(array('success'=>true,'final_pass'=>$final_pass,'last_unit_name'=>$lastUnitName?$lastUnitName:null,'product_id'=>$product_id,'product_configuration'=>$term_meta_data,'length'=>$value_input?$value_input:null,'matched_product_id'=>$_SESSION['matched_product_id'],'part_number'=>$finalPartNodataUnit,'action'=>$action,'excerpt'=>$_SESSION['excerpt'],'config_price'=> $_SESSION['config_price_sum'],'check_arr'=>$check_arr));
//}
//print_r($_SESSION);	
?>
