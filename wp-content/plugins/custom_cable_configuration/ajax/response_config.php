<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
$data = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."configuration_conditions ORDER BY cid  desc LIMIT ".$_REQUEST['start']." ,".$_REQUEST['length']." " );
$dataCount = $wpdb->get_results( "SELECT count(*) as count FROM ".$wpdb->prefix."configuration_conditions" );
foreach ($data as $key => $value)
{
	if(empty($value->title) || $value->title == "")
	{
		$tooltip_title = "View Details";
	}
	else
	{
		$tooltip_title = $value->title;
	}
	$title = '<div class="tooltip">'.$tooltip_title.'<span class="tooltiptext"><ul>';
	$child_key_arr = [];
	$child_val_arr=[];
    if($value->primary_parent_id)
    {
      $terms_group  =  get_term($value->group_id, 'configuration');       
      $terms_parent =  get_term($value->primary_parent_id, 'configuration');
      $value_child = json_decode($value->primary_child_id);      
      foreach($value_child as $child_key => $child_val)
      {
        $child_key_terms  =  get_term($child_key, 'configuration');
        $child_val_terms  =  get_term($child_val, 'configuration');        
        $child_key_arr[]=$child_key_terms->name;
        $child_val_arr[]=$child_val_terms->name.'('.$child_val_terms->slug.')';
      }
      $config_type_val = implode(', ',$child_key_arr);      
      $config_value_val = implode(', ',$child_val_arr);
      
      $conditions_value = json_decode($value->conductions);      
      foreach($conditions_value as $condition_key=>$condition_value)
      {
        $condition_key_term = get_term($condition_key, 'configuration');        
        $title .= "<li><h4>".$condition_key_term->name."</h4><ul>";
        foreach($condition_value as $condition_val)
        {
          $condition_val_term = get_term($condition_val, 'configuration');
          $title .= "<li>".$condition_val_term->name."(".$condition_val_term->slug.")</li>";
        }  
        $title .="</ul></li>";
      }
      $title .= "</ul></span></div>";
      $action = "<a href=".admin_url('edit.php?post_type=product&page=configuration_conditions_update&cid='.$value->cid)." class='edit-btn'>Edit</a>  <a href='javascipt:void(0);' onclick='delete_data_config(this);return false' class='delete-btn deleteConduction' data-id=".$value->cid.">Delete</a>";
      $dataF[] = array($terms_group->name,$config_type_val,$config_value_val,$title,$action);
    }
}
$json_data = array(
    "draw"            => intval( $_REQUEST['draw'] ),   
    "recordsTotal"    => intval(  $dataCount[0]->count ),  
    "recordsFiltered" => intval( $dataCount[0]->count),
            "data"            => $dataF?$dataF:array()   // total data array
            );
echo json_encode($json_data); ?>

  
