<?php get_header(); ?>

<?php
global $porto_settings;

$post_layout = $porto_settings['post-layout'];
if ( is_category() ) {
	global $wp_query;

	$term    = $wp_query->queried_object;
	$term_id = $term->term_id;

	$post_options = get_metadata( $term->taxonomy, $term->term_id, 'post_options', true ) == 'post_options' ? true : false;

	$post_layout = $post_options ? get_metadata( $term->taxonomy, $term->term_id, 'post_layout', true ) : $post_layout;

	if ( 'grid' == $post_layout || 'masonry' == $post_layout ) {
		global $porto_blog_columns;
		$grid_columns = get_metadata( $term->taxonomy, $term->term_id, 'post_grid_columns', true );
		if ( $grid_columns ) {
			$porto_blog_columns = $grid_columns;
		}
	}
}
?>

<div id="content" role="main">

	<?php if ( have_posts() ) : ?>

		<?php if ( category_description() ) : ?>
			<div class="page-content">
				<?php echo category_description(); ?>
			</div>
		<?php endif; ?>

		<?php
		if ( 'timeline' == $post_layout ) {
			global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

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

		<h2 class="entry-title"><?php esc_html_e( 'Nothing Found', 'porto' ); ?></h2>

		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<?php /* translators: $1: opening A tag which has url to the admin post new url $2: closing A tag */ ?>
			<p><?php printf( esc_html__( 'Ready to publish your first post? %1$sGet started here%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'post-new.php' ) ) . '>', '</a>' ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'porto' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'porto' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>

	<?php endif; ?>
</div>

<?php get_footer(); ?>
