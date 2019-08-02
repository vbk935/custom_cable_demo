<?php
@session_start();
require('../../../../wp-load.php');
global $wpdb;
$parentId = $_POST['parentId'];
$check_parent = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."configuration_conditions where group_id ='".$parentId."'");
if(!empty($check_parent))
{
	$where = array('group_id' => $parentId);
	$delete = $wpdb->delete('x2mnb_configuration_conditions',$where);
	function sample_admin_notice__success()
	{
?>
		<div class="notice notice-success is-dismissible" style="display:none;">
			<p><?php _e( 'Group Conditions Deleted Successfully.', 'sample-text-domain' ); ?></p>
		</div>
<?php
	}
	//add_action( 'admin_notices', 'sample_admin_notice__success' );
	echo json_encode(array('success'=>true));
}

?>
