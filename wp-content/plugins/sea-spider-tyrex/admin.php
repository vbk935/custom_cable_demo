<?php

add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');
 
function new_mail_from($old) {
	$email_default = get_field('default_email', 'option');
	if($email_default != ""){
		return $email_default;
	}
}
 
function new_mail_from_name($old){
	$email_name = get_field('field_name', 'option');
	if($email_name != ""){
		return $email_name;
	}
}
?>