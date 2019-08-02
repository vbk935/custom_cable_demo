/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    
    //set selected tab for reports page default first tab will be selected
    $( '#esb_cie_settings .nav-tab-wrapper a:first' ).addClass( 'nav-tab-active' );
    $( '#esb_cie_settings .esb-cie-content div:first' ).show();

    //  When user clicks on tab, this code will be executed
    $( document ).on( 'click', '.nav-tab-wrapper a', function() {

        //  First remove class "active" from currently active tab
        $(".nav-tab-wrapper a").removeClass('nav-tab-active');
 
        //  Now add class "active" to the selected/clicked tab
        $(this).addClass("nav-tab-active");
 
        //  Hide all tab content
        $(".esb-cie-tab-content").hide();
 
        //  Here we get the href value of the selected tab
        var selected_tab = $(this).attr("href");
 
        //  Show the selected tab content
        $(selected_tab).show();
        
        //  At the end, we add return false so that the click on the link is not executed
        return false;
    });
    
});