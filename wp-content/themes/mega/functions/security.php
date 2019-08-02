<?php
//*****************************************************************************
// Security - Pingback Denial of Service Attacks
// http://wptavern.com/how-to-prevent-wordpress-from-participating-in-pingback-denial-of-service-attacks
//*****************************************************************************

add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );
function remove_xmlrpc_pingback_ping( $methods ) {
   unset( $methods['pingback.ping'] );
   return $methods;
} ;
?>