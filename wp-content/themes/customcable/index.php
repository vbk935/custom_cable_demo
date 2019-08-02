<?php get_header(); ?>

<div class="col-sm-9 clearfix main-column" data-mh="equal">
	<section id="content" role="main">
		<?php //if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="header">
					<?php 
						if( get_field('slides_repeater') ) { 
							include (STYLESHEETPATH . '/snippets/slideshow-featured.php');
						} else { 
							if ( has_post_thumbnail() ) { the_post_thumbnail(); }
						}
						include (STYLESHEETPATH . '/snippets/menu-about-us.php'); 	
					?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php edit_post_link(); ?>
				</header>
				<section class="entry-content">
					<?php the_content(); ?>
					<?php include (STYLESHEETPATH . '/snippets/content-panels.php'); ?>
					<?php include (STYLESHEETPATH . '/snippets/news.php'); ?>
					<div class="entry-links"><?php wp_link_pages(); ?></div>
				</section>
			</article>
		<?php 
			//endwhile; 
			//endif; 
		?>
	</section>
</div>



<?php get_sidebar(); ?>
<?php get_footer(); ?>
