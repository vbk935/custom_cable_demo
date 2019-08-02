<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * @version     2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop, $porto_woocommerce_loop, $porto_settings, $porto_layout, $porto_products_cols_lg, $porto_products_cols_md, $porto_products_cols_xs, $porto_products_cols_ls;

$extra_class = '';
if ( isset( $porto_woocommerce_loop['view'] ) && 'creative' == $porto_woocommerce_loop['view'] && isset( $porto_woocommerce_loop['grid_layout'] ) && isset( $porto_woocommerce_loop['grid_layout'][ $woocommerce_loop['cat_loop'] % count( $porto_woocommerce_loop['grid_layout'] ) ] ) ) {
	$grid_layout  = $porto_woocommerce_loop['grid_layout'][ $woocommerce_loop['cat_loop'] % count( $porto_woocommerce_loop['grid_layout'] ) ];
	$extra_class .= 'grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ' grid-height-' . $grid_layout['height'];

	$porto_woocommerce_loop['image_size'] = $grid_layout['size'];
}
$woocommerce_loop['cat_loop']++;

$class = 'product-category product-col ' . esc_attr( apply_filters( 'product_cat_class', $extra_class, '', $category ) );

if ( ! $porto_products_cols_lg ) {
	$cols = $porto_settings['product-cols'];
	if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
		if ( 8 == $cols || 7 == $cols ) {
			$cols = 6;
		}
	}

	switch ( $cols ) {
		case 1:
			$cols_md = 1;
			$cols_xs = 1;
			$cols_ls = 1;
			break;
		case 2:
			$cols_md = 2;
			$cols_xs = 2;
			$cols_ls = 1;
			break;
		case 3:
			$cols_md = 3;
			$cols_xs = 2;
			$cols_ls = 1;
			break;
		case 4:
			$cols_md = 4;
			$cols_xs = 2;
			$cols_ls = 1;
			break;
		case 5:
			$cols_md = 4;
			$cols_xs = 2;
			$cols_ls = 1;
			break;
		case 6:
			$cols_md = 5;
			$cols_xs = 3;
			$cols_ls = 2;
			break;
		case 7:
			$cols_md = 6;
			$cols_xs = 3;
			$cols_ls = 2;
			break;
		case 8:
			$cols_md = 6;
			$cols_xs = 3;
			$cols_ls = 2;
			break;
		default:
			$cols    = 4;
			$cols_md = 4;
			$cols_xs = 2;
			$cols_ls = 1;
	}
}

$view_type = isset( $porto_woocommerce_loop['category-view'] ) ? $porto_woocommerce_loop['category-view'] : ( '2' == $porto_settings['cat-view-type'] ? 'category-pos-outside' : '' );
?>

<li class="<?php echo esc_attr( trim( $class ) ); ?>">
	<?php
	/**
	 * woocommerce_before_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_open - 10 : removed
	 */
	do_action( 'woocommerce_before_subcategory', $category );

	?>

	<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">
		<span class="thumb-info <?php echo ! $view_type ? '' : ' align-center'; ?>">
			<span class="thumb-info-wrapper<?php echo ! $view_type ? '' : ' tf-none'; ?>">
				<?php
				/**
				 * woocommerce_before_subcategory_title hook.
				 *
				 * @hooked woocommerce_subcategory_thumbnail - 10
				 */
				do_action( 'woocommerce_before_subcategory_title', $category );
				?>
			</span>
			<?php if ( 'category-pos-outside' != $view_type ) : ?>
				<span class="thumb-info-wrap">
					<span class="thumb-info-title">
						<h3 class="sub-title thumb-info-inner"><?php echo esc_html( $category->name ); ?></h3>
						<?php
						if ( $category->count > 0 ) :
							$count_html = apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">' . ( (int) $category->count ) . '</mark>', $category );
							if ( $count_html ) :
								?>
							<span class="thumb-info-type">
								<?php /* translators: %s: Products count */ ?>
								<?php printf( esc_html__( '%s Products', 'porto' ), $count_html ); ?>
							</span>
								<?php
							endif;
						endif;
						?>
					</span>
				</span>
			<?php endif; ?>
		</span>
	</a>

	<?php if ( 'category-pos-outside' == $view_type ) : ?>
		<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>"><h4 class="m-t-md m-b-none"><?php echo esc_html( $category->name ); ?></h4></a>
		<?php
		if ( $category->count > 0 ) :
			$count_html = apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">' . ( (int) $category->count ) . '</mark>', $category );
			if ( $count_html ) :
				?>
				<?php /* translators: %s: Products count */ ?>
			<p class="m-b-sm"><?php printf( esc_html__( '%s Products', 'porto' ), $count_html ); ?></p>
				<?php
			endif;
		endif;
	endif;
	?>

	<?php
	/**
	 * woocommerce_shop_loop_subcategory_title hook.
	 *
	 * @hooked woocommerce_template_loop_category_title - 10 : removed
	 */
	do_action( 'woocommerce_shop_loop_subcategory_title', $category );

	/**
	 * woocommerce_after_subcategory_title hook.
	 */
	do_action( 'woocommerce_after_subcategory_title', $category );

	/**
	 * woocommerce_after_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_close - 10 : removed
	 */

	do_action( 'woocommerce_after_subcategory', $category );
	?>
</li>
