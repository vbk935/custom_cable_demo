<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!empty( $_POST ) && isset($_POST['btnSubmitAutocompleteStyleOptions']) && check_admin_referer($this->updateAutocompleteNonce)){
		$siq_styling						= 	$_POST['styling'];
		$siq_styling_						= array();
		if(count($siq_styling) > 0){
			foreach($siq_styling as $k => $v){
				$siq_styling_[$k] = sanitize_text_field($v);
				if ($k == "autocompleteWidth" && $siq_styling_[$k] != "auto" && intval($siq_styling_[$k]) <= 0) {
					$siq_styling_[$k] = "auto";
				}
			}
		}
		$error=array();
        $siq_use_autocomplete_images	=	(!empty($_POST['siq_use_autocomplete_images'])) ? sanitize_text_field($_POST['siq_use_autocomplete_images']): "";
		if(isset($siq_styling_) && count($siq_styling_) > 0){			
			update_option($this->pluginOptions['autocomplete_style'], $siq_styling_);
		}

		if (!empty($_POST['autocompleteNumRecords'])) {
			$siq_autocompleteNumRecords = sanitize_text_field($_POST['autocompleteNumRecords']);
			update_option($this->pluginOptions['autocomplete_num_records'], intval($siq_autocompleteNumRecords) > 0 ? $siq_autocompleteNumRecords : $this->autocompleteDefaultNumRecords);
		}
                
        $autocomplete_text_results = trim(sanitize_text_field($_POST['autocomplete_text_results']));
        if (trim($autocomplete_text_results) == false) $autocomplete_text_results = $this->defaultAutocompleteTextResults;
        update_option($this->pluginOptions['autocomplete_text_results'], $autocomplete_text_results);

        $autocomplete_text_poweredBy = trim(sanitize_text_field($_POST['autocomplete_text_poweredBy']));
        if (trim($autocomplete_text_poweredBy) == false) $autocomplete_text_poweredBy = $this->defaultAutocompleteTextPoweredBy;
        update_option($this->pluginOptions['autocomplete_text_poweredBy'], $autocomplete_text_poweredBy);

        $autocomplete_text_moreLink = trim(sanitize_text_field($_POST['autocomplete_text_moreLink']));
        if (trim($autocomplete_text_moreLink) == false) $autocomplete_text_moreLink = $this->defaultAutocompleteTextMoreLink;
        update_option($this->pluginOptions['autocomplete_text_moreLink'], $autocomplete_text_moreLink);

        if($siq_use_autocomplete_images !=""){
            update_option($this->pluginOptions['show_autocomplete_images'],$siq_use_autocomplete_images);
        }else{
            delete_option($this->pluginOptions['show_autocomplete_images']);
        }
		
        $this->_siq_sync_settings();
	
}

$settings 					= $this->getPluginSettings();
$stylingVar					= $settings['autocomplete_style'];
$styling					= $this->getAutocompleteStyling($stylingVar);
$siq_use_autocomplete_images	= $settings['show_autocomplete_images'];

 ?>
