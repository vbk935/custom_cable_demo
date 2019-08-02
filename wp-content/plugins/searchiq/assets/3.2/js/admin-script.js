$jQu = jQuery;
var currentPage;
var postTypeSelection		= {};
var postTypeSelectionTemp	= {};

var postTypeForSearchSelection		= {};
var postTypeForSearchSelectionTemp	= {};

var imageTypeSelection		= {};
var imageTypeSelectionTemp	= {};

var excerptTypeSelection		= {};
var excerptTypeSelectionTemp		= {};

var excludeCustomFieldsSelection		= {};
var excludeCustomFieldsSelectionTemp	= {};

var excludeCustomTaxonomySelection		= {};
var excludeCustomTaxonomySelectionTemp	= {};

var excludePostsSelection		= "";
var excludePostsSelectionTemp	= "";
var resAfterSync;

var excludeBlackListUrls = "";
var excludeBlackListUrlsTemp = "";

var flag 				= 0;
var flagImageSel  		= 0;
var flagCustomSel 		= 0;
var flagExPidSel 		= 0;
var flagCustomTaxSel 	= 0;
var flagError			= 0;
var flagCustomExcerptSel 	= 0;
var flagSelectForSearch 	= 0;
var flagBLUrlsSel = 0;

$jQu(document).ready(function(){
	if($jQu("input[name*='post_types_for_search']").length > 0){
		$jQu("input[name*='post_types_for_search']").each(function(){
			if($jQu(this).is(":checked")){
				postTypeSelection[$jQu(this).val()] = true;
			}else{
				postTypeSelection[$jQu(this).val()] = false;
			}
		});
	}
	if($jQu("select[name*='postTypesSetForSearch']").length > 0){
		$jQu("select[name*='postTypesSetForSearch']:not('.isDisabled')").each(function(){
			var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
			if($jQu("input[name*='post_types_for_search'][value='"+name+"']").is(":checked")){
				postTypeForSearchSelection[name] = $jQu(this).val();
			}
		});
	}
	
    if($jQu("select[name*='imageCustomFieldSelect']").length > 0){
        $jQu("select[name*='imageCustomFieldSelect']").each(function(){
            var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
            imageTypeSelection[name] = $jQu(this).val();
        });
    }
	$jQu("select[name*='excerptCustomFieldSelect']").each(function(){
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		excerptTypeSelection[name] =$jQu(this).val();
		excerptTypeSelectionTemp[name] = $jQu(this).val();
	});
  
	if($jQu("select[name*='customFieldFilterSelect']").length > 0){
		$jQu("select[name*='customFieldFilterSelect']").each(function(){
			var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
			if(typeof excludeCustomFieldsSelection[name] == "undefined") {
				excludeCustomFieldsSelection[name] = [];
				excludeCustomTaxonomySelection[name] = [];
			}
			$jQu("input[name*='customFieldFilterCheckbox["+name+"]']:checked").each(function(){
				excludeCustomFieldsSelection[name].push($jQu(this).val());
			});

			$jQu("input[name*='customTaxonomyFilterCheckbox["+name+"]']:checked").each(function(){
				excludeCustomTaxonomySelection[name].push($jQu(this).val());
			});
		});
	}
	if($jQu(".excludePostIds").size() > 0){
		excludePostsSelection = $jQu(".excludePostIds").val();
	}

	if($jQu(".blackListUrls").size() > 0){
		excludeBlackListUrls = $jQu(".blackListUrls").val();
	}

	$jQu('#btnSubmitcode').click(function(){
		var thisVar = $jQu(this);
		thisVar.addClass('ajaxLoading');
		var activationVal = $jQu("#siq_activation").val();
		if(activationVal != ""){
			thisVar.parents('.data').find('div.msg').remove();
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "verify_subscription", api_key: activationVal, security:window.siq_admin_nonce },
				dataType: 'json',
				crossDomain: true
			}).done(function( response ) {
				thisVar.removeClass('ajaxLoading');
				var res = eval(response);
				thisVar.parents('.data').find('div.msg').remove();
				if(res.success == 0){
					thisVar.parents('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
				}else{
					if(typeof res.filters != "undefined"){
						$jQu("#imageCustomFieldSubsection").replaceWith(res.filters);
					}
					if(typeof res.cropOrResize != "undefined" && res.cropOrResize != ""){
						$jQu("input[name^='selectCropResize'][value^='"+res.cropOrResize+"']").attr("checked", true);
					}
					thisVar.parents('.data').append('<div class="msg success">'+res.message+'</div>');
					setTimeout(function () {
						thisVar.parents('.section').removeClass('not-done').addClass('done').addClass('closed');
						thisVar.parents('.wsplugin').find('.dwAdminHeading').eq(0).addClass('hidden');
						$jQu(res.optionsBox).insertBefore(thisVar.parents('.section'));
						if (typeof res.engineName != 'undefined') {
							thisVar.parents('.section').parents(".wsplugin").find(".section.section-top").find("li.ename span").html(res.engineName);
							thisVar.parents('.data').append('<div class="msg success">'+res.message+'</div>');
							thisVar.parents('.section').nextAll('.section.section-3').removeClass('not-done').removeClass('no-engine').addClass('engine')
						}
					}, 3000);
				}
			});
		}else{
			thisVar.parents('.data').find('.errorMsg').remove();
			thisVar.parents('.data').append('<div class="msg errorMsg">Please enter activation code</div>');
			thisVar.removeClass('ajaxLoading');
		}
	});

	$jQu('#btnSubmitEngine').click(function(){
		var thisVar 	= $jQu(this);

        thisVar.parents('.data').find('div.msg').remove();
        thisVar.addClass('ajaxLoading');
        $jQu.ajax({
            method: "POST",
            url: adminAjax,
            data: { action: "siq_ajax", task: "submit_engine", security:window.siq_admin_nonce },
            crossDomain: true,
            dataType: 'json'
        }).done(function( response ) {
            thisVar.removeClass('ajaxLoading');
            var res = eval(response);
            thisVar.parents('.data').find('div.msg').remove();
            if(res.success == 0){
                thisVar.parents('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
            }else{
                thisVar.parents('.section').parents(".wsplugin").find(".section.section-top").find("li.ename span").html(res.engine);
                thisVar.parents('.data').append('<div class="msg success">'+res.message+'</div>');
                thisVar.parents('.section').next('.section.section-3').removeClass('not-done').removeClass('no-engine').addClass('engine').removeClass('closed');
                window.setTimeout(function(){
                    thisVar.parents('.section').addClass('engine').removeClass('no-engine').addClass("closed").removeAttr('style');

                }, 3000);
            }
        });
	});

	$jQu("select[name=siq_image_custom_field_name_select]").on("change", function() {
		if ($jQu(this).val() === "Other...") {
			$jQu("input[name=imageCustomFieldName], #siq_image_custom_field_name_input_label, input[name=siq_image_custom_field_name_input]").show();
		} else {
			$jQu("input[name=imageCustomFieldName], #siq_image_custom_field_name_input_label, input[name=siq_image_custom_field_name_input]").hide();
		}
	});

    $jQu("select[name*='imageCustomFieldSelect']").on("change", function() {
        if ($jQu(this).val() === "Other...") {
            $jQu(this).parent().find("input[name*='imageCustomFieldName']").show();
        } else {
            $jQu(this).parent().find("input[name*='imageCustomFieldName']").hide();
        }
    });
	$jQu(document).on('click', '#btnSubmitPosts' ,function(){
		syncPosts();
	});

	$jQu('#btnResetConfig').click(function(){
		var thisVar 	= $jQu(this);

		if(confirm('Are you sure you want to clear the configuration?')){
			thisVar.addClass('ajaxLoading');
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "clear_configuration", security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				thisVar.removeClass('ajaxLoading');
				var res = eval(response);
				thisVar.parents('.data').find('div.msg').remove();
				if(res.success == 0){
					thisVar.parents('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
				}else{
					thisVar.parents('.data').append('<div class="msg success">'+res.message+'</div>');
					window.location.href = adminBaseUrl;
				}
			});
		}else{

		}
	});

	$jQu('#btnDelDomain').click(function(){
		var thisVar 	= $jQu(this);

		if(confirm('Are you sure you want to deleted all data?')){
			thisVar.addClass('ajaxLoading');
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "delete_domain", security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				thisVar.removeClass('ajaxLoading');
				var res = eval(response);
				thisVar.parent().find('.data').find('div.msg').remove();
				if(res.success == 0){
					thisVar.parent().find('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
				}else{
					thisVar.parent().find('.data').append('<div class="msg success">'+res.message+'</div>');
					window.location.href = adminBaseUrl;
				}
			});
		}else{

		}
	});

	$jQu('#btnClearStyle').click(function(){
		var thisVar 	= $jQu(this);

		if(confirm('Are you sure you want to reset style to default?')){
			thisVar.addClass('ajaxLoading');
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "reset_style", security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				thisVar.removeClass('ajaxLoading');
				var res = eval(response);
				thisVar.parent().find('.data').find('div.msg').remove();
				if(res.success == 0){
					thisVar.prev('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
				}else{
					thisVar.prev('.data').append('<div class="msg success">'+res.message+'</div>');
					window.location.href = window.location.href;
				}
			});
		}else{

		}
	});

	$jQu('#btnClearAutocompleteStyle').click(function(){
		var thisVar 	= $jQu(this);

		if(confirm('Are you sure you want to reset style to default?')){
			thisVar.addClass('ajaxLoading');
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "reset_autocomplete_style", security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				thisVar.removeClass('ajaxLoading');
				var res = eval(response);
				thisVar.parent().find('.data').find('div.msg').remove();
				if(res.success == 0){
					thisVar.prev('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
				}else{
					thisVar.prev('.data').append('<div class="msg success">'+res.message+'</div>');
					window.location.href = window.location.href;
				}
			});
		}else{

		}
	});

	$jQu('#btnClearMobileStyle').click(function(){
		var thisVar 	= $jQu(this);

		if(confirm('Are you sure you want to reset style to default?')){
			thisVar.addClass('ajaxLoading');
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "reset_mobile_style", security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				thisVar.removeClass('ajaxLoading');
				var res = eval(response);
				thisVar.parent().find('.data').find('div.msg').remove();
				if(res.success == 0){
					thisVar.prev('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
				}else{
					thisVar.prev('.data').append('<div class="msg success">'+res.message+'</div>');
					window.location.href = window.location.href;
				}
			});
		}else{

		}
	});

	$jQu('#btnGenerateThumbnails, #btnGenerateThumbnailsOptions').click(function(){
		var thisVar 	= $jQu(this);
		var res 		= {};
		res.show 	= 1;
		res.started = 1;
		thumbProgress(thisVar, res);

		return false;
	});
	
	$jQu(document).on("click", '#btnDeltaSync', function(){
		var thisVar 	= $jQu(this);
		var res 		= {};
		res.show 	= 1;
		res.started = 1;
		deltaSyncProgress(thisVar, res);
		return false;
	});

	$jQu('#siq_searchbox_name').on('keyup', function() {
		var thisVal   = $jQu(this).val();
		var numSpaces;
		try{
			numSpaces = thisVal.match(/[^a-z0-9\-\_]/g).length;
		}catch(e){
			numSpaces = 0;
		}
		if(numSpaces > 0){
			thisVal = thisVal.replace(/[^a-z0-9\-\_]/gi, '');
			$jQu(this).val(thisVal);
		}
	});

	$jQu('#siq_search_query_param_name').on('keyup', function() {
		var thisElem 	= $jQu(this);
		var thisVal   	= thisElem.val();
		var errorSpan =	thisElem.parents('.data').find('small span');
		var numSpaces;
		try {
			numSpaces = thisVal.match(/[^a-z0-9]/gi).length;
		}catch(e){
			numSpaces = 0;
		}

		if(numSpaces > 0) {
			thisVal = thisVal.replace(/[^a-z0-9]/gi, '');
			thisElem.val(thisVal);
		}
		if(thisElem.val() == "s"){
			if(!thisElem.hasClass('errorElement')) {
				thisElem.addClass("errorElement");
			}
			if(thisElem.parent().find('div.message').size() == 0) {
				thisElem.parent().append('<div class="message error">' + errorSpan.html() + '</div>');
			}
		}else{
			thisElem.removeClass('errorElement');
			thisElem.parent().find('div.message').remove();
		}
	});

	$jQu('#siq_search_query_param_name').change(function() {
		var thisElem			 = $jQu(this);
		var searchqueryparamname = thisElem.val().replace(/[^a-z0-9]/gi, '');
		var errorSpan =	thisElem.parents('.data').find('small span');

		if(searchqueryparamname == 's'){
			if(!thisElem.hasClass('errorElement')) {
				thisElem.addClass("errorElement");
			}
			if(thisElem.parent().find('div.message').size() == 0) {
				thisElem.parent().append('<div class="message error">' + errorSpan.html() + '</div>');
			}
			return false;
		}
		thisElem.removeClass('errorElement');
		thisElem.parent().find('div.message').remove();
	});
});
function syncPosts(a){
	var thisVar = $jQu("#btnSubmitPosts");
	var thisSection = thisVar.parents(".section");
	var thisSectionPre = thisVar.parents(".section").prev(".section");
	var thisVarParent   = thisSection.find("input[name*='post_types_for_search']").size() > 0 ? thisSection : thisSectionPre;
	thisSection.find(".msg").remove();
	thisSectionPre.find(".msg").remove();
	var excludePostIds = (typeof $jQu(".excludePostIds") != "undefined" && $jQu(".excludePostIds").size() > 0) ? $jQu(".excludePostIds").val() : "";
	var blackListUrls = (typeof $jQu(".blackListUrls") != "undefined" && $jQu(".blackListUrls").length > 0) ? $jQu(".blackListUrls").val(): "";
	var siq_field_for_excerpt = "";
	siq_field_for_excerpt = getExcerptCustomFieldSelect(siq_field_for_excerpt);

	flagError	= 0;
	if(excludePostIds != "") {
		flagError = 1;
		var isValid = excludePostIds.match(/^[0-9\,]+$/);
		if(isValid == null) {
			showMessage($jQu(".excludePostIds"), "<b style='font-size:120%;'>Error:</b> Post Id's can only have numeric id's separated by commas(without spaces). Please check the post id's you have entered and Sync again.");
			return false;
		}else{
			$jQu(".excludePostIds").parents('.data.sep-block').find('.message').remove();
		}
	}

	if(typeof a == "undefined"){
		currentPage = 0;
		thisVar.parents(".section").find('.progress-wrap.progress').show();
		thisVar.parents(".section").find('.progressText').show().html("Progress: 0%");
		thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
		thisVar.parents('.data').find('div.msg').remove();
	}

	var setImageCustomField = 1;

	var imageCustomField  			= "";
	var postTypesToSearch 			= "";
	var customFieldsToExclude 		= "";
	var customTaxonomiesToExclude 	= "";
	var postTypesForSearchSelection = ""; 
	
	if (setImageCustomField) {
		imageCustomField = getImageCustomFields(imageCustomField);
	}

	customFieldsToExclude 		= getCutomFieldsToExclude(customFieldsToExclude);
	customTaxonomiesToExclude 	= getCustomTaxonomiesToExclude(customTaxonomiesToExclude);
	postTypesToSearch			= getPostTypesToSearch(thisVarParent, postTypesToSearch);
	postTypesForSearchSelection	= getPostTypesForSearchSelection(thisVarParent, postTypesForSearchSelection);
	setProp(thisVarParent, true);

	thisVar.addClass('ajaxLoading');
	thisVar.prop("disabled",'disabled');
	$jQu.ajax({
		method: "POST",
		url: adminAjax,
		data: { action: "siq_ajax", task: "submit_for_indexing", setImageCustomField: setImageCustomField, imageCustomField: imageCustomField, postTypesToSearch: postTypesToSearch, customFieldsToExclude: customFieldsToExclude, customTaxonomiesToExclude: customTaxonomiesToExclude, siq_field_for_excerpt:siq_field_for_excerpt, excludePostIds: excludePostIds, currentPage : currentPage, security:window.siq_admin_nonce, postTypesForSearchSelection:postTypesForSearchSelection, blackListUrls:blackListUrls },
		crossDomain: true,
		dataType: 'json'
	}).done(function( response ) {
		thisVar.removeClass('ajaxLoading');
		var res = eval(response);
		thisVar.parents('.data').find('div.msg').remove();
		resAfterSync =  res;
		if(res.success == 0){
			thisVar.parents('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
			thisVar.removeProp("disabled");
			setProp(thisVarParent, false);
			if($jQu(".messageIndexPosts").size() > 0){
				$jQu(".messageIndexPosts").remove();
				postTypeSelection = postTypeSelectionTemp;
				imageTypeSelection = imageTypeSelectionTemp;
				excerptTypeSelection = excerptTypeSelectionTemp;
				postTypeForSearchSelection = postTypeForSearchSelectionTemp;
			}
			thisVar.parents('.section').find('.progress-wrap.progress').hide();
			thisVar.parents('.section').find('.progressText').hide();
			if(typeof res.indexed != "undefined"){
				thisVar.parents('.section').parents(".wsplugin").find(".section.section-top").find("li.iposts span").html(res.indexed);
			}
			if(typeof res.searchengine != "undefined" && res.searchengine == false){
				setTimeout(function(){
					if(thisVar.parents(".section").hasClass("section-3")){
						thisVar.parents(".section").prev('.section').find(".msg").remove();
						thisVar.parents('.section').find('h2').html("Synchronize Posts");
						thisVar.parents('.section').find('h3').html("Click on the button to submit posts for synchronization");
						thisVar.parents('.section').attr("class", 'section section-3 not-indexed');
						thisVar.val("Synchronize Posts");

						thisVar.parents(".section").prev('.section').removeClass('closed').show();
						thisVar.parents(".section").find('.msg.errorMsg').remove();
						thisVar.parents(".section").addClass('closed');
						thisVar.parents(".section").nextAll('.section').not(".section-5").addClass('not-done');
					}else{
						var prevParent 		= thisVar.parents(".section").prev('.section');
						var prevPrevParent 	= thisVar.parents(".section").prev('.section').prev('.section');
						prevParent.find(".syncSettingsWrapper").remove();
						prevPrevParent.find(".msg").remove();

						prevParent.find('h2').html("Synchronize Posts");
						thisVar.parents(".section").find('h2').html("Synchronize Posts");
						thisVar.parents(".section").find('h3').html("Click on the button to submit posts for synchronization");
						prevParent.attr("class", 'section section-3 not-indexed');
						thisVar.val("Synchronize Posts");

						prevPrevParent.removeClass('closed').show();
						prevParent.find('.msg.errorMsg').remove();
						prevParent.addClass('closed');
						prevParent.nextAll('.section').not(".section-5").addClass('not-done');
						$jQu('.section.section-3-1 .resyncPostsActionWrapper').appendTo(prevParent.find(".data").eq(0));
						$jQu('.section.section-3-1').remove();
					}


				}, 4000);
			}
		}else{
			thisVar.parents('.section').find('.progressText').show();
			if(res.next != ""){
				currentPage = res.next;
				syncPosts('simulate');
				moveProgressBar(res.progress,'', thisVar);
				thisVar.parents('.section').parents(".wsplugin").find(".section.section-top").find("li.iposts span").html(res.indexed);
			}else{
				moveProgressBar(res.progress, res.message, thisVar);
				thisVar.parents('.section').parents(".wsplugin").find(".section.section-top").find("li.iposts span").html(res.indexed);
				if(typeof res.thumbServiceDisabledMsg != "undefined" && typeof res.success != "undefined" && res.success){
					$jQu(".tab.tab-1 .section.section-4").find(".data, h3, .message").remove();
					$jQu(".tab.tab-1 .section.section-4").append(res.thumbServiceDisabledMsg);
				}
				if(typeof res.facetsEnabled != "undefined" && typeof res.success != "undefined" && res.success){
					if($jQu("#tab-6").length == 0){
						$jQu("#searchIqBackend .tabsHeading ul").append(res.facetsEnabled);
						if(typeof res.facetsHtml != "undefined" && $jQu(".tab-6").length == 0){
							$jQu("#searchIqBackend .tabsContent").append(res.facetsHtml);
						}
					}
				}
				
				if($jQu(".siq-notices.siq-facets").length > 0){
					$jQu(".siq-notices.siq-facets").remove();
				}
			}
		}
	}).fail(function(jqXHR, textStatus, errorThrown){
		var errorText = "There was some error processing your request, please try later.";
		if(!!jqXHR && !!jqXHR.responseJSON && !!jqXHR.responseJSON.message){
			errorText = jqXHR.responseJSON.message;
		}else if(typeof errorThrown != "undefined" && errorThrown != ""){
			errorText = jqXHR.status+" "+jqXHR.statusText+". Please try again.";
		}
		thisVar.parents('.data').append('<div class="msg errorMsg">'+errorText+'</div>');
		thisVar.removeProp("disabled");
		setProp(thisVarParent, false);
		if($jQu(".messageIndexPosts").size() > 0){
			$jQu(".messageIndexPosts").remove();
			postTypeSelection = postTypeSelectionTemp;
			imageTypeSelection = imageTypeSelectionTemp;
			excerptTypeSelection = excerptTypeSelectionTemp;
			postTypeForSearchSelection = postTypeForSearchSelectionTemp;
		}
		thisVar.removeClass('ajaxLoading');
		thisVar.parents('.section').find('.progressText').hide();
		thisVar.parents('.section').find('.progress-wrap.progress').hide();
	});
}


function checkLastCharForCommaAndTrim(stringToTest){
	var lastChar = stringToTest.substring(stringToTest.length - 1, stringToTest.length);
	if(lastChar == ","){
		stringToTest = stringToTest.substring(0, stringToTest.length - 1);
	}
	return stringToTest;
}
function moveProgressBar(percent, msg, el, sync) {
	var elParent = $jQu(el).parents(".section");
	var getPercent = percent;
	var getProgressWrapWidth = elParent.find('.progress-wrap').width();
	var progressTotal = getPercent * getProgressWrapWidth;
	var isSync = (typeof sync != "undefined") ? sync : true;
	var animationLength = 2000;
	var elParentPre = (elParent.find("input[name*='post_types_for_search']").size() > 0) ? elParent  : elParent.prev(".section");
	// on page load, animate percentage bar to data percentage length
	// .stop() used to prevent animation queueing
	elParent.find('.progress-bar').stop().animate({
		left: progressTotal
	},{
		duration: animationLength,
		step: function( now, fx ){
			var totalPer = parseInt(now/getProgressWrapWidth*100);
			totalPer = (totalPer < 100) ? totalPer : 100;
			elParent.find('.progressText').html("Progress: "+totalPer+"%");
		},complete:function(){
			if (isSync) {
				if(msg != ''){

					if (el.parents('.section').next('.section.section-4').hasClass("not-indexed")) {
						if (msg.trim() == "Data synchronization complete. You can now check your site's frontend to try SearchIQ search.") {
							el.parents('.data').append('<div class="msg success undeletable">Congratulation! Your site now has a new search experience.</div>');
						} else {
							el.parents('.data').append('<div class="msg success undeletable">'+msg+'</div>');
						}

						$jQu.ajax({
							"method": "POST",
							"url": adminAjax,
							"data": {"action": "siq_ajax", "task": "check_graphic_editor_status", "security": window.siq_admin_nonce},
							"dataType": "json"
						}).done(function(data) {
							if (data.success && data.graphicEditorEnabled) {
								elParentPre.find('.data').eq(0).append('<div class="msg success undeletable">Your built-in search page is replaced by our SearchIQ search page. ' +
									'Both autocomplete and search page will come with thumbnails and it will take few minutes to create.<br/>' +
									'If you don’t like our custom search page, you can always switch it back in Option tab.</div>');
							} else {
								elParentPre.find('.data').eq(0).append('<div class="msg success undeletable">Your built-in search page is replaced by our SearchIQ search page.<br/>' +
									'If you don’t like our custom search page, you can always switch it back in Option tab.</div>');
								elParentPre.find('.data').eq(0).append('<div class="msg error undeletable">Couldn\'t generate thumbnails because of no any installed graphic libraries for php.</div>');
							}
						}).fail(function() {
							elParentPre.find('.data').eq(0).append('<div class="msg error undeletable">Cannot check graphic editor state.</div>');
						});

					} else {
						el.parents('.data').append('<div class="msg success">'+msg+'</div>');
					}
					el.removeProp("disabled");
					if(elParentPre.find("input[name*='post_types_for_search']").size() > 0) {
						elParentPre.find("input[name*='post_types_for_search']").removeProp("disabled");
						elParentPre.find("select[name*='imageCustomFieldSelect'], input[name*='imageCustomFieldName'], input[name*='customFieldFilterCheckbox'], input[name*='customTaxonomyFilterCheckbox'], .customFieldFilterSelect, .excludePostIds,select[name*='excerptCustomFieldSelect'],select[name*='postTypesSetForSearch']:not('.isDisabled'),.blackListUrls").removeProp("disabled");
						if($jQu(".messageIndexPosts").size() > 0){
							$jQu(".messageIndexPosts").remove();
							postTypeSelection = postTypeSelectionTemp;
							imageTypeSelection = imageTypeSelectionTemp;
							excerptTypeSelection = excerptTypeSelectionTemp;
							postTypeForSearchSelection = postTypeForSearchSelectionTemp;
							resetAllFlags();
						}
					}
					elParent.find('.progressText').hide();
					elParent.find('.progress-wrap.progress').hide();
					elParent.find('.progress-bar.progress').css('left',0);
					el.parents('.section').next('.section.section-4').removeClass('not-done').removeClass('no-engine').removeClass('not-indexed').addClass('engine').addClass('done').addClass('indexed');
					el.parents('.section').next('.section').next('.section.section-5').removeClass('not-done').removeClass('no-engine').removeClass('not-indexed').addClass('engine').addClass('done').addClass('indexed');
					el.parents('.backendTabbed').find(".tabsHeading li.hide").removeClass('hide');
					el.parents('.backendTabbed').find(".tabsContent div.hide").removeClass('hide');
					
					window.setTimeout(function(){
						if(el.parents('.section').hasClass("section-3")){
							el.parents('.section').find('h2').eq(0).html("Synchronization Settings");
						}
						el.parents('.section').find('h3').html("Click <b>\"Full Resynchronize posts\"</b> button to submit posts for re-synchronization or else click <b>\"Delta Resynchronize posts\"</b> button to submit only updated posts for re-synchronization");
						el.val("Full Resynchronize Posts");
						$jQu("#btnDeltaSync").show().val("Delta Resynchronize Posts");
						el.parents(".resyncPostsActionWrapper").find("h2").html("Resynchronize Posts");
						el.parents('.section').removeClass('not-indexed').addClass('indexed').removeClass('not-closed');
						el.parents('.section').find(".msg").each(function() {
							if ($jQu(this).hasClass("undeletable")) return;
							$jQu(this).remove();
						});
						if(resAfterSync != null && typeof resAfterSync.syncSaveHtml != "undefined"){
							if($jQu("#imageCustomFieldSubsection").parent().find(".syncSettingsWrapper").size() == 0){
								$jQu("#imageCustomFieldSubsection").after(resAfterSync.syncSaveHtml);
							}
							if($jQu(".resyncPostsActionWrapper").find("h2").size() == 0){
								$jQu(".resyncPostsActionWrapper").prepend("<h2>Resynchronize posts</h2>");
							}
							if($jQu("#imageCustomFieldSubsection").parent().find(".resyncPostsActionWrapper").size() > 0){
								$jQu("#imageCustomFieldSubsection").parents(".section").after('<div class="section section-3-1"><div class="data"></div></div>');
								$jQu("#imageCustomFieldSubsection").parent().find(".resyncPostsActionWrapper").clone().appendTo(".section.section-3-1 .data");
								$jQu("#imageCustomFieldSubsection").parent().find(".resyncPostsActionWrapper").remove();
							}
						}
						resAfterSync = null;
					}, 5000);

				}
			}
		}
	});
}
function thumbProgress(thisVar, res, keepshowingprogress) {
	var showProgress = (typeof keepshowingprogress != "undefined") ? keepshowingprogress : true;
	var result;
	var percent 	 = (typeof res.percent != "undefined") ? res.percent: 0.01;
	var currentPage  = (typeof res.currentPage != "undefined") ? res.currentPage : 0;
	var cropOrResize = $jQu("input[name^='selectCropResize']:checked").val();

	if (showProgress && ( (percent * 100) < 100)) {
		if(res.show == 1 && res.started == 1){
			//START Thumbnail Creation
			thisVar.parents(".section").find('.progress-wrap.progress').show();
			thisVar.parents(".section").find('.progressText').show().html("Progress: 0%");
			thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
			thisVar.parents('.section').find('div.message').remove();
			thisVar.parents('.section').append('<div class="message">Thumbnail generation in progress, please don\'t navigate to another page or the process will not be completed.</div>')
		}else if(res.inProgress){
			moveProgressBar(res.percent, res.message, thisVar, false);
		}
		if (res.show || res.inProgress) {
			$jQu("#btnGenerateThumbnails").attr("disabled","disabled");
			$jQu("#btnGenerateThumbnailsOptions").attr("disabled","disabled");
			$jQu("input[name^='selectCropResize']").attr("disabled","disabled");
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "generate_post_thumbs",cropOrResize:cropOrResize, inProgress: 1, percent: percent, currentPage:currentPage, security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				var res = eval(response);
				var per = res.percent*100;
				if(res.success == 1){
					if (per >= 100) {
						thumbProgress(thisVar, res, false);
						moveProgressBar(res.percent, res.message, thisVar, false);
					}else{
						thumbProgress(thisVar, res);
					}

				}else{
					thisVar.parents(".section").find('.progress-wrap.progress').hide();
					thisVar.parents(".section").find('.progressText').hide().html("Progress: 0%");
					thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
					thisVar.parents('.section').find('.message').html(res.message);
					$jQu("#btnGenerateThumbnails").removeAttr("disabled");
					$jQu("#btnGenerateThumbnails").parent().removeClass('dontshowbutton');

					$jQu("#btnGenerateThumbnailsOptions").removeAttr("disabled");
					$jQu("input[name^='selectCropResize']").removeAttr("disabled");
					$jQu("#btnGenerateThumbnailsOptions").parent().removeClass('dontshowbutton');

					if (res.message) {
						thisVar.parent().append("<div class='msg error clear'>"+res.message+"</div>")
					}
				}
			});
		} else {
			thisVar.parents(".section").find('.progress-wrap.progress').hide();
			thisVar.parents(".section").find('.progressText').hide().html("Progress: 0%");
			thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
			thisVar.parents('.section').find('div.message').remove();
		}

	} else {
		thisVar.parents('.section').find('.message').html(res.message);
		setTimeout(function(){
			thisVar.parents('.section').find('.progress-wrap.progress').hide();
			thisVar.parents('.section').find('.progressText').hide();
			thisVar.parents('.section').find('.message').hide();
		},3000);

		$jQu("#btnGenerateThumbnails").removeAttr("disabled");
		$jQu("#btnGenerateThumbnails").parent().removeClass('dontshowbutton');
		$jQu("#btnGenerateThumbnailsOptions").removeAttr("disabled");
		$jQu("#btnGenerateThumbnailsOptions").parent().removeClass('dontshowbutton');
		$jQu("input[name^='selectCropResize']").removeAttr("disabled");

	}
}

