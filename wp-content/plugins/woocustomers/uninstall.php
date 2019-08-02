<?php
/*
	This file is part of wooCustomers
*/	
 
	//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

global $wpdb;

if (function_exists('is_multisite') && is_multisite()){ 
	$blogids = $wpdb->get_col("SELECT blog_id FROM ".$wpdb->prefix."blogs");
	foreach($blogids as $blogid_x){
		$blogid_x = ($blogid_x == 1)? "": $blogid_x."_";
		$wpdb->query("DROP TABLE ".$wpdb->prefix.$blogid_x."woocustomers");
		$wpdb->query("DROP TABLE ".$wpdb->prefix.$blogid_x."woocustomers_orders");
	}
}else{
	$wpdb->query("DROP TABLE ".$wpdb->prefix."woocustomers");
	$wpdb->query("DROP TABLE ".$wpdb->prefix."woocustomers_orders");
}

delete_option("wooCustomers_version");
?>
