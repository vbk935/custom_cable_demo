<?php
/*
Plugin Name: Front End Registration and Login
Plugin URI: https://pippinsplugins.com/creating-custom-front-end-registration-and-login-forms
Description: Provides simple front end registration and login forms
Version: 1.0
Author: Pippin Williamson
Author URI: https://pippinsplugins.com
*/
// user registration login form
function pippin_registration_form() {
 
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
 
		global $pippin_load_css;
 
		// set this to true so the CSS is loaded
		$pippin_load_css = true;
 
		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');
 
		// only show the registration form if allowed
		if($registration_enabled) {
			$output = pippin_registration_form_fields();
		} else {
			$output = __('User registration is not enabled');
		}
		return $output;
	}
	else
	{
		echo 'User is already logged in';
	}
}
add_shortcode('register_form', 'pippin_registration_form');




// registration form fields
function pippin_registration_form_fields() {
 
	ob_start(); ?>	
		
 
		<?php 
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_registration_form" class="pippin_form" action="" method="POST">
			<fieldset>
				
				<p>
					<label for="pippin_user_first"><?php _e('First Name'); ?> <strong>*</strong></label>
					<input name="pippin_user_first" id="pippin_user_first" type="text"/>
				</p>
				<p>
					<label for="pippin_user_last"><?php _e('Last Name'); ?> <strong>*</strong></label>
					<input name="pippin_user_last" id="pippin_user_last" type="text"/>
				</p>
				<p>
					<label for="pippin_user_title"><?php _e('Title '); ?> <strong>*</strong></label>
					<input name="pippin_user_title" id="pippin_user_title" type="text"/>
				</p>
				<p>
					<label for="pippin_user_company"><?php _e('Company '); ?> <strong>*</strong></label>
					<input name="pippin_user_company" id="pippin_user_company" type="text"/>
				</p>
				<p>
					<label for="pippin_user_address"><?php _e('Address '); ?> <strong>*</strong></label>
					<input name="pippin_user_address" id="pippin_user_address" type="text"/>
				</p>
				<p>
					<label for="pippin_user_email"><?php _e('Email'); ?> <strong>*</strong></label>
					<input name="pippin_user_email" id="pippin_user_email" class="required" type="email"/>
					<span class="imp-note">The email will be your account username</span>
				</p>
				<p>
					<label for="pippin_user_phone"><?php _e('Phone number'); ?> <strong>*</strong></label>
					<input name="pippin_user_phone" id="pippin_user_phone" type="text"/>
				</p>
				<p>
					<label for="pippin_company_website"><?php _e('Company website'); ?> <strong>*</strong></label>
					<input name="pippin_company_website" id="pippin_company_website" type="text"/>
				</p>
				<p>
					<label for="password"><?php _e('Password'); ?> <strong>*</strong></label>
					<input name="pippin_user_pass" id="password" class="required" type="password"/>
				</p>
				<p>
					<label for="password_again"><?php _e('Confirm Password'); ?> <strong>*</strong></label>
					<input name="pippin_user_pass_confirm" id="password_again" class="required" type="password"/>
				</p>
			</fieldset>
			<fieldset>
				<p>
					<input type="hidden" name="pippin_register_nonce" value="<?php echo wp_create_nonce('pippin-register-nonce'); ?>"/>
					<input type="submit" class="btn btn-primary" value="<?php _e('Register'); ?>"/>
				</p>
			</fieldset>
		</form>
		<div class="forget"><a href="<?php echo site_url(). '/?page_id=1426'?>">Already Exists User?</a></div>
	<?php
	return ob_get_clean();
}




