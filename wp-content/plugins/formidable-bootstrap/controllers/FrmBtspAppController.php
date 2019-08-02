<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

if(class_exists('FrmBtspAppController'))
    return;

class FrmBtspAppController{
    function __construct(){
        add_action('admin_init', array(__CLASS__, 'include_updater'), 1);
        add_action('frm_style_general_settings', array(__CLASS__, 'general_style_settings'), 20);
        add_action('frm_update_settings', array(__CLASS__, 'update_global_settings') );
        add_action('frm_field_options_form', array(__CLASS__, 'field_options'), 10, 3);
        add_filter('frm_default_field_opts', array(__CLASS__, 'default_field_opts'), 10, 3);
        
        add_action('frm_form_classes', array(__CLASS__, 'form_class'));
        add_filter('frm_form_fields_class', array(__CLASS__, 'form_fields_class'));
        add_filter('frm_cpt_field_classes', array(__CLASS__, 'form_fields_class'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'front_head'));
        add_filter('frm_checkbox_class', array(__CLASS__, 'inline_class'), 10, 2);
        add_filter('frm_radio_class', array(__CLASS__, 'inline_class'), 10, 2);
        add_filter('frm_form_replace_shortcodes', array(__CLASS__, 'form_html'), 10, 2);
        add_filter('frm_before_replace_shortcodes', array(__CLASS__, 'field_html'), 30, 2);
        
        add_filter('frm_field_classes', array(__CLASS__, 'field_classes'), 10, 2);
        add_filter('frm_submit_button_class', array(__CLASS__, 'submit_button'));
        add_filter('frm_back_button_class', array(__CLASS__, 'back_button'));
        
        add_filter('frm_ul_pagination_class', array(__CLASS__, 'pagination_class'));
    }
    
    public static function path(){
        return dirname(dirname(__FILE__));
    }
    
    public static function include_updater() {
		if ( class_exists( 'FrmAddon' ) ) {
			include( self::path() .'/models/FrmBtspUpdate.php' );
			FrmBtspUpdate::load_hooks();
		}
    }
    
    /*
    * Add option to not load styling
    */
    public static function general_style_settings($frm_settings) {
        if ( $_POST && isset($_POST['frm_btsp_css']) ) {
            $frm_settings->btsp_css = $_POST['frm_btsp_css'];
            $frm_settings->btsp_errors = isset($_POST['frm_btsp_errors']) ? 1 : 0;
        } else {
            if ( !isset($frm_settings->btsp_css) ){
                $frm_settings->btsp_css = 'all';
            }
            
            if ( !isset($frm_settings->btsp_errors) ){
                $frm_settings->btsp_errors = 0;
            }
        }
        
        include( self::path() .'/views/style_settings.php');
    }
    
    public static function update_global_settings($params) {
        global $frm_settings;
        $frm_settings->btsp_css = $params['frm_btsp_css'];
        $frm_settings->btsp_errors = isset($params['frm_btsp_errors']) ? 1 : 0;
    }
    
    public static function field_options($field, $display, $values){
        $default = array('prepend' => '', 'append' => '');
        if(empty($field['btsp']) or !is_array($field['btsp'])){
            $field['btsp'] = $default;
        }else{
            foreach($default as $k => $v){
                if(!isset($field['btsp'][$k]))
                    $field['btsp'][$k] = $v;
                unset($k);
                unset($v);
            }
        }
        
        include(self::path() .'/views/field_options.php');
    }
    
    public static function default_field_opts($opts, $values, $field){
        $opts['btsp'] = '';
        return $opts;
    }
    
    public static function form_class($form){
        //echo ' form-inline';
    }
    
    public static function form_fields_class($classes) {
        if ( is_array($classes) ) {
            $classes[] = 'form-group';
        } else {
            $classes .= ' form-group';
        }
        
        return $classes;
    }
    
