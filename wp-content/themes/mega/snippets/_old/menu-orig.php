              <a class="navbar-brand" href="http://208.109.106.56/~tyrexmfg/tyr/">
				<?php 
                $attachment_id = 32; // attachment ID
                $alt_text = get_post_meta('32', '_wp_attachment_image_alt', true); 
                $image_attributes = wp_get_attachment_image_src( $attachment_id, large ); // returns an array
                if( $image_attributes ) { ?> 
                    <img src="<?php echo $image_attributes[0]; ?>"  class="main-logo hidden-md hidden-lg" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<<?php echo $alt_text; ?>">
                <?php } ?> 
            </a>
            
            
<nav id="menu" class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
            <a class="navbar-brand" href="http://208.109.106.56/~tyrexmfg/tyr/">
				<?php 
                $attachment_id = 97; // attachment ID
                $alt_text = get_post_meta('97', '_wp_attachment_image_alt', true);
                $image_attributes = wp_get_attachment_image_src( $attachment_id, large ); // returns an array
                if( $image_attributes ) { ?> 
                    <img src="<?php echo $image_attributes[0]; ?>"  class="main-logo" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
                <?php } ?> 
            </a>
            
					              
            
		</div>
	    	<?php get_search_form('SS_search_form');  ?>

	
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
				'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
				'walker'            => new wp_bootstrap_navwalker())
			);
		?>
	

	</div>
</nav>