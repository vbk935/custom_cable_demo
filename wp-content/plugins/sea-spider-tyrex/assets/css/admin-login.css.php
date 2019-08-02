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
require '/home/'. $folder .'/public_html/'. $subfolder  .'/wp-load.php';  // I can access any WP or theme functions
  // here to get values that will be used in
  // dynamic css below
?>

<?php if( get_field('login_logo', 'option') ) { ?>
body {
  background-color: <?php the_field('login_background_color', 'option'); ?> !important;
}	
.login #login h1 a {
  background-image: url("<?php the_field('login_logo', 'option'); ?>");
  background-size: 100% 100% !important;
  background-position: center top !important;
  background-repeat: no-repeat !important;
  width: 320px;
  height: 216px;
  margin-bottom: 10px;
  padding-bottom: 0px;
  display: block;
}	
<?php } ?>