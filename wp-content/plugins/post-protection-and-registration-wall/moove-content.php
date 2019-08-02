<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Moove_Custom_Content File Doc Comment
 *
 * @category Moove_Custom_Content
 * @package   moove-protection-plugin
 * @author    Gaspar Nemes
 */

load_textdomain( 'moove', plugins_url( __FILE__ ).DIRECTORY_SEPARATOR.'languages' );

/**
 * Moove_Custom_Content Class Doc Comment
 *
 * @category Class
 * @package  Moove_Custom_Content
 * @author   Gaspar Nemes
 */
class Moove_Custom_Content {
	/**
	 * Construct functions
	 */
	function __construct() {
		$this->moove_register_content_elements();
	}
	/**
	 * Register actions and filters
	 */
	function moove_register_content_elements() {
		// Custom meta box for protection.
		add_action( 'add_meta_boxes', array( &$this, 'moove_protection_meta_boxes' ) );
		add_action( 'save_post', array( &$this, 'moove_save_protection_metabox' ) );
		add_filter( 'the_content', array( &$this, 'moove_protect_content' ) );
		add_action( 'wp_head', array( &$this, 'moove_add_protection_modal' ) );
	}
	/**
	 * ADD the protection modal to the content
	 */
	function moove_add_protection_modal() {
		$post = $GLOBALS['post'];
		$protection_selected = get_post_meta( $post->ID, 'moove_post_protect_data', true );
		$modal_open = array(
			'modal-free'	=> '',
			'modal-premium'	=> '',
		);
		if ( empty( $protection_selected ) ) :
			$post_type = $post->post_type;
			$options = get_option( 'moove_post_protect' );
			if ( isset( $options[ $post_type ] ) ) :
				$protection_selected = $options[ $post_type . '_protection_type' ];
			endif;
		endif;
		if ( ! is_user_logged_in() && ! moove_is_public( $post ) && ! moove_is_premium( $post ) ) :
			if ( $protection_selected === 'protection_modal' && ! current_user_can( 'edit_posts' ) ) :
				$modal_open['modal-free'] = 'modal-open';
			endif;
		endif;
		if ( moove_is_premium( $post ) ) :
			if ( $protection_selected === 'protection_modal' && ! current_user_can( 'edit_posts' ) ) :
				$modal_open['modal-premium'] = 'modal-open';
			endif;
		endif;

		if ( is_single( $post->ID ) ) :
			echo Moove_View::load( 'moove.modal', $modal_open );
		endif;
	}
	/**
	 * Adding META-BOX for protection
	 */
	function moove_protection_meta_boxes() {
		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $post_type ) :
	   		add_meta_box(
	   			'moove_item_protection',
	   			'Post protection',
	   			array( &$this, 'moove_protection_metabox_callback' ),
	   			$post_type,
	   			'side',
	   			'high'
	   		);
		endforeach;
	}
	/**
	 * Creating META-BOX for pages/posts for protection
	 *
	 * @param obj $post Post object.
	 */
	function moove_protection_metabox_callback( $post ) {
		wp_nonce_field( 'moove_item_protection_data', 'moove_item_protection_nonce' );
		$value = get_post_meta( $post->ID, 'moove_protection_level', true );
		$protection_selected = get_post_meta( $post->ID, 'moove_post_protect_data', true );
		$post_type = $post->post_type;
		$options = get_option( 'moove_post_protect' );
		$post_type_default = -1;

		if ( isset( $options[ $post_type ] ) ) :
			$post_type_default = $options[ $post_type ];
		endif;

		if ( ! $value ) :
			$value = -1;
		endif;

		$protection_type_default = -1;
		if ( isset( $options[ $post_type.'_protection_type' ] ) ) :
			$protection_type_default = $options[ $post_type.'_protection_type' ];
		endif;

		$def_value_text = 'Public';

		$prot_type_text = 'Protection Modal';
		switch ( $protection_type_default ) {
			default:
			case '-1':
			case 'protection_modal':
				$prot_type_text = __( 'Protection Modal', 'moove' );
			break;
			case 'protection_truncated':
				$prot_type_text = __( 'Truncated Content', 'moove' );
			break;
		}
		if ( $protection_selected === '' ) :
			$protection_selected = $options[ $post_type.'_protection_type' ];
		endif;
		switch ( $post_type_default ) {
			default:
			case '-1':
			case '1':
				$def_value_text = __( 'Public', 'moove' );
			break;
			case '2':
				$def_value_text = __( 'Free membership' , 'moove' );
			break;
			case '3':
				$def_value_text = __( 'Premium membership' , 'moove' );
			break;
		}

		echo Moove_View::load(
			'moove.admin.protection_metabox',
			array(
				'moove_protection_level' 	=> $value,
				'def_value_text' 			=> $def_value_text,
				'prot_type_text'			=> $prot_type_text,
				'protection_selected'		=> $protection_selected,
			)
		);
	}
	/**
	 * Save META-BOX data
	 *
	 * @param int $post_id Post ID
	 */
	function moove_save_protection_metabox( $post_id ) {
		$nonce = sanitize_key( $_POST['moove_item_protection_nonce'] );

		if ( ! isset( $nonce ) ) :
			return;
		endif;

		if ( ! wp_verify_nonce( $nonce , 'moove_item_protection_data' ) ) :
			return;
		endif;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) :
			return;
		endif;

		$moove_protection_data = intval( $_POST['moove_protection_data'] );
		if ( ! isset( $moove_protection_data ) ) :
			return;
		endif;

		if ( ! $moove_protection_data ) : $moove_protection_data = ''; endif;

		update_post_meta( $post_id, 'moove_protection_level', $moove_protection_data );

		$moove_post_protect_data = sanitize_text_field( wp_unslash( $_POST['moove_post_protect_data'] ) );
		if ( ! isset( $moove_post_protect_data ) ) :
			return;
		endif;

		if ( ! $moove_post_protect_data ) : $moove_post_protect_data = ''; endif;

		update_post_meta( $post_id, 'moove_post_protect_data', $moove_post_protect_data );
	}
	/**
	 * Content protection, returns the trimmed content if is protected.
	 *
	 * @param  string $content Content string.
	 */
	function moove_protect_content( $content ) {
		$moove_user = new Moove_User();
		$u = $moove_user->moove_check();
		$post = $GLOBALS['post'];
		$protection_selected = get_post_meta( $post->ID, 'moove_post_protect_data', true );

		if ( empty( $protection_selected ) ) :
			$post_type = $post->post_type;
			$options = get_option( 'moove_post_protect' );
			if ( isset( $options[ $post_type ] ) ) :
				$protection_selected = $options[ $post_type ];
			endif;
		endif;

		if ( ! $u['wp_admin'] || ! $u['editor'] ) :
			if ( ! is_admin() && ! current_user_can( 'edit_posts' ) ) :
				$post_level = moove_post_protection_level( $post );
				if ( ! is_user_logged_in() && ! moove_is_public( $post ) && ! moove_is_premium( $post ) ) :
					$trimmed = wp_trim_words( $post->post_content, $num_words = 55, $more = null );
					$content = $trimmed;
					$content .= Moove_View::load( 'moove.protected.truncated.free_membership_restriction' );
				endif;
				if ( moove_is_premium( $post ) ) :
					$trimmed = wp_trim_words( $post->post_content, $num_words = 55, $more = null );
					$content = $trimmed;
					$content .= Moove_View::load( 'moove.protected.truncated.premium_membership_restriction' );
				endif;
			endif;
		endif;
		return $content;
	}
}
$moove_custom_content_provider = new Moove_Custom_Content();
