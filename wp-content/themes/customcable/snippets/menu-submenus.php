<?php if (is_page(array (362,368,367,366,365,364,363,348) ) ) {
	wp_nav_menu( array(
		'menu'              => 'about-us',
		'theme_location'    => 'about-us',
		'depth'             => 1,
		'container'         => 'div',
		'container_class'   => 'nav-pills-container',
		'menu_class'        => 'nav nav-pills sub-pills',
		'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		'walker'            => new wp_bootstrap_navwalker())
	);
} ?>
<?php if (is_page(array (352,358,357,356,355,354,353,359) ) ) {
	wp_nav_menu( array(
		'menu'              => 'group',
		'theme_location'    => 'group',
		'depth'             => 1,
		'container'         => 'div',
		'container_class'   => 'nav-pills-container',
		'menu_class'        => 'nav nav-pills sub-pills',
		'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		'walker'            => new wp_bootstrap_navwalker())
	);
} ?>
<?php if (is_page(array (372,381,380,379,645,369) ) ) {
	wp_nav_menu( array(
		'menu'              => 'Resources',
		'theme_location'    => 'resources',
		'depth'             => 1,
		'container'         => 'div',
		'container_class'   => 'nav-pills-container',
		'menu_class'        => 'nav nav-pills sub-pills',
		'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		'walker'            => new wp_bootstrap_navwalker())
	);
} ?>
