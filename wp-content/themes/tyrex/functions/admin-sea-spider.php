<? 
//  http://wpsnipp.com/index.php/functions-php/enable-gzip-output-compression/
if(extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler"))
   add_action('wp', create_function('', '@ob_end_clean();@ini_set("zlib.output_compression", 1);'));

//change or delete footer admin text
function change_footer_admin () {
  echo 'Big Marlin Group - Question?  <strong><a href="support@bigmarlingroup.com">Email Us</a></strong>';
}
add_filter('admin_footer_text', 'change_footer_admin');

function my_footer_shh() {
    remove_filter( 'update_footer', 'core_update_footer' );
}
add_action('admin_menu','my_footer_shh');

// Change color of Posts depending on status
add_action('admin_footer','posts_status_color');
function posts_status_color(){
?>
<style>
.status-draft {
	background: #ffffe0 !important;
}
.status-future {
	background: #E9F2D3 !important;
}
.status-publish {/* no background keep WordPress colors */
}
.status-pending {
	background: #D3E4ED !important;
}
.status-private {
	background: #FFECE6 !important;
}
.status-sticky {
	background: #F9F9F9 !important;
}
.post-password-required {
	background: #F7FCFE !important;
}
</style>
<?php
}

//Remove  WordPress Welcome Panel
remove_action('welcome_panel', 'wp_welcome_panel');


// replace WordPress Howdy
function replace_howdy( $wp_admin_bar ) {
$my_account=$wp_admin_bar->get_node('my-account');
$newtitle = str_replace( 'Howdy,', 'Hi,', $my_account->title );
$wp_admin_bar->add_node( array(
'id' => 'my-account',
'title' => $newtitle,
) );
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );


  
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');
 
function new_mail_from($old) 
{
 return 'do_not_reply@megladonmfg.com';
}
 
function new_mail_from_name($old) 
{
 return 'Megladon';
}


// Move Yoast to bottom
function yoasttobottom() { return 'low';}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');





/*add_action('all','template_snoop');
	function template_snoop(){    
		$args = func_get_args();    
		if(!is_admin()and $args[0]){
			if( $args[0]=='template_include'){
				echo "<!-- Base Template: {$args[1]} -->\n";        
			} elseif( strpos($args[0],'get_template_part_')===0){
				global $last_template_snoop;
				if( $last_template_snoop )
					echo "\n\n<!-- End Template Part: {$last_template_snoop} -->";
					$tpl = rtrim(join('-',  array_slice($args,1)),'-').'.php';
					echo "\n<!-- Template Part: {$tpl} -->\n\n";
					$last_template_snoop = $tpl;
				}
			}
	}
*/

?>
