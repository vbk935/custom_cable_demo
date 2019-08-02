<?php get_header(); ?>

<div class="col-sm-9 clearfix main-column" data-mh="equal">
	<section id="content" role="main">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry">
				<header>
					<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/article-header.php'); ?>

					<?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="entry-title">'; } ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
					<?php the_title(); ?>
					</a>
					<?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
					<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/article-subheader.php'); ?>
							
					<?php 
					if($post->post_content=="") {
							the_excerpt();
						} else {
							the_content(); 
						}
					?>
					<?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
				</header>
					<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content.php'); ?>
				<?php //get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
			</div>
		</article>
		<?php if ( ! post_password_required() ) comments_template( '', true ); ?>
		<?php endwhile; endif; ?>
		<footer class="footer">

			
			
			<?php if( have_rows('downloads_repeater') ) : ?>
				<ul class="download-repeater">
				<?php while ( have_rows('downloads_repeater') ) : the_row(); ?>
					<?php $file = get_sub_field('downloads'); ?>
						<li><a href="<?php echo $file['url']; ?>"><?php echo $file['title']; ?></a></li>
				<?php endwhile; ?>
			</ul>
			<?php endif; ?>			
			
			
			<?php get_template_part( 'entry', 'footer' ); ?>
			<?php get_template_part( 'nav', 'below-single' ); ?>		
			
		</footer>
	</section>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
