<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Moove_Actions File Doc Comment
 *
 * @category Moove_Actions
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

/**
 * Moove_Actions Class Doc Comment
 *
 * @category Class
 * @package  Moove_Actions
 * @author   Gaspar Nemes
 */

class Moove_Actions {
	/**
	 * Variable for scrip localization
	 *
	 * @var loc_data
	 */
	var $loc_data;
	/**
	 * Construct actions
	 */
	function __construct() {
		$this->moove_register_scripts();
		$this->moove_register_ajax_actions();
		$this->moove_register_front_end_actions();
		$this->moove_register_admin_actions();
	}
	/**
	 * Register front-end or back-end scripts
	 */
	function moove_register_scripts() {
		if ( is_admin() ) :
			add_action( 'admin_enqueue_scripts', array( &$this, 'moove_admin_scripts' ) );
		else :
			add_action( 'wp_enqueue_scripts', array( &$this, 'moove_front_end_scripts' ) );
		endif;
	}

	/**
	 * Register global variables to head, AJAX, Form validation messages
	 *
	 * @param  string $ascript Localize function name.
	 */
	public function moove_localize_script( $ascript ) {
		global $wp_query;
		$max = $wp_query->max_num_pages;
			$paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;
		$this->loc_data = array(
			'ajaxurl' 					=> admin_url( 'admin-ajax.php' ),
			'startpage' 				=> $paged,
				'maxpages' 				=> $max,
				'nextlink' 				=> next_posts( $max, false ),
				'validationoptions'		=> get_option( 'moove_protection-validation' ),
		);
		wp_localize_script( $ascript, 'moove_front_end_scripts', $this->loc_data );
	}
	/**
	 * Registe FRONT-END Javascripts and Styles
	 */
	public function moove_front_end_scripts() {
		wp_enqueue_script( 'moove_validation', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/third-party/jquery-validation/jquery.validate.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'moove_validation_add', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/third-party/jquery-validation/additional-methods.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'moove_custom_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/moove_frontend.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'moove_custom_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/css/moove_frontend.css' );
		$this->moove_localize_script( 'moove_custom_frontend' );
	}
	/**
	 * Back-end CSS
	 */
	public function moove_admin_scripts() {
		wp_enqueue_style( 'moove_custom_backend', plugins_url( basename( dirname( __FILE__ ) ) ).'/assets/css/moove_backend.css' );
	}
	/**
	 * Register AJAX actions registration for the plugin
	 */
	public function moove_register_ajax_actions() {
		/**
		 * AJAX function for email validation.
		 */
		add_action( 'wp_ajax_check_email', array( &$this, 'moove_check_email' ) );
		add_action( 'wp_ajax_nopriv_check_email', array( &$this, 'moove_check_email' ) );
	}
	/**
	 * Register FRONT-END actions for protection and login
	 */
	public function moove_register_front_end_actions() {
		add_action( 'init', array( 'Moove_Controller', 'moove_add_client_roles' ) );
		if ( defined( 'DOING_AJAX' ) && ! DOING_AJAX ) :
			add_action( 'wp_login_failed', array( 'Moove_Controller', 'moove_login_fail' ) );
		endif;
		add_action( 'init', array( 'Moove_Controller', 'moove_redirect_login_page' ) );
	}
	/**
	 * Register action to restrict admin pages
	 */
	public function moove_register_admin_actions() {
		add_action( 'admin_init', array( &$this, 'moove_restrict_admin_pages' ) );
	}
	/**
	 * Hide ADMIN area for users without admin rights
	 */
	public function moove_restrict_admin_pages() {
		$is_ajax = false;
		if ( isset( $_SERVER['DOING_AJAX'] ) ) :
			$is_ajax = $_SERVER['DOING_AJAX'] === '/wp-admin/admin-ajax.php';
		else :
			$is_ajax = $_SERVER['REQUEST_URI'] === '/wp-admin/admin-ajax.php';
		endif;
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_posts' ) && ! $is_ajax ) :
	  		wp_redirect( home_url( '/my-account' ) );
	  		exit;
		endif;
	}
	/**
	 * Check the E-mail address if is already registered or not.
	 */
	public function moove_check_email() {
		$email_address = sanitize_email( wp_unslash( $_POST['email_address'] ) );
		if ( is_email( $email_address ) ) :
			echo email_exists( $email_address ) === false ? 'true' : 'false';
		else :
			echo 'false';
		endif;
		die();
	}
}
$moove_actions_provider = new Moove_Actions();

