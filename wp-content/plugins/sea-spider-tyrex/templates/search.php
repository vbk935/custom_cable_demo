<?php get_header(); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 clearfix main-column" data-mh="equal">
			<section id="content" role="main">
				<?php  $image = get_field('search_featured_image', 'option');
					if( !empty($image) ): ?>
					<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" />
				<?php endif; ?>
				
				<?php if ( have_posts() && strlen( trim(get_search_query()) ) != 0 ) : ?>
				
					<header class="header">
						<h1 class="entry-title"><?php printf( __( 'Search Results for: %s', 'blankslate' ), get_search_query() ); ?></h1>
						<?php  the_field('search_text', 'option');?>
					</header>
					<section class="entry-content">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'entry' ); ?>
						<?php endwhile; ?>					
						<?php get_template_part( 'nav', 'below' ); ?>
					</section>
							
							
				<?php else : ?>
				
				<article id="post-0" class="post no-results not-found">
					<header class="header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'blankslate' ); ?></h1>
					</header>
					<section class="entry-content">
						<p>
							<?php 
							if(strlen( trim(get_search_query()) ) != 0 ) {							
								// Nothing found
								_e( 'Your search did not match any documents.  Please try again.', 'blankslate' ); 
							} else {
								// Nothing searched (empty search)
								_e( 'Your search did not match any documents.  Please enter a search term.', 'blankslate' ); 
							}
							?>
						</p>
					</section>
				</article>
			<?php endif; ?>
					
					
					
			</section>
		</div>
		<?php get_sidebar(); ?>
	</div>
	<? // row ; ?>
</div>
<? // container ; ?>
<?php get_footer(); ?>