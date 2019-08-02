<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

wp_enqueue_script( 'isotope', PORTO_JS . '/libs/isotope.pkgd.min.js', array( 'jquery' ), '3.0.1', true );
?>

<div class="blocks-wrapper">
	<div class="category-list">
		<h2>Porto Studio</h2>
		<p>Pre-defined blocks for Porto Theme</p>
		<ul>
		<?php foreach ( $blocks as $category => $category_blocks ) : ?>
			<li><a href="#" data-filter-by="<?php echo esc_attr( $category ); ?>"><?php echo esc_html( $category ); ?><span><?php echo count( $category_blocks ); ?></span></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="blocks-list">
	<?php foreach ( $blocks as $category => $category_blocks ) : ?>
		<?php foreach ( $category_blocks as $block ) : ?>
			<div class="block <?php echo esc_attr( $category ); ?>" data-template_name="<?php echo esc_attr( vc_slugify( $category . ' ' . $block['title'] ) ); ?>">
				<a href="<?php echo esc_url( $block['url'] ); ?>" target="_blank"><img src="<?php echo esc_url( isset( $block['img'] ) ? $block['img'] : '//sw-themes.com/porto_dummy/wp-content/uploads/studio/' . $block['id'] . '.jpg' ); ?>" alt="<?php echo esc_attr( $block['title'] ); ?>"><h4 class="block-title"><?php echo esc_html( $block['title'] ); ?></h4></a>
				<div class="block-actions">
					<?php if ( Porto()->is_registered() ) : ?>
						<button class="btn btn-primary import" data-id="<?php echo esc_attr( $block['id'] ); ?>"><?php esc_html_e( 'Import', 'porto' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</div>
</div>
