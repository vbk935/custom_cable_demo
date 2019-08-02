// JavaScript Document
//http://www.webdesignerdepot.com/2014/05/how-to-create-an-animated-sticky-header-with-css3-and-jquery/
jQuery(window).scroll(function() {
if (jQuery(this).scrollTop() > 900){  
    jQuery('#header').addClass("sticky");
   // $('#article').addClass("sticky");
  }
  else{
    jQuery('#header').removeClass("sticky");
   // $('#article').removeClass("sticky");
  }
});


jQuery(window).scroll(function() {
if (jQuery(this).scrollTop() > 2400){  
    jQuery('#header').addClass("red");
   // $('#article').addClass("sticky");
  }
  else{
    jQuery('#header').removeClass("red");
   // $('#article').removeClass("sticky");
  }
});
