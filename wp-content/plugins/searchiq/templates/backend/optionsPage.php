<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!empty( $_POST ) && isset($_POST['btnSubmitOptions']) && check_admin_referer($this->updateOptionsNonce)){
	$this->getPluginSettings();
	$siq_use_custom_search			=	!empty($_POST['siq_use_custom_search']) ? sanitize_text_field($_POST['siq_use_custom_search']) : "";
	$siq_custom_search_page			=	sanitize_text_field($_POST['siq_custom_search_page']);
	$siq_enable_autocomplete		=	!empty($_POST['siq_enable_autocomplete']) ? sanitize_text_field($_POST['siq_enable_autocomplete']) : "";
	$siq_searchbox_name             =   sanitize_text_field($_POST['siq_searchbox_name']);
	$siq_search_query_param_name = sanitize_text_field($_POST['siq_search_query_param_name']);
	$siq_menu_select_box = sanitize_text_field($_POST['siq_menu_select_box']);
	$siq_menu_select_box_color = sanitize_text_field($_POST['siq_menu_select_box_color']);
	$siq_menu_select_box_pos_right = intval(sanitize_text_field($_POST['siq_menu_select_box_pos_right']));
	$siq_menu_select_box_pos_top = intval(sanitize_text_field($_POST['siq_menu_select_box_pos_top']));
	$siq_menu_select_box_pos_absolute = !empty($_POST['siq_menu_select_box_pos_absolute']) ? sanitize_text_field($_POST['siq_menu_select_box_pos_absolute']) : "";
	$siq_menu_select_box_direction = sanitize_text_field($_POST['siq_menu_select_box_direction']);
	$siq_search_sortby		= sanitize_text_field($_POST['siq_search_sortby']);
	$siq_default_thumbnail = sanitize_text_field($_POST['siq_default_thumbnail']);
	$forceLoadSettings = !empty($_POST['siq_forceLoadSettings']) ? sanitize_text_field($_POST['siq_forceLoadSettings']) : '';
	$siq_wc_hide_out_of_stock_in_search  = !empty($_POST['siq_wc_hide_out_of_stock_in_search']) ? sanitize_text_field($_POST['siq_wc_hide_out_of_stock_in_search']) : "";
	// update search query param name
	if (empty($siq_search_query_param_name) || $siq_search_query_param_name === $this->search_query_param_name) {
		delete_option($this->pluginOptions['search_query_param_name']);
	} else {
		update_option($this->pluginOptions['search_query_param_name'], $siq_search_query_param_name);
	}

	$this->setSearchAlgorithm(sanitize_text_field($_POST['siq_search_algorithm']));

	// update custom css
	$settings 					= $this->getPluginSettings();
	$stylingVar					= $settings['custom_search_page_style'];
	$styling					= $this->getStyling($stylingVar);

	$styling['customCss']       = sanitize_text_field($_POST["stylingCustomCss"]);
	update_option($this->pluginOptions['custom_search_page_style'], $styling);

	$siq_invalid_custom_search_page = false;
	$error=array();
	if($siq_use_custom_search=='yes'){
		update_option($this->pluginOptions['use_custom_search'],$siq_use_custom_search);
		$customSearchPage = $this->createCustomSearchPageIfNotExists();
	}else{
		delete_option($this->pluginOptions['use_custom_search']);
	}


	// enable/disable autocomplete
	if ($siq_enable_autocomplete == "yes") {
		delete_option($this->pluginOptions['disable_autocomplete']);
	} else {
		update_option($this->pluginOptions['disable_autocomplete'], "yes");
	}

	// Update custom search page
	if($siq_custom_search_page!=""){
		if ($this->is_valid_custom_search_page($siq_custom_search_page) ) {
			$siq_custom_search_page = $this->getSearchPageUrl($siq_custom_search_page);
			update_option($this->pluginOptions['custom_search_page'], $siq_custom_search_page);
		} else {
			update_option($this->pluginOptions['custom_search_page'], $siq_custom_search_page);
			// show error message
			$siq_invalid_custom_search_page = true;
		}
	}else{
		$customSearchPageOld = get_option($this->pluginOptions['custom_search_page']);
		if($customSearchPageOld != ""){
			if ($this->is_valid_custom_search_page($customSearchPageOld)) {
				$customSearchPageOld = $this->getSearchPageUrl($customSearchPageOld);
				update_option($this->pluginOptions['custom_search_page_old'], $customSearchPageOld);
			} else {
				// show error message
				$siq_invalid_custom_search_page = true;
			}
		}
		update_option($this->pluginOptions['custom_search_page'], "");
	}

	if (empty($siq_searchbox_name) || $siq_searchbox_name === "s") {
		delete_option($this->pluginOptions['searchbox_name']);
	} else {
		update_option($this->pluginOptions['searchbox_name'], $siq_searchbox_name);
	}

	if($siq_menu_select_box !=""){
		update_option($this->pluginOptions['siq_menu_select_box'],$siq_menu_select_box);
	}else{
		delete_option($this->pluginOptions['siq_menu_select_box']);
	}

	if($siq_menu_select_box_color !=""){
		update_option($this->pluginOptions['siq_menu_select_box_color'],$siq_menu_select_box_color);
	}else{
		delete_option($this->pluginOptions['siq_menu_select_box_color']);
	}

	if($siq_menu_select_box_pos_right != "" && $siq_menu_select_box_pos_right != 0){
		update_option($this->pluginOptions['siq_menu_select_box_pos_right'],$siq_menu_select_box_pos_right);
	}else{
		delete_option($this->pluginOptions['siq_menu_select_box_pos_right']);
	}

	if($siq_menu_select_box_pos_top != "" && $siq_menu_select_box_pos_top != 0){
		update_option($this->pluginOptions['siq_menu_select_box_pos_top'],$siq_menu_select_box_pos_top);
	}else{
		delete_option($this->pluginOptions['siq_menu_select_box_pos_top']);
	}

	if($siq_menu_select_box_pos_absolute != "" && $siq_menu_select_box_pos_absolute == "yes"){
		update_option($this->pluginOptions['siq_menu_select_box_pos_absolute'],$siq_menu_select_box_pos_absolute);
	}else{
		delete_option($this->pluginOptions['siq_menu_select_box_pos_absolute']);
	}

	if($siq_menu_select_box_direction != "" && $siq_menu_select_box_direction != ""){
		update_option($this->pluginOptions['siq_menu_select_box_direction'],$siq_menu_select_box_direction);
	}else{
		delete_option($this->pluginOptions['siq_menu_select_box_direction']);
	}
	if($siq_search_sortby != ""){
		update_option($this->pluginOptions['siq_search_sortby'],$siq_search_sortby);
	}else{
		delete_option($this->pluginOptions['siq_search_sortby']);
	}
	if(!empty($siq_default_thumbnail)){
		update_option($this->pluginOptions['siq_default_thumbnail'],$siq_default_thumbnail);
	}else{
		delete_option($this->pluginOptions['siq_default_thumbnail']);
	}
	if(!empty($forceLoadSettings)){
		update_option($this->pluginOptions['siq_forceLoadSettings'],$forceLoadSettings);
	}else{
		delete_option($this->pluginOptions['siq_forceLoadSettings']);
	}
	
	if ($siq_wc_hide_out_of_stock_in_search == "yes") {
		update_option($this->pluginOptions['siq_wc_hide_out_of_stock_in_search'], "yes");
	} else {
		delete_option($this->pluginOptions['siq_wc_hide_out_of_stock_in_search']);
	}

	$arrSync['openResultInTab'] = isset($_POST['siq_openResultInTab']) && !!$_POST['siq_openResultInTab'];
	$arrSync['hideLogo'] = (isset($_POST['siq_hideLogo'])) ? !!$_POST['siq_hideLogo'] : 0;
	$this->_siq_sync_settings($arrSync);
	$this->siqSyncSettings['openResultInTab'] = $arrSync['openResultInTab'];
	$this->siqSyncSettings['hideLogo'] = $arrSync['hideLogo'];
}
$settings 					= $this->getPluginSettings();
$use_custom_search			= $settings['use_custom_search'];
$custom_search_page			= is_numeric( $settings['custom_search_page'] ) ? $this->getSearchPageUrl( $settings['custom_search_page'] ) :  $settings['custom_search_page'];

