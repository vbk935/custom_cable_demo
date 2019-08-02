<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$image_count     = count( $featured_images );

if ( ! $image_count ) {
	return;
}
if ( ! isset( $carousel_options ) ) {
	$carousel_options = array();
}
$carousel_options['nav'] = true;
?>
	<div class="post-image<?php echo 1 == $image_count ? ' single' : ''; ?>">
		<div class="post-slideshow porto-carousel owl-carousel nav-inside nav-inside-center nav-style-2 show-nav-hover has-ccols ccols-1" data-plugin-options='<?php echo json_encode( $carousel_options ); ?>'>
			<?php
			foreach ( $featured_images as $featured_image ) {
				$attachment_large = porto_get_attachment( $featured_image['attachment_id'], ( isset( $image_size ) ? $image_size : 'blog-large' ) );
				$attachment       = porto_get_attachment( $featured_image['attachment_id'] );
				if ( ! $attachment ) {
					continue;
				}
				$placeholder = porto_generate_placeholder( $attachment_large['width'] . 'x' . $attachment_large['height'] );
				?>
				<?php if ( is_single() ) : ?>
				<div>
			<?php else : ?>
				<a href="<?php echo esc_url( apply_filters( 'the_permalink', get_permalink() ) ); ?>">
			<?php endif; ?>
					<div class="img-thumbnail">
						<img class="owl-lazy img-responsive" src="<?php echo esc_url( $placeholder[0] ); ?>" width="<?php echo esc_attr( $attachment_large['width'] ); ?>" height="<?php echo esc_attr( $attachment_large['height'] ); ?>" data-src="<?php echo esc_url( $attachment_large['src'] ); ?>" alt="<?php echo esc_attr( $attachment_large['alt'] ); ?>" />
						<?php if ( $porto_settings['post-zoom'] ) { ?>
							<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment_large['caption'] ); ?>"><i class="fas fa-search"></i></span>
						<?php } ?>
					</div>
				<?php if ( is_single() ) : ?>
				</div>
			<?php else : ?>
				</a>
			<?php endif; ?>
			<?php } ?>
		</div>
		<?php if ( is_single() && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<?php get_template_part( 'views/posts/single/share' ); ?>
		<?php endif; ?>

		<?php if ( isset( $extra_html ) ) : ?>
			<?php echo wp_kses_post( $extra_html ); ?>
		<?php endif; ?>
	</div>
