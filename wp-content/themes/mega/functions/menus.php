<?
// Add Your Menu Locations
function register_my_menus() {
  register_nav_menus(
    array(  
    	'about_us' => __( 'About Us' ),
    	'footer' => __( 'Footer' ),
    )
  );
} 
add_action( 'init', 'register_my_menus' );

function default_expanded_footer() {
	echo "<ul class='nav'><li>Create the Expanded Footer</li></ul>";
}
?>