$siq_enable_autocomplete	= $settings['disable_autocomplete'] == "yes" ? "" : "yes";
$stylingVar					= $settings['custom_search_page_style'];
$styling					= $this->getStyling($stylingVar);
$siq_menu_selected          = $settings['siq_menu_select_box'];
$siq_menu_select_box_color  = ($settings['siq_menu_select_box_color'] != "") ? $settings['siq_menu_select_box_color'] : "000000";
$siq_menu_select_box_pos_right = ($settings["siq_menu_select_box_pos_right"] != "" && $settings["siq_menu_select_box_pos_right"] != 0) ? $settings["siq_menu_select_box_pos_right"] : 0;

$siq_menu_select_box_pos_top = ($settings["siq_menu_select_box_pos_top"] != "" && $settings["siq_menu_select_box_pos_top"] != 0) ? $settings["siq_menu_select_box_pos_top"] : 0;
$siq_menu_select_box_pos_absolute = $settings["siq_menu_select_box_pos_absolute"];
$siq_menu_select_box_direction = $settings["siq_menu_select_box_direction"];

$sortBy	= ($settings["siq_search_sortby"] != "") ? $settings["siq_search_sortby"] :"RELEVANCE";
$siq_custom_search_page = is_numeric( $settings['custom_search_page'] ) ? $this->getSearchPageUrl( $settings['custom_search_page'] ) :  $settings['custom_search_page'];
$siq_invalid_custom_search_page = true;
$siq_invalid_custom_search_page = !($siq_custom_search_page != "" && $this->is_valid_custom_search_page($siq_custom_search_page));
$defaultThumbnail									  = (!empty($settings["siq_default_thumbnail"])) ? $settings["siq_default_thumbnail"] :"";

