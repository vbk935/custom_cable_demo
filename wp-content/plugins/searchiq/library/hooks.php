<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class siq_hooks extends siq_core{
	
	public function __construct(){
		parent::__construct();
		$this->executeHooks();
	}
	
	public function executeHooks(){
		add_action( 'transition_post_status', 	array($this, 'siq_post_status'), 10, 3);
		add_action( 'save_post', array($this, 'siq_save_post'));
		add_action( 'untrash_post', array($this, 'siq_save_post'));
		add_action('admin_init',  array($this, 'siq_redirect_after_media_save'));
		add_action('delete_attachment',  array($this, '_deletePdfFromSearchiq'));
	}
	
	public function siq_save_post($postID) {
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$post = get_post($postID);
		$postTypes = $this->getPostTypesForIndexing();
		if ($post->post_status != "publish" || !in_array($post->post_type, $postTypes) || trim($post->post_title) == "") return;
		if (wp_is_post_revision( $postID )) {
			return;
		}
		$post = get_post($postID);
		if (!empty($post)) {
			$this->lock_meta_update == true;
			parent::siq_insert_post($post->ID, $post);
			$this->lock_meta_update == false;
		}
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
	}
	
	public function siq_post_status($new_status, $old_status=null, $post=null){
		global $siq_plugin;
		$this->lock_meta_update == true;
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$postID = is_numeric( $this->pluginSettings['custom_search_page'] ) ? $this->pluginSettings['custom_search_page']  : url_to_postid($this->pluginSettings['custom_search_page']);
		if (!empty($post->ID) && !empty($siq_plugin) && ! empty( $postID ) && $postID == $post->ID) {
			if (!has_shortcode($post->post_content, 'siq_ajax_search') || $new_status != 'publish') {
				$siq_plugin->createCustomSearchPageIfNotExists();
                                $this->_siq_sync_settings();
			}
		}
		if(trim($post->post_title) != ""){
			if($new_status != 'publish' && $old_status == "publish" && !wp_is_post_revision($post->ID)){
				$post_type = $post->post_type;
				parent::siq_delete_post($post->ID, $post_type);
			}
		} else if (trim($post->post_title) == "") {
			$post_type = $post->post_type;
			parent::siq_delete_post($post->ID, $post_type);
		}
		$this->lock_meta_update == false;
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
	}
	
	public function siq_redirect_after_media_save(){
		 global $pagenow;
		if ( $pagenow == 'upload.php' ) { // we are on media library page
			$data = $_GET;
			if(array_key_exists("found_post_id", $data) && array_key_exists("find-posts-submit", $data) && array_key_exists("media", $data)){
				$media 	= $data["media"];
				$postID = $data["found_post_id"];
				parent::addPdfToSearchiq($media, $postID);
			}else if(array_key_exists("parent_post_id", $data) && array_key_exists("media", $data)){
				$media 	= $data["media"];
				$postID = $data["parent_post_id"];
				parent::deletePdfFromSearchiq($media, $postID);
			}
		}
	}
	public function _deletePdfFromSearchiq($media){
		parent::deletePdfFromSearchiq($media);
	}
}
