<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
$data = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."canvas_setting ORDER BY canvas_id  asc LIMIT ".$_REQUEST['start']." ,".$_REQUEST['length']." " );
$dataCount = $wpdb->get_results( "SELECT count(*) as count FROM ".$wpdb->prefix."canvas_setting" );
foreach ($data as $key => $value) {
    if($value->primary_parent_id){
        $terms_group  =  get_term($value->group_id, 'configuration');
        $terms_parent =  get_term($value->primary_parent_id, 'configuration');
        $position = $value->position;
        $action = "<a href=".admin_url('edit.php?post_type=product&page=canvas_settings_update&canvas_id='.$value->canvas_id)." class='edit-btn'>Edit</a>  <a href='javascipt:void(0);' class='delete-btn deleteConductionCanvas' onclick='delete_data(this);return false' data-id=".$value->canvas_id.">Delete</a>";
        $dataF[] = array($terms_group->name,$terms_parent->name,$position,$action);
        ?>
     
        <?php
    }
}
$json_data = array(
    "draw"            => intval( $_REQUEST['draw'] ),   
    "recordsTotal"    => intval( $dataCount[0]->count ),  
    "recordsFiltered" => intval($dataCount[0]->count),
            "data"            => $dataF?$dataF:array()  // total data array
            );
echo json_encode($json_data); ?>

  