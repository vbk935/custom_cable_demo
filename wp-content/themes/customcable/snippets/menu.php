<nav id="menu" class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container clearfix">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		<?php if ( is_front_page() ) { echo '<h1>'; } ?>
            <a class="navbar-brand" href="<?php echo get_permalink( 170 ); ?>" >
			<?php echo wp_get_attachment_image( 484, 'medium', 0, array(  'class' => 'logo-small img-responsive  hidden-lg' ) ); ?>
			<?php echo wp_get_attachment_image( 490, 'full', 0, array(  'class' => 'logo-large hidden-xs hidden-sm' ) ); ?>
			
			<span class="text-hide"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            </a>
		<?php if (is_front_page() ) { echo '</h1>'; } ?>
		</div>
	    	<div class="nav-right">
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
			<?php get_search_form('SS_search_form');  ?>
	

	</div>
</nav>




<div class="subnav">
		<nav id="menu2"  class="navbar navbar-default" role="navigation">
		  <div class="container">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
			 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			   <span class="sr-only">Toggle navigation</span>
			   <span class="icon-bar"></span>
			   <span class="icon-bar"></span>
			   <span class="icon-bar"></span>
			 </button>
		    </div>
					<?php
						wp_nav_menu( array(
							'menu'              => 'menu2',
							'theme_location'    => 'menu2',
							'depth'             => 4,
							'container'         => 'div',
							'container_class'   => 'collapse navbar-collapse',
							'container_id'      => 'bs-example-navbar-collapse-1',
							'menu_class'        => 'nav navbar-nav',
							//'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
							'walker'            => new wp_bootstrap_navwalker())
						);
					?>
		 </div>
		</nav>
</div>

<div class="clearfix"></div>