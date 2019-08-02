<?php if ( is_singular() ) { ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="col-xs-12 entry">
			<header>
				<?php if ( has_post_thumbnail() ) { 
				the_post_thumbnail(); } ?>
				<?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="entry-title">'; } ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
				<?php the_title(); ?>
				</a>
				<?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
				<?php edit_post_link(); ?>
				<?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
			</header>
			<?php get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
		</div>
	</article>
<?php } else { ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="col-xs-12 entry list-posts">
			<header class="row">
				<div class="col-md-4">
	
					
				<?php 
					if ( has_post_thumbnail() ) { ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
							<?php the_post_thumbnail('medium'); ?>
						</a>
					<?php } else {
						echo wp_get_attachment_image( 423, 'medium', 0, array(  'class' => 'center-block img-responsive' ) );
					}
				?>					
					
				</div>
				<div class="col-md-8">
				<div class="pull-right"><?php edit_post_link(); ?></div>
					<h2  class="entry-title"> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
						<? the_title() ; ?>
						</a> </h2>
					<?php /*?><strong class="entry-date">
					<?php the_time( get_option( 'date_format' ) ); ?>
					</strong><?php */?>
					<?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
					<?php //get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>

					<?php 
						 the_excerpt();
					
					?>

				</div>
			</header>
			<?php if ( !is_search() && (!is_archive)) get_template_part( 'entry-footer' ); ?>
			<?php if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
		</div>
	</article>
					
<?php } ?>
