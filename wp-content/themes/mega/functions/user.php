<?php
//http://wpbandit.com/code/check-a-users-role-in-wordpress/ ?>
<?php /*?>function check_user_role($roles,$user_id=NULL) {
	// Get user by ID, else get current user
	if ($user_id)
		$user = get_userdata($user_id);
	else
		$user = wp_get_current_user();

	// No user found, return
	if (empty($user))
		return FALSE;

	// Append administrator to roles, if necessary
	if (!in_array('administrator',$roles))
		$roles[] = 'administrator';

	// Loop through user roles
	foreach ($user->roles as $role) {
		// Does user have role
		if (in_array($role,$roles)) {
			return TRUE;
		}
	}

	// User not in roles
	return FALSE;
}
 
 
 
 
 // Define roles to check
		//$roles = array('editor','author');

// Check roles
		//$in_role = check_user_role($roles);

// Do something based on role
//if ($in_role) {
  // User in role, do something
//} else {
  // User not in role do something else
//}
 ?>