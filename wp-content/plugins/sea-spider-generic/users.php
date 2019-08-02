<?php
// Hide Remaining menu items.	
	function ssg_hide_admin() {
	global $user_level;
		if( current_user_can( 'client_admin' ) ) {
			echo '<style type="text/css"></style>';
		}
		if( current_user_can( 'client_user' ) ) {
			
		}
	}
	
	add_action('admin_head', 'ssg_hide_admin');	

?>