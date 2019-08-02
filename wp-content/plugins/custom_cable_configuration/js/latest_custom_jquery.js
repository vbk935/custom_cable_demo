//get the total passes to configure the product
var total_parts_to_configure = $('.parent-element').length;
var name="";
var product_info = []; 


function getProductInfo(parent_type,child_type,group_id,val_input,display_type,cur_val)
{
	product_info['type'] = parent_type;
	product_info['value'] = child_type;
	product_info['val_input'] = val_input;
	product_info['display_type']=display_type;
	if(product_info['val_input'] == ''){
		var val_input = $('.lengthinput').val();
	} else {
		var val_input = product_info['val_input'];
	}
	jQuery('#loader-image').removeClass('hide');
	jQuery('.pro_id input[name="add-to-cart"]').val('');
	var idp = $(".activeValue").children('input[name^=idp]').map(function(idx, elem) {
			return $(elem).val();
		}).get();

	jQuery.ajax({		
		method: "POST",
		url:ajaxPath+"get_product_info.php",
		dataType: "json",
		data: {'curval':cur_val, 'total_parts':total_parts_to_configure,'group_id':group_id,'product_type': product_info['type'],'product_value':product_info['value'],'product_value_input':val_input,'product_display_type':product_info['display_type'],'selected_val':idp},
		success: function(response) {
			var check_arr = response.check_arr;	
			if(check_arr != '' || check_arr != null)				
			{
				jQuery.each(check_arr, function(i,val) 
				{				
					$('.config_id_'+val).parent().hide(); 
			 	});	
			}

			if(response.config_price == null || response.config_price == '')
			{
				jQuery(".price").html('');
				jQuery(".price").hide();
			}
			if($(".parent-element.active").next().is('li')) {
				jQuery('.parent-element.active').next('.parent-element').removeAttr("disabled").addClass('active').addClass("added");
				jQuery('.parent-element.active').children('.sub-content').show();
				jQuery('.parent-element.active').prev('.parent-element').removeClass("active").addClass("added");
				jQuery('.parent-element.active').prev('.parent-element').children('.sub-content').hide();
			}
			else 
			{
				$('.sub-content').hide();
				jQuery('.parent-element.active').removeClass("active").addClass("added");
			}	

			if(response.matched_product_id != '')
			{

				jQuery(".product_desc").show();
				jQuery(".product_desc").html(response.excerpt);		
				if(response.part_number != null && cur_val == total_parts_to_configure)
				{
				var canvas = document.getElementById('canvas_config');
				var context = canvas.getContext('2d');
				context.clearRect(350, 495, 300,55);
				context.font="16px Comic Sans MS";
				context.fillStyle = "black";
				context.textAlign = "center";						
				context.fillText('Part Number = '+response.part_number, 500, 510);

				}	
				jQuery('.add-to-cart').show();				
				jQuery('.request-to-admin').hide();
				jQuery('.pro_id input[name="add-to-cart"]').val(response.matched_product_id);
				jQuery(".add-to-cart").removeAttr('disabled');
				jQuery(".add-to-cart").removeClass('disable-btn');
				jQuery('.add-to-cart').val("Add to cart");									
				jQuery('form.cart-config').attr('action',response.action);					
				if(response.config_price != null && cur_val == total_parts_to_configure)
				{
					//alert("in if");
					var canvas = document.getElementById('canvas_config');
					var context = canvas.getContext('2d');
					context.font="16px Comic Sans MS";
					context.fillStyle = "black";
					context.textAlign = "center";
					context.clearRect(740, 495,130,55);
					context.fillText('Price: $'+response.config_price, 800, 510);
				}			

			} 
			else if(cur_val == total_parts_to_configure  && response.matched_product_id == '')
			{			
				jQuery('.add-to-cart').hide();
				jQuery(".price").html('');
				jQuery(".price").hide();
				jQuery('.partNo').html('');	
				jQuery('.request-to-admin').show();
				if(response.part_number != null)
				{

				var canvas = document.getElementById('canvas_config');
				var context = canvas.getContext('2d');
				context.clearRect(350, 495, 300,55);
				context.font="16px Comic Sans MS";
				context.fillStyle = "black";
				context.textAlign = "center";						
				context.fillText('Part Number = '+response.part_number, 500, 510);
					
				}
				jQuery(".request-to-admin").removeAttr('disabled');
				jQuery(".request-to-admin").removeClass('disable-btn');
			}
			else 
			{
				jQuery(".product_desc").hide();
				var canvas = document.getElementById('canvas_config');
				var context = canvas.getContext('2d');
				context.clearRect(350, 495, 300,55);
				context.clearRect(740, 495,130,55);
			}		
	
		},
		complete: function(){
			jQuery('#loader-image').addClass('hide');
		}
	});
}
function occurrences(string, substring){
    var n=0;
    var pos=0;

    while(true){
        pos=string.indexOf(substring,pos);
        if(pos!=-1){ n++; pos+=substring.length;}
        else{break;}
    }
    return(n);
}
function requestToAdmin(config_id)
{
	jQuery.ajax({
		method: "POST",
		url:ajaxPath+"request_to_admin.php",
		dataType: "json",	
		data: {'config_id':config_id},
		success: function(response) {					
			jQuery('.alert').show();
			if(response.final_pass=="yes")	
			{					
				jQuery('.alert').addClass("alert-success");
			}
			else 
			{
				jQuery('.alert').addClass("alert-danger");					
			}
			jQuery('.alert_msg').text(response.msg);					
		},
	}); 
}
function activeFirstDisableAllOther()
{
	//default add disbaled to all other except first selection
	jQuery('#steps-customs').each(function( index ) {
		jQuery('#steps-customs .parent-element').removeClass('added').removeClass('active');
		jQuery('#steps-customs .parent-element').removeClass('newActiveCheck');
	});
	//make the first selection active by default
	var get_first = jQuery("#first_selected").val();

	if(get_first)
	{
		jQuery("#steps-customs li").first().addClass("added").removeAttr("disabled");
		jQuery("#steps-customs li").first().next('.parent-element').removeAttr("disabled").addClass('active').addClass("added");
		jQuery(".config_id_"+get_first).parent('li').addClass('activeValue');
		jQuery(".config_id_"+get_first).parent('li').addClass('canvasClass');
		jQuery(".activeValue").css("background", "#f3f3f3 none repeat scroll 0 0");
		jQuery(".config_id_"+get_first).addClass('checked_value');
	}
	else 
	{
		jQuery("#steps-customs li").first().addClass("active").removeAttr("disabled");
		jQuery("#steps-customs li").first().children('.sub-content').show();
		jQuery("#steps-customs li").first().children('.sub-content').children('li').removeClass('activeValue');
		jQuery("#steps-customs li").first().children('.sub-content').children('li').removeClass('canvasClass');
		jQuery("#steps-customs li").first().children('.sub-content').children('li').removeAttr('style');
		jQuery("#steps-customs li").first().children('.sub-content').children('li').removeAttr('class');
	}
}
jQuery(document).ready(function(){

	activeFirstDisableAllOther();
//make the current class inactive ,add class added and active the next option to select
jQuery(document.body).on('click', '.nextPage' ,function(event)
{
	var cur_val = jQuery('.parent-element.active').attr('data-value');
	var input_data_val = jQuery('.lengthinput').parent('li').parent('ul').parent('li').data('value');
	var input_val = jQuery('.lengthinput').val();
	if(cur_val < input_data_val && input_val != "")
	{
		jQuery('.lengthinput').val("");
		jQuery('.request-to-admin').hide();
		jQuery('.add-to-cart').hide();		
		jQuery('.partNo').html('');
	}	
	event.preventDefault();
	var parent_type=jQuery('.checked_value').parent('li').parent('ul').parent('li').attr('parent_type');
	var child_type=jQuery('.checked_value').attr('data-id');
	var group_id=jQuery('.checked_value').parent('li').parent('ul').parent('li').attr('group_id');
	var class_val = jQuery('.checked_value').parent('li').parent('ul').parent('li').hasClass('hidden_field');
	if(class_val == true){
		var display_type = "1";
		getProductInfo(parent_type,child_type,group_id,val_input,display_type,cur_val);
	} else {
		var val_input = '';
		var display_type = '';
		getProductInfo(parent_type,child_type,group_id,val_input,display_type,cur_val);
	}
	return false;
});
//to get value of changeable field. 
jQuery(document.body).on('click', '.addlengthinput' ,function(event){
	var cur_val = jQuery('.parent-element.active').attr('data-value');
	var val_input=jQuery(".lengthinput").val();
	var count= occurrences(val_input,'.');
	if(val_input == '')
	{
		alert("Please enter the required length.");
		return false;
	}
	if(val_input == 0 || count > 1){
		alert('The given value in not valid');
		return false;
	}	
	if(val_input.length > 7)
	{
		alert('Maximum 7 digits are allowed');
		return false;
	}
	
	event.preventDefault();
	var parent_type=jQuery(this).parent('li').parent('ul').parent('li').attr('parent_type');
	var child_type=jQuery(this).attr('data-id');
	var group_id=jQuery(this).parent('li').parent('ul').parent('li').attr('group_id');
	var class_val = jQuery(this).parent('li').parent('ul').parent('li').hasClass('hidden_field');					
	if(class_val == true){		
		var display_type = "1";
		getProductInfo(parent_type,child_type,group_id,val_input,display_type,cur_val);
	} else {		
		var display_type = '';
		getProductInfo(parent_type,child_type,group_id,val_input,display_type,cur_val);
	}
	return false;
});
jQuery(document.body).on('click','.added',function(event){
	event.preventDefault();
	var target = $(event.target);
	if (target.attr('class') != 'pro-lbl') {
		if (target.attr('id') != 'pro-lbl') {
			if($(this).hasClass('added') == true && $(this).hasClass('active') == false){
				$('.sub-content').hide();
				$(this).children('.sub-content').show();
				jQuery('#steps-customs').each(function( index ) {
					jQuery(this).find('.active').removeClass('active');
					jQuery(this).find('.newActiveCheck').removeClass('newActiveCheck');
				});
				$(this).addClass('active');
			}else if($(this).hasClass('added') == true && $(this).hasClass('active') == true){
				$(this).removeClass('active');
				$(this).addClass('newActiveCheck');
				$(this).children('.sub-content').hide();
			}
		}
	}
}); 
//if clicked on disabled
jQuery(document.body).on('click','.parent-element',function(event){
	event.preventDefault();
	return false;
});
//clicked on reset button
jQuery('#reset_config').click(function() {

	jQuery('.request-to-admin').hide();
	jQuery('.add-to-cart').hide();
	jQuery('.partNo').html('');
	jQuery('.pro_id input[name="add-to-cart"]').val('');
	jQuery('.add-to-cart').addClass('disable-btn');
	jQuery('.add-to-cart').attr("disabled", "true");
	jQuery("#steps-customs li").children('.sub-content').hide();
	var canvas = document.getElementById('canvas_config');
	var context = canvas.getContext('2d');
	context.clearRect(0, 0, canvas.width, canvas.height);
	jQuery(".lengthinput").val("");
	activeFirstDisableAllOther();
	var config = getUrlVars()["config"];
	var group_id = getUrlVars()["group_id"];
	if(group_id){
		var groId =  group_id;
	}else{
		var groId =  '';
	}
	var l = window.location;
	var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
	var url = base_url+'/configure-product/?config='+config+'&group_id='+groId;
	window.location.href = url;
	return false;
});

function getUrlVars()
{
  var vars = [], hash;
  var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
  for(var i = 0; i < hashes.length; i++)
  {
    hash = hashes[i].split('=');

    if($.inArray(hash[0], vars)>-1)
    {
        vars[hash[0]]+=","+hash[1];
    }
    else
    {
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
  }
  return vars;
}
jQuery('#reset_config').click(function(){	
	activeFirstDisableAllOther();
	jQuery('.pro_id input[name="add-to-cart"]').val('');
	jQuery('.add-to-cart').addClass('disable-btn');
	jQuery('.add-to-cart').attr("disabled", "true");
	$.ajax({
		type: "POST",
		url: ajaxPath+"get_product_info.php",
		data: "action=session_des",
		success: function(msg){   
		}
	});
})
});
