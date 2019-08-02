<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class PortoShortcodesClass {

	private $shortcodes = array(
		'porto_toggles',
		'porto_block',
		'porto_container',
		'porto_animation',
		'porto_carousel',
		'porto_carousel_item',
		'porto_testimonial',
		'porto_content_box',
		'porto_image_frame',
		'porto_preview_image',
		'porto_feature_box',
		'porto_lightbox_container',
		'porto_lightbox',
		'porto_blockquote',
		'porto_tooltip',
		'porto_popover',
		'porto_grid_container',
		'porto_grid_item',
		'porto_links_block',
		'porto_links_item',
		'porto_recent_posts',
		'porto_blog',
		'porto_recent_portfolios',
		'porto_portfolios',
		'porto_portfolios_category',
		'porto_recent_members',
		'porto_members',
		'porto_faqs',
		'porto_concept',
		'porto_map_section',
		'porto_history',
		'porto_diamonds',
		'porto_section',
		'porto_price_boxes',
		'porto_price_box',
		'porto_sort_filters',
		'porto_sort_filter',
		'porto_sort_container',
		'porto_sort_item',
		'porto_sticky',
		'porto_sticky_nav',
		'porto_sticky_nav_link',
		'porto_schedule_timeline_container',
		'porto_schedule_timeline_item',
		'porto_experience_timeline_container',
		'porto_experience_timeline_item',
		'porto_floating_menu_container',
		'porto_floating_menu_item',
		'porto_events',
		/* 4.0 shortcodes */
		'porto_icon',
		'porto_ultimate_heading',
		'porto_info_box',
		'porto_stat_counter',
		'porto_buttons',
		'porto_ultimate_content_box',
		'porto_google_map',
		'porto_icons',
		'porto_single_icon',
		'porto_countdown',
		'porto_ultimate_carousel',
		'porto_fancytext',
		'porto_modal',
		'porto_carousel_logo',
		'porto_info_list',
		'porto_info_list_item',
		'porto_interactive_banner',
		'porto_page_header',
	);

	private $woo_shortcodes = array( 'porto_recent_products', 'porto_featured_products', 'porto_sale_products', 'porto_best_selling_products', 'porto_top_rated_products', 'porto_products', 'porto_product_category', 'porto_product_attribute', 'porto_product', 'porto_product_categories', 'porto_one_page_category_products', 'porto_product_attribute_filter', 'porto_widget_woo_products', 'porto_widget_woo_top_rated_products', 'porto_widget_woo_recently_viewed', 'porto_widget_woo_recent_reviews', 'porto_widget_woo_product_tags' );

	public function __construct() {

		add_action( 'init', array( $this, 'add_tinymce_buttons' ) );
		add_action( 'plugins_loaded', array( $this, 'add_shortcodes' ) );

		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'add_editor_assets' ), 999 );
			add_filter( 'block_categories', array( $this, 'porto_blocks_categories' ), 10, 2 );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_css_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_css_js' ) );
		add_filter( 'the_content', array( $this, 'format_shortcodes' ) );
		add_filter( 'widget_text', array( $this, 'format_shortcodes' ) );
	}

	public function porto_blocks_categories( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'porto',
					'title' => __( 'Porto', 'porto-functionality' ),
					'icon'  => '',
				),
			)
		);
	}

	// load frontend css and js
	public function load_frontend_css_js() {
		$rtl_suffix = is_rtl() ? '_rtl' : '';
		wp_register_style( 'jquery-flipshow', PORTO_SHORTCODES_URL . 'assets/css/jquery.flipshow' . $rtl_suffix . '.min.css' );

		if ( ! is_404() && ! is_search() ) {
			global $post;
			if ( $post ) {
				$use_google_map = get_post_meta( $post->ID, 'porto_page_use_google_map_api', true );
				if ( '1' === $use_google_map || stripos( $post->post_content, '[porto_google_map' ) || stripos( $post->post_content, 'porto/porto-google-map' ) ) {
					wp_enqueue_script( 'googleapis' );
				}
				if ( stripos( $post->post_content, '[porto_concept ' ) ) {
					wp_enqueue_style( 'jquery-flipshow' );
				}
			}
		}

		wp_register_script( 'jquery-flipshow', PORTO_SHORTCODES_URL . 'assets/js/jquery.flipshow.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_flipshow_loader_js', PORTO_SHORTCODES_URL . 'assets/js/jquery.flipshow-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'countdown', PORTO_SHORTCODES_URL . 'assets/js/countdown.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_countdown_loader_js', PORTO_SHORTCODES_URL . 'assets/js/countdown-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'countup', PORTO_SHORTCODES_URL . 'assets/js/countup.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_countup_loader_js', PORTO_SHORTCODES_URL . 'assets/js/countup-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_ultimate_carousel_loader_js', PORTO_SHORTCODES_URL . 'assets/js/ultimate-carousel-loader.min.js', array( 'jquery', 'jquery-slick' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_map_loader_js', PORTO_SHORTCODES_URL . 'assets/js/map-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
	}

	// load css and js
	public function load_admin_css_js() {
		wp_register_style( 'porto_shortcodes_admin', PORTO_SHORTCODES_URL . 'assets/css/admin.css' );
		wp_enqueue_style( 'porto_shortcodes_admin' );
		wp_register_style( 'simple-line-icons', PORTO_SHORTCODES_URL . 'assets/css/Simple-Line-Icons/Simple-Line-Icons.css' );
		wp_enqueue_style( 'simple-line-icons' );

		global $pagenow;
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

			wp_register_style( 'bootstrap-datetimepicker-admin', PORTO_SHORTCODES_URL . 'assets/css/bootstrap-datetimepicker-admin' . ( WP_DEBUG ? '' : '.min' ) . '.css' );
			wp_enqueue_style( 'bootstrap-datetimepicker-admin' );
		}
	}

	/**
	 * Enqueue styles and scripts for gutenberg blocks
	 *
	 * @since 1.2
	 */
	public function add_editor_assets() {
		wp_enqueue_script( 'owl-carousel', PORTO_SHORTCODES_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ) );
		wp_enqueue_style( 'owl-carousel', PORTO_SHORTCODES_URL . 'assets/css/owl.carousel.min.css' );

		wp_enqueue_script( 'porto_blocks', PORTO_SHORTCODES_URL . 'assets/blocks/blocks.min.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-data', 'wp-editor' ), PORTO_SHORTCODES_VERSION, true );

		porto_google_map_script();
		wp_enqueue_script( 'googleapis' );
	}

	// Add buttons to tinyMCE
	public function add_tinymce_buttons() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_js' ) );
			add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons' ) );
		}
	}

	public function add_tinymce_js( $plugin_array ) {
		if ( get_bloginfo( 'version' ) >= 3.9 ) {
			$plugin_array['shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/shortcodes_4.js';
		} else {
			$plugin_array['shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/shortcodes.js';
		}

		$plugin_array['porto_shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/porto_shortcodes' . ( WP_DEBUG ? '' : '.min' ) . '.js';
		return $plugin_array;
	}

	public function register_tinymce_buttons( $buttons ) {
		array_push( $buttons, 'porto_shortcodes_button' );
		return $buttons;
	}

	// Add shortcodes
	public function add_shortcodes() {

		if ( function_exists( 'get_plugin_data' ) ) {
			$plugin = get_plugin_data( dirname( dirname( __FILE__ ) ) . '/porto-functionality.php' );
			define( 'PORTO_SHORTCODES_VERSION', $plugin['Version'] );
		} else {
			define( 'PORTO_SHORTCODES_VERSION', '' );
		}

		require_once( PORTO_SHORTCODES_LIB . 'functions.php' );
		foreach ( $this->shortcodes as $shortcode ) {
			require_once( PORTO_SHORTCODES_PATH . $shortcode . '.php' );
		}
		if ( class_exists( 'Woocommerce' ) ) {
			foreach ( $this->woo_shortcodes as $woo_shortcode ) {
				require_once( PORTO_SHORTCODES_WOO_PATH . $woo_shortcode . '.php' );
			}
		}
	}

	// Format shortcodes content
	public function format_shortcodes( $content ) {
		$block = join( '|', $this->shortcodes );
		// opening tag
		$content = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
		// closing tag
		$content = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)/", '[/$2]', $content );

		$woo_block = join( '|', $this->woo_shortcodes );
		// opening tag
		$content = preg_replace( "/(<p>)?\[($woo_block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
		// closing tag
		$content = preg_replace( "/(<p>)?\[\/($woo_block)](<\/p>|<br \/>)/", '[/$2]', $content );

		return $content;
	}

}

// Finally initialize code
new PortoShortcodesClass();

include_once( dirname( PORTO_SHORTCODES_PATH ) . '/lib/blocks/porto-blocks.php' );
