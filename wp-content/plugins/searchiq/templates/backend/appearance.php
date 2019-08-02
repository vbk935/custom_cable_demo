<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!empty( $_POST ) && isset($_POST['btnSubmitStyleOptions']) && check_admin_referer($this->updateAppearanceNonce)){
    $siq_styling						= 	$_POST['styling'];
    $siq_styling_						= array();
    if(count($siq_styling) > 0){
        foreach($siq_styling as $k => $v){
            $siq_styling_[$k] = sanitize_text_field($v);
        }
    }
    $error=array();
                
    $this->setCustomSearchNumRecords(sanitize_text_field($_POST['custom_search_num_records']));

    update_option($this->pluginOptions['custom_search_bar_placeholder'], sanitize_text_field($_POST['custom_search_bar_placeholder']));

    if(isset($siq_styling_) && count($siq_styling_) > 0){
                    $settings 		= $this->getPluginSettings();
                    $stylingVar		= $settings['custom_search_page_style'];
                    $styling		= $this->getStyling($stylingVar);
                    $siq_styling_['customCss'] = 	$styling['customCss'];
        update_option($this->pluginOptions['custom_search_page_style'], $siq_styling_);
    }
    $siq_use_custom_images			=	!empty($_POST['siq_use_custom_images']) ? sanitize_text_field($_POST['siq_use_custom_images']) : "";
    $customSearchResultsInfoText =  ( isset($_POST['customSearchResultsInfoText']) && $_POST['customSearchResultsInfoText'] != "")? $_POST['customSearchResultsInfoText'] : "Showing ##offset## to ##limit## of ##total## Results";
    update_option($this->pluginOptions['customSearchResultsInfoText'], $customSearchResultsInfoText);

    $customSearchResultsOrderRelevanceText =   ( isset($_POST['customSearchResultsOrderRelevanceText']) && $_POST['customSearchResultsOrderRelevanceText'] != "")? $_POST['customSearchResultsOrderRelevanceText'] : "Relevance";
    update_option($this->pluginOptions['customSearchResultsOrderRelevanceText'], $customSearchResultsOrderRelevanceText);

    $customSearchResultsOrderNewestText =   ( isset($_POST['customSearchResultsOrderNewestText']) && $_POST['customSearchResultsOrderNewestText'] != "")? $_POST['customSearchResultsOrderNewestText'] : "Newest";
    update_option($this->pluginOptions['customSearchResultsOrderNewestText'], $customSearchResultsOrderNewestText);

    $customSearchResultsOrderOldestText =  ( isset($_POST['customSearchResultsOrderOldestText']) && $_POST['customSearchResultsOrderOldestText'] != "")? $_POST['customSearchResultsOrderOldestText'] : "Oldest";
    update_option($this->pluginOptions['customSearchResultsOrderOldestText'], $customSearchResultsOrderOldestText);

    $noRecordsFoundText =  ( isset($_POST['noRecordsFoundText']) && $_POST['noRecordsFoundText'] != "")? $_POST['noRecordsFoundText']: "No records found";
    update_option($this->pluginOptions['noRecordsFoundText'], $noRecordsFoundText);

    $paginationPrevText =  ( isset($_POST['paginationPrevText']) && $_POST['paginationPrevText'] != "")? $_POST['paginationPrevText']: "Prev";
    update_option($this->pluginOptions['paginationPrevText'], $paginationPrevText);

    $paginationNextText =  ( isset($_POST['paginationNextText']) && $_POST['paginationNextText'] != "")? $_POST['paginationNextText']: "Next";
    update_option($this->pluginOptions['paginationNextText'], $paginationNextText);

    $showAuthorAndDate = isset($_POST['custom_page_display_author']) && $_POST['custom_page_display_author'] ? "1" : "0";
    update_option($this->pluginOptions['custom_page_display_author'], $showAuthorAndDate);
    $showCategory = isset($_POST['custom_page_display_category']) && $_POST['custom_page_display_category'] ? "1" : "0";
    update_option($this->pluginOptions['custom_page_display_category'], $showCategory);
    $showTag = isset($_POST['custom_page_display_tag']) && $_POST['custom_page_display_tag'] ? "1" : "0";
    update_option($this->pluginOptions['custom_page_display_tag'], $showTag);

    if($siq_use_custom_images !=""){
        update_option($this->pluginOptions['show_search_page_images'],$siq_use_custom_images);
    }else{
        delete_option($this->pluginOptions['show_search_page_images']);
    }

    if (isset($_POST['siqResultPageLayout'])) {
        update_option($this->pluginOptions['resultPageLayout'], $_POST['siqResultPageLayout'] === self::RP_LAYOUT_GRID ? self::RP_LAYOUT_GRID : self::RP_LAYOUT_LIST);
    } else {
        delete_option($this->pluginOptions['resultPageLayout']);
    }

	$siq_displayContentFromStart			=	!empty($_POST['siq_displayContentFromStart']) ? sanitize_text_field($_POST['siq_displayContentFromStart']) : "";
	if($siq_displayContentFromStart !=""){
        update_option($this->pluginOptions['siq_displayContentFromStart'],$siq_displayContentFromStart);
    }else{
        delete_option($this->pluginOptions['siq_displayContentFromStart']);
    }		
    $siq_resultPageShowPostLink = isset($_POST['siq_resultPageShowPostLink']) && $_POST['siq_resultPageShowPostLink'] ? 1 : self::DEFAULT_POST_URL_ENABLED;
    update_option($this->pluginOptions['siq_resultPageShowPostLink'], $siq_resultPageShowPostLink);
    
    $siq_resultPageCustomSearchItemLinkFontSize = isset($_POST['siq_resultPageCustomSearchItemLinkFontSize']) && $_POST['siq_resultPageCustomSearchItemLinkFontSize'] ? $_POST['siq_resultPageCustomSearchItemLinkFontSize'] : self::DEFAULT_POST_URL_FONT_SIZE;
    update_option($this->pluginOptions['siq_resultPageCustomSearchItemLinkFontSize'], $siq_resultPageCustomSearchItemLinkFontSize);
    
    $siq_resultPageCustomSearchItemLinkColor = isset($_POST['siq_resultPageCustomSearchItemLinkColor']) && $_POST['siq_resultPageCustomSearchItemLinkColor'] ? $_POST['siq_resultPageCustomSearchItemLinkColor'] : self::DEFAULT_POST_URL_FONT_COLOR;
    update_option($this->pluginOptions['siq_resultPageCustomSearchItemLinkColor'], $siq_resultPageCustomSearchItemLinkColor);
    
    $this->_siq_sync_settings();
	
}

