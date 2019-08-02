<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Contributors: MooveAgency
 * Plugin Name: Post Protection and Registration Wall
 * Plugin URI: http://www.mooveagency.com/
 * Description: This plugin gives you the ability to protect any kind of content on your website for registered users/members only.
 * Version: 1.0.5
 * Author: Moove Agency
 * Author URI: http://www.mooveagency.com/
 * License: GPLv2
 * Text Domain: moove
 */

register_activation_hook( __FILE__, 'moove_activate' );
register_deactivation_hook( __FILE__, 'moove_deactivate' );

/**
 * Create initial pages with shortcodes
 *
 * @param  string $option_name Option name to update.
 * @param  array  $page        New page info.
 */
function moove_create_page_with_shortcode( $option_name, $page ) {
	$page_new = array(
		'post_title' 	=> $page['title'],
		'post_content' 	=> $page['content'],
		'post_status' 	=> 'publish',
		'post_type' 	=> 'page',
	);
	$post_id = wp_insert_post( $page_new );
	update_option( $option_name, $post_id, true );
}
/**
 * Settings on plugin activation
 */
function moove_settings_activate() {

}
/**
 * Settings on plugin deactivation
 */
function moove_settings_deactivate() {

}
/**
 * If the Validation part is empty on settings, it set up the defaults.
 */
function moove_set_validation_messages() {
	$validations = get_option( 'moove_protection-validation' );
	$validation_options = array(
		'Sign-up'   => array(
		    'first-name'    => array(
		        'required'              => __( 'This field is required.', 'moove' ),
		    ),
		    'last-name'     => array(
		        'required'              => __( 'This field is required.', 'moove' ),
		    ),
		    'email'         => array(
		        'required'              => __( 'Your e-mail address is required as it serves as your user name.', 'moove' ),
		        'invalid-email'         => __( 'This is not a valid e-mail address.', 'moove' ),
		        'already-registered'    => __( 'This e-mail address has already been registered.', 'moove' ),
		    ),
		    'password'      => array(
		        'required'              => __( 'A password is required to protect your account.', 'moove' ),
		        'min-length'            => __( 'Your password needs to have at least 8 characters.', 'moove' ),
		        'equal-to'              => __( 'The two password fields must match, to avoid typos in your password.', 'moove' ),
		    ),
		),
		'Login'    => array(
		    'ajax-message'  => array(
		        'signing-in'            => __( 'Signing in...', 'moove' ),
		        'invalid-login'         => __( 'Invalid login...', 'moove' ),
		    ),
		    'email'         => array(
		        'required'              => __( 'E-mail address is required.', 'moove' ),
		        'invalid-email'         => __( 'This is not a valid e-mail address.', 'moove' ),
		    ),
		    'password'      => array(
		        'required'              => __( 'The password cannot be empty.', 'moove' ),
		    ),
		    'lost-mail'     => array(
		        'required'              => __( 'E-mail address is required.', 'moove' ),
		        'invalid-email'         => __( 'This is not a valid e-mail address.', 'moove' ),
		        'nonexistent-email'		=> __( 'Invalid or nonexistent email address!', 'moove' ),
		    ),
		),
		'Reset'    => array(
		    'password'      => array(
		        'required'              => __( 'A password is required to protect your account.', 'moove' ),
		        'min-length'            => __( 'Your password needs to have at least 8 characters.', 'moove' ),
		        'equal-to'              => __( 'The two password fields must match, to avoid typos in your password.', 'moove' ),
		    ),
		),
	);
	foreach ( $validation_options as $section => $section_fields ) :
		foreach ( $section_fields as $field_type => $fieldset ) :
		    foreach ( $fieldset as $field_title => $message ) :
		        if ( $validations[ $section.'_'.$field_type.'_'.$field_title ] === '' ) :
		            $validations[ $section.'_'.$field_type.'_'.$field_title ] = $message;
		            update_option( 'moove_protection-validation', $validations );
		        endif;
		    endforeach;
		endforeach;
	endforeach;
}
/**
 * Set the protection type to protection modal by default
 */
function moove_set_protection_type() {
	$post_types = get_post_types( array( 'public' => true ) );
	foreach ( $post_types as &$post_type ) :
		$protection = get_option( 'moove_post_protect' )[ $post_type . '_protection_type' ];
		if ( ! isset( $protection ) ) :
			$prot_type[ $post_type . '_protection_type' ] = 'protection_modal';
			update_option( 'moove_post_protect', $prot_type );
		endif;
	endforeach;
}
/**
 * Functions on plugin activation, create relevant pages and defaults for settings page.
 */
