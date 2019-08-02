<?php
/**
 * Template Name: Product table
 *
 */
get_header(); ?>
<div class="col-sm-9 clearfix main-column" data-mh="equal">
	<section id="content" role="main">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
		</article>
		<?php 
			endwhile; 
			endif; 
		?>
	</section>
</div>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
