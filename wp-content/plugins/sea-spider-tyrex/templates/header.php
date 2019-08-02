<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=1024" />
    <title>
	    <?php
		if ( is_front_page() ) {
			echo get_bloginfo('name'); ?> | <?php  echo get_field('tagline', 'option');
		} else {
			wp_title( ' | ', true, 'right' );
		}
        ?>
    </title>

    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
    <?php wp_head(); ?>
</head>

<? $code = get_field('code', 'option'); ?>
<body <?php body_class($code); ?>>
	<div id="wrapper" class="hfeed">
		<header id="header" role="banner">

			<?php $main_menu = get_field('main_menu', 'option') ; ?>

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
					  <a class="navbar-brand" href="<?php echo get_home_url(); ?>" >
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
						<?php
							$main_type = get_field('menu_type', 'option');
							if($main_type == "Click") {
								include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/frameworks/bootstrap-extras/bootstrap-wp-navwalker.php');
							} else {
								include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/frameworks/bootstrap-extras/bootstrap-wp-navwalker3.php');
							}
						?>
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
					<?php //get_search_form('SS_search_form');  ?>
				</div>
			</nav>


			<?php $show_sub_nav = get_field('show_sub_nav', 'option') ; ?>
			<?php if( $show_sub_nav == 1){ ?>
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

			<?php } ?>

		</header>
        <div class="container main-container">


				<?php if ( current_user_can( 'manage_options' ) )  {
					//ECHO $main_type;
				}
				?>