function moove_activate() {

	// Activation code here...
	$pages = array(
		'moove_login_page' 			=> array( 'title' => 'Login', 'content' => '[LoginForm]' ),
		'moove_myaccount_page' 		=> array( 'title' => 'My Account', 'content' => '[MyAccount]' ),
		'moove_register_page'		=> array( 'title' => 'Register', 'content' => '[RegisterForm]' ),
		'moove_resetpassword_page'	=> array( 'title' => 'Reset password', 'content' => '[moove_reset_password]' ),
	);

	foreach ( $pages as $option_name => $page ) :
		moove_create_page_with_shortcode( $option_name, $page );
	endforeach;

	if ( empty( get_option( 'moove_protection-email' )['Remindcontent'] ) ) :
		$email['Remindcontent'] = Moove_View::load( 'moove.mail.remind-content' );
		if ( empty( get_option( 'moove_protection-email' )['Remindemail_title'] ) ) :
			$email['Remindemail_title'] = __( 'Reset your password', 'moove' );
			update_option( 'moove_protection-email', $email );
		endif;
		update_option( 'moove_protection-email', $email );
	endif;

	if ( empty( get_option( 'moove_protection-email' )['Confirmcontent'] ) ) :
		$email['Confirmcontent'] = Moove_View::load( 'moove.mail.confirm-content' );
		if ( empty( get_option( 'moove_protection-email' )['Confirmemail_title'] ) ) :
			$email['Confirmemail_title'] = __( 'User signup confirmation mail', 'moove' );
			update_option( 'moove_protection-email', $email );
		endif;
		update_option( 'moove_protection-email', $email );
	endif;

	$protection = get_option( 'moove_protection-settings' );
	if ( empty( $protection['Free-membershipmodal-content'] ) ) :
		$protection['Free-membershipmodal-content'] = Moove_View::load( 'moove.protected.free_membership' );
		if ( empty( $protection['Free-membershiptruncate-button-text'] ) ) :
			$protection['Free-membershiptruncate-button-text'] = __( 'Click here to register.', 'moove' );
			update_option( 'moove_protection-settings', $protection );
		endif;
		if ( empty( $protection['Free-membershiptruncate-button-link'] ) ) :
			$protection['Free-membershiptruncate-button-link'] = home_url() . '/register';
			update_option( 'moove_protection-settings', $protection );
		endif;
		if ( empty( $protection['Free-membershipprotection-message'] ) ) :
			$protection['Free-membershipprotection-message'] = __( 'This content is protected, available for registered users.', 'moove' );
			update_option( 'moove_protection-settings', $protection );
		endif;
		update_option( 'moove_protection-settings', $protection );
	endif;

	if ( empty( $protection['Premium-membershipmodal-content'] ) ) :
		$protection['Premium-membershipmodal-content'] = Moove_View::load( 'moove.protected.premium_membership' );
		if ( empty( $protection['Premium-membershiptruncate-button-text'] ) ) :
			$protection['Premium-membershiptruncate-button-text'] = __( 'Click here to register.', 'moove' );
			update_option( 'moove_protection-settings', $protection );
		endif;

		if ( empty( $protection['Premium-membershiptruncate-button-link'] ) ) :
			$protection['Premium-membershiptruncate-button-link'] = home_url() . '/register';
			update_option( 'moove_protection-settings', $protection );
		endif;

		if ( empty( $protection['Premium-membershipprotection-message'] ) ) :
			$protection['Premium-membershipprotection-message'] = __( 'This content is protected, available for users with premium membership.', 'moove' );
			update_option( 'moove_protection-settings', $protection );
		endif;

		update_option( 'moove_protection-settings', $protection );
	endif;

	moove_set_validation_messages();
	moove_set_protection_type();
	moove_settings_activate();
}
/**
 * Function on plugin deactivation. It removes the pages created before.
 */
function moove_deactivate() {
	$page_titles = array( 'Login', 'My Account', 'Register', 'Reset password' );
	foreach ( $page_titles as $title ) :
		$page = get_page_by_title( $title );
		wp_delete_post( $page->ID, true );
	endforeach;
	moove_settings_deactivate();
}

include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-view.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-content.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-options.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-user-settings.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-controller.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-actions.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-shortcodes.php' );
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-functions.php' );

