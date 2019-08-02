<?php if (is_page(array (9,13,15,17,124) ) ) { 			?>

<?	wp_nav_menu( array(
		'menu'              => 'about-us',
		'theme_location'    => 'about-us',
		'depth'             => 1,
		'container'         => 'div',
		'container_class'   => 'nav-tabs-container',
		'menu_class'        => 'nav nav-tabs',
		'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		'walker'            => new wp_bootstrap_navwalker())
	);?>

<? } ?>