<?php get_header(); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 clearfix" data-mh="equal">
			<section id="content" role="main">
				<header class="header">
					<h1 class="entry-title">
					
						<?php _e( 'Tag Archives: ', 'blankslate' ); ?>
						<?php single_tag_title(); ?>
					</h1>
				</header>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'entry', 'archive' ); ?>
				<?php endwhile; endif; ?>
				<?php get_template_part( 'nav', 'below' ); ?>
			</section>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>
