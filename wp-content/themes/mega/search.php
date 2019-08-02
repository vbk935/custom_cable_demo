<?php get_header(); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 clearfix" data-mh="equal">
			<section id="content" role="main">
				<?php if ( have_posts() ) : ?>
				<header class="header">
					<?php 
					$attachment_id = 82; // attachment ID
					$alt_text = get_post_meta('82', '_wp_attachment_image_alt', true);
					$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
					if( $image_attributes ) { ?>
					<img src="<?php echo $image_attributes[0]; ?>"  class="attachment-post-thumbnail wp-post-image" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
					<?php } ?>
					<h1 class="entry-title"><?php printf( __( 'Search Results for: %s', 'blankslate' ), get_search_query() ); ?></h1>
				</header>
				<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'entry' ); ?>
				<?php endwhile; ?>
				<?php get_template_part( 'nav', 'below' ); ?>
				<?php else : ?>
				<article id="post-0" class="post no-results not-found">
					<header class="header">
						<?php 
					$attachment_id = 82; // attachment ID
					$alt_text = get_post_meta('82', '_wp_attachment_image_alt', true);
					$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
					if( $image_attributes ) { ?>
						<img src="<?php echo $image_attributes[0]; ?>"  class="attachment-post-thumbnail wp-post-image" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
						<?php } ?>
						<h2 class="entry-title">
							<?php _e( 'Nothing Found', 'blankslate' ); ?>
						</h2>
					</header>
					<section class="entry-content">
						<p>
							<?php _e( 'Sorry, nothing matched your search. Please try again.', 'blankslate' ); ?>
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
