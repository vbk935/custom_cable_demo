<?php get_header(); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 clearfix" data-mh="equal">
			<section id="content" role="main">
				<header class="header">
						<?php if( get_field('category_featured_image', 'option') ): ?>
							<img src="<?php the_field('category_featured_image', 'option'); ?>" class='img-responsive' style="margin-bottom:30px;" />
						<?php endif; ?>					
						
						
						
						
						<h1 class="entry-title">
						<?php _e( 'Category: ', 'blankslate' ); ?>
						<?php single_cat_title(); ?>
					</h1>
					<?php if ( '' != category_description() ) echo apply_filters( 'archive_meta', '<div class="archive-meta" style="margin-bottom:30px; border-bottom: solid 1px #ccc; pading-bottom: 20px;">' . category_description() . '</div>' ); ?>
				</header>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> style="margin-bottom: 30px;">
					  <div class="media"> 
					  	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark" class="pull-left">
					    		<?php if ( has_post_thumbnail() ) { echo get_the_post_thumbnail( $post->ID, 'medium', array( 'class' => 'media-object col-md-2' ) ); } ?>
					    	</a>
					    <div class="media-body">
						 <?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="media-heading">'; } ?>
						 <?php the_title(); ?>
						 <?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
						 <?php the_excerpt_dynamic(300); ?>
						<a href="<?php the_permalink(); ?>" class="read-more">Read more</a>
						 <?php edit_post_link(); ?>						
					    </div>
					  </div>
					</article>

				<?php endwhile; endif; ?>
			</section>
			<?php wp_reset_query();  wp_reset_postdata(); ?>
		</div>
		<?php  get_sidebar(); ?>
	</div>
	<? // row ; ?>
</div>
<? // container ; ?>
<?php get_footer(); ?>
