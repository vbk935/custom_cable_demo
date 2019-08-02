<?php get_header(); ?>
<div class="container-fluid">
	<div class="row">
    		<div class="col-sm-9 clearfix" data-mh="equal">
    
					<section id="content" role="main">
						<article id="post-0" class="post not-found">
							<header class="header">
							
									<?php 
									$attachment_id = 82; // attachment ID
									$alt_text = get_post_meta('82', '_wp_attachment_image_alt', true);
									$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
									if( $image_attributes ) { ?> 
										<img src="<?php echo $image_attributes[0]; ?>"  class="attachment-post-thumbnail wp-post-image" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
									<?php } ?> 							
							
							
								<h1 class="entry-title">
									<?php _e( 'Not Found', 'blankslate' ); ?>
								</h1>
							</header>
							<section class="entry-content">
								<p>
									<?php _e( 'Nothing found for the requested page. Try a search instead?', 'blankslate' ); ?>
								</p>
								<?php get_search_form(); ?>
							</section>
						</article>
					</section>
      </div>  
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>