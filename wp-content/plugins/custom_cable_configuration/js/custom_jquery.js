jQuery(document).ready(function(){
//disable the cart button
jQuery("#add_to_cart_config").css('background-color','gray').css('text-decoration','none').css('cursor','default');
//get the total passes to configure the product
var total_parts_to_configure = $('.config_single_outer ').length;
disableAndFirstSelect();
//make first active and all other disabled
function disableAndFirstSelect()
{
//default add disbaled to all the options to get select
jQuery('.canvas_sidebar').each(function( index ) {
	jQuery('.config_single_outer').attr("disabled","disabled").removeClass('added');
});
//make sure first option is selected
jQuery(".canvas_sidebar div").first().addClass("active").removeAttr("disabled");
}

var name="";
var product_info = [];  
 //common function
 function Common_config($case)
 {
 	var configure_type=$case;
 	jQuery('.single_heading').click(function(){
 		var parent_type=jQuery(this).attr('parent_type');
 		var child_type=jQuery(this).attr('child_type');
 		var group_id=jQuery(this).attr('group_id');
 		product_info['type'] = parent_type;
 		product_info['value'] = child_type;
 		product_info.push({type: parent_type,value: child_type}); 
 		if(configure_type =='B') {
	//close the other active and make the current active already configured as active
	jQuery('.canvas_sidebar').find('.config_single_outer').each(function() {
	//deactive the active open
	jQuery(document).find('.config_single_outer.active').removeClass("active");
});
	jQuery(this).parent('.added').addClass('.active');
	
}
else{
	//make the current class inactive and add class added 
	jQuery(this).parent('.sub_config').parent('.config_single_outer').removeClass("active").addClass("added");
	//make the next class active with open options
	jQuery(this).parent('.sub_config').parent('.config_single_outer').next('.config_single_outer:first').removeAttr("disabled").addClass('active');
}


jQuery.ajax({
	method: "POST",
	url:ajaxPath+"/get_product_info.php",
	dataType: "json",
	data: {'total_parts':total_parts_to_configure,'group_id':group_id,'product_info': product_info},
	success: function(response) {
		if(response.success && response.final_pass=="yes" )
		{
					//active the cart button
					jQuery("#add_to_cart_config").css('background-color','#C72222').css('text-decoration','line').css('cursor','cursor');
					
					
				}
				
			}
		});
           // return false;
       });
 	
 }

 jQuery('.canvas_sidebar div').first().find('.sub_config').each(function() {
 	Common_config("A");
 });

 jQuery(document.body).on('click', '.added .main_title' ,function(){
 	Common_config("B");
 });

 jQuery("#reset_config").on('click',function(){
 	clearCanvas();
 });
//clear the canvas on click of reset
function clearCanvas() {
	/* Add image to canvas */
	var canvas = document.getElementById('canvas_config');
	var context = canvas.getContext('2d');
	var imageObj = new Image();
	context.clearRect(0, 0, 0, 0);
//deactive the active open
jQuery(document).find('.config_single_outer.active').removeClass("active");
disableAndFirstSelect();

}



});