$settings   = $this->getPluginSettings();
$stylingVar = $settings['custom_search_page_style'];
$styling    = $this->getStyling($stylingVar);

$customSearchResultsInfoText =  !empty($settings['customSearchResultsInfoText']) ? $settings['customSearchResultsInfoText'] : "Showing ##offset## to ##limit## of ##total## Results";
$customSearchResultsOrderRelevanceText =  !empty($settings['customSearchResultsOrderRelevanceText']) ? $settings['customSearchResultsOrderRelevanceText'] : "Relevance";
$customSearchResultsOrderNewestText =  !empty($settings['customSearchResultsOrderNewestText']) ? $settings['customSearchResultsOrderNewestText'] : "Newest";
$customSearchResultsOrderOldestText =  !empty($settings['customSearchResultsOrderOldestText']) ? $settings['customSearchResultsOrderOldestText'] : "Oldest";
$noRecordsFoundText =  !empty($settings['noRecordsFoundText']) ? $settings['noRecordsFoundText'] : "No records found";
$paginationPrevText =  !empty($settings['paginationPrevText']) ? $settings['paginationPrevText'] : "Prev";
$paginationNextText =  !empty($settings['paginationNextText']) ? $settings['paginationNextText'] : "Next";
$siq_use_custom_images	= $settings['show_search_page_images'];
?>
<div class="wsplugin">
 <h2>SearchIQ: Results Page <a class="helpSign userGuide" target="_blank" style="text-decoration: none" href="<?php echo $this->userGuideLink;?>"><img style="vertical-align:bottom" src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/help-icon.png"> User Guide</a></h2>
 <div class="dwAdminHeading">You can change appearance of custom ajax results page here. You can use the textbox on right to see the results.</div>
 <form method="POST" action="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-3'); ?>" class="custom_page_options siq-styling-form" id="siq-results-styling-form" name="custom_options" onSubmit="">
     <div class="section section-0">
         <h2>Search</h2>
         <?php if ($this->pluginSettings['facets_enabled']) { ?>
             <div class="data">
                 <label>Default result page layout</label>
                 <select name="siqResultPageLayout">
                     <option value="LIST" <?php echo !isset($settings['resultPageLayout']) || $settings['resultPageLayout'] !== "GRID" ? "selected" : "";?>>list</option>
                     <option value="GRID" <?php echo isset($settings['resultPageLayout']) && $settings['resultPageLayout'] === "GRID" ? "selected" : "";?>>grid</option>
                 </select>
             </div>
         <?php  } ?>
         <div class="data">
             <label>Show images in search results</label>
             <input type="checkbox" name="siq_use_custom_images" id="siq_use_custom_images" value="yes"
                 <?php if(isset($siq_use_custom_images) && ($siq_use_custom_images=='yes')){
                     echo "checked ='checked'";
                 }?>
             />
         </div>
         <div class="data">
             <label>Display Content From Start</label>
             <input type="checkbox" name="siq_displayContentFromStart" id="siq_displayContentFromStart" value="yes"
                 <?php if(isset($siq_displayContentFromStart) && ($siq_displayContentFromStart=='yes')){
                     echo "checked ='checked'";
                 }?>
             /><br/>
             <small>Select this if you want to show content from start of text in search results irrespective of matched text</small>
         </div>
         <div class="data">
             <label>Number records per page</label>
             <input value="<?php echo $this->getCustomSearchNumRecords();?>" name="custom_search_num_records"/>
	         <br/>
	         <small>
		         You need to click on submit button in order to see results for this change.
	         </small>
         </div>
		  <div class="data">
             <label class="full">Search info <small>(available tags ##offset##, ##limit##, ##total##)</small></label>  <br/>
             <input class="long longest" value="<?php echo $customSearchResultsInfoText;?>" name="customSearchResultsInfoText"/>
         </div>
		 
		 <div class="data">
             <label class="">Order name for "Relevance"</label>
             <input class="" value="<?php echo $customSearchResultsOrderRelevanceText;?>" name="customSearchResultsOrderRelevanceText"/>
         </div>
		 
		 <div class="data">
             <label class="">Order name for "Newest"</label>
             <input class="" value="<?php echo $customSearchResultsOrderNewestText;?>" name="customSearchResultsOrderNewestText"/>
         </div>
		 
		 <div class="data">
             <label class="">Order name for "Oldest"</label>
             <input class="" value="<?php echo $customSearchResultsOrderOldestText;?>" name="customSearchResultsOrderOldestText"/>
         </div>
		 
		 <div class="data">
             <label class="">No Records Found Message</label>
             <input class="" value="<?php echo $noRecordsFoundText;?>" name="noRecordsFoundText"/>
         </div>
     </div>
     <div class="section section-1">
         <h2>Search Bar</h2>
         <div class="data">
             <label>Placeholder</label>
             <input name="custom_search_bar_placeholder" class="long" value="<?php echo $settings['custom_search_bar_placeholder'] ? $settings['custom_search_bar_placeholder'] : self::DEFAULT_CUSTOM_SEARCH_BAR_PLACEHOLDER;?>"/>
         </div>
         <div class="data">
             <label>Background</label>
             <input value="<?php echo $styling['resultSearchBarBackground'];?>" name="styling[resultSearchBarBackground]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data">
             <label>Text color</label>
             <input value="<?php echo $styling['resultSearchBarColor'];?>" name="styling[resultSearchBarColor]" class="color {required:false}"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data">
             <label>Powered By text color</label>
             <input value="<?php echo $styling['resultSearchBarPoweredByColor'];?>" name="styling[resultSearchBarPoweredByColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
     </div>
        <div class="section section-2">
		<h2>Single Result Box</h2>
		<div class="data">
			<label>Background color</label>
			<input value="<?php echo $styling['resultBoxBg']; ?>" name="styling[resultBoxBg]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	<div class="section section-3">
		<h2>Result Title</h2>
		<div class="data">
			<label>Font size</label>
			<?php echo $this->getFontSizeBox('styling[resultTitleFontSize]', $styling['resultTitleFontSize']); ?>
		
		</div>
		<div class="data">
			<label>Text Color</label>
			<input value="<?php echo $styling['resultTitleColor']; ?>" name="styling[resultTitleColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	<div class="section section-4">
		<h2>Author & Date</h2>
                <div class="data">
                    <label>Enable</label>
                    <input type="checkbox" <?php echo $settings["custom_page_display_author"] === "0" ? "" : "checked";?> name="custom_page_display_author"/>
                </div>
		<div class="data">
			<label>Font size</label>
			<?php echo $this->getFontSizeBox('styling[resultAuthDateFontSize]', $styling['resultAuthDateFontSize']); ?>
		
		</div>
		
		<div class="data">
			<label>Text Color</label>
			<input value="<?php echo $styling['resultAuthDateColor']; ?>" name="styling[resultAuthDateColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	<div class="section section-5">
		<h2>Body Text</h2>
		<div class="data">
			<label>Font size</label>
			<?php echo $this->getFontSizeBox('styling[resultTextFontSize]', $styling['resultTextFontSize']); ?>
		
		</div>
		<div class="data">
			<label>Text color</label>
			<input value="<?php echo $styling['resultTextColor']; ?>" name="styling[resultTextColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	
<!--		<div class="data">-->
<!--			<label>Highlight font size</label>-->
<!--			--><?php //echo $this->getFontSizeBox('styling[resultTextHlFontSize]', $styling['resultTextHlFontSize']); ?>
<!--		-->
<!--		</div>-->
<!--		<div class="data">-->
<!--			<label>Highlight text color</label>-->
<!--			<input value="--><?php //echo $styling['resultTextHlColor']; ?><!--" name="styling[resultTextHlColor]" class="color"/>-->
<!--			<div class="clearColor"><span></span></div>-->
<!--		</div>-->
	</div>
	<div class="section section-6">
		<h2>Category</h2>
                <div class="data">
                    <label>Enable</label>
                    <input type="checkbox" <?php echo $settings["custom_page_display_category"] === "0" ? "" : "checked";?> name="custom_page_display_category"/>
                </div>
		<div class="data">
			<label>Title font size</label>
			<?php echo $this->getFontSizeBox('styling[resultCatTitleFontSize]', $styling['resultCatTitleFontSize']); ?>
		
		</div>
		<div class="data">
			<label>Title text color</label>
			<input value="<?php echo $styling['resultCatTitleColor']; ?>" name="styling[resultCatTitleColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
		<div class="data">
			<label>Background color</label>
			<input value="<?php echo $styling['resultCatBgColor']; ?>" name="styling[resultCatBgColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	<div class="section section-7">
		<h2>Tags</h2>
                <div class="data">
                    <label>Enable</label>
                    <input type="checkbox" <?php echo $settings["custom_page_display_tag"] === "0" ? "" : "checked";?> name="custom_page_display_tag"/>
                </div>
		<div class="data">
			<label>Font size</label>
			<?php echo $this->getFontSizeBox('styling[resultTagFontSize]', $styling['resultTagFontSize']); ?>
		
		</div>
		<div class="data">
			<label>Text color</label>
			<input value="<?php echo $styling['resultTagColor']; ?>" name="styling[resultTagColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	  
     <div class="section section-8-1">     
         <h2>Display post URL on search result page</h2>
		 <div class="data">
                    <label>Enable</label>
                    <input type="checkbox" <?php echo $settings["siq_resultPageShowPostLink"] === 0 ? "" : "checked";?> name="siq_resultPageShowPostLink" value="1"/>
                </div>
         <div class="data">
             <label>URL Font Size</label>
             <?php echo $this->getFontSizeBox('siq_resultPageCustomSearchItemLinkFontSize', $settings['siq_resultPageCustomSearchItemLinkFontSize']); ?>
         </div>
         <div class="data without-line">
             <label>URL Font Color</label>
             <input type="text" value="<?php echo $settings['siq_resultPageCustomSearchItemLinkColor'];?>" name="siq_resultPageCustomSearchItemLinkColor" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
     </div>
     
     <div class="section section-8">
         <h2>Pagination</h2>
		 <div class="data">
             <label class="">"Prev" label</label>
             <input class="" value="<?php echo $paginationPrevText;?>" name="paginationPrevText"/>
         </div>
		 <div class="data">
             <label class="">"Next" label</label>
             <input class="" value="<?php echo $paginationNextText;?>" name="paginationNextText"/>
         </div>
         <div class="data">
             <label>Font size</label>
             <?php echo $this->getFontSizeBox('styling[paginationFontSize]', $styling['paginationFontSize']); ?>
         </div>
         <div class="data without-line">
             <label>Selected page background</label>
             <input type="text" value="<?php echo $styling['paginationCurrentBackground'];?>" name="styling[paginationCurrentBackground]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data without-line">
             <label>Selected page text color</label>
             <input type="text" value="<?php echo $styling['paginationCurrentColor'];?>" name="styling[paginationCurrentColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data">
             <label>Selected page border color</label>
             <input type="text" value="<?php echo $styling['paginationCurrentBorderColor'];?>" name="styling[paginationCurrentBorderColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data without-line">
             <label>Other links background</label>
             <input type="text" value="<?php echo $styling['paginationActiveBackground'];?>" name="styling[paginationActiveBackground]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data without-line">
             <label>Other links text color</label>
             <input type="text" value="<?php echo $styling['paginationActiveColor'];?>" name="styling[paginationActiveColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data">
             <label>Other links border color</label>
             <input type="text" value="<?php echo $styling['paginationActiveBorderColor'];?>" name="styling[paginationActiveBorderColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data without-line">
             <label>Inactive links background</label>
             <input type="text" value="<?php echo $styling['paginationInactiveBackground'];?>" name="styling[paginationInactiveBackground]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data without-line">
             <label>Inactive links text color</label>
             <input type="text" value="<?php echo $styling['paginationInactiveColor'];?>" name="styling[paginationInactiveColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
         <div class="data">
             <label>Inactive links border color</label>
             <input type="text" value="<?php echo $styling['paginationInactiveBorderColor'];?>" name="styling[paginationInactiveBorderColor]" class="color"/>
             <div class="clearColor"><span></span></div>
         </div>
     </div>
   
	<div class="section submit">
            <div class="data">
                    <input type="submit" name="btnSubmitStyleOptions" id="btnSubmitStyleOptions" value="Submit" class="btn">
                    <input type="button" name="btnClearStyle" id="btnClearStyle" value="Reset Style to default" class="btn">
                    <?php wp_nonce_field( $this->updateAppearanceNonce );?>
            </div>
	</div>
 </form>
 <div class="siq-styling-preview">
	 <div class="preview-heading">Preview</div>
     <div id="siq_search_results"></div>
     <?php $this->includeContainerScript(true); ?>
 </div>
 <div class="clearfix"></div>
</div>
<script type="text/javascript">
    (function($){
        $(window).load( function () {
          setTimeout(function() {
            var updateSearchResultsPreview = function() {
                var displayDate = $("#siq-results-styling-form [name='custom_page_display_author']").is(":checked"),
                        displayCat = $("#siq-results-styling-form [name='custom_page_display_category']").is(":checked"),
                        displayTag = $("#siq-results-styling-form [name='custom_page_display_tag']").is(":checked");
                $("#siq_search_results .siq_searchForm .siq_searchWrapper .siq_searchTop .siq_searchInner .siq_searchBox").css({
                    "backgroundColor": $("#siq-results-styling-form [name='styling[resultSearchBarBackground]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultSearchBarBackground]']").val() : "",
                    "color": $("#siq-results-styling-form [name='styling[resultSearchBarColor]']").val()? "#" + $("#siq-results-styling-form [name='styling[resultSearchBarColor]']").val():""
                });
                var noimg = '<div class="search-results-L has-image" style="background:#ddd; border-radius:10px;"></div>';
                var imageStatus =  $("#siq-results-styling-form [name='siq_use_custom_images']").is(":checked");

				$("#siq_search_results .search-results-row").each(function () {
                        var imageTdHasImg = $(this).find(".imageTd.has-image");
                        var imageTdNoImg = $(this).find(".imageTd.no-image");
                        var imageTd 				= $(this).find(".imageTd");
						var noImgSize 			= imageTd.find(".search-results-L");
						if(imageStatus == false){
							if(imageTdHasImg.size() > 0  || imageTdNoImg.size() > 0){
								imageTd.hide();
							}
						}else{
							if((noImgSize.size() == 0)){
								imageTd.append(noimg);
							}
							imageTd.show();
						}
                    });
					
                var infoText = $("#siq_search_results .siq_searcharea_table .siq-show-result").html();
                var totalCount = (infoText != "undefined" && infoText != null && infoText != "") && infoText.match(/([0-9,]+)/g) ? infoText.match(/([0-9,]+)/g) : "";
                var infoTextChange =  $("#siq-results-styling-form [name='customSearchResultsInfoText']").val();

                if(typeof totalCount == "object" && totalCount.length > 0){
                    var offset = (typeof totalCount[0] != "undefined") ? "<span>"+totalCount[0]+"</span>" : "";
                    var limit = (typeof totalCount[1] != "undefined") ? "<span>"+totalCount[1]+"</span>" : "";
                    var total = (typeof totalCount[2] != "undefined") ? "<span>"+totalCount[2]+"</span>" : "";
                    infoTextChange = infoTextChange.replace("##offset##",offset);
                    infoTextChange = infoTextChange.replace("##limit##",limit);
                    infoTextChange = infoTextChange.replace("##total##",total);
                    $("#siq_search_results .siq_searcharea_table .siq-show-result").html("<div>"+infoTextChange+"</div>");
                }

                if($("#siq_search_results .siq_message.siq_error").length > 0){
                    $("#siq_search_results .siq_message.siq_error").text(
                        $("#siq-results-styling-form [name='noRecordsFoundText']").val()
                    );
                }

                $("#siq_search_results .siq_searcharea_table .siq_filters .filterValue a[alt='relevance']").text(
                    $("#siq-results-styling-form [name='customSearchResultsOrderRelevanceText']").val()
                );
                $("#siq_search_results .siq_searcharea_table .siq_filters .filterValue a[alt='newest']").text(
                    $("#siq-results-styling-form [name='customSearchResultsOrderNewestText']").val()
                );
                $("#siq_search_results .siq_searcharea_table .siq_filters .filterValue a[alt='oldest']").text(
                    $("#siq-results-styling-form [name='customSearchResultsOrderOldestText']").val()
                );
                var placeholder = $("#siq-results-styling-form [name='custom_search_bar_placeholder']").val() || "Enter Your Search Term";
                $("#siq_search_results .siq_searchForm .siq_searchWrapper .siq_searchTop .siq_searchInner .siq_searchBox").attr("placeholder", placeholder);
                $("#siq_search_results .srch-poweredbysiq div, #siq_search_results .srch-poweredbysiq div a").css({
                    "color": $("#siq-results-styling-form [name='styling[resultSearchBarPoweredByColor]']").val()?"#" + $("#siq-results-styling-form [name='styling[resultSearchBarPoweredByColor]']").val():""
                });
                $("#siq_search_results .search-results-row, #siq_search_results div.siq-prodfacet-contR div.siq-prdrslts-row div.siq-prdrslts-box").css({
                    "backgroundColor": $("#siq-results-styling-form [name='styling[resultBoxBg]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultBoxBg]']").val() : ""
                });
                $("body #siq_search_results .search-results-R div.search-results-title a, body #siq_search_results .siq-ads h2.srch-sponsored-title a, #siq_search_results div.siq-prodfacet-contR div.siq-prdrslts-row div.siq-prdrslts-box a.siq-prdbx div.siq-prdtls h3").css({
                    "fontSize": $("#siq-results-styling-form [name='styling[resultTitleFontSize]']").val() + "px",
                    "lineHeight": (parseInt($("#siq-results-styling-form [name='styling[resultTitleFontSize]']").val() * 1.2)) + "px",
                    "color": $("#siq-results-styling-form [name='styling[resultTitleColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultTitleColor]']").val() : ""
                });
                $("#siq_search_results .search-results-R div.sr-R-author").css({
                    "fontSize": $("#siq-results-styling-form [name='styling[resultAuthDateFontSize]']").val() + "px",
                    "lineHeight": (parseInt($("#siq-results-styling-form [name='styling[resultAuthDateFontSize]']").val() * 1.2)) + "px",
                    "color": $("#siq-results-styling-form [name='styling[resultAuthDateColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultAuthDateColor]']").val() : "",
                    "display": displayDate ? "inline-block" : "none"
                });
                $("#siq_search_results .search-results-R .sr-R-cont div").css({
                    "fontSize": $("#siq-results-styling-form [name='styling[resultTextFontSize]']").val() + "px",
                    "lineHeight": (parseInt($("#siq-results-styling-form [name='styling[resultTextFontSize]']").val() * 1.2)) + "px",
                    "color": $("#siq-results-styling-form [name='styling[resultTextColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultTextColor]']").val() : ""
                });
                $("#siq_search_results .search-results-R .sr-R-categories ul li").css({
                    "fontSize": $("#siq-results-styling-form [name='styling[resultCatTitleFontSize]']").val() + "px",
                    "lineHeight": (parseInt($("#siq-results-styling-form [name='styling[resultCatTitleFontSize]']").val() * 1.2)) + "px",
                    "color": $("#siq-results-styling-form [name='styling[resultCatTitleColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultCatTitleColor]']").val() : "",
                    "backgroundColor": $("#siq-results-styling-form [name='styling[resultCatBgColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultCatBgColor]']").val() : ""
                });
                $("#siq_search_results .search-results-R .sr-R-categories").css({
                    "display": displayCat ? "inline-block" : "none"
                });
                $("#siq_search_results .search-results-R .sr-R-tags ul li").css({
                    "fontSize": $("#siq-results-styling-form [name='styling[resultTagFontSize]']").val() + "px",
                    "lineHeight": (parseInt($("#siq-results-styling-form [name='styling[resultTagFontSize]']").val() * 1.2)) + "px",
                    "color": $("#siq-results-styling-form [name='styling[resultTagColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[resultTagColor]']").val() : ""
                });
                $("#siq_search_results .search-results-R .sr-R-tags").css({
                    "display": displayTag ? "inline-block" : "none"
                });
                $("#siq_search_results ._siq_pagination a, #siq_search_results ._siq_pagination span").css({
                    "fontSize": $("#siq-results-styling-form [name='styling[paginationFontSize]']").val() + "px",
                    "lineHeight": (parseInt($("#siq-results-styling-form [name='styling[paginationFontSize]']").val() * 1.2)) + "px"
                });
                $("#siq_search_results ._siq_pagination a").css({
                    "backgroundColor": $("#siq-results-styling-form [name='styling[paginationActiveBackground]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationActiveBackground]']").val() : "",
                    "color": $("#siq-results-styling-form [name='styling[paginationActiveColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationActiveColor]']").val() : "",
                    "borderColor": $("#siq-results-styling-form [name='styling[paginationActiveBorderColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationActiveBorderColor]']").val() : ""
                });
                $("#siq_search_results ._siq_pagination span.current").css({
                    "backgroundColor": $("#siq-results-styling-form [name='styling[paginationCurrentBackground]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationCurrentBackground]']").val() : "",
                    "color": $("#siq-results-styling-form [name='styling[paginationCurrentColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationCurrentColor]']").val() : "",
                    "borderColor": $("#siq-results-styling-form [name='styling[paginationCurrentBorderColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationCurrentBorderColor]']").val() : ""
                });
                $("#siq_search_results ._siq_pagination span.disabled").css({
                    "backgroundColor": $("#siq-results-styling-form [name='styling[paginationInactiveBackground]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationInactiveBackground]']").val() : "",
                    "color": $("#siq-results-styling-form [name='styling[paginationInactiveColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationInactiveColor]']").val() : "",
                    "borderColor": $("#siq-results-styling-form [name='styling[paginationInactiveBorderColor]']").val() ? "#" + $("#siq-results-styling-form [name='styling[paginationInactiveBorderColor]']").val() : ""
                });
                
                $("#siq_search_results .search-results-R .srch-res-info").each(function() {
                    var hasDate = $(this).find(".sr-R-author").size() > 0,
                        hasCat = $(this).find(".sr-R-categories").size() > 0,
                        hasTag = $(this).find(".sr-R-tags").size() > 0,
                        show = hasDate && displayDate || hasCat && displayCat || hasTag && displayTag;
                    $(this).css("display", show ? "block" : "none");
                })
            };
            $("#siq-results-styling-form input, #siq-results-styling-form select").on("change keyup", updateSearchResultsPreview);
            $("#siq-results-styling-form .clearColor").click(function() {
                setTimeout(updateSearchResultsPreview);
            });
            updateSearchResultsPreview();
            setInterval(updateSearchResultsPreview, 500);
            
            $("#siq_search_results").parent().parent().css({"position": "relative"});
            var fixPreviewBlockPosition = function() {
                if (!$("#siq_search_results").parent().parent().parent().hasClass("selected")) return;
                if ($('#siq-results-styling-form').height() < $('#siq-results-styling-form').next().height()) {
                    $("#siq_search_results").parent().css({"position": "static", "width": "47%", "marginLeft": "3%"});
                    return;
                }
                $("#siq_search_results").parent().css({"position": "static", "width": "47%", "marginLeft": "3%"});
                var offset = $("#siq_search_results").parent().offset();
                var scrollTop = $(window).scrollTop();
                var clientOffsetTop = offset.top - scrollTop;
                var height = $("#siq_search_results").parent().outerHeight();
                var $form = $("#siq_search_results").parent().parent().find("form");
                var bottom = $form.find(".section.submit").outerHeight(true) + 20;
                var maxOffset = $form.offset().top + $form.outerHeight(true) - height - bottom;
                var width = $("#siq_search_results").parent().outerWidth(false);
                if (clientOffsetTop < 50 && scrollTop + 50 <= maxOffset) {
                    $("#siq_search_results").parent().css({position: "fixed", top: "50px", bottom: "auto", width: width, right: "auto", left: offset.left, marginLeft: 0});
                } else if (scrollTop + 50 > maxOffset) {
                    $("#siq_search_results").parent().css({position: "absolute", top: "auto", bottom: bottom, width: width, right: "0px", left: "auto", marginLeft: 0});
                } else {
                    $("#siq_search_results").parent().css({position: "static", top: "auto", bottom: "auto", width: "47%", right: 0, marginLeft: "3%"});
                }
            };
            $(window).scroll(fixPreviewBlockPosition);
            setTimeout(fixPreviewBlockPosition, 200);
          }, 500);
        });
    })($jQu);
</script>
