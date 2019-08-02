<?php
require('../../../../wp-load.php');
global $wpdb;
$name = $_POST['name'];
if($_POST['parent'] == "-1")
{
	$parent = 0;
}
else
{
	$parent = $_POST['parent'];
}

$taxonomy_type = $_POST['taxonomy_type'];
$query_term = "SELECT * FROM ".$wpdb->prefix."terms WHERE name = '".$name."'";
$get_term  = $wpdb->get_results($query_term, OBJECT );
$check = "";
if(!empty($get_term))
{
	foreach($get_term as $term)
	{
		
		$query_parent = "SELECT * FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = '".$term->term_id."' and parent = '".$parent."' and taxonomy = '".$taxonomy_type."'";
		$get_parent = $wpdb->get_results($query_parent, OBJECT );
		if(!empty($get_parent))
		{
			$check = "1";
		}
	}
	
	if($check != "")		
	{
		echo "matched";
	}
	else
	{
		echo "no match";
	}
}
else
{
	echo "no match";
}

?>