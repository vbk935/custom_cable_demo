//http://speckyboy.com/2012/03/07/scroll-to-internal-link-with-jquery/
jQuery(document).ready(function(){
	jQuery('a.navjump').add('.links a').add('.sectionTitle a').add('.foot-nav a').add('a#goToServices').click(function(){
		var el = jQuery(this).attr('href');
		var elWrapped = jQuery(el);
		scrollToDiv(elWrapped,100);
		return false;
	});
	function scrollToDiv(element,navheight){
		var offset = element.offset();
		var offsetTop = offset.top;
		var totalScroll = offsetTop-navheight;	
		jQuery('body,html').animate({
				scrollTop: totalScroll
		}, 500);
	}	
});