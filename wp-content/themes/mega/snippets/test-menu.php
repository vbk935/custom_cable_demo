  <style>
/* Flexnav Base Styles */
.flexnav {
  -webkit-transition: none;
  -moz-transition: none;
  -ms-transition: none;
  transition: none;
  -webkit-transform-style: preserve-3d;
  overflow: hidden;
  margin: 0 auto;
  width: 100%;
  max-height: 0; }
  .flexnav.opacity {
    opacity: 0; }
  .flexnav.flexnav-show {
    max-height: 2000px;
    opacity: 1;
    -webkit-transition: all .5s ease-in-out;
    -moz-transition: all .5s ease-in-out;
    -ms-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out; }
  .flexnav.one-page {
    position: fixed;
    top: 50px;
    right: 5%;
    max-width: 200px; }
  .flexnav li {
    font-size: 100%;
    position: relative;
    overflow: hidden; }
  .flexnav li a {
    position: relative;
    display: block;
    padding: .96em;
    z-index: 2;
    overflow: hidden;
    color: #fff;
    /*background: #a6a6a2;*/
    border-bottom: 1px solid rgba(0, 0, 0, 0.15); }
  .flexnav li ul {
    width: 100%; }
    .flexnav li ul li {
      font-size: 100%;
      position: relative;
      overflow: hidden; }
  .flexnav li ul.flexnav-show li {
    overflow: visible; }
  .flexnav li ul li a {
    display: block;
    background: #b2b2af; }
  .flexnav ul li ul li a {
    background: #bfbfbc; }
  .flexnav ul li ul li ul li a {
    background: #cbcbc9; }
  .flexnav .touch-button {
    position: absolute;
    z-index: 999;
    top: 0;
    right: 0;
    width: 50px;
    height: 50px;
    display: inline-block;
    background: #acaca1;
    background: rgba(0, 0, 0, 0.075);
    text-align: center; }
    .flexnav .touch-button:hover {
      cursor: pointer; }
    .flexnav .touch-button .navicon {
      position: relative;
      top: 1.4em;
      font-size: 12px;
      color: #666; }

.menu-button {
  position: relative;
  display: block;
  padding: 1em;
  background: #a6a6a2;
  color: #222222;
  cursor: pointer;
  border-bottom: 1px solid rgba(0, 0, 0, 0.2); }
  .menu-button.one-page {
    position: fixed;
    top: 0;
    right: 5%;
    padding-right: 45px; }
  .menu-button .touch-button {
    background: transparent;
    position: absolute;
    z-index: 999;
    top: 0;
    right: 0;
    width: 50px;
    height: 50px;
    display: inline-block;
    text-align: center; }
    .menu-button .touch-button .navicon {
      font-size: 16px;
      position: relative;
      top: 1em;
      color: #666; }

@media all and (min-width: 800px) {
  body.one-page {
    padding-top: 70px; }

  .flexnav {
    overflow: visible; }
    .flexnav.opacity {
      opacity: 1; }
    .flexnav.one-page {
      top: 0;
      right: auto;
      max-width: 1080px; }
    .flexnav li {
      position: relative;
      list-style: none;
      float: left;
      display: block;
      /*background-color: #a6a6a2;*/
      overflow: visible;
     /* width: 20%; */
	  }
    .flexnav li a {
      border-left: 1px solid #acaca1;
      border-bottom: none; }
    .flexnav li > ul {
      position: absolute;
      top: auto;
      left: 0; }
      .flexnav li > ul li {
        width: 100%; }
    .flexnav li ul li > ul {
      margin-left: 100%;
      top: 0; }
    .flexnav li ul li a {
      border-bottom: none; }
    .flexnav li ul.open {
      display: block;
      opacity: 1;
      visibility: visible;
      z-index: 1; }
      .flexnav li ul.open li {
        overflow: visible;
        max-height: 100px; }
      .flexnav li ul.open ul.open {
        margin-left: 100%;
        top: 0; }

  .menu-button {
    display: none; } }
.oldie body.one-page {
  padding-top: 70px; }
.oldie .flexnav {
  overflow: visible; }
  .oldie .flexnav.one-page {
    top: 0;
    right: auto;
    max-width: 1080px; }
  .oldie .flexnav li {
    position: relative;
    list-style: none;
    float: left;
    display: block;
    background-color: #a6a6a2;
    width: 20%;
    min-height: 50px;
    overflow: visible; }
  .oldie .flexnav li:hover > ul {
    display: block;
    width: 100%;
    overflow: visible; }
    .oldie .flexnav li:hover > ul li {
      width: 100%;
      float: none; }
  .oldie .flexnav li a {
    border-left: 1px solid #acaca1;
    border-bottom: none;
    overflow: visible; }
  .oldie .flexnav li > ul {
    background: #acaca1;
    position: absolute;
    top: auto;
    left: 0;
    display: none;
    z-index: 1;
    overflow: visible; }
  .oldie .flexnav li ul li ul {
    top: 0; }
  .oldie .flexnav li ul li a {
    border-bottom: none; }
  .oldie .flexnav li ul.open {
    display: block;
    width: 100%;
    overflow: visible; }
    .oldie .flexnav li ul.open li {
      width: 100%; }
    .oldie .flexnav li ul.open ul.open {
      margin-left: 100%;
      top: 0;
      display: block;
      width: 100%;
      overflow: visible; }
  .oldie .flexnav ul li:hover ul {
    margin-left: 100%;
    top: 0; }
.oldie .menu-button {
  display: none; }
.oldie.ie7 .flexnav li {
  width: 19.9%; }


</style>  
  
  
  
  
  
  
  <div class="navbar navbar-default navbar-static-top">
    <div class="container-fluid"> <a class="navbar-brand" href="http://208.109.106.56/~tyrexmfg/tyr/">
      <?php 
                $attachment_id = 32; // attachment ID
                $alt_text = get_post_meta($post->ID, ‘_wp_attachment_image_alt’, true); 
                $image_attributes = wp_get_attachment_image_src( $attachment_id, large ); // returns an array
                if( $image_attributes ) { ?>
      <img src="<?php echo $image_attributes[0]; ?>"  class="main-logo hidden-md hidden-lg" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
      <?php } ?>
      </a>
      <?
wp_nav_menu( array( 
    'theme_location' => 'primary',
    'menu_class' => 'flexnav', //Adding the class for FlexNav
    'items_wrap' => '<ul data-breakpoint="800" id="%1$s" class="%2$s">%3$s</ul>', // Adding data-breakpoint for FlexNav
    ));

?>
      <?php get_search_form('SS_search_form');  ?>
    </div>
  </div>
  <div class="menu-button">Menu</div>
  <script>
(function($) {

  $.fn.flexNav = function(options) {
    var $nav, breakpoint, isDragging, nav_open, resizer, settings;
    settings = $.extend({
      'animationSpeed': 100
    }, options);
    $nav = $(this);
    nav_open = false;
    isDragging = false;
    $nav.find("li").each(function() {
      if ($(this).has("ul").length) {
        return $(this).addClass("item-with-ul").find("ul").hide();
      }
    });
    if ($nav.data('breakpoint')) {
      breakpoint = $nav.data('breakpoint');
    }
    resizer = function() {
      if ($(window).width() <= breakpoint) {
        $nav.removeClass("lg-screen").addClass("sm-screen");
        $('.one-page li a').on('click', function() {
          return $nav.removeClass('show');
        });
        return $('.item-with-ul').off();
      } else {
        $nav.removeClass("sm-screen").addClass("lg-screen");
        $nav.removeClass('show');
        return $('.item-with-ul').on('mouseenter', function() {
          return $(this).find('>ul').addClass('show').stop(true, true).slideDown(settings.animationSpeed);
        }).on('mouseleave', function() {
          return $(this).find('>ul').removeClass('show').stop(true, true).slideUp(settings.animationSpeed);
        });
      }
    };
    $('.item-with-ul, .menu-button').append('<span class="touch-button"><i class="navicon">&#9660;</i></span>');
    $('.menu-button, .menu-button .touch-button').on('touchstart mousedown', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log(isDragging);
      return $(this).on('touchmove mousemove', function(e) {
        var msg;
        msg = e.pageX;
        isDragging = true;
        return $(window).off("touchmove mousemove");
      });
    }).on('touchend mouseup', function(e) {
      var $parent;
      e.preventDefault();
      e.stopPropagation();
      isDragging = false;
      $parent = $(this).parent();
      if (isDragging === false) {
        console.log('clicked');
      }
      if (nav_open === false) {
        $nav.addClass('show');
        return nav_open = true;
      } else if (nav_open === true) {
        $nav.removeClass('show');
        return nav_open = false;
      }
    });
    $('.touch-button').on('touchstart mousedown', function(e) {
      e.stopPropagation();
      e.preventDefault();
      return $(this).on('touchmove mousemove', function(e) {
        isDragging = true;
        return $(window).off("touchmove mousemove");
      });
    }).on('touchend mouseup', function(e) {
      var $sub;
      e.preventDefault();
      e.stopPropagation();
      $sub = $(this).parent('.item-with-ul').find('>ul');
      if ($nav.hasClass('lg-screen') === true) {
        $(this).parent('.item-with-ul').siblings().find('ul.show').removeClass('show').hide();
      }
      if ($sub.hasClass('show') === true) {
        return $sub.removeClass('show').slideUp(settings.animationSpeed);
      } else if ($sub.hasClass('show') === false) {
        return $sub.addClass('show').slideDown(settings.animationSpeed);
      }
    });
    $('.item-with-ul *').focus(function() {
      $(this).parent('.item-with-ul').parent().find(".open").not(this).removeClass("open").hide();
      return $(this).parent('.item-with-ul').find('>ul').addClass("open").show();
    });
    resizer();
    return $(window).on('resize', resizer);
  };

})(jQuery);






jQuery(document).ready(function($){
   $(".flexnav").flexNav({
});
});
</script> 
</header>
<div id="container">
