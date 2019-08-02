<?php
require('../../../../wp-load.php');



$user_name  = $_POST['username'];
$password 	= $_POST['password'];
$punchout 	= $_POST['punchout'];
$returnURL 	= $_POST['returnURL'];
$sessionID 	= $_POST['sessionID'];
$client 	= $_POST['client'];

$user_id = username_exists( $user_name );
if (!$user_id) {
		//Register new user
	$user_id = wp_create_user( $user_name, $password);
	add_user_meta($user_id, '_punchout', $punchout);
	add_user_meta($user_id, '_client', $client);
	add_user_meta($user_id, '_returnURL', $returnURL);
	add_user_meta($user_id, '_sessionID', $sessionID);

	return  array('Message' => 'User successfully registered','code' =>  200,'user_id'=>$user_id);
}else{
		//Login and update the user meta data
	update_user_meta($user_id, '_punchout', $punchout);
	update_user_meta($user_id, '_client', $client);
	update_user_meta($user_id, '_returnURL', $returnURL);
	update_user_meta($user_id, '_sessionID', $sessionID);

	return  array('Message' => 'User successfully login','code' =>  200,'user_id'=>$user_id);
}




