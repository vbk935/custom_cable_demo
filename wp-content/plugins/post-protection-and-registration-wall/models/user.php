<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Moove_User File Doc Comment
 *
 * @category Moove_User
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

/**
 * Moove_User Class Doc Comment
 *
 * @category Class
 * @package  Moove_User
 * @author   Gaspar Nemes
 */
class Moove_User {
	/**
	 * Return informations about logged in user
	 *
	 * @return array Current user data
	 */
	public function moove_check() {
		$user = wp_get_current_user();
		return array(
			'id' 			=> $user->ID,
			'logged_in' 	=> $user->ID > 0,
			'wp_admin' 		=> current_user_can( 'manage_options' ),
			'editor' 		=> current_user_can( 'editor' ),
			'role' 			=> $this->moove_get_role( $user ),
		);
	}
	/**
	 * Return User ID by activation_key
	 *
	 * @param  string $key Activation key.
	 * @return array  SQL Result
	 */
	public function moove_get_id_by_key( $key ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_activation_key = %s", esc_sql( $key ) ) );
	}
	/**
	 * Retrun User activation key by username
	 *
	 * @param  string $login username.
	 * @return array  SQL Result
	 */
	public function moove_get_key_by_login( $login ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $login ) );
	}
	/**
	 * Update User Activation key by username
	 *
	 * @param  string $key        Activation key.
	 * @param  string $user_login Username.
	 * @return void
	 */
	public function moove_set_activation_key( $key, $user_login ) {
		global $wpdb;
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $user_login ) );
	}
	/**
	 * Update user password by key
	 *
	 * @param  string $key      User activation key.
	 * @param  string $password New password for user with $key.
	 * @return boolean
	 */
	public function moove_update_password( $key, $password ) {
		$user_id = $this->moove_get_id_by_key( $key );
		$user = get_user_by( 'id', $user_id );
		if ( $user !== false ) {
			wp_set_password( $password, $user_id );
			$this->moove_set_activation_key( '', $user->user_login );
			return true;
		}
		return false;
	}
	/**
	 * Return user role
	 *
	 * @param  obj $user WP User.
	 * @return string       User role, if is not logged in, it returns false
	 */
	public function moove_get_role( $user ) {
		if ( is_user_logged_in() ) {
			return array_shift( $user->roles );
		}
		return false;
	}
	/**
	 * Set user role by user_id
	 *
	 * @param int    $user_id WP User ID.
	 * @param string $role    New role for $user_id.
	 */
	public function set_user_role( $user_id, $role ) {
		$userdata = array(
			'id' 	=> $user_id,
			'role' 	=> $role,
		);
		wp_update_user( $userdata );
	}
	/**
	 * Return all WP Users by role
	 *
	 * @return array WP Users
	 */
	public function moove_get_users() {
		$admin_users = get_users(
			array(
				'role' => 'client_admin',
			)
		);
		$client_users = get_users(
			array(
				'role' => 'client_user',
			)
		);
		return array_merge( $admin_users, $client_users );
	}
	/**
	 * Replace shortcodes on register e-mail content
	 *
	 * @param  array $args Register e-mail content from Settings page.
	 * @return array sanitized e-mail content
	 */
	function moove_register_mail_content( $args ) {
		$mailcontent = $args['mail'];
		$title = get_option( 'moove_protection-email' )['Confirmemail_title'];
		$view_data = $args['view_data'];
		if ( $mailcontent ) :
		    $replace_options = array(
		        '[[client_name]]'       => $view_data['username'],
		        '[[client_email]]'      => $view_data['user_email'],
		        '[[register_page]]'     => site_url().'/register',
		        '[[blog_name]]'         => get_option( 'blogname' ),
		        '[[mail_title]]'		=> $title,
		    );
		    foreach ( $replace_options as $moove_shortcode => $moove_value ) {
		        $mailcontent = str_replace( $moove_shortcode, $moove_value, $mailcontent );
		    };
		    return $mailcontent;
		endif;
	}
	/**
	 * New user registration
	 *
	 * @param  array $userData     Form data.
	 * @param  array $customFields Extra fields from register form.
	 * @return array Created and updated user data
	 */
	public function moove_register( $userData, $customFields ) {
		$user_id = wp_create_user( $userData['username'], $userData['password'], $userData['email'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$userdata = array(
			'ID' 			=> $user_id,
			'first_name' 	=> esc_attr( $userData['name'] ),
			'last_name' 	=> esc_attr( $userData['surname'] ),
			'display_name' 	=> esc_attr( $userData['name'] . ' ' . esc_attr( $userData['surname'] ) ),
			'user_nicename' => esc_attr( $userData['name'] . ' ' . esc_attr( $userData['surname'] ) ),
			'role' 			=> 'free_registration',
		);
		$x = wp_update_user( $userdata );
		if ( ! is_wp_error( $x ) ) {
			$maildata = array(
				'username' 		=> esc_attr( $userData['name'] . ' ' . esc_attr( $userData['surname'] ) ),
				'user_email' 	=> esc_attr( $userData['email'] ),
			);
			$_mailcontent = Moove_View::load( 'moove.mail.confirm' );
			$mailcontent = Moove_User::moove_register_mail_content( array( 'mail' => $_mailcontent, 'view_data' => $maildata ) );
			wp_mail( $userData['email'], 'Welcome to '.get_option( 'blogname' ), $mailcontent, 'Content-type: text/html'."\r\n".'' );
			do_action( 'moove_user_normal_registration_complete', $user_id );
		}
		return $x;
	}
}
