jQuery(document).ready(function(){	

	//Update Products Menu
	if (jQuery('#mega-menu-3472-0-0').is(':empty')){
	    jQuery("#mega-menu-3472-0-0").remove();
	}
	if (jQuery('#mega-menu-3472-0-1').is(':empty')){
	    jQuery("#mega-menu-3472-0-1").remove();
	}
	if (jQuery('#mega-menu-3472-0-2').is(':empty')){
	    jQuery("#mega-menu-3472-0-2").remove();
	}
	if (jQuery('#mega-menu-3472-0-3').is(':empty')){
	    jQuery("#mega-menu-3472-0-3").remove();
	}


	if (jQuery('#mega-menu-3472-1-0').is(':empty')){
	    jQuery("#mega-menu-3472-1-0").remove();
	}
	if (jQuery('#mega-menu-3472-1-1').is(':empty')){
	    jQuery("#mega-menu-3472-1-1").remove();
	}
	if (jQuery('#mega-menu-3472-1-2').is(':empty')){
	    jQuery("#mega-menu-3472-1-2").remove();
	}
	if (jQuery('#mega-menu-3472-1-3').is(':empty')){
	    jQuery("#mega-menu-3472-1-3").remove();
	}


	var row0_size = (jQuery('#mega-menu-3472-0 ul .mega-sub-menu').size())/2;
	var row1_size = (jQuery('#mega-menu-3472-1 ul .mega-sub-menu').size())/2;
	//console.log("row0_size = " + row0_size);
	//console.log("row1_size = " + row1_size);


	var row0_required = 4 - row0_size;
	//console.log("row0_required = " + row0_required);
	if(row0_required > 0)
	{
	    if(row1_size > row0_required)
	    {	    
	        for (i = 0; i < row0_required; ++i)
	        {                                          
	           jQuery('#mega-menu-3472-0').append(jQuery('#mega-menu-3472-1-'+i));
	        }
	    }

	    if(row1_size <= row0_required)
	    {	    
	        for (i = 0; i < row1_size; ++i)
	        {                                          
	           jQuery('#mega-menu-3472-0').append(jQuery('#mega-menu-3472-1-'+i));
	        }
	    }
	}

	jQuery(".save-order").click(function(){
		jQuery(".notice-success").show();
		jQuery('.notice-success p').html("Item Updated.");		
		jQuery(window).scrollTop(0);
	});	
			
	if (jQuery(".sortable").length > 0) 
	{
		jQuery(".sortable").sortable({
			update: function( event, ui ) 
			{
				var get_itemid = ui.item.attr("id");
				var split_get_itemid = get_itemid.split("_");
				var itemid = split_get_itemid[1];
				var get_parentId = jQuery('#'+get_itemid).parent().parent().attr('id');
				var split_get_parentId = get_parentId.split("_");
				var parentId = split_get_parentId[1];	
				jQuery.ajax({
					method: "POST",
					url:cableconfiguration.pluginsUrl+"/ajax/get_config_condition.php",
					dataType: "json",
					data: {'parentId':parentId},
					success: function(response) 
					{						
						if(response.success == true)
						{
							var get_length = jQuery('#'+get_itemid).find("ul").length;
							if(get_length == 1)
							{
								if(confirm("All Configuration Conditions of this group will be deleted. If you want to proceed than click on OK otherwise click on Cancel.") == true)					
								{
									jQuery.ajax({
										method: "POST",
										url:cableconfiguration.pluginsUrl+"/ajax/get_item_config_condition.php",
										dataType: "json",
										data: {'parentId':parentId},
										success: function(result) 
										{
											if(result.success == true)
											{
												alert("Configuration Conditions of this Group deleted successfully.");
											}							
										}
									});	
								} 
								else 
								{
									location.reload(); 
								} 
							}
						}							
					}
				});
			}
		});
	}
});
