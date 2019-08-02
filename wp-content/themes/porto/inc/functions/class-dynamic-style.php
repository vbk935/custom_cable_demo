<?php
/**
 * Porto Dynamic Style
 *
 * @author     Porto Themes
 * @category   Style Functions
 * @since      4.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Porto_Dynamic_Style' ) ) :
	class Porto_Dynamic_Style {

		protected $mode = null;

		public function __construct() {
			add_action( 'wp', array( $this, 'init' ) );

			add_action( 'porto_admin_save_theme_settings', array( $this, 'compile_dynamic_css' ) );
			add_action( 'customize_save_after', array( $this, 'compile_dynamic_css' ), 99 );
		}

		public function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 990 );
			if ( 'internal' == $this->get_mode() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'output_dynamic_styles' ), 1002 );
			}
			if ( ! is_customize_preview() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'output_internal_styles' ), 1005 );
			}
			add_action( 'wp_head', array( $this, 'output_internal_js' ), 153 );
			add_action( 'wp_footer', array( $this, 'output_custom_js_body' ) );
		}

		public function get_mode() {
			if ( null != $this->mode ) {
				return $this->mode;
			}
			$upload_dir = wp_upload_dir();
			$css_file   = $upload_dir['basedir'] . '/porto_styles/dynamic_style.css';
			if ( ! get_option( 'porto_dynamic_style', false ) || is_customize_preview() || ! file_exists( $css_file ) ) {
				$this->mode = 'internal';
			} else {
				$this->mode = 'file';
			}
			return $this->mode;
		}

		/**
		 * compile dynamic css when saving theme options
		 */
		public function compile_dynamic_css() {
			global $reduxPortoSettings;
			$reduxFramework = $reduxPortoSettings->ReduxFramework;

			$upload_dir = wp_upload_dir();
			$style_path = $upload_dir['basedir'] . '/porto_styles';
			// Compile dynamic styles
			$rtl_arr                               = array( '', '_rtl' );
			$GLOBALS['porto_save_settings_is_rtl'] = false;
			try {
				if ( ! file_exists( $style_path ) ) {
					wp_mkdir_p( $style_path );
				}
				$result = true;

				foreach ( $rtl_arr as $rtl_arr_value ) {
					ob_start();
					include PORTO_DIR . '/style.php';
					$css = ob_get_clean();

					$filename = $style_path . '/dynamic_style' . $rtl_arr_value . '.css';
					porto_check_file_write_permission( $filename );
					$result = $reduxFramework->filesystem->execute( 'put_contents', $filename, array( 'content' => $this->minify_css( $css ) ) );
					if ( $result ) {
						$result = true;
					} else {
						$result = false;
					}
					$GLOBALS['porto_save_settings_is_rtl'] = true;
				}

				// compile gutenberg editor style
				ob_start();
				include PORTO_DIR . '/style-editor.php';
				$css      = ob_get_clean();
				$filename = $style_path . '/style-editor.css';
				porto_check_file_write_permission( $filename );
				$result1 = $reduxFramework->filesystem->execute( 'put_contents', $filename, array( 'content' => $this->minify_css( $css ) ) );
				if ( $result1 && $result ) {
					$result = true;
				} else {
					$result = false;
				}

				update_option( 'porto_dynamic_style', $result );
			} catch ( Exception $e ) {
				update_option( 'porto_dynamic_style', false );
				// try to recompile dynamic style in every 4 days if compilation is failed
				set_transient( 'porto_dynamic_style_time', time(), DAY_IN_SECONDS * 4 );
			}
			unset( $GLOBALS['porto_save_settings_is_rtl'] );
		}

		public function output_dynamic_styles( $output = false ) {

			ob_start();
			require_once( PORTO_DIR . '/style.php' );
			if ( is_customize_preview() ) {
				require_once( PORTO_DIR . '/style-internal.php' );
			}
			$css = ob_get_clean();
			if ( $output ) {
				return $this->minify_css( $css );
			} else {
				wp_add_inline_style( 'porto-style', apply_filters( 'porto_dynamic_style_internal_output', $this->minify_css( $css ) ) );
			}
		}

		public function output_internal_styles() {

			ob_start();
			require_once( PORTO_DIR . '/style-internal.php' );
			do_action( 'porto_head_css' );
			$css = ob_get_clean();

			wp_add_inline_style( 'porto-style', $this->minify_css( $css ) );
		}

		public function output_internal_js() {
			global $porto_settings;
			if ( isset( $porto_settings['js-code-head'] ) && trim( $porto_settings['js-code-head'] ) ) { ?>
				<script>
					<?php echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $porto_settings['js-code-head'] ) ); ?>
				</script>
				<?php
			}
			$custom_js_head = porto_get_meta_value( 'custom_js_head' );
			if ( isset( $custom_js_head ) && trim( $custom_js_head ) ) {
				?>
				<script>
					<?php echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $custom_js_head ) ); ?>
				</script>
				<?php
			}
		}

		public function output_custom_js_body() {
			$custom_js_body = porto_get_meta_value( 'custom_js_body' );
			if ( ! empty( $custom_js_body ) ) {
				?>
				<script>
					<?php echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $custom_js_body ) ); ?>
				</script>
				<?php
			}
		}

		public function enqueue_style() {

			global $porto_settings_optimize;

			// load visual composer styles
			if ( defined( 'WPB_VC_VERSION' ) && isset( $porto_settings_optimize['shortcodes_to_remove'] ) && ! empty( $porto_settings_optimize['shortcodes_to_remove'] ) ) {
				$upload_dir = wp_upload_dir();
				$css_file   = $upload_dir['basedir'] . '/porto_styles/js_composer.css';
				if ( file_exists( $css_file ) ) {
					wp_deregister_style( 'js_composer_front' );
					wp_dequeue_style( 'js_composer_front' );
					porto_register_style( 'js_composer_front', 'js_composer', false, false );
				}
			}

			// bootstrap css
			if ( is_customize_preview() ) {
				// config file
				ob_start();
				require PORTO_ADMIN . '/theme_options/config_scss_bootstrap.php';
				$_config_css = ob_get_clean();

				if ( ! class_exists( 'scssc' ) ) {
					require_once( PORTO_ADMIN . '/scssphp/scss.inc.php' );
				}
				$scss = new scssc();
				$scss->setImportPaths( PORTO_DIR . '/scss' );
				$scss->setFormatter( 'scss_formatter_crunched' );

				try {
					// bootstrap styles
					ob_start();
					$optimize_suffix = '';
					if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
						$optimize_suffix = '.optimized';
					}
					if ( is_rtl() ) {
						$rtl_prefix = '$rtl: 1; $dir: rtl !default;';
					} else {
						$rtl_prefix = '$rtl: 0; $dir: ltr !default;';
					}
					echo '' . $scss->compile( $rtl_prefix . '@import "plugins/directional"; ' . $_config_css . ' @import "plugins/bootstrap/bootstrap' . $optimize_suffix . '";' );
					$css = ob_get_clean();

					if ( wp_style_is( 'js_composer_front', 'registered' ) ) {
						wp_add_inline_style( 'js_composer_front', $css );
					} else {
						wp_add_inline_style( 'wp-block-library', $css );
					}
				} catch ( Exception $e ) {
				}
			} else {
				wp_deregister_style( 'bootstrap' );
				if ( is_rtl() ) {
					porto_register_style( 'bootstrap', 'bootstrap_rtl', false, true );
				} else {
					porto_register_style( 'bootstrap', 'bootstrap', false, true );
				}
			}

			// dynamic styles
			if ( 'file' == $this->get_mode() ) {
				wp_deregister_style( 'porto-dynamic-style' );
				if ( is_rtl() ) {
					porto_register_style( 'porto-dynamic-style', 'dynamic_style_rtl', false, false );
				} else {
					porto_register_style( 'porto-dynamic-style', 'dynamic_style', false, false );
				}
			}
		}

		protected function minify_css( $css ) {
			$output = preg_replace( '#/\*.*?\*/#s', '', $css );
			$output = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $output );
			$output = preg_replace( '/\s\s+(.*)/', '$1', $output );
			return $output;
		}

	}
	if ( is_customize_preview() ) {
		$GLOBALS['porto_dynamic_style'] = new Porto_Dynamic_Style();
	} else {
		new Porto_Dynamic_Style();
	}

endif;
