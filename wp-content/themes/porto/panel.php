<?php
global $porto_settings;

$header_type = porto_get_header_type();
?>
<div class="panel-overlay"></div>
<div id="side-nav-panel" class="<?php echo ( isset( $porto_settings['mobile-panel-pos'] ) && $porto_settings['mobile-panel-pos'] ) ? $porto_settings['mobile-panel-pos'] : ''; ?>">
	<a href="#" class="side-nav-panel-close"><i class="fas fa-times"></i></a>
	<?php
	if ( '7' == $header_type || '8' == $header_type ) {
		// show currency and view switcher
		$switcher  = '';
		$switcher .= porto_mobile_currency_switcher();
		$switcher .= porto_mobile_view_switcher();

		if ( $switcher ) {
			echo '<div class="switcher-wrap">' . $switcher . '</div>';
		}
	}

	// show top navigation and mobile menu
	$menu = porto_mobile_menu( '19' == $header_type || empty( $header_type ) );

	if ( $menu ) {
		echo '<div class="menu-wrap">' . $menu . '</div>';
	}

	if ( ( ! porto_header_type_is_preset() || 1 == $header_type || 3 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type ) && $porto_settings['menu-block'] ) {
		echo '<div class="menu-custom-block">' . wp_kses_post( $porto_settings['menu-block'] ) . '</div>';
	}

	$menu = porto_mobile_top_navigation();

	if ( $menu ) {
		echo '<div class="menu-wrap">' . $menu . '</div>';
	}

	// show social links
	echo porto_header_socials();
	?>
</div>
