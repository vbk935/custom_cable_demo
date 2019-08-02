<?php if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly ?>
<?php
/**
 * Moove_Controller File Doc Comment
 *
 * @category Moove_Controller
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

/**
 * Moove_Controller Class Doc Comment
 *
 * @category Class
 * @package  Moove_Controller
 * @author   Gaspar Nemes
 */
class Moove_Controller {
	/**
	 * Register client roles: Membership, Free registration
	 *
	 * @return void
	 */
	public static function moove_add_client_roles() {
		$membership_role = add_role(
			'membership',
			__( 'Membership' , 'moove' ),
			array(
				'read'              => true,    // True allows this capability.
				'edit_posts'        => false,   // Allows user to edit their own posts.
				'edit_pages'        => false,   // Allows user to edit pages.
				'edit_others_posts' => false,   // Allows user to edit others posts not just their own.
				'create_posts'      => false,   // Allows user to create new posts.
				'manage_categories' => false,   // Allows user to manage post categories.
				'publish_posts'     => false,   // Allows the user to publish, otherwise posts stays in draft mode.
				'edit_themes'       => false,   // false denies this capability. User can’t edit your theme.
				'install_plugins'   => false,   // User cant add new plugins.
				'update_plugin'     => false,   // User can’t update any plugins.
				'update_core'       => false,	// User cant perform core updates.
			)
		);
		$free_registration_role = add_role(
			'free_registration',
			__( 'Free Registration' , 'moove' ),
			array(
				'read'              => true,    // True allows this capability.
				'edit_posts'        => false,   // Allows user to edit their own posts.
				'edit_pages'        => false,   // Allows user to edit pages.
				'edit_others_posts' => false,   // Allows user to edit others posts not just their own.
				'create_posts'      => false,   // Allows user to create new posts.
				'manage_categories' => false,   // Allows user to manage post categories.
				'publish_posts'     => false,   // Allows the user to publish, otherwise posts stays in draft mode.
				'edit_themes'       => false,   // false denies this capability. User can’t edit your theme.
				'install_plugins'   => false,   // User cant add new plugins.
				'update_plugin'     => false,   // User can’t update any plugins.
				'update_core'       => false,	// User cant perform core updates.
			)
		);
	}
	/**
	 * Redirect after successfull login
	 *
	 * @param  string $redirect_to URL where the user need to be redirected.
	 * @param  string $request  Client request.
	 * @param  obj    $user Current user.
	 * @return string Redirect URL
	 */
	public static function moove_login_redirect( $redirect_to, $request, $user ) {
		return $redirect_to;
	}
	/**
	 * Logout redirect
	 *
	 * @return void
	 */
	public static function moove_logout_page() {
		$login_page  = home_url( '/login/' );
		wp_redirect( $login_page . '?login=false' );
		exit;
	}
	/**
	 * Login page redirect
	 *
	 * @return void
	 */
	public static function moove_redirect_login_page() {
		$login_page  = home_url( '/login' );
		global $pagenow;
		$page_viewed = $pagenow;
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		if ( ( $page_viewed === 'wp-login' || $page_viewed === 'wp-login.php' ) && $action !== 'logout' && $_SERVER['REQUEST_METHOD'] === 'GET' ) {
			$redirect_to = esc_url_raw( wp_unslash( $_GET['redirect_to'] ) );
			if ( isset( $redirect_to ) && $redirect_to !== '' ) {
				$login_page = add_query_arg( 'redirect' , esc_url( $redirect_to ) , $login_page );
			}

			wp_redirect( $login_page );
			exit;
		}
		if ( ! is_user_logged_in() && $page_viewed === 'wp-admin' ) {
			$login_page = add_query_arg( 'redirect', urlencode( home_url( '/wp-admin/' ) ), $login_page );
			wp_redirect( $login_page );
			exit;
		}
	}
	/**
	 * Verify login details
	 *
	 * @param  obj    $user  WP User.
	 * @param  string $username WP User username.
	 * @param  string $password Password.
	 */
	public static function moove_verify_username_password( $user, $username, $password ) {
		if ( $username === '' || $password === '' ) {
			$referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
			$referrer = add_query_arg( 'login', 'empty', $referrer );
			$referrer = add_query_arg( 'username', $username, $referrer );
			$reauth = sanitize_text_field( $redirect_to( $_GET['reauth'] ) );
			if ( isset( $reauth ) ) {
				$redirect_to = esc_url_raw( wp_unslash( $_GET['redirect_to'] ) );
				$referrer = add_query_arg( 'redirect', esc_url( $redirect_to ), $referrer );
			}
			$login_page  = home_url( '/login-page/?login=empty' );
			wp_redirect( $login_page );
			exit;
		}
	}
	/**
	 * Redirect to referrer url if the login was unsuccessfull
	 *
	 * @return void
	 */
	public static function moove_login_fail() {
		$referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
		$referrer = add_query_arg( 'login', 'failed', $referrer );
		$referrer = add_query_arg( 'username', $username, $referrer );
		wp_redirect( $referrer );
		exit;
	}
	/**
	 * Checking WP Admin access
	 *
	 * @return void
	 */
	public static function moove_check_redirect() {
		$moove_user = new Moove_User();
		$u = $moove_user->moove_check();
		if ( $u['logged_in'] === true ) {
			if ( ($u['wp_admin'] === true || $u['editor'] === true) ) {
				wp_redirect( site_url( '/wp-admin/' ) );
			} else {
				$redirect_to = esc_url_raw( wp_unslash( $_GET['redirect_to'] ) );
				if ( isset( $redirect_to ) && $redirect_to !== '' ) {
					wp_redirect( esc_url( $redirect_to ) );
				} else {
					wp_redirect( site_url( '/' ) ); // /my-account
				}
			}
		}
	}
	/**
	 * Login form with redirect url
	 *
	 * @return string Login form
	 */
	public static function moove_login() {
		Moove_Controller::moove_check_redirect();
		$get_login = sanitize_text_field( wp_unslash( $_GET['login'] ) );
		$login  = ( isset( $get_login ) ) ? esc_attr( $get_login ) : 0;
		$login_message = array( 'type' => false );
		if ( $login === 'failed' ) {
			$login_message['type'] = 'error';
			$login_message['msg'] = __( 'Invalid username and/or password.' , 'moove' );
		} elseif ( $login === 'empty' ) {
			$login_message['type'] = 'error';
			$login_message['msg'] = __( 'Username and/or Password is empty.' , 'moove' );
		} elseif ( $login === 'false' ) {
			$login_message['type'] = 'info';
			$login_message['msg'] = __( 'You are logged out.' , 'moove' );
		}
		$redirect_to = esc_url_raw( wp_unslash( $_GET['redirect'] ) );
		$redirect = isset( $redirect_to ) ? trim( esc_url( $redirect_to ) ) : '';
		$view_bag = array(
		  'system_message' => $login_message,
		);
		if ( trim( $redirect ) !== '' ) {
			$view_bag['redirect_to'] = $redirect;
		}
		return Moove_View::load( 'moove.login', $view_bag );
	}
}