<div class="wsplugin">
 <h2>SearchIQ: Autocomplete <a class="helpSign userGuide" target="_blank" style="text-decoration: none" href="<?php echo $this->userGuideLink;?>"><img style="vertical-align:bottom" src="<?php echo SIQ_BASE_URL;?>/assets/<?php echo SIQ_PLUGIN_VERSION;?>/images/help/help-icon.png"> User Guide</a></h2>
 <div class="dwAdminHeading">You can change appearance of autocomplete here. You can use the textbox on right to see the results.</div>
 <form method="POST" action="<?php echo admin_url( 'admin.php?page=dwsearch&tab=tab-4'); ?>" class="custom_page_options siq-styling-form" id="siq-autocomplete-form" name="custom_options" onSubmit="return checkAutocompleteStyleSubmit()">
	<div class="section section-1">
		<h2>Autocomplete block</h2>
			<div class="data">
				<label>Show images in autocomplete search results</label>
				<input type="checkbox" name="siq_use_autocomplete_images" id="siq_use_autocomplete_images" value="yes"
					<?php if(isset($siq_use_autocomplete_images) && ($siq_use_autocomplete_images=='yes')){
						echo "checked ='checked'";
					}?>
				/>
			</div>
                <div class="data">
                    <label>Header for “Result” section</label>
                    <input value="<?php echo trim($this->pluginSettings["autocomplete_text_results"]) ? $this->pluginSettings["autocomplete_text_results"] : $this->defaultAutocompleteTextResults ;?>" name="autocomplete_text_results"/>
                </div>
                <div class="data">
                    <label>
                        Footer “Show all # results” link text<br/>
                        <small>Use # to include number of results</small>
                    </label>
                    <input value="<?php echo trim($this->pluginSettings["autocomplete_text_moreLink"]) ? $this->pluginSettings["autocomplete_text_moreLink"] : $this->defaultAutocompleteTextMoreLink ;?>" name="autocomplete_text_moreLink"/>
                </div>
                <div class="data">
                    <label>Header “Powered by” text</label>
                    <input value="<?php echo trim($this->pluginSettings["autocomplete_text_poweredBy"]) ? $this->pluginSettings["autocomplete_text_poweredBy"] : $this->defaultAutocompleteTextPoweredBy ;?>" name="autocomplete_text_poweredBy"/>
                </div>
		<div class="data">
			<label>Number of records</label>
			<input value="<?php echo $this->getAutocompleteNumRecords();?>" name="autocompleteNumRecords"/>
		</div>
		<div class="data">
			<label>
				Autocomplete width<br/>
				<small>(Keep empty or type &laquo;auto&raquo; to make the width fit search box size)</small>
			</label>
			<input value="<?php echo $styling['autocompleteWidth']; ?>" name="styling[autocompleteWidth]"/> px
		</div>
		<div class="data">
			<label>Background color</label>
			<input value="<?php echo $styling['autocompleteBackground']; ?>" name="styling[autocompleteBackground]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
		<div class="data">
			<label>Section Title Text Color</label>
			<input value="<?php echo $styling['sectionTitleColor']; ?>" name="styling[sectionTitleColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
		<div class="data">
			<label>More Link Text Color</label>
			<input value="<?php echo $styling['moreLinkColor']; ?>" name="styling[moreLinkColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
		<div class="data">
			<label>Hovered More Link Text Color</label>
			<input value="<?php echo $styling['hoverMoreLinkColor']; ?>" name="styling[hoverMoreLinkColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	<div class="section section-2">
		<h2>Result List Item</h2>
		<div class="data">
			<label>Font size</label>
			<?php echo $this->getFontSizeBox('styling[resultFontSize]', $styling['resultFontSize']); ?>
		</div>
		<div class="data">
			<label>Text Color</label>
			<input value="<?php echo $styling['resultFontColor']; ?>" name="styling[resultFontColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
		<div class="data">
			<label>Highlighted text font size</label>
			<?php echo $this->getFontSizeBox('styling[highlightFontSize]', $styling['highlightFontSize']); ?>
		</div>
		<div class="data">
			<label>Highlighted text Color</label>
			<input value="<?php echo $styling['highlightFontColor']; ?>" name="styling[highlightFontColor]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
		<div class="data">
			<label>Image Placeholder Background</label>
			<input value="<?php echo $styling['imagePlacehoderBackground']; ?>" name="styling[imagePlacehoderBackground]" class="color"/>
			<div class="clearColor"><span></span></div>
		</div>
	</div>
	 <div class="section section-3">
		 <h2>Hovered Item</h2>
		 <div class="data">
			 <label>Background color</label>
			 <input value="<?php echo $styling['hoverResultBackground']; ?>" name="styling[hoverResultBackground]" class="color"/>
			 <div class="clearColor"><span></span></div>
		 </div>
		 <div class="data">
			 <label>Font size</label>
			 <?php echo $this->getFontSizeBox('styling[hoverResultFontSize]', $styling['hoverResultFontSize']); ?>
		 </div>
		 <div class="data">
			 <label>Text Color</label>
			 <input value="<?php echo $styling['hoverResultFontColor']; ?>" name="styling[hoverResultFontColor]" class="color"/>
			 <div class="clearColor"><span></span></div>
		 </div>
		 <div class="data">
			 <label>Highlighted text font size</label>
			 <?php echo $this->getFontSizeBox('styling[hoverHighlightFontSize]', $styling['hoverHighlightFontSize']); ?>
		 </div>
		 <div class="data">
			 <label>Highlighted text Color</label>
			 <input value="<?php echo $styling['hoverHighlightFontColor']; ?>" name="styling[hoverHighlightFontColor]" class="color"/>
			 <div class="clearColor"><span></span></div>
		 </div>
		 <div class="data">
			 <label>Image Placeholder Background</label>
			 <input value="<?php echo $styling['hoverImagePlacehoderBackground']; ?>" name="styling[hoverImagePlacehoderBackground]" class="color"/>
			 <div class="clearColor"><span></span></div>
		 </div>
	 </div>

	<div class="section submit">
	<div class="data">
		<input type="submit" name="btnSubmitAutocompleteStyleOptions" id="btnSubmitAutocompleteStyleOptions" value="Submit" class="btn">
		<input type="button" name="btnClearAutocompleteStyle" id="btnClearAutocompleteStyle" value="Reset Style to default" class="btn">
		<?php wp_nonce_field( $this->updateAutocompleteNonce );?>
	</div>
	</div>
 </form>
 <div class="siq-styling-preview" id="siq-autocomplete-wrapper">
	 <div class="preview-heading">Preview</div>
     <input type="text" placeholder="Type here to see autocomplete results" name="s" style="width: 100%" />
 </div>
 <div class="clearfix"></div>
