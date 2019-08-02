<?php
/**
 * define porto blocks
 *
 * @since 4.8.4
 */

if ( ! class_exists( 'PortoBlocksClass' ) ) :
	class PortoBlocksClass {
		function __construct() {
			add_action( 'rest_api_init', array( $this, 'registerRestAPI' ) );
		}

		function registerRestAPI() {
			// Register router to get data for Woocommerce Products block
			if ( class_exists( 'WC_REST_Products_Controller' ) ) {
				include_once( 'class-products-controller.php' );
				$controller = new PortoBlocksProductsController();
				$controller->register_routes();
			}

			register_rest_field(
				'post',
				'featured_image_src',
				array(
					'get_callback'    => array( $this, 'featuredImageSrc' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
		}

		/**
		 * Get featured image link for REST API
		 *
		 * @param array $object API Object
		 *
		 * @return mixed
		 */
		public function featuredImageSrc( $object ) {
			$featured_img_full   = wp_get_attachment_image_src(
				$object['featured_media'],
				'full',
				false
			);
			$featured_img_large  = wp_get_attachment_image_src(
				$object['featured_media'],
				'blog-large',
				false
			);
			$featured_img_list   = wp_get_attachment_image_src(
				$object['featured_media'],
				'blog-medium',
				false
			);
			$featured_img_medium = wp_get_attachment_image_src(
				$object['featured_media'],
				'medium',
				false
			);

			return array(
				'landsacpe' => $featured_img_large,
				'list'      => $featured_img_list,
				'medium'    => $featured_img_medium,
				'full'      => $featured_img_full,
			);
		}
	}
endif;

new PortoBlocksClass();