function deltaSyncProgress(thisVar, res, keepshowingprogress) {
	var showProgress = (typeof keepshowingprogress != "undefined") ? keepshowingprogress : true;
	var result;
	var percent 	 = (typeof res.percent != "undefined") ? res.percent: 0.01;
	var currentPage  = (typeof res.currentPage != "undefined") ? res.currentPage : 0;
	var totalDelta  = (typeof res.totalPosts != "undefined") ? res.totalPosts : 0;
	var deltaPostIDs = (typeof res.deltaPostIDs != "undefined") ? res.deltaPostIDs : "";
	if (showProgress && ( (percent * 100) < 100)) {
		if(res.show == 1 && res.started == 1){
			thisVar.parents(".section").find('.progress-wrap.progress').show();
			thisVar.parents(".section").find('.progressText').show().html("Progress: 0%");
			thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
			thisVar.parents('.section').find('div.message').remove();
			thisVar.parents('.section').append('<div class="message">Delta Post Sync in progress, please don\'t navigate to another page or the process will not be completed.</div>')
		}else if(res.inProgress){
			moveProgressBar(res.percent, res.message, thisVar, false);
		}
		if (res.show || res.inProgress) {
			$jQu("#btnDeltaSync").attr("disabled","disabled");
			$jQu.ajax({
				method: "POST",
				url: adminAjax,
				data: { action: "siq_ajax", task: "delta_sync_posts", inProgress: 1, percent: percent, total:totalDelta,deltaPostIDs: deltaPostIDs, currentPage:currentPage, security:window.siq_admin_nonce},
				crossDomain: true,
				dataType: 'json'
			}).done(function( response ) {
				var res = eval(response);
				var per = res.percent*100;
				if(res.success == 1){
					if (per >= 100) {
						deltaSyncProgress(thisVar, res, false);
						moveProgressBar(res.percent, res.message, thisVar, false);
					}else{
						deltaSyncProgress(thisVar, res);
					}

				}else{
					thisVar.parents(".section").find('.progress-wrap.progress').hide();
					thisVar.parents(".section").find('.progressText').hide().html("Progress: 0%");
					thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
					thisVar.parents('.section').find('.message').html(res.message);
					$jQu("#btnDeltaSync").removeAttr("disabled");
				}
			});
		} else {
			thisVar.parents(".section").find('.progress-wrap.progress').hide();
			thisVar.parents(".section").find('.progressText').hide().html("Progress: 0%");
			thisVar.parents(".section").find('.progress-bar.progress').css('left',0);
			thisVar.parents('.section').find('div.message').remove();
		}

	} else {
		thisVar.parents('.section').find('.message').html(res.message);
		setTimeout(function(){
			thisVar.parents('.section').find('.progress-wrap.progress').hide();
			thisVar.parents('.section').find('.progressText').hide();
			thisVar.parents('.section').find('.message').hide();
		},3000);

		$jQu("#btnDeltaSync").removeAttr("disabled");
		}
}

