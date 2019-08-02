jQuery(document).ready(function(){
    jQuery("#gen_data").click(function(){
       jQuery(this).addClass('regenerating'); 
	   document.getElementById("gen_data").textContent="Regenerating Data";
	  
    });
	jQuery(".wooC_form_button").click(function(e){
		e.preventDefault();
		id = jQuery(this)[0].id
		ident = id.split("_");
		jQuery("#wooC_form_" + ident[3] + " input").prop("disabled", false);
	});
	jQuery(".wooC_form_cancel_button").click(function(e){
		e.preventDefault();
		id = jQuery(this)[0].id
		ident = id.split("_");
		jQuery("#wooC_form_" + ident[3] + " input").prop("disabled", true);
	});
	//  When user clicks on tab, this code will be executed
	jQuery(".tab-menu a").click(function() {
		//  First remove class "active" from currently active tab
		jQuery(".tab-menu a").removeClass("active");

		//  Now add class "active" to the selected/clicked tab
		jQuery(this).addClass("active");

		//  Hide all tab content
		jQuery(".white-box .tab").hide();

		//  Here we get the href value of the selected tab
		var selected_tab = jQuery(this).attr("href");

		//  Show the selected tab content
		jQuery(selected_tab).show();

		//  At the end, we add return false so that the click on the link is not executed
		return false;
	});
	
	 //  When user clicks on tab, this code will be executed
	jQuery(".add-address-information a").click(function() {

		//  Hide all tab content
		jQuery(".add-address-information").hide();

		//  Show the selected tab content
		jQuery(".billing-shipping").show();

		return false;
	});
		
	
});