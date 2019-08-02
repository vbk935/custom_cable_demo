<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * CUSTOM FUNCTIONS FOR PLGUIN
 *
 * @package   moove-protection-plugin
 */

add_action( 'after_setup_theme', 'moove_remove_admin_bar' );
/**
 * Disable admin bar for all user_status
 */
function moove_remove_admin_bar() {
	if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_posts' ) && ! is_admin() ) :
		show_admin_bar( false );
	endif;
}
/**
 * ENABLE E-mail login authentification
 *
 * @param  obj    $user     WP User.
 * @param  string $username WP User username.
 * @param  string $password Password.
 * @return Authenticate the user.
 */
function moove_email_login_authenticate( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) ) : return $user;
endif;
	if ( ! empty( $username ) ) :
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 === (int) $user->user_status ) :
			$username = $user->user_login;
		endif;
	endif;
	return wp_authenticate_username_password( null, $username, $password );
}
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'moove_email_login_authenticate', 20, 3 );

/**
 * Add compatibility for WPMU 2.9.1 and WPMU 2.9.2, props r-a-y
 */
if ( ! function_exists( 'is_super_admin' ) ) :
	/**
	 * Super admins
	 *
	 * @return array Super Admins
	 */
	function get_super_admins() {
		global $super_admins;

		if ( isset( $super_admins ) ) :
			return $super_admins;
		else :
			return get_site_option( 'site_admins', array( 'admin' ) );
		endif;
	}
	/**
	 * Check if is super admin
	 *
	 * @param  boolean $user_id User_id
	 * @return boolean
	 */
	function is_super_admin( $user_id = false ) {
		if ( ! $user_id ) :
			$current_user = wp_get_current_user();
			$user_id = ! empty( $current_user ) ? $current_user->id : 0;
		endif;

		if ( ! $user_id ) :
			return false;
		endif;

		$user = new WP_User( $user_id );

		if ( is_multisite() ) :
			$super_admins = get_super_admins();
			if ( is_array( $super_admins ) && in_array( $user->user_login, $super_admins ) ) :
				return true;
			endif;
		else :
			if ( $user->has_cap( 'delete_users' ) ) :
				return true;
			endif;
		endif;

		return false;
	}
endif;

/**
 * Modify the string on the login page to prompt for username or email address
 */
function moove_username_or_email_login() {
	if ( 'wp-login.php' !== basename( $_SERVER['SCRIPT_NAME'] ) ) :
		return;
	endif;

	?><script type="text/javascript">
	// Form Label.
	if ( document.getElementById('loginform') ) :
		document.getElementById('loginform').childNodes[1].childNodes[1].childNodes[0].nodeValue = '<?php echo esc_js( __( 'Username or Email', 'email-login' ) ); ?>';
	endif;

	// Error Messages.
	if ( document.getElementById('login_error') ) :
		document.getElementById('login_error').innerHTML = document.getElementById('login_error').innerHTML.replace( '<?php echo esc_js( __( 'username' ) ); ?>', '<?php echo esc_js( __( 'Username or Email' , 'email-login' ) ); ?>' );
	endif;
	</script><?php
}
add_action( 'login_form', 'moove_username_or_email_login' );

/**
 * Returns the logged_in user level
 *
 * @return  int User level.
 */
function moove_current_user_level() {
	if ( ! is_user_logged_in() ) :
		return 1;
	endif;

	if ( current_user_can( 'free_registration' ) ) :
		return 2;
	endif;

	if ( current_user_can( 'membership' ) ) :
		return 3;
	endif;
}
/**
 * Difference between post protection level and user level
 *
 * @param  string $content_level Post's protection level.
 * @return  string Content protection type.
 */
function moove_protection_level_diff( $content_level ) {
	$user_level = moove_current_user_level();
	if ( $user_level >= $content_level ) :
		return false;
	else :
		switch ( $content_level ) {
			case 2:
				return 'login';
			break;
			case 3:
				return 'premium';
			break;
		}
	endif;
}
/**
 * Return the post/page protection level
 *
 * @param  obj $post Post Object.
 * @return  int Post protection level.
 */
function moove_post_protection_level( &$post ) {
	$protection_level_default = get_option( 'moove_post_protect' );

	if ( isset( $protection_level_default[ $post->post_type ] ) ) :
		$def_level = $protection_level_default[ $post->post_type ];
	else :
		$def_level = 1;
	endif;

	$post_level = get_post_meta( $post->ID, 'moove_protection_level' , true );

	if ( ! $post_level || $post_level === '-1' ) :
		$post_level = $def_level;
	endif;
	return $post_level;
}
/**
 * Return true if the post/page is protected
 *
 * @param  obj $post Post Object.
 * @return  boolean
 */
function moove_is_protected( &$post ) {
	$post_level = moove_post_protection_level( $post );
	return moove_protection_level_diff( $post_level ) !== false;
}
/**
 * Return true is the post/page is protected as public
 *
 * @param  obj $post Post Object.
 * @return  boolean
 */
function moove_is_public( &$post ) {
	$post_level = moove_post_protection_level( $post );
	return moove_protection_level_diff( $post_level ) === false;
}
/**
 * Return true is the post/page is protected for free users
 *
 * @param  obj $post Post Object.
 * @return  boolean
 */
function moove_is_reg( &$post ) {
	$post_level = moove_post_protection_level( $post );
	return moove_protection_level_diff( $post_level ) === 'login';
}
/**
 * Return true is the post/page is protected for premium users
 *
 * @param  obj $post Post Object.
 * @return  boolean
 */
function moove_is_premium( &$post ) {
	$post_level = moove_post_protection_level( $post );
	return moove_protection_level_diff( $post_level ) === 'premium';
}
/**
 * Load the view for login form. Can be loaded to a modal.
 *
 * @param  string $redirect_to URL, after login the user will be redirected to this URL.
 * @return  string HTML form view.
 */
function get_moove_login_form( $redirect_to ) {
	return Moove_View::load(
		'moove.login',
		array(
			'redirect_to' => $redirect_to,
		)
	);
}
/**
 * Show the login form
 *
 *  @param  string $redirect_to URL, after login the user will be redirected to this URL.
 *  @return  void.
 */
function moove_login_form( $redirect_to ) {
	echo get_moove_login_form( esc_url( $redirect_to ) );
}