function getImageCustomFields(imageCustomField){
	$jQu("select[name*='imageCustomFieldSelect']").each(function(){
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		if ($jQu(this).val() === "Other...") {
			imageCustomField += name+":"+$jQu(this).parent().find("input[name*=imageCustomFieldName]").val()+",";
		} else {
			imageCustomField += name+":"+$jQu(this).val()+",";
		}
	});
	if(imageCustomField.length > 0){
		imageCustomField =  imageCustomField.substr(0, imageCustomField.length -1);
	}
	return imageCustomField;
}

function getExcerptCustomFieldSelect(siq_field_for_excerpt) {
	$jQu("select[name*='excerptCustomFieldSelect']").each(function () {
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		siq_field_for_excerpt += name + ":" + $jQu(this).val() + ",";
	});
	return siq_field_for_excerpt;
}

function getCutomFieldsToExclude(customFieldsToExclude){
	var oldName 		= "";
	if($jQu("input[name*='customFieldFilterCheckbox']").size() > 0) {
		$jQu("input[name*='customFieldFilterCheckbox']").each(function(){
			var name 	 = $jQu(this).attr('name').match(/\[(.*)\]\[/)[1];
			if(oldName != name){
				customFieldsToExclude = checkLastCharForCommaAndTrim(customFieldsToExclude);
				if(oldName == "") {
					customFieldsToExclude += name + ":";
				}else{
					customFieldsToExclude += ";"+name + ":";
				}
				oldName = name;
			}
			if($jQu(this).is(":checked")){
				customFieldsToExclude += $jQu(this).val()+",";
			}
		});
		customFieldsToExclude = checkLastCharForCommaAndTrim(customFieldsToExclude);
	}
	return customFieldsToExclude;
}

function getCustomTaxonomiesToExclude(customTaxonomiesToExclude){
	var oldNameTaxonomy 		= "";
	if($jQu("input[name*='customTaxonomyFilterCheckbox']").size() > 0) {
		$jQu("input[name*='customTaxonomyFilterCheckbox']").each(function(){
			var name 	 = $jQu(this).attr('name').match(/\[(.*)\]\[/)[1];
			if(oldNameTaxonomy != name){
				customTaxonomiesToExclude = checkLastCharForCommaAndTrim(customTaxonomiesToExclude);
				if(oldNameTaxonomy == "") {
					customTaxonomiesToExclude += name + ":";
				}else{
					customTaxonomiesToExclude += ";"+name + ":";
				}
				oldNameTaxonomy = name;
			}
			if($jQu(this).is(":checked")){
				customTaxonomiesToExclude += $jQu(this).val()+",";
			}
		});
		customTaxonomiesToExclude = checkLastCharForCommaAndTrim(customTaxonomiesToExclude);
	}
	return customTaxonomiesToExclude;
}

function getPostTypesToSearch(thisVarParent, postTypesToSearch){
	thisVarParent.find("input[name*='post_types_for_search']").each(function(){
		if($jQu(this).is(":checked")){
			postTypesToSearch += $jQu(this).val()+",";
		}
	});
	if(postTypesToSearch != ""){
		postTypesToSearch = postTypesToSearch.substring(0, postTypesToSearch.length - 1);
	}
	return postTypesToSearch;
}

function getPostTypesForSearchSelection(thisVarParent, postTypesForSearchSelection){
	thisVarParent.find("select[name*='postTypesSetForSearch']:not('.isDisabled')").each(function(){
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		if(thisVarParent.find("input[name*='post_types_for_search'][value='"+name+"']").is(":checked")){
			postTypesForSearchSelection += name+":"+$jQu(this).val()+",";
		}
	});
	if(postTypesForSearchSelection != ""){
		postTypesForSearchSelection = postTypesForSearchSelection.substring(0, postTypesForSearchSelection.length - 1);
	}
	return postTypesForSearchSelection;
}

function setProp(thisVarParent, addProp){
	if(addProp) {
		thisVarParent.find("input[name*='post_types_for_search'], select[name*='imageCustomFieldSelect'], input[name*='imageCustomFieldName'], input[name*='customFieldFilterCheckbox'], input[name*='customTaxonomyFilterCheckbox'], .customFieldFilterSelect, .excludePostIds,select[name*='excerptCustomFieldSelect'],select[name*='postTypesSetForSearch']:not('.isDisabled'), .blackListUrls").prop("disabled", 'disabled');
	}else{
		thisVarParent.find("input[name*='post_types_for_search'], select[name*='imageCustomFieldSelect'], input[name*='imageCustomFieldName'], input[name*='customFieldFilterCheckbox'], input[name*='customTaxonomyFilterCheckbox'], .customFieldFilterSelect, .excludePostIds,select[name*='excerptCustomFieldSelect'],select[name*='postTypesSetForSearch']:not('.isDisabled'), .blackListUrls").removeProp("disabled");
	}
}

function syncPostsOnFacets(a){
	var currentSection  = $jQu(".section-facets-resync");
	currentSection.show();
	$jQu('html,body').animate({
			scrollTop: currentSection.offset().top - 50},
		'slow');
	var thisVar 		= $jQu("#btnSubmitPosts");
	var thisSection 	= thisVar.parents(".section");
	var thisSectionPre 	= thisVar.parents(".section").prev(".section");
	var thisVarParent   = thisSection.find("input[name*='post_types_for_search']").size() > 0 ? thisSection : thisSectionPre;

	var excludePostIds = (typeof $jQu(".excludePostIds") != "undefined" && $jQu(".excludePostIds").size() > 0) ? $jQu(".excludePostIds").val() : "";
	var blackListUrls = (typeof $jQu(".blackListUrls") != "undefined" && $jQu(".blackListUrls").length > 0) ? $jQu(".blackListUrls").val() : "";
	var siq_field_for_excerpt = "";
	siq_field_for_excerpt = getExcerptCustomFieldSelect(siq_field_for_excerpt);

	flagError	= 0;
	if(typeof a == "undefined"){
		currentPage = 0;
		currentSection.find('.progress-wrap.progress').show();
		currentSection.find('.progressText').show().html("Progress: 0%");
		currentSection.find('.progress-bar.progress').css('left',0);
		currentSection.find('div.msg').remove();
	}

	var setImageCustomField = 1;

	var imageCustomField  			= "";
	var postTypesToSearch 			= "";
	var customFieldsToExclude 		= "";
	var customTaxonomiesToExclude 	= "";
	var postTypesForSearchSelection = "";

	if (setImageCustomField) {
		imageCustomField = getImageCustomFields(imageCustomField);
	}

	customFieldsToExclude 		= getCutomFieldsToExclude(customFieldsToExclude);
	customTaxonomiesToExclude 	= getCustomTaxonomiesToExclude(customTaxonomiesToExclude);
	postTypesToSearch			= getPostTypesToSearch(thisVarParent, postTypesToSearch);
	postTypesForSearchSelection	= getPostTypesForSearchSelection(thisVarParent, postTypesForSearchSelection);
	setProp(thisVarParent, true);

	$jQu.ajax({
		method: "POST",
		url: adminAjax,
		data: { action: "siq_ajax", task: "submit_for_indexing", setImageCustomField: setImageCustomField, imageCustomField: imageCustomField, postTypesToSearch: postTypesToSearch, customFieldsToExclude: customFieldsToExclude, customTaxonomiesToExclude: customTaxonomiesToExclude, siq_field_for_excerpt:siq_field_for_excerpt, excludePostIds: excludePostIds, currentPage : currentPage, security:window.siq_admin_nonce, postTypesForSearchSelection: postTypesForSearchSelection, blackListUrls:blackListUrls },
		crossDomain: true,
		dataType: 'json'
	}).done(function( response ) {
		var res = eval(response);
		currentSection.parents('.data').find('div.msg').remove();
		resAfterSync =  res;
		setProp(thisVarParent, false);
		if(res.success == 0){
			currentSection.find('.data').append('<div class="msg errorMsg">'+res.message+'</div>');
			currentSection.find('.progress-wrap.progress').hide();
			currentSection.find('.progressText').hide();

		}else{
			currentSection.find('.progressText').show();
			if(res.next != ""){
				currentPage = res.next;
				syncPostsOnFacets('simulate');
				moveProgressBar(res.progress,'', currentSection.find('.progressText'));
			}else{
				moveProgressBar(res.progress, res.message, currentSection.find('.progressText'));
				setTimeout(function(){ $jQu(".section-facets-resync").hide();},5000);
				if($jQu(".siq-notices.siq-facets").length > 0){
					$jQu(".siq-notices.siq-facets").remove();
				}
			}
		}
	}).fail(function(jqXHR, textStatus, errorThrown){
		var errorText = "There was some error processing your request, please try later.";
        if(!!jqXHR && !!jqXHR.responseJSON && !!jqXHR.responseJSON.message){
			errorText = jqXHR.responseJSON.message;
        }else if(typeof errorThrown != "undefined" && errorThrown != ""){
            errorText = jqXHR.status+" "+jqXHR.statusText+". Please try again.";
        }
		currentSection.find('.data').append('<div class="msg errorMsg">'+errorText+'</div>');
		setProp(thisVarParent, false);
		currentSection.find('.progressText').hide();
		currentSection.find('.progress-wrap.progress').hide();
	});
}

function resetAllFlags(){
		flag  = 0;
		flagImageSel= 0;
		flagCustomSel=0;
		flagExPidSel=0;
		flagCustomTaxSel=0;
		flagError=0;
		flagCustomExcerptSel=0;
		flagSelectForSearch=0;
}

function showMessage(el, message){
	var length 	= $jQu("input[name*='post_types_for_search']").size();
	var msg		= (typeof message !="undefined" && message != null) ? message : 'Click on <b style="font-size:120%;">'+$jQu("#btnSubmitPosts").val()+'</b> button to index posts to reflect this change in search.';
	if(flag > 0 || flagImageSel > 0 || flagCustomSel > 0 || flagExPidSel > 0 || flagCustomTaxSel > 0 ||  flagError > 0 || flagCustomExcerptSel > 0 ||  flagSelectForSearch > 0 || flagBLUrlsSel > 0){
		el.parents('.data.sep-block').find('.message').remove();
		var msgDiv = '<div class="message messageIndexPosts">'+msg+'</div>';
		el.parents('.data.sep-block').prepend(msgDiv);
		el.parents('.data.sep-block').append(msgDiv);
	}else{
		el.parents('.data.sep-block').find('.message').remove();
	}
}

$jQu(document).on('change', "select[name*='imageCustomFieldSelect']", function(){
    flagImageSel = 0;
	imageTypeSelectionTemp = {};
    $jQu("select[name*='imageCustomFieldSelect']").each(function(){
        var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
        imageTypeSelectionTemp[name] = $jQu(this).val();
    });
    for(i in imageTypeSelection){
        if(imageTypeSelectionTemp[i] !== imageTypeSelection[i]){
            flagImageSel++;
        }
    }
	showMessage($jQu(this));
});

$jQu(document).on('change', "select[name*='postTypesSetForSearch']", function(){
	flagSelectForSearch = 0;
	var lengthS = 0;
	for( i in postTypeForSearchSelection){ 
		if(postTypeForSearchSelection.hasOwnProperty(i)){
			lengthS++;
		} 
	};
	var lengthSelected = 0;
	postTypeForSearchSelectionTemp = {};
	$jQu("select[name*='postTypesSetForSearch']:not('.isDisabled')").each(function(){
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		if($jQu("input[name*='post_types_for_search'][value='"+name+"']").is(":checked")){
			postTypeForSearchSelectionTemp[name] = $jQu(this).val();
			lengthSelected++;
		}
	});
	if(lengthSelected > lengthS){
		flagSelectForSearch++;
	}else{
		for(i in postTypeForSearchSelection){
			if(postTypeForSearchSelectionTemp[i] !== postTypeForSearchSelection[i]){
				flagSelectForSearch++;
			}
		}
	}
	if(flag > 0 || flagImageSel > 0 || flagCustomSel > 0 || flagExPidSel > 0){
			showMessage($jQu(this));
	}else{
		if($jQu("#btnSyncSettings").size() > 0){
				showMessage($jQu(this), 'Click on <b style="font-size:120%;">Save settings</b> button to synchronize settings with SearchIQ.');
		}else{
			showMessage($jQu(this));
		}
	}
});
	
$jQu(document).on('click', "input[name*='post_types_for_search']", function(){
	flag = 0;
	var length = 0;
	var lengthSelected = 0;
	if($jQu(this).is(":checked")){
		$jQu(this).parent('td').next().find("select[name*='postTypesSetForSearch']").val("yes").removeClass('isDisabled').removeAttr("disabled");
	}else{
		$jQu(this).parent('td').next().find("select[name*='postTypesSetForSearch']").val("no").addClass('isDisabled').attr("disabled","disabled");
	}
	postTypeSelectionTemp = {};
	$jQu("#btnSubmitPosts").show();
	$jQu(this).parents('.data').find("h3").show();
	$jQu("input[name*='post_types_for_search']").each(function(){
		if($jQu(this).is(":checked")){
			postTypeSelectionTemp[$jQu(this).val()] = true;
			lengthSelected++;
		}else{
			postTypeSelectionTemp[$jQu(this).val()] = false;
		}
		length++;
	});
	for(i in postTypeSelection){
		if(postTypeSelectionTemp[i] !== postTypeSelection[i]){
			flag++;
		}
	}
	if(lengthSelected == 0){
		$jQu("#btnSubmitPosts").hide();
		$jQu(this).parents('.data').find("h3").hide();
		$jQu(this).parents('.data.sep-block').find('.message').remove();
		var msgDiv = '<div class="message messageIndexPosts">Please select atleast one post type to search</div>';
		$jQu(this).parents('.data.sep-block').prepend(msgDiv);
		if(length > 10) {
			$jQu(this).parents('.data.sep-block').append(msgDiv);
		}
		return;
	}
	showMessage($jQu(this));
});

$jQu(document).on('click', "input[name*='customFieldFilterCheckbox']", function(){

	flagCustomSel = 0;
	excludeCustomFieldsSelectionTemp = {};
	$jQu("#btnSubmitPosts").show();
	$jQu(this).parents('.data').find("h3").show();
	$jQu("select[name*='customFieldFilterSelect']").each(function(){
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		if(typeof excludeCustomFieldsSelectionTemp[name] == "undefined") {
			excludeCustomFieldsSelectionTemp[name] = [];
		}
		$jQu("input[name*='customFieldFilterCheckbox["+name+"]']:checked").each(function(){
			excludeCustomFieldsSelectionTemp[name].push($jQu(this).val());
		});
	});
	for(i in excludeCustomFieldsSelectionTemp){
		if(typeof excludeCustomFieldsSelection[i] != "undefined" && (excludeCustomFieldsSelectionTemp[i].length !== excludeCustomFieldsSelection[i].length)){
			flagCustomSel++;
			break;
		}else{
			for(j in excludeCustomFieldsSelectionTemp[i]){
				if(typeof excludeCustomFieldsSelection[i] != "undefined"){
					if(excludeCustomFieldsSelection[i].indexOf(excludeCustomFieldsSelectionTemp[i][j]) == -1){
						flagCustomSel++;
						break;
					}
				}else{
					flagCustomSel++;
					break;
				}
			}
			if(flagCustomSel > 0){
				break;
			}
		}
	}
	showMessage($jQu(this));
});

$jQu(document).on('click', "input[name*='customTaxonomyFilterCheckbox']", function(){

	flagCustomTaxSel = 0;
	excludeCustomTaxonomySelectionTemp = {};
	$jQu("#btnSubmitPosts").show();
	$jQu(this).parents('.data').find("h3").show();
	$jQu("select[name*='customFieldFilterSelect']").each(function(){
		var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
		if(typeof excludeCustomTaxonomySelectionTemp[name] == "undefined") {
			excludeCustomTaxonomySelectionTemp[name] = [];
		}
		$jQu("input[name*='customTaxonomyFilterCheckbox["+name+"]']:checked").each(function(){
			excludeCustomTaxonomySelectionTemp[name].push($jQu(this).val());
		});
	});
	for(i in excludeCustomTaxonomySelectionTemp){
		if(typeof excludeCustomTaxonomySelection[i] != "undefined" && (excludeCustomTaxonomySelectionTemp[i].length !== excludeCustomTaxonomySelection[i].length)){
			flagCustomTaxSel++;
			break;
		}else{
			for(j in excludeCustomTaxonomySelectionTemp[i]){
				if(typeof excludeCustomTaxonomySelection[i] != "undefined"){
					if(excludeCustomTaxonomySelection[i].indexOf(excludeCustomTaxonomySelectionTemp[i][j]) == -1){
						flagCustomTaxSel++;
						break;
					}
				}else{
					flagCustomTaxSel++;
					break;
				}
			}
			if(flagCustomTaxSel > 0){
				break;
			}
		}
	}
	showMessage($jQu(this));
});

$jQu(document).on('change', "select[name*='excerptCustomFieldSelect']", function(){
	flagCustomExcerptSel = 0;
	$jQu("#btnSubmitPosts").show();
	$jQu(this).parents('.data').find("h3").show();
	
	var name = $jQu(this).attr('name').match(/\[(.*)\]/)[1];
	excerptTypeSelectionTemp[name] = $jQu(this).val();
	
	for(i in excerptTypeSelectionTemp){
		if(typeof excerptTypeSelection[i] != "undefined" && excerptTypeSelection[i] != excerptTypeSelectionTemp[i]){
			flagCustomExcerptSel++;
			break;
		}
	}
	if(flag > 0 || flagImageSel > 0 || flagCustomSel > 0 || flagExPidSel > 0){
			showMessage($jQu(this));
	}else{
		if($jQu("#btnSyncSettings").size() > 0){
				showMessage($jQu(this), 'Click on <b style="font-size:120%;">Save settings</b> button to synchronize settings with SearchIQ.');
		}else{
			showMessage($jQu(this));
		}
	}
});

$jQu(document).on('keyup onfocusout', '.excludePostIds', function(){
	excludePostsSelectionTemp = $jQu(this).val();
	flagExPidSel = 0;
	flagError = 0;
	var length = $jQu("select[name*='customFieldFilterSelect']").size();
	if(excludePostsSelectionTemp != excludePostsSelection){
		flagExPidSel = 1;
	}
	showMessage($jQu(this));
	if(excludePostsSelectionTemp != "") {
		var isValid = excludePostsSelectionTemp.match(/^[0-9\,]+$/);
		if(isValid == null) {
			flagError = 1;
			showMessage($jQu(this), "<b style='font-size:120%;'>Error:</b> Post Id's can only have numeric id's separated by commas(without spaces). Please check the post id's you have entered and Sync again.");
			return false;
		}else{
			showMessage($jQu(this));
		}
	}
});

$jQu(document).on('keyup onfocusout', '.blackListUrls', function(){
	excludeBlackListUrlsTemp = $jQu(this).val();
	flagBLUrlsSel = 0;
	if(excludeBlackListUrlsTemp != excludeBlackListUrls){
		flagBLUrlsSel = 1;
	}
	showMessage($jQu(this));
});
$jQu(document).on('click', '.tabsHeading a', function(){
	var thisId = $jQu(this).parent().attr("id");
	$jQu(".tabsContent").addClass("showLoader");
	$jQu(".tabsHeading li").removeClass("selected").addClass("notselected");
	$jQu(this).parent().removeClass('notselected').addClass('selected');
	$jQu(".tabsContent div.tab").removeClass('selected').addClass("notselected");
	$jQu(".tabsContent div."+thisId).removeClass('notselected').addClass('selected');
});
window.onload = function(){ $jQu(".tabsContent").removeClass("showLoader"); }
$jQu(document).on('click', '.customFieldFilterSelect', function(){
	var selectboxDiv = $jQu(this).parents('.selectorOuter').find('.selectorInner');
	$jQu('.selectorInner').not(selectboxDiv).removeClass('showThis');
	$jQu('.customFieldFilterSelect').not($jQu(this)).removeClass('selectedBox');

	if(selectboxDiv.hasClass('showThis')){
		$jQu(this).removeClass('selectedBox');
		selectboxDiv.removeClass('showThis');
	}else{
		$jQu(this).addClass('selectedBox');
		selectboxDiv.addClass('showThis');
	}
	return false;
});

$jQu(document).on('click', 'body', function(evt){
	if($jQu(evt.target).closest('.selectorInner').length || $jQu(evt.target).closest('.customFieldFilterSelect').length)
		return;
	$jQu('.customFieldFilterSelect').removeClass('selectedBox');
	$jQu('.selectorInner').removeClass('showThis');

});

$jQu(document).on('click', '.top .checkAll', function(evt) {
	if ($jQu(this).is(":checked")) {
		$jQu(this).parent('li').nextUntil('li.top', "li").find("input[type='checkbox']").attr("checked", "checked");
	}else{
		$jQu(this).parent('li').nextUntil('li.top', "li").find("input[type='checkbox']").removeAttr("checked");
	}
});
$jQu(document).on('click', '#btnSyncSettings', function(evt) {
	var thisEl =  $jQu(this);
	var dataDiv = thisEl.parents(".data");
	var thisSection = thisEl.parents(".section");
	var thisVarParent   = thisSection;
	thisSection.find(".msg").remove();
	
	var excludePostIds = (typeof $jQu(".excludePostIds") != "undefined" && $jQu(".excludePostIds").size() > 0) ? $jQu(".excludePostIds").val() : "";
	var blackListUrls = (typeof $jQu(".blackListUrls") != "undefined" && $jQu(".blackListUrls").length > 0) ? $jQu(".blackListUrls").val(): "";
	var siq_field_for_excerpt = "";
	siq_field_for_excerpt = getExcerptCustomFieldSelect(siq_field_for_excerpt);
	
	flagError	= 0;
	if(excludePostIds != "") {
		flagError = 1;
		var isValid = excludePostIds.match(/^[0-9\,]+$/);
		if(isValid == null) {
			showMessage($jQu(".excludePostIds"), "<b style='font-size:120%;'>Error:</b> Post Id's can only have numeric id's separated by commas(without spaces). Please check the post id's you have entered and Sync again.");
			return false;
		}else{
			$jQu(".excludePostIds").parents('.data.sep-block').find('.message').remove();
		}
	}

	var setImageCustomField = 1;

	var imageCustomField  			= "";
	var postTypesToSearch 			= "";
	var customFieldsToExclude 		= "";
	var customTaxonomiesToExclude 	= "";
	var postTypesForSearchSelection = ""; 
	
	if (setImageCustomField) {
		imageCustomField = getImageCustomFields(imageCustomField);
	}

	customFieldsToExclude 		= getCutomFieldsToExclude(customFieldsToExclude);
	customTaxonomiesToExclude 	= getCustomTaxonomiesToExclude(customTaxonomiesToExclude);
	postTypesToSearch			= getPostTypesToSearch(thisVarParent, postTypesToSearch);
	postTypesForSearchSelection	= getPostTypesForSearchSelection(thisVarParent, postTypesForSearchSelection);
	setProp(thisVarParent, true);

	
	thisEl.addClass("ajaxLoading");
	thisEl.prop("disabled", "disabled");
	dataDiv.find('.msg').remove();
	
	$jQu.ajax({
		method: "POST",
		url: adminAjax,
		data: { action: "siq_ajax", task: "save_sync_settings", setImageCustomField: setImageCustomField, imageCustomField: imageCustomField, postTypesToSearch: postTypesToSearch, customFieldsToExclude: customFieldsToExclude, customTaxonomiesToExclude: customTaxonomiesToExclude, siq_field_for_excerpt:siq_field_for_excerpt, excludePostIds: excludePostIds, currentPage : currentPage, security:window.siq_admin_nonce, postTypesForSearchSelection:postTypesForSearchSelection, blackListUrls:blackListUrls},
		crossDomain: true,
		dataType: 'json'
	}).done(function( response ) {
		thisEl.removeClass("ajaxLoading");
		dataDiv.find('.msg').remove();
		if(response.success != false){
			dataDiv.append('<div class="msg success">'+response.message+'</div>');	
		}else{
			dataDiv.append('<div class="msg errorMsg">'+response.message+'</div>');	
		}
		excerptTypeSelection = excerptTypeSelectionTemp;
		excerptTypeSelectionTemp = {};
		dataDiv.find('.message').remove();
		thisEl.removeAttr("disabled");
		setProp(thisVarParent, false);
	}).fail(function(response){
		thisEl.removeClass("ajaxLoading");
		thisEl.parents('.data').find('.msg').remove();
		var res = eval(response);
		if(response.responseJSON.success != false){
			dataDiv.append('<div class="msg success">'+response.responseJSON.message+'</div>');	
		}else{
			dataDiv.append('<div class="msg errorMsg">'+response.responseJSON.message+'</div>');	
		}
		thisEl.removeAttr("disabled");
		setProp(thisVarParent, false);
	});
	
});


function SIQ_validateMobileSettingsForm() {
	var valid = true;

	var faviconInput = document.getElementById("mobileSearchBarFavicon");
	if (faviconInput) {
		if (faviconInput.value.length > 0 && !/^https?:\/\/(([\w\-_]+\.)+[a-z]{2,3}|localhost|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(\/.*)?$/i.test(faviconInput.value)) {
			valid = false;
			faviconInput.nextElementSibling.style="display:block!important;";
		} else {
			faviconInput.nextElementSibling.style="";
		}
	} else {
		valid = false;
	}

	return valid;
}

// Facets

function SIQ_getNextFacetIndex() {
    if ($jQu('.siq-facet-item').length == 0) {
        return 0;
    } else {
        var index = 0;
        $jQu('.siq-facet-item').each(function(key, el) {
            var nextIndex = el.getAttribute("id").replace(/siq-facet-item-(\d+)/, '$1') - 0 + 1;
            index = Math.max(index, nextIndex);
        });
        return index;
    }
}

function SIQ_addNewFacet() {
    if (typeof SIQ_addNewFacet.index === "undefined") {
        SIQ_addNewFacet.index = SIQ_getNextFacetIndex();
    }
	var index = SIQ_addNewFacet.index;
	if ($jQu(".siq-no-facets").length > 0) {
		$jQu(".siq-no-facets").remove();
	}
	var formHtml = "<div id='siq-facet-item-##NUM##' class='siq-facet-item'>" +
        "<table>" +
        "<tr><td>" +
        "<label>" +
        "Label</label></td><td>" +
        "<input name='siqFacetLabel[]' required/>" +
        "</td>" +
        "<td><label>Post type</label></td>" +
        "<td><select name='siqFacetPostType[]' onchange='SIQ_buildFacetFieldSelectBox(##NUM##, this);'>" + SIQ_getPostTypeOptions() + "</select></td>" +
        "<td>" +
        "<label>" +
        "Field</label></td><td>" +
        "<select name='siqFacetField[]' required onchange='SIQ_changeFacetField(##NUM##);'>" + SIQ_buildFacetFieldSelectBox(index) + "</select>" +
        "<input type='hidden' name='siqFacetTargetField[]' value=''/>" +
        "</td>" +
        "<td class='siqFacetType'>" +
        "<label>Type</label>" +
        "</td><td class='siqFacetType'>" +
        "<select name='siqFacetType[]' required onchange='SIQ_changeFacetType(##NUM##);'>" +
        "<option value=''></option>" +
        "<option value='string'>String</option>" +
        "<option value='number'>Number</option>" +
        "<option value='rating'>Rating</option>" +
        "<option value='date'>Date</option>" +
        "</select>" +
        "</td>" +
        "<td class='siqDateFormat hidden'>" +
        "<label>" +
        "Date format</label></td><td class='siqDateFormat hidden'>" +
        "<input type='text' name='siqFacetDateFormat[]'/>" +
        "</td></tr>" +
        "<tr><td colspan='8'>" +
        "<a href='javascript:SIQ_moveFacetUp(##NUM##);' class='siq-facet-move-up hidden'>Move up</a> " +
        "<a class='siq-facet-move-down hidden' href='javascript:SIQ_moveFacetDown(##NUM##);'>Move down</a> " +
        "<a class='siq-facet-remove' href='javascript:SIQ_removeFacet(##NUM##);'>Remove</a>" +
        "</td></tr>" +
        "</table>" +
        "</div>";
	var $items = $jQu('.siq-facet-item');
	formHtml = formHtml.replace(/##NUM##/g, index);
	$jQu("#siq-facet-form").append(formHtml);
	if ($items.length > 0) {
		$items.last().find(".siq-facet-move-down").removeClass("hidden");
		$jQu(".siq-facet-item").each(function(i, elm) {
			if (i > 0) {
				$jQu(elm).find(".siq-facet-move-up.hidden").removeClass("hidden");
			}
		});
	}
    SIQ_addNewFacet.index++;
    if ($jQu('.siq-facet-item').length >= 5) {
        $jQu("#btnAddFacet").addClass("hidden");
    }
}

function SIQ_changeFacetType(id) {
	var elm = $jQu("#siq-facet-item-" + id);
	if (elm.find("select[name='siqFacetType[]']").val() === "date" && elm.find("select[name='siqFacetField[]']").val() !== "timestamp") {
		elm.find(".siqDateFormat.hidden").removeClass("hidden");
	} else {
		elm.find(".siqDateFormat").addClass("hidden");
	}

	if (elm.find("select[name='siqFacetType[]']").val() === "date" && elm.find("select[name='siqFacetField[]']").val() === "timestamp") {
		elm.find("input[name='siqFacetDateFormat[]']").val("Y-m-d\\TH:i:s\\.\\0\\0\\0");
	} else if (elm.find("select[name='siqFacetType[]']").val() === "date") {
		elm.find("input[name='siqFacetDateFormat[]']").val("");
	}

	if (elm.find("select[name='siqFacetType[]']").val() === "date") {
		elm.find("input[name='siqFacetDateFormat[]']").attr("required", "required");
	} else {
		elm.find("input[name='siqFacetDateFormat[]']").removeAttr("required");
	}
}

function SIQ_changeFacetField(id) {
    var elm = $jQu("#siq-facet-item-" + id);
    var option = elm.find("select[name='siqFacetField[]'] option:selected");
    if (option.is("[data-targetfield]") && option.is("[data-facettype]")) {
        var type = option.attr("data-facettype");
        elm.find(".siqFacetType, .siqDateFormat").addClass("hidden");
        elm.find("select[name='siqFacetType[]']").val(type);
        elm.find("input[name='siqFacetTargetField[]']").val(option.attr("data-targetfield"));
        if (type === "date") {
            elm.find("input[name='siqFacetDateFormat[]']").attr("required", "required");
            elm.find("input[name='siqFacetDateFormat[]']").val(option.attr("data-dateformat"));
        } else {
            elm.find("input[name='siqFacetDateFormat[]']").removeAttr("required");
            elm.find("input[name='siqFacetDateFormat[]']").val("");
        }
    } else {
        elm.find(".siqFacetType, .siqDateFormat").removeClass("hidden");
        elm.find("select[name='siqFacetType[]']").val("");
        elm.find("input[name='siqFacetDateFormat[]']").val("");
        elm.find("input[name='siqFacetTargetField[]']").val("");
        SIQ_changeFacetType(id);
    }
}

function SIQ_buildFacetFieldSelectBox(id, el) {
    var postType = typeof el === "undefined" ? null : el.value;
	$jQu.ajax({
		"method": "POST",
		"url": adminAjax,
		"data": {"action": "siq_ajax", "postType": postType, "task": "get_all_document_fields_option_list_for_facet", "security": siq_admin_nonce},
		"dataType": "json"
	}).done(function (response) {
		var res = eval(response);
		if (res.html) {
			$jQu("#siq-facet-item-" + id + " select[name='siqFacetField[]']").html(res.html);
		} else {
			$jQu("#siq-facet-item-" + id + " select[name='siqFacetField[]']").html("");
			$jQu("#siq-facet-item-" + id + " select[name='siqFacetField[]']").after("<div class='error'>Cannot load list of custom fields</div>");
		}
	});
    if (postType === null) {
        return '<option value="">Loading...</option>';
    }
}

function SIQ_moveFacetUp(id) {
	var el = $jQu("#siq-facet-item-" + id)[0];
	if (el.previousElementSibling !== null) {
		el.parentElement.insertBefore(el, el.previousElementSibling);
	}
    SIQ_fixFacetMoveLinks();
}

function SIQ_moveFacetDown(id) {
	var el = $jQu("#siq-facet-item-" + id)[0];
	if (el.nextElementSibling !== null) {
		el.parentElement.insertBefore(el.nextElementSibling, el);
	}
    SIQ_fixFacetMoveLinks();
}

function SIQ_removeFacet(id) {
    var el = $jQu("#siq-facet-item-" + id)[0];
    el.parentElement.removeChild(el);
    SIQ_fixFacetMoveLinks();
    $jQu("#btnAddFacet").removeClass("hidden");
	if ($jQu(".siq-facet-item").length == 0) {
		$jQu("#siq-facet-form").append("<div class='siq-no-facets'>No any facet created. Click &laquo;Add facet&raquo; button.</div>");
	}
}

function SIQ_fixFacetMoveLinks() {
    var $items = $jQu('.siq-facet-item');
    if ($items.length > 1) {
        $items.find(".siq-facet-move-up, .siq-facet-move-down").removeClass("hidden");
        $items.first().find(".siq-facet-move-up").addClass("hidden");
        $items.last().find(".siq-facet-move-down").addClass("hidden");
    } else {
        $items.find(".siq-facet-move-up, .siq-facet-move-down").addClass("hidden");
    }
}

function SIQ_getPostTypeOptions() {
    var html = "<option value='_siq_all_posts'>All types</option>",i;
    for(i = 0; i < SIQ_postTypes.length; ++i) {
        html += "<option value='" + SIQ_postTypes[i] + "'>" + SIQ_postTypes[i] + "</option>";
    }
    return html;
}

$jQu(document).on('click', "#siq_default_thumbnail_uploader", function(e) {
     var thisEl = $jQu(this);
	 var arrImageExtensions = ['jpg', "jpeg", "jpe","gif", "png", "bmp", "tif", "tiff"];
	e.preventDefault();
	console.log("click here");
	var custom_uploader = wp.media({
		title: "Upload and set image as default thumbnail here ",
		button: {
			text: "Set as default thumbnail"
		},
		multiple: 0
	})
	.on('select', function() {
		var attachment = custom_uploader.state().get('selection').first().toJSON();
		
		var len = attachment.url.length;
		var attachmentExt = attachment.url.substring(len-4, len);
		
		var imageSelected = 0;
		for(i in arrImageExtensions){
			if(attachmentExt.indexOf(arrImageExtensions[i]) != -1){
				imageSelected = 1;
				break;
			}
		}
		if(imageSelected){
			$jQu( "input[name^='siq_default_thumbnail']").val(attachment.url);
		}else{
			alert("Selected file is not a valid image file"); 
			$jQu(thisEl).trigger("click");
		}
	})
	.open();
});
