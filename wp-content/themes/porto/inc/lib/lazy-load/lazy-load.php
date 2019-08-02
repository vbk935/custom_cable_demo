<?php
/**
 * Porto Lazy Load
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// generate placeholders
if ( ! function_exists( 'porto_generate_placeholder' ) ) :
	function porto_generate_placeholder( $image_size, $placeholder_width = 100 ) {

		if ( preg_match_all( '/(\d+)x(\d+)/', $image_size, $sizes ) ) {
			$width  = isset( $sizes[1][0] ) ? $sizes[1][0] : '1';
			$height = isset( $sizes[2][0] ) ? $sizes[2][0] : '1';
		} else {
			$image_sizes = wp_get_additional_image_sizes();
			if ( in_array( $image_size, $image_sizes ) ) {
				$width  = $image_sizes[ $image_size ]['width'];
				$height = $image_sizes[ $image_size ]['height'];
			} else {
				$width  = '1';
				$height = '1';
			}
		}

		if ( $width === $height || ( '1' === $width && '1' === $height ) ) {
			return array( PORTO_URI . '/images/lazy.png', $width, $height );
		}

		$upload_dir         = wp_upload_dir();
		$placeholder_height = round( $height * ( $placeholder_width / $width ) );
		$placeholder_path   = $upload_dir['basedir'] . '/porto_placeholders/' . $placeholder_width . 'x' . $placeholder_height . '.jpg';
		$placeholder_url    = $upload_dir['baseurl'] . '/porto_placeholders/' . $placeholder_width . 'x' . $placeholder_height . '.jpg';
		if ( file_exists( $placeholder_path ) ) {
			return array( $placeholder_url, $width, $height );
		}

		if ( ! file_exists( $upload_dir['basedir'] . '/porto_placeholders' ) ) {
			wp_mkdir_p( $upload_dir['basedir'] . '/porto_placeholders' );
		}

		$im = @imagecreatetruecolor( $placeholder_width, $placeholder_height );
		if ( ! $im ) {
			return array( PORTO_URI . '/images/lazy.png', $width, $height );
		}
		$bgc = @imagecolorallocate( $im, 245, 245, 245 );
		@imagefilledrectangle( $im, 0, 0, $placeholder_width, $placeholder_height, $bgc );
		@imagejpeg( $im, $placeholder_path, 40 );
		@imagedestroy( $im );
		return array( $placeholder_url, $width, $height );
	}
endif;

if ( ! class_exists( 'Porto_LazyLoad_Images' ) ) :
	class Porto_LazyLoad_Images {

		static function init() {
			global $porto_settings_optimize;
			if ( ! isset( $porto_settings_optimize['lazyload'] ) || ! $porto_settings_optimize['lazyload'] || porto_is_ajax() ) {
				return;
			}
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 99 );
			add_action( 'wp_head', array( __CLASS__, 'setup' ), 99 );
		}
		static function setup() {
			add_filter( 'the_content', array( __CLASS__, 'add_image_placeholders' ), 9999 );
			add_filter( 'post_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'get_avatar', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'woocommerce_single_product_image_html', array( __CLASS__, 'add_image_placeholders' ), 9999 );
			add_filter( 'porto_lazy_load_images', array( __CLASS__, 'add_image_placeholders' ), 9999 );
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 9999 );

			wp_enqueue_script( 'jquery-lazyload' );
		}
		static function add_scripts() {

		}
		static function add_image_placeholders( $content ) {

			if ( is_feed() || is_preview() ) {
				return $content;
			}

			if ( false !== strpos( $content, 'data-src' ) || false !== strpos( $content, 'data-original' ) ) {
				return $content;
			}

			$matches = array();
			preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches );

			$search  = array();
			$replace = array();

			$i = 0;
			foreach ( $matches[0] as $img_html ) {

				if ( ! preg_match( "/src=['\"]data:image/is", $img_html ) ) {
					$lazy_image = get_template_directory_uri() . '/images/lazy.png';

					$i++;
					// replace the src and add the data-src
					$replace_html = '';

					if ( preg_match( '/width=["\']/i', $img_html ) && preg_match( '/height=["\']/i', $img_html ) ) {
						preg_match( '/width=(["\'])(.*?)["\']/is', $img_html, $match_width );
						preg_match( '/height=(["\'])(.*?)["\']/is', $img_html, $match_height );
						if ( isset( $match_width[2] ) && isset( $match_height[2] ) ) {
							$lazy_image = porto_generate_placeholder( $match_width[2] . 'x' . $match_height[2] );
							$lazy_image = $lazy_image[0];
						}
					}

					$replace_html = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . esc_url( $lazy_image ) . '" data-original=', $img_html );
					$replace_html = preg_replace( '/<img(.*?)srcset=/is', '<img$1srcset="' . esc_url( $lazy_image ) . ' 100w" data-srcset=', $replace_html );

					if ( preg_match( '/class=["\']/i', $replace_html ) ) {
						$replace_html = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1porto-lazyload $2$1', $replace_html );
					} else {
						$replace_html = preg_replace( '/<img/is', '<img class="porto-lazyload"', $replace_html );
					}

					array_push( $search, $img_html );
					array_push( $replace, $replace_html );
				}
			}

			$search  = array_unique( $search );
			$replace = array_unique( $replace );

			$content = str_replace( $search, $replace, $content );

			return $content;
		}
	}

	if ( ! is_admin() && ! is_customize_preview() ) {
		add_action( 'init', array( 'Porto_LazyLoad_Images', 'init' ) );
	}
endif;
