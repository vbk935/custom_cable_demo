<?php
/**
 * Porto Studio
 *
 * @author     Porto Themes
 * @category   Library
 * @since      4.9.10
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Studio' ) ) :

	class Porto_Studio {

		public function __construct() {
			// add studio tab to vc templates panel
			add_filter( 'vc_get_all_templates', array( $this, 'add_tab' ) );

			add_filter( 'vc_nav_controls', array( $this, 'add_studio_control' ) );

			add_action( 'wp_ajax_porto_studio_import', array( $this, 'import' ) );
			add_action( 'wp_ajax_nopriv_porto_studio_import', array( $this, 'import' ) );
		}

		public function add_tab( $data ) {
			$data[] = array(
				'category'             => 'porto_studio',
				'category_name'        => __( 'Porto Studio', 'porto' ),
				'category_description' => __( 'Pre-defined templates for Porto theme', 'porto' ),
				'category_weight'      => 8,
				'templates'            => array(),
				'output'               => $this->get_tab_content(),
			);
			return $data;
		}

		public function add_studio_control( $list ) {
			for ( $index = count( $list ) - 1; $index >= 1; $index-- ) {
				$list[ $index ] = $list[ $index - 1 ];
			}
			$list[1] = array( 'porto_studio', '<li><a href="javascript:;" class="vc_icon-btn porto-studio-editor-button" id="porto-studio-editor-button" title="Porto Studio">Porto Studio</a></li>' );
			return $list;
		}

		public function import() {
			check_ajax_referer( 'porto-nonce', 'nonce' );

			if ( isset( $_POST['block_id'] ) ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();

				$args = $importer_api->generate_args( false );
				$url  = add_query_arg( $args, $importer_api->get_url( 'blocks_content' ) );
				$url  = add_query_arg( array( 'block_id' => sanitize_text_field( $_POST['block_id'] ) ), $url );

				$block = $importer_api->get_response( $url );
				if ( is_wp_error( $block ) ) {
					echo json_encode( array( 'error' => esc_js( __( 'Security issue found! Please try again later.', 'porto' ) ) ) );
					die();
				}

				$block_content = $block['content'];
				if ( isset( $block['images'] ) ) {
					// Check if image is already imported by its ID.
					$query = new \WP_Query(
						array(
							'post_type'   => 'attachment',
							'post_status' => 'inherit',
							'meta_query'  => array(
								array(
									'key'     => '_porto_studio_id',
									'value'   => array_keys( $block['images'] ),
									'compare' => 'IN',
								),
							),
						)
					);

					if ( $query->have_posts() ) {
						foreach ( $query->posts as $attachment ) {
							$block_content = str_replace( '{{{' . get_post_meta( $attachment->ID, '_porto_studio_id', true ) . '}}}', $attachment->ID, $block_content );
						}
					} else {

						if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
							define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
						}

						if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
							require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
						}

						if ( ! class_exists( 'WP_Import' ) ) { // if WP importer doesn't exist
							require_once PORTO_PLUGINS . '/importer/wordpress-importer.php';
						}

						if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) {

							$importer                    = new WP_Import();
							$importer->fetch_attachments = true;

							foreach ( $block['images'] as $image_id => $image_url ) {
								$post_data = array(
									'post_title'   => substr( $image_url, strrpos( $image_url, '/' ) + 1, -4 ),
									'post_content' => '',
									'upload_date'  => date( 'Y-m-d H:i:s' ),
									'post_status'  => 'inherit',
								);
								$import_id = $importer->process_attachment( $post_data, $image_url );
								if ( ! is_wp_error( $import_id ) ) {
									update_post_meta( $import_id, '_porto_studio_id', $image_id );
									$block_content = str_replace( '{{{' . $image_id . '}}}', $import_id, $block_content );
								}
							}
						}
					}
				}
				echo json_encode( array( 'content' => $block_content ) );
				die();
			}
		}

		private function get_tab_content() {

			//$blocks = get_site_transient( 'porto_blocks' );
			//if ( ! $blocks ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();
				$blocks       = $importer_api->get_response( 'blocks' );
				if ( is_wp_error( $blocks ) || ! $blocks ) {
					return esc_html__( 'Could not connect to the API Server! Please try again later.', 'porto' );
				}
				//set_site_transient( 'porto_blocks', $blocks, 24 * HOUR_IN_SECONDS );
			//}

			if ( is_array( $blocks ) ) {
				ob_start();
				porto_get_template_part(
					'inc/lib/porto-studio/blocks.tpl',
					null,
					array(
						'blocks' => $blocks,
					)
				);
				return ob_get_clean();
			}

			return '';
		}
	}

	//new Porto_Studio;
endif;
