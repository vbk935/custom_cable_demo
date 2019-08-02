<?php
/**
 * Porto Theme Setup Wizard Class
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Theme_Setup_Wizard' ) ) {
	/**
	 * Porto_Theme_Setup_Wizard class
	 */
	class Porto_Theme_Setup_Wizard {

		protected $version = '1.2';

		protected $theme_name = '';

		protected $step = '';

		protected $steps = array();

		public $page_slug;

		protected $tgmpa_instance;

		protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

		protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

		protected $page_url;

		protected $porto_url = 'https://www.portotheme.com/wordpress/porto/';

		private static $instance = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function porto_demo_filters() {
			return array(
				'all'       => 'Show All',
				'onepage'   => 'One Page',
				'business'  => 'Business',
				'portfolio' => 'Portfolio',
				'shop'      => 'Shop',
				'classic'   => 'Classic',
				'blog'      => 'Blog',
			);
		}

		private function porto_demo_types() {
			return array(
				'classic'             => array(
					'alt'        => 'Main Demo <small>(23 VARIATIONS)</small>',
					'slider_cat' => 'classic',
					'img'        => PORTO_OPTIONS_URI . '/demos/classic_original.jpg',
					'filter'     => 'all open-classic',
					'grouped'    => true,
				),
				'shop'                => array(
					'alt'        => 'Shop Demo <small>(20 VARIATIONS)</small>',
					'slider_cat' => 'shop',
					'img'        => PORTO_OPTIONS_URI . '/demos/shop1.jpg',
					'filter'     => 'all open-shop',
					'grouped'    => true,
				),
				'blog'                => array(
					'alt'        => 'Blog Demo <small>(5 VARIATIONS)</small>',
					'slider_cat' => 'blog',
					'img'        => PORTO_OPTIONS_URI . '/demos/blog1.jpg',
					'filter'     => 'all open-blog',
					'grouped'    => true,
				),
				'classic-original'    => array(
					'alt'       => 'Main Demo',
					'img'       => PORTO_OPTIONS_URI . '/demos/classic_original.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-classic-original.zip', 'media-gallery.zip' ),
				),
				'construction'        => array(
					'alt'       => 'Construction',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_construction.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-construction.zip' ),
				),
				'hotel'               => array(
					'alt'       => 'Hotel',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_hotel.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-hotel.zip' ),
				),
				'restaurant'          => array(
					'alt'       => 'Restaurant',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_restaurant.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-restaurant.zip' ),
				),
				'law-firm'            => array(
					'alt'       => 'Law Firm',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_law_firm.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-law-firm.zip' ),
				),
				'digital-agency'      => array(
					'alt'       => 'Digital Agency',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_digital_agency.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-digital-agency.zip' ),
				),
				'medical'             => array(
					'alt'       => 'Medical',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_medical.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-medical.zip' ),
				),
				'wedding'             => array(
					'alt'       => 'Wedding',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_wedding.jpg',
					'filter'    => 'business onepage all',
					'revslider' => array( 'demo-wedding.zip' ),
				),
				'photography1'        => array(
					'alt'       => 'Photography 1',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_photography_1.jpg',
					'filter'    => 'business portfolio all',
					'revslider' => array( 'Photography1-About-us.zip', 'Photography1-Fullscreen.zip', 'Photography1-Home.zip', 'Photography1-Kenburns.zip' ),
				),
				'photography2'        => array(
					'alt'       => 'Photography 2',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_photography_2.jpg',
					'filter'    => 'business portfolio all',
					'revslider' => array( 'Photography2-aboutus.zip', 'Photography2-Fullscreen.zip', 'Photography2-Home.zip', 'Photography2-Kenburns.zip' ),
				),
				'photography3'        => array(
					'alt'       => 'Photography 3',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_photography_3.jpg',
					'filter'    => 'business portfolio all',
					'revslider' => array( 'Photography3-AboutUs.zip', 'Photography3-Fullscreen.zip', 'Photography3-Home.zip', 'Photography3-Home_2.zip', 'Photography3-Kenburns.zip' ),
				),
				'business-consulting' => array(
					'alt'       => 'Business Consulting',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_busi_cons.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'home-BC.zip' ),
				),
				'gym'                 => array(
					'alt'       => 'Gym',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_gym.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'home-gym.zip' ),
					'plugins'   => array( 'instagram-slider-widget' ),
				),
				'event'               => array(
					'alt'       => 'Event',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_event.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'home-event.zip' ),
				),
				'resume'              => array(
					'alt'    => 'Resume',
					'img'    => PORTO_OPTIONS_URI . '/demos/demo_resume.jpg',
					'filter' => 'business onepage portfolio all',
				),
				'church'              => array(
					'alt'       => 'Church',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_church.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'demo-church.zip' ),
				),
				'finance'             => array(
					'alt'       => 'Finance',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_finance.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'home-finance.zip' ),
				),
				'agency-one-page'     => array(
					'alt'       => 'Agency Onepage',
					'img'       => PORTO_OPTIONS_URI . '/demos/agency_onepage.jpg',
					'filter'    => 'business onepage portfolio all',
					'revslider' => array( 'agency-onepage.zip' ),
				),
				'app-landing'         => array(
					'alt'    => 'App Landing',
					'img'    => PORTO_OPTIONS_URI . '/demos/demo_applanding.jpg',
					'filter' => 'business onepage all',
				),
				'real-estate'         => array(
					'alt'       => 'Real Estate',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_real_estate.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'real-estate-home.zip' ),
				),
				'education'           => array(
					'alt'       => 'Education',
					'img'       => PORTO_OPTIONS_URI . '/demos/demo_education.jpg',
					'filter'    => 'business all',
					'revslider' => array( 'home_education.zip' ),
				),
				'classic-one-page'    => array(
					'alt'       => 'Classic One Page',
					'img'       => PORTO_OPTIONS_URI . '/demos/classic_one_page.jpg',
					'filter'    => 'classic onepage',
					'revslider' => array( 'home-one-page.zip' ),
				),
				'classic-color'       => array(
					'alt'       => 'Classic Color',
					'img'       => PORTO_OPTIONS_URI . '/demos/classic_color.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-classic-color.zip', 'media-gallery.zip' ),
				),
				'classic-light'       => array(
					'alt'       => 'Classic Light',
					'img'       => PORTO_OPTIONS_URI . '/demos/classic_light.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-classic-light.zip', 'media-gallery.zip' ),
				),
				'classic-video'       => array(
					'alt'       => 'Classic Video',
					'img'       => PORTO_OPTIONS_URI . '/demos/classic_video.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-classic-video.zip', 'media-gallery.zip' ),
				),
				'classic-video-light' => array(
					'alt'       => 'Classic Video Light',
					'img'       => PORTO_OPTIONS_URI . '/demos/classic_video_light.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-classic-video-light.zip', 'media-gallery.zip' ),
				),
				'corporate1'          => array(
					'alt'       => 'Corporate 1',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_1.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate1.zip', 'media-gallery.zip' ),
				),
				'corporate2'          => array(
					'alt'       => 'Corporate 2',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_2.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate2.zip', 'media-gallery.zip' ),
				),
				'corporate3'          => array(
					'alt'       => 'Corporate 3',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_3.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate3.zip', 'media-gallery.zip' ),
				),
				'corporate4'          => array(
					'alt'       => 'Corporate 4',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_4.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate4.zip', 'media-gallery.zip' ),
				),
				'corporate5'          => array(
					'alt'       => 'Corporate 5',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_5.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate5.zip', 'media-gallery.zip' ),
				),
				'corporate6'          => array(
					'alt'       => 'Corporate 6',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_6.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate6.zip', 'media-gallery.zip' ),
				),
				'corporate7'          => array(
					'alt'       => 'Corporate 7',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_7.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate7.zip', 'media-gallery.zip' ),
				),
				'corporate8'          => array(
					'alt'       => 'Corporate 8',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_8.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate8.zip', 'media-gallery.zip' ),
				),
				'corporate9'          => array(
					'alt'       => 'Corporate 9',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_9.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate9.zip', 'media-gallery.zip' ),
				),
				'corporate10'         => array(
					'alt'       => 'Corporate 10',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_10.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate10.zip', 'media-gallery.zip' ),
				),
				'corporate11'         => array(
					'alt'       => 'Corporate 11',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_11.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate11.zip', 'media-gallery.zip' ),
				),
				'corporate12'         => array(
					'alt'       => 'Corporate 12',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_12.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate12.zip', 'media-gallery.zip' ),
				),
				'corporate13'         => array(
					'alt'       => 'Corporate 13',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_13.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate13.zip', 'media-gallery.zip' ),
				),
				'corporate14'         => array(
					'alt'       => 'Corporate 14',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_14.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate14.zip', 'media-gallery.zip' ),
				),
				'corporate-hosting'   => array(
					'alt'       => 'Corporate Hosting',
					'img'       => PORTO_OPTIONS_URI . '/demos/corporate_hosting.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-corporate-hosting.zip', 'media-gallery.zip' ),
				),
				'shop1'               => array(
					'alt'     => 'Shop 1',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop1.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop2'               => array(
					'alt'     => 'Shop 2',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop2.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop3'               => array(
					'alt'     => 'Shop 3',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop3.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop4'               => array(
					'alt'     => 'Shop 4',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop4.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop5'               => array(
					'alt'     => 'Shop 5',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop5.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop6'               => array(
					'alt'     => 'Shop 6',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop6.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop7'               => array(
					'alt'     => 'Shop 7',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop7.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop8'               => array(
					'alt'     => 'Shop 8',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop8.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop9'               => array(
					'alt'     => 'Shop 9',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop9.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop10'              => array(
					'alt'     => 'Shop 10',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop10.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop11'              => array(
					'alt'     => 'Shop 11',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop11.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop12'              => array(
					'alt'     => 'Shop 12',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop12.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop13'              => array(
					'alt'     => 'Shop 13',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop13.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop14'              => array(
					'alt'     => 'Shop 14',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop14.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop15'              => array(
					'alt'     => 'Shop 15',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop15.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop16'              => array(
					'alt'     => 'Shop 16',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop16.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop17'              => array(
					'alt'     => 'Shop 17',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop17.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop18'              => array(
					'alt'     => 'Shop 18',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop18.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop19'              => array(
					'alt'     => 'Shop 19',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop19.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce' ),
				),
				'shop20'              => array(
					'alt'     => 'Shop 20',
					'img'     => PORTO_OPTIONS_URI . '/demos/shop20.jpg',
					'filter'  => 'shop',
					'plugins' => array( 'woocommerce', 'instagram-slider-widget' ),
				),
				'dark'                => array(
					'alt'       => 'Dark Original',
					'img'       => PORTO_OPTIONS_URI . '/demos/dark_original.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-dark.zip', 'media-gallery.zip' ),
				),
				'rtl'                 => array(
					'alt'       => 'RTL Original',
					'img'       => PORTO_OPTIONS_URI . '/demos/rtl_original.jpg',
					'filter'    => 'classic',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'home-rtl.zip', 'media-gallery.zip' ),
				),
				'blog1'               => array(
					'alt'       => 'Blog 1',
					'img'       => PORTO_OPTIONS_URI . '/demos/blog1.jpg',
					'filter'    => 'blog all',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'media-gallery.zip' ),
				),
				'blog2'               => array(
					'alt'       => 'Blog 2',
					'img'       => PORTO_OPTIONS_URI . '/demos/blog2.jpg',
					'filter'    => 'blog all',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'media-gallery.zip' ),
				),
				'blog3'               => array(
					'alt'       => 'Blog 3',
					'img'       => PORTO_OPTIONS_URI . '/demos/blog3.jpg',
					'filter'    => 'blog all',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'media-gallery.zip' ),
				),
				'blog4'               => array(
					'alt'       => 'Blog 4',
					'img'       => PORTO_OPTIONS_URI . '/demos/blog4.jpg',
					'filter'    => 'blog all',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'media-gallery.zip' ),
				),
				'blog5'               => array(
					'alt'       => 'Blog 5',
					'img'       => PORTO_OPTIONS_URI . '/demos/blog5.jpg',
					'filter'    => 'blog all',
					'revslider' => array( 'full-width-slider.zip', 'full-width-video.zip', 'media-gallery.zip' ),
				),
			);
		}

		public function porto_extra_demos() {
			return array( 'digital-agency', 'law-firm', 'construction', 'restaurant', 'hotel', 'medical', 'wedding', 'photography1', 'photography2', 'photography3', 'business-consulting', 'gym', 'event', 'resume', 'church', 'finance', 'agency-onepage', 'app-landing' );
		}

		public function __construct() {
			$this->init_globals();
			$this->init_actions();
		}

		public function get_header_logo_width() {
			return '200px';
		}

		public function init_globals() {
			$current_theme    = wp_get_theme();
			$this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
			$this->page_slug  = 'porto-setup-wizard';
			$this->page_url   = 'admin.php?page=' . $this->page_slug;
		}

		public function init_actions() {
			if ( apply_filters( $this->theme_name . '_enable_setup_wizard', true ) && current_user_can( 'manage_options' ) ) {

				if ( ! is_child_theme() ) {
					add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );
				}

				if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
					add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
					add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
				}

				add_action( 'admin_menu', array( $this, 'admin_menus' ) );
				add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );

				add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
				add_action( 'admin_init', array( $this, 'setup_wizard' ), 30 );
				add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
				add_action( 'wp_ajax_porto_setup_wizard_plugins', array( $this, 'ajax_plugins' ) );

				// importer actions
				add_action( 'wp_ajax_porto_reset_menus', array( $this, 'reset_menus' ) );
				add_action( 'wp_ajax_porto_reset_widgets', array( $this, 'reset_widgets' ) );
				add_action( 'wp_ajax_porto_import_dummy', array( $this, 'import_dummy' ) );
				add_action( 'wp_ajax_porto_import_dummy_step_by_step', array( $this, 'import_dummy_step_by_step' ) );
				add_action( 'wp_ajax_porto_import_revsliders', array( $this, 'import_revsliders' ) );
				add_action( 'wp_ajax_porto_import_widgets', array( $this, 'import_widgets' ) );
				add_action( 'wp_ajax_porto_import_icons', array( $this, 'import_icons' ) );
				add_action( 'wp_ajax_porto_import_options', array( $this, 'import_options' ) );
				add_action( 'wp_ajax_porto_delete_tmp_dir', array( $this, 'delete_tmp_dir' ) );
				add_action( 'wp_ajax_porto_download_demo_file', array( $this, 'download_demo_file' ) );

				add_filter( 'wp_import_existing_post', array( $this, 'import_override_contents' ), 10, 2 );
				add_action( 'import_start', array( $this, 'import_dummy_start' ) );
				add_action( 'import_end', array( $this, 'import_dummy_end' ) );

				if ( isset( $_GET['page'] ) && $this->page_slug === $_GET['page'] ) {
					add_filter( 'wp_title', array( $this, 'page_title' ) );
				}
			}

			add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );
		}

		public function page_title() {
			return esc_html__( 'Theme &rsaquo; Setup Wizard', 'porto' );
		}

		public function upgrader_post_install( $return, $theme ) {
			if ( is_wp_error( $return ) ) {
				return $return;
			}
			if ( get_stylesheet() != $theme ) {
				return $return;
			}
			update_option( 'porto_setup_complete', false );

			return $return;
		}

		public function tgmpa_load( $status ) {
			return is_admin() || current_user_can( 'install_themes' );
		}

		public function switch_theme() {
			set_transient( '_' . $this->theme_name . '_activation_redirect', 1 );
		}

		public function admin_redirects() {
			ob_start();

			if ( ! get_transient( '_' . $this->theme_name . '_activation_redirect' ) || get_option( 'porto_setup_complete', false ) ) {
				return;
			}
			delete_transient( '_' . $this->theme_name . '_activation_redirect' );
			wp_safe_redirect( admin_url( $this->page_url ) );
			exit;
		}

		public function get_tgmpa_instanse() {
			$this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		}

		public function set_tgmpa_url() {

			$this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
			$this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );

			$tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && 'themes.php' !== $this->tgmpa_instance->parent_slug ) ? 'admin.php' : 'themes.php';

			$this->tgmpa_url = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );

		}

		public function admin_menus() {
			add_submenu_page( 'porto', esc_html__( 'Setup Wizard', 'porto' ), esc_html__( 'Setup Wizard', 'porto' ), 'manage_options', $this->page_slug, array( $this, $this->page_slug ) );
		}

		public function init_wizard_steps() {

			$this->steps = array(
				'introduction' => array(
					'name'    => esc_html__( 'Welcome', 'porto' ),
					'view'    => array( $this, 'porto_setup_wizard_welcome' ),
					'handler' => array( $this, 'porto_setup_wizard_welcome_save' ),
				),
			);

			$this->steps['updates'] = array(
				'name'    => esc_html__( 'Activate', 'porto' ),
				'view'    => array( $this, 'porto_setup_wizard_updates' ),
				'handler' => '',
			);

			$this->steps['status'] = array(
				'name'    => esc_html__( 'Status', 'porto' ),
				'view'    => array( $this, 'porto_setup_wizard_status' ),
				'handler' => array( $this, 'porto_setup_wizard_status_save' ),
			);

			$this->steps['customize'] = array(
				'name'    => esc_html__( 'Child Theme', 'porto' ),
				'view'    => array( $this, 'porto_setup_wizard_customize' ),
				'handler' => '',
			);

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				$this->steps['default_plugins'] = array(
					'name'    => esc_html__( 'Plugins', 'porto' ),
					'view'    => array( $this, 'porto_setup_wizard_default_plugins' ),
					'handler' => '',
				);
			}
			$this->steps['demo_content'] = array(
				'name'    => esc_html__( 'Demo Content', 'porto' ),
				'view'    => array( $this, 'porto_setup_wizard_demo_content' ),
				'handler' => array( $this, 'porto_setup_wizard_demo_content_save' ),
			);
			$this->steps['help_support'] = array(
				'name'    => esc_html__( 'Support', 'porto' ),
				'view'    => array( $this, 'porto_setup_wizard_help_support' ),
				'handler' => '',
			);
			$this->steps['next_steps']   = array(
				'name'    => esc_html__( 'Ready!', 'porto' ),
				'view'    => array( $this, 'porto_setup_wizard_ready' ),
				'handler' => '',
			);

			$this->steps = apply_filters( $this->theme_name . '_theme_setup_wizard_steps', $this->steps );
		}

		/**
		 * Display the setup wizard
		 */
		public function setup_wizard() {
			if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
				return;
			}
			ob_end_clean();

			$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

			wp_register_script( 'jquery-blockui', PORTO_URI . '/inc/admin/setup_wizard/assets/js/jquery.blockUI.js', array( 'jquery' ), '2.70', true );
			wp_register_script( 'isotope', PORTO_JS . '/libs/isotope.pkgd.min.js', array( 'jquery' ), '3.0.1', true );
			wp_register_script( 'jquery-magnific-popup', PORTO_JS . '/libs/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
			wp_register_script( 'porto-admin', PORTO_JS . '/admin/admin.min.js', array( 'jquery', 'jquery-magnific-popup' ), $this->version, true );
			wp_register_script( 'porto-setup', PORTO_URI . '/inc/admin/setup_wizard/assets/js/setup-wizard.js', array( 'jquery', 'isotope', 'porto-admin', 'jquery-blockui' ), $this->version );
			wp_localize_script(
				'porto-setup',
				'porto_setup_wizard_params',
				array(
					'tgm_plugin_nonce' => array(
						'update'  => wp_create_nonce( 'tgmpa-update' ),
						'install' => wp_create_nonce( 'tgmpa-install' ),
					),
					'tgm_bulk_url'     => esc_url( admin_url( $this->tgmpa_url ) ),
					'wpnonce'          => wp_create_nonce( 'porto_setup_wizard_nonce' ),
				)
			);

			wp_enqueue_style( 'jquery-magnific-popup', PORTO_CSS . '/magnific-popup.min.css', false, $this->version, 'all' );
			wp_enqueue_style( 'porto-setup', PORTO_URI . '/inc/admin/setup_wizard/assets/css/style.css', array( 'wp-admin', 'dashicons', 'install', 'jquery-magnific-popup' ), $this->version );

			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_media();
			wp_enqueue_script( 'media' );

			ob_start();
			$this->setup_wizard_header();
			$this->setup_wizard_steps();
			$show_content = true;
			echo '<div class="porto-setup-content">';
			if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
				$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
			}
			if ( $show_content ) {
				$this->setup_wizard_content();
			}
			echo '</div>';
			$this->setup_wizard_footer();
			exit;
		}

		public function get_step_link( $step ) {
			return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . $this->page_slug ) );
		}
		public function get_next_step_link() {
			$keys = array_keys( $this->steps );
			return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
		}

		/**
		 * Setup Wizard Header
		 */
		public function setup_wizard_header() {
			?>
			<!DOCTYPE html>
			<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
			<head>
				<meta name="viewport" content="width=device-width" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title><?php wp_title(); ?></title>
				<script type="text/javascript">
					var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php', 'relative' ) ); ?>';
				</script>
				<?php wp_print_scripts( 'porto-setup' ); ?>
				<?php do_action( 'admin_print_styles' ); ?>
				<?php do_action( 'admin_print_scripts' ); ?>
			</head>
			<body class="porto-setup wp-core-ui">
			<h1 id="porto-logo">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>" class="overlay-logo">
					<img class="img-responsive" src="<?php echo PORTO_URI; ?>/images/logo/logo.png" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" width="111" height="54" />
				</a>
			</h1>
			<?php
		}

		/**
		 * Setup Wizard Footer
		 */
		public function setup_wizard_footer() {
			?>
			<a class="wc-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to the WordPress Dashboard', 'porto' ); ?></a>
			<?php
			@do_action( 'admin_footer' );
			do_action( 'admin_print_footer_scripts' );
			?>
			</body>
			</html>
			<?php
		}

		/**
		 * Output the steps
		 */
		public function setup_wizard_steps() {
			$ouput_steps = $this->steps;
			array_shift( $ouput_steps );
			?>
			<ol class="porto-setup-steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<?php
				$show_link        = true;
				$li_class_escaped = '';
				if ( $step_key === $this->step ) {
					$li_class_escaped = 'active';
				} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
					$li_class_escaped = 'done';
				}
				if ( $step_key === $this->step || 'next_steps' == $step_key ) {
					$show_link = false;
				}
				?>
				<li class="<?php echo esc_attr( $li_class_escaped ); ?>">
				<?php
				if ( $show_link ) {
					?>
						<a href="<?php echo esc_url( $this->get_step_link( $step_key ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
						<?php
				} else {
					echo esc_html( $step['name'] );
				}
				?>
					</li>
			<?php endforeach; ?>
			</ol>
			<?php
		}

		/**
		 * Output the content for the current step
		 */
		public function setup_wizard_content() {
			isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
		}

		/**
		 * Welcome step
		 */
		public function porto_setup_wizard_welcome() {
			if ( get_option( 'porto_setup_complete', false ) ) {
				?>
				<?php /* translators: %s: Theme name */ ?>
				<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.', 'porto' ), wp_get_theme() ); ?></h1>
				<p class="lead success"><?php esc_html_e( 'It looks like you already have setup Porto.', 'porto' ); ?></p>

				<p class="porto-setup-actions step">
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-next button-large"><?php esc_html_e( 'Run Setup Wizard Again', 'porto' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=porto' ) ); ?>" class="button button-large"><?php esc_html_e( 'Exit to Porto Panel', 'porto' ); ?></a>
				</p>
				<?php
			} else {
				?>
				<?php /* translators: %s: Theme name */ ?>
				<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.', 'porto' ), wp_get_theme() ); ?></h1>
				<?php /* translators: %s: Theme name */ ?>
				<p class="lead"><?php printf( esc_html__( 'Thank you for choosing the %s theme. This quick setup wizard will help you configure your new website. This wizard will install the required WordPress plugins, demo content, logo, etc.', 'porto' ), wp_get_theme() ); ?></p>
				<p><?php esc_html_e( "No time right now? If you don't want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!", 'porto' ); ?></p>
				<p class="porto-setup-actions step">
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( "Let's Go!", 'porto' ); ?></a>
					<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>" class="button button-large"><?php esc_html_e( 'Not right now', 'porto' ); ?></a>
				</p>
				<?php
			}
		}

		public function porto_setup_wizard_welcome_save() {

			check_admin_referer( 'porto-setup' );
			return false;
		}

		public function porto_setup_wizard_status() {
			?>
			<h1><?php esc_html_e( 'System Status', 'porto' ); ?></h1>
			<?php include_once PORTO_ADMIN . '/admin_pages/mini-status.php'; ?>
			<p class="porto-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
			</p>
			<?php
		}

		public function porto_setup_wizard_status_save() {

			check_admin_referer( 'porto-setup' );
		}

		private function _wp_get_attachment_id_by_post_name( $post_name ) {
			global $wpdb;
			$str   = $post_name;
			$posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = %s", $str ), OBJECT );
			if ( $posts ) {
				return $posts[0]->ID;
			}
		}

		private function _get_plugins() {
			$instance         = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
			$plugin_func_name = 'is_plugin_active';
			$plugins          = array(
				'all'      => array(), // Meaning: all plugins which still have open actions.
				'install'  => array(),
				'update'   => array(),
				'activate' => array(),
			);

			foreach ( $instance->plugins as $slug => $plugin ) {
				if ( $instance->$plugin_func_name( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) {
					continue;
				} else {
					$plugins['all'][ $slug ] = $plugin;

					if ( ! $instance->is_plugin_installed( $slug ) ) {
						$plugins['install'][ $slug ] = $plugin;
					} else {
						if ( false !== $instance->does_plugin_have_update( $slug ) ) {
							$plugins['update'][ $slug ] = $plugin;
						}

						if ( $instance->can_plugin_activate( $slug ) ) {
							$plugins['activate'][ $slug ] = $plugin;
						}
					}
				}
			}
			return $plugins;
		}

		/**
		 * Page setup
		 */
		public function porto_setup_wizard_default_plugins() {

			tgmpa_load_bulk_installer();
			if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
				die( 'Failed to find TGM' );
			}
			$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'porto-setup' );
			$plugins = $this->_get_plugins();

			$method = '';
			$fields = array_keys( $_POST );

			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}

			?>
			<h1><?php esc_html_e( 'Default Plugins', 'porto' ); ?></h1>
			<form method="post">

				<?php
				$plugins = $this->_get_plugins();
				if ( count( $plugins['all'] ) ) {
					?>
					<p class="lead"><?php esc_html_e( 'This will install the default plugins which is used in Porto.', 'porto' ); ?></p>
					<p><?php esc_html_e( 'Please check the plugins to install.', 'porto' ); ?></p>
					<ul class="porto-setup-wizard-plugins">
						<?php
						foreach ( $plugins['all'] as $slug => $plugin ) {
							if ( isset( $plugin['visibility'] ) && 'speed_wizard' == $plugin['visibility'] ) {
								continue;
							}
							?>
							<?php if ( 'wysija-newsletters' === $plugin['slug'] ) : ?>
								<li class="separator">
									<a href="#" class="button-load-plugins"><?php esc_html_e( 'Load more plugins fully compatible with Porto', 'porto' ); ?></a>
								</li>
							<?php endif; ?>
							<li data-slug="<?php echo esc_attr( $slug ); ?>"<?php echo isset( $plugin['visibility'] ) && 'hidden' === $plugin['visibility'] ? ' class="hidden"' : ''; ?>>
								<label class="checkbox checkbox-inline">
									<input type="checkbox" name="setup-plugin"<?php echo ! $plugin['required'] ? '' : ' checked="checked"'; ?>>
									<?php echo esc_html( $plugin['name'] ); ?>
									<span>
									<?php
										$key = '';
									if ( isset( $plugins['install'][ $slug ] ) ) {
										$key = esc_html__( 'Installation', 'porto' );
									} elseif ( isset( $plugins['update'][ $slug ] ) ) {
										$key = esc_html__( 'Update', 'porto' );
									} elseif ( isset( $plugins['activate'][ $slug ] ) ) {
										$key = esc_html__( 'Activation', 'porto' );
									}
									if ( $key ) {
										if ( $plugin['required'] ) {
											/* translators: %s: Plugin name */
											printf( esc_html__( '%s required', 'porto' ), $key );
										} else {
											/* translators: %s: Plugin name */
											printf( esc_html__( '%s recommended for certain demos', 'porto' ), $key );
										}
									}
									?>
									</span>
								</label>
								<div class="spinner"></div>
							</li>
							<?php if ( 'porto-functionality' === $plugin['slug'] ) : ?>
								<li class="separator"></li>
							<?php endif; ?>
						<?php } ?>
					</ul>
					<?php
				} else {
					echo '<p class="lead">' . esc_html__( 'Good news! All plugins are already installed and up to date. Please continue.', 'porto' ) . '</p>';
				}
				?>

				<p class="porto-setup-actions step">
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
					<?php wp_nonce_field( 'porto-setup' ); ?>
				</p>
			</form>
			<?php
		}


		public function ajax_plugins() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__(
							'No Slug Found',
							'porto'
						),
					)
				);
			}
			$json = array();
			// send back some json we use to hit up TGM
			$plugins = $this->_get_plugins();
			// what are we doing with this plugin?
			foreach ( $plugins['activate'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-activate',
						'action2'       => -1,
						'message'       => esc_html__( 'Activating Plugin', 'porto' ),
					);
					break;
				}
			}
			foreach ( $plugins['update'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-update',
						'action2'       => -1,
						'message'       => esc_html__( 'Updating Plugin', 'porto' ),
					);
					break;
				}
			}
			foreach ( $plugins['install'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-install',
						'action2'       => -1,
						'message'       => esc_html__( 'Installing Plugin', 'porto' ),
					);
					break;
				}
			}

			if ( $json ) {
				$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
				wp_send_json( $json );
			} else {
				wp_send_json(
					array(
						'done'    => 1,
						'message' => esc_html__(
							'Success',
							'porto'
						),
					)
				);
			}
			exit;
		}

		private function _make_child_theme( $new_theme_title ) {

			$parent_theme_title    = 'Porto';
			$parent_theme_template = 'porto';
			$parent_theme_name     = get_stylesheet();
			$parent_theme_dir      = get_stylesheet_directory();

			$new_theme_name = sanitize_title( $new_theme_title );
			$theme_root     = get_theme_root();

			$new_theme_path = $theme_root . '/' . $new_theme_name;
			if ( ! file_exists( $new_theme_path ) ) {
				wp_mkdir_p( $new_theme_path );

				$plugin_folder = get_template_directory() . '/inc/admin/setup_wizard/porto-child/';

				ob_start();
				require $plugin_folder . 'style.css.php';
				$css = ob_get_clean();

				// filesystem
				global $wp_filesystem;
				// Initialize the WordPress filesystem, no more using file_put_contents function
				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}

				if ( ! $wp_filesystem->put_contents( $new_theme_path . '/style.css', $css, FS_CHMOD_FILE ) ) {
					echo '<p class="lead success">Directory permission required for /wp-content/themes.</p>';
					return;
				}

				// Copy functions.php
				copy( $plugin_folder . 'functions.php', $new_theme_path . '/functions.php' );

				// Copy screenshot
				copy( $plugin_folder . 'screenshot.png', $new_theme_path . '/screenshot.png' );

				// Copy style rtl
				copy( $plugin_folder . 'style_rtl.css', $new_theme_path . '/style_rtl.css' );

				// Make child theme an allowed theme (network enable theme)
				$allowed_themes                    = get_site_option( 'allowedthemes' );
				$allowed_themes[ $new_theme_name ] = true;
				update_site_option( 'allowedthemes', $allowed_themes );
			}

			// Switch to theme
			if ( $parent_theme_template !== $new_theme_name ) {
				echo '<p class="lead success">Child Theme <strong>' . esc_html( $new_theme_title ) . '</strong> created and activated!<br />Folder is located in wp-content/themes/<strong>' . esc_html( $new_theme_name ) . '</strong></p>';
				switch_theme( $new_theme_name, $new_theme_name );
			}
		}

		/**
		 * Logo & Design
		 */
		public function porto_setup_wizard_demo_content() {
			$url    = wp_nonce_url( add_query_arg( array( 'demo_content' => 'go' ) ), 'porto-setup' );
			$method = '';
			$fields = array_keys( $_POST );
			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}
			?>
			<h1><?php esc_html_e( 'Demo Install', 'porto' ); ?></h1>
			<h3><?php esc_html_e( 'Upload Logo', 'porto' ); ?></h3>
			<form method="post" class="porto-install-demos">
				<input type="hidden" id="current_site_url" value="<?php echo esc_url( site_url() ); ?>">
				<table>
					<tr>
						<td>
							<div id="current-logo">
							<?php
								global $porto_settings;
							if ( ! isset( $porto_settings['logo-type'] ) || ! $porto_settings['logo-type'] ) {
								$image_url  = $porto_settings['logo'] && $porto_settings['logo']['url'] ? $porto_settings['logo']['url'] : PORTO_URI . '/images/logo/logo.png';
								$logo_width = $porto_settings['logo-overlay-width'] ? $porto_settings['logo-overlay-width'] : 250;
								if ( $image_url ) {
									$image = '<img class="site-logo" src="%s" alt="%s" style="max-width:%spx; height:auto" />';
									printf(
										$image,
										$image_url,
										get_bloginfo( 'name' ),
										$logo_width
									);
								}
							} else {
								?>
								<input type="text" name="new_logo_text" id="new_logo_text" value="<?php echo esc_attr( $porto_settings['logo-text'] ); ?>" style="padding: 7px 10px; width: 300px;">
								<?php
							}
							?>
							</div>
						</td>
						<td>
							<?php if ( ! isset( $porto_settings['logo-type'] ) || ! $porto_settings['logo-type'] ) : ?>
							<a href="#" class="button button-upload"><?php esc_html_e( 'Upload New Logo', 'porto' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				</table>
				<p>You can upload and customize this in Theme Options later.</p>

				<hr/>

				<h3 style="margin-top: 30px;"><?php esc_html_e( 'Select Demo', 'porto' ); ?></h3>
				<?php
					$demos               = $this->porto_demo_types();
					$demo_filters        = $this->porto_demo_filters();
					$memory_limit        = wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) );
					$porto_plugins_obj   = new PortoTGMPlugins();
					$required_plugins    = $porto_plugins_obj->get_plugins_list();
					$uninstalled_plugins = array();
					$all_plugins         = array();
				foreach ( $required_plugins as $plugin ) {
					if ( $plugin['required'] && is_plugin_inactive( $plugin['url'] ) ) {
						$uninstalled_plugins[ $plugin['slug'] ] = $plugin;
					}
					$all_plugins[ $plugin['slug'] ] = $plugin;
				}
					$time_limit    = ini_get( 'max_execution_time' );
					$server_status = $memory_limit >= 268435456 && ( $time_limit >= 600 || 0 == $time_limit );
				?>

				<div class="porto-install-demo mfp-hide">
					<div class="theme-img"></div>
					<div id="import-status"></div>
					<div id="porto-install-options">
						<h3>
							<span class="theme-name"></span> <?php esc_html_e( 'Demo', 'porto' ); ?>
							<?php if ( Porto()->is_registered() ) : ?>
								<span class="more-options"><?php esc_html_e( 'Details', 'porto' ); ?></span>
							<?php endif; ?>
						</h3>
						<div class="porto-install-section" style="margin-bottom: 10px;">
							<?php if ( Porto()->is_registered() ) : ?>
								<div class="porto-install-options-section" style="display: none;">
									<label for="porto-import-options"><input type="checkbox" id="porto-import-options" value="1" checked="checked"/> <?php esc_html_e( 'Import theme options', 'porto' ); ?></label>
									<input type="hidden" id="porto-install-demo-type" value="landing"/>
									<label for="porto-reset-menus"><input type="checkbox" id="porto-reset-menus" value="1" checked="checked"/> <?php esc_html_e( 'Reset menus', 'porto' ); ?></label>
									<label for="porto-reset-widgets"><input type="checkbox" id="porto-reset-widgets" value="1" checked="checked"/> <?php esc_html_e( 'Reset widgets', 'porto' ); ?></label>
									<label for="porto-import-dummy"><input type="checkbox" id="porto-import-dummy" value="1" checked="checked"/> <?php esc_html_e( 'Import dummy content', 'porto' ); ?></label>
									<label for="porto-import-widgets"><input type="checkbox" id="porto-import-widgets" value="1" checked="checked"/> <?php esc_html_e( 'Import widgets', 'porto' ); ?></label>
									<label for="porto-import-icons"><input type="checkbox" id="porto-import-icons" value="1" checked="checked"/> <?php esc_html_e( 'Import icons for ultimate addons plugin', 'porto' ); ?></label>
									<label for="porto-import-shortcodes"><input type="checkbox" id="porto-import-shortcodes" value="1"/> <?php esc_html_e( 'Import Element pages', 'porto' ); ?></label>
									<label for="porto-override-contents"><input type="checkbox" id="porto-override-contents" value="1" checked="checked" /> <?php esc_html_e( 'Override existing contents', 'porto' ); ?></label>
								</div>
								<p><?php esc_html_e( 'Do you want to install demo? It can also take a minute to complete.', 'porto' ); ?></p>
								<button class="btn <?php echo ! $server_status ? 'btn-quaternary' : 'btn-primary'; ?> porto-import-yes"<?php echo ! $server_status ? ' disabled="disabled"' : ''; ?>><?php esc_html_e( 'Standard Import', 'porto' ); ?></button>
								<?php if ( ! $server_status ) : ?>
								<p><?php esc_html_e( 'Your server performance does not satisfy Porto demo importer engine\'s requirement. We recommend you to use alternative method to perform demo import without any issues but it may take much time than standard import.', 'porto' ); ?></p>
								<?php else : ?>
								<p><?php esc_html_e( 'If you have any issues with standard import, please use Alternative mode. But it may take much time than standard import.', 'porto' ); ?></p>
								<?php endif; ?>
								<button class="btn btn-primary porto-import-yes alternative"><?php esc_html_e( 'Alternative Mode', 'porto' ); ?></button>
							<?php endif; ?>
						</div>
						<?php if ( ! Porto()->is_registered() ) : ?>
							<a href="<?php echo esc_url( $this->get_step_link( 'updates' ) ); ?>" class="btn btn-quaternary" style="display: inline-block; box-sizing: border-box; text-decoration: none; text-align: center; margin-bottom: 20px;"><?php esc_html_e( 'Activate Theme', 'porto' ); ?></a>
						<?php endif; ?>
						<a href="#" class="live-site" target="_blank"><?php esc_html_e( 'Live Preview', 'porto' ); ?></a>
					</div>
				</div>
				<div class="demo-sort-filters">
					<ul data-sort-id="theme-install-demos" class="sort-source">
					<?php foreach ( $demo_filters as $filter_class => $filter_name ) : ?>
						<li data-filter-by="<?php echo esc_attr( $filter_class ); ?>" data-active="<?php echo ( 'all' == $filter_class ? 'true' : 'false' ); ?>"><a href="#"><?php echo esc_html( $filter_name ); ?></a></li>
					<?php endforeach; ?>
					</ul>
					<div class="clear"></div>
				</div>
				<div id="theme-install-demos">
					<?php foreach ( $demos as $demo => $demo_details ) : ?>
						<?php
							$uninstalled_demo_plugins = $uninstalled_plugins;
						if ( isset( $all_plugins['revslider'] ) && isset( $demo_details['revslider'] ) && ! empty( $demo_details['revslider'] ) && is_plugin_inactive( 'revslider/revslider.php' ) ) {
							$uninstalled_demo_plugins['revslider'] = $all_plugins['revslider'];
						}
						if ( ! empty( $demo_details['plugins'] ) ) {
							foreach ( $demo_details['plugins'] as $plugin ) {
								if ( is_plugin_inactive( $all_plugins[ $plugin ]['url'] ) ) {
									$uninstalled_demo_plugins[ $plugin ] = $all_plugins[ $plugin ];
								}
							}
						}
						?>
						<div class="theme <?php echo esc_attr( $demo_details['filter'] ); ?>">
							<div class="theme-wrapper">
								<div class="theme-screenshot">
									<img src="<?php echo esc_url( $demo_details['img'] ); ?>" />
								</div>
								<h3 class="theme-name" id="<?php echo esc_attr( $demo ); ?>" data-live-url="<?php echo esc_url( 'landing' != $demo ? $this->porto_url . $demo : $this->porto_url ); ?>"><?php echo porto_filter_output( $demo_details['alt'] ); ?></h3>
								<?php if ( ! empty( $uninstalled_demo_plugins ) ) : ?>
									<ul class="plugins-used">
										<?php foreach ( $uninstalled_demo_plugins as $plugin ) : ?>
											<li>
												<div class="thumb">
													<img src="<?php echo esc_url( $plugin['image_url'] ); ?>" />
												</div>
												<div>
													<h5><?php echo esc_html( $plugin['name'] ); ?></h5>
													<?php if ( 'revslider' == $plugin['slug'] ) : ?>
														<?php /* translators: $1 and $2 opening and closing underline tags respectively */ ?>
														<p><?php printf( esc_html__( 'Demo sliders %1$swill not%2$s be installed if Revolution Slider is not active.', 'porto' ), '<u>', '</u>' ); ?></p>
													<?php endif; ?>
												</div>
											</li>
										<?php endforeach; ?>
										<li>
											<?php /* translators: %s: Plugins step link */ ?>
											<p><?php printf( esc_html__( 'Please go to %1$sPlugins step%2$s and install required plugins.', 'porto' ), '<a href="' . esc_url( $this->get_step_link( 'default_plugins' ) ) . '">', '</a>' ); ?></p>
										</li>
									</ul>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<br />
				<p><?php esc_html_e( 'Installing a demo provides pages, posts, menus, images, theme options, widgets and more.', 'porto' ); ?>
				<br /><strong><?php esc_html_e( 'IMPORTANT: The included plugins need to be installed and activated before you install a demo.', 'porto' ); ?> </strong>
				<?php /* translators: $1: opening A tag which has link to the plugins step $2: closing A tag */ ?>
				<br /><?php printf( esc_html__( 'Please check the %1$sStatus%2$s step to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red.', 'porto' ), '<a href="' . esc_url( $this->get_step_link( 'status' ) ) . '">', '</a>' ); ?></p>
				<p class="lead"><?php esc_html_e( 'If you want to install demo later or don\'t want it, you can skip this step', 'porto' ); ?></p>

				<input type="hidden" name="new_logo_id" id="new_logo_id" value="">

				<p class="porto-setup-actions step">
					<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'porto' ); ?>" name="save_step" />
					<?php wp_nonce_field( 'porto-setup' ); ?>
				</p>
			</form>
			<?php
		}

		/**
		 * Save logo & design options
		 */
		public function porto_setup_wizard_demo_content_save() {
			check_admin_referer( 'porto-setup' );

			$new_logo_id   = (int) $_POST['new_logo_id'];
			$new_logo_text = sanitize_text_field( $_POST['new_logo_text'] );

			if ( ( $new_logo_id || $new_logo_text ) && class_exists( 'ReduxFrameworkInstances' ) ) {
				$redux = ReduxFrameworkInstances::get_instance( 'porto_settings' );
				global $porto_settings;
				if ( $new_logo_id ) {
					$attr = wp_get_attachment_image_src( $new_logo_id, 'full' );
					if ( $attr && ! empty( $attr[1] ) && ! empty( $attr[2] ) ) {
						$porto_settings['logo']['url']    = $attr[0];
						$porto_settings['logo']['id']     = $new_logo_id;
						$porto_settings['logo']['width']  = $attr[1];
						$porto_settings['logo']['height'] = $attr[2];
					}
				}
				if ( $new_logo_text ) {
					$porto_settings['logo-text'] = $new_logo_text;
				}
				$redux->set_options( $porto_settings );
			}

			wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
			exit;
		}

		/**
		 * Payments Step
		 */
		public function porto_setup_wizard_updates() {
			?>
			<h1><?php esc_html_e( 'Activate Theme', 'porto' ); ?></h1>
			<?php if ( Porto()->is_envato_hosted() ) : ?>
				<p class="lead" style="margin-bottom:40px">
				<?php esc_html_e( 'You are using Envato Hosted.', 'porto' ); ?>
				</p>
				<p class="porto-setup-actions step">
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
				</p>
			<?php else : ?>
				<p class="lead">Enter your Purchase Code.</p>
					<?php
						$output = '';

						$errors = get_option( 'porto_register_error_msg' );
						delete_option( 'porto_register_error_msg' );
						$purchase_code = Porto()->get_purchase_code_asterisk();

					if ( ! empty( $errors ) ) {
						echo '<div class="notice-error notice-alt"><p>' . esc_html( $errors ) . '</p></div>';
					}

					if ( ! empty( $purchase_code ) ) {
						if ( ! empty( $errors ) ) {
							echo '<div class="notice-warning notice-alt"><p>' . esc_html__( 'Purchase code not updated. We will keep the existing one.', 'porto' ) . '</p></div>';
						} else {
							/* translators: $1 and $2 opening and closing strong tags respectively */
							echo '<div class="notice-success notice-alt notice-large" style="margin-bottom:15px!important">' . sprintf( esc_html__( 'Your %1$spurchase code is valid%2$s. Thank you! Enjoy Porto Theme and automatic updates.', 'porto' ), '<strong>', '</strong>' ) . '</div>';
						}
					}

					if ( ! Porto()->is_registered() ) {
						echo '<form action="" method="post">';
						?>
							<p style="margin-bottom: 0;"><?php esc_html_e( 'Where can I find my purchase code?', 'porto' ); ?></p>
							<ol>
								<?php /* translators: $1: opening A tag which has link to the Themeforest downloads page $2: closing A tag */ ?>
								<li><?php printf( esc_html__( 'Please go to %1$sThemeForest.net/downloads%2$s', 'porto' ), '<a target="_blank" href="https://themeforest.net/downloads">', '</a>' ); ?></li>
								<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
								<li><?php printf( esc_html__( 'Click the %1$sDownload%2$s button in Porto row', 'porto' ), '<strong>', '</strong>' ); ?></li>
								<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
								<li><?php printf( esc_html__( 'Select %1$sLicense Certificate &amp; Purchase code%2$s', 'porto' ), '<strong>', '</strong>' ); ?></li>
								<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
								<li><?php printf( esc_html__( 'Copy %1$sItem Purchase Code%2$s', 'porto' ), '<strong>', '</strong>' ); ?></li>
							</ol>
						<?php
						echo '<input type="hidden" name="porto_registration" /><input type="hidden" name="action" value="register" />' .
								'<input type="text" id="porto_purchase_code" name="code" value="' . esc_attr( $purchase_code ) . '" placeholder="Purchase code" style="width:100%; padding:10px;"/><br/><br/>' .
								'<p class="porto-setup-actions step">' .
								'<input type="submit" class="button button-large button-next button-primary" value="' . esc_attr__( 'Activate', 'porto' ) . '" />' .
								'<a href="' . esc_url( $this->get_next_step_link() ) . '" class="button button-large button-next">' . esc_html__( 'Skip this step', 'porto' ) . '</a>' .
								'</p>';
					} else {
						echo '<form action="" method="post"><input type="hidden" name="porto_registration" /><input type="hidden" name="action" value="unregister" />' .
								'<input type="text" id="porto_purchase_code" name="code" value="' . esc_attr( $purchase_code ) . '" placeholder="Purchase code" style="width:100%; padding:10px;"/><br/><br/>' .
								'<p class="porto-setup-actions step">' . '<a href="' . esc_url( $this->get_next_step_link() ) . '" class="button button-large button-next" style="margin-right: 0;">' . esc_html__( 'Next Step', 'porto' ) . '</a>' . '<input type="submit" class="button button-large button-next button-primary" value="' . esc_attr__( 'Deactivate', 'porto' ) . '" style="margin-right: 0.5em;" />' .
								'</p>';
					}
						wp_nonce_field( 'porto-setup' );
						echo '</form>';
					?>
				<?php
			endif;
		}

		public function porto_setup_wizard_customize() {
			?>

			<h1><?php esc_html_e( 'Setup Porto Child Theme (Optional)', 'porto' ); ?></h1>

			<p>
				<?php
					echo wp_kses(
						__( 'If you are going to make changes to the theme source code please use a <a href="https://codex.wordpress.org/Child_Themes" target="_blank">Child Theme</a> rather than modifying the main theme HTML/CSS/PHP code. This allows the parent theme to receive updates without overwriting your source code changes. Use the form below to create and activate the Child Theme.', 'porto' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
							),
						)
					);
				?>
			</p>

			<?php if ( ! isset( $_REQUEST['theme_name'] ) ) { ?>
			<p class="lead"><?php esc_html_e( 'If you\'re not sure what a Child Theme is just click the "Skip this step" button.', 'porto' ); ?></p>
			<?php } ?>

			<?php
				// Create Child Theme
			if ( isset( $_REQUEST['theme_name'] ) && current_user_can( 'manage_options' ) ) {
				echo porto_filter_output( $this->_make_child_theme( sanitize_text_field( $_REQUEST['theme_name'] ) ) );
			}
				$theme = 'Porto Child';
			?>

			<?php if ( ! isset( $_REQUEST['theme_name'] ) ) { ?>

			<form method="POST">
				<div class="child-theme-input" style="margin-bottom: 20px;">
				<label style="font-weight: bold;margin-bottom: 5px; display: block;"><?php esc_html_e( 'Child Theme Title', 'porto' ); ?></label>
				<input type="text" style="padding:10px; width: 100%;" name="theme_name" value="<?php echo esc_attr( $theme ); ?>" />
				</div>
				<p class="porto-setup-actions step">
					<button type="submit" id= type="submit"  class="button button-primary button-next button-next"><?php esc_html_e( 'Create and Use Child Theme', 'porto' ); ?></button>
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'porto' ); ?></a>

				</p>
			</form>
			<?php } else { ?>
			<p class="porto-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
			</p>
			<?php } ?>
			<?php
		}
		public function porto_setup_wizard_help_support() {
			?>
			<h1><?php esc_html_e( 'Help and Support', 'porto' ); ?></h1>
			<p class="lead">This theme comes with 6 months item support from purchase date (with the option to extend this period). This license allows you to use this theme on a single website. Please purchase an additional license to use this theme on another website.</p>

			<p class="success">Item Support <strong>DOES</strong> Include:</p>

			<ul>
				<li>Availability of the author to answer questions</li>
				<li>Answering technical questions about item features</li>
				<li>Assistance with reported bugs and issues</li>
				<li>Help with bundled 3rd party plugins</li>
			</ul>

			<p class="error">Item Support <strong>DOES NOT</strong> Include:</p>
			<ul>
				<li>Customization services (this is available through <a href="mailto:nicework125@gmail.com">nicework125@gmail.com</a>)</li>
				<li>Installation services (this is available through <a href="mailto:nicework125@gmail.com">nicework125@gmail.com</a>)</li>
				<li>Help and Support for non-bundled 3rd party plugins (i.e. plugins you install yourself later on)</li>
			</ul>
			<p>More details about item support can be found in the ThemeForest <a href="http://themeforest.net/page/item_support_policy" target="_blank">Item Support Policy</a>. </p>
			<p class="porto-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Agree and Continue', 'porto' ); ?></a>
				<?php wp_nonce_field( 'porto-setup' ); ?>
			</p>
			<?php
		}

		/**
		 * Final step
		 */
		public function porto_setup_wizard_ready() {

			update_option( 'porto_setup_complete', time() );
			?>

			<h1><?php esc_html_e( 'Your Website is Ready!', 'porto' ); ?></h1>

			<p class="lead success">Congratulations! The theme has been activated and your website is ready. Please go to your WordPress dashboard to make changes and modify the content for you needs.</p>
			<p>Please come back and <a href="http://themeforest.net/downloads" target="_blank">leave a 5-star rating</a> if you are happy with this theme. Thanks! </p>

			<div class="porto-setup-next-steps">
				<div class="porto-setup-next-steps-first">
					<h2><?php esc_html_e( 'Next Steps', 'porto' ); ?></h2>
					<ul>
						<?php
						if ( class_exists( 'woocommerce' ) ) {
							?>
							<li class="setup-product"><a class="button  button-primary button-large woocommerce-button" href="<?php echo esc_url( admin_url() ) . 'index.php?page=wc-setup'; ?>"><?php esc_html_e( 'Setup WooCommerce (optional)', 'porto' ); ?></a></li><?php } ?>
						<li class="setup-product"><a class="button button-large" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your new website!', 'porto' ); ?></a></li>
					</ul>
				</div>
				<div class="porto-setup-next-steps-last">
					<h2><?php esc_html_e( 'More Resources', 'porto' ); ?></h2>
					<ul>
						<li class="documentation"><a href="http://www.portotheme.com/wordpress/porto/documentation"><?php esc_html_e( 'Porto Documentation', 'porto' ); ?></a></li>
						<li class="woocommerce documentation"><a href="https://docs.woocommerce.com/document/woocommerce-101-video-series/"><?php esc_html_e( 'Learn how to use WooCommerce', 'porto' ); ?></a></li>
						<li class="howto"><a href="https://wordpress.org/support/"><?php esc_html_e( 'Learn how to use WordPress', 'porto' ); ?></a></li>
						<li class="rating"><a href="http://themeforest.net/downloads"><?php esc_html_e( 'Leave an Item Rating', 'porto' ); ?></a></li>
					</ul>
				</div>
			</div>
			<?php
		}


		/****************** importer functions *************************/
		private function get_demo_file( $demo = false ) {
			if ( ! $demo ) {
				$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
			}
			// Importer remote API
			require_once PORTO_PLUGINS . '/importer/importer-api.php';
			$importer_api   = new Porto_Importer_API( $demo );
			$demo_file_path = $importer_api->get_remote_demo();
			if ( ! $demo_file_path ) {
				echo json_encode(
					array(
						'process' => 'error',
						'message' => __(
							'Remote API error.',
							'porto'
						),
					)
				);
				die();
			} elseif ( is_wp_error( $demo_file_path ) ) {
				echo json_encode(
					array(
						'process' => 'error',
						'message' => $demo_file_path->get_error_message(),
					)
				);
				die();
			}
			return $demo_file_path;
		}

		private function get_file_data( $path ) {
			$data = false;
			$path = wp_normalize_path( $path );
			// filesystem
			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			if ( $wp_filesystem->exists( $path ) ) {
				$data = $wp_filesystem->get_contents( $path );
			}
			return $data;
		}

		public function download_demo_file() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			$this->get_demo_file();
			echo json_encode( array( 'process' => 'success' ) );
			die();
		}

		function delete_tmp_dir() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
			// Importer remote API
			require_once PORTO_PLUGINS . '/importer/importer-api.php';
			$importer_api = new Porto_Importer_API( $demo );
			$importer_api->delete_temp_dir();
			die();
		}


		function reset_menus() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				$import_shortcodes = ( isset( $_POST['import_shortcodes'] ) && 'true' == $_POST['import_shortcodes'] ) ? true : false;
				if ( $import_shortcodes ) {
					$menus = array( 'Main Menu', 'Secondary Menu', 'Top Navigation', 'Home One Page', 'Footer Bottom Links', 'Departments', 'Resources', 'Company', 'Services' );
				} else {
					$menus = array( 'Main Menu', 'Secondary Menu', 'Top Navigation', 'Home One Page', 'Footer Bottom Links', 'Departments', 'Resources', 'Company', 'Services' );
				}

				foreach ( $menus as $menu ) {
					wp_delete_nav_menu( $menu );
				}
				esc_html_e( 'Successfully reset menus!', 'porto' );
			}
			die;
		}

		function reset_widgets() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				ob_start();
				$sidebars_widgets = retrieve_widgets();
				foreach ( $sidebars_widgets as $area => $widgets ) {
					foreach ( $widgets as $key => $widget_id ) {
						$pieces       = explode( '-', $widget_id );
						$multi_number = array_pop( $pieces );
						$id_base      = implode( '-', $pieces );
						$widget       = get_option( 'widget_' . $id_base );
						unset( $widget[ $multi_number ] );
						update_option( 'widget_' . $id_base, $widget );
						unset( $sidebars_widgets[ $area ][ $key ] );
					}
				}

				update_option( 'sidebars_widgets', $sidebars_widgets );
				ob_clean();
				ob_end_clean();
				esc_html_e( 'Successfully reset widgets!', 'porto' );
			}
			die;
		}

		function import_dummy() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}

			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			}

			if ( ! class_exists( 'WP_Import' ) ) { // if WP importer doesn't exist
				require_once PORTO_PLUGINS . '/importer/wordpress-importer.php';
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) { // check for main import class and wp import class

				$demo                        = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				$process                     = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$demo_path                   = $this->get_demo_file();
				$importer                    = new WP_Import();
				$theme_xml                   = $demo_path . '/content.gz';
				$importer->fetch_attachments = true;

				$this->import_before_functions( $demo );

				// ob_start();
				$response = $importer->import( $theme_xml, $process );
				// ob_end_clean();
				if ( 'import_start' == $process && $response ) {
					echo json_encode(
						array(
							'process' => 'importing',
							'count'   => 0,
							'index'   => 0,
							'message' => esc_html__(
								'Importing posts',
								'porto'
							),
						)
					);
				} else {
					$this->import_after_functions( $demo );
				}
			}
			die();
		}

		function import_override_contents( $post_exists, $post ) {
			$override_contents = ( isset( $_POST['override_contents'] ) && 'true' == $_POST['override_contents'] ) ? true : false;
			if ( ! $override_contents || ( $post_exists && get_post_type( $post_exists ) != 'revision' ) ) {
				return $post_exists;
			}

			// remove posts which have same ID
			$processed_duplicates = get_option( 'porto_import_processed_duplicates', array() );
			if ( in_array( $post['post_id'], $processed_duplicates ) ) {
				return false;
			}
			$old_post = get_post( $post['post_id'] );
			if ( $old_post ) {
				if ( $old_post->post_type == $post['post_type'] && ( 'page' == $post['post_type'] || 'block' == $post['post_type'] || 'member' == $post['post_type'] || 'portfolio' == $post['post_type'] || 'event' == $post['post_type'] || 'post' == $post['post_type'] || 'product' == $post['post_type'] ) ) {
					return $post['post_id'];
				}
				wp_delete_post( $post['post_id'], true );
			}

			// remove posts which have same title and slug
			global $wpdb;

			$post_title = wp_unslash( sanitize_post_field( 'post_title', $post['post_title'], 0, 'db' ) );
			$post_name  = wp_unslash( sanitize_post_field( 'post_name', $post['post_name'], 0, 'db' ) );

			$query  = "SELECT ID FROM $wpdb->posts WHERE 1=1";
			$args   = array();
			$query .= ' AND post_title = %s';
			$args[] = $post_title;
			$query .= ' AND post_name = %s';
			$args[] = $post_name;

			$old_post = (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );

			if ( $old_post && get_post_type( $old_post ) == $post['post_type'] ) {
				if ( 'page' == $post['post_type'] || 'block' == $post['post_type'] || 'member' == $post['post_type'] || 'portfolio' == $post['post_type'] || 'event' == $post['post_type'] || 'post' == $post['post_type'] || 'product' == $post['post_type'] ) {
					$processed_duplicates[] = $old_post;
					update_option( 'porto_import_processed_duplicates', $processed_duplicates );
					return $old_post;
				}
				wp_delete_post( $old_post, true );
			}

			return false;
		}

		function import_dummy_start() {
			$process = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
			if ( current_user_can( 'manage_options' ) && 'import_start' == $process ) {
				delete_option( 'porto_import_processed_duplicates' );
			}
		}

		function import_dummy_end() {
			if ( current_user_can( 'manage_options' ) && isset( $_POST['action'] ) && 'porto_import_dummy' === $_POST['action'] ) {
				ob_end_clean();
				ob_start();
				echo json_encode(
					array(
						'process' => 'complete',
						'message' => esc_html__(
							'Imported posts',
							'porto'
						),
					)
				);
				ob_end_flush();
				ob_start();
			}
		}

		function import_dummy_step_by_step() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}

			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				include $wp_importer;
			}

			if ( ! class_exists( 'Porto_WP_Import' ) ) { // if WP importer doesn't exist
				$wp_import = PORTO_PLUGINS . '/importer/porto-wordpress-importer.php';
				include $wp_import;
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'Porto_WP_Import' ) ) { // check for main import class and wp import class

				$process   = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$demo      = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				$index     = ( isset( $_POST['index'] ) && $_POST['index'] ) ? (int) $_POST['index'] : 0;
				$demo_path = $this->get_demo_file();

				$importer                    = new Porto_WP_Import();
				$theme_xml                   = $demo_path . '/content.gz';
				$importer->fetch_attachments = true;

				if ( 'import_start' == $process ) {
					$this->import_before_functions( $demo );
				}

				$loop = (int) ( ini_get( 'max_execution_time' ) / 60 );
				if ( $loop < 1 ) {
					$loop = 1;
				}
				if ( $loop > 10 ) {
					$loop = 10;
				}
				$i = 0;
				while ( $i < $loop ) {
					$response = $importer->import( $theme_xml, $process, $index );
					if ( isset( $response['count'] ) && isset( $response['index'] ) && $response['count'] && $response['index'] && $response['index'] < $response['count'] ) {
						$i++;
						$index = $response['index'];
					} else {
						break;
					}
				}

				echo json_encode( $response );
				ob_start();
				if ( 'complete' == $response['process'] ) {
					$this->import_after_functions( $demo );
				}
				ob_end_clean();
			}
			die();
		}

		function import_widgets() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				// Import widgets
				$demo_path   = $this->get_demo_file();
				$widget_data = $this->get_file_data( $demo_path . '/widget_data.json' );
				$this->import_widget_data( $widget_data );
				esc_html_e( 'Successfully imported widgets!', 'porto' );
				flush_rewrite_rules();
			}
			die();
		}

		function import_revsliders() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				// Import Revolution Slider
				if ( class_exists( 'RevSlider' ) ) {
					$demos = $this->porto_demo_types();
					if ( isset( $demos[ $demo ]['revslider'] ) && ! empty( $demos[ $demo ]['revslider'] ) ) {

						$demo_path = $this->get_demo_file();
						$slider    = new RevSlider();
						foreach ( $demos[ $demo ]['revslider'] as $rev ) {
							$slider->importSliderFromPost( true, false, $demo_path . '/' . $rev );
						}

						esc_html_e( 'Successfully imported revolution sliders!', 'porto' );
					}
				}

				if ( isset( $_POST['import_options_too'] ) && 'true' == $_POST['import_options_too'] ) {
					$this->import_options();
				}
			}
			die();
		}

		function import_icons() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) && get_option( 'smile_fonts', false ) ) {
				// Import icons
				ob_start();
				$paths            = wp_upload_dir();
				$paths['fonts']   = 'smile_fonts';
				$paths['temp']    = trailingslashit( $paths['fonts'] ) . 'smile_temp';
				$paths['fontdir'] = trailingslashit( $paths['basedir'] ) . $paths['fonts'];
				$paths['tempdir'] = trailingslashit( $paths['basedir'] ) . $paths['temp'];
				$paths['fonturl'] = set_url_scheme( trailingslashit( $paths['baseurl'] ) . $paths['fonts'] );
				$paths['tempurl'] = trailingslashit( $paths['baseurl'] ) . trailingslashit( $paths['temp'] );
				$paths['config']  = 'charmap.php';
				$sli_fonts        = trailingslashit( $paths['basedir'] ) . $paths['fonts'] . '/Simple-Line-Icons';
				$sli_fonts_dir    = PORTO_PLUGINS . '/importer/data/Simple-Line-Icons/';

				// Make destination directory
				if ( ! is_dir( $sli_fonts ) ) {
					wp_mkdir_p( $sli_fonts );
				}
				@chmod( $sli_fonts, 0777 );
				foreach ( glob( $sli_fonts_dir . '*' ) as $file ) {
					$new_file = basename( $file );
					@copy( $file, $sli_fonts . '/' . $new_file );
				}
				$fonts = get_option( 'smile_fonts' );
				if ( empty( $fonts ) ) {
					$fonts = array();
				}
				$fonts['Simple-Line-Icons'] = array(
					'include' => trailingslashit( $paths['fonts'] ) . 'Simple-Line-Icons',
					'folder'  => trailingslashit( $paths['fonts'] ) . 'Simple-Line-Icons',
					'style'   => 'Simple-Line-Icons' . '/' . 'Simple-Line-Icons' . '.css',
					'config'  => $paths['config'],
				);
				update_option( 'smile_fonts', $fonts );
				ob_get_clean();
				esc_html_e( 'Successfully imported simple line icon!', 'porto' );
			}
			die();
		}

		function import_options() {
			if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				$demo_path = $this->get_demo_file();
				ob_start();
				include $demo_path . '/theme_options.php';
				$theme_options = ob_get_clean();

				ob_start();
				$options = json_decode( $theme_options, true );
				$redux   = ReduxFrameworkInstances::get_instance( 'porto_settings' );
				$redux->set_options( $options );
				ob_clean();
				ob_end_clean();

				if ( ! isset( $_POST['import_options_too'] ) || 'true' != $_POST['import_options_too'] ) {
					try {
						porto_import_theme_settings( false, $options );
						porto_save_theme_settings();
						esc_html_e( 'Successfully imported theme options!', 'porto' );
					} catch ( Exception $e ) {
						esc_html_e( 'Successfully imported theme options! Please compile default css files in Theme Options > Skin > Compile Default CSS.', 'porto' );
					}
				}
			}
			die();
		}

		// Parsing Widgets Function
		// Reference: http://wordpress.org/plugins/widget-settings-importexport/
		private function import_widget_data( $widget_data ) {
			$json_data = $widget_data;
			$json_data = json_decode( $json_data, true );

			$sidebar_data = $json_data[0];
			$widget_data  = $json_data[1];

			foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
				$widgets[ $widget_data_title ] = array();
				foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
					if ( is_int( $widget_data_key ) ) {
						$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
					}
				}
			}
			unset( $widgets[''] );

			foreach ( $sidebar_data as $title => $sidebar ) {
				$count = count( $sidebar );
				for ( $i = 0; $i < $count; $i++ ) {
					$widget               = array();
					$widget['type']       = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
					$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
					if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
						unset( $sidebar_data[ $title ][ $i ] );
					}
				}
				$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
			}

			foreach ( $widgets as $widget_title => $widget_value ) {
				foreach ( $widget_value as $widget_key => $widget_value ) {
					$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
				}
			}

			$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
			$this->parse_import_data( $sidebar_data );
		}

		private function parse_import_data( $import_array ) {
			global $wp_registered_sidebars;
			$sidebars_data    = $import_array[0];
			$widget_data      = $import_array[1];
			$current_sidebars = get_option( 'sidebars_widgets' );
			$new_widgets      = array();

			foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

				foreach ( $import_widgets as $import_widget ) :
					// if the sidebar exists
					if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) :
						$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
						$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
						$current_widget_data = get_option( 'widget_' . $title );
						$new_widget_name     = $this->get_new_widget_name( $title, $index );
						$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

						if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
							while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
								$new_index++;
							}
						}
						$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
						if ( array_key_exists( $title, $new_widgets ) ) {
							$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
							$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];
							unset( $new_widgets[ $title ]['_multiwidget'] );
							$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
						} else {
							$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
							$current_multiwidget               = ( isset( $current_widget_data['_multiwidget'] ) ) ? $current_widget_data['_multiwidget'] : '';
							$new_multiwidget                   = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
							$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
							unset( $current_widget_data['_multiwidget'] );
							$current_widget_data['_multiwidget'] = $multiwidget;
							$new_widgets[ $title ]               = $current_widget_data;
						}

					endif;
				endforeach;
			endforeach;

			if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
				update_option( 'sidebars_widgets', $current_sidebars );

				foreach ( $new_widgets as $title => $content ) {
					update_option( 'widget_' . $title, $content );
				}

				return true;
			}

			return false;
		}

		private function get_new_widget_name( $widget_name, $widget_index ) {
			$current_sidebars = get_option( 'sidebars_widgets' );
			$all_widget_array = array();
			foreach ( $current_sidebars as $sidebar => $widgets ) {
				if ( ! empty( $widgets ) && is_array( $widgets ) && 'wp_inactive_widgets' != $sidebar ) {
					foreach ( $widgets as $widget ) {
						$all_widget_array[] = $widget;
					}
				}
			}
			while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
				$widget_index++;
			}
			$new_widget_name = $widget_name . '-' . $widget_index;
			return $new_widget_name;
		}

		private function importer_get_page_by_title( $page_title, $output = OBJECT ) {
			global $wpdb;
			$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s order by post_name desc limit 1", $page_title, 'page' ) );

			if ( $page ) {
				return get_post( $page, $output );
			}
		}

		private function import_before_functions( $demo ) {
			if ( 'shortcodes' != $demo ) {
				// update visual composer content types
				update_option( 'wpb_js_content_types', array( 'post', 'page', 'block', 'faq', 'member', 'portfolio', 'event' ) );

				$is_shop_demo = ( strpos( '__' . $demo, 'shop' ) === false ) ? false : true;
				// update woocommerce image sizes
				$catalog = array(
					'width'  => '300',   // px
					'height' => ( $is_shop_demo ? '400' : '300' ), // px
					'crop'   => 1,        // true
				);

				$single = array(
					'width'  => '500',   // px
					'height' => ( $is_shop_demo ? '666' : '500' ), // px
					'crop'   => 1,        // true
				);

				$thumbnail = array(
					'width'  => '150',   // px
					'height' => '150',   // px
					'crop'   => 1,        // false
				);

				// Image sizes
				add_image_size( 'shop_thumbnail', $thumbnail['width'], $thumbnail['height'], $thumbnail['crop'] );
				add_image_size( 'shop_catalog', $catalog['width'], $catalog['height'], $catalog['crop'] );
				add_image_size( 'shop_single', $single['width'], $single['height'], $single['crop'] );

				// Add sidebar widget areas
				$extra_demos = $this->porto_extra_demos();
				if ( ! in_array( $demo, $extra_demos ) ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'PortfolioSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'PortfolioSidebar' => 'Portfolio Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				}

				if ( 'construction' == $demo ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'CompanySidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'CompanySidebar' => 'Company Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'ServicesSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'ServicesSidebar' => 'Services Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				} elseif ( 'law-firm' == $demo || 'shop20' == $demo ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'GeneralSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'GeneralSidebar' => 'General Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				} elseif ( 'hotel' == $demo ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'HotelSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'HotelSidebar' => 'Hotel Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				} elseif ( 'medical' == $demo ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'DepartmentsSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'DepartmentsSidebar' => 'Departments Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'ResourcesSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'ResourcesSidebar' => 'Resources Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				} elseif ( 'real-estate' == $demo ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'AboutUsSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'AboutUsSidebar' => 'About Us Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				}

				if ( $is_shop_demo ) {
					$sbg_sidebar = get_option( 'sbg_sidebars', array() );
					if ( ! array_key_exists( 'ProductLeftSidebar', $sbg_sidebar ) ) {
						$sbg_sidebar = array_merge( $sbg_sidebar, array( 'ProductLeftSidebar' => 'Product Left Sidebar' ) );
						update_option( 'sbg_sidebars', $sbg_sidebar );
					}
				}
			} else {
				// Add sidebar widget areas
				$sbg_sidebar = get_option( 'sbg_sidebars', array() );
				if ( ! array_key_exists( 'ShortcodesSidebar', $sbg_sidebar ) ) {
					$sbg_sidebar = array_merge( $sbg_sidebar, array( 'ShortcodesSidebar' => 'Shortcodes Sidebar' ) );
					update_option( 'sbg_sidebars', $sbg_sidebar );
				}
			}
		}

		private function import_after_functions( $demo ) {
			delete_option( 'porto_import_processed_duplicates' );
			if ( 'shortcodes' != $demo ) {
				// Set woocommerce pages
				$woopages = array(
					'woocommerce_shop_page_id'      => 'Shop',
					'woocommerce_cart_page_id'      => 'Cart',
					'woocommerce_checkout_page_id'  => 'Checkout',
					'woocommerce_myaccount_page_id' => 'My Account',
				);

				foreach ( $woopages as $woo_page_name => $woo_page_title ) {
					$woopage = get_page_by_title( $woo_page_title );
					if ( isset( $woopage ) && $woopage->ID ) {
						update_option( $woo_page_name, $woopage->ID ); // Front Page
					}
				}

				// We no longer need to install pages
				$notices = array_diff( get_option( 'woocommerce_admin_notices', array() ), array( 'install', 'update' ) );
				update_option( 'woocommerce_admin_notices', $notices );
				delete_option( '_wc_needs_pages' );
				delete_transient( '_wc_activation_redirect' );

				// Set imported menus to registered theme locations
				$locations = get_theme_mod( 'nav_menu_locations' ); // registered menu locations in theme
				$menus     = wp_get_nav_menus(); // registered menus

				if ( $menus ) {
					foreach ( $menus as $menu ) { // assign menus to theme locations
						if ( 'Main Menu' == $menu->name ) {
							$locations['main_menu'] = $menu->term_id;
						} elseif ( 'Secondary Menu' == $menu->name ) {
							$locations['secondary_menu'] = $menu->term_id;
						} elseif ( 'Top Navigation' == $menu->name ) {
							$locations['top_nav'] = $menu->term_id;
						} elseif ( 'View Switcher' == $menu->name ) {
							$locations['view_switcher'] = $menu->term_id;
						} elseif ( 'Currency Switcher' == $menu->name ) {
							$locations['currency_switcher'] = $menu->term_id;
						}
					}
				}

				set_theme_mod( 'nav_menu_locations', $locations ); // set menus to locations

				// Set reading options
				$homepage = $this->importer_get_page_by_title( 'Home' );
				if ( 'law-firm' == $demo || 'finance' == $demo ) {
					$posts_page = $this->importer_get_page_by_title( 'News' );
				} elseif ( 'restaurant' == $demo ) {
					$posts_page = $this->importer_get_page_by_title( 'Press' );
				} elseif ( 'wedding' == $demo ) {
					$posts_page = $this->importer_get_page_by_title( 'Our Blog' );
				} else {
					$posts_page = $this->importer_get_page_by_title( 'Blog' );
				}

				if ( ( $homepage && $homepage->ID ) || ( $posts_page && $posts_page->ID ) ) {
					update_option( 'show_on_front', 'page' );
					if ( $homepage && $homepage->ID ) {
						update_option( 'page_on_front', $homepage->ID ); // Front Page
					}
					if ( $posts_page && $posts_page->ID ) {
						update_option( 'page_for_posts', $posts_page->ID ); // Blog Page
					}
				}

				// Set one page menu
				$onepage = $this->importer_get_page_by_title( 'Home One Page' );
				$menu    = wp_get_nav_menu_object( 'Home One Page' );
				if ( $menu && $onepage ) {
					$menu_id = $menu->term_id;
					update_post_meta( $onepage->ID, 'main_menu', $menu_id );
				}
			} else {
				// Import widgets
				$demo_path   = $this->get_demo_file( $demo );
				$widget_data = $this->get_file_data( $demo_path . '/widget_data.json' );
				$this->import_widget_data( $widget_data );
			}
			// Flush rules after install
			flush_rewrite_rules();
		}

	}
}

add_action( 'after_setup_theme', 'porto_theme_setup_wizard', 10 );

if ( ! function_exists( 'porto_theme_setup_wizard' ) ) :
	function porto_theme_setup_wizard() {
		$instance = Porto_Theme_Setup_Wizard::get_instance();
	}
endif;