</div>
<script type="text/javascript">
    (function($){
        $(function() {
            var compileColorCss = function(property, color, defaultValue) {
                if (color != defaultValue && (color.length == 3 || color.length == 6)) return property + ":#" + color + "!important;";
                else return "";
            };
            var compileSizeCss = function(property, str, defaultValue) {
                if (str == defaultValue || isNaN(parseFloat(str))) return  "";
                else return property + ":" + parseFloat(str) + "px!important;";
            }
            var updateAutocompletePreview = function() {
                if ($("#tab-4").hasClass("selected")) {
                    $("#siq-autocomplete-wrapper").append($(".holdResults"));
                    $("#siq-autocomplete-wrapper .holdResults").attr("id", "siq-autocomplete");
                    $("#siq-autocomplete-wrapper .holdResults").attr("style", "position: relative!important; z-index: 1!important;");
                    $("body .holdResults._siq_main_searchbox .topArrow").attr("style", "top: -12px!important; z-index: 2!important;");
                    $("body .holdResults._siq_main_searchbox ul:eq(0)").attr("style", "z-index: 1!important;");
                }

                var imageStatusAC =  $("#siq-autocomplete-form [name='siq_use_autocomplete_images']").is(":checked");
                var noImgAC = '<div class="siq_resultLeft no-image"><span class="no-img"></span></div>';

                if($("body #siq-autocomplete ul li.siq-autocomplete").size() > 0 && $("body #siq-autocomplete ul li.siq-autocomplete .siq_resultLeft").size()==0){
                    $("body #siq-autocomplete ul li.siq-autocomplete").each(function(){
                        var imageAC = $(this).find(".siq_resultLeft");
                        if(imageStatusAC == true && imageAC.size() == 0){
                            $(this).find("a").prepend(noImgAC);
                        }else {
                            if(imageStatusAC == false) {
                                imageAC.attr("style", "display:none!important");
                            }else{
                                imageAC.removeAttr("style");
                            }
                        }

                    });
                }else{
                    var imageDiv  = $("body #siq-autocomplete ul li.siq-autocomplete .siq_resultLeft");
                    if(imageStatusAC == false) {
                        imageDiv.attr("style","display:none!important");
                    }else{
                        imageDiv.removeAttr("style");
                    }
                }


                if (window.SiqConfig && !SiqConfig.hideLogo) {
                    $("#siq-autocomplete > ul > li.sectionHead > h3:eq(0)").html($("#siq-autocomplete-form [name='autocomplete_text_results']").val() +
                        "<div class='siq-powered-by'>" +
                        $("#siq-autocomplete-form [name='autocomplete_text_poweredBy']").val() +
                        " <a href='http://searchiq.xyz/' target='_blank'>SearchIQ</a></div>");
                }

                var origText   = $("#siq-autocomplete > ul > li.resultsMoreLi > a").text();
	            var totalCount = (origText != "undefined" && origText != null && origText != "") && origText.match(/([0-9]+)/) ? origText.match(/([0-9]+)/)[1] : "342";
	            $("#siq-autocomplete > ul > li.resultsMoreLi > a").text($("#siq-autocomplete-form [name='autocomplete_text_moreLink']").val().replace("#", totalCount));
                $("body ._siq_main_searchbox ul li a .siq_resultRight").attr("style", "");
                
                var cssRules = "";
                cssRules += "body .holdResults._siq_main_searchbox{" + compileSizeCss("width", $("#siq-autocomplete-form [name='styling[autocompleteWidth]']").val(), "auto") + "}";
                cssRules += "body .holdResults._siq_main_searchbox .searchWrapperLabel, body .holdResults._siq_main_searchbox ul, body .holdResults._siq_main_searchbox ul li.sectionHead, body .holdResults._siq_main_searchbox ul li.sectionHead:hover, body .holdResults._siq_main_searchbox ul li.no-result, body .holdResults._siq_main_searchbox ul li.no-record, body .holdResults._siq_main_searchbox ul li.no-result, body .holdResults._siq_main_searchbox ul li.no-record:hover, body .holdResults._siq_main_searchbox .siq-blogrfct-cont, body .holdResults._siq_main_searchbox .siq-blogrfct-cont .siq-blogrfct-srchmain li{" +
                        compileColorCss("background-color", $("#siq-autocomplete-form [name='styling[autocompleteBackground]']").val(), "FFFFFF") + "}";
                cssRules += "body .holdResults._siq_main_searchbox .searchWrapperLabel, body .holdResults._siq_main_searchbox ul li.sectionHead h3, body .holdResults._siq_main_searchbox ul .siq-powered-by, body .holdResults._siq_main_searchbox ul .siq-powered-by a, body .holdResults._siq_main_searchbox .siq-blogrfct-srchmain li.siq-tabswrp li.siq-autab-lnk{" +
                            compileColorCss("color", $("#siq-autocomplete-form [name='styling[sectionTitleColor]']").val(), "505050") + "}";
                cssRules += "body .holdResults._siq_main_searchbox ul li .resultsMore, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.sectionHead h3 .resultsMore{" +
                        compileColorCss("color", $("#siq-autocomplete-form [name='styling[moreLinkColor]']").val(), "B7B7B7") + "}";
                cssRules += "body .holdResults._siq_main_searchbox ul li .resultsMore:hover, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.sectionHead h3 .resultsMore:hover{" +
                        compileColorCss("color", $("#siq-autocomplete-form [name='styling[hoverMoreLinkColor]']").val(), "000000") + "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li a h3, body .holdResults._siq_main_searchbox ul li a h3, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete h3{" +
                        compileSizeCss("font-size", $("#siq-autocomplete-form [name='styling[resultFontSize]']").val(), 13) + 
                        compileSizeCss("line-height", $("#siq-autocomplete-form [name='styling[resultFontSize]']").val() * 1.1, 13 * 1.1) + 
                        "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li a h3, body .holdResults._siq_main_searchbox ul li a h3, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete h3{" +
                        compileColorCss("color", $("#siq-autocomplete-form [name='styling[resultFontColor]']").val(), "333333") + "}";
                cssRules += "body .holdResults._siq_main_searchbox ul li a em, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li a em, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete a em{" +
                        compileSizeCss("font-size", $("#siq-autocomplete-form [name='styling[highlightFontSize]']").val(), 13) +
                        compileSizeCss("line-height", $("#siq-autocomplete-form [name='styling[highlightFontSize]']").val() * 1.1, 13 * 1.1) + 
                        "}";
                cssRules += "body .holdResults._siq_main_searchbox ul li a em, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li a em, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete a em{" +
                        compileColorCss("color", $("#siq-autocomplete-form [name='styling[highlightFontColor]']").val(), "333333") + "}";
                cssRules += "body ._siq_main_searchbox .siq_resultLeft.no-image .no-img, body #siq_search_results .siq_resultLeft.no-image .no-img, body ._siq_main_searchbox .siq_resultLeft.has-image img, body .holdResults._siq_main_searchbox ul li.siq-tabswrp ul li.siq-autocomplete a .siq_resultLeft.no-image span, body .holdResults._siq_main_searchbox ul li.siq-tabswrp ul li.siq-autocomplete a .siq_resultLeft.has-image img{" +
                        compileColorCss("background-color", $("#siq-autocomplete-form [name='styling[imagePlacehoderBackground]']").val(), "EFEDED") + "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.siq-autocomplete:hover, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.siq-autocomplete.highlighted, body .holdResults._siq_main_searchbox ul li.siq-autocomplete:hover, body .holdResults._siq_main_searchbox ul li.siq-autocomplete.highlighted{" +
                        compileColorCss("background-color", $("#siq-autocomplete-form [name='styling[hoverResultBackground]']").val(), "F9F9F9") + "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li:hover a h3, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.highlighted a h3, body .holdResults._siq_main_searchbox ul li:hover a h3, body .holdResults._siq_main_searchbox ul li.highlighted a h3, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete:hover h3, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete.highlighted h3{" +
                        compileSizeCss("font-size", $("#siq-autocomplete-form [name='styling[hoverResultFontSize]']").val(), 13) + 
                        compileSizeCss("line-height", $("#siq-autocomplete-form [name='styling[hoverResultFontSize]']").val() * 1.1, 13 * 1.1) + 
                        "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li:hover a h3, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.highlighted a h3, body .holdResults._siq_main_searchbox ul li:hover a h3, body .holdResults._siq_main_searchbox ul li.highlighted a h3, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete:hover h3, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete.highlighted h3{" +
                        compileColorCss("color", $("#siq-autocomplete-form [name='styling[hoverResultFontColor]']").val(), "333333") + "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li:hover a em, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.highlighted a em, body .holdResults._siq_main_searchbox ul li:hover a em, body .holdResults._siq_main_searchbox ul li.highlighted a em, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete:hover a em, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete.highlighted a em{" +
                        compileSizeCss("font-size", $("#siq-autocomplete-form [name='styling[hoverHighlightFontSize]']").val(), 13) + 
                        compileSizeCss("line-height", $("#siq-autocomplete-form [name='styling[hoverHighlightFontSize]']").val() * 1.1, 13 * 1.1) + 
                        "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li:hover a em, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.highlighted a em, body .holdResults._siq_main_searchbox ul li:hover a em, body .holdResults._siq_main_searchbox ul li.highlighted a em, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete:hover a em, body ._siq_main_searchbox .siq-blogrfct-srchmain .siq-tabswrp ul li.siq-autocomplete.highlighted a em{" +
                        compileColorCss("color", $("#siq-autocomplete-form [name='styling[hoverHighlightFontColor]']").val(), "333333") + "}";
                cssRules += "body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li:hover .siq_resultLeft.no-image .no-img, body #siq_search_results .siq_searchForm .siq_searchWrapper .holdResults ul li.highlighted .siq_resultLeft.no-image .no-img, body .holdResults._siq_main_searchbox ul li:hover .siq_resultLeft.no-image .no-img, body .holdResults._siq_main_searchbox ul li.highlighted .siq_resultLeft.no-image .no-img, body .holdResults._siq_main_searchbox ul li.siq-tabswrp ul li.siq-autocomplete:hover a .siq_resultLeft.no-image span, body .holdResults._siq_main_searchbox ul li.siq-tabswrp ul li.siq-autocomplete:hover a .siq_resultLeft.has-image img, body .holdResults._siq_main_searchbox ul li.siq-tabswrp ul li.siq-autocomplete.highlighted a .siq_resultLeft.no-image span, body .holdResults._siq_main_searchbox ul li.siq-tabswrp ul li.siq-autocomplete.highlighted a .siq_resultLeft.has-image img{" +
                        compileColorCss("background-color", $("#siq-autocomplete-form [name='styling[hoverImagePlacehoderBackground]']").val(), "EFEDED") + "}";
                $("#siq-autocomplete-style").remove();
                $("body").append("<style id='siq-autocomplete-style'>" + cssRules + "</style>");
            };
            $("#siq-autocomplete-form input, #siq-autocomplete-form select").on("change keyup", updateAutocompletePreview);
            $("#siq-autocomplete-form .clearColor").click(function() {
                setTimeout(updateAutocompletePreview);
            });
            updateAutocompletePreview();
            setInterval(updateAutocompletePreview, 500);
            
            $("#siq-autocomplete-wrapper").parent().css({"position": "relative"});
            var fixPreviewBlockPosition = function() {
                if (!$("#tab-4").hasClass("selected")) return;
                if (!$("#siq-autocomplete-wrapper").parent().parent().hasClass("selected")) return;
                $("#siq-autocomplete-wrapper").css({"position": "static", "width": "47%", "marginLeft": "3%"});
                var offset = $("#siq-autocomplete-wrapper").offset();
                var scrollTop = $(window).scrollTop();
                var clientOffsetTop = offset.top - scrollTop;
                var height = $("#siq-autocomplete-wrapper").outerHeight();
                var $form = $("#siq-autocomplete-wrapper").parent().find("form");
                var bottom = $form.find(".section.submit").outerHeight(true) + 20;
                var maxOffset = $form.offset().top + $form.outerHeight(true) - height - bottom;
                var width = $("#siq-autocomplete-wrapper").outerWidth(false);
                if (clientOffsetTop < 50 && scrollTop + 50 <= maxOffset) {
                    $("#siq-autocomplete-wrapper").css({position: "fixed", top: "50px", bottom: "auto", width: width, right: "auto", left: offset.left, marginLeft: 0});
                } else if (scrollTop + 50 > maxOffset) {
                    $("#siq-autocomplete-wrapper").css({position: "absolute", top: "auto", bottom: bottom, width: width, right: "0px", left: "auto", marginLeft: 0});
                } else {
                    $("#siq-autocomplete-wrapper").css({position: "static", top: "auto", bottom: "auto", width: "47%", right: 0, marginLeft: "3%"});
                }
            };
            $(window).scroll(fixPreviewBlockPosition);
            setTimeout(fixPreviewBlockPosition, 200);
            setInterval(fixPreviewBlockPosition, 500);
            
        });
    })($jQu);
</script>