// register a new user
function pippin_add_new_member() {
  	if (isset( $_POST["pippin_user_email"] ) && wp_verify_nonce($_POST['pippin_register_nonce'], 'pippin-register-nonce')) {
		$user_login		= $_POST["pippin_user_email"];	
		$user_email		= $_POST["pippin_user_email"];
		$user_first 	= $_POST["pippin_user_first"];
		$user_last	 	= $_POST["pippin_user_last"];
		$user_pass		= $_POST["pippin_user_pass"];
		$pass_confirm 	= $_POST["pippin_user_pass_confirm"];
		$user_address 	= $_POST["pippin_user_address"];
		$user_title	= $_POST["pippin_user_title"];
		$user_company  	= $_POST["pippin_user_company"];
		echo $user_company_website 	= $_POST["pippin_company_website"];
		$user_phone	= $_POST["pippin_user_phone"];
		
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
		if(!is_email($user_email)) {
			//invalid email
			pippin_errors()->add('email_invalid', __('Invalid email'));
		}
		if(email_exists($user_email)) {
			//Email address already registered
			pippin_errors()->add('email_used', __('Email already registered'));
		}
		if($user_pass == '') {
			// passwords do not match
			pippin_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			pippin_errors()->add('password_mismatch', __('Passwords do not match'));
		}
		if($user_first == '') {
			pippin_errors()->add('first_empty', __('Please enter a first name'));
		}
		if($user_last == '') {
			pippin_errors()->add('last_empty', __('Please enter a last name'));
		}
		if($user_address == '') {
			pippin_errors()->add('address_empty', __('Please enter a address'));
		}
		if($user_title == '') {
			pippin_errors()->add('title_empty', __('Please enter a title'));
		}
		if($user_company == '') {
			pippin_errors()->add('company_empty', __('Please enter a company'));
		}
		if($user_company_website == '') {
			pippin_errors()->add('company_website_empty', __('Please enter company website'));
		}
		if($user_phone == '') {
			pippin_errors()->add('phone_empty', __('Please enter a telephone'));
		}
		
		$errors = pippin_errors()->get_error_messages();
 
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_email,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_email,
					'first_name'		=> $user_first,
					'last_name'			=> $user_last,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'subscriber'
				)
			);
			if($new_user_id) {
				add_user_meta( $new_user_id, '_address', $user_address);
				add_user_meta( $new_user_id, 'title', $user_title);
				add_user_meta( $new_user_id, 'billing_company', $user_company);
				add_user_meta( $new_user_id, 'company_website', $user_company_website);
				add_user_meta( $new_user_id, '_phone', $user_phone);
				wp_new_user_notification($new_user_id);
				
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
 
				// send the newly created user to the home page after logging them in
				wp_redirect(home_url().'/?page_id=1413'); exit;
			}
		}
	}
}
add_action('init', 'pippin_add_new_member');


// user login form
function pippin_login_form() {
 
	if(!is_user_logged_in()) {
 
		global $pippin_load_css;
 
		// set this to true so the CSS is loaded
		$pippin_load_css = true;
 
		$output = pippin_login_form_fields();
	} else {
		// could show some logged in user info here
		// $output = 'user info here';
	}
	return $output;
}
add_shortcode('login_form', 'pippin_login_form');

// login form fields
function pippin_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="pippin_header"><?php _e(''); ?></h3>
 
		<?php
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_login_form"  class="pippin_form"action="" method="post">
			<fieldset>
				<div class="form-group">
					<label for="pippin_user_Login">Username</label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</div>
				<div class="form-group">
					<label for="pippin_user_pass">Password</label>
					<input name="pippin_user_pass" id="pippin_user_pass" class="required" type="password"/>
				</div>
				<p>
					<input type="hidden" name="pippin_login_nonce" value="<?php echo wp_create_nonce('pippin-login-nonce'); ?>"/>
					<input id="pippin_login_submit" type="submit" class="btn btn-primary" value="Login"/>
				</p>
			</fieldset>
		</form>
		<div class="forget"><a href="<?php echo site_url(). '/?page_id=1421'?>">Forgot Username or Password?</a></div>
		<div class="forget"><a href="<?php echo site_url(). '/?page_id=1419'?>">New User Registration </a></div>
		
	<?php
	return ob_get_clean();
}

// logs a member in after submitting a form
function pippin_login_member() {
 
	if(isset($_POST['pippin_user_login']) && wp_verify_nonce($_POST['pippin_login_nonce'], 'pippin-login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_userdatabylogin($_POST['pippin_user_login']);
 
		if(!$user) {
			// if the user name doesn't exist
			pippin_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['pippin_user_pass']) || $_POST['pippin_user_pass'] == '') {
			// if no password was entered
			pippin_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['pippin_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			pippin_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = pippin_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			wp_setcookie($_POST['pippin_user_login'], $_POST['pippin_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['pippin_user_login']);	
			do_action('wp_login', $_POST['pippin_user_login']);
 
			   $location = site_url . '/?page_id=1413';
                wp_safe_redirect($location);
                exit();

		}
	}
}
add_action('init', 'pippin_login_member');


// used for tracking error messages
function pippin_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}


