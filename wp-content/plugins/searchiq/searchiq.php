<?php
/*
	Plugin Name: SearchIQ
	Plugin URI: http://searchiq.co/
	Description: SearchIQ replaces default WordPress search and offers fast, relevant and a better search engine.
	Author: searchiq 
	Version: 3.2
	Author URI: 
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$siqpluginUrl = plugin_dir_url(__FILE__);
$siqpluginPath = plugin_dir_path(__FILE__);
if(substr($siqpluginUrl, -1) == "/" || substr($siqpluginUrl, -1) == "\\" ){
	$siqpluginUrl  = substr($siqpluginUrl, 0, strlen($siqpluginUrl)-1 );
}
if(substr($siqpluginPath, -1) == "/" || substr($siqpluginPath, -1) == "\\" ){
	$siqpluginPath  = substr($siqpluginPath, 0, strlen($siqpluginPath)-1 );
}

define("SIQ_BASE_URL", $siqpluginUrl);
define("SIQ_ADMIN_URL", get_admin_url().'admin.php?page=dwsearch');
define("SIQ_BASE_PATH", $siqpluginPath);
define("SIQ_PLUGIN_VERSION", "3.2");
define("SIQ_SCRIPT_BASE", '//api.searchiq.co/');
define("SIQ_SERVER_BASE", 'https:' . SIQ_SCRIPT_BASE);
define("SIQ_CUSTOM_GET_PARAM", "q");

define("SIQ_SERVER_API_ENDPOINT", 'api/');
define("SIQ_SERVER_SUB_FOLDER", '');
define("SIQ_TIMEOUT_SECONDS", 30);
define("SIQ_FILE", __FILE__);


global $siqAPIClient;
include_once('library/core.php');
include_once('library/hooks.php');
include_once('library/shortcode.php');
include_once('library/plugin.php');
$siq_plugin = new siq_plugin;
