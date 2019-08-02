<?php get_header(); ?>

<div class="col-sm-9 clearfix main-column" data-mh="equal">
	<section id="content" role="main">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry">
				<header>
					<?php
					$thumb_id = get_post_thumbnail_id(get_the_ID());
					$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
					if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'alt' => $alt, 'title' => get_the_title(), 'class' => "img-responsive" ) ); } 
 					?>
					<?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="entry-title">'; } ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
					<?php the_title(); ?>
					</a>
					<?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
					<?php the_content(); ?>
					<?php edit_post_link(); ?>
					<?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
				</header>
				<?php //get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
			</div>
		</article>
		<?php if ( ! post_password_required() ) comments_template( '', true ); ?>
		<?php endwhile; endif; ?>
		<footer class="footer">
			<?php get_template_part( 'entry', 'footer' ); ?>
			<?php get_template_part( 'nav', 'below-single' ); ?>
		</footer>
	</section>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
