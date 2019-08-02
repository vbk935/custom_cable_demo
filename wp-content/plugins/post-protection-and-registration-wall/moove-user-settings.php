<?php if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly ?>
<?php
/**
 * Moove_User_Custom_Settings File Doc Comment
 *
 * @category Moove_User_Custom_Settings
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

/**
 * Moove_User_Custom_Settings Class Doc Comment
 *
 * @category Class
 * @package  Moove_User_Custom_Settings
 * @author   Gaspar Nemes
 */
class Moove_User_Custom_Settings {
	/**
	 * Global variable for custom user fields
	 *
	 * @var array $user_fields
	 */
	public $user_fields;
	/**
	 * Construct function for actions
	 */
	function __construct() {
		add_action( 'wp_ajax_nopriv_moove_ajaxlogin', array( &$this, 'moove_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_moove_reset_password', array( &$this, 'moove_password_reset_request' ) );
		add_filter( 'authenticate', array( &$this, 'moove_email_login_filter' ), 20, 3 );
	}
	/**
	 * Login function with AJAX
	 *
	 * @return void
	 */
	function moove_ajax_login() {
		check_ajax_referer( 'ajax-login-nonce', 'security' );
		$redirect_url = esc_url_raw( $_POST['redirect'] );
		$redirect = isset( $redirect_url ) && trim( $redirect_url !== '' ) ? esc_attr( $redirect_url ) : site_url();
		$error = false;
		$remember_user = sanitize_text_field( $_POST['remember'] );
		$email_address = sanitize_email( $_POST['email'] );
		if ( is_email( $email_address ) ) :
			$user_data = array(
				'user_login' 	=> $email_address,
				'user_password' => sanitize_text_field( $_POST['password'] ),
				'remember' 		=> $remember_user === 'on',
			);
			$user_signon = wp_signon( $user_data );
			if ( is_wp_error( $user_signon ) ) :
				$error = true;
			endif;
		else :
			$error = true;
		endif;
		if ( $error === true ) :
			echo json_encode( array( 'login' => false, 'redirect' => $redirect ) );
		else :
			echo json_encode( array( 'login' => true, 'redirect' => $redirect ) );
		endif;
		die();
	}
	/**
	 * Enable login with e-mail address
	 *
	 * @param  obj    $user     WP User.
	 * @param  string $username WP User username.
	 * @param  string $password Passeword.
	 * @return boolean True/false after authentification
	 */
	function moove_email_login_filter( $user, $username, $password ) {
		if ( is_email( $username ) ) :
	        $user = get_user_by( 'email', $username );
	        if ( $user ) { $username = $user->user_login; }
	    endif;
	    return wp_authenticate_username_password( null, $username, $password );
	}
	/**
	 * Replacing password reset e-mail content shortcodes
	 *
	 * @param  array $args E-mail data array.
	 * @return array Sanitized e-mail content array
	 */
	function moove_password_reset_content( $args ) {
		$mailcontent = $args['mail'];
		$title = get_option( 'moove_protection-email' )['Remindemail_title'];
		$view_data = $args['view_data'];
		if ( $mailcontent ) :
		    $replace_options = array(
		        '[[client_name]]'       => $view_data['name'],
		        '[[client_email]]'      => $view_data['email'],
		        '[[site_url]]'          => site_url(),
		        '[[reset_link]]'        => $view_data['link'],
		        '[[blog_name]]'         => get_option( 'blogname' ),
		        '[[mail_title]]'		=> $title,
		    );
		    foreach ( $replace_options as $moove_shortcode => $moove_value ) :
		        $mailcontent = str_replace( $moove_shortcode, $moove_value, $mailcontent );
		    endforeach;
		    return $mailcontent;
		endif;
	}
	/**
	 * Sending the password reset e-mail with token
	 *
	 * @return void
	 */
	function moove_password_reset_request() {
		$email_address = sanitize_email( $_POST['email'] );
		if ( is_email( $email_address ) ) :
			$user = get_user_by( 'email', $email_address );
		else :
			$user = false;
		endif;
		if ( $user === false ) :
			echo json_encode( array( 'success' => false ) );
		else :
			$token = sha1( $email_address . substr( str_shuffle( str_repeat( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand( 1,20 ) ) ), 1, 20 ) );
			$model = new Moove_User();
			$model->moove_set_activation_key( $token, $user->user_login );
			$subject = get_option( 'blogname' ) . ' - Reset your password';
			$view_data = array(
				'name' 	=> $user->display_name,
				'email' => $email_address,
				'link' 	=> site_url( '/reset-password/?token=' . $token ),
			);
			$_mailcontent = Moove_View::load( 'moove.mail.remind' );
			$mailcontent = Moove_User_Custom_Settings::moove_password_reset_content( array( 'mail' => $_mailcontent, 'view_data' => $view_data ) );
			wp_mail( $email_address, $subject, $mailcontent, 'Content-type: text/html'."\r\n" );
			echo json_encode( array( 'success' => true ) );
		endif;
		die();
	}
}
$moove_user_custom_settings = new Moove_User_Custom_Settings();
