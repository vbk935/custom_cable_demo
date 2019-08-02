
            
            
<nav id="menu" class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		<?php if ( is_front_page() ) { echo '<h1>'; } ?>
            <a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" >
			<?php /*?><img src="http://50.62.139.33/~tyrexmfg/tyr/wp-content/uploads/tyrex-e1410300740458.png" class="logo-large">
			<img src="http://50.62.139.33/~tyrexmfg/tyr/wp-content/uploads/trx-white.png" class="logo-small img-responsive hidden-xs"><?php */?>
			<span class="text-hide"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            </a>
		<?php if (is_front_page() ) { echo '</h1>'; } ?>
		</div>
	    	<div class="nav-right">
			<?php //get_search_form('SS_search_form');  ?>
			<?php
				include_once (STYLESHEETPATH . '/library/bootstrap-wp-navwalker.php');
				wp_nav_menu( array(
					'menu'              => 'main-menu',
					'theme_location'    => 'main-menu',
					'depth'             => 4,
					'container'         => 'div',
					'container_class'   => 'collapse navbar-collapse',
					'container_id'      => 'navbar',
					'menu_class'        => 'nav navbar-nav navbar-right',
					//'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
					'walker'            => new wp_bootstrap_navwalker())
				);
			?>
		</div>
	

	</div>
</nav>