    public static function front_head() {
        if ( is_admin() && !defined('DOING_AJAX') ) {
            return;
        }
        
        global $frm_settings;
        if ( !isset($frm_settings->btsp_errors) ) {
            $frm_settings->btsp_errors = 0;
        }
        
        wp_register_script('frmbtsp', plugins_url('js/frmbtsp.js', dirname(__FILE__)),  array('formidable'), '1.0', true);
        wp_localize_script('frmbtsp', 'frmbtsp', array(
            'show_error'  => $frm_settings->btsp_errors,
        ));
        add_action('frm_enqueue_form_scripts', array(__CLASS__, 'enqueue_script'));
        
        if ( !isset($frm_settings->btsp_css) ) {
            $frm_settings->btsp_css = 'all';
        }
        
        if ( 'none' == $frm_settings->btsp_css ) {
            return;
        }
        
		wp_register_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css', array(), '3.3.6');
        
        if ( 'all' == $frm_settings->btsp_css ) {
            // load on all pages
            wp_enqueue_style('bootstrap');
        } else {
            // load on form pages
            add_action('frm_enqueue_form_scripts', array(__CLASS__, 'enqueue_style'));
        }
    }
    
    public static function enqueue_style() {
        wp_enqueue_style('bootstrap');
    }
    
    public static function enqueue_script(){
        wp_enqueue_script('frmbtsp');
    }
    
    public static function inline_class($class, $field){
        $type = $field['type'];

	    if ( $field['type'] == 'data' ) {
		    $type = $field['data_type'];
	    } else if ( $field['type'] == 'lookup' ) {
		    $type = $field['data_type'];
	    }
        
        if(isset($field['align']) and $field['align'] == 'inline')
            $class .= ' '. $type .'-inline';
            
        $class .= ' '. $type;
        
        return $class;
    }
    
    public static function form_html($html, $form){
        $html = str_replace('frm_submit', 'form-group frm_submit', $html);
        $html = str_replace('class="frm_prev_page', 'class="frm_prev_page btn btn-default', $html);
        return $html;
    }
    
    public static function field_html($html, $field){
        $class = '[required_class] form-group';
        if ( isset($field['btsp']) && ! empty($field['btsp']) && is_array($field['btsp']) ) {
            
            if ( ( isset($field['btsp']['prepend']) && ! empty($field['btsp']['prepend']) ) || ( isset($field['btsp']['append']) && ! empty($field['btsp']['append']) ) ) {

                preg_match_all( "/\[(input)\b(.*?)(?:(\/))?\]/s", $html, $matches, PREG_PATTERN_ORDER);
                foreach ( $matches[0] as $match_key => $val ) {
                    $html = str_replace($val, '<div class="input-group">'. $val .'</div>', $html);
                }
                
                if ( ! empty($field['btsp']['prepend']) ) {
                    $html = str_replace('[input', '<span class="input-group-addon">'. $field['btsp']['prepend'] .'</span> [input', $html);
                }
                if ( ! empty($field['btsp']['append']) ) {
					preg_match_all( '/\[input\b(.*?)(?:(\/))?\]/s', $html, $matches, PREG_PATTERN_ORDER );
					$input = '[input]';
					if ( isset( $matches[0] ) && isset( $matches[0][0] ) ) {
						$input = $matches[0][0];
					}
					$html = str_replace( $input, $input . ' <span class="input-group-addon">' . $field['btsp']['append'] .'</span>', $html );
                }
            }
        }
        
        $html = str_replace('frm_primary_label', 'frm_primary_label control-label', $html);
        $html = str_replace('frm_description', 'frm_description help-block', $html);
        
        $html = str_replace('[required_class]', $class, $html);
        return $html;
    }
    
	public static function field_classes( $class, $field ) {
		if ( ! in_array( $field['type'], array( 'radio', 'checkbox', 'data', 'file', 'scale', 'lookup' ) ) ) {
			$class .= ' form-control';
		} else if ( 'data' == $field['type'] && isset( $field['data_type'] ) && 'select' == $field['data_type'] ) {
			$class .= ' form-control';
		} else if ( 'lookup' == $field['type'] && isset( $field['data_type'] ) && 'select' == $field['data_type'] ) {
			$class .= ' form-control';
		}
        
		return $class;
	}
    
    public static function submit_button($class){
        $class[] = 'btn btn-default';
        return $class;
    }
    
    public static function back_button($class){
        $class[] = 'btn';
        return $class;
    }
    
    public static function pagination_class($class){
        if(is_array($class)) {
            $class[] = 'pagination';
        } else {
            $class .= ' pagination';
        }
        return $class;
    }
    
}