<?php get_header(); ?>

<?php
global $porto_settings, $page_share;

$post_layout = $porto_settings['post-layout'];
?>

<div id="content" role="main">
	<?php if ( have_posts() ) : ?>

		<?php
		if ( 'timeline' == $post_layout ) {
			global $porto_settings, $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count, $post;
			$prev_post_year      = null;
			$prev_post_month     = null;
			$first_timeline_loop = false;
			$post_count          = 1;

			?>
		<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> <?php echo ! $porto_settings['post-style'] ? '' : 'blog-posts-' . esc_attr( $porto_settings['post-style'] ); ?>">
			<section class="timeline">
				<div class="timeline-body posts-container">
		<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) { ?>
		<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> <?php echo ! $porto_settings['post-style'] ? '' : 'blog-posts-' . esc_attr( $porto_settings['post-style'] ); ?>">
			<div class="row posts-container">
		<?php } else { ?>
		<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?> posts-container">
		<?php } ?>
			<?php
			$page_for_posts_id = get_option( 'page_for_posts' );
			$page_share        = get_post_meta( $page_for_posts_id, 'page_share', true );
			while ( have_posts() ) {
				the_post();
				get_template_part( 'content', 'blog-' . $post_layout );
			}

			?>
		<?php if ( 'timeline' == $post_layout ) { ?>
				</div>

			</section>
		<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) { ?>
			</div>
		<?php } else { ?>
		<?php } ?>
			<?php porto_pagination(); ?>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