// displays error messages from form submissions
function pippin_show_error_messages() {
	if($codes = pippin_errors()->get_error_codes()) {
		echo '<div class="pippin_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = pippin_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}


// register our form css
function pippin_register_css() {
	wp_register_style('pippin-form-css', plugin_dir_url( __FILE__ ) . '/css/style.css');
}
add_action('init', 'pippin_register_css');



function pippin_change_password_form() {
	global $post;	
 
   	if (is_singular()) :
   		$current_url = get_permalink($post->ID);
   	else :
   		$pageURL = 'http';
   		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
   		$pageURL .= "://";
   		if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
   		else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
   		$current_url = $pageURL;
   	endif;		
	$redirect = $current_url;
 
	ob_start();
 
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<?php if(isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') { ?>
			<div class="pippin_message success">
				<span><?php _e('Password changed successfully', 'rcp'); ?></span>
			</div>
		<?php } ?>
		<form id="pippin_password_form" class="pippin_form" method="POST" action="<?php echo $current_url; ?>">
			<fieldset>
				<p>
					<label for="pippin_user_pass"><?php _e('New Password', 'rcp'); ?></label>
					<input name="pippin_user_pass" id="pippin_user_pass" class="required" type="password"/>
				</p>
				<p>
					<label for="pippin_user_pass_confirm"><?php _e('Password Confirm', 'rcp'); ?></label>
					<input name="pippin_user_pass_confirm" id="pippin_user_pass_confirm" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="pippin_action" value="reset-password"/>
					<input type="hidden" name="pippin_redirect" value="<?php echo $redirect; ?>"/>
					<input type="hidden" name="pippin_password_nonce" value="<?php echo wp_create_nonce('rcp-password-nonce'); ?>"/>
					<input id="pippin_password_submit" class="btn btn-primary" type="submit" value="<?php _e('Change Password', 'pippin'); ?>"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();	
}
 
// password reset form
function pippin_reset_password_form() {
	//if(is_user_logged_in()) {
		return pippin_change_password_form();
	//}
}
add_shortcode('password_form', 'pippin_reset_password_form');
 
 
function pippin_reset_password() {
	// reset a users password
	if(isset($_POST['pippin_action']) && $_POST['pippin_action'] == 'reset-password') {
 
		global $user_ID;
 
		if(!is_user_logged_in())
			return;
 
		if(wp_verify_nonce($_POST['pippin_password_nonce'], 'rcp-password-nonce')) {
 
			if($_POST['pippin_user_pass'] == '' || $_POST['pippin_user_pass_confirm'] == '') {
				// password(s) field empty
				pippin_errors()->add('password_empty', __('Please enter a password, and confirm it', 'pippin'));
			}
			if($_POST['pippin_user_pass'] != $_POST['pippin_user_pass_confirm']) {
				// passwords do not match
				pippin_errors()->add('password_mismatch', __('Passwords do not match', 'pippin'));
			}
 
			// retrieve all error messages, if any
			$errors = pippin_errors()->get_error_messages();
 
			if(empty($errors)) {
				// change the password here
				$user_data = array(
					'ID' => $user_ID,
					'user_pass' => $_POST['pippin_user_pass']
				);
				wp_update_user($user_data);
				// send password change email here (if WP doesn't)
				wp_redirect(add_query_arg('password-reset', 'true', $_POST['pippin_redirect']));
				exit;
			}
		}
	}	
}
add_action('init', 'pippin_reset_password');
 
if(!function_exists('pippin_show_error_messages')) {
	// displays error messages from form submissions
	function pippin_show_error_messages() {
		if($codes = pippin_errors()->get_error_codes()) {
			echo '<div class="pippin_message error">';
			    // Loop error codes and display errors
			   foreach($codes as $code){
			        $message = pippin_errors()->get_error_message($code);
			        echo '<span class="pippin_error"><strong>' . __('Error', 'rcp') . '</strong>: ' . $message . '</span><br/>';
			    }
			echo '</div>';
		}	
	} 
}
 
if(!function_exists('pippin_errors')) { 
	// used for tracking error messages
	function pippin_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}

// load our form css
function pippin_print_css() {
	global $pippin_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $pippin_load_css )
		return; // this means that neither short code is present, so we get out of here
 
	wp_print_styles('pippin-form-css');
}
add_action('wp_footer', 'pippin_print_css');
