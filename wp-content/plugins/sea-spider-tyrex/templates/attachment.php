<?php get_header(); ?>
<?php global $post; ?>

<div class="col-sm-9 clearfix main-column" data-mh="equal">
	<section id="content" role="main">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<header class="header">
			<h1 class="entry-title" style="margin-top: 100px;margin-bottom: 20px;">
				<?php //the_title(); ?>
				<?php /*?><span class="meta-sep">|</span> <?php */?>
				<a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php printf( __( 'Return to %s', 'blankslate' ), esc_html( get_the_title( $post->post_parent ), 1 ) ); ?>" rev="attachment" class="img-responsive"><?php /*?><span class="meta-nav">&larr; </span><?php */?><?php echo get_the_title( $post->post_parent ); ?></a></h1>
			<?php get_template_part( 'entry', 'meta' ); ?>
		</header>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<section class="entry-content">
				<div class="entry-attachment">
					<?php if ( wp_attachment_is_image( $post->ID ) ) : $att_image = wp_get_attachment_image_src( $post->ID, "large" ); ?>
					<p class="attachment"><a href="<?php echo wp_get_attachment_url( $post->ID ); ?>" title="<?php the_title(); ?>" rel="attachment"><img src="<?php echo $att_image[0]; ?>" width="<?php echo $att_image[1]; ?>" height="<?php echo $att_image[2]; ?>" class="attachment-medium" alt="<?php $post->post_excerpt; ?>" /></a></p>
					<?php else : ?>
					<a href="<?php echo wp_get_attachment_url( $post->ID ); ?>" title="<?php echo esc_html( get_the_title( $post->ID ), 1 ); ?>" rel="attachment"><?php echo basename( $post->guid ); ?></a>
					<?php endif; ?>
				</div>
				<div class="entry-caption">
					<?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?>
				</div>
				<?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
				
			<div class="col-xs-12" style="margin-bottom:20px">
				
				<nav id="nav-above" class="navigation" role="navigation">
					<div class="nav-previous">
						<?php previous_image_link( false, '&larr;' ); ?>
					</div>
					<div class="nav-next">
						<?php next_image_link( false, '&rarr;' ); ?>
					</div>
				</nav>	
			</div>			
				
				
			<?php edit_post_link(); ?>
			</section>
		</article>
		<?php comments_template(); ?>
		<?php endwhile; endif; ?>
	</section>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
