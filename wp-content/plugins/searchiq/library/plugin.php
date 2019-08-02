<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class siq_plugin extends siq_shortcode{
	private $errorMessage               = "";
	private $resultSent                 = 0;
	private $postsPerCall 				= 500;	// for bulk index
	private $postsPerCallForThumb 		= 50;	// for thumbnail generating
    private $postsPerCallForBlacklist   = 50;   // For blacklist functionality
	private $postsPerCallForDeltaSync   = 500; // For delta sync
	private $currentStart 				= 0;
	private $totalPosts 				= 0;
	private $postsFetched				= 0;
	private $totalPages					= 0;
	private $currentPage				= 1;
	public  $postsToIndex 				= array( 'post', 'page' );
	public  $postsToIndexAndSearch 		= array( 'post', 'page' );
	public  $searchFields				= array('post_type', 'searchTerm');
	public 	$documentTypesRegistered 	= array();
	private $domain						= "";
	private $defaultDomain				= "";
	public 	$searchPostsPerCall 		= 10;
	public $customSearchString 			= "";
	public $adminNonceString			= "siq_admin_nonce_security";
	public $siteNonceString				= "siq_site_nonce_security";
	public $updateOptionsNonce			= "siq_update_options";
	public $updateAppearanceNonce		= "siq_update_appearance";
	public $updateAutocompleteNonce		= "siq_update_autocomplete";
	public $updateMobileNonce		    = "siq_update_mobile";
	public $customSearchCurrentPage 	= 1;
	public $customSortoption			= 'relevance';
	public $searchPageUrl				= "";
	public $labels						= array();
	public $administerPanelLink			= "";
	public static $frontPanelLink		= "signup.html";
	public $contactFormLink				= "";
	public $adScriptLink				= "";
	public $syncPostIDs					= "";
	public $pluginBaseJs				= "";
	public $maxNumRetry					= 2;
	public $currentTry					= 1;
	public $totalTries					= 0;
	public $totalTriesMax				= 6;
	public $logError					= true;
	public $logAPICalls					= false;
	public $logInfo						= false;
	public $cronCurrentPage				= 0;
	public $results						= false;
	public $searchbox_name              = 's';
	public $search_query_param_name     = 'q';
	public $pluginOptionsDontClear 		= array('custom_search_page', 'custom_search_page_old');

	public $serverDownKey	= "_siq_server_down_while_resync";
	public $autocompleteDefaultNumRecords = 5;
	public $siq_menu_select_box_color     = "";
	public $siq_menu_select_box_pos_top   = 0;
	public $siq_menu_select_box_pos_right = 0;
	public $siq_menu_select_box_pos_absolute = "";
	
	public $syncTableFieldToBeRemoved = 'status';

	private $group_concat_limit = 20000;
    private $group_concat_query = "SET @@group_concat_max_len = ";
    public $search_successful = false;
    public $post_ids = array();
    public $resultPostIds = array();

	public static function init(){
		new self;
	}

	public function __construct(){
		global $siqAPIClient;
		$this->signUpLink		= $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."signup.html");

		$this->administerPanelLink		= $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."puser/instructions.html", true);							// link to the user's dashboard on searchiq.xyz
		$this->contactFormLink			= $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."contact.html");							// link to the contact page on searchiq.xyz

		$this->userGuideLink			= $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."guide.html");							// link to the user guide page on searchiq.xyz

		$this->forgotPasswordLink       = $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."forgot-password.html");
		$this->pricingPageLink			= $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."pricing.html");
		$this->loginPageLink			= $this->clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER."login.html");
		$this->pluginIconUrl			= SIQ_BASE_URL.'/assets/'.SIQ_PLUGIN_VERSION."/images/icon.png";
		parent::enableErrorLog($this->logError, $this->logInfo, $this->logAPICalls);
		parent::__construct();
		global $paged, $post;																							// TODO: not using variables must be deleted
		$this->getDomain();																								// get domain name without protocol and with root WordPress installation path and set it to $this->domain (for ex. example.com/wp)
		$this->createLabels();
		$this->searchPostsPerCall = get_option('posts_per_page');
		$api_key = $siqAPIClient->siq_get_api_key();
		$this->siq_custom_get_param = "";
		$searchQueryParamName = $this->getSearchQueryParamName();
		if(!empty($searchQueryParamName)){
			$this->siq_custom_get_param = $searchQueryParamName;
		}else{
			$this->siq_custom_get_param = SIQ_CUSTOM_GET_PARAM;
		}
		if(isset($_GET[$this->siq_custom_get_param])){
			$this->customSearchString 		= $_GET[$this->siq_custom_get_param];
		}

		add_action( 'admin_menu', array($this, 'createAdminMenu') );
		add_action( 'admin_enqueue_scripts', array($this, 'addScriptsAndStyles'));
		add_action( 'wp_enqueue_scripts', array($this, 'addScriptsAndStylesOnFront'));

		add_action( 'wp_ajax_siq_ajax', array($this,'siq_admin_ajax'));
		add_action( 'wp_ajax_nopriv_siq_ajax', array($this,'siq_admin_ajax'));

		add_action("wp_ajax_siq_get_settings", array($this, "getSIQPluginSettings"));
		add_action("wp_ajax_nopriv_siq_get_settings", array($this, "getSIQPluginSettings"));

		if($api_key != "" && $api_key != null && isset($this->pluginSettings['engine_code']) && !empty($this->pluginSettings['engine_code'])){
			add_action( 'wp_head', array($this, 'includeContainerScript'));

			add_filter( 'the_posts', array( $this, 'get_search_result_posts' ) );
			add_action( 'pre_get_posts', array( $this, 'siq_get_posts' ) );
			add_action( 'template_redirect', array($this, 'check_if_search') );

			add_filter( 'script_loader_tag', array($this, 'add_cfasync_attr_to_script_tag'), 10, 2);

			add_filter("body_class", array($this, "add_body_class"));
			add_shortcode( 'siq_ajax_search',  array($this, 'siq_ajax_search')  );
		}
		add_action('admin_init', array($this,  'siq_admin_init'));
		add_action('admin_notices', array($this, 'siq_admin_notices'));
		add_action('admin_notices', array($this, 'siq_admin_notice_for_searchbox'));
		add_action('admin_notices', array($this, 'siq_admin_notice_for_review'));
		add_action('admin_notices', array($this, 'siq_admin_notice_for_plus_package'));
		
		add_filter('_siq_check_facets_error', array($this, 'checkFacetsError'));
		register_activation_hook(SIQ_FILE, array($this, 'siq_activate'));
		add_action( 'wp_footer', array($this, 'includeSearchboxScript'), 20);
		if(isset($this->pluginSettings['engine_code']) && $this->pluginSettings['engine_code']!=""){
			$this->pluginBaseJs			= preg_replace('/^https?:\/\//i', '//', SIQ_SERVER_BASE) . "js/container/siq-container-2.js";
		}
		if(isset($this->pluginSettings['siq_menu_select_box']) && $this->pluginSettings['siq_menu_select_box'] != "") {
			add_filter( 'wp_nav_menu_'.$this->pluginSettings['siq_menu_select_box'].'_items', array($this,'addSearchboxToMenu') );
		}
		if(isset($this->pluginSettings['siq_menu_select_box_color']) && $this->pluginSettings['siq_menu_select_box_color'] != "") {
			$this->siq_menu_select_box_color = $this->pluginSettings['siq_menu_select_box_color'];
		}

		if(isset($this->pluginSettings['siq_menu_select_box_pos_top']) && $this->pluginSettings['siq_menu_select_box_pos_top'] != 0) {
			$this->siq_menu_select_box_pos_top = $this->pluginSettings['siq_menu_select_box_pos_top'];
		}

		if(isset($this->pluginSettings['siq_menu_select_box_pos_right']) && $this->pluginSettings['siq_menu_select_box_pos_right'] != 0) {
			$this->siq_menu_select_box_pos_right = $this->pluginSettings['siq_menu_select_box_pos_right'];
		}

		if(isset($this->pluginSettings['siq_menu_select_box_pos_absolute']) && $this->pluginSettings['siq_menu_select_box_pos_absolute'] != "") {
			$this->siq_menu_select_box_pos_absolute = $this->pluginSettings['siq_menu_select_box_pos_absolute'];
		}
		if(isset($this->pluginSettings['siq_menu_select_box_direction']) && $this->pluginSettings['siq_menu_select_box_direction'] != "") {
			$this->siq_menu_select_box_direction = $this->pluginSettings['siq_menu_select_box_direction'];
		}
		add_action('init', array($this,  'add_metadata_hooks'));
	}

	public function add_metadata_hooks(){
		add_action('woocommerce_product_set_stock_status', array($this, 'siq_update_product_stock'), 99);
		add_action('woocommerce_variation_set_stock_stock', array($this, 'siq_update_product_stock'), 99);
	
		add_action( 'updated_post_meta', array($this, 'siq_update_metadata'), 10, 4 ); 
		add_action( 'added_post_meta', array($this, 'siq_add_metadata'), 10, 4 ); 
	}
	
	public function siq_add_metadata( $meta_id, $object_id, $meta_key, $_meta_value ){
		 $this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		 if( !in_array( $meta_key, $this->metaFieldsSkipped) && !in_array( $meta_key, $this->siqMetaFieldsSkipped) && $this->lock_meta_update == false){
			$document_id 		= !empty($object_id) ? $object_id : '' ;
			$document_data 	= !empty($document_id) ? get_post($document_id) : '';
			$excludeFields = (is_array($this->excludeCustomFields) && array_key_exists($document_data->post_type, $this->excludeCustomFields) && is_array($this->excludeCustomFields[$document_data->post_type])) ? $this->excludeCustomFields[$document_data->post_type] : array();
			if( $this->validate_post_for_siq( $document_data ) && !in_array($meta_key, $excludeFields ) ){
				$this->lock_meta_update = true;
				$this->siq_update_post($document_id,  $document_data);
			}
		 }
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
	}
	
	public function siq_update_metadata( $meta_id, $object_id, $meta_key, $_meta_value ){
		 $this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		 if( !in_array( $meta_key, $this->metaFieldsSkipped) && !in_array( $meta_key, $this->siqMetaFieldsSkipped) && $this->lock_meta_update == false){
			$document_id 		= !empty($object_id) ? $object_id : '' ;
			$document_data 	= !empty($document_id) ? get_post($document_id) : '';
			$excludeFields = (is_array($this->excludeCustomFields) && array_key_exists($document_data->post_type, $this->excludeCustomFields) && is_array($this->excludeCustomFields[$document_data->post_type])) ? $this->excludeCustomFields[$document_data->post_type] : array();
			if( $this->validate_post_for_siq( $document_data ) && !in_array($meta_key, $excludeFields ) ){
				$this->lock_meta_update = true;
				$this->siq_update_post($document_id,  $document_data);
			}
		}
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
	}

	public static function clearLink($link, $replaceWithPub = false){
		$search     = array('api.');
		$replace    = ($replaceWithPub == true) ? array('pub.') : array('');
		return str_replace($search, $replace, $link);
	}

	public function createLabels(){
		$this->labels		= array(
			"verificationCode" 	=> "API Key:",
			"engineName"		=> "Search Engine Name:",
			"postsIndexed"		=> "Number of posts indexed:"
		);
	}

	function errorHandler($errno, $errstr, $errfile, $errline){
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting
			return;
		}
		$type = $this->FriendlyErrorType($errno);
		$this->errorMessage .= "Error: [".$type."] $errstr in $errfile on line number $errline\n";
		/* Don't execute PHP internal error handler */
		return true;
	}

	public function handleErrors() {
		$error = error_get_last();

		# Checking if last error is a fatal error
		if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR)){
			# Here we handle the error, displaying HTML, logging, ...
			$type = $this->FriendlyErrorType($error['type']);
			$this->errorMessage .= "Error: [".$type."] ".$error['message']." in ".$error['file']." on line number ".$error['line'];
			$result["success"] = false;
			$result["message"] = $this->errorMessage;
			header('content-type: application/json');
			$this->log_error("SIQ AJAX ERROR:script shut down: result('".json_encode($result)."');","error");
			$response = $result;
			echo json_encode($response);
			die();
		}else if($error['type'] != ""){
			$type = $this->FriendlyErrorType($error['type']);
			$this->errorMessage .= "Error: [".$type."] ".$error['message']." in ".$error['file']." on line number ".$error['line'];
		}
	}

	public function FriendlyErrorType($type){
		switch($type)
		{
			case E_ERROR: // 1 //
				return 'E_ERROR';
			case E_WARNING: // 2 //
				return 'E_WARNING';
			case E_PARSE: // 4 //
				return 'E_PARSE';
			case E_NOTICE: // 8 //
				return 'E_NOTICE';
			case E_CORE_ERROR: // 16 //
				return 'E_CORE_ERROR';
			case E_CORE_WARNING: // 32 //
				return 'E_CORE_WARNING';
			case E_COMPILE_ERROR: // 64 //
				return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING: // 128 //
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR: // 256 //
				return 'E_USER_ERROR';
			case E_USER_WARNING: // 512 //
				return 'E_USER_WARNING';
			case E_USER_NOTICE: // 1024 //
				return 'E_USER_NOTICE';
			case E_STRICT: // 2048 //
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR: // 4096 //
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED: // 8192 //
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED: // 16384 //
				return 'E_USER_DEPRECATED';
		}
		return "";
	}

	public function getSIQPluginSettings(){

		$theme_name = wp_get_theme();

		$settingsJson = array();
		$settingsJson["theme_name"] = esc_html($theme_name);
		$settingsJson["wordpress_version"] = get_bloginfo('version');
		$settingsJson["php_version"] = phpversion();
		$settingsJson["siq_plugin_version"] = SIQ_PLUGIN_VERSION;

		foreach($this->pluginSettings as $k => $v){
			if(strcmp($k,'auth_code') != 0 && strcmp($k,'engine_code') != 0)
			{
				if((strcmp($k,"custom_search_page_style") == 0 || strcmp($k, "autocomplete_style") == 0) && !empty($v))
				{
					$css = "";
					foreach($v as $key => $value){
						$css .= $key.':'.$value.'|';
					}
					$settingsJson[$k] = $css;

				}
				else
				{
					$settingsJson[$k] = $v;
				}

			}

		}

		echo json_encode($settingsJson);
		exit;
	}

	public function checkSyncStatus(){
		$option = get_option($this->serverDownKey, 0);
		return $option;
	}

	public function addCssToFooter(){
		$stylingVar	= $this->pluginSettings['custom_search_page_style'];
		$style = $this->getStyling($stylingVar);
		if($style['customCss'] !=""){
			echo "<style type='text/css'>";
			echo $this->processStyling(stripslashes($style['customCss']));
			echo "</style>";
		}
	}

	public function getStyling($styling = array()){
		$stlingVar = array();
		foreach($this->styling as $k => $v){
			if(isset($styling[$k]) && $styling[$k] != ""){
				$stlingVar[$k] = stripslashes($styling[$k]);
			}else{
				$stlingVar[$k] = stripslashes($v['default']);
			}
		}
		return $stlingVar;
	}

	public function getAutocompleteStyling($styling = array()){
		$stlingVar = array();
		foreach($this->autocompleteStyling as $k => $v){
			if(isset($styling[$k]) && $styling[$k] != ""){
				$stlingVar[$k] = $styling[$k];
			}else{
				$stlingVar[$k] = $v['default'];
			}
		}
		return $stlingVar;
	}

	public function getMobileStyling($styling = array()){
		$stlingVar = array();
		foreach($this->mobileStyling as $k => $v){
			if(isset($styling[$k]) && $styling[$k] != ""){
				$stlingVar[$k] = $styling[$k];
			}else{
				$stlingVar[$k] = $v['default'];
			}
		}
		return $stlingVar;
	}


	public function getFontSizeBox($name, $val, $start = 10, $end = 30){
		$strFontBox = "";
		$strFontBox .= "<select name='".$name."' id='".$name."' class='fontBox' >";
		$strFontBox .= "<option  value=''>-- Select --</option>";
		for ($v = $start; $v <= $end; ++$v) {
			$selectedOpt = ($v == $val) ? "selected='selected' ": "" ;
			$strFontBox .= "<option ".$selectedOpt." value='".$v."'>".$v."px</option>";
		}
		$strFontBox .= "</select>";
		return $strFontBox;
	}

	public function siq_activate() {
		$this->sendActivationInfo();
		$notices = get_option('_siq_admin_notices', array());
		$indexedPosts = get_option('_siq_indexed_posts');
		if($indexedPosts != "" && $indexedPosts != 0 && $indexedPosts != false){
		}else{
			$msg = siq_plugin::admin_notice_messages();
			if(!$this->checkNotices($notices, "been activated")){
				$notices[]= $msg;
			}
		}
		update_option('_siq_admin_notices', $notices);
		$this->createTables();
		$this->createCustomSearchPageIfNotExists();
	}

	public function checkNotices($notices, $word){
		if(count($notices) > 0){
			foreach($notices as $k=>$v){
				if(strpos($v, $word) !== false){
					return true;
				}
			}
		}
		return false;
	}

	public function createTables(){
		global $wpdb;
		$table_name = siq_plugin::syncTableName();
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
				`post_id` INT NOT NULL,
				`sync_time` DATETIME NULL,
				UNIQUE INDEX `post_id_UNIQUE` (`post_id`)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		$this->alterTable();
	}
	
	public function alterTable(){
		global $wpdb;
		$table_name = siq_plugin::syncTableName();
		$cols_sql = "DESCRIBE $table_name";
		$all_objects = $wpdb->get_results( $cols_sql );
		$flagField = 0;
		if(empty($wpdb->last_error)){
			foreach ( $all_objects as $object ) {
			  if($object->Field == $this->syncTableFieldToBeRemoved){
				  $flagField = 1;
				  break;
			  }
			}
			if($flagField == 1){
				$sql = "ALTER TABLE `".$table_name."` CHANGE `".$this->syncTableFieldToBeRemoved."` `sync_time` DATETIME NULL";
				$wpdb->query($sql);
			}
		}
	}

	public static function admin_reindex_messages(){
		return "<b>SearchIQ:</b> It is recommended to <a href='".SIQ_ADMIN_URL."'><b>synchronize your data</b></a>, in order to avoid conflicts.";
	}

	public static function admin_notice_messages(){
		return "<b>SearchIQ:</b> Your plugin has been activated. You need to go to <a target='_blank' href='".self::clearLink(SIQ_SERVER_BASE.SIQ_SERVER_SUB_FOLDER.self::$frontPanelLink, true)."'><b>this link</b></a> to get your Verification/Authentication key if you have not configured the plugin yet.";
	}

	public function siq_admin_init() {
		global $siqAPIClient;
		$current_version = SIQ_PLUGIN_VERSION;
		$version = get_option('_siq_plugin_version');
		if ($version != $current_version && $version != "") {
			// Do whatever upgrades needed here.
			update_option('_siq_plugin_version', $current_version);
			$this->alterTable();
		}else if($version == ""){
			update_option('_siq_plugin_version', $current_version);
		}
		$api_key = $siqAPIClient->siq_get_api_key();
		if ($version != $current_version && $api_key != "" && $api_key != null && $this->pluginSettings['engine_code'] != "" && $this->pluginSettings['engine_code'] != null) {
			$this->_siq_sync_settings();
		}
	}

	function siq_admin_notices() {
		$notices = get_option('_siq_admin_notices', array());
		$checkSync	= $this->checkSyncStatus();
		if($checkSync){
			$notices[] = $this->admin_reindex_messages();
		}
		if (count($notices) > 0) {
			foreach ($notices as $notice) {
				echo "<div class='update-nag siq-notices'>$notice</div>";
			}
			delete_option('_siq_admin_notices');
		}
	}


	public function siq_admin_notice_for_searchbox(){
		if(isset($_GET['showIconNotice']) && $_GET['showIconNotice'] == 0){
			update_option('_siq_hide_icon_notice', 1);
		}
		$getNoticeStatus = get_option('_siq_hide_icon_notice', 0);
		if($getNoticeStatus == 0) {
			$notice = "<b>SearchIQ:</b> If your site doesn’t have a search bar, then add a SearchIQ search box from widget or add search icon in menu from SearchIQ options tab ";
			echo "<div class='update-nag siq-notices siq-notice-icon'>$notice
<a href='" . get_admin_url() . "admin.php?page=dwsearch&showIconNotice=0' class='disableAdminNotice siqNoticeDismiss'>Dismiss</a></div>";
		}
	}
	
	public function siq_admin_notice_for_review(){
		if(!empty($this->pluginSettings['engine_code'])){
			$currTime = current_time('timestamp', 1);
			$arrayReviewNotice = array("time" => $currTime, "show"=>1, "dismiss"=>0);
			if(isset($_GET['showReviewNotice']) && $_GET['showReviewNotice'] == 0){
				$arrayReviewNotice['time'] 		= $currTime+86400*30;
				$arrayReviewNotice['show'] 	= 0;
				$arrayReviewNotice['dismiss'] = 0;
				update_option('_siq_hide_review_notice', $arrayReviewNotice);
			}
			if(isset($_GET['reviewNoticeDismiss']) && $_GET['reviewNoticeDismiss'] == 1){
				$arrayReviewNotice['time'] 		= $currTime+86400*30;
				$arrayReviewNotice['show'] 	= 0;
				$arrayReviewNotice['dismiss'] = 1;
				update_option('_siq_hide_review_notice', $arrayReviewNotice);
			}
			$getNoticeStatus = get_option('_siq_hide_review_notice', $arrayReviewNotice);
			if(!is_array($getNoticeStatus) || ( is_array($getNoticeStatus)  && ($getNoticeStatus["show"] == 1 || ($getNoticeStatus['show'] ==0 && $getNoticeStatus['time'] <= $currTime))  && $getNoticeStatus["dismiss"] == 0))  {
				$notice = "<div class='notice-area'><b>SearchIQ</b> 
											<div class='notice-text'>
												<h3>We need your appreciation and support! Would you like to appreciate our work? </h3>
												<p>SearchIQ authors need your help and support to make SearchIQ reach out to the WordPress community.</p>
												<p>Providing a rating and review about SearchIQ to the community can help us keep up the work and achieve many more milestones in WordPress search.</p>
												<p>We are constantly improving ourselves to help the WordPress community have the best search experience. Your review and rating will make us reach that extra mile.</p>
												<p><a href='https://wordpress.org/support/plugin/searchiq/reviews/#new-post' class='button button-primary' target='_blank'>Please  give us a 5&#10030; rating and review on wordpress.org</a></p>
												<p>If you are experiencing any difficulty please <a href='https://wordpress.org/support/plugin/searchiq/#new-post' target='_blank'>Leave us a support ticket</a> and we'll make sure to respond a.s.a.p.</p>
												<p class='already-done'><a href='" . get_admin_url() . "admin.php?page=dwsearch&reviewNoticeDismiss=1'>I have already added a review for SearchIQ</a></p>
												
											</div>
										</div>";
				echo "<div class='update-nag siq-notices siq-notice-icon siq-review-notice'>
					<div class='notice-icon'>
			<span class='dashicons dashicons-heart'></span>&nbsp;
		</div>
		$notice
	<a href='" . get_admin_url() . "admin.php?page=dwsearch&showReviewNotice=0' class='disableAdminNotice siqNoticeDismiss'>Dismiss</a></div>";
			}
		}
	}
	
	public function siq_admin_notice_for_plus_package(){
		if(!empty($this->pluginSettings['engine_code'])){
			$currTime = current_time('timestamp', 1);
			$arrayReviewNotice = array("time" => $currTime, "show"=>1, "dismiss"=>0);
			if(isset($_GET['showPlusNotice']) && $_GET['showPlusNotice'] == 0){
				$arrayReviewNotice['time'] 		= $currTime+86400*30;
				$arrayReviewNotice['show'] 	= 0;
				$arrayReviewNotice['dismiss'] = 0;
				update_option('_siq_hide_plus_notice', $arrayReviewNotice);
			}
			if(isset($_GET['plusNoticeDismiss']) && $_GET['plusNoticeDismiss'] == 1){
				$arrayReviewNotice['time'] 		=	$currTime+86400*30;
				$arrayReviewNotice['show'] 	= 0;
				$arrayReviewNotice['dismiss'] = 1;
				update_option('_siq_hide_plus_notice', $arrayReviewNotice);
			}
			$getNoticeStatus = get_option('_siq_hide_plus_notice', $arrayReviewNotice);
			
			if(!is_array($getNoticeStatus) || ( is_array($getNoticeStatus)  && ($getNoticeStatus["show"] == 1 || ($getNoticeStatus['show'] ==0 && $getNoticeStatus['time'] <= $currTime))  && $getNoticeStatus["dismiss"] == 0)) {
				$notice = "<div class='notice-area'><b>SearchIQ<span class='plus'><span>Plus</span></span> is available</b>
											<div class='notice-text'>
												<h3>All because for your love and support for SearchIQ we have launched a much advanced version <span class='plus'>SearchIQ<span>Plus</span></span></h3>
												<p><a href='".$this->loginPageLink."?upgradeEngine=".$this->pluginSettings['engine_code']."' class='button button-primary' target='_blank'>Click here to upgrade to <span class='plus'>SearchIQ<span>Plus</span></span></a></p>
												<p class='already-done'><a href='" . get_admin_url() . "admin.php?page=dwsearch&plusNoticeDismiss=1'>I have already upgraded to <span class='plus'>SearchIQ<span>Plus</span></span></a></p>
												
											</div>
										</div>";
				echo "<div class='update-nag siq-notices siq-notice-icon siq-plus-notice'>
									$notice
								<a href='" . get_admin_url() . "admin.php?page=dwsearch&showPlusNotice=0' class='disableAdminNotice siqNoticeDismiss'>Dismiss</a></div>";
			}
		}
	}
	
	public function add_body_class($classes){
		global $post;
		$postID = is_numeric( $this->pluginSettings['custom_search_page'] ) ? $this->pluginSettings['custom_search_page']  : url_to_postid($this->pluginSettings['custom_search_page']);
		if($this->is_custom_search_page_set() && !empty($post) && ! empty( $postID ) && $postID == $post->ID ){
			$classes[] = "siq_search_page";
		}
		return $classes;
	}
	public function checkIfServerWorking(){
		$searchJs = @get_headers("http:" . $this->customSearchPageJs);
		if($searchJs != "" && count($searchJs) > 0 && strpos($searchJs[0], '200') !== FALSE){
			return true;
		}
		return false;
	}

	public function check_if_search(){
        global $post;
        $searchBoxName = $this->getSearchboxName();
        $searchQuery = '';
        $searchQueryVal = get_search_query();
        if ( ! empty( $searchQueryVal ) ) {
            $searchQuery = rawurlencode( wp_unslash( $searchQueryVal ) );
        } else if ( isset( $_GET[ $searchBoxName ] ) ) {
            $searchQuery = rawurlencode( sanitize_text_field( wp_unslash( $_GET[ $searchBoxName ] ) ) );
        }
		$postID = is_numeric( $this->pluginSettings['custom_search_page'] ) ? $this->pluginSettings['custom_search_page']  : url_to_postid($this->pluginSettings['custom_search_page']);
		if ( ( is_search() || isset( $_GET[ $searchBoxName ] ) && ( is_home() || ( ! empty( $post ) && ! empty( $postID ) && $postID == $post->ID ) ) ) && $this->is_custom_search_page_set() && ! isset( $_GET['siq_e'] ) && $searchQuery != '' && $this->pluginSettings['engine_code'] != '' ) {
            if ( $this->checkIfServerWorking() ) {
                $resultOnOff = isset( $_GET['result'] ) ? '&result=' . urlencode( sanitize_text_field( wp_unslash( $_GET['result'] ) ) ) : '';
                $redirect = $this->getSearchPageUrl($this->pluginSettings['custom_search_page']);
                if ( strpos( $redirect, '?' ) === false ) {
                    $redirect .= '?' . $this->siq_custom_get_param . '=' . $searchQuery . $resultOnOff;
                } else {
                    $redirect .= '&' . $this->siq_custom_get_param . '=' . $searchQuery . $resultOnOff;
                }
                wp_redirect( $redirect );
                exit();
            }
        } else if ( ! empty( $post ) && $post->ID == $this->pluginSettings['custom_search_page'] && isset( $_GET[ $this->siq_custom_get_param ] ) ) {
            set_query_var( 's', sanitize_text_field( wp_unslash( $_GET[ $this->siq_custom_get_param ] ) ) );
        }

    }

	public function is_searchable( $wp_query ) {
		global $siqAPIClient;
		$api_key = $siqAPIClient->siq_get_api_key();
		if ( function_exists( 'is_main_query' ) && ! $wp_query->is_main_query() ) {
			return false;
		} elseif ( is_search() && ! is_admin() && $api_key && strlen( $api_key ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	public function siq_ajax_search(){
		global $post;
		$content = "";
		$postID = is_numeric( $this->pluginSettings['custom_search_page'] ) ? $this->pluginSettings['custom_search_page']  : url_to_postid($this->pluginSettings['custom_search_page']);
		if(($this->is_custom_search_page_set() && !empty( $postID ) && $postID == $post->ID) || (isset($_GET['result']) && $_GET['result'] == 'on')){
			$this->pluginSettings['use_custom_search'] = 'yes';
			ob_start();
			include_once(SIQ_BASE_PATH.'/templates/frontend/customSearch.php');
			$data = ob_get_contents();
			ob_end_clean();
			$content = $data;

		}
		return $content;
	}

	public function getDomain(){
		$domain 	= 	get_option('siteurl');
		$find		= 	array('http://','https://');
		$replace	= 	array('','');
		$domain		=	str_replace($find, $replace, $domain);
		$this->domain	=	strtolower($domain);
		return $this->domain;
	}

	public function siq_get_posts( $wp_query ) {

		$this->search_successful = false;
		if( $this->is_searchable( $wp_query ) && !$this->is_custom_search_page_set() && !isset($_GET['siq_e'])) {
			// Get query string from 's' url parameter.
			$query_string = urlencode(stripslashes( get_search_query(  ) ));

			$params = $this->getSearchParams();

			$this->siq_search( $query_string, $params );

			$this->apply_search_result();

		}

	}
	public function removeWhere($pieces, $query){
		if( is_admin() || ! $query->is_search() ) return $pieces;

		global $wpdb;
		if(!empty($this->post_ids) && count($this->post_ids) > 0){
			$pieces['where'] = " AND   {$wpdb->posts}.ID IN(".implode(',',$this->post_ids).")";
			$pieces['orderby'] = " {$wpdb->posts}.ID ";
		}

		return $pieces;
	}

	public function apply_search_result() {
		$this->post_ids = array();
		$this->post_ids = $this->resultPostIds;
		if(!empty($this->post_ids) && count($this->post_ids) > 0){
			add_filter( 'posts_clauses' , array($this, 'removeWhere'), 10, 2 );
			set_query_var( 'paged', 1);
		}
	}

	public function getSearchParams(){
		$page 			= ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) - 1 : 0;
		$documentTypes 	= implode(",", $this->getPostTypesForIndexing());

		$params = array(  	'documentTypes'	=> $documentTypes,
			'page' 			=> $page,
			'itemsPerPage' 	=> $this->searchPostsPerCall
		);
		return $params;
	}

	public function siq_search( $query_string, $params ) {
		try {
			$response = parent::siq_search($query_string, $params);
			if($response['success']){
				if(!empty($response['records']) && count($response['records']) > 0) {
					$ids						= array();
					foreach ($response['records'] as $k => $v) {
						array_push($ids, $v['externalId']);
					}
					$this->results 				= true;
					$this->resultPostIds		= $ids;
					$this->search_successful 	= true;
					$this->currentPage			= $response['currentPage'];
					$this->recordsPerPage 		= $response['recordsPerPage'];
					$this->numPages 			= $response['numPages'];
					$this->totalResults			= $response['totalResults'];
				}
			}
		} catch( Exception $e ) {
			$this->results = NULL;
			$this->search_successful = false;
			$this->log_error("SIQ ERROR: ".__FILE__.":".__FUNCTION__." error('".json_encode($e->getMessage())."');","error");
		}
	}

	public function get_search_result_posts( $posts ) {
		global $siqAPIClient;
		$api_key = $siqAPIClient->siq_get_api_key();
		if( ! is_search() || ! $api_key || strlen( $api_key ) == 0 ) {
			return $posts;
		}
		if( ! $this->search_successful ) {
			return $posts;
		}
		global $wp_query, $wpdb;

		$wp_query->max_num_pages = $this->numPages;
		$wp_query->found_posts = $this->totalResults;

		$lookup_table = array();
		foreach( $posts as $post ) {
			$lookup_table[ $post->ID ] = $post;
		}

		$ordered_posts = array();
		if(!empty($this->post_ids) && count($this->post_ids) > 0) {
            foreach ($this->post_ids as $pid) {
                if (isset($lookup_table[$pid])) {
                    $ordered_posts[] = $lookup_table[$pid];
                }
            }
            set_query_var('paged', $this->currentPage + 1);
            if (count($ordered_posts) > 0) {
                return $ordered_posts;
            }
        }
		return $posts;
	}
	private function getScriptBaseUrl(){
			$baseUrl  = SIQ_SCRIPT_BASE;
			$find		 = array("api.");
			$replace	 = array("pub.");
			return str_replace($find, $replace, $baseUrl);
		}

	public function includeContainerScript($preview = false){
		$baseUrl = $this->getScriptBaseUrl();
		if (!isset($this->pluginSettings['engine_code']) || trim($this->pluginSettings['engine_code']) == FALSE) return;
		$engineKey = $this->pluginSettings['engine_code'];
		$forceLoadSettings = !empty($this->pluginSettings['siq_forceLoadSettings']) ? ", forceLoadSettings: true": "";
		if (!$preview) {
			$output = <<<EOF
                    <script type="text/javascript">
                        (function () {
                            window.siqConfig = {
                                engineKey: "$engineKey"$forceLoadSettings
                            };
                            window.siqConfig.baseUrl = "$baseUrl";
                            var script = document.createElement("SCRIPT");
                            script.src = window.siqConfig.baseUrl + 'js/container/siq-container-2.js?cb=' + (Math.floor(Math.random()*999999)) + '&engineKey=' + siqConfig.engineKey;
                            script.id = "siq-container";
                            document.getElementsByTagName("HEAD")[0].appendChild(script);
                        })();
                    </script>
EOF;
		} else {
			$output = <<<EOF
                    <script type="text/javascript">
                        (function () {
                            window.siqConfig = {
                                engineKey: "$engineKey"$forceLoadSettings
                            };
                            window.siqConfig.baseUrl = "$baseUrl";
                            window.siqConfig.preview = true;
                            var script = document.createElement("SCRIPT");
                            script.src = window.siqConfig.baseUrl + 'js/container/siq-container-2.js?cb=' + (Math.floor(Math.random()*999999)) + '&engineKey=' + siqConfig.engineKey;
                            script.id = "siq-container";
                            document.getElementsByTagName("HEAD")[0].appendChild(script);
                        })();
                    </script>
EOF;
		}
		echo $output;
	}

	public function createAdminMenu(){
		add_menu_page(__('SearchIQ', 'dwsearch'),__('SearchIQ', 'dwsearch'),'manage_options', 'dwsearch', array($this, "config"), $this->pluginIconUrl);
	}

	public function addScriptsAndStyles($hook){
		wp_register_style( 'siq_admin_css', SIQ_BASE_URL. '/assets/'.SIQ_PLUGIN_VERSION.'/css/admin-style.css', false, time());
		wp_enqueue_style('siq_admin_css');
		if(strpos($hook, 'dwsearch') !== FALSE){
			wp_register_script( 'siq_admin_js', SIQ_BASE_URL. '/assets/'.SIQ_PLUGIN_VERSION.'/js/admin-script.js', array('jquery'), time());
			wp_enqueue_script('siq_admin_js');

			wp_register_script( 'siq_color_js', SIQ_BASE_URL. '/assets/'.SIQ_PLUGIN_VERSION.'/jscolor/jscolor.js', array('jquery'), time());
			wp_enqueue_script('siq_color_js');
			add_filter('upload_mimes', array($this, 'siq_myme_types'), 1, 1);
			wp_enqueue_media();
		}

	}
	public function siq_myme_types($mime_types){
		$mime_types = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'bmp' => 'image/bmp',
			'tif|tiff' => 'image/tiff'
		);
		return $mime_types;
	}

	public function addScriptsAndStylesOnFront(){
		if(!is_admin() && (!empty($this->pluginSettings['siq_menu_select_box']) || is_active_widget( false, false, 'siq_search_widget', true ) )){
			wp_register_style( 'siq_icon_css', SIQ_BASE_URL. '/assets/'.SIQ_PLUGIN_VERSION.'/css/frontend/icon-moon.css', false, time());
			wp_register_style( 'siq_front_css', SIQ_BASE_URL. '/assets/'.SIQ_PLUGIN_VERSION.'/css/frontend/stylesheet.css', false, time());
			
			wp_enqueue_style('siq_icon_css');
			wp_enqueue_style('siq_front_css');
			
			if(!wp_script_is('jquery','enqueued')){
				wp_enqueue_script('jquery');
			}
		}
	}

	public function getPageList(){
		$customSearchPage = $this->createCustomSearchPageIfNotExists();
		$getPages= get_pages();
		return $getPages;
	}

	public function optionsPage(){
		include_once(SIQ_BASE_PATH.'/templates/backend/optionsPage.php');
	}

	public function config(){
		include_once(SIQ_BASE_PATH.'/templates/backend/tabbed.php');
	}

	public function returnOnDie($message){
		if($this->resultSent == 0){
			$result["success"] = false;
			$result["message"] = $message;
			$result["errors"]  = $this->errorMessage;
			$this->log_error("SIQ AJAX ERROR:script execution terminated: ".__FILE__.":".__FUNCTION__." error('".json_encode($result)."');","error");
			header('content-type: application/json');
			$response = $result;
			echo json_encode($response);
			exit;
		}
	}

	public function dieHandler($param){
		die();
	}

	public function checkIfInAdmin(){
		if( (is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX) || (strpos(strtolower($_SERVER[HTTP_REFERER]), strtolower(site_url())) !== FALSE && strpos(strtolower($_SERVER[HTTP_REFERER]), "wp-admin") !== FALSE)){
			return true;
		}
		return false;
	}
	
	private function checkSecurity($nonce, $bypass = 0){
		if($bypass == 1){
			return true;
		}
		if(!$this->checkIfInAdmin()){
			register_shutdown_function( array($this, 'returnOnDie'), 'Invalid nonce on frontend.');
			if( check_ajax_referer($this->siteNonceString, 'security'  ) ){
				return true;
			}
		}else if(current_user_can("activate_plugins") ){
			register_shutdown_function( array($this, 'returnOnDie'), 'Invalid nonce on admin.');
			if(check_ajax_referer( $this->adminNonceString, 'security'  )) {
				return true;
			}
		}
		return false;
	}

	public function siq_admin_ajax(){
		error_reporting(0);
		register_shutdown_function( array($this, 'handleErrors'));
		set_error_handler(array($this, "errorHandler"));
		if(!empty($_POST)){
			$task = $_POST['task'];
			$nonce = $_POST['security'];
			$bypassNonce = (isset($_POST['nononce']) && $_POST['nononce'] == 1) ? 1 : 0;
			add_filter('wp_die_ajax_handler', array($this, 'dieHandler'));
			if($this->checkSecurity($nonce, $bypassNonce)){
				$post = $this->sanitizeVariables($_POST);
				$this->log_error("SIQ AJAX CALL STARTED: params('".json_encode($post)."');");
				switch($task){
					
					case 'verify_subscription':
						$result = $this->verify_subscription($post);
						break;

					case 'submit_engine':
						$result = $this->submit_engine($post);
						break;

					case 'submit_for_indexing':
						$result = $this->index_posts($post);
						break;

					case 'clear_configuration':
						$result = $this->clear_configuration($post);
						break;

					case 'get_page_list':
						$result = $this->getPageListAjax($post);
						break;

					case 'reset_style':
						$result = $this->resetCustomStyle();
						break;

					case 'reset_autocomplete_style':
						$result = $this->resetAutocompleteStyle();
						break;

					case 'reset_mobile_style':
						$result = $this->resetMobileStyle();
						break;

					case 'change_autocomplete_image_status':
					case 'change_custom_search_image_status':
						$result = $this->setAutoCompleteStatus($post);
						break;

					case 'check_graphic_editor_status':
						$result = array('success' => true, 'graphicEditorEnabled' => $this->checkGraphicEditor());
						break;

					case 'generate_post_thumbs':
						$result = $this->generatePostThumbnails($post);
						break;

					case 'set_custom_search_page':
						$result = $this->setCustomSearchPage($post);
						break;

					case "set_enable_autocomplete":
						$result = $this->setEnableAutocomplete($post);
						break;

					case "set_searchbox_name":
						$result = $this->setSearchboxName($post);
						break;

					case "set_search_query_param_name":
						$result = $this->setSearchQueyParamName($post);
						break;

					case "set_post_types_for_search":
						$result = $this->setPostTypesForSearch($post);
						break;

					case "set_custom_style":
						$result = $this->setCustomStyle($post);
						break;

					case "set_image_custom_field":
						$result = $this->setImageCustomField($post);
						break;

					case "set_mobile_enabled":
						$this->setMobileEnabled($post['mobile_enabled'] === "1");
						$this->_siq_sync_settings();
						$result = array('success'=>true, 'message'=>'Mobile settings changed.');
						break;

					case "set_search_algorithm":
						$result = $this->ajaxSetSearchAlgorithm($post['search_algorithm']);
						break;

					case "save_sync_settings":
							$result = $this->save_and_sync_settings($post);
						break;

					case "get_all_document_fields_option_list_for_facet":
                        $postType = sanitize_text_field($_POST['postType']);
                        $defaultPostTypes = array("post", "page", "_siq_all_posts");
                        if (!empty($postType) && !in_array($postType, $defaultPostTypes)) {
                            $tmp = $this->getAllCustomFields(array($postType));
                            $tmp = array_merge($tmp[$postType]["regular_fields"], $tmp[$postType]['system_fields']);
                            $tmp = array_map(function ($val) {
                                return $this->customFieldPrefix . $val;
                            }, $tmp);
                            $documentTypeFields = array_merge($tmp, array_map(function ($val) {
                                return $this->customTaxonomyPrefix . $val;
                            }, $this->getPostTypeTaxonomies($postType)));
                            $result = array(
                                "success" => 1,
                                "html" => $this->buildFacetFieldOptionList($postType, $this->getDocumentFieldMapping($postType, $documentTypeFields))
                            );
                        } else {
                            $result = array(
                                "success" => 1,
                                "html" => $this->buildFacetFieldOptionList($postType, null)
                            );
                        }

						break;
					case "delta_sync_posts":
							$result = $this->delta_sync_posts($post);
						break;
					default:
						$result = array('success'=>0, 'message'=>'Parameter missing, please try again.');

				}
			}else{
				$result = array('success'=>0, 'message'=>'Insecure request, please try again.');
			}
		}else{
			$result = array('success'=>0, 'message'=>'Invalid request, please try again.');
		}

		header('content-type: application/json');
		$result['errors'] = $this->errorMessage;
		$response = $result;
		$this->log_error("SIQ AJAX CALL COMPLETE: response('".json_encode($response)."')"."\n");
		$this->resultSent = 1;
		echo json_encode($response);
		exit;
	}

	public function ajaxSetSearchAlgorithm($searchAlgorithm) {
		$this->setSearchAlgorithm($searchAlgorithm);
		$settings = $this->getPluginSettings();
		if ($settings['search_algorithm'] == $searchAlgorithm) {
			return array('success'=>true, 'message'=>'Search algorithm saved');
		} else {
			return array('success'=>false, 'message'=>'Search algorithm has not been saved');
		}
	}

	public function setCustomStyle($post){
		$settings 					= $this->getPluginSettings();
		$stylingVar					= $settings['custom_search_page_style'];
		$styling					= $this->getStyling($stylingVar);

		$styling['customCss']       = $post["customCss"];
		if($styling){
			update_option($this->pluginOptions['custom_search_page_style'], $styling);
		}
		$result["success"]     = 1;
		$result["message"]     = "Style saved successfully";
		$result["css"]         = $post;
		$this->_siq_sync_settings();
		return $result;
	}

	public function setAutoCompleteStatus($post){
		$show = (int)$post['show'];
		$showHide = ($show == 1) ? "show": "hide";
		if($post['task'] == 'change_custom_search_image_status'){
			$optionName = "show_search_page_images";
		}else if($post['task'] == 'change_autocomplete_image_status'){
			$optionName = "show_autocomplete_images";
		}
		if($show){
			if (!$this->checkGraphicEditor()) {
				delete_option($this->pluginOptions['change_custom_search_image_status']);
				delete_option($this->pluginOptions['change_autocomplete_image_status']);
				update_option($this->pluginOptions['graphic_editor_error'], '1');
				return array("success" => false, "message" => "Couldn't generate thumbnails because of no any installed graphic libraries for php.");
			}
			delete_option($this->pluginOptions['graphic_editor_error']);
			update_option($this->pluginOptions[$optionName], "yes");
		}else{
			delete_option($this->pluginOptions[$optionName]);
		}
		$result = array('success'=> 1, 'message'=>'Image option set to '.$showHide, 'show'=> $show);
		if($show){
			$result['started'] = 1;
		}
		$this->_siq_sync_settings();
		return $result;
	}

	public function setEnableAutocomplete($post) {
		if ($post['value'] == "yes") {
			delete_option($this->pluginOptions['disable_autocomplete']);
		} else {
			update_option($this->pluginOptions['disable_autocomplete'], 'yes');
		}
		$this->_siq_sync_settings();
		return array("success" => 1);
	}

	public function generatePostThumbnails($post){
		if (!$this->checkGraphicEditor()) {
			delete_option($this->pluginOptions['change_custom_search_image_status']);
			delete_option($this->pluginOptions['change_autocomplete_image_status']);
			update_option($this->pluginOptions['graphic_editor_error'], '1');
			return array("success" => false, "message" => "Couldn't generate thumbnails because of no any installed graphic libraries for php.");
		}
		delete_option($this->pluginOptions['graphic_editor_error']);
		return $this->generate_thumbnails($post);
	}

	public function resetCustomStyle(){
		if (!empty($this->pluginSettings['custom_search_page_style']['customCss'])) {
			$customCss = $this->pluginSettings['custom_search_page_style']['customCss'];
			$this->pluginSettings['custom_search_page_style'] = array();
			$this->pluginSettings['custom_search_page_style']['customCss'] = $customCss;
			update_option($this->pluginOptions['custom_search_page_style'], $this->pluginSettings['custom_search_page_style']);
		} else {
			delete_option($this->pluginOptions['custom_search_page_style']);
		}
        delete_option($this->pluginOptions['customSearchResultsInfoText']);
        delete_option($this->pluginOptions['customSearchResultsOrderRelevanceText']);
        delete_option($this->pluginOptions['customSearchResultsOrderNewestText']);
        delete_option($this->pluginOptions['customSearchResultsOrderOldestText']);
        delete_option($this->pluginOptions['noRecordsFoundText']);
        delete_option($this->pluginOptions['paginationPrevText']);
        delete_option($this->pluginOptions['paginationNextText']);
        delete_option($this->pluginOptions['custom_page_display_category']);
        delete_option($this->pluginOptions['custom_page_display_tag']);
        delete_option($this->pluginOptions['custom_page_display_author']);
        delete_option($this->pluginOptions['siq_use_meta_desc']);
        delete_option($this->pluginOptions['resultPageLayout']);
        delete_option($this->pluginOptions['siq_resultPageShowPostLink']);
        delete_option($this->pluginOptions['siq_resultPageCustomSearchItemLinkColor']);
        delete_option($this->pluginOptions['siq_resultPageCustomSearchItemLinkFontSize']);
        update_option($this->pluginOptions['show_search_page_images'], 'yes');
		$this->setCustomSearchNumRecords(10);
		delete_option($this->pluginOptions['custom_search_bar_placeholder']);
		$this->_siq_sync_settings();
		$result['success'] = 1;
		$result['message'] = 'Style Reset Successfully';
		return $result;
	}

	public function resetAutocompleteStyle(){
		delete_option($this->pluginOptions['autocomplete_style']);
		update_option($this->pluginOptions['autocomplete_text_results'], $this->defaultAutocompleteTextResults);
		update_option($this->pluginOptions['autocomplete_text_poweredBy'], $this->defaultAutocompleteTextPoweredBy);
		update_option($this->pluginOptions['autocomplete_text_moreLink'], $this->defaultAutocompleteTextMoreLink);
		update_option($this->pluginOptions['autocomplete_num_records'], $this->autocompleteDefaultNumRecords);
		update_option($this->pluginOptions['show_autocomplete_images'], 'yes');
		$this->_siq_sync_settings();
		$result['success'] = 1;
		$result['message'] = 'Style Reset Successfully';
		return $result;
	}

	public function resetMobileStyle(){
		delete_option($this->pluginOptions['mobile_style']);
		$this->setMobileEnabled(parent::DEFAULT_MOBILE_ENABLED);
		$this->setMobileIconEnabled(parent::DEFAULT_MOBILE_ICON_ENABLED);
		$this->setMobileFloatBarEnabled(parent::DEFAULT_MOBILE_FLOAT_BAR_ENABLED);
		$this->_siq_sync_settings();
		$result['success'] = 1;
		$result['message'] = 'Style Reset Successfully';
		return $result;
	}

	public function createCustomAjaxSearchPage(){
		global $wpdb;
		$newPost = "";
		$query = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'page' AND post_content LIKE '%[siq_ajax_search]%' AND post_status = 'publish' ORDER BY ID DESC LIMIT 0, 1";
		$sPage = $wpdb->get_row($query);
		if(!is_null($sPage) && $sPage !="" && (int)$sPage->ID !=0){
			$newPost 		= $sPage->ID;
		}else{
			$postArray		= array("post_title"=>"Search", "post_type" => "page", "post_status" => 'publish', 'post_content'=>'[siq_ajax_search]');
			$newPost 		= wp_insert_post($postArray);
		}
		$url = ((int)$newPost !=0) ? get_permalink($newPost): $newPost;
		return $url;
	}

	public function createCustomSearchPageIfNotExists() {
		$customSearchPage 	= $this->pluginSettings['custom_search_page'];
		$customSearchPageOld = $this->pluginSettings['custom_search_page_old'];
		if($customSearchPage == "" && $customSearchPageOld == ""){
			$customSearchPage 	= $this->createCustomAjaxSearchPage();
			update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
		}else if(!empty($customSearchPage)){
			$pageData = get_post(url_to_postid($customSearchPage));
			if(!is_null($pageData) && strpos($pageData->post_content, '[siq_ajax_search') !== FALSE && $pageData->post_status == 'publish'){
				update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
			}else if($customSearchPageOld != ""){
				$pageData = get_post(url_to_postid($customSearchPageOld));
				if(!is_null($pageData) && strpos($pageData->post_content, '[siq_ajax_search') !== FALSE && $pageData->post_status == 'publish'){
					$customSearchPage 	= $customSearchPageOld;
					update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
				}else{
					$customSearchPage 	= $this->createCustomAjaxSearchPage();
					update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
				}
			}else{
				$customSearchPage 	= $this->createCustomAjaxSearchPage();
				update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
			}
		}else if(!empty($customSearchPageOld)){
			$pageData = get_post(url_to_postid($customSearchPageOld));
			if(!is_null($pageData) && strpos($pageData->post_content, '[siq_ajax_search') !== FALSE && $pageData->post_status == 'publish'){
				$customSearchPage 	= $customSearchPageOld;
				update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
			}else{
				$customSearchPage 	= $this->createCustomAjaxSearchPage();
				update_option($this->pluginOptions['custom_search_page'], $customSearchPage);
			}
		}

		return $customSearchPage;
	}

	public function setCustomSearchPage($data) {
		$updated = true;
		if (!empty($data["url"]) && $this->is_valid_custom_search_page($data["url"])) {
			update_option($this->pluginOptions["custom_search_page"], $data["url"]);
			$this->pluginSettings["custom_search_page"] = $data["url"];
		} else {
			$updated = false;
		}
		$allpages = $this->getPageList();
		$selectPageList 	= '<div class="data pageList"><label>Custom Search Page</label>';
		$selectPageList 	.= '<input type="text" class="textbox large" value="'.$this->pluginSettings["custom_search_page"].'" id="siq_custom_search_page" name="siq_custom_search_page"/>';
		if (!$updated) {
			$selectPageList .= '<div class="message error">Selected page doesn\'t have [siq_ajax_search] tag</div>';
		}
		$selectPageList 	.= '<div class="message">Please make sure the selected page has [siq_ajax_search] on top of the content.</div>';
		$selectPageList 	.= '</div>';
		$this->_siq_sync_settings();
		if ($updated) {
			return array('success' => 1, 'message' => 'Page selected', 'list' => $selectPageList);
		} else {
			return array('success' => 0, 'message' => 'Selected page doesn\'t have [siq_ajax_search] tag', 'list' => $selectPageList);
		}
	}

	public function getPageListAjax($data){
		$selectPageList = "";
		if($data['show'] != 0){
			update_option($this->pluginOptions['use_custom_search'], $data['value']);

			$allpages 			= $this->getPageList();
			$selectPageList 	= '<div class="data pageList"><label>Custom Search Page</label>';
			$selectPageList 	.= '<select name="siq_custom_search_page">';
			$selectPageList 	.= '<option value="" selected="">--Select Page--</option>';
			foreach($allpages as $k => $v){
				$selected		= ($customSearchPage !="" && $customSearchPage == $v->ID )? 'selected="selected"': "";
				$selectPageList 	.= '<option '.$selected.' value="'.$v->ID.'">'.$v->post_title.'</option>';
			}
			$selectPageList 	.= '</select>';
			$selectPageList 	.= '<div class="message">Please make sure the selected page has [siq_ajax_search] on top of the content.</div>';
			$selectPageList 	.= '</div>';

		}else{
			delete_option($this->pluginOptions['use_custom_search']);
		}
		$this->_siq_sync_settings();
		$result = array('success'=> 1, 'message'=>'Page selected', 'list'=>$selectPageList, 'show' => (int)$data['show']);
		return $result;
	}

	public function delete_domain($data){
		$result = parent::delete_domain($_POST);
		if($result['success'] == true){
			foreach($this->pluginOptions as $k => $v){
				delete_option($v);
			}
		}
		return $result;
	}

	public function verify_subscription($data){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$result = array();
		$code 	= "";
		global $siqAPIClient;
		if(trim($data["api_key"]) != ""){
			$result = parent::verify_subscription($data);
			if(strtolower($result['message']) === "ok"){
				update_option($this->pluginOptions['auth_code'], $data["api_key"]);
				$code = get_option($this->pluginOptions['auth_code']);
				$siqAPIClient->siq_set_api_key($code);
				update_option($this->pluginOptions["show_search_page_images"], "yes");
				update_option($this->pluginOptions["show_autocomplete_images"], "yes");

				$result['optionsBox'] 	= $this->getConfigMainView($code);
				$this->submitEngineOnVerification = true;
				$resData                = $this->submit_engine();
				if($resData['created'] == "ok"){
					$result['message']		= "Authentication successful. Moving to next step, please wait..";
					$result['success']    = true;
					$result['engineName'] = $resData['engine'];

				}else{
					$result['success'] = $resData['success'];
					$result['message'] = (strpos(strtolower($resData['message']), "paid license required") !== FALSE) ? "You have exhausted your free search engine limit, please purchase license for your site from your SearchIQ account and then try again. To know what is included in a paid license <a href='".$this->pricingPageLink."' target='_blank'>click here</a>": $resData['message'];
				}
			}else if($result['success'] === false){
				if(strpos($result['message'], 'Access Denied') !== FALSE || strpos($result['message'], 'error processing') !== FALSE){
					$result['message'] = "API key is invalid.";
				}
			}
			if(isset($result['response']) && !empty($result['response'])){
				foreach($result['response'] as $k => $v){
					if($v['domain'] == $this->domain){
						$result['engineName'] 	= $v['name'];
						update_option($this->pluginOptions['engine_name'], $v['name']);
						update_option($this->pluginOptions['engine_code'], $v['engineKey']);
						$this->pluginSettings['engine_code'] =  $v['engineKey'];
						$result['message'] = "Authentication successful. Moving to next step, please wait..";
						$result['success'] = true;
						update_option($this->pluginOptions['use_custom_search'], "yes");
						update_option($this->pluginOptions['engine_just_created'], "1");
						break;
					}
				}
				unset($result['response']);
			}


		}else{
			$result['success'] = false;
			$result['message'] = "There is some issue with the entered key";
		}
		$this->_siq_get_sync_settings();
		$result['cropOrResize'] = $this->checkThumbnailTypeFromApi();
		$result['filters'] = $this->getFilterAndPostTypeHTML();
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return $result;

	}

	private function getTotalPosts($allowedPostTypes, $createThumb = false){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		global $wpdb;
		$excludePosts  				= (!empty($this->excludePostIds)) ? " AND p.ID NOT IN(".$this->excludePostIds.") " : "";
		$postMimeTypes 				= " OR (p.post_type = 'attachment' AND p.post_mime_type = 'application/pdf' AND p.post_parent !='') ";
		$allowedPostTypesFilter 	= (!empty($postMimeTypes)) ? " AND ( p.post_type IN(".$allowedPostTypes.") ".$postMimeTypes.") ": " AND p.post_type IN(".$allowedPostTypes.") ";
		$postStatus					= (!empty($postMimeTypes)) ? " AND p.post_status IN('publish','inherit')": " AND p.post_status = 'publish'";
		if(!$createThumb){
			if($this->syncPostIDs != ""){
				$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."posts p WHERE p.ID IN(".$this->syncPostIDs.") ".$excludePosts."";
			}else{
				$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."posts p WHERE TRIM(p.post_title) <> '' AND p.post_password = '' ".$excludePosts."  ".$allowedPostTypesFilter.$postStatus;
			}
		}else{
			$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."posts p WHERE TRIM(p.post_title) <> ''  AND p.post_password = '' ".$excludePosts." ".$allowedPostTypesFilter.$postStatus;
		}
		$total = $wpdb->get_var($query);
		$this->totalPosts = $total;
		$this->log_error("TOTAL POSTS:(".$this->totalPosts.")");
		if(!$createThumb){
			$this->totalPages = ceil($this->totalPosts / $this->postsPerCall);
		}else{
			$this->totalPages = ceil($this->totalPosts / $this->postsPerCallForThumb);
		}
		$this->log_error("TOTAL PAGES:(".$this->totalPages.")");
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
	}

	public function generate_thumbnails($data, $total = 0){
		global $wpdb;

		if(isset($data['cropOrResize']) && $data['cropOrResize'] != "" && $data['currentPage'] == 0){
			$this->saveCropResize($data['cropOrResize']);
		}
		$this->getPostTypesForIndexing();
		$allowedPostTypes = implode("','", $this->postsToIndexAndSearch);
		if($allowedPostTypes != ""){
			$allowedPostTypes = "'".$allowedPostTypes."'";
		}
		if($this->totalPosts == 0){
			
			$this->getTotalPosts($allowedPostTypes, true);
		}
		if($this->totalPosts > 0){
			if(isset($data['currentPage']) && $data['currentPage'] > 0){
				$this->currentStart = $this->postsPerCallForThumb*($data['currentPage'] - 1);
				$this->currentPage	= $data['currentPage'];
			}

			$excludePosts = (!empty($this->excludePostIds)) ? " AND p.ID NOT IN(".$this->excludePostIds.") " : "";
			$query = "SELECT p.* FROM ".$wpdb->prefix."posts p WHERE TRIM(p.post_title) <> '' AND p.post_password = '' ".$excludePosts." AND p.post_type IN(".$allowedPostTypes.") AND p.post_status = 'publish' ORDER BY p.ID ASC limit ".$this->currentStart.",".$this->postsPerCallForThumb." ";
			$db_result = $wpdb->get_results($query);
			if(!empty($db_result)){
				foreach($db_result as $k => $v){
					$result['generated'][$v->ID] = parent::generateThumbnails($v);
				}
			}
			if(isset($data['currentPage']) && $data['currentPage'] > 0){
				$this->postsFetched = (count($db_result) <= $this->postsPerCallForThumb) ? count($db_result) + $this->currentStart : $this->currentStart + $this->postsPerCallForThumb;
			}else{
				$this->postsFetched = $this->postsPerCallForThumb;
			}
			
			$result['postData']	= $this->syncPostsAfterThumbnailGeneration($db_result);
			$result['success'] 		= 1;
			$result['show']			= 1;
			$result['inProgress'] 	= 1;
			$result['fetched']		= $this->postsFetched;
			$result['percent']		= ($this->postsFetched / $this->totalPosts);
			$result['currentPage'] 	= $this->currentPage + 1;
			$result['totalPosts'] 	= $this->totalPosts;
			$result['totalPages']	= $this->totalPages;
			if($this->currentPage < $this->totalPages){
				$result['message'] 		= 'Thumbnail generation in progress';
			}else{
				$result['message'] 		= 'Thumbnail generation complete';
			}
		}else{
			$result['success'] 	= 0;
			$result['message'] 	= "No posts found";
			$result['show']		= 0;
		}
		return $result;
	}

	public function delta_sync_posts($data, $total = 0){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		global $wpdb;
		$this->lock_meta_update = true;
		$logError = false;
		$postIDs		=array();
		if(array_key_exists('fromdate', $data) && !empty($data['fromdate'])){
			$this->fromDate  = date("Y-m-d H:i:s", strtotime($data['fromdate']));
		}
		if(array_key_exists('todate', $data) && !empty($data['todate'])){
			$this->toDate =  date("Y-m-d H:i:s", strtotime($data['todate']));
		}
		if($this->totalPosts == 0 && $data['total'] == 0){
			//Please check deleted records 
			$this->totalPosts	=	parent::getSyncPostIds('u', true) ; //delta_sync_posts  GET ALL UPDATED POSTS for SYNC here	
			$this->deltaPostIDs	=	parent::getSyncPostIds('u', false, $this->postsPerCallForDeltaSync); //delta_sync_posts  GET ALL UPDATED POSTS for SYNC here	
			$this->log_error("POSTS IDS TO BE UPDATED: (".json_encode($this->deltaPostIDs).")");		
			
			$deletePostIds		= parent::getSyncPostIds('d'); //delta_sync_posts  GET ALL DELETED POSTS for SYNC here	
			$this->log_error("POSTS IDS TO BE DELETED (".json_encode($deletePostIds).")");		
			
			$postIDs						=	explode(',',$this->deltaPostIDs); // Convert comma saparated postsID string to postsIDs Array
			if(!empty($deletePostIds)){ // Check if there are posts to delete
				$deletePostIds	        = explode(",", $deletePostIds);
				$postIDs				= array_diff($postIDs, $deletePostIds);	 // Sync only those posts which are to be updated
			}
			$this->log_error("POSTS IDS ONLY FOR UPDATION: (".json_encode($postIDs).")");	 // Log only those posts which are to be updated
			if(is_array($deletePostIds) && count($deletePostIds) > 0){ //IF posts available for deletion bulk delete from SearchIQ
				$deleteBulk = parent::submit_for_bulk_deletion($deletePostIds);
				if(is_array($deleteBulk) && array_key_exists("success", $deleteBulk)){
					parent::removeFromSync($deletePostIds);
				}
			}
			$this->deltaPostIDs =	implode(",",$postIDs);
		}else{
			$this->totalPosts 	= 	$data['total'];
			$this->deltaPostIDs	=	parent::getSyncPostIds('u', false, $this->postsPerCallForDeltaSync);
		}		
		$this->log_error("DELTA POST IDS (".json_encode($this->deltaPostIDs).")");// Log delta post ID's
		if($this->totalPosts > 0){
			if(isset($data['currentPage']) && $data['currentPage'] > 0){
				$this->currentStart = $this->postsPerCallForDeltaSync*($data['currentPage'] - 1);
				$this->currentPage	= $data['currentPage'];
			}
			$excludePosts = (!empty($this->excludePostIds)) ? " AND p.ID NOT IN(".$this->excludePostIds.") " : "";
			$query = "SELECT p.*, GROUP_CONCAT(pm.meta_key SEPARATOR '<>') as meta_key, GROUP_CONCAT(IFNULL(pm.meta_value,'') SEPARATOR '<>') as meta_value FROM ".$wpdb->prefix."posts p LEFT JOIN ".$wpdb->prefix."postmeta pm ON p.ID = pm.post_id WHERE p.ID IN(".$this->deltaPostIDs.") ".$excludePosts." GROUP BY p.ID ORDER BY p.ID ASC, pm.meta_id ASC limit 0,".$this->postsPerCallForDeltaSync." ";

            $this->setGroupConcatLimit();
			$dataResult = $wpdb->get_results($query);
			
			$this->log_error("DELTA SYNC QUERY TO GET IDS: ".$query);		// Log QUERY based on current page
			
			$dataForSubmission = array();
			$idsForSubmission = array();
            $this->bulkDeletionIds = array();
			if(!empty($dataResult)){
				$count = 0;
				foreach($dataResult as $k => $v){
					$document_ 	= parent::createDocumentFromPost($v, false);
					if(is_array($document_) && count($document_) > 0){
						$dataForSubmission[$count]	=	$document_;  // Add posts to be synced in array
						$idsForSubmission[] 			= 	$dataForSubmission[$count]['externalId'];
						$count++;
					}
				}
				if($count == 0 && isset($data['currentPage']) && $data['currentPage'] > 0 && $this->currentPage==$this->totalPages){
					$result['success'] 	= 1;
					$result['message'] 	= 'Post Sync complete';
					$result['percent']		= 100;
					$result['show']		= 0;
					return $result;
				}else if($count == 0){
					$result['success'] 	= 0;
					$result['message'] 	= "No posts found for delta syncing";
					$result['show']		= 0;
					return $result;
				}
				if(isset($data['currentPage']) && $data['currentPage'] > 0){
					$this->postsFetched = (count($dataResult) <= $this->postsPerCallForDeltaSync) ? count($dataResult) + $this->currentStart : $this->currentStart + $this->postsPerCallForDeltaSync;
				}else{
					$this->postsFetched = $this->postsPerCallForDeltaSync;
				}
                if(count($this->bulkDeletionIds) > 0){
                    parent::submit_for_bulk_deletion($this->bulkDeletionIds);
                }
				$result = parent::submit_for_indexing($dataForSubmission, $this->postsPerCallForDeltaSync);  // Submit posts for bulk Sync
				$this->log_error($result);		
				if($result['success'] == true){
					parent::addToSync($idsForSubmission);  // Add posts to local DB for tracking delta
					$this->totalPages 	= ceil($this->totalPosts/$this->postsPerCallForDeltaSync);
					$result['success'] 		= 1;
					$result['show']			= 1;
					$result['inProgress'] 	= 1;
					$result['query'] = $query;
					$result['fetched']		= $this->postsFetched;
					$result['percent']		= ($this->postsFetched / $this->totalPosts);
					$result['currentPage'] 	= $this->currentPage + 1;
					$result['totalPosts'] 	= $this->totalPosts;
					$result['totalPages']	= $this->totalPages;
					$result['exclude']			= $this->excludePostIds;
					$result['deltaPostIDs'] = "";
					if($this->currentPage < $this->totalPages){
						$result['message'] 		= 'Delta Post Sync in progress';
					}else{
						$result['message'] 		= 'Delta Post Sync complete';
					}
				}else{
					$result['success'] 	= 0;
					$result['message'] 	= "Error syncing posts, please try again later";
					$result['show']		= 0;
				}
			}else{
				$result['success'] 	= 0;
				$result['message'] 	= "No posts found for delta sync";
				$result['show']		= 0;
				$result['query'] = $query;
			}			
		}else{
			$result['success'] 	= 0;
			$result['message'] 	= "No posts found for delta sync";
			$result['show']		= 0;
			$result['query'] = $query;
		}
		$this->log_error("result: (".json_encode($result).")");
		$this->lock_meta_update = false;
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return $result;
	}
	
	public function full_sync_posts($post){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$this->lock_meta_update = true;
		$settings 	= $this->getPluginSettings();
		$result = $this->submit_for_indexing($post);
		if(array_key_exists("next", $result)){
			if(!empty($result["next"])){
				$result["currentPage"] = $result["next"];
			}else{
				$result["currentPage"] = $result["next"];
			}
		}
		if(array_key_exists("progress", $result)){
			$result["percent"] = $result["progress"]*100;
		}
		$result["totalPages"] = $this->totalPages;
		$result = $this->setParamsForCron($result, $post);
		$this->log_error("FULL SYNC POSTS: result(".json_encode($result).")");
		$this->lock_meta_update = false;
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return $result;
	}
	
	public function submit_for_indexing($data, $total = 0){
		global $wpdb;
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$log = false;
		$dataOptions = $data;
        if(!empty($this->blackListUrls) && is_array($this->blackListUrls) && count($this->blackListUrls) > 0){
            $this->postsPerCall = $this->postsPerCallForBlacklist;
        }
		$this->getPostTypesForIndexing();
		$allowedPostTypes = implode("','", $this->postsToIndexAndSearch);
		if($allowedPostTypes != ""){
			$allowedPostTypes = "'".$allowedPostTypes."'";
		}
		if($this->totalPosts == 0){
			$this->getTotalPosts($allowedPostTypes);
		}
		$postMimeTypes 				= " OR (p.post_type = 'attachment' AND p.post_mime_type = 'application/pdf' AND p.post_parent !='') ";
		$allowedPostTypesFilter 	= (!empty($postMimeTypes)) ? " AND ( p.post_type IN(".$allowedPostTypes.") ".$postMimeTypes.") ": " AND p.post_type IN(".$allowedPostTypes.") ";
		$postStatus					= (!empty($postMimeTypes)) ? " AND p.post_status IN('publish','inherit')": " AND p.post_status = 'publish'";

		if($this->totalPosts > 0){
			if(isset($data['currentPage']) && $data['currentPage'] > 0){
				$this->currentStart = $this->postsPerCall*( $data['currentPage'] - 1);
				$this->currentPage	= $data['currentPage'];
				$this->postsFetched = $this->currentStart + $this->postsPerCall;
			}else{
				$this->postsFetched = 0;
			}
			if($this->postsFetched == 0 && $data['task'] != "") {
				$indexVal = (int)get_option($this->pluginOptions['index_posts']) + 1;
				update_option($this->pluginOptions['index_posts'], $indexVal);
				if(!isset($dataOptions['resetcount']) || (isset($dataOptions['resetcount']) && $dataOptions['resetcount'] == 1)) {
					update_option($this->pluginOptions['num_indexed_posts'], 0);
				}
				$this->postsFetched = $this->postsPerCall;
			}
			
			$excludePosts = (!empty($this->excludePostIds)) ? " AND p.ID NOT IN(".$this->excludePostIds.") " : "";
			if($this->syncPostIDs != ""){
				$query = "SELECT p.*, GROUP_CONCAT(pm.meta_key SEPARATOR '<>') as meta_key, GROUP_CONCAT(IFNULL(pm.meta_value,'') SEPARATOR '<>') as meta_value FROM ".$wpdb->prefix."posts p LEFT JOIN ".$wpdb->prefix."postmeta pm ON p.ID = pm.post_id WHERE p.ID IN(".$this->syncPostIDs.") ".$excludePosts." GROUP BY p.ID ORDER BY p.ID ASC, pm.meta_id ASC limit ".$this->currentStart.",".$this->postsPerCall." ";
			}else{
				$query = "SELECT p.*, GROUP_CONCAT(pm.meta_key SEPARATOR '<>') as meta_key, GROUP_CONCAT(IFNULL(pm.meta_value,'') SEPARATOR '<>') as meta_value FROM ".$wpdb->prefix."posts p LEFT JOIN ".$wpdb->prefix."postmeta pm ON p.ID = pm.post_id WHERE TRIM(p.post_title) <> '' AND p.post_password = '' ".$excludePosts." ".$allowedPostTypesFilter." ".$postStatus." GROUP BY p.ID ORDER BY p.ID ASC, pm.meta_id ASC limit ".$this->currentStart.",".$this->postsPerCall." ";
			}
            $this->setGroupConcatLimit();
			$this->log_error("QUERY: ".$query);
            $data = $wpdb->get_results($query);
			$this->log_error("NUMBER OF POSTS FETCHED: ".count($data));
			$dataForSubmission = array();
			$idsForSubmission = array();
            $this->bulkDeletionIds = array();
			if(!empty($data)){
				$count = 0;
				foreach($data as $k => $v){
					$document_ 	= parent::createDocumentFromPost($v, false);
					if(is_array($document_) && count($document_) > 0){
						$dataForSubmission[$count]	=	$document_;
						$idsForSubmission[] 			= 	$dataForSubmission[$count]['externalId'];
						$count++;
					}
				}
			}
            if(count($this->bulkDeletionIds) > 0){
			    parent::submit_for_bulk_deletion($this->bulkDeletionIds);
            }
			$this->log_error("PASSING ".count($dataForSubmission)." posts to parent::submit_for_indexing function for submission to API");
			$result = parent::submit_for_indexing($dataForSubmission, $this->postsPerCall);
            $result['debug']['query'] = $query;
			if($result['success'] == true && array_key_exists("errors", $result) && count($result['errors']) == 0){
				$this->totalTries = 0;
				parent::addToSync($idsForSubmission);
				$result['fetched']	= $this->postsFetched;
				$total = (int)get_option($this->pluginOptions['num_indexed_posts']) + (int)$result['created'] + (int)$result['updated'];
				if(!isset($dataOptions['resetcount']) || (isset($dataOptions['resetcount']) && $dataOptions['resetcount'] == 1)) {
					update_option($this->pluginOptions['num_indexed_posts'], $total);
				}
				$result['indexed']	= $total;
				if($this->currentPage < $this->totalPages){
					$result['next']		= $this->currentPage + 1;
					$result['message'] 	= 'Data synchronization in progress. Please don\'t navigate to another page.';
				}else{
					$result['next']		= "";
					$this->_siq_set_option($this->serverDownKey, "", true);
					$result['removeFacetsNotice']   = $this->removeFacetsNotice();
					$result['syncSaveHtml'] = $this->getSyncSaveButtonHtml();
					$result['message'] 	= 'Data synchronization complete. You can now check your site\'s frontend to try SearchIQ search.';
				}
				$result['progress']	= ($this->postsFetched / $this->totalPosts);
				$result['total']	= (int)$this->totalPosts;
				$result['allowed']=$allowedPostTypes;
			}else{
				if(count($result["errors"]) > 0){
					$firstError = array_pop($result["errors"]);
					if(!empty($firstError) && strpos($firstError, "limit exceeded") !== FALSE){
						if((int)$result['created'] > 0 || (int)$result['updated'] > 0){
							$total = (int)get_option($this->pluginOptions['num_indexed_posts']) + (int)$result['created'] + (int)$result['updated'];
							if($total  > 0) {
								update_option($this->pluginOptions['num_indexed_posts'], $total);
								$result['indexed']	= $total;
							}
						}	
						$result['success'] 	= false;
						$result['next'] 	= "";
						$result['message'] = $firstError.". Please contact SearchIQ support.";
						$this->log_error("response from function:error :result(".json_encode($result).")");
						if($this->enableThumbnailService){
							$result['thumbServiceDisabledMsg'] = $this->thumbServiceDisabledMsg;
						}
						if (!!$this->pluginSettings["facets_enabled"]) {
							$result['facetsEnabled'] = $this->facetsTabHtml();
							$result['facetsHtml'] 	 = $this->facetsTabContentHtml();
						}
						$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
						return $result;
					}else if(!empty($firstError)){
						$result['success'] 	= false;
						$result['next'] 	= "";
						$result['message'] = $firstError;
						$this->log_error("response from function:error :result(".json_encode($result).")");
						if($this->enableThumbnailService){
							$result['thumbServiceDisabledMsg'] = $this->thumbServiceDisabledMsg;
						}
						if (!!$this->pluginSettings["facets_enabled"]) {
							$result['facetsEnabled'] = $this->facetsTabHtml();
							$result['facetsHtml'] 	 = $this->facetsTabContentHtml();
						}
						$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
						return $result;
					}
				}else if(!empty($result['message']) && strpos($result["message"], "engine is inactive") !== FALSE){
						$result['success'] 	= false;
						$result['next'] 	= "";
						if($this->enableThumbnailService){
							$result['thumbServiceDisabledMsg'] = $this->thumbServiceDisabledMsg;
						}
						if (!!$this->pluginSettings["facets_enabled"]) {
							$result['facetsEnabled'] = $this->facetsTabHtml();
							$result['facetsHtml'] 	 = $this->facetsTabContentHtml();
						}
						$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
						return $result;
				}
				if($this->totalTries >= $this->totalTriesMax){
					$result['success'] 	= false;
					if($this->enableThumbnailService){
						$result['thumbServiceDisabledMsg'] = $this->thumbServiceDisabledMsg;
					}
					if (!!$this->pluginSettings["facets_enabled"]) {
						$result['facetsEnabled'] = $this->facetsTabHtml();
						$result['facetsHtml'] 	 = $this->facetsTabContentHtml();
					}
					$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
					return $result;
				}
				parent::log_error("Total Try:".$this->totalTries);
				parent::log_error("Current Try:".$this->currentTry);
				parent::log_error("Current Page:".$this->currentPage);
				parent::log_error("Total Pages:".$this->totalPages);
				if(($this->currentPage <= $this->totalPages) && $this->currentTry <= $this->maxNumRetry){
					$this->currentTry++;
					$this->totalTries++;
					return $this->submit_for_indexing($data);
				}else if($this->currentPage < $this->totalPages){
					$this->currentTry	 = 1;
					$this->totalTries++;
					$data["currentPage"] = $this->currentPage + 1;
					return $this->submit_for_indexing($data);
				}else{
					$result['success'] 	= false;
					$result['next'] 	= "";
					if(strpos($result['message'],'network') !== FALSE && strpos($result['message'],'reachable') !== FALSE && strpos($result['message'],'limit exceeded') !== FALSE){
						$result['message'] 	= $result['message'];
					}else{
						if ($result['response_code'] == 404 && $result['message'] == "Domain not found") {
							$result['message'] = "Search engine not found. Taking you to search engine creation step. Please wait..";
							$result['searchengine'] = false;
							$this->searchEngineNotFound();
						} else {
							$result['message'] = "There was some error indexing posts, please try again later.";
						}
					}
				}
				$result['success'] 	= false;
				$result['next'] 	= "";
				if(strpos($result['message'],'network') !== FALSE && strpos($result['message'],'reachable') !== FALSE ){
					$result['message'] 	= $result['message'];
				}else{
					if ($result['response_code'] == 404 && $result['message'] == "Domain not found") {
						$result['message'] = "Search engine not found. Taking you to search engine creation step. Please wait..";
						$result['searchengine'] = false;
						$this->searchEngineNotFound();
					} else {
						$result['message'] = "There was some error indexing posts, please try again later.";
					}
				}
				$this->log_error("response from function: result(".json_encode($result).")");
				if($this->enableThumbnailService){
					$result['thumbServiceDisabledMsg'] = $this->thumbServiceDisabledMsg;
				}
				if (!!$this->pluginSettings["facets_enabled"]) {
					$result['facetsEnabled'] = $this->facetsTabHtml();
					$result['facetsHtml'] 	 = $this->facetsTabContentHtml();
				}
				$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
				return $result;
			}
		}else{
			$result['success'] 	= false;
			$result['message'] 	= "There are no posts to index currently. Please add some posts with content and try again.";
		}
		$this->log_error("response from function: result(".json_encode($result).")");
		if($this->enableThumbnailService){
			$result['thumbServiceDisabledMsg'] = $this->thumbServiceDisabledMsg;
		}
		if (!!$this->pluginSettings["facets_enabled"]) {
			$result['facetsEnabled'] = $this->facetsTabHtml();
			$result['facetsHtml'] 	 = $this->facetsTabContentHtml();
		}
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return $result;
	}

	public function submit_engine($data = array()){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$result			= array();
		$data['domain'] = $this->domain;
		$data['name']   = $this->domain;

		$result = parent::submit_engine($data);
		$this->createCustomSearchPageIfNotExists();
		if($result['engineKey'] != ""){
			$this->log_error("response from api:success(engineKey received)");
			if(empty( $this->pluginSettings['engine_name'] ) ){
				update_option($this->pluginOptions['use_custom_search'], "yes");
			}
			update_option($this->pluginOptions['engine_just_created'], "1");
			$engine_name	=	$result['name'];
			$engine_code	=	$result['engineKey'];
			update_option($this->pluginOptions['engine_name'], $engine_name);
			update_option($this->pluginOptions['engine_code'], $engine_code);
			$res			= $result['response_body'];
			unset($result["response"]);
			$result['engine'] 	= $engine_name;
			$result['message'] = "Your search engine has been created on the server and your domain name has been registered.";
			$result['created']  = "ok";
			$this->searchEngineNotFound(true);
		}

		if(strpos($result['message'], "Domain created") !== FALSE){
			$result['message'] = "Your search engine has been created on the server and your domain name has been registered.";
		}else if(strpos($result['message'], "engine limit") !== FALSE){
			$result['message'] = "Search engine limit exeeded, in order to create another one you need to delete any existing search engine from the <a class='siq_error_link' href='".$this->administerPanelLink."' target='_blank'>SearchIQ dashboard</a>";
		}
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return $result;
	}

	public function getConfigMainView($code = "", $engine = "", $postsIndexed = ""){

		$str = '<div class="section section-top">
					<h2>Plugin Settings</h2>
					<div class="data">
						<ul>
							<li class="vcode">
								<label>'.$this->labels["verificationCode"].'</label> <span class="info">'.$code.'</span>
							</li>
							<li class="ename">
								<label>'.$this->labels["engineName"].'</label> <span class="info">'.$engine.'</span>
							</li>
							<li class="iposts">
								<label>'.$this->labels["postsIndexed"].'</label> <span class="info">'.$postsIndexed.'</span>
							</li>
						</ul>
					</div>
				</div>';
		return $str;
	}

	private function clear_configuration($data){
		foreach($this->pluginOptions as $k => $v){
			if(!in_array($k, $this->pluginOptionsDontClear)){
				delete_option($v);
			}
		}
		$upload_dir = wp_upload_dir();
		$this->getPostTypesForIndexing();
		$args = array('numberposts' => -1, 'post_type'=> $this->postsToIndexAndSearch, 'post_status' => 'publish');
		$allposts = get_posts($args);
		if(count($allposts) > 0){
			foreach( $allposts as $postinfo ) {
				delete_post_meta($postinfo->ID, parent::POST_THUMB_META_KEY);
				delete_post_meta($postinfo->ID, parent::POST_THUMB_URL_META_KEY);
				delete_post_meta($postinfo->ID, parent::POST_ORIG_IMG_URL_META_KEY);
				delete_post_meta($postinfo->ID, parent::POST_THUMB_META_KEY_LARGE);
				delete_post_meta($postinfo->ID, parent::POST_THUMB_URL_META_KEY_LARGE);
			}
		}
		$result['success'] 	= 1;
		$result['message'] 	= 'Configuration has been reset';
		return $result;
	}

	public function index_posts($post){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		$this->lock_meta_update = true;
		try{
			if ($post['setImageCustomField'] && !empty($post['imageCustomField'])) {
				update_option($this->pluginOptions['image_custom_field'], $post['imageCustomField']);
				$this->pluginSettings["image_custom_field"] = $post['imageCustomField'];
			}
			if(!empty($post["postTypesToSearch"])) {
				$this->savePostTypes($post["postTypesToSearch"]);
			}
			if(isset($post["customFieldsToExclude"])) {
				$this->saveFieldsToExclude($post["customFieldsToExclude"]);
			}
			if(isset($post["customTaxonomiesToExclude"])) {
				$this->saveTaxonomiesToExclude($post["customTaxonomiesToExclude"]);
			}
			if(isset($post["excludePostIds"])) {
				$this->savePostsToExclude($post["excludePostIds"]);
			}
			if(isset($post["blackListUrls"])) {
				$this->saveBlackListUrls($post["blackListUrls"]);
			}
			if(isset($post["siq_field_for_excerpt"])) {
				$this->saveFieldForExcerpt($post["siq_field_for_excerpt"]);
			}
			if(isset($post["postTypesForSearchSelection"])) {
				$this->savePostTypesForSearchSelection($post["postTypesForSearchSelection"], true);
			}
			
			$settings 	= $this->getPluginSettings();
			$engine_just_created = get_option($this->pluginOptions['engine_just_created']);
			if ($engine_just_created == "1") {
				if ($this->checkGraphicEditor()) {
					delete_option($this->pluginOptions['engine_just_created']);
					update_option($this->pluginOptions["show_search_page_images"], "yes");
					update_option($this->pluginOptions["show_autocomplete_images"], "yes");
					$this->setMobileEnabled(self::DEFAULT_MOBILE_ENABLED);
					$this->_siq_sync_settings();
				} else {
					delete_option($this->pluginOptions['engine_just_created']);
					update_option($this->pluginOptions['graphic_editor_error'], '1');
					$this->_siq_sync_settings();
				}
			}
			if($post['currentPage'] == 0 && (int)$settings['index_posts'] > 0){
				$result = $this->delete_all_posts();
				if($result['searchengine'] == false){
					update_option($this->pluginOptions['num_indexed_posts'], 0);
				}
				if($result["success"] == true){
					$result = $this->submit_for_indexing($post);
				}
			}else{
				$result = $this->submit_for_indexing($post);
			}
		}catch(Exception $e){
			$result['success'] 		= false;
			if (strpos($e->getMessage(), "Search engine not found") !== FALSE) {
				$result['message'] = "Search engine not found. Taking you to search engine creation step. Please wait..";
				$result['searchengine'] = false;
				$this->searchEngineNotFound();
			} else {
				$result['message'] = $e->getMessage() . ", please try again after some time.";
			}
		}
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		$this->lock_meta_update = false;
		return $result;
	}

	public function delete_all_posts(){
		return parent::delete_all_posts();
	}

	private function is_valid_custom_search_page($postID) {
		$post = is_numeric( $postID ) ? get_post( $postID ) : get_post(url_to_postid($postID));
		return !is_null($post) && has_shortcode($post->post_content, 'siq_ajax_search') && $post->post_status == 'publish';
	}

	public function siq_cron_resynchronize_posts() {
		set_time_limit(0);
		$post = array("currentPage" => 0, 'task'=>'sync_posts', 'resetcount' => 0);
		$indexResponse = $this->submit_for_indexing($post);
		while(!empty($indexResponse["next"])) {
			$post["currentPage"] = $indexResponse["next"];
			$indexResponse = $this->submit_for_indexing($post);
		}
	}

	/**
	 * this function returns true if the WordPress can edit images. Otherwise false.
	 * @return boolean
	 */
	private function checkGraphicEditor() {
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		foreach($this->supported_image as $ext) {
			if ($ext == "jpeg") continue;
			$imgPath = SIQ_BASE_PATH . "/assets/test.$ext";
			$image = wp_get_image_editor($imgPath);
			if (is_wp_error($image)) {
				$this->log_error("checking graphics editor: error: (".json_encode($image->get_error_message()).")");
				return false;
			}
		}
		$this->log_error("checking graphics editor: success");
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return true;
	}

	public function getCustomSearchQuery(){
		return $this->customSearchString;
	}

	public function add_cfasync_attr_to_script_tag($tag, $handle) {
		if ('siq-container' !== $handle) {
			return $tag;
		}
		return str_replace( ' src', ' id="siq-container" data-cfasync="false" async defer src', $tag );
	}

	public function setImageCustomField($post) {
		update_option($this->pluginOptions['image_custom_field'], $post['imageCustomField']);
		$result["success"]     = 1;
		$result["message"]     = "Image custom field saved successfully";
		$this->_siq_sync_settings();
	}

	public function getMenuLocationSelectBox($name, $select){
		$html       = "";
		$selected   = "";
		$html       .= "<select name='".$name."' id='".$name."'>";
		$html       .= "<option value=''>-- Select menu to add searchbox --</option>";
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			$selected = ($select != "" && $select == $menu->slug) ? 'selected="selected"' : "";
			$html .= "<option ".$selected." value='".$menu->slug."'>".$menu->name."</option>";
		}
		$html .= "</select>";
		return $html;
	}

	public function customSearchBoxHtml(){
		$html       ="";
		$styleColor = ($this->siq_menu_select_box_color != "") ? "style='color:#".$this->siq_menu_select_box_color."'": "";
		$html .= '<div id="siq-expsearch-cont" class="siq-expsearch-cont">
			  <form class="siq-expsearch"  action="'.get_home_url().'">
			    <input type="search" placeholder="Search" name="s" class="siq-expsearch-input">
			    <span class="siq-expsearch-icon" '.$styleColor.'></span>
			  </form>
			</div>';
		return $html;
	}
	public function addSearchboxToMenu($items){
		if(strpos($items, '<option') === FALSE && strpos($items, '<li') !== FALSE) {
			$stylePosition  = "";
			$positionAbs    = "";
			$openFromClass  = "";
			if ($this->siq_menu_select_box_pos_right != "" && $this->siq_menu_select_box_pos_right != 0) {
				$stylePosition .= " right:" . $this->siq_menu_select_box_pos_right . "px; ";
			}

			if ($this->siq_menu_select_box_pos_top != "" && $this->siq_menu_select_box_pos_top != 0) {
				$stylePosition .= " top:" . $this->siq_menu_select_box_pos_top . "px; ";
			}
			if ($stylePosition != "") {
				$stylePosition = 'style="' . $stylePosition . '"';
			}

			if ($this->siq_menu_select_box_pos_absolute == "yes") {
				$positionAbs = "moveToExtremeRight";
			}

			if($this->siq_menu_select_box_direction == "left"){
				$openFromClass  = "openFromLeft";
			}

			$items = $items . '<li id="menu-item-siq-selectbox" class="menu-item ' . $positionAbs . '" ' . $stylePosition . '>
			<div id="siq-menu-searchbox-wrap" class="siq-menu-searchbox-wrap '.$openFromClass.'" >' . $this->customSearchBoxHtml() . '</div></li>';
		}
		return $items;
	}

	public function includeSearchboxScript(){
		?>
		<script type="text/javascript">
			if(typeof jQuery != "undefined") {
					jQuery('.siq-expsearch-icon').each(function(){
					var searchBox = (jQuery(this).parents('.siq-icon-searchbox-wrap').size() > 0) ? jQuery(this).parents('.siq-icon-searchbox-wrap') : jQuery('.siq-menu-searchbox-wrap') ;
					var inputBox = searchBox.find('.siq-expsearch-input');
					
					jQuery(this).on('click', function () {
						if (!searchBox.hasClass('siq-search-open')) {
							searchBox.addClass('siq-search-open');
							inputBox.focus();
						} else {
							searchBox.removeClass('siq-search-open');
							inputBox.focusout();
						}
					});
					jQuery('body').click(function (evt) {
						if (evt.target.id == "siq-menu-searchbox-wrap")
							return;
						if (jQuery(evt.target).closest('#siq-menu-searchbox-wrap').length || jQuery(evt.target).closest('.siq-icon-searchbox-wrap').length)
							return;

						if (searchBox.hasClass('siq-search-open')) {
							searchBox.removeClass('siq-search-open');
						}
					});
				});
			}
		</script>
		<?php
	}

	public function customThumbnailDropDown($fields, $name){
		$pluginSettingsICF = $this->pluginSettings["image_custom_field"];
		$others = false;
		if(strpos($pluginSettingsICF, ':') !== FALSE){
			$others = true;
		}
		$value = $this->getPostImageCustomField($name, true);
		$strSelect  = '';
		$inFields   = false;
		$strSelect .= '<select name="imageCustomFieldSelect['.$name.']">
			    <option value="">Featured image (If not then first image from content)</option>';
		if(count($fields["regular_fields"]) > 0) {
			$strSelect .= '<optgroup label="Regular Custom Fields">';
			    $inFields = false;
			    foreach ($fields["regular_fields"] as $regularField) {
			        $selected = "";
			        if ($regularField === $value) {
			            $inFields = true;
			            $selected = " selected='selected'";
			        }
				    $strSelect .= "<option value='$regularField'$selected>$regularField</option>";
			    }
		$strSelect .= '</optgroup>';
		}
		if(count($fields["system_fields"]) > 0) {
			$strSelect .= '<optgroup label="System Fields">';

			foreach ($fields["system_fields"] as $systemField) {
				$selected = "";
				if ($systemField === $value) {
					$inFields = true;
					$selected = " selected='selected'";
				}
				$strSelect .= "<option value='$systemField'$selected>$systemField</option>";
			}
			$strSelect .= '</optgroup>';
		}
		if($others === false){
			$inFields = true;
		}
		$strSelect .= '<option value="Other..."'.(!empty($value) && !$inFields ? " selected='selected'" : "").'>Other...</option>
			</select>';
		$strSelect .= '<input type="text" class="imageCustomFieldName" name="imageCustomFieldName['.$name.']"';
		$strSelect .= ' style="'.($inFields || empty($value) ? "display: none;" : "").'"';
		$strSelect .= ' value="'.($inFields ? "" : $value).'" placeholder="Enter custom field name..."/>';
		return $strSelect;
	}
	
	public function customDescriptionDropDown($fields, $name, $value= ""){
		$strSelect  = '';
		$selectedExc = ('excerpt' === $value) ? "selected='selected'": "";
		$strSelect .= '<select name="excerptCustomFieldSelect['.$name.']" class="excerptCustomFieldSelect">
				<option value="">Post content</option>
			    <option '.$selectedExc.' value="excerpt">Post excerpt</option>';
		if(count($fields["regular_fields"]) > 0) {
			$strSelect .= '<optgroup label="Regular Custom Fields">';
			    foreach ($fields["regular_fields"] as $regularField) {
			        $selected = "";
			        if ($this->customFieldPrefix.$regularField === $value) {
			            $selected = " selected='selected'";
			        }
				    $strSelect .= "<option value='".$this->customFieldPrefix.$regularField."' $selected>$regularField</option>";
			    }
		$strSelect .= '</optgroup>';
		}
		if(count($fields["system_fields"]) > 0) {
			$strSelect .= '<optgroup label="System Fields">';

			foreach ($fields["system_fields"] as $systemField) {
				$selected = "";
				if ($this->customFieldPrefix.$systemField === $value) {
					$selected = " selected='selected'";
				}
				$strSelect .= "<option value='".$this->customFieldPrefix.$systemField."' $selected>$systemField</option>";
			}
			$strSelect .= '</optgroup>';
		}
		$strSelect .= '</select>';
		return $strSelect;
	}

	protected function getDocumentFieldsOptionList($selected = null, $excludeFields = null, $postType = null) {
		$options = "";
		$postFields = array(
			"externalId" => "ID",
			"title" => "Title",
			"author" => "Author",
			"categories" => "Category",
			"timestamp" => "Date",
			"url" => "URL",
			"body" => "Body",
			"excerpt" => "Excerpt",
			"documentType" => "Post type",
			"tags" => "Tag",
			"image" => "Featured image");

        if (is_null($postType) || $postType == "_siq_all_posts") {
            $postTypes = $this->getAllpostTypes();
            $allCustomFields = array();
            $tmpPostFields = $this->getAllCustomFields($postTypes);
            $commonRegular = array();
            $commonSystem = array();
            $initial = true;
            foreach($tmpPostFields as $postType => $postTypeFields) {
                if ($initial) {
                    $initial = false;
                    $commonRegular = $postTypeFields['regular_fields'];
                    $commonSystem = $postTypeFields['system_fields'];
                } else {
                    $commonRegular = array_intersect($postTypeFields['regular_fields'], $commonRegular);
                    $commonSystem = array_intersect($postTypeFields['system_fields'], $commonSystem);
                }
            }
            $allCustomFields = array(
                "regular_fields" => $commonRegular,
                "system_fields" => $commonSystem
            );
        } else {
            $tmpCustomFields = $this->getAllCustomFields(array($postType));
            $allCustomFields = $tmpCustomFields[$postType];
        }
		$options .= "<optgroup label='Post fields'>";
		foreach ($postFields as $key => $val) {
			if (is_array($excludeFields) && array_search($key, $excludeFields, TRUE) !== FALSE) {
				continue;
			}
			$options .= "<option value='$key'" . ($key === $selected ? " selected" : "") . ">$val</option>";
		}
		$options .= "</optgroup>";

		$allTaxonomies = is_null($postType) || $postType == "_siq_all_posts"
            ? $this->getCommonTaxonomies()
            : $this->getPostTypeTaxonomies($postType);

		if (is_array($allTaxonomies) && count($allTaxonomies) > 0) {
			$options .= "<optgroup label='Taxonomies'>";
			foreach ($allTaxonomies as $taxonomy) {
                if (is_array($excludeFields) && array_search($this->customTaxonomyPrefix.$taxonomy, $excludeFields, true) !== false) {
                    continue;
                }
				$options .= "<option value='{$this->customTaxonomyPrefix}{$taxonomy}'" . ($selected === $this->customTaxonomyPrefix . $taxonomy ? " selected" : "") . ">{$taxonomy}</option>";
			}
			$options .= "</optgroup>";
		}

		if ($postType == "product" && $this->woocommerceActive) {
			$productAttributes = $this->getAllWoocommerceAtributeNames();
			if (count($productAttributes) > 0) {
				$options .= "<optgroup label='Product attributes'>";

				foreach ($productAttributes as $attr) {
                    if (is_array($excludeFields) && array_search($this->customAttributePrefix.$attr['key'], $excludeFields, true) !== false) {
                        continue;
                    }
					$options .= "<option value='{$this->customAttributePrefix}{$attr['key']}'" . ($selected === $this->customAttributePrefix . $attr['key'] ? " selected" : "") . ">{$attr['name']}</option>";
				}

				$options .= "</optgroup>";
			}
		}
		
		foreach ($allCustomFields as $key => $customFields) {
            if (count($customFields) != 0) {
                $options .= "<optgroup label='" . ($key === "regular_fields" ? "Regular custom fields" : "System fields") . "'>";
                foreach ($customFields as $field) {
                    if (is_array($excludeFields) && array_search($this->customFieldPrefix.$field, $excludeFields, true) !== false) {
                        continue;
                    }
                    $options .= "<option value='$this->customFieldPrefix$field'" . ($selected === $this->customFieldPrefix . $field ? " selected" : "") . ">$field</option>";
                }
                $options .= "</optgroup>";
            }
		}
		return $options;
	}

	public function customFieldsFilterDropDown($fields, $name){

		$fieldCount = 0;
		$value          = $this->getExcludedCustomFields($name);
		$valueTaxonomy  = $this->getExcludedCustomTaxonomies($name);
		$taxonomies     = array_key_exists($name, $this->allTaxonomiesWithPostType) ? $this->allTaxonomiesWithPostType[$name]: array();
		$strSelect  	= '';
		$strInnerDiv   	= '';
		$noFields   = '';
		$strSelect .= '<div class="selectorOuter">';
		$strSelect .= 'SELECTBOXFILTER';
		$strSelect .= 'STARTINNERDIV';
		if(count($fields["regular_fields"]) > 0) {
			$strSelect .= '<ul class="regularFields fieldFilterList">';
			$strSelect .= '<li class="top"><input type="checkbox" class="checkAll"><label class="filterLabel">Regular Custom Fields</label></li>';
			foreach ($fields["regular_fields"] as $regularField) {
				$selected = "";
				if (in_array($regularField, $value)) {
					$selected = " checked='checked'";
				}
				$strSelect .= "<li><input id='customFieldFilterCheckbox_".$regularField."_".$name."' type='checkbox' value='$regularField' $selected name=customFieldFilterCheckbox[".$name."][]'><label for='customFieldFilterCheckbox_".$regularField."_".$name."' class='checkboxLabel'>$regularField</label></li>";
			}
			$strSelect .= '</ul>';
			$fieldCount++;
		}
		if(count($fields["system_fields"]) > 0) {
			$strSelect .= '<ul class="systemFields fieldFilterList">';
			$strSelect .= '<li class="top"><input type="checkbox" class="checkAll"><label class="filterLabel">System Custom Fields</label></li>';
			foreach ($fields["system_fields"] as $systemField) {
				$selected = "";
				if (in_array($systemField, $value)) {
					$selected = " checked='checked'";
				}
				$strSelect .= "<li><input id='customFieldFilterCheckbox_".$systemField."_".$name."' type='checkbox' value='$systemField' $selected name=customFieldFilterCheckbox[".$name."][]'><label class='checkboxLabel' for='customFieldFilterCheckbox_".$systemField."_".$name."'>$systemField</label></li>";
			}
			$strSelect .= '</ul>';
			$fieldCount++;
		}

		if($taxonomies!= "" && is_array($taxonomies) && count($taxonomies) > 0) {
			$strSelect .= '<ul class="customTaxonomies fieldFilterList">';
			$strSelect .= '<li class="top"><input type="checkbox" class="checkAll"><label class="filterLabel">Custom Taxonomy</label></li>';
			$allTaxonomies = $this->getCombinedTaxonomyTerms($taxonomies);
			foreach($taxonomies as $taxonomy) {
				if(array_key_exists($taxonomy['name'], $allTaxonomies)){
					$selected = "";
					if (in_array($taxonomy['name'], $valueTaxonomy)) {
						$selected = " checked='checked'";
					}
					$strSelect .= "<li><input id='customTaxonomyFilterCheckbox_" . $taxonomy['name'] . "_" . $name . "' type='checkbox' value='".$taxonomy['name']."' $selected name=customTaxonomyFilterCheckbox[" . $name . "][]'><label class='checkboxLabel' for='customTaxonomyFilterCheckbox_" . $taxonomy['name'] . "_" . $name . "'>".$taxonomy['label']."</label></li>";
				}
			}
			$strSelect .= '</ul>';
			$fieldCount++;
		}


		$strSelect .= '</div>';
		$strSelect .= '</div>';
		if($fieldCount == 0){
			$noFields 	 = "noFields";
			$strInnerDiv = "<label>No custom fields</label>";
		}
		$strSelectBox = '<select class="customFieldFilterSelect '.$noFields.'" name="customFieldFilterSelect['.$name.']">
			    		<option>Select custom fields to exclude from search</option>
			    		</select>';

		$strSelect = str_replace(array('SELECTBOXFILTER', 'STARTINNERDIV'),array($strSelectBox, '<div class="selectorInner '.$noFields.'">'.$strInnerDiv.''),$strSelect);
		return $strSelect;
	}

	protected function getFilterAndPostTypeHTML(){
		$settings 		= $this->getPluginSettings();
		$strHTML = "";
		$strHTML .='<div class="data sep-block" id="imageCustomFieldSubsection">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" class="tableCustomPostTypeImages">
							<tr>
								<th class="tdPostType">Select post type to search</th>
								<th>Searchable by default</th>
								<th>(Optional) Image from custom field</th>
								<th>Result page description field</th>';
		if($this->areExcludeFeaturesEnabled()){
			$strHTML .= '<th>Custom field filter</th>';
		}
		$strHTML .='</tr>';
		$postTypes = $this->getAllpostTypes();
		$postTypesForSearch = $this->getPostTypesForIndexing();
		$fields = $this->getAllCustomFields($postTypes);
		$customExcerpt = $this->getFieldForExcerpt();
		foreach($postTypes as $key => $val){
			$strHTML .= '<tr>
				<td class="tdPostType">';
					$checked = '';
					if (in_array($val, $postTypesForSearch)) {
						$checked = ' checked="checked"';
					}
			$strHTML .= "<input type=\"checkbox\" name=\"post_types_for_search[]\" value=\"$val\"$checked/>";
			$strHTML .=$val;
			$strHTML .='</td>';
			
			$strHTML .='<td>';
				$strHTML .= $this->getSearchableDropdown($val, $checked);
			$strHTML .='</td>';
			
			$strHTML .='<td>'.$this->customThumbnailDropDown($fields[$val], $val).'</td>';  
			$selectedVal    = "";
			if (is_array($customExcerpt) && array_key_exists($val, $customExcerpt ) && $customExcerpt [$val] != "") {
				$selectedVal  = $customExcerpt [$val];
			}
			$strHTML .= '<td>'.$this->customDescriptionDropDown($fields[$val], $val, $selectedVal).'</th>';
			if($this->areExcludeFeaturesEnabled()) {
				$strHTML .= '<td>' . $this->customFieldsFilterDropDown($fields[$val], $val) . '</td>';
			}
			$strHTML .=	'</tr>';
		}
		if($this->areExcludeFeaturesEnabled()) {
			$strHTML .= '<tr>
				<th colspan="5"><h4>Add post ID\'s that you want to exclude from getting indexed in the box below separated by comma.</h4></th>
			</tr>
			<tr>
				<td colspan="5">';
			$strHTML .= '<textarea class="excludePostIds">' . $settings["exclude_posts"] . '</textarea>';
			$strHTML .= '</td>
			</tr>';
			$blackListUrls =  !empty($settings["blacklist_urls"]) ? $settings["blacklist_urls"] : "";
			
			$strHTML .= '<tr>
				<th colspan="5"><h4>Add URL\'s that you want to blacklist <small>(Add one URL per line, can be full or partial URL)</small>.</h4></th>
			</tr>
			<tr>
				<td colspan="5">';
			$strHTML .= '<textarea class="blackListUrls">' . $blackListUrls . '</textarea>';
			$strHTML .= '</td>
			</tr>';
		}
		
		$strHTML .='</table>';
		$strHTML .='</div>';
		
		if($this->pluginSettings["index_posts"] >= 1 && empty($this->pluginSettings["siq_engine_not_found"])){
			$strHTML .= $this->getSyncSaveButtonHtml();
		}
		return $strHTML;
	}
	
	private function getSearchableDropdown($postType, $checked = ""){
		$options = array("yes", 'no');
		$strHTML = "";
		$searchablePostTypes = $this->getPostTypesForSearchSelection();
		$defaultVal 		= (empty($checked)) ? "no": "";
		$defaulState 	= (!empty($defaultVal)) ? "disabled='disabled' ": "";
		$defaulClass 	= (!empty($defaultVal)) ? "isDisabled": "";
		$strHTML .='<select class="widthDefault '.$defaulClass.'" name="postTypesSetForSearch['.$postType.']" '.$defaulState.'>';
			foreach($options as $key => $val){
				$optionSelection =  (!empty($defaultVal) && $val == $defaultVal) ?  "selected='selected' " : array_key_exists($postType, $searchablePostTypes) && $searchablePostTypes[$postType] == $val ? "selected='selected' ":  "";
				$strHTML .='<option value="'.$val.'" '.$optionSelection.'>'.$val.'</option>';
			}
		$strHTML .='</select>';
		return $strHTML;
	}
	
	public function getSyncSaveButtonHtml(){
			$strHTML = "";
			$strHTML .='<div class="syncSettingsWrapper">';
				$strHTML .='<input type="button" name="btnSyncSettings" id="btnSyncSettings" value="Save settings" class="btn">';
			$strHTML .='</div>';
			return $strHTML;
	}
	
	public function getResyncBlock($textIndexing, $textMessageStep3, $is_indexed = 0 ){
		$strHTML = "";
		$showDeltaSync = "display:none;";
		$strHTML.='<div class="resyncPostsActionWrapper">';
			if($is_indexed > 0){
					$strHTML.='<h2>'.str_replace("Full", "", $textIndexing).'</h2>';
					$showDeltaSync = "";
			}
					$strHTML.='<h3>'.$textMessageStep3.'</h3>
					<input type="button" name="btnSubmitPosts" id="btnSubmitPosts" value="'.$textIndexing.'" class="btn" />
					<input type="button" name="btnDeltaSync" id="btnDeltaSync" style="'.$showDeltaSync.'" value="'.str_replace("Full", "Delta", $textIndexing).'" class="btn" />
					<div class="progress-wrap progress" data-progress-percent="25">
						<div class="progress-bar progress"></div>
					</div>
					<div class="progressText"></div>
				</div>';
		return $strHTML;
	}
	
	protected function save_and_sync_settings($data = array(), $syncWithSearchIQ = true){
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
		try{
			$result = parent::save_and_sync_settings($data, $syncWithSearchIQ);
			if($result['success'] == true){
				$result['message'] = "Settings synced successfully";
				$this->log_error("response from api:success: result(".json_encode($result).")");
			}
		}catch(Exception $e){
			$result['success'] = false;
			$result['message'] = $e->getMessage();
			$this->log_error("SIQ ERROR: ".__FILE__.":".__FUNCTION__." error('".json_encode($e->getMessage())."');","error");
		}
		$this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
		return $result;
	}
	
	public function getPostTypesForSearchOnWidget(){
		return $this->getPostTypesForIndexing();
	}
	

	public function checkFacetsError($show){ ?>
		<script type="text/javascript">
			$jQu	        = jQuery.noConflict();
			var showFacet   = <?php echo $show;?>;
			$jQu(window).load(function(){
				if(showFacet == 1) {
					$jQu("#tab-6 a").trigger("click");
					syncPostsOnFacets();
				}
			});
		</script>
	<?php }

    public function buildFacetFieldOptionList($postType, $mappings, $selectedField = null)
    {
        $optionList = "<option value=''></option>";
        $excludeFields = array(
            "externalId", "title", "url", "body", "excerpt", "image", "documentType"
        );
        if (!is_null($mappings)) {
            $excludeFields = array_merge($excludeFields, array_map(function ($val) {
                return $val['field'];
            }, $mappings));

            // create options from mapping
            foreach ($mappings as $mapping) {
                $optionList .= "<option data-targetfield='{$mapping['targetField']}' data-facettype='{$mapping['type']}' data-dateformat='{$mapping['dateFormat']}' value='{$mapping['field']}' " . (!is_null($selectedField) && $selectedField == $mapping['field'] ? " selected='selected' : ''" : "") . ">{$mapping['label']}</option>";
            }
        }
        return $optionList . $this->getDocumentFieldsOptionList($selectedField, $excludeFields, $postType);
    }

    public function getCorrectFacetType($field, $defaultType, $mappings) {
        if (is_array($mappings)) {
            foreach ($mappings as $mapping) {
                if ($mapping['field'] == $field) {
                    return $mapping['type'];
                }
            }
        }
        return $defaultType;
    }

    public function getTargetField($field, $mappings) {
        if (is_array($mappings)) {
            foreach ($mappings as $mapping) {
                if ($mapping['field'] == $field) {
                    return $mapping['targetField'];
                }
            }
        }
        return null;
    }

    public function isPredefinedField($field, $mappings) {
        if (is_array($mappings)) {
            foreach ($mappings as $mapping) {
                if ($mapping['field'] == $field) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getCorrectFacetDateFormat($field, $defaultFormat, $mappings) {
        if (is_array($mappings)) {
            foreach ($mappings as $mapping) {
                if ($mapping['field'] == $field) {
                    return $mapping['dateFormat'];
                }
            }
        }
        return $defaultFormat;
    }
	
	protected function syncPostsAfterThumbnailGeneration($data){
		$dataForSubmission 	= array();
		$idsForSubmission 		= array();
        $this->bulkDeletionIds = array();
		if(!empty($data)){
				$count = 0;
				foreach($data as $k => $v){
					$document_ 	= parent::createDocumentFromPost($v, false);
					if(is_array($document_) && count($document_) > 0){
						$dataForSubmission[$count]	=	$document_;
						$idsForSubmission[] 			= 	$dataForSubmission[$count]['externalId'];
						$count++;
					}
				}
			}
            if(count($this->bulkDeletionIds) > 0){
                parent::submit_for_bulk_deletion($this->bulkDeletionIds);
            }
			$result = parent::submit_for_indexing($dataForSubmission, $this->postsPerCallForThumb);
			return $result;
	}
	
	private function checkThumbnailTypeFromApi(){
		$thumbnailTypeInApi 			= (is_array($this->siqSyncSettings) && array_key_exists("thumbnailType", $this->siqSyncSettings)) ? $this->siqSyncSettings['thumbnailType'] : "";
		$thumbnailTypeInLocalDb = get_option($this->pluginOptions["siq_crop_resize_thumb"], FALSE);
		if(!empty($thumbnailTypeInApi) && empty($thumbnailTypeInLocalDb)){
			update_option($this->pluginOptions["siq_crop_resize_thumb"], $thumbnailTypeInApi);
			$this->pluginSettings["siq_crop_resize_thumb"] = $thumbnailTypeInApi;
			return $thumbnailTypeInApi;
		}
		return  "";
	}
	
	private function getPostParamsForCron($task = ""){
		if(!empty($task)){
			switch($task){
				case "deltasync";
					$result	= $this->getParamsForDeltaSync();
					return $result;
					break;
			}
		}
		return "";
	}
	
	private function getParamsForDeltaSync(){
		$params = array();
		$params["action"] 			 		= "siq_ajax";
		$params["task"]					 		=  "delta_sync_posts";
		$params["inProgress"] 	 		= 1;
		$params["percent"]			 		= .01;
		$params["total"]				 		= "";
		$params["deltaPostIDs"] 		= "";
		$params["currentPage"]		= 0;
		$params["totalPages"]			= 1;
		$params["nononce"]				= 1;
		$params["success"]				= 1;
		return $params;
	}
	
	public function processCron($task = "", $params = array()){
			$log = false;
			if(!empty($task)){
			switch($task){
				case "deltasync";
					$postParams = $this->getPostParamsForCron($task);
					$postParams = $this->setParamsForCron($postParams, $params);
					while($postParams["percent"] < 100 && $postParams["currentPage"]<= $postParams["totalPages"]){
						$this->log_error("CRON PARAMETERS: ".json_encode($postParams));
						if(array_key_exists('forceSync', $postParams) && $postParams['forceSync'] == 1){
							$result	= $this->full_sync_posts($postParams);
							$this->log_error("RESULT: ".json_encode($result));
							if($result["success"] == 1){
								$postParams["percent"] 			=  $result["percent"];
								$postParams["currentPage"] =  $result["currentPage"];
								$postParams["totalPages"] 	=  $result["totalPages"];
								$postParams["total"] 					=  $result["total"];
								$postParams["success"] 			=  $result["success"];
								$postParams									= $this->setParamsForCron($postParams, $result);
							}else{
								$postParams["percent"] 				=  100;
							}
						}else{
							$result	= $this->delta_sync_posts($postParams);
							$this->log_error("RESULT: ".json_encode($result));
							if($result["success"] == 1){
								$postParams["percent"] 				=  $result["percent"];
								$postParams["currentPage"] 	=  $result["currentPage"];
								$postParams["totalPages"] 		=  $result["totalPages"];
								$postParams["total"] 					=  $result["totalPosts"];
								$postParams["deltaPostIDs"] 	=  $result["deltaPostIDs"];
								$postParams["success"] 				=  $result["success"];
								$postParams									= $this->setParamsForCron($postParams, $result);
							}else{
								$postParams["percent"] 				=  100;
							}
						}
					}
					$this->log_error("POSTPARAMS FINAL".json_encode($postParams));
					break;
			}
		}
		return "";
	}
	private function setParamsForCron($postParams, $params){
		if(array_key_exists('fromdate', $params) && !empty($params['fromdate'])){
			$postParams['fromdate']  = $params['fromdate'];
		}
		if(array_key_exists('todate', $params) && !empty($params['todate'])){
			$postParams['todate']  = $params['todate'];
		}
		if(array_key_exists('forceSync', $params) && !empty($params['forceSync'])){
			$postParams['forceSync']  = $params['forceSync'];
		}
		return $postParams;
	}
	
	public function facetsTabHtml($hide = "hide", $selected = 'notselected', $tab = ""){
		$html = "";
		$html .= '<li id="'.$tab.'" class="'.$selected.' '.$hide.'">';
		$html .= '<a href="'.admin_url('admin.php?page=dwsearch&tab=tab-6').'">Facets</a>';
		$html .= '</li>';
		return $html;
	}
	
	public function facetsTabContentHtml(){
		$html = "";
		ob_start();
			echo '<div class="tab tab-6 notselected hide">';
			include_once(SIQ_BASE_PATH . '/templates/backend/facets.php');
			echo '</div>';
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	public function getTaxonomyNames($include, $type = "category"){
		$args 		= array('taxonomy'=> $type, 'include'=>$include, 'hide_empty'=>false);
		$terms 		= get_terms($args);
		$termNames 	= array();
		if(!empty($terms) && count($terms) > 0){
			foreach($terms as $k => $v){
				$termNames[]  = $v->name;
			}
		}
		return $termNames;
	}
	public function siq_update_product_stock($product){ // function to read product status and update stock in SIQ Database
		if( !empty( $product) ){
			parent::_siq_update_product_stock($product);
		}
	}
	private function setGroupConcatLimit(){
        $this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__);
	    global $wpdb;
	    $group_concat_query = $this->group_concat_query.$this->group_concat_limit;
	    $wpdb->query($group_concat_query);
        $this->log_error("GROUP_CONCAT_QUERY: ".$group_concat_query);
        $this->logFunctionCall(__FILE__, __CLASS__, __FUNCTION__, __LINE__, true);
    }
}
