<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Moove_Custom_ShortCodes File Doc Comment
 *
 * @category Moove_Custom_ShortCodes
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

/**
 * Moove_Custom_ShortCodes Class Doc Comment
 *
 * @category Class
 * @package  Moove_Custom_ShortCodes
 * @author   Gaspar Nemes
 */
class Moove_Custom_ShortCodes
{
	/**
	 * Construct shortcodes
	 */
	function __construct() {

		$this->moove_register_shortcodes();
	}
	/**
	 * Registering shortcodes and actions
	 *
	 * @return void
	 */
	function moove_register_shortcodes() {

		add_shortcode( 'LoginForm', array( &$this, 'moove_login' ) );
		add_shortcode( 'RegisterForm', array( &$this, 'moove_signup_form' ) );
		add_shortcode( 'moove_content_gate', array( &$this, 'moove_content_gate' ) );
		add_shortcode( 'moove_register_form', array( &$this, 'moove_signup_form' ) );
		add_shortcode( 'moove_form_confirmation', array( &$this, 'moove_confirmation_form' ) );
		add_shortcode( 'moove_reset_password', array( &$this, 'moove_reset_password' ) );
		add_action( 'wp_ajax_moove_update_user_modal', array( &$this, 'moove_update_user_ajax' ) );
	}
	/**
	 * Login function
	 *
	 * @return boolean
	 */
	function moove_login() {
		return Moove_Controller::moove_login();
	}
	/**
	 * Trim post/page content
	 *
	 * @param  array  $atts    Shortcode attributes.
	 * @param  string $content Post/page content to trim.
	 * @return string          Protection level with excerpt.
	 */
	function moove_content_gate( $atts, $content ) {
		$a = shortcode_atts(
			array(
				'level' => 1,
			),
			$atts
		);
		$protection_level = $a['level'];
		$ret = '';
		if ( $protection_level < 2 ) :
			return $content;
		endif;
		$diff = moove_protection_level_diff( $protection_level );
		switch ( $diff ) {
			case false:
				return $content;
			break;
			case 'login':
				return Moove_View::load( 'moove.protected.free_membership', null );
			break;
			case 'premium':
				return Moove_View::load( 'moove.protected.premium_membership', null );
			break;
		}
		return $a['level'] . $content;
	}
	/**
	 * Register form
	 *
	 * @param  array $atts User attributes.
	 * @return string HTML register form
	 */
	function moove_signup_form( $atts ) {
		global $moove_user_custom_settings;
		if ( ! is_user_logged_in() ) :
			$errs = array();
			$nonce = sanitize_key( wp_unslash( $_POST['moove_register'] ) );
			if ( ! empty( $_POST ) && $nonce ) :
				$result = $this->moove_register_user();
				if ( is_wp_error( $result ) ) :
					return __( 'An error has occurred while registering. Please return later', 'moove' );
				endif;
				if ( is_array( $result ) && isset( $result['error'] ) ) :
					$errs = $result;
				endif;
				// Return soft redirect when there are no errors.
				if ( ! is_wp_error( $result ) && ( ! is_array( $result ) ) && ! isset( $result['error'] ) ) :
					return Moove_View::load( 'moove.registration.confirm', null );
				endif;
			endif;
			return Moove_View::load(
				'moove.registration.register',
				array(
					'atts'			 	=> $atts,
					'custom_fields' 	=> $custom_fields,
					'errors' 			=> $errs,
				)
			);
		endif;
	}
	/**
	 * Register user
	 *
	 * @return array Result of user registration
	 */
	function moove_register_user() {
		$user = new Moove_User();
		$fields = array(
			'name' 			=> sanitize_text_field( wp_unslash( $_POST['moove_name'] ) ),
			'surname' 		=> sanitize_text_field( wp_unslash( $_POST['surname'] ) ),
			'email' 		=> sanitize_email( wp_unslash( $_POST['email'] ) ),
			'password' 		=> sanitize_text_field( wp_unslash( $_POST['pwd'] ) ),
			'password2'		=> sanitize_text_field( wp_unslash( $_POST['pwdc'] ) ),
		);
		$validation_messages = get_option( 'moove_protection-validation' );
		$errs = array();
		if ( email_exists( $fields['email'] ) ) :
			$errs['email'] = $validation_messages['Sign-up_email_already-registered'];
		endif;
		if ( ! is_email( $fields['email'] ) ) :
			$errs['email'] = $validation_messages['Sign-up_email_invalid-email'];
		endif;
		if ( trim( $fields['email'] === '' ) ) :
			$errs['email'] = $validation_messages['Sign-up_email_required'];
		endif;
		if ( trim( $fields['name'] === '' ) ) :
			$errs['moove_name'] = $validation_messages['Sign-up_first-name_required'];
		endif;
		if ( trim( $fields['surname'] === '' ) ) :
			$errs['surname'] = $validation_messages['Sign-up_last-name_required'];
		endif;
		if ( trim( $fields['password'] === '' ) ) :
			$errs['pwd'] = $validation_messages['Sign-up_password_required'];
		endif;
		if ( strlen( $fields['password'] ) < 8 ) :
			$errs['pwd'] = $validation_messages['Sign-up_password_min-length'];
		endif;
		if ( $fields['password'] !== $fields['password2'] ) :
			$errs['pwdc'] = $validation_messages['Sign-up_password_equal-to'];
		endif;
		if ( ! empty( $errs ) ) :
			$errs['error'] = true;
			return $errs;
		endif;
		$username = str_replace( '-', '_', $fields['email'] );
		$username = str_replace( ' ', '_s_', $username );
		$username = str_replace( '.', '_dot_', $username );
		$username = str_replace( '@', '_at_', $username );
		$fields['username'] = $username;
		$custom_fields = array();
		$result = $user->moove_register( $fields, null );
		return $result;
	}
	/**
	 * Password reset
	 *
	 * @param  array $atts User attributes.
	 * @return string Error message
	 */
	function moove_reset_password( $atts ) {
		$validation_messages = get_option( 'moove_protection-validation' );
		if ( is_user_logged_in() ) :
			return '<p>' . __( 'Cannot reset password for a logged in user.' , 'moove' ) . '</p>';
		endif;
		$errors = array();
		$token = sanitize_key( wp_unslash( $_GET['token'] ) );
		$reset_token = sanitize_key( wp_unslash( $_POST['reset_token'] ) );
		if ( trim( $token ) === '' && ! isset( $reset_token ) ) :
			return '<p>' . __( 'No token has been specified.' , 'moove' ) . '</p>';
		endif;
		if ( isset( $reset_token ) ) :
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['moove_reset'] ) ), 'moove_reset_action' ) ) :
				$errors['error'] = true;
				$errors['nonce'] = 'Remote check failed';
			endif;
			$token = $reset_token;
			$password = sanitize_text_field( wp_unslash( $_POST['password'] ) );
			$password2 = sanitize_text_field( wp_unslash( $_POST['password2'] ) );
			if ( trim( $password ) === '' ) :
				$errors['error'] 	= true;
				$errors['password'] = $validation_messages['Reset_password_required'];
			endif;
			if ( mb_strlen( $password ) < 8 ) :
				$errors['error'] = true;
				$errors['password'] = $validation_messages['Reset_password_min-length'];
			endif;
			if ( $password !== $password2 ) :
				$errors['error'] 	 = true;
				$errors['password2'] = $validation_messages['Reset_password_equal-to'];
			endif;
		endif;
		if ( ! isset( $errors['error'] ) && isset( $reset_token ) ) :
			$user = new Moove_User();
			$result = $user->moove_update_password( $token, sanitize_text_field( wp_unslash( $_POST['password'] ) ) );
			if ( $result === false ) :
				$errors['error'] = true;
				$errors['fail']  = __( 'An error occurred. Please try again' , 'moove' );
			endif;
		endif;
		if ( ! isset( $errors['error'] ) && isset( $reset_token ) ) :
			echo Moove_View::load( 'moove.reset-completed' , false );
		else :
			echo Moove_View::load(
				'moove.reset',
				array(
					'token' 	=> esc_attr( $token ),
					'errors' 	=> $errors,
				)
			);
		endif;
	}
}
$moove_event_shortcodes_provider = new Moove_Custom_ShortCodes();
