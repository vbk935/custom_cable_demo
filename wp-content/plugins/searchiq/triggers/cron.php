<?php 
include_once('../../../../wp-load.php');
$allParams 			= $argv;
$task 						= "deltasync";
$paramString		= "";
$parsedParams	= array();
global $siq_plugin;
if(!is_array($allParams) && count($allParams) > 0){
	foreach($allParams as $k => $v){
			$paramString .= $allParams[$k]."&";
	}
	if(!empty($paramString)){
		$paramString = substr($paramString, 0, -1);
		parse_str($paramString, $parsedParams);
	}
}
if(count($parsedParams) > 0 && array_key_exists("task", $parsedParams)){
	$task = $parsedParams["task"];
}
if(is_array($_REQUEST) && count($_REQUEST) > 0){
	foreach($_REQUEST as $k => $v){
		$parsedParams[$k] = $v;
	}
}
$siq_plugin->processCron($task, $parsedParams);