$siq_wc_hide_out_of_stock_in_search  = $settings['siq_wc_hide_out_of_stock_in_search'] == "yes" ? "yes" : "";

if($this->getSyncSettingsCalled == 0) {
	$syncSettings = $this->_siq_get_sync_settings();
}else{
	$syncSettings = $this->siqSyncSettings;
}
$openResultInTab = false;
$hideLogo				 = false;
if(!empty($syncSettings) && is_array($syncSettings) && count($syncSettings) > 0){
	if(array_key_exists('openResultInTab', $syncSettings)){
		$openResultInTab = ($syncSettings['openResultInTab'] === true);
	}
	if(array_key_exists('hideLogo', $syncSettings)){
		$hideLogo = ($syncSettings['hideLogo'] === true);
	}
}
$forceLoadSettings = !empty($settings['siq_forceLoadSettings']) ? $settings['siq_forceLoadSettings'] : false;
?>
<div class="wsplugin">
	<h2>SearchIQ: Plugin options <a class="helpSign userGuide" target="_blank" style="text-decoration: none" href="<?php echo $this->userGuideLink;?>"><img style="vertical-align:bottom" src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/help-icon.png"> User Guide</a></h2>
	<div class="dwAdminHeading">You can set options for the plugin on this page.</div>
	<form method="POST" action="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-2'); ?>" class="custom_page_options" name="custom_options" onSubmit="return checkSiqOptions();">
		<div class="section section-1">
			<h2>General</h2>
			<div class="data">
				<label>Use SearchIQ results page</label>
				<input type="checkbox" name="siq_use_custom_search" value="yes"
					<?php if(isset($use_custom_search) && ($use_custom_search=='yes')){
						echo "checked ='checked'";
					}?>
					/>
			</div>
			<div class="data pageList">
				<label>Result page</label>
				<input type="text" class="textbox large" value="<?php echo $custom_search_page; ?>" id="siq_custom_search_page" name="siq_custom_search_page"/>
				<?php if ($siq_invalid_custom_search_page) { ?>
					<div class="message error">Selected page doesn't have [siq_ajax_search] tag</div>
				<?php } ?>
				<div class="message">Please make sure the selected page has [siq_ajax_search] in content.</div>
			</div>
			<div class="data">
				<label>Open results in new page</label>
				<input type="checkbox" name="siq_openResultInTab" value="true"
					<?php if(isset($openResultInTab) && ($openResultInTab== true)){
						echo "checked ='checked'";
					}?>
					/>
			</div>
			<div class="data">
				<label>Default thumbnail url<br/>
					<small>Url entered here will be used as default thumbnail(in case post thumbnail does not exist) for the search results from SearchIQ.<br/>
						Enter image url  in the text box directly or click on <b>"Select/Upload Thumbnail"</b> button to select image from wordpress media</small></label>
				<input type="text" name="siq_default_thumbnail" class="textbox large"  value="<?php echo $defaultThumbnail;?>"/>
				<input type="button" id="siq_default_thumbnail_uploader" class="btn" value="Select/Upload Thumbnail" />
			</div>
			<?php if($this->allowHideLogo){ ?>
				<div class="data">
					<label>Hide SearchIQ logo</label>
					<input type="checkbox" name="siq_hideLogo" value="true"
						<?php if(isset($hideLogo) && ($hideLogo == true)){
							echo "checked ='checked'";
						}?>
						/>
				</div>
			<?php } ?>
			<div class="data">
				<label>Force Load settings and autocomplete JS</label>
				<input type="checkbox" name="siq_forceLoadSettings" value="true"
					<?php if(isset($forceLoadSettings) && ($forceLoadSettings == true)){
						echo "checked ='checked'";
					}?>
					/>
			</div>
			<h2>Autocomplete</h2>
			<div class="data">
				<label>Enable autocomplete</label>
				<input type="checkbox" name="siq_enable_autocomplete" id="siq_enable_autocomplete" value="yes" <?php echo ($siq_enable_autocomplete == "yes" ? "checked='checked'" : "");?>/>
			</div>
			<div class="data">
				<label>Search box name<br/>
					<small>
						No special characters except `-` or `_` allowed in this field. Any other special characters will be removed automatically.
					</small></label>
				<input type="text" name="siq_searchbox_name" id="siq_searchbox_name" value="<?php echo $this->getSearchboxName();?>"/>
			</div>

			<h2>Search</h2>
			<div class="data">
				<label>
					Search query parameter name<br/>
					<small>This is for custom search page to get the query keyword.<br/>
						No special characters are allowed in this field.<br/>
						<span>It cannot be `s` as this parameter is reserved by wordpress built-in search functionality</span></small>
				</label>
				<div class="inlineRight">
					<input type="text" name="siq_search_query_param_name" id="siq_search_query_param_name" value="<?php echo $this->getSearchQueryParamName();?>"/>
				</div>
			</div>

			<div class="data">
				<label>Search algorithm</label>
				<select id="search-algorithm" name="siq_search_algorithm">
					<?php $searchAlgo = $this->getSearchAlgorithm(); ?>
					<option value="BROAD_MATCH" <?php echo $searchAlgo == "BROAD_MATCH" ? "selected" : "";?>>Broad match</option>
					<option value="EXACT_MATCH" <?php echo $searchAlgo == "EXACT_MATCH" ? "selected" : "";?>>Exact match</option>
					<option value="ALL_TERM_MATCH" <?php echo $searchAlgo == "ALL_TERM_MATCH" ? "selected" : "";?>>All term match</option>
				</select>
			</div>
			<div class="data">
				<label>Sort By</label>
				<select id="search-sort-by" name="siq_search_sortby">
					<option value="RELEVANCE" <?php echo $sortBy == "RELEVANCE" ? "selected" : "";?>>Relevance</option>
					<option value="NEWEST" <?php echo $sortBy == "NEWEST" ? "selected" : "";?>>Newest</option>
				</select>
			</div>
			<h2>Add search to your site</h2>
			<h3><b>Using Shortcode</b></h3>
			<div class="data">
				<div class="">SearchIQ searchbox shortcode can be used on all pages, posts and sidebars</div><br/>
				<code>[siq_searchbox type="search-bar" placeholder="Search here" post-types="post,page" width="500" placement="left"]</code><br/>
				<div class="">In the shortcode all the fields i.e. type, placeholder, post-types, width and placement are optional.</div><br/>
				<div class="">More information on the shortcode options can be found <a href="<?php echo $this->userGuideLink;?>#guide-shortcode" target="_blank">here</a></div><br/>
			</div>
			<h3><b>Using Widget</b></h3>
			<div class="data">
				<div>Searchiq search widget is availabe on widgets screen which can be reached from <a href="<?php echo get_admin_url();?>widgets.php"><b>here</b></a>
					<br/>See image below
				</div><br/>
				<div class="helpWrap">
					<img src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/search-box-widget.png" />
				</div>
			</div>
			<h3><b>Using Icon in menu</b></h3>
			<div class="data">
				<label>
					Select the menu you want to add search icon to<br/>
					<small>This will add a search icon at the right side of the menu you select. Clicking the icon will open a search box. See image below</small>
				</label>
				<?php echo $this->getMenuLocationSelectBox('siq_menu_select_box', $siq_menu_selected);?>
				<div class="helpWrap">
					<img src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/search-icon-1.png" />
				</div>
			</div>
			<div class="data">
				<label>Move search icon to extreme right in menu<br/>
					<small>Selecting this will move searchicon to extreme right of menu. See image below</small>
				</label>
				<div class="inlineRight">
					<input type="checkbox" name="siq_menu_select_box_pos_absolute" id="siq_menu_select_box_pos_absolute" <?php echo ($siq_menu_select_box_pos_absolute == 'yes')? "checked='checked'": "";?> value="yes"/>
				</div>
				<div class="helpWrap">
					<img src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/search-icon-extreme-right.png" />
				</div>
			</div>
			<div class="data">
				<label>Search icon direction<br/>
					<small>Select the direction in which search box should open when clicked on search icon</small>
				</label>
				<select name="siq_menu_select_box_direction" id="siq_menu_select_box_direction">
					<?php foreach($this->menuSearchBoxDirection as $k => $v){ ?>
						<?php $varSelected = ($siq_menu_select_box_direction != "" && $siq_menu_select_box_direction == $k) ? "selected='selected'": "" ;?>
						<option <?php echo $varSelected;?> value="<?php echo $k; ?>"><?php echo $v;?></option>
					<?php } ?>
				</select>
			</div>
			<div class="data">
				<label>Search icon color</label>
				<input value="<?php echo $siq_menu_select_box_color; ?>" name="siq_menu_select_box_color" class="color"/>
				<div class="clearColor"><span></span></div>
			</div>
			<div class="data">
				<label>Position from right<br/>
					<small>Position of search icon from right. Can be negative also. See image below</small>
				</label>
				<div class="inlineRight">
					<input type="text" name="siq_menu_select_box_pos_right" id="siq_menu_select_box_pos_right" class="textbox small" value="<?php echo $siq_menu_select_box_pos_right;?>"/>px
				</div>
				<div class="helpWrap">
					<img src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/search-icon-right.png" />
				</div>
			</div>
			<div class="data">
				<label>Position from top<br/>
					<small>Position of search icon from top. Can be negative also. See image below</small>
				</label>
				<div class="inlineRight">
					<input type="text" name="siq_menu_select_box_pos_top" id="siq_menu_select_box_pos_top" class="textbox small" value="<?php echo $siq_menu_select_box_pos_top;?>"/>px
				</div>
				<div class="helpWrap">
					<img src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/search-icon-top.png" />
				</div>
			</div>
			<?php if($this->woocommerceActive){ ?> 
			<h2>Woocommerce</h2>
			<div class="data">
					<label>Hide out of stock products in search results</label>
				<input type="checkbox" name="siq_wc_hide_out_of_stock_in_search" id="siq_wc_hide_out_of_stock_in_search" value="yes" <?php echo ($siq_wc_hide_out_of_stock_in_search == "yes" ? "checked='checked'" : "");?>/>
				 </span>
			</div>
			<?php } ?>
			<h2>Custom CSS</h2>
			<div class="data">
				<label>Add your custom CSS here</label>
			 <span style="display: inline-block;">
				 <textarea id="customCssTextarea" name="stylingCustomCss" style="background-color:#fff!important; width:400px; height:300px;"><?php echo $styling['customCss']; ?></textarea>
				 <i>Add your custom css in this box without &lt;style&gt; tag</i><br/>
			 </span>
			</div>
		</div>
		<div class="section submit">
			<div class="data">
				<input type="submit" name="btnSubmitOptions" id="btnSubmitOptions" value="Save" class="btn">
				<?php wp_nonce_field( $this->updateOptionsNonce );?>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$jQu	= jQuery;
	function checkSiqOptions(){
		$jQu('select[name*="siq_custom_search_page"]').parent().find($jQu('.msg')).remove();
		if($jQu('input[name*="siq_use_custom_search"]').is(":checked") && $jQu('select[name*="siq_custom_search_page"]').val() == ""){
			$jQu('<div class="msg">Please select a page</div>').insertAfter($jQu('select[name*="siq_custom_search_page"]'));
			return false;
		}
		if ($jQu("#siq_options input.errorElement, #siq_options select.errorElement").size() > 0) {
			return false;
		}
	}
</script>
