<?php
/**
 * Generates dynamic css styles for only special pages or post types
 * @package Porto
 * @author P-Themes
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $porto_settings, $porto_product_layout, $porto_layout;

$porto_settings_backup = $porto_settings;
$b                     = porto_check_theme_options();
$porto_settings        = $porto_settings_backup;

if ( is_rtl() ) {
	$left  = 'right';
	$right = 'left';
	$rtl   = true;
} else {
	$left  = 'left';
	$right = 'right';
	$rtl   = false;
}

if ( ! isset( $porto_layout ) ) {
	$porto_layout = porto_meta_layout();
	$porto_layout = $porto_layout[0];
}
$body_type                     = porto_get_wrapper_type();
$is_wide                       = ( 'wide' == $body_type || porto_is_wide_layout() );
$body_mobile_font_size_scale   = ( 0 == (float) $b['body-font']['font-size'] || 0 == (float) $b['body-mobile-font']['font-size'] ) ? 1 : ( (float) $b['body-mobile-font']['font-size'] / (float) $b['body-font']['font-size'] );
$body_mobile_line_height_scale = ( 0 == (float) $b['body-font']['line-height'] || 0 == (float) $b['body-mobile-font']['line-height'] ) ? 1 : ( (float) $b['body-mobile-font']['line-height'] / (float) $b['body-font']['line-height'] );

/* logo css */
if ( ! isset( $porto_settings['logo-type'] ) || 'text' != $porto_settings['logo-type'] ) :
	$logo_width        = ( isset( $porto_settings['logo-width'] ) && (int) $porto_settings['logo-width'] ) ? (int) $porto_settings['logo-width'] : 170;
	$logo_width_wide   = ( isset( $porto_settings['logo-width-wide'] ) && (int) $porto_settings['logo-width-wide'] ) ? (int) $porto_settings['logo-width-wide'] : 250;
	$logo_width_tablet = ( isset( $porto_settings['logo-width-tablet'] ) && (int) $porto_settings['logo-width-tablet'] ) ? (int) $porto_settings['logo-width-tablet'] : 110;
	$logo_width_mobile = ( isset( $porto_settings['logo-width-mobile'] ) && (int) $porto_settings['logo-width-mobile'] ) ? (int) $porto_settings['logo-width-mobile'] : 110;
	$logo_width_sticky = ( isset( $porto_settings['logo-width-sticky'] ) && (int) $porto_settings['logo-width-sticky'] ) ? (int) $porto_settings['logo-width-sticky'] : 80;
	?>
	#header .logo,
	.side-header-narrow-bar-logo { max-width: <?php echo esc_html( $logo_width ); ?>px; }
	@media (min-width: <?php echo (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width']; ?>px) {
		#header .logo { max-width: <?php echo esc_html( $logo_width_wide ); ?>px; }
	}
	@media (max-width: 991px) {
		#header .logo { max-width: <?php echo esc_html( $logo_width_tablet ); ?>px; }
	}
	@media (max-width: 767px) {
		#header .logo { max-width: <?php echo esc_html( $logo_width_mobile ); ?>px; }
	}
	<?php if ( $b['change-header-logo'] ) : ?>
		#header.sticky-header .logo { width: <?php echo esc_html( $logo_width_sticky * 1.25 ); ?>px; }
	<?php endif; ?>
	<?php
endif;

/* loading overlay */
$loading_overlay = porto_get_meta_value( 'loading_overlay' );
if ( 'no' !== $loading_overlay && ( 'yes' === $loading_overlay || ( 'yes' !== $loading_overlay && $b['show-loading-overlay'] ) ) ) :
	?>
	/* Loading Overlay */
	/*.loading-overlay-showing { overflow-x: hidden; }*/
	.loading-overlay-showing > .loading-overlay { opacity: 1; visibility: visible; transition-delay: 0; }
	.loading-overlay { transition: visibility 0s ease-in-out 0.5s, opacity 0.5s ease-in-out; position: absolute; bottom: 0; left: 0; opacity: 0; right: 0; top: 0; visibility: hidden; }
	.loading-overlay .loader { display: inline-block; border: 2px solid transparent; width: 40px; height: 40px; -webkit-animation: spin 0.75s infinite linear; animation: spin 0.75s infinite linear; border-image: none; border-radius: 50%; vertical-align: middle; position: absolute; margin: auto; left: 0; right: 0; top: 0; bottom: 0; z-index: 2; border-top-color: <?php echo esc_html( $b['skin-color'] ); ?>; }
	.loading-overlay .loader:before { content: ""; display: inline-block; border: inherit; width: inherit; height: inherit; -webkit-animation: spin 1.5s infinite ease; animation: spin 1.5s infinite ease; border-radius: inherit; position: absolute; left: -2px; top: -2px; border-top-color: inherit; }
	body > .loading-overlay { position: fixed; z-index: 999999; }
	<?php
endif;

/* header */
?>
<?php if ( $b['header-top-border']['border-top'] && '0px' != $b['header-top-border']['border-top'] ) : ?>
	#header,
	.sticky-header .header-main.sticky { border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid <?php echo esc_html( $b['header-top-border']['border-color'] ); ?> }
<?php endif; ?>
@media (min-width: 992px) {
	<?php
	if ( $b['header-margin']['margin-top'] || $b['header-margin']['margin-bottom'] || $b['header-margin']['margin-left'] || $b['header-margin']['margin-right'] ) :
		if ( $rtl ) {
			$temp                               = $b['header-margin']['margin-left'];
			$b['header-margin']['margin-left']  = $b['header-margin']['margin-right'];
			$b['header-margin']['margin-right'] = $temp;
		}
		?>
		#header { margin: <?php echo porto_config_value( $b['header-margin']['margin-top'] ); ?>px <?php echo porto_config_value( $b['header-margin']['margin-right'] ); ?>px <?php echo porto_config_value( $b['header-margin']['margin-bottom'] ); ?>px <?php echo porto_config_value( $b['header-margin']['margin-left'] ); ?>px; }
	<?php endif; ?>
	<?php if ( $b['header-margin']['margin-top'] && $b['logo-overlay'] && $b['logo-overlay']['url'] ) : ?>
		#header.logo-overlay-header .overlay-logo { top: -<?php echo esc_html( $b['header-margin']['margin-top'] ); ?>px }
		#header.logo-overlay-header.sticky-header .overlay-logo { top: -<?php echo (int) $b['header-margin']['margin-top'] + 90; ?>px }
	<?php endif; ?>
}

<?php if ( isset( $porto_settings['logo-type'] ) && 'text' == $porto_settings['logo-type'] ) : ?>
	@media (max-width: 575px) {
		#header .logo-text { font-size: <?php echo (float) $b['logo-font']['font-size'] * $body_mobile_font_size_scale; ?>px; line-height: <?php echo (float) $b['logo-font']['line-height'] * $body_mobile_line_height_scale; ?>px; }
	}
<?php endif; ?>

<?php if ( isset( $porto_settings['header-main-padding-mobile'] ) && ( $porto_settings['header-main-padding-mobile']['padding-top'] || $porto_settings['header-main-padding-mobile']['padding-bottom'] ) ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left,
		#header .header-main .header-center,
		#header .header-main .header-right,
		.fixed-header #header .header-main .header-left,
		.fixed-header #header .header-main .header-right,
		.fixed-header #header .header-main .header-center { padding-top: <?php echo esc_html( $porto_settings['header-main-padding-mobile']['padding-top'] ); ?>px; padding-bottom: <?php echo esc_html( $porto_settings['header-main-padding-mobile']['padding-bottom'] ); ?>px }
	}
<?php endif; ?>

/* breadcrumb type */
<?php
	$page_header_type = porto_get_meta_value( 'porto_page_header_shortcode_type' );
	$page_header_type = $page_header_type ? $page_header_type : porto_get_meta_value( 'breadcrumbs_type' );
	$page_header_type = $page_header_type ? $page_header_type : ( $porto_settings['breadcrumbs-type'] ? $porto_settings['breadcrumbs-type'] : '1' );
?>
<?php if ( 1 === (int) $page_header_type ) : ?>
	.page-top .page-title-wrap { line-height: 0; }
	.page-top .page-title:not(.b-none):after { content: ''; position: absolute; width: 100%; left: 0; border-bottom: <?php echo esc_html( $b['breadcrumbs-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['skin-color'] ); ?>; bottom: -<?php echo (int) porto_config_value( $b['breadcrumbs-padding']['padding-bottom'] ) + (int) porto_config_value( $b['breadcrumbs-bottom-border']['border-top'] ) + 12; ?>px; }
<?php elseif ( 3 === (int) $page_header_type || 4 === (int) $page_header_type || 5 === (int) $page_header_type || 7 === (int) $page_header_type ) : ?>
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.page-top .product-nav { position: static; height: auto; margin-top: 0; }
		.page-top .product-nav .product-prev,
		.page-top .product-nav .product-next { float: none; position: absolute; height: 30px; top: 50%; bottom: 50%; margin-top: -15px; }
		.page-top .product-nav .product-prev { <?php echo porto_filter_output( $right ); ?>: 10px; }
		.page-top .product-nav .product-next { <?php echo porto_filter_output( $left ); ?>: 10px; }
		.page-top .product-nav .product-next .product-popup { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 0; }
		.page-top .product-nav .product-next .product-popup:before { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 6px; }
	<?php endif; ?>
	.page-top .sort-source { position: static; text-align: center; margin-top: 5px; border-width: 0; }
	<?php if ( 3 === (int) $page_header_type || 7 === (int) $page_header_type ) : ?>
		.page-top ul.breadcrumb { -webkit-justify-content: center; -ms-justify-content: center; justify-content: center; -ms-flex-pack: center; }
		.page-top .page-title { font-weight: 700; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( 4 === (int) $page_header_type || 5 === (int) $page_header_type ) : ?>
	.page-top { padding-top: 20px; padding-bottom: 20px; }
	.page-top .page-title { padding-bottom: 0; }
	@media (max-width: 991px) {
		.page-top .page-sub-title { margin-bottom: 5px; margin-top: 0; }
		.page-top .breadcrumbs-wrap { margin-bottom: 5px; }
	}
	@media (min-width: 992px) {
		.page-top .page-title { min-height: 0; line-height: 1.25; }
		.page-top .page-sub-title { line-height: 1.6; }
		<?php if ( class_exists( 'Woocommerce' ) ) : ?>
			.page-top .product-nav { display: inline-block; height: 30px; vertical-align: middle; margin-<?php echo porto_filter_output( $left ); ?>: 10px; }
			.page-top .product-nav .product-prev,
			.page-top .product-nav .product-next { position: relative; }
			.page-top .product-nav .product-prev { float: <?php echo porto_filter_output( $left ); ?>; <?php echo porto_filter_output( $left ); ?>: 0; }
			.page-top .product-nav .product-prev .product-popup { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: -26px; }
			.page-top .product-nav .product-prev:before { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 32px; }
			.page-top .product-nav .product-next { float: <?php echo porto_filter_output( $left ); ?>; <?php echo porto_filter_output( $left ); ?>: 0; }
			.page-top .product-nav .product-next .product-popup { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 0; }
			.page-top .product-nav .product-next .product-popup:before { <?php echo porto_filter_output( $right ); ?>: auto; }
		<?php endif; ?>
	}
<?php endif; ?>
<?php if ( 4 === (int) $page_header_type ) : ?>
	@media (min-width: 992px) {
		<?php if ( class_exists( 'Woocommerce' ) ) : ?>
			.page-top .product-nav { height: auto; }
		<?php endif; ?>
		.page-top .breadcrumb { -webkit-justify-content: flex-end; -ms-justify-content: flex-end; justify-content: flex-end; -ms-flex-pack: end; }
	}
<?php elseif ( 6 === (int) $page_header_type ) : ?>
	.page-top ul.breadcrumb > li.home { display: inline-block; }
	.page-top ul.breadcrumb > li.home a { position: relative; width: 14px; text-indent: -9999px; }
	.page-top ul.breadcrumb > li.home a:after { content: "\e883"; font-family: 'porto'; position: absolute; <?php echo porto_filter_output( $left ); ?>: 0; top: 0; text-indent: 0; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) && ( 1 === (int) $page_header_type || 2 === (int) $page_header_type ) ) : ?>
	body.single-product .page-top .breadcrumbs-wrap { padding-<?php echo porto_filter_output( $right ); ?>: 55px; }
<?php endif; ?>

/* sidebar width */
<?php if ( $is_wide ) : ?>
	@media (min-width: 1500px) {
		.left-sidebar.col-lg-3,
		.right-sidebar.col-lg-3 { -webkit-flex: 0 0 20%; -ms-flex: 0 0 20%; flex: 0 0 20%; max-width: 20%; }
		.main-content.col-lg-9 { -webkit-flex: 0 0 80%; -ms-flex: 0 0 80%; flex: 0 0 80%; max-width: 80%; }
	}
	<?php
endif;

/* woocommerce single product */
if ( isset( $porto_product_layout ) && $porto_product_layout ) :
	?>
	.product-images .img-thumbnail .inner,
	.product-images .img-thumbnail .inner img { -webkit-transform: none; transform: none; }
	<?php if ( 'default' === $porto_product_layout || 'extended' === $porto_product_layout || 'grid' === $porto_product_layout || 'sticky_both_info' === $porto_product_layout || 'transparent' === $porto_product_layout || 'left_sidebar' === $porto_product_layout ) : ?>
		.single-product .product-summary-wrap .share-links a { background: #4c4c4c; }
	<?php endif; ?>
	<?php if ( 'default' === $porto_product_layout ) : ?>
		.product-layout-default .variations { display: block; }
		.product-layout-default .variations:after { content: ''; position: absolute; border-top: 1px solid #ebebeb; }
		.product-layout-default .variations tr:last-child td { padding-bottom: 20px; }
	<?php elseif ( 'extended' === $porto_product_layout ) : ?>
		@media (min-width: 992px) {
			.single-product .product_title { font-size: 38px; letter-spacing: -0.01em; }
		}
		.product-summary-images .product-image-slider .center .inner:before { content: ''; position: absolute; left: 0; top: 0; width: 100%; height: 100%; z-index: 1; background: rgba(0, 0, 0, 0.07); }
		.product-summary-images .product-image-slider .center .zoomContainer { z-index: 2; }
		.product-summary-images .product-images .img-thumbnail .inner { border: none; }
		.product-layout-extended .product_title { display: inline-block; width: auto; }
		.product-layout-extended .woocommerce-product-rating { margin-bottom: 20px; }
		.product-layout-extended .product-summary-wrap .price { font-size: 30px; line-height: 1; }
		.product-layout-extended .product-summary-wrap .product-share { margin-top: 0; }
		@media (min-width: 992px) {
			.product-layout-extended .product-summary-wrap .product-share { float: <?php echo porto_filter_output( $right ); ?>; }
			.share-links a { margin-top: 0; margin-bottom: 0; }
			p.price { display: inline-block; }
			.single-product-custom-block { float: <?php echo porto_filter_output( $right ); ?>; }
			.product-layout-extended form.cart { text-align: <?php echo porto_filter_output( $right ); ?> }
		}
		.product-layout-extended .product-summary-wrap .description { clear: both; padding-top: 25px; border-top: 1px solid #ebebeb; }
		.product-layout-extended .product-nav { position: relative; float: <?php echo porto_filter_output( $right ); ?>; margin: 4px 10px 0; <?php echo porto_filter_output( $right ); ?>: 0; }
		.product-layout-extended .single_variation_wrap { padding-top: 0; }
		@media (min-width: 576px) {
			.product-layout-extended .single_variation_wrap { vertical-align: middle; margin-bottom: 5px; }
		}
		.product-layout-extended .product_meta { margin-bottom: 20px; }
		.product-layout-extended .single-variation-msg { margin-bottom: 10px; line-height: 1.4; }
		.product-layout-extended form.cart { position: relative; }
		.product-layout-extended .entry-summary .quantity { position: relative; margin-<?php echo porto_filter_output( $right ); ?>: 20px; color: #8798a1; }
		.product-layout-extended .entry-summary .quantity .minus, 
		.product-layout-extended .entry-summary .quantity .qty,
		.product-layout-extended .entry-summary .quantity .plus { height: 24px; }
		.product-layout-extended .entry-summary .quantity .minus,
		.product-layout-extended .entry-summary .quantity .plus { border: none; position: relative; z-index: 2; left: 0; }
		.product-layout-extended .entry-summary .quantity .qty { width: 36px; border-width: 1px 1px 1px 1px; font-size: 13px; background: #f4f4f4; }
		.product-layout-extended .entry-summary .quantity:before { content: 'QTY:'; font-size: 15px; font-weight: 600; color: #21293c; line-height: 23px; }
		.product-layout-extended .product-summary-wrap .summary-before { margin-bottom: 2em; }
		.product-layout-extended .woocommerce-variation.single_variation { margin-top: 20px; }
		.single_variation_wrap .variations_button { padding-top: 0; }
		@media (min-width: 576px) {
			.product-layout-extended .variations { display: inline-block; vertical-align: middle; margin-bottom: 5px; }
			.product-layout-extended .variations tr { display: inline-block; margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
			.product-layout-extended .variations tr:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
			.product-layout-extended .variations td { padding-top: 0; padding-bottom: 0; }
			.product-layout-extended .variations .label { padding-top: 4px; padding-bottom: 4px; }
			.product-layout-extended .variations .reset_variations { display: none !important; }
			.product-layout-extended .variations .filter-item-list { margin-top: 0; }
			.product-layout-extended .product-summary-wrap .quantity,
			.product-layout-extended .product-summary-wrap .single_add_to_cart_button,
			.product-layout-extended .product-summary-wrap .yith-wcwl-add-to-wishlist { margin-bottom: 0; }
		}
		@media (max-width: 991px) {
			.product-summary-images { max-width: none; }
			.product-layout-extended .woocommerce-product-rating { margin-top: 15px; }
			.product-layout-extended .product-summary-wrap .price { margin-bottom: 40px; }
			.product-layout-extended .product-nav { display: none; }
		}
		@media (max-width: 575px) {
			.product-layout-extended .product-summary-wrap .single_add_to_cart_button { padding: 0 35px; }
			.product-layout-extended .entry-summary .quantity { display: -webkit-flex; display: -ms-flexbox; display: flex; margin-bottom: 20px; margin-top: 10px; -webkit-flex-basis: 100%; flex-basis: 100%; -ms-flex-preferred-size: 100%; }
			.product-layout-extended .entry-summary .quantity:before { margin-<?php echo porto_filter_output( $right ); ?>: 28px; }
		}
	<?php elseif ( 'grid' === $porto_product_layout ) : ?>
		.main-content { padding-bottom: 20px; }
		.porto-related-products { background: none; padding-top: 0; }
		.product-images:hover .zoom { opacity: 0; }
		.product-images .img-thumbnail:hover .zoom { opacity: 1; background: none; }
		.product-images .img-thumbnail .inner { border: none; }
		.product-summary-wrap .description { margin-bottom: 20px; }
		.product-summary-wrap .product_meta { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
		.single-product .product-summary-wrap .price { font-size: 25px; letter-spacing: 0; line-height: 1; }
		.single-product .variations { width: 100%; }
		.single-product .variations tr { border-bottom: 1px solid #dae2e6; }
		.variations tr td { padding-top: 15px; padding-bottom: 15px; }
		.single-product .variations .label { width: 75px; }
		.filter-item-list .filter-color { width: 28px; height: 28px; }
		.woocommerce-widget-layered-nav-list a:not(.filter-color), .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: #21293c; background-color: #f4f4f4; }
		.product-nav .product-link { width: 20px; font-size: 20px; }
		.product-nav .product-link:before { line-height: 32px; }
		.single-product .variations .filter-item-list { margin-top: 0; }
	<?php elseif ( 'full_width' === $porto_product_layout ) : ?>
		@media (max-width: 991px) {
			.summary-before { max-width: none; }
		}
		.product-images { margin-bottom: 0; }
		.product-layout-full_width { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; }
		.product-layout-full_width > div:not(.product-summary-wrap),
		.porto-related-products .container-fluid { padding-left: 50px; padding-right: 50px; }
		body.boxed .porto-related-products .container-fluid,
		.main-boxed .porto-related-products .container-fluid { padding-left: 20px; padding-right: 20px; }
		.product-summary-wrap { padding-<?php echo porto_filter_output( $right ); ?>: 50px; }
		@media (max-width: 1199px) {
			.product-layout-full_width > div:not(.product-summary-wrap),
			.porto-related-products .container-fluid { padding-left: 30px; padding-right: 30px; }
			.product-summary-wrap { padding-<?php echo porto_filter_output( $right ); ?>: 30px; }
		}
		.single-product .product-media { position: relative; }
		.single-product .product-thumbnails { position: absolute; top: 20px; <?php echo porto_filter_output( $left ); ?>: 20px; z-index: 2; bottom: 20px; overflow: hidden auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
		.single-product .product-thumbnails::-webkit-scrollbar { width: 5px; }
		.single-product .product-thumbnails::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo 'dark' != $b['css-type'] ? 'rgba(204, 204, 204, 0.5)' : '#39404c'; ?>; }
		.single-product .main-boxed .product-thumbnails,
		.single-product.boxed .product-thumbnails { top: 20px; <?php echo porto_filter_output( $left ); ?>: 20px; }
		.single-product .product-thumbnails .img-thumbnail { width: 100px; border: 1px solid rgba(0, 0, 0, 0.1); cursor: pointer; margin-bottom: 10px; background-color: rgba(244, 244, 244, 0.5); }
		.single-product .product-thumbnails .img-thumbnail:last-child { margin-bottom: 0; }
		.single-product.boxed .product-thumbnails .img-thumbnail { width: 80px; }
		.single-product .product-thumbnails .img-thumbnail img { opacity: 0.5; }
		@media (max-width: 1679px) {
			.single-product .product-thumbnails { top: 20px; <?php echo porto_filter_output( $left ); ?>: 20px; }
			.single-product .product-thumbnails .img-thumbnail { width: 80px; }
			.single-product.boxed .product-thumbnails .img-thumbnail { width: 70px; }
		}
		@media (max-width: 991px) {
			.product-summary-wrap { padding-<?php echo porto_filter_output( $left ); ?>: 30px; }
			.single-product .product-thumbnails { <?php echo porto_filter_output( $left ); ?>: 15px; top: 15px; }
		}
		.single-product .product-summary-wrap .product-share { display: block; position: absolute; top: 0; <?php echo porto_filter_output( $right ); ?>: -20px; margin-top: 0; }
		.single-product .product-summary-wrap .product-share label { margin: 0; font-size: 9px; letter-spacing: 0.05em; color: #c6c6c6; }
		.single-product .product-summary-wrap .share-links a { display: block; margin: 0 auto 2px; border-radius: 0; }
		.product-nav { <?php echo porto_filter_output( $right ); ?>: 30px; }
		.single-product-custom-block { margin-bottom: 20px; }
		.single-product .product_title { font-size: 40px; line-height: 1; }
		.single-product .main-boxed .product_title,
		.single-product.boxed .product_title { font-size: 28px; }

		@media (max-width: 575px) {
			.single-product .product-thumbnails .img-thumbnail { width: 60px; }
			.porto-related-products .container-fluid,
			.product-summary-wrap { padding-left: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
			.single-product .product-summary-wrap .product-share { <?php echo porto_filter_output( $right ); ?>: 0; }
		}
		@media (max-width: 1680px) {
			.single-product .product_title { font-size: 30px; }
		}
		.single-product .product-summary-wrap .price { font-size: 25px; line-height: 1; letter-spacing: 0; }
		@media (min-width: 576px) {
			.single-product .variations tr { display: inline-block; margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
			.single-product .variations tr:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
			.single-product .variations td { padding-top: 0; padding-bottom: 0; }
			.single-product .variations .label { padding-top: 4px; padding-bottom: 4px; }
			.single-product .variations .reset_variations { display: none !important; }
			.single-product .variations .filter-item-list { margin-top: 0; }
		}
		.single-product form.cart { margin-bottom: 40px; }
		@media (min-width: 576px) {
			.single-product .entry-summary .add_to_wishlist:before { border: none; color: <?php echo esc_html( $b['skin-color'] ); ?> !important; }
		}
		.single-product .entry-summary .quantity { margin-<?php echo porto_filter_output( $right ); ?>: 10px; }
		.single-product .entry-summary .quantity .plus { font-family: inherit; font-size: 20px; line-height: 25px; font-weight: 200; }
		#main.wide .vc_row { margin-left: -<?php echo (int) $porto_settings['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $porto_settings['grid-gutter-width'] / 2; ?>px; }
		<?php if ( $porto_settings['border-radius'] ) { ?>
			.single-product .product-thumbnails .img-thumbnail,
			.single-product .product-thumbnails .img-thumbnail img,
			.product-summary-wrap .single_add_to_cart_button { border-radius: 3px; }
			.single-product .entry-summary .quantity .minus { border-radius: <?php echo porto_filter_output( $rtl ? '0 2px 2px 0' : '2px 0 0 2px' ); ?>; }
			.single-product .entry-summary .quantity .plus { border-radius: <?php echo porto_filter_output( $rtl ? '2px 0 0 2px' : '0 2px 2px 0' ); ?>; }
			.single-product .product-summary-wrap .share-links a { border-radius: 2px; }
		<?php } ?>
		.filter-item-list .filter-color { width: 28px; height: 28px; }
		.woocommerce-widget-layered-nav-list a:not(.filter-color), .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: #21293c; background-color: #f4f4f4; }
		.product-summary-wrap .product_meta { border-bottom: none; margin-bottom: 0; }
	<?php elseif ( 'sticky_info' === $porto_product_layout ) : ?>
		div#main { overflow: hidden; }
		.porto-related-products { background: none; padding-top: 0; }
		.product-images .img-thumbnail { margin-bottom: 4px; }
		.product-images .img-thumbnail .inner { cursor: resize; }
		.product-images .img-thumbnail img { width: 100%; height: auto; }
		.product-images:hover .zoom { opacity: 0; }
		.product-images .img-thumbnail:hover .zoom { opacity: 1; background: none; }
		.single-product .variations .filter-item-list { margin-top: 0; }
		.filter-item-list .filter-color { width: 28px; height: 28px; }
		.woocommerce-widget-layered-nav-list a:not(.filter-color), .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: #21293c; background-color: #f4f4f4; }
		.product-nav:before { line-height: 32px; }
		.single-product .share-links a { border-radius: 20px; background: #4c4c4c; margin-<?php echo porto_filter_output( $right ); ?>: 0.2em; }
		.single-product .product-share > * { display: inline-block; }
		.product-share label { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
		.woocommerce-tabs { clear: both; background: #f4f4f4; padding-top: 70px; padding-bottom: 70px; position: relative; }
		body.boxed .woocommerce-tabs,
		.main-boxed .woocommerce-tabs { background: none; padding-top: 20px; padding-bottom: 0; }
		.woocommerce-tabs .tab-content { background: none; }
		.woocommerce-tabs:before, .woocommerce-tabs:after { content: ''; position: absolute; width: 30vw; height: 100%; top: 0; background: inherit; }
		.woocommerce-tabs:before { right: 100%; }
		.woocommerce-tabs:after { left: 100%; }
		.product-share { margin-bottom: 40px; }
		.single-product-custom-block { margin-bottom: 2em; }
		@media (min-width: 992px) {
			.product-share { float: <?php echo porto_filter_output( $right ); ?>; }
			.single-product-custom-block { float: <?php echo porto_filter_output( $left ); ?>; }
			.single-product-custom-block { width: 50%; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $porto_settings['grid-gutter-width'] / 2; ?>px; }
			.woocommerce-tabs .resp-tabs-list li { font-size: 18px; margin-<?php echo porto_filter_output( $right ); ?>: 50px; }
		}
		.woocommerce-tabs .resp-tabs-list { text-align: center; }
		.woocommerce-tabs .resp-tabs-list li { position: relative; bottom: -1px; border-bottom-color: transparent !important; }
		.single_variation_wrap { padding-top: 0; }
	<?php elseif ( 'sticky_both_info' === $porto_product_layout ) : ?>
		@media (min-width: 1200px) {
			.single-product .product_title { font-size: 32px; }
		}
		.single-product .product-summary-wrap .product-share { margin-top: 0; margin-bottom: 20px; }
		.product-nav { top: 30px; }
		@media (min-width: 768px) {
			.single-product .product_title { float: <?php echo porto_filter_output( $left ); ?>; width: auto; margin-<?php echo porto_filter_output( $right ); ?>: 20px; }
			.product-nav { position: relative; float: <?php echo porto_filter_output( $left ); ?>; <?php echo porto_filter_output( $right ); ?>: auto; top: 2px; }
			.single-product .product-summary-wrap .product-share { float: <?php echo porto_filter_output( $right ); ?>; margin-top: 0; margin-bottom: 0; }
		}
		.single-product .woocommerce-product-rating { clear: both; }
		.product-layout-sticky_both_info { padding-top: 10px; }
		.product-summary-wrap .summary-before { -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.product-summary-wrap .summary:last-child { -webkit-order: 3; order: 3; -ms-flex-order: 3; }
		.product-images .img-thumbnail { margin-bottom: 4px; }
		.product-images .img-thumbnail img { width: 100%; height: auto; }
		.entry-summary .quantity:before { content: 'QTY:'; font-size: 15px; font-weight: 600; color: #21293c; margin-<?php echo porto_filter_output( $right ); ?>: 10px; line-height: 24px; }
		.single-product .entry-summary .quantity { display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-align-items: center; -ms-align-items: center; align-items: center; -ms-flex-align: center; margin-bottom: 20px; -webkit-flex-basis: 100%; flex-basis: 100%; -ms-flex-preferred-size: 100%; }
		.single-product .variations { width: 100%; }
		.single-product .variations tr { border-bottom: 1px solid #dae2e6; }
		.single-product .variations .label { width: 75px; }
		.variations tr td { padding-top: 15px; padding-bottom: 15px; }
		.variations tr:first-child td { padding-top: 0; }
		.product_meta { text-transform: uppercase; }
		.product_meta span span,
		.product_meta span a,
		.product-summary-wrap .stock { color: #4c4c4c; font-size: 14px; font-weight: 700; }
		.product-summary-wrap .product_meta { margin-top: 30px; padding-bottom: 0; border-bottom: none; }
		.single-product .variations .filter-item-list { margin-top: 0; }
		.filter-item-list .filter-color { width: 28px; height: 28px; }
		.woocommerce-widget-layered-nav-list a:not(.filter-color), .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: #21293c; background-color: #f4f4f4; }
		@media (min-width: 992px) {
			.woocommerce-tabs .resp-tabs-list li { font-size: 18px; margin-<?php echo porto_filter_output( $right ); ?>: 50px; }
		}
		.porto-related-products { background: none; }
		#product-tab { margin-bottom: 2em; }
	<?php elseif ( 'transparent' === $porto_product_layout ) : ?>
		div#main { overflow: hidden; }
		.product-summary-wrap,
		.img-thumbnail,
		.product-summary-wrap:before,
		.product-summary-wrap:after,
		.product-summary-wrap .zoomContainer .zoomWindow { background-color: #f4f4f4; }
		.product-summary-wrap { position: relative; padding-top: 40px; margin-bottom: 40px; }
		.product-summary-wrap:before,
		.product-summary-wrap:after { content: ''; position: absolute; top: 0; width: 30vw; height: 100%; }
		.product-summary-wrap:before { right: 100%; }
		.product-summary-wrap:after { left: 100%; }
		.single-product .entry-summary .quantity .qty { background: none; }
		.product-summary-wrap .summary-before { margin-bottom: 29px; }
		.product-summary-wrap .summary { margin-bottom: 40px; }
		#main.boxed .product-summary-wrap { padding-top: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		#main.boxed .product-summary-wrap .summary-before { margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width'] - 11; ?>px; }
		#main.boxed .product-summary-wrap .summary  { margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		.product-summary-wrap .summary-before { margin-top: -5px; padding: 0 <?php echo (int) $b['grid-gutter-width'] / 2 - 5; ?>px; }
		.product-summary-wrap .summary-before:after { content: ''; display: table; clear: both; }
		.product-summary-wrap .summary-before .product-images { width: 80%; float: <?php echo porto_filter_output( $right ); ?>; padding: 5px; }
		.product-summary-wrap .summary-before .product-thumbnails { width: 20%; float: <?php echo porto_filter_output( $right ); ?>; }
		body.boxed .product-summary-wrap .summary-before .product-thumbnails { padding-left: 10px; }
		.woocommerce-tabs .resp-tabs-list { display: none; }
		.woocommerce-tabs h2.resp-accordion { display: block; }
		.woocommerce-tabs h2.resp-accordion:before { font-size: 20px; font-weight: 400; position: relative; top: -5px; }
		.woocommerce-tabs .tab-content { border-top: none; }
		.porto-related-products { background: none; padding-top: 0; }

		.product-thumbs-vertical-slider .slick-arrow { text-indent: -9999px; width: 40px; height: 30px; display: block; margin-left: auto; margin-right: auto; position: relative; text-shadow: none; background: none; font-size: 30px; color: #21293c; cursor: pointer; }
		.product-thumbs-vertical-slider .slick-arrow:before { content: '\e81b'; font-family: Porto; text-indent: 0; position: absolute; left: 0; width: 100%; line-height: 25px; top: 0; }
		.product-thumbs-vertical-slider .slick-next:before { content: '\e81c'; }
		.product-thumbs-vertical-slider .slick-next { margin-top: 10px; }
		.product-thumbs-vertical-slider .slick-prev { margin-bottom: 0; margin-top: -10px; }
		.product-thumbs-vertical-slider .img-thumbnail { padding: 5px; border: none; }
		.product-thumbs-vertical-slider .img-thumbnail img { width: 100%; height: auto; -webkit-transform: none; transform: none; border: 1px solid #ddd; }
		.product-thumbs-vertical-slider .img-thumbnail.selected img { border-color: <?php echo esc_html( $b['skin-color'] ); ?> }
		@media (max-width: 767px) {
			.product-thumbs-vertical-slider .slick-prev, .product-thumbs-vertical-slider .slick-next { display: block !important; }
		}
		.single-product .woocommerce-tabs .tab-content { background: none; }
		.product-layout-transparent .variations:after { content: ''; position: absolute; border-top: 1px solid #ebebeb; }
		.product-layout-transparent .variations tr:last-child td { padding-bottom: 20px; }
		#product-tab { margin-bottom: 2em; }
		.single-product .product-thumbnails .img-thumbnail { cursor: pointer; }
	<?php elseif ( 'centered_vertical_zoom' === $porto_product_layout ) : ?>
		@media (max-width: 991px) {
			.summary-before { max-width: none; }
		}
		.product-summary-wrap { margin-top: 20px; }
		.product-summary-wrap .summary-before { display: -webkit-flex; display: -ms-flexbox; display: flex; }
		.product-summary-wrap .summary-before .product-images { width: calc(100% - 110px); -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.summary-before .labels { <?php echo porto_filter_output( $left ); ?>: calc(100px + 0.8em); }
		.product-summary-wrap .summary-before .product-thumbnails { width: 110px; }
		.single-product .product-thumbnails .img-thumbnail { width: 100px; border: 1px solid rgba(0, 0, 0, 0.1); cursor: pointer; margin-bottom: 10px; }
		@media (max-width: 1679px) {
			.single-product .product-thumbnails .img-thumbnail { width: 80px; }
			.product-summary-wrap .summary-before .product-images { width: calc(100% - 90px); }
			.product-summary-wrap .summary-before .product-thumbnails { width: 90px; }
			.summary-before .labels { <?php echo porto_filter_output( $left ); ?>: calc(80px + 0.8em); }
		}
		@media (max-width: 575px) {
			.single-product .product-thumbnails .img-thumbnail { width: 60px; }
			.product-summary-wrap .summary-before .product-images { width: calc(100% - 60px); }
			.product-summary-wrap .summary-before .product-thumbnails { width: 80px; }
		}
		.product-summary-wrap .summary-before { -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.product-summary-wrap .summary:last-child { -webkit-order: 3; order: 3; -ms-flex-order: 3; }
		.product_title.show-product-nav { width: calc(100% - 42px); }
		.product-nav { <?php echo porto_filter_output( $right ); ?>: 0; }
		.product-summary-wrap .product_meta { text-transform: uppercase; padding-bottom: 0; border-bottom: none; }
		.product_meta span span,
		.product_meta span a,
		.product-summary-wrap .stock { color: #4c4c4c; font-size: 14px; font-weight: 700; }
		.single-product .variations .filter-item-list { margin-top: 0; }
		.filter-item-list .filter-color { width: 28px; height: 28px; }
		.woocommerce-widget-layered-nav-list a:not(.filter-color), .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: #21293c; background-color: #f4f4f4; }
		.single-product .variations { width: 100%; }
		.single-product .variations tr { border-bottom: 1px solid #dae2e6; }
		.variations tr td { padding-top: 15px; padding-bottom: 15px; }
		.variations tr:first-child td { padding-top: 0; }
		.single-product .variations .label { width: 75px; }
		.single-product .product-summary-wrap .product-share { display: block; position: fixed; top: 50%; <?php echo porto_filter_output( $right ); ?>: 15px; margin-top: -100px; z-index: 99; }
		.single-product .product-summary-wrap .product-share label { margin: 0; font-size: 9px; letter-spacing: 0.05em; color: #c6c6c6; }
		.single-product .product-summary-wrap .share-links a { display: block; margin: 0 auto 2px; border-radius: 0; }

		.entry-summary .quantity:before { content: 'QTY:'; font-size: 15px; font-weight: 600; color: #21293c; margin-<?php echo porto_filter_output( $right ); ?>: 10px; line-height: 24px; }
		.single-product .entry-summary .quantity { display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-align-items: center; -ms-align-items: center; align-items: center; -ms-flex-align: center; margin-bottom: 20px; -webkit-flex-basis: 100%; flex-basis: 100%; -ms-flex-preferred-size: 100%; }

		@media (min-width: 768px) {
			.woocommerce-tabs .resp-tabs-list li { margin-<?php echo porto_filter_output( $right ); ?>: 0; display: block; float: <?php echo porto_filter_output( $left ); ?>; clear: both; padding: 3px 0 10px !important; margin-bottom: 13px !important; position: relative; }
			.woocommerce-tabs .resp-tabs-list li:after { content: ''; position: absolute; width: 30vw; bottom: -2px; border-bottom: 1px solid #dae2e6; z-index: 0; <?php echo porto_filter_output( $left ); ?>: 0; }
			.woocommerce-tabs .resp-tabs-list li:hover:before,
			.woocommerce-tabs .resp-tabs-list .resp-tab-active:before { content: ''; position: absolute; width: 100%; bottom: -2px; border-bottom: 1px solid #dae2e6; z-index: 1; border-bottom-color: inherit; }
			.woocommerce-tabs { display: table !important; width: 100%; }
			.woocommerce-tabs .resp-tabs-list,
			.woocommerce-tabs .resp-tabs-container { display: table-cell; vertical-align: top; }
			.woocommerce-tabs .resp-tabs-list { width: 20%; overflow: hidden; }
			.woocommerce-tabs .tab-content { padding-top: 0; border-top: none; padding-<?php echo porto_filter_output( $left ); ?>: 30px; }
		}
		.porto-related-products { background: none; padding-top: 0; }
	<?php elseif ( 'left_sidebar' === $porto_product_layout ) : ?>
		@media (min-width: 1200px) {
			.product-summary-wrap .summary-before { -webkit-flex: 0 0 54%; -ms-flex: 0 0 54%; flex: 0 0 54%; max-width: 54%; }
			.product-summary-wrap .summary { -webkit-flex: 0 0 46%; -ms-flex: 0 0 46%; flex: 0 0 46%; max-width: 46%; }
		}
		.woocommerce-tabs .resp-tabs-list { display: none; }
		.woocommerce-tabs h2.resp-accordion { display: block; }
		.woocommerce-tabs h2.resp-accordion:before { font-size: 20px; font-weight: 400; position: relative; top: -4px; }
		.woocommerce-tabs .tab-content { border-top: none; }
		.porto-related-products { background: none; }
		.left-sidebar .widget_product_categories { border: 1px solid #dae2e6; padding: 15px 30px; }
		.left-sidebar .widget_product_categories .current > a { color: #21293c; text-transform: uppercase; }
		.left-sidebar .widget_product_categories li .toggle { font-size: 14px; }
		.left-sidebar .widget_product_categories li > .toggle:before { font-family: 'Porto'; content: '\e81c'; font-weight: 700; }
		.left-sidebar .widget_product_categories li.current >.toggle:before,
		.left-sidebar .widget_product_categories li.open >.toggle:before { content: '\e81b'; }
		.left-sidebar .widget_product_categories li.closed > .toggle:before { content: '\e81c'; }
		.sidebar .product-categories li>a { color: #7a7d82; font-weight: 600; }
		.product-images .zoom { opacity: 1; }
		.product-layout-left_sidebar .section.porto-related-products > .container { padding-left: 0; padding-right: 0; }
		<?php
	endif;

	if ( is_product() && isset( $porto_settings['product-sticky-addcart'] ) && $porto_settings['product-sticky-addcart'] ) :
		?>
		.sticky-product { position: fixed; top: 0; left: 0; width: 100%; z-index: 100; background-color: #fff; box-shadow: 0 3px 5px rgba(0,0,0,0.08); padding: 15px 0; }
		.sticky-product .container { display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; -ms-flex-wrap: wrap; flex-wrap: wrap; }
		.sticky-product .sticky-image { max-width: 60px; margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
		.sticky-product .add-to-cart { -ms-flex: 1; flex: 1; text-align: <?php echo porto_filter_output( $right ); ?> }
		.sticky-product .product-name { font-size: 16px; font-weight: 600; line-height: inherit; margin-bottom: 0; }
		.sticky-product .sticky-detail { line-height: 1.5; display: -ms-flexbox; display: flex;  }
		.sticky-product .star-rating { margin: 5px 15px; font-size: 1em; }
		.sticky-product .sticky-detail .price { font-family: 'Oswald', <?php echo sanitize_text_field( $b['h2-font']['font-family'] ); ?>, <?php echo sanitize_text_field( $b['h3-font']['font-family'] ); ?>; font-weight: 400; margin-bottom: 0; font-size: 1.3em; line-height: 1.5; }
		@media (max-width: 767px) {
			.sticky-product { display: none; }
		}
		<?php
	endif;
endif;

/* skin options */
$body_bg_color      = porto_get_meta_value( 'body_bg_color' );
$body_bg_image      = porto_get_meta_value( 'body_bg_image' );
$body_bg_repeat     = porto_get_meta_value( 'body_bg_repeat' );
$body_bg_size       = porto_get_meta_value( 'body_bg_size' );
$body_bg_attachment = porto_get_meta_value( 'body_bg_attachment' );
$body_bg_position   = porto_get_meta_value( 'body_bg_position' );

$page_bg_color      = porto_get_meta_value( 'page_bg_color' );
$page_bg_image      = porto_get_meta_value( 'page_bg_image' );
$page_bg_repeat     = porto_get_meta_value( 'page_bg_repeat' );
$page_bg_size       = porto_get_meta_value( 'page_bg_size' );
$page_bg_attachment = porto_get_meta_value( 'page_bg_attachment' );
$page_bg_position   = porto_get_meta_value( 'page_bg_position' );

$content_bottom_bg_color      = porto_get_meta_value( 'content_bottom_bg_color' );
$content_bottom_bg_image      = porto_get_meta_value( 'content_bottom_bg_image' );
$content_bottom_bg_repeat     = porto_get_meta_value( 'content_bottom_bg_repeat' );
$content_bottom_bg_size       = porto_get_meta_value( 'content_bottom_bg_size' );
$content_bottom_bg_attachment = porto_get_meta_value( 'content_bottom_bg_attachment' );
$content_bottom_bg_position   = porto_get_meta_value( 'content_bottom_bg_position' );

$header_bg_color      = porto_get_meta_value( 'header_bg_color' );
$header_bg_image      = porto_get_meta_value( 'header_bg_image' );
$header_bg_repeat     = porto_get_meta_value( 'header_bg_repeat' );
$header_bg_size       = porto_get_meta_value( 'header_bg_size' );
$header_bg_attachment = porto_get_meta_value( 'header_bg_attachment' );
$header_bg_position   = porto_get_meta_value( 'header_bg_position' );

$sticky_header_bg_color      = porto_get_meta_value( 'sticky_header_bg_color' );
$sticky_header_bg_image      = porto_get_meta_value( 'sticky_header_bg_image' );
$sticky_header_bg_repeat     = porto_get_meta_value( 'sticky_header_bg_repeat' );
$sticky_header_bg_size       = porto_get_meta_value( 'sticky_header_bg_size' );
$sticky_header_bg_attachment = porto_get_meta_value( 'sticky_header_bg_attachment' );
$sticky_header_bg_position   = porto_get_meta_value( 'sticky_header_bg_position' );

$footer_top_bg_color      = porto_get_meta_value( 'footer_top_bg_color' );
$footer_top_bg_image      = porto_get_meta_value( 'footer_top_bg_image' );
$footer_top_bg_repeat     = porto_get_meta_value( 'footer_top_bg_repeat' );
$footer_top_bg_size       = porto_get_meta_value( 'footer_top_bg_size' );
$footer_top_bg_attachment = porto_get_meta_value( 'footer_top_bg_attachment' );
$footer_top_bg_position   = porto_get_meta_value( 'footer_top_bg_position' );

$footer_bg_color      = porto_get_meta_value( 'footer_bg_color' );
$footer_bg_image      = porto_get_meta_value( 'footer_bg_image' );
$footer_bg_repeat     = porto_get_meta_value( 'footer_bg_repeat' );
$footer_bg_size       = porto_get_meta_value( 'footer_bg_size' );
$footer_bg_attachment = porto_get_meta_value( 'footer_bg_attachment' );
$footer_bg_position   = porto_get_meta_value( 'footer_bg_position' );

$footer_main_bg_color      = porto_get_meta_value( 'footer_main_bg_color' );
$footer_main_bg_image      = porto_get_meta_value( 'footer_main_bg_image' );
$footer_main_bg_repeat     = porto_get_meta_value( 'footer_main_bg_repeat' );
$footer_main_bg_size       = porto_get_meta_value( 'footer_main_bg_size' );
$footer_main_bg_attachment = porto_get_meta_value( 'footer_main_bg_attachment' );
$footer_main_bg_position   = porto_get_meta_value( 'footer_main_bg_position' );

$footer_bottom_bg_color      = porto_get_meta_value( 'footer_bottom_bg_color' );
$footer_bottom_bg_image      = porto_get_meta_value( 'footer_bottom_bg_image' );
$footer_bottom_bg_repeat     = porto_get_meta_value( 'footer_bottom_bg_repeat' );
$footer_bottom_bg_size       = porto_get_meta_value( 'footer_bottom_bg_size' );
$footer_bottom_bg_attachment = porto_get_meta_value( 'footer_bottom_bg_attachment' );
$footer_bottom_bg_position   = porto_get_meta_value( 'footer_bottom_bg_position' );

$breadcrumbs_bg_color      = porto_get_meta_value( 'breadcrumbs_bg_color' );
$breadcrumbs_bg_image      = porto_get_meta_value( 'breadcrumbs_bg_image' );
$breadcrumbs_bg_repeat     = porto_get_meta_value( 'breadcrumbs_bg_repeat' );
$breadcrumbs_bg_size       = porto_get_meta_value( 'breadcrumbs_bg_size' );
$breadcrumbs_bg_attachment = porto_get_meta_value( 'breadcrumbs_bg_attachment' );
$breadcrumbs_bg_position   = porto_get_meta_value( 'breadcrumbs_bg_position' );

if ( $body_bg_color || $body_bg_image || $body_bg_repeat || $body_bg_size || $body_bg_attachment || $body_bg_position
	|| $page_bg_color || $page_bg_image || $page_bg_repeat || $page_bg_size || $page_bg_attachment || $page_bg_position
	|| $content_bottom_bg_color || $content_bottom_bg_image || $content_bottom_bg_repeat || $content_bottom_bg_size || $content_bottom_bg_attachment || $content_bottom_bg_position
	|| $header_bg_color || $header_bg_image || $header_bg_repeat || $header_bg_size || $header_bg_attachment || $header_bg_position
	|| $sticky_header_bg_color || $sticky_header_bg_image || $sticky_header_bg_repeat || $sticky_header_bg_size || $sticky_header_bg_attachment || $sticky_header_bg_position
	|| $footer_top_bg_color || $footer_top_bg_image || $footer_top_bg_repeat || $footer_top_bg_size || $footer_top_bg_attachment || $footer_top_bg_position
	|| $footer_bg_color || $footer_bg_image || $footer_bg_repeat || $footer_bg_size || $footer_bg_attachment || $footer_bg_position
	|| $footer_main_bg_color || $footer_main_bg_image || $footer_main_bg_repeat || $footer_main_bg_size || $footer_main_bg_attachment || $footer_main_bg_position
	|| $footer_bottom_bg_color || $footer_bottom_bg_image || $footer_bottom_bg_repeat || $footer_bottom_bg_size || $footer_bottom_bg_attachment || $footer_bottom_bg_position
	|| $breadcrumbs_bg_color || $breadcrumbs_bg_image || $breadcrumbs_bg_repeat || $breadcrumbs_bg_size || $breadcrumbs_bg_attachment || $breadcrumbs_bg_position ) :
	?>
	<?php
	if ( $body_bg_color || $body_bg_image || $body_bg_repeat || $body_bg_size || $body_bg_attachment || $body_bg_position ) :
		?>
	body {
		<?php
		if ( $body_bg_color ) :
			?>
		background-color: <?php echo esc_html( $body_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $body_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $body_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $body_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $body_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $body_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $body_bg_size ) :
			?>
		background-size: <?php echo esc_html( $body_bg_size ); ?> !important;
			<?php
		endif;
		if ( $body_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $body_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $body_bg_position ) :
			?>
		background-position: <?php echo esc_html( $body_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $page_bg_color || $page_bg_image || $page_bg_repeat || $page_bg_size || $page_bg_attachment || $page_bg_position ) :
		?>
	#main {
		<?php
		if ( $page_bg_color ) :
			?>
		background-color: <?php echo esc_html( $page_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $page_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $page_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $page_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $page_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $page_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $page_bg_size ) :
			?>
		background-size: <?php echo esc_html( $page_bg_size ); ?> !important;
			<?php
		endif;
		if ( $page_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $page_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $page_bg_position ) :
			?>
		background-position: <?php echo esc_html( $page_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
		if ( 'transparent' == $page_bg_color ) :
			?>
		.page-content { margin-left: -<?php echo (int) $porto_settings['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $porto_settings['grid-gutter-width']; ?>px; } .main-content { padding-bottom: 0 !important; } .left-sidebar, .right-sidebar, .wide-left-sidebar, .wide-right-sidebar { padding-top: 0 !important; padding-bottom: 0 !important; margin: 0; }
			<?php
		endif;
	endif;
	if ( $content_bottom_bg_color || $content_bottom_bg_image || $content_bottom_bg_repeat || $content_bottom_bg_size || $content_bottom_bg_attachment || $content_bottom_bg_position ) :
		?>
	#main .content-bottom-wrapper {
		<?php
		if ( $content_bottom_bg_color ) :
			?>
		background-color: <?php echo esc_html( $content_bottom_bg_color ); ?> !important;
			<?php
			endif;
		if ( 'none' == $content_bottom_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $content_bottom_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $content_bottom_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $content_bottom_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $content_bottom_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $content_bottom_bg_size ) :
			?>
		background-size: <?php echo esc_html( $content_bottom_bg_size ); ?> !important;
			<?php
		endif;
		if ( $content_bottom_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $content_bottom_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $content_bottom_bg_position ) :
			?>
		background-position: <?php echo esc_html( $content_bottom_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $header_bg_color || $header_bg_image || $header_bg_repeat || $header_bg_size || $header_bg_attachment || $header_bg_position ) :
		?>
	#header, .fixed-header #header {
		<?php
		if ( $header_bg_color ) :
			?>
		background-color: <?php echo esc_html( $header_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $header_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $header_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $header_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $header_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $header_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $header_bg_size ) :
			?>
		background-size: <?php echo esc_html( $header_bg_size ); ?> !important;
			<?php
		endif;
		if ( $header_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $header_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $header_bg_position ) :
			?>
		background-position: <?php echo esc_html( $header_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;

	if ( $header_bg_color || $header_bg_image ) :
		?>
	.header-wrapper #header .header-main { background: none; }
		<?php
	endif;

	if ( $sticky_header_bg_color || $sticky_header_bg_image || $sticky_header_bg_repeat || $sticky_header_bg_size || $sticky_header_bg_attachment || $sticky_header_bg_position ) :
		?>
	#header.sticky-header, .fixed-header #header.sticky-header {
		<?php
		if ( $sticky_header_bg_color ) :
			?>
		background-color: <?php echo esc_html( $sticky_header_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $sticky_header_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $sticky_header_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $sticky_header_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $sticky_header_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $sticky_header_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $sticky_header_bg_size ) :
			?>
		background-size: <?php echo esc_html( $sticky_header_bg_size ); ?> !important;
			<?php
		endif;
		if ( $sticky_header_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $sticky_header_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $sticky_header_bg_position ) :
			?>
		background-position: <?php echo esc_html( $sticky_header_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_top_bg_color || $footer_top_bg_image || $footer_top_bg_repeat || $footer_top_bg_size || $footer_top_bg_attachment || $footer_top_bg_position ) :
		?>
	.footer-top {
		<?php
		if ( $footer_top_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_top_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_top_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_top_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_top_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_top_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_top_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_top_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_top_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_top_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_top_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_top_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_top_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_bg_color || $footer_bg_image || $footer_bg_repeat || $footer_bg_size || $footer_bg_attachment || $footer_bg_position ) :
		?>
	#footer {
		<?php
		if ( $footer_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_main_bg_color || $footer_main_bg_image || $footer_main_bg_repeat || $footer_main_bg_size || $footer_main_bg_attachment || $footer_main_bg_position ) :
		?>
	#footer .footer-main {
		<?php
		if ( $footer_main_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_main_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_main_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_main_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_main_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_main_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_main_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_main_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_main_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_main_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_main_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_main_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_main_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_bottom_bg_color || $footer_bottom_bg_image || $footer_bottom_bg_repeat || $footer_bottom_bg_size || $footer_bottom_bg_attachment || $footer_bottom_bg_position ) :
		?>
	#footer .footer-bottom, .footer-wrapper.fixed #footer .footer-bottom {
		<?php
		if ( $footer_bottom_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_bottom_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_bottom_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_bottom_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_bottom_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_bottom_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_bottom_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_bottom_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_bottom_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_bottom_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_bottom_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_bottom_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_bottom_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $breadcrumbs_bg_color || $breadcrumbs_bg_image || $breadcrumbs_bg_repeat || $breadcrumbs_bg_size || $breadcrumbs_bg_attachment || $breadcrumbs_bg_position ) :
		?>
	.page-top {
		<?php
		if ( $breadcrumbs_bg_color ) :
			?>
		background-color: <?php echo esc_html( $breadcrumbs_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $breadcrumbs_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $breadcrumbs_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $breadcrumbs_bg_image ) ); ?>') !important;
					<?php
		endif;
		if ( $breadcrumbs_bg_repeat ) :
			?>
	background-repeat: <?php echo esc_html( $breadcrumbs_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $breadcrumbs_bg_size ) :
			?>
	background-size: <?php echo esc_html( $breadcrumbs_bg_size ); ?> !important;
			<?php
		endif;
		if ( $breadcrumbs_bg_attachment ) :
			?>
	background-attachment: <?php echo esc_html( $breadcrumbs_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $breadcrumbs_bg_position ) :
			?>
	background-position: <?php echo esc_html( $breadcrumbs_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
endif;

if ( isset( $b['mainmenu-toplevel-link-color-sticky'] ) && $b['mainmenu-toplevel-link-color-sticky'] ) :
	if ( isset( $b['mainmenu-toplevel-link-color-sticky']['regular'] ) && $b['mainmenu-toplevel-link-color-sticky']['regular'] ) :
		?>
		#header.sticky-header .main-menu > li.menu-item > a,
		#header.sticky-header .main-menu > li.menu-custom-content a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color-sticky']['regular'] ); ?> }
		<?php
	endif;
	if ( isset( $b['mainmenu-toplevel-link-color-sticky']['hover'] ) && $b['mainmenu-toplevel-link-color-sticky']['hover'] ) :
		?>
		#header.sticky-header .main-menu > li.menu-item:hover > a,
		#header.sticky-header .main-menu > li.menu-item.active:hover > a,
		#header.sticky-header .main-menu > li.menu-custom-content:hover a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color-sticky']['hover'] ); ?> }
		<?php
	endif;
	if ( isset( $b['mainmenu-toplevel-link-color-sticky']['active'] ) && $b['mainmenu-toplevel-link-color-sticky']['active'] ) :
		?>
		#header.sticky-header .main-menu > li.menu-item.active > a,
		#header.sticky-header .main-menu > li.menu-custom-content.active a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color-sticky']['active'] ); ?> }
		<?php
	endif;
endif;

/* post type woocommerce */
$post_layout = $porto_settings['post-layout'];
if ( 'woocommerce' === $post_layout && ( ! class_exists( 'Woocommerce' ) || ! is_woocommerce() ) && ( is_home() || is_archive() || is_singular( 'post' ) ) ) :
	?>
	article.post-woocommerce .post-date,
	article.post-woocommerce > .read-more,
	.pagination>a, .pagination>span,
	.pagination .prev, .pagination .next,
	.sidebar-content .widget-title,
	.widget .tagcloud,
	input[type="submit"], .btn,
	.related-posts .read-more { font-family: 'Oswald', <?php echo sanitize_text_field( $b['h2-font']['font-family'] ); ?>, <?php echo sanitize_text_field( $b['h3-font']['font-family'] ); ?>; }
	article.post-full > .btn,
	.pagination>.dots { color: <?php echo esc_html( $b['skin-color'] ); ?> !important; }
	.pagination>a:hover, .pagination>a:focus, .pagination>span.current { background-color: <?php echo esc_html( $b['skin-color'] ); ?>; color: #fff; }

	.post.format-video .mejs-container .mejs-controls { opacity: 0; transition: opacity 0.25s ease; }
	.post.format-video .img-thumbnail:hover .mejs-container .mejs-controls { opacity: 1; }
	article.post-woocommerce { margin-<?php echo porto_filter_output( $left ); ?>: 90px; }
	article.post-woocommerce:after { content: ''; display: block; clear: both; }
	article.post-woocommerce h2.entry-title { color: #21293c; font-size: 22px; font-weight: 600; letter-spacing: normal; line-height: 1.2; margin-bottom: 15px; }
	article.post-woocommerce h2.entry-title a { color: inherit; }
	article.post-woocommerce .post-image,
	article.post-woocommerce .post-date { margin-<?php echo porto_filter_output( $left ); ?>: -90px; }
	article.post-woocommerce .post-date { width: 60px; }
	article.post-woocommerce .post-date .day { font-size: 28px; color: #21293c; font-weight: 400; border: 1px solid #e3e3e3; border-bottom: none; }
	body article.post-woocommerce .post-date .day { color: #21293c; background: none; }
	article.post-woocommerce .post-date .month { font-size: 14px; text-transform: uppercase; }
	article.post-woocommerce .post-meta { display: inline-block; margin-bottom: 6px; }
	article.post-woocommerce > .read-more { font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; float: <?php echo porto_filter_output( $right ); ?>; }
	article.post-woocommerce > .read-more:after { content: '\f04b'; font-family: 'Font Awesome 5 Free'; font-weight: 900; -moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased; margin-<?php echo porto_filter_output( $left ); ?>: 3px; position: relative; top: -1px; }
	article.post-woocommerce .post-content { padding-bottom: 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.06); margin-bottom: 15px; }
	article.post-woocommerce .post-meta { font-size: 13px; text-transform: uppercase; font-weight: 600; letter-spacing: 0; }
	article.post-woocommerce .post-meta a { color: #7b858a; }
	article.post-woocommerce .post-meta i,
	article.post-woocommerce .post-meta .post-views-icon.dashicons { font-size: 16px !important; }
	article.post-woocommerce .post-excerpt { font-size: 15px; line-height: 27px; color: #7b858a; }
	article.post-woocommerce .owl-carousel .owl-nav [class*="owl-"] { background: none; border: none; color: #9a9996; font-size: 30px; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-prev { <?php echo porto_filter_output( $left ); ?>: 20px; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-next { <?php echo porto_filter_output( $right ); ?>: 20px; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-prev:before { content: '\e829'; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-next:before { content: '\e828'; }

	.pagination>a, .pagination>span { padding: 0; min-width: 2.6em; width: auto; height: 2.8em; background: #d1f0ff; border: none; line-height: 2.8em; font-size: 15px; padding: 0 1em; }
	.pagination-wrap .pagination>a, .pagination-wrap .pagination>span { margin: 0 4px 8px; }
	.pagination>.dots { background: none; }
	.pagination .prev,
	.pagination .next { text-indent: 0; text-transform: uppercase; background: #272723; color: #fff; width: auto; }
	.pagination .prev:before,
	.pagination .next:before { display: none; }
	.pagination .prev i,
	.pagination .next i { font-size: 18px; }
	.pagination .prev i:before { content: '\f104'; }
	.pagination .next i:before { content: '\f105'; }
	.pagination span.dots { min-width: 1.8em; font-size: 15px; }

	/* sidebar */
	.widget .tagcloud a { font-size: 14px !important; text-transform: uppercase; color: #fff; background: #272723; padding: 12px 22px; border: none; border-radius: 3px; letter-spacing: 0.05em; }
	.sidebar-content { border: 1px solid #e1e1e1; padding: 20px; }
	.sidebar-content .widget:last-child { margin-bottom: 0; }
	.sidebar-content .widget .widget-title { font-size: 17px; font-weight: 400; }
	.widget-recent-posts { line-height: 1.25; }
	.widget-recent-posts a { color: #21293c; font-size: 16px; font-weight: 600; line-height: 1.25; }
	.post-item-small .post-date { margin-top: 10px; }
	.post-item-small .post-image img { width: 60px; margin-<?php echo porto_filter_output( $right ); ?>: 5px; margin-bottom: 5px; }
	.widget_categories>ul li { padding: <?php echo porto_filter_output( $rtl ? '10px 15px 10px 0' : '10px 0 10px 15px' ); ?>; }
	.widget>ul li>ul { margin-top: 10px; }
	.widget>ul { font-size: 14px; }
	.widget_categories > ul li:before { border: none; content: '\e81a'; font-family: 'porto'; font-size: 15px; color: #21293c; margin-<?php echo porto_filter_output( $right ); ?>: 15px; width: auto; height: auto; position: relative; top: -1px }
	.widget>ul { border-bottom: none; }
	<?php
endif;

/* single post */
if ( is_singular( 'post' ) ) :
	$post_layout = get_post_meta( get_the_ID(), 'post_layout', true );
	$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? $porto_settings['post-content-layout'] : $post_layout;
	if ( 'woocommerce' === $post_layout ) :
		?>
		article.post-woocommerce { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
		article.post-woocommerce .post-image, article.post-woocommerce .post-date { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
		article.post-woocommerce .post-date { margin-<?php echo porto_filter_output( $right ); ?>: 30px; }
		.single-post article.post-woocommerce .post-content { padding-bottom: 0; margin-bottom: 20px; }
		.single-post #content hr { display: none; }
		.entry-content { padding-bottom: 30px; border-bottom: 1px solid rgba(0, 0, 0, 0.06); padding-<?php echo porto_filter_output( $left ); ?>: 90px; margin-bottom: 20px; }
		@media (min-width: 1200px) {
			.entry-content { padding-<?php echo porto_filter_output( $right ); ?>: 80px; }
		}
		.post-share { margin-top: 0; padding: 0; display: inline-block; }
		.post-share > * { display: inline-block; vertical-align: middle; }
		.post-share .share-links { margin-<?php echo porto_filter_output( $left ); ?>: 3px; }
		.post-share h3 { margin: 0; }
		.post-block h3, .post-share h3, article.post .comment-respond h3, article.portfolio .comment-respond h3, .related-posts .sub-title { color: #21293c; font-size: 19px; font-weight: 700; text-transform: uppercase; line-height: 1.5; margin-bottom: 15px; }
		article.post-woocommerce .share-links a { width: 22px; height: 22px; border-radius: 11px; background: #939393; color: #fff; font-size: 11px; font-weight: 400; margin: <?php echo porto_filter_output( $rtl ? '2px 0 2px 4px' : '2px 4px 2px 0' ); ?>; }
		.post-meta { padding-<?php echo porto_filter_output( $left ); ?>: 90px; }
		.post-meta > * { vertical-align: middle; }
		article.post .post-meta>span, article.post .post-meta>.post-views { padding-<?php echo porto_filter_output( $right ); ?>: 20px; }
		.post-author { padding-bottom: 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.06); margin-bottom: 2rem; }
		.post-author h3 { display: none; }
		.post-author .name a { color: #21293c; font-size: 18px; text-transform: uppercase; }
		.post-author p { margin-bottom: 10px; font-size: 1em; line-height: 1.8; }
		article.post .comment-respond { margin-top: 0; }
		.comment-form input[type="text"], .comment-form textarea { box-shadow: none; }
		.comment-form input[type="text"] { padding: 10px 12px; }
		input[type="submit"], .btn { background: #272723; border: none; text-transform: uppercase; }
		.related-posts h3 { font-size: 19px; color: #21293c; line-height: 26px; font-weight: 600; margin-top: 5px; margin-bottom: 15px; }
		.related-posts .meta-date { color: #7b858a; font-size: 13px; text-transform: uppercase; letter-spacing: 0; }
		.related-posts .meta-date i { font-size: 18px; margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
		.related-posts .read-more { text-transform: uppercase; }
		.comment-form { background: none; border-radius: 0; padding: 0; }
		<?php
	endif;
endif;

/* horizontal shop filter */
global $porto_shop_filter_layout;
if ( $porto_shop_filter_layout ) :
	if ( 'horizontal' === $porto_shop_filter_layout || 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		.woocommerce-result-count { margin-bottom: 0; float: <?php echo porto_filter_output( $right ); ?>; margin-top: 6px; font-size: 12px; color: #7a7d82; margin-<?php echo porto_filter_output( $left ); ?>: 10px; line-height: 24px; }
		.shop-loop-before { background: #f4f4f4; padding: 12px 12px 2px; margin-bottom: 20px; }
		.shop-loop-before .woocommerce-ordering,
		.shop-loop-before .woocommerce-pagination > *,
		.shop-loop-before .gridlist-toggle { margin-bottom: 10px; }
		.shop-loop-before .gridlist-toggle { margin-top: 5px; }
		.shop-loop-before .woocommerce-ordering { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
		.shop-loop-before .woocommerce-ordering select, .shop-loop-before .woocommerce-viewing select { border: none; }
		@media (max-width: 575px) {
			.shop-loop-before .woocommerce-result-count { display: none; }
			.shop-loop-before .woocommerce-ordering select { width: 140px; }
		}
		<?php
	}
	if ( 'horizontal' === $porto_shop_filter_layout ) {
		?>
		.porto-product-filters-toggle { display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-align-items: center; -ms-align-items: center; align-items: center; position: relative; padding: 14px; background: #f4f4f4; margin-bottom: 20px; }
		.shop-loop-before .porto-product-filters-toggle { float: <?php echo porto_filter_output( $left ); ?>; height: 34px; margin-<?php echo porto_filter_output( $right ); ?>: 10px; padding: 0; background: none; margin-bottom: 0; }
		.porto-product-filters-toggle a { position: relative; width: 46px; height: 26px; background: #e6e6e6; border-radius: 13px; transition: all .3s linear; margin-<?php echo porto_filter_output( $left ); ?>: 8px; }
		.porto-product-filters-toggle a:before { content: ''; position: absolute; left: 0; width: 42px; height: 22px; background-color: #fff; border-radius: 11px; -webkit-transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1); -ms-transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1); transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1); transition: all .3s linear; }
		.porto-product-filters-toggle a:after { content: ''; position: absolute; left: 0; width: 22px; height: 22px; background-color: #fff; border-radius: 11px; box-shadow: 0 2px 2px rgba(0, 0, 0, 0.24); -webkit-transform: translate3d(2px, 2px, 0); -ms-transform: translate3d(2px, 2px, 0); transform: translate3d(2px, 2px, 0); transition: all .2s ease-in-out; }
		.porto-product-filters-toggle a:active:after { width: 28px; -webkit-transform: translate3d(2px, 2px, 0); -ms-transform: translate3d(2px, 2px, 0); transform: translate3d(2px, 2px, 0); }
		.porto-product-filters-toggle.opened a:active:after { -webkit-transform: translate3d(16px, 2px, 0); -ms-transform: translate3d(16px, 2px, 0); transform: translate3d(16px, 2px, 0); }
		.porto-product-filters-toggle.opened a { background-color: <?php echo esc_html( $b['skin-color'] ); ?>; }
		.porto-product-filters-toggle.opened a:before { -webkit-transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); -ms-transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); }
		.porto-product-filters-toggle.opened a:after { -webkit-transform: translate3d(22px, 2px, 0); -ms-transform: translate3d(22px, 2px, 0); transform: translate3d(22px, 2px, 0); }
		.porto-product-filters-toggle a:hover { text-decoration: none; }
		@media (min-width: 992px) {
			.shop-loop-before { text-align: <?php echo porto_filter_output( $right ); ?>; padding-left: 20px; padding-right: 20px; }
			.porto-product-filters-toggle + .woocommerce-ordering { display: inline-block; float: none; margin-<?php echo porto_filter_output( $right ); ?>: 20px; }
			.woocommerce-result-count { margin-<?php echo porto_filter_output( $left ); ?>: 20px; }
			.main-content-wrap { overflow: hidden; }
			.main-content-wrap .sidebar { transition: left .3s linear, right .3s linear, visibility .3s linear, z-index .3s linear; visibility: hidden; z-index: -1; }
			.main-content-wrap .left-sidebar { <?php echo porto_filter_output( $left ); ?>: -25%; }
			.main-content-wrap .right-sidebar { <?php echo porto_filter_output( $right ); ?>: -25%; }
			.main-content-wrap .main-content { transition: all 0.3s linear 0s; }
			.main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $left ); ?>: -25%; max-width: 100%; -webkit-flex: 0 0 100%; -ms-flex: 0 0 100%; flex: 0 0 100%; min-height: 1px; }
			.column2-right-sidebar .main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $right ); ?>: -25%; margin-<?php echo porto_filter_output( $left ); ?>: 0; }
			.main-content-wrap.opened .sidebar { z-index: 0; visibility: visible; }
			.main-content-wrap.opened .left-sidebar { <?php echo porto_filter_output( $left ); ?>: 0; }
			.main-content-wrap.opened .right-sidebar { <?php echo porto_filter_output( $right ); ?>: 0; }
			.main-content-wrap.opened .main-content { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
			.column2-right-sidebar .main-content-wrap.opened .main-content { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
			ul.products li.product-col { transition: width 0.3s linear 0s; }
		}
		@media (max-width: 767px) {
			.shop-loop-before { padding-left: 10px; padding-right: 10px; }
			.porto-product-filters-toggle + .woocommerce-ordering label { display: none; }
			.woocommerce-ordering select { width: 140px; }
		}
		<?php if ( $is_wide ) : ?>
		@media (min-width: 1500px) {
			.main-content-wrap .left-sidebar { <?php echo porto_filter_output( $left ); ?>: -20%; }
			.main-content-wrap .right-sidebar{ <?php echo porto_filter_output( $right ); ?>: -20%; }
			.main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $left ); ?>: -20%; }
		}
	<?php endif; ?>
		body.woocommerce-page.archive .sidebar-content { border-bottom: none !important; }
		body.woocommerce-page.archive .sidebar-content aside.widget.woocommerce:last-child { border-bottom: 1px solid #efefef; }
		.sidebar .sidebar-content .widget:not(.woocommerce) { display: none; }
		body.woocommerce .porto-products-filter-body > .main-content { padding-top: 0; }
		<?php
	} elseif ( 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		.porto_widget_price_filter .widget-title { position: relative; }
		.porto_widget_price_filter .widget-title .toggle { display: inline-block; width: 1.8571em; height: 1.8571em; line-height: 1.7572em; position: absolute; <?php echo porto_filter_output( $right ); ?>: -7px; top: 50%; margin-top: -0.9em; padding: 0; cursor: pointer; font-family: "porto"; text-align: center; transition: all 0.25s ease 0s; color: #21293c; font-size: 17px; }
		.porto_widget_price_filter { font-weight: 500; }
		.porto_widget_price_filter label { font-size: 12px; margin-bottom: 0; }
		.porto_widget_price_filter .form-control { box-shadow: none; margin-bottom: 10px; padding-top: 6px; padding-bottom: 6px; }
		.porto_widget_price_filter .widget-title .toggle:before { content: "\e81c"; }
		.porto-product-filters .widget>div>ul, .porto-product-filters .widget>ul { font-size: 12px; font-weight: 600; border-bottom: none; text-transform: uppercase; padding: 0; }
		.porto_widget_price_filter .button { text-transform: uppercase; }
		.porto-product-filters .widget>div>ul li, .porto-product-filters .widget>ul li { border-top: none; }
		.porto-product-filters .widget_product_categories ul li>a,
		.porto-product-filters .widget_product_categories ol li>a,
		.porto-product-filters .porto_widget_price_filter ul li>a,
		.porto-product-filters .porto_widget_price_filter ol li>a,
		.porto-product-filters .widget_layered_nav ul li>a,
		.porto-product-filters .widget_layered_nav ol li>a, 
		.porto-product-filters .widget_layered_nav_filters ul li>a,
		.porto-product-filters .widget_layered_nav_filters ol li>a,
		.porto-product-filters .widget_rating_filter ul li>a,
		.porto-product-filters .widget_rating_filter ol li>a { padding: 7px 0; }
		.porto-product-filters .widget_product_categories ul li .toggle { top: 3px; }
		.widget_product_categories ul li .toggle:before { content: '\f105' !important; font-weight: 900; font-family: 'Font Awesome 5 Free' !important; -moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased }

		.gridlist-toggle { margin-<?php echo porto_filter_output( $left ); ?>: 6px; }
		.woocommerce-ordering label { display: none; }
		.woocommerce-ordering select { margin-<?php echo porto_filter_output( $left ); ?>: 0; text-transform: uppercase; }
		.porto-product-filters { float: <?php echo porto_filter_output( $left ); ?>; }
		.porto-product-filters .widget-title { font-family: inherit; }
		.porto-product-filters .widget-title .toggle { display: none; }
		.porto-product-filters .widget { display: block; max-width: none; width: auto; flex: none; padding: 0; background: #fff url("<?php echo PORTO_URI; ?>/images/select-bg.svg") no-repeat; background-position: <?php echo porto_filter_output( $rtl ? '4' : '96' ); ?>% -13px; background-size: 26px 60px; margin-bottom: 10px; margin-top: 0; margin-<?php echo porto_filter_output( $right ); ?>: 10px; position: relative; font-size: .9286em; }
		.porto-product-filters-body { display: -webkit-inline-flex; display: -ms-inline-flexbox; display: inline-flex; vertical-align: middle; }
		@media (min-width: 992px) {
			.porto-product-filters .widget-title { background: none; font-size: inherit !important; border-bottom: none; padding: 0; color: inherit !important; font-weight: 400; cursor: pointer; height: 34px; line-height: 34px; padding: 0 10px; width: 160px; color: inherit; margin-bottom: 0;  transition: none; }
			.woocommerce-ordering select { width: 160px; }
			.porto-product-filters .widget>div>ul, .porto-product-filters .widget>ul, .porto-product-filters .widget > form { display: none; position: absolute; padding: 10px 15px 10px; top: 100%; margin-top: 9px; <?php echo porto_filter_output( $left ); ?>: 0; min-width: 220px; background: #fff; z-index: 99; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
			.porto-product-filters .opened .widget-title:before { content: ''; position: absolute; top: 100%; border-bottom: 11px solid #e8e8e8; border-left: 11px solid transparent; border-right: 11px solid transparent; <?php echo porto_filter_output( $left ); ?>: 20px; }
			.porto-product-filters .opened .widget-title:after { content: ''; position: absolute; top: 100%; border-bottom: 10px solid #fff; border-left: 10px solid transparent; border-right: 10px solid transparent; <?php echo porto_filter_output( $left ); ?>: 21px; margin-top: 1px; z-index: 999; }
		}
		@media (min-width: 992px) and (max-width: <?php echo (int) $b['container-width'] + (int) $b['grid-gutter-width'] - 1; ?>px) {
			.porto-product-filters .widget-title,
			.woocommerce-ordering select { width: 140px; }
		}
		<?php
	}
	if ( 'horizontal' === $porto_shop_filter_layout || 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		@media (max-width: 991px) {
			.porto-product-filters-toggle { display: none; }
			.porto-product-filters .sidebar-toggle { margin-top: 50px; }
			.porto-product-filters.mobile-sidebar { position: fixed; }
			.porto-product-filters .widget { float: none; margin-right: 0; background: none; margin-bottom: 20px; width: 100%; }
			.porto-product-filters .row > .widget { padding-left: 10px !important; padding-right: 10px !important; }
			.porto-product-filters .porto-product-filters-body { height: 100%; overflow-x: hidden; overflow-y: scroll; padding: 20px; display: block !important; top: 0; box-shadow: none; }
			.porto-product-filters .widget-title { padding: 0; background: none; border-bottom: none; background: none; pointer-events: none; margin-bottom: 15px; }
			.porto-product-filters .widget-title .toggle { display: none; }
			.porto-product-filters .widget>div>ul, .porto-product-filters .widget>ul, .porto-product-filters .widget > form { display: block !important; }
		}
		html.filter-sidebar-opened body > * { z-index: 0; }
		html.filter-sidebar-opened .porto-product-filters { z-index: 9000; transition: transform 0.3s ease-in-out; -webkit-transform: translate(0px); transform: translate(0px); }
		html.filter-sidebar-opened .sidebar-toggle i:before { content: '\f00d'; }
		<?php
	}
endif;

if ( class_exists( 'Woocommerce' ) && ! is_checkout() && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) :
	?>
	#login-form-popup { position: relative; width: 80%; max-width: 872px; margin-left: auto; margin-right: auto; }
	#login-form-popup .featured-box { margin-bottom: 0; box-shadow: none; border: none; }
	#login-form-popup .featured-box .box-content { padding: 25px 35px; }
	#login-form-popup .featured-box h2 { text-transform: uppercase; font-size: 15px; letter-spacing: 0.05em; font-weight: 600; color: <?php echo esc_html( $b['h2-font']['color'] ); ?>; line-height: 2; }
	.porto-social-login-section { background: #f4f4f2; text-align: center; padding: 20px 20px 25px; }
	.porto-social-login-section p { text-transform: uppercase; font-size: 12px; color: <?php echo esc_html( $b['h4-font']['color'] ); ?>; font-weight: 600; margin-bottom: 8px; }
	#login-form-popup .col2-set { margin-left: -20px; margin-right: -20px; }
	#login-form-popup .col-1, #login-form-popup .col-2 { padding-left: 20px; padding-right: 20px; }
	@media (min-width: 992px) {
		#login-form-popup .col-1 { border-<?php echo porto_filter_output( $right ); ?>: 1px solid #f5f6f6; }
	}
	#login-form-popup .input-text { box-shadow: none; padding-top: 10px; padding-bottom: 10px; border-color: #ddd; border-radius: 2px; }
	#login-form-popup form label { font-size: 12px; line-height: 1; }
	#login-form-popup .form-row { margin-bottom: 20px; }
	#login-form-popup .button { border-radius: 2px; padding: 10px 24px; text-transform: uppercase; text-shadow: none; 
	<?php
	if ( isset( $b['add-to-cart-font'] ) && ! empty( $b['add-to-cart-font']['font-family'] ) ) {
		echo 'font-family: ' . sanitize_text_field( $b['add-to-cart-font']['font-family'] ) . ';'; }
	?>
	font-size: 12px; letter-spacing: 0.025em; color: #fff; }
	#login-form-popup label.inline { margin-top: 15px; float: <?php echo porto_filter_output( $right ); ?>; position: relative; cursor: pointer; line-height: 1.5; }
	#login-form-popup label.inline input[type=checkbox] { opacity: 0; margin-<?php echo porto_filter_output( $right ); ?>: 8px; margin-top: 0; margin-bottom: 0; }
	#login-form-popup label.inline span:before { content: ''; position: absolute; border: 1px solid #ddd; border-radius: 1px; width: 16px; height: 16px; <?php echo porto_filter_output( $left ); ?>: 0; top: 0; text-align: center; line-height: 15px; font-family: 'Font Awesome 5 Free'; font-weight: 900; font-size: 9px; color: #aaa; }
	#login-form-popup label.inline input[type=checkbox]:checked + span:before { content: '\f00c'; }
	#login-form-popup .social-button { text-decoration: none; margin-left: 10px; margin-right: 10px; }
	#login-form-popup .social-button i { font-size: 16px; margin-<?php echo porto_filter_output( $right ); ?>: 8px; }
	#login-form-popup p.status { color: <?php echo esc_html( $b['h4-font']['color'] ); ?>; }
	#login-form-popup .lost_password { margin-top: -15px; font-size: 13px; margin-bottom: 0; }
	.porto-social-login-section .google-plus { background: #dd4e31; }
	.porto-social-login-section .facebook { background: #3a589d; }
	.porto-social-login-section .twitter { background: #1aa9e1; }
	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) : ?>
		#login-form-popup { max-width: 480px; }
	<?php endif; ?>
	html.panel-opened body > .mfp-bg { z-index: 9042; }
	html.panel-opened body > .mfp-wrap { z-index: 9043; }
	<?php
endif;


/* header builder custom css */
if ( ! is_customize_preview() && ! porto_header_type_is_preset() ) {
	$current_layout = porto_header_builder_layout();
	if ( isset( $current_layout['custom_css'] ) && $current_layout['custom_css'] ) {
		echo trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $current_layout['custom_css'] ) );
	}
}

/* custom css */
$theme_options_custom_css = $porto_settings['css-code'];
$custom_css               = porto_get_meta_value( 'custom_css' );
if ( $theme_options_custom_css ) {
	echo trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $theme_options_custom_css ) );
}
if ( ! empty( $custom_css ) ) {
	echo trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $custom_css ) );
}
