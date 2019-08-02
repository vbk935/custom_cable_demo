<?php
header('Content-type: text/css');
$url = $_SERVER['SERVER_NAME'];
if($url == "tyrexmfg.com" OR $url == "www.tyrexmfg.com") {
	$folder = 'tyrexmfg';
	$subfolder = 'tyr';
}
if($url == "www.austinrl.com" OR $url == "austinrl.com") {
	$folder = 'austinrl';
	$subfolder = 'arl';
}
if($url == "www.dlinnovations.com" OR $url == "dlinnovations.com") {
	$folder = 'dlinnovations';
	$subfolder = 'dli';
}
if($url == "www.irexmfg.com" OR $url == "irexmfg.com") {
	$folder = 'irexmfg';
	$subfolder = 'rex';
}
if($url == "www.megladonmfg.com" OR $url == "megladonmfg.com") {
	$folder = 'megladonmfg';
	$subfolder = 'meg';
}
if($url == "www.saberdata.com" OR $url == "saberdata.com") {
	$folder = 'saberdata';
	$subfolder = 'sbd';
}
if($url == "www.saberex.com" OR $url == "saberex.com") {
	$folder = 'saberex';
	$subfolder = 'sbr';
}
if($url == "www.recognizegood.org" OR $url == "recognizegood.org") {
	$folder = 'recognizegood';
	$subfolder = 'good';
}
//echo $url ;
@require '/home/'. $folder .'/public_html/'. $subfolder  .'/wp-load.php';  // I can access any WP or theme functions
  // here to get values that will be used in
  // dynamic css below
?>
<?php if( get_field('company_color', 'option') ) { ?>
	a {color: <?php the_field('company_color', 'option'); ?>;}
	h1 { color: <?php the_field('company_color', 'option'); ?>;}
	.red { background: <?php the_field('company_color', 'option'); ?>;}
<?php } ?>




<?php 
/*****************************************************************

				TOP NAV

*****************************************************************/
?>

<?php if( get_field('logo_top_position') OR get_field('logo_left_position')) { ?>
	.logo-large {
	  position: absolute;
	  top: <?php the_field('logo_top_position', 'option'); ?>px;
	  left: <?php the_field('logo_left_position', 'option'); ?>px;
	  max-width: 300%;
	  z-index: 20;
	}
<?php } ?>


<?php if( get_field('company_color', 'option') ) { ?>
	.navbar-default {
		background-color: <?php the_field('company_color', 'option'); ?>;
		border-color: <?php the_field('company_color', 'option'); ?>;
	}
	.navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:hover, .navbar-default .navbar-nav>.open>a:focus {
		background-color: <?php the_field('company_color', 'option'); ?>;
	}
	.navbar-nav>li>.dropdown-menu {
		background: <?php the_field('company_color', 'option'); ?>;
		border: none 0px <?php the_field('company_color', 'option'); ?>;
	}
	.nav-tabs>li.active>a {
		background: <?php the_field('company_color', 'option'); ?>; 
		color: #fff;
	}
	.sidebar a:active, .sidebar a:focus, .sidebar a:visited {color: <?php the_field('company_color', 'option'); ?>;}
	.sidebar .red-box {	background: <?php the_field('company_color', 'option'); ?>;}
	.footer-main {	background: <?php the_field('company_color', 'option'); ?>; }

	.home.page h2  { color: <?php the_field('company_color', 'option'); ?>;}

	.archive-title {background: <?php the_field('company_color', 'option'); ?>;}
	.nav-previous a:hover, .nav-next a:hover {background: <?php the_field('company_color', 'option'); ?>;}





	.navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus {
	color: #FFF;
	background-color: <?php the_field('company_color_current', 'option'); ?>;
	}
	
	.navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:hover, .navbar-default .navbar-nav>.open>a:focus {
	background-color: <?php the_field('company_color_current', 'option'); ?>;
	}
	.navbar-nav>li>.dropdown-menu {
		background: <?php the_field('company_color_current', 'option'); ?>;
		border: none 0px <?php the_field('company_color_current', 'option'); ?>;
	}
	
	


	
	
<?php } ?>




	.navbar-default .navbar-nav>.active>a, .navbar-default .navbar-nav>.active>a:hover, .navbar-default .navbar-nav>.active>a:focus {
	background-color: <?php the_field('company_color_hover', 'option'); ?>;
	color: #fff;
	}

	.dropdown-menu>li>a:hover, .dropdown-menu>li>a:focus {
	color: #fff;
	background-color: <?php the_field('company_color_hover', 'option'); ?>;
	}
	.dropdown-menu>.active>a, .dropdown-menu>.active>a:hover, .dropdown-menu>.active>a:focus {
	background: <?php the_field('company_color_hover', 'option'); ?>;
	}
	#navbar ul ul ul {
	  background: <?php the_field('company_color_current', 'option'); ?> !important;
	  border: none 0px <?php the_field('company_color_current', 'option'); ?> !important;
	}	
.secondary-menu li.current_page_item a:hover, .secondary-menu li a:hover {
  background: <?php the_field('company_color_hover', 'option'); ?>;
  color: #FFF;
}
	
	.navbar-default .navbar-nav>li>a, .navbar-default .navbar-brand {
	color: #fff;
}
.navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus {
	color: #FFF;
}
.navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:hover, .navbar-default .navbar-nav>.open>a:focus {
	color: #fff;
}
.dropdown-menu>li>a {
	color: #fff;
}

<?php 
/*****************************************************************

				TABS

*****************************************************************/
?>

.secondary-menu li.current_page_item a {
  background: <?php the_field('company_color_current', 'option'); ?>;
  color: #FFF;
}
.secondary-menu li.current_page_item a:hover, .secondary-menu li a:hover {
  background: <?php the_field('company_color_hover', 'option'); ?>;
  color: #FFF;
}

<?php 
/*****************************************************************

				CAROUSEL 

*****************************************************************/
?>

<?php 
$test = get_field('company_color');
if( $test != "#f7941e") { ?>
.carousel .item { background-color:<?php the_field('company_color', 'option'); ?>; }
<?php } ?>


<?php 
/*****************************************************************

				sidebar

*****************************************************************/
?>
	.sidebar .color-box {
		background: <?php the_field('company_color', 'option'); ?>;
	}

	.sidebar a:hover {color: <?php the_field('company_color_hover', 'option'); ?>;}