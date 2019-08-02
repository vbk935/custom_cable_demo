<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;

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
		'coordinate_y' => '250' 
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

$return_array = array();
$t_id = $_REQUEST['this_id'];
$parent_id = $_REQUEST['parent_id'];
$group_id = $_REQUEST['group_id'];
$FID = array_combine($t_id, $parent_id);
$con_count = 0;
foreach ($FID as $key => $value) {
	$term_meta = get_option( "taxonomy_term_$key" );
	$part_image = $term_meta['image'] ? $term_meta['image'] : '';
	$term_name = get_term_by( 'id', $key, 'configuration' );	
	if($term_name->name == "E2000 APC" || $term_name->name == "e2000 apc")
	{
		$con_count++;
	}

	/* Get postion */	
	$canvasData = $wpdb->get_results( "SELECT position FROM ".$wpdb->prefix."canvas_setting WHERE group_id = ".$group_id." AND primary_parent_id = ".$value."" );
	$position = $canvasData[0]->position;
	if($position){				
		if(($term_name->name == "E2000 APC" || $term_name->name == "e2000 apc") && $con_count == 1)
		{
			$coordinate_x = "142";
			$coordinate_y = "100";
		}
		elseif(($term_name->name == "E2000 APC" || $term_name->name == "e2000 apc") && $con_count == 2)
		{
			$coordinate_x = "630";
			$coordinate_y = "100";
		}
		else
		{		
			foreach ($coordinateData as $key => $value) {
				if($position == $key){
					$coordinateValue = $value;
				}
			}
			
			
			$coordinate_x = $coordinateValue['coordinate_x'];
			$coordinate_y = $coordinateValue['coordinate_y'];
		}
	}
	$return_array[] = array('image' => $part_image,'coordinate_x' => $coordinate_x,'coordinate_y' => $coordinate_y );
}


if($return_array){
	$return_array[]= $return_array;
}
echo json_encode(array('return_array_data'=>$return_array));

?>
