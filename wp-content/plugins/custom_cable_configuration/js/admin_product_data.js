/*!
 * Admin order details page Use for display the products additional data
 *       
 */
( function(){
    var metaData = jQuery('.dataMeta');

    for (var i=0; i<metaData.length; i++) { 
    	var meta = jQuery(metaData[i]).data('meta'); 
    	 var jsonData = JSON.parse(meta);
    	 htmlData = '';
    	jQuery.each(jsonData, function( index, value ) {
    		var label = value.configName;
    		var value =  value.cguiComponentName;

    		htmlData += '<tr><th>'+ label +'</th><td>'+ value +'</td></tr>';

    	});
    	 jQuery(metaData[i]).next().html(htmlData);

    }
    
   


}());

