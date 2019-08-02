<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width" />
    <title>
	    <?php wp_title( ' | ', true, 'right' ); ?>
    </title>

    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
    <?php wp_head(); ?>
</head>

<body <?php body_class('tyr'); ?>>
	<div id="wrapper" class="hfeed">
		<header id="header" role="banner">


			<nav id="menu" class="navbar navbar-default navbar-static-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					<?php if ( is_front_page() ) { echo '<h1>'; } ?>
					  <a class="navbar-brand" href="/" >
						<?php  $image = get_field('logo_small', 'option');
						if( !empty($image) ): ?>
							<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>"  class="logo-small img-responsive"/>
						<?php endif; ?>
						<?php  $image = get_field('logo_large', 'option');
						if( !empty($image) ): ?>
							<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>"  class="logo-large"/>
						<?php endif; ?>			
						<span class="text-hide"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
					  </a>
					<?php if (is_front_page() ) { echo '</h1>'; } ?>
					</div>
						<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/frameworks/bootstrap-extras/bootstrap-wp-navwalker.php'); ?>			
						<?php
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
					<?php get_search_form('SS_search_form');  ?>	
				</div>
			</nav>




		</header>
        <div class="container main-container">
	   
	   
	