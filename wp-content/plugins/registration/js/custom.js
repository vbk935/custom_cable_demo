jQuery(document).ready(function() {
	
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = jQuery(".input_fields_wrap"); //Fields wrapper
    var add_button      = jQuery(".add_field_button"); //Add button ID
   
    var x = 1; //initlal text box count
    jQuery(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            jQuery(wrapper).append('<div class="add-field"><input type="text" class="um_field_5 um_input form-control" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
        }
    });
   
    jQuery(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); jQuery(this).parent('.add-field').remove(); x--;
    })
});