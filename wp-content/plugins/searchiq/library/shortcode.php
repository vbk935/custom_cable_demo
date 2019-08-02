<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class siq_shortcode extends siq_hooks{
	
	function __construct(){
		parent::__construct();
		add_action( 'widgets_init', array($this, 'registerWidgets') );
		if(!is_admin()) {
			add_shortcode('siq_searchbox', array($this, 'siq_searchbox'));
		}
	}

	public function siq_searchbox($atts){
		$searchBox = "";
		$atts = shortcode_atts( array(
			'type'							=> 'search-bar',
			'post-types' 				=> '',
			'placeholder' 			=> '',
			'width'						=> '',
			'placement'				=> 'left'
		), $atts, 'siq_searchbox' );
		$atts = $this->getSelectedPostTypesForSearchBox($atts);
		if($atts['type'] == 'icon'){
			$searchBox = $this->getShortcodeSearchIcon($atts);
		}else{
			$searchBox = $this->getShortcodeSearchBox($atts);
		}
		return $searchBox;
	}
	
	private function getSelectedPostTypesForSearchBox($atts = array()){
		if(!empty($atts['post-types'])){
			$postTypes = explode(',', $atts['post-types']);
			if(!empty($postTypes) && is_array($postTypes) && count($postTypes) > 0){
				$postTypes				= array_map('trim', $postTypes);
				$indexedPostTypes =  $this->getPostTypesForIndexing();
				$postTypesArr = array();
				if(is_array($indexedPostTypes) && count($indexedPostTypes) > 0){
					$indexedPostTypes = array_map('trim', $indexedPostTypes);
					foreach($postTypes as $key => $value){
						if(in_array($value, $indexedPostTypes)){
							array_push($postTypesArr, $value);
						}
					}
					if(count($postTypesArr) > 0){
						$postTypesString = implode(',', $postTypesArr);
						$atts['postTypes'] = $postTypesString;
					}
				}
			}
		}
		return $atts;
	}
	
	private function getShortcodeSearchBox($atts = array()){
		$strWidget        = "";
		$searchValue    = get_search_query();
		$placeholder    = !empty($atts['placeholder']) ? $atts['placeholder'] : "Search";
		$width			= $this->calculateWidth($atts['width']);
		$cssStyle		= !empty($width) ? "style='width:".$width.";' ": "";
		$placementClass =  ($atts['placement'] == "right") ? "moveToExtremeRight" : "";
		$strWidget .='<div class="siq-expandwdgt-cont siq-searchwidget '.$placementClass.'">
		  <form class="siq-expandwdgt siq-searchwidget" action="'.get_home_url().'">
		    <input type="search" placeholder="'.$placeholder.'" '.$cssStyle.' value="'.$searchValue.'" name="s" class="siq-expandwdgt-input siq-searchwidget-input">';
			$postTypes 		= !empty($atts['postTypes'] ) ? $atts['postTypes'] : "";
			if(!empty($postTypes)){
				$strWidget 		.='<input type="hidden"  value="'.$postTypes.'" name="postTypes" />';
			}
		    $strWidget .='<span class="siq-expandwdgt-icon"></span>
		  </form>
		</div>';
		return $strWidget;
	}
	
	private function getShortcodeSearchIcon($atts = array()){
		$positionAbs    = "";
		$openFromClass  = "";
		$styleWidth	= "";
		
		$width		= $this->calculateWidth($atts['width']);
		
		if(!empty($width)){
			$styleWidth = "style='width:" . $width . ";'";
		}
		$openFromClass  = "openFromLeft";	
		
		if ($atts['placement'] == "right") {
			$positionAbs = "moveToExtremeRight";
			$openFromClass  = "";
		}
		
		$strWidget = '<div class="siq-icon-item-siq-selectbox ' . $positionAbs . '">
			<div class="siq-icon-searchbox-wrap '.$openFromClass.'" '.$styleWidth.'>' . $this->shortcodeCustomSearchBoxHtml($atts) . '</div></div>';	
		return $strWidget;
	}
	
	private function calculateWidth($width = ""){
		if(!empty($width)){
			if(strpos($width, 'px') !== FALSE){
				$finalWidth =  abs((int)str_replace("px", "", $width));
				if($finalWidth > 0){
					$finalWidth = $finalWidth."px";
					return $finalWidth;
				}
			}else if(strpos($width, '%') !== FALSE){
				$finalWidth =  abs((int)str_replace("%", "", $width));
				if($finalWidth > 0){
					$finalWidth = $finalWidth."%";
					return $finalWidth;
				}
			}else{
				$finalWidth =  abs((int)$width);
				if($finalWidth > 0){
					return $finalWidth."px";
				}else{
					return "";
				}
			}
		}
		return $width;
	}
	
	private function shortcodeCustomSearchBoxHtml($atts = array()){
		$html       				="";
		$placeholder     	= !empty($atts['placeholder']) ? $atts['placeholder'] : "Search";
		$width				= $this->calculateWidth($atts['width']);
		$cssStyle			= !empty($width) ? "style='width:".$width.";' ": "";
		$html .= '<div class="siq-expsearch-cont">
			  <form class="siq-expsearch" action="'.get_home_url().'">
			    <input '.$cssStyle.' type="search" placeholder="'.$placeholder.'" name="s" class="siq-expsearch-input">';
				$postTypes 		= !empty($atts['postTypes'] ) ? $atts['postTypes'] : "";
				if(!empty($postTypes)){
					$html .='<input type="hidden"  value="'.$postTypes.'" name="postTypes" />';
				}
			   $html .= '<span class="siq-expsearch-icon"></span>
			  </form>
			</div>';
		return $html;
	}
	
	public function registerWidgets(){
		require_once(SIQ_BASE_PATH.'/library/widget-search.php');
		register_widget( 'SIQ_Search_Widget' );
	}
}