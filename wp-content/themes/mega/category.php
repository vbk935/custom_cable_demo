<?php get_header(); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 clearfix" data-mh="equal">
		
		
		
	
		
		
		
		
		
		
			<section id="content" role="main">
				<header class="header">
					<h1 class="entry-title">
						<?php _e( 'Category: ', 'blankslate' ); ?>
						<?php single_cat_title(); ?>
					</h1>
					<?php if ( '' != category_description() ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . category_description() . '</div>' ); ?>
				</header>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'entry', 'archive' ); ?>
				<?php endwhile; endif; ?>
				<?php get_template_part( 'nav', 'below' ); ?>
			</section>
			<?php if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
			<?php wp_reset_query();  wp_reset_postdata(); ?>
			
			
			
			
		
	
			
			
		</div>
		<?php  get_sidebar(); ?>
	</div>
	<? // row ; ?>
</div>
<? // container ; ?>
<?php get_footer(); ?>
