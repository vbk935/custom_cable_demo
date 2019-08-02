<?php
class WC_Settings_Tab {

    //Bootstraps the class and hooks actions & filters.
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_memberpress_tab', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_memberpress_tab', __CLASS__ . '::update_settings' );
    }
    
    
    // Add a new settings tab to the WooCommerce settings tabs array.
    public static function add_settings_tab( $settings_tabs ) 
    {
        $settings_tabs['memberpress_tab'] = __( 'Settings', 'woocommerce-memberpress-tab' );
        return $settings_tabs;
    }
     
     
    
	//Uses the WooCommerce admin fields API to output settings.
    public static function settings_tab() {
       woocommerce_admin_fields( self::get_settings() );
       
    }
    
	//Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
    
	//Get all the settings for this plugin for @see woocommerce_admin_fields() function.
    public static function get_settings() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Configuration Page Settings', 'woocommerce-memberpress-tab' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'section_title'
            ),
            'canvas_title' => array(
                'name' => __( 'Title', 'woocommerce-memberpress-tab' ),
                'type' => 'text',
                'desc' =>'Canvas title',
                'id'   => 'canvas_title',
               'css'     => 'min-width:100%;'
               
            ),
            'watermark_logo_url' => array(
                'name' => __( 'Watermark Logo', 'woocommerce-memberpress-tab' ),
                'type' => 'text',
                 'desc' =>'Add Url for Branding Watermark Logo',
               'id'   => 'watermark_logo_url',
               'css'     => 'min-width:100%;'
               
            ),  
                
               'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wpmc_memberpress_tab_end'
            )
        );
        return apply_filters( 'woocommerce-memberpress-tab', $settings );
    }
}
WC_Settings_Tab::init();
