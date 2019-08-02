<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Moove_Protection_Options File Doc Comment
 *
 * @category Moove_Protection_Options
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

/**
 * Moove_Protection_Options Class Doc Comment
 *
 * @category Class
 * @package  Moove_Protection_Options
 * @author   Gaspar Nemes
 */
class Moove_Protection_Options
{
	/**
	 * Store option settings for moove_post_protect
	 *
	 * @var options
	 */
	private $options;
	/**
	 * Actions for option page
	 */
	public function __construct() {

		add_action( 'admin_menu', array( &$this, 'moove_admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'moove_page_init' ) );
	}
	/**
	 * Create option page for the plugin
	 */
	public function moove_admin_menu() {

		add_options_page(
			'Post protection',
			'Moove protection',
			'manage_options',
			'moove-protection',
			array( $this, 'moove_settings_page' )
		);
	}
	/**
	 * Show the settings page on the back-end
	 */
	public function moove_settings_page() {

		$this->options = get_option( 'moove_post_protect' );
		echo Moove_View::load( 'moove.admin.settings.settings_page', array() );
	}
	/**
	 * Create option groups and setting fields
	 */
	public function moove_page_init() {
		register_setting(
			'moove_post_protection', // Option group.
			'moove_post_protect' // Option name.
		);
		add_settings_section(
			'post_type_prot', // ID.
			'Post-type level protection', // Title.
			array( &$this, 'moove_print_section_info' ), // Callback.
			'moove-protection' // Page.
		);
		register_setting(
			'moove_protection_email', // Option group.
			'moove_protection-email' // Option name.
		);
		register_setting(
			'moove_protection_validation', // Option group.
			'moove_protection-validation' // Option name.
		);
		register_setting(
			'moove_protection_settings', // Option group.
			'moove_protection-settings' // Option name.
		);
		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as &$post_type ) :
			add_settings_field(
				$post_type,
				ucfirst( str_replace( '_', ' ', preg_replace( '/_cpt$/', '', $post_type ) ) ),
				array( &$this, 'moove_setting_callback' ),
				'moove-protection',
				'post_type_prot',
				array( 'post_type' => $post_type, 'protection_type' => $this->options[ $post_type . '_protection_type' ] )
			);
		endforeach;
		$email_options = array(
			'Confirm' => array(
				'email_title' => 'input',
				'content' => 'textarea',
			),
			'Remind' => array(
				'email_title' => 'input',
				'content' => 'textarea',
			),
		);
		foreach ( $email_options as $title => $fields ) :
			add_settings_section(
				'moove_email_options_'.$title, // ID.
				ucfirst( str_replace( '-', ' ', $title ) ), // Title.
				array( &$this, 'moove_print_email_info_'.strtolower( $title ) ), // Callback.
				'moove-protection-email' // Page.
			);
			foreach ( $fields as $field_title => $field_type ) :
				add_settings_field(
					$field_title,
					ucfirst( str_replace( '_', ' ', $field_title ) ),
					array( &$this, 'moove_create_email_settings' ),
					'moove-protection-email',
					'moove_email_options_'.$title,
					array( 'current_type' => $field_type, 'parent_title' => $title, 'field_title' => $field_title )
				);
			endforeach;
		endforeach;
		$validation_options = array(
			'Sign-up' => array(
				'first-name' => array( 'required' ),
				'last-name' => array( 'required' ),
				'email' => array( 'required', 'invalid-email', 'already-registered' ),
				'password' => array( 'required', 'min-length', 'equal-to' ),
			),
			'Login' => array(
				'ajax-message' => array( 'signing-in', 'invalid-login' ),
				'email' => array( 'required', 'invalid-email' ),
				'password' => array( 'required' ),
				'lost-mail' => array( 'required', 'invalid-email', 'nonexistent-email' ),
			),
			'Reset' => array(
				'password' => array( 'required', 'min-length', 'equal-to' ),
			),
		);
		foreach ( $validation_options as $title => $fields ) :
			add_settings_section(
				'moove_validate_options_'.$title, // ID.
				ucfirst( str_replace( '-', ' ', $title ) ), // Title.
				array( &$this, 'moove_print_validation_info' ), // Callback.
				'moove-protection-validation' // Page.
			);
			foreach ( $fields as $field_title => $field_childs ) :
				add_settings_field(
					$field_title,
					ucfirst( str_replace( '-', ' ', $field_title ) ),
					array( &$this, 'moove_create_validation_settings' ),
					'moove-protection-validation',
					'moove_validate_options_'.$title,
					array( 'fields' => $field_childs, 'parent_title' => $title, 'field_title' => $field_title )
				);
			endforeach;
		endforeach;
		$protection_settings = array(
			'Free-membership' => array(
				'modal-content' => 'textarea',
				'protection-message' => 'input',
				'truncate-button-text' => 'input',
				'truncate-button-link' => 'input',
			),
			'Premium-membership' => array(
				'modal-content' => 'textarea',
				'protection-message' => 'input',
				'truncate-button-text' => 'input',
				'truncate-button-link' => 'input',
			),
		);
		foreach ( $protection_settings as $title => $fields ) :
			add_settings_section(
				'moove_protection_options_'.$title, // ID.
				ucfirst( str_replace( '-', ' ', $title ) ), // Title.
				array( &$this, 'moove_print_protection_info' ), // Callback.
				'moove-protection-settings' // Page.
			);
			foreach ( $fields as $field_title => $field_type ) :
				add_settings_field(
					$field_title,
					ucfirst( str_replace( '-', ' ', $field_title ) ),
					array( &$this, 'moove_create_protection_settings' ),
					'moove-protection-settings',
					'moove_protection_options_'.$title,
					array( 'current_type' => $field_type, 'parent_title' => $title, 'field_title' => $field_title )
				);
			endforeach;
		endforeach;
	}
	/**
	 * Setting page for emails
	 *
	 * @param  array $args Data array to view.
	 */
	public function moove_create_email_settings( $args ) {
		echo Moove_View::load(
			'moove.admin.settings.email',
			array(
				'current_type'  => $args['current_type'],
				'parent_title'  => $args['parent_title'],
				'field_title'   => $args['field_title'],
				'options'       => get_option( 'moove_protection-email' ),
			)
		);
	}
	/**
	 * Setting page for validation messages
	 *
	 * @param  array $args Data array to view.
	 */
	public function moove_create_validation_settings( $args ) {
		echo Moove_View::load(
			'moove.admin.settings.validation',
			array(
				'fields'        => $args['fields'],
				'parent_title'  => $args['parent_title'],
				'field_title'   => $args['field_title'],
				'options'       => get_option( 'moove_protection-validation' ),
			)
		);
	}
	/**
	 * Setting page for protection settings
	 *
	 * @param  array $args Data array to view.
	 */
	public function moove_create_protection_settings( $args ) {
		echo Moove_View::load(
			'moove.admin.settings.protection_settings',
			array(
				'current_type'  => $args['current_type'],
				'parent_title'  => $args['parent_title'],
				'field_title'   => $args['field_title'],
				'options'       => get_option( 'moove_protection-settings' ),
			)
		);
	}
	/**
	 * Setting page for post type protection settings
	 *
	 * @param  array $args Data array to view.
	 */
	public function moove_setting_callback( $args ) {
		echo Moove_View::load(
			'moove.admin.settings.post_type',
			array(
				'post_type' => $args['post_type'],
				'protection_type' => sanitize_text_field( wp_unslash( $args['protection_type'] ) ),
				'options' => $this->options,
			)
		);
	}
	/**
	 * Show section info on back-end option sections
	 *
	 * @return string Message.
	 */
	public function moove_print_section_info() {
		return _e( 'This page provides a facility to set visibility options globally for post types.', 'moove' );
	}
	/**
	 * Show section info on back-end option sections
	 *
	 * @return string Message.
	 */
	public function moove_print_email_info_remind( $args ) {
		return _e( 'You can modify the email content below. <br>Please use the following tags for the right functionality: [[client_name]], [[client_email]], [[site_url]], [[reset_link]], [[blog_name]] !', 'moove' );
	}
	/**
	 * Show section info on back-end option sections
	 *
	 * @return string Message.
	 */
	public function moove_print_email_info_confirm( $args ) {
		return _e( 'You can modify the email content below. <br>Please use the following tags for the right functionality: [[client_name]], [[client_email]], [[blog_name]], [[register_page]] !', 'moove' );
	}
	/**
	 * Show section info on back-end option sections
	 *
	 * @return string Message.
	 */
	public function moove_print_validation_info() {
		return _e( 'This page provides a facility to set up validation error messages.', 'moove' );
	}
	/**
	 * Show section info on back-end option sections
	 *
	 * @return string Message.
	 */
	public function moove_print_protection_info() {
		return _e( 'You can modify the modal content below!', 'moove' );
	}
}
$moove_protection_options = new Moove_Protection_Options();
