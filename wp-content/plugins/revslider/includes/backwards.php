<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

/**
 * backwards compatibility code
 * @START
 **/
//mostly needed for RevSlider AddOns
class RevSliderGlobals {
	const SLIDER_REVISION = RS_REVISION;
	public static $table_slides;
	public static $table_static_slides;
}
global $wpdb;

RevSliderGlobals::$table_slides = $wpdb->prefix.'revslider_slides';
RevSliderGlobals::$table_static_slides = $wpdb->prefix.'revslider_static_slides';
class RevSliderBase {
	
	public static function check_file_in_zip($d_path, $image, $alias, $alreadyImported = false){
		$f = new RevSliderFunctions();
		
		return $f->check_file_in_zip($d_path, $image, $alias, $alreadyImported, $add_path = false);
	}
}

class RevSliderFunctionsWP {
	public static function getImageUrlFromPath($url){
		$f = new RevSliderFunctions();
		return $f->get_image_url_from_path($url);
	}
	
	public static function get_image_id_by_url($image_url){
		$f = new RevSliderFunctions();
		return $f->get_image_id_by_url($image_url);
	}
}

class RevSliderOperations {
	public function getGeneralSettingsValues(){
		$f = new RevSliderFunctions();
		return $f->get_global_settings();
	}
}

class RevSlider extends RevSliderSlider {
	public function __construct(){
		//echo '<!-- Slider Revolution Notice: Please do not use the class "RevSlider" anymore, use "RevSliderSlider" instead -->'."\n";
	}
}

class UniteFunctionsRev extends RevSliderFunctions {}

/**
 * backwards compatibility code
 * @END
 **/
 
 ?>