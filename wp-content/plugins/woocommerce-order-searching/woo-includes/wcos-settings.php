<?php
class WC_Settings_Order_Search {

    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', array(__CLASS__ , 'add_settings_tab'), 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_order_search',  array(__CLASS__ , 'settings_tab') );
        add_action( 'woocommerce_update_options_settings_tab_order_search', array(__CLASS__ , 'update_settings') );
		add_action( 'woocommerce_admin_settings_sanitize_option', array(__CLASS__ , 'settings_sanitize_option'), 10, 3 );
    }
    public static function settings_sanitize_option( $value, $option, $raw_value){
		if($option['type'] == 'multiselect_text'){
			$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
		}
		return $value;
	}

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_order_search'] = __( 'Order Search', 'wc-ost' );
        return $settings_tabs;
    }


    public static function settings_tab() {
       // woocommerce_admin_fields( self::get_settings() ); 
	   self::admin_template_options();
    }


    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
        self::process_admin_options();
    }

    public static function get_settings($key = "") {
		global $wp_roles;
     	$roles = $wp_roles->get_names();
		
		
		$settings = array();
		if(empty($key)){
			$settings = array(
					'section_title' => array(
						'name'     => __( 'Order search settings', 'wc-ost' ),
						'type'     => 'title',
						'desc'     => '',
						'id'       => 'wc_settings_tab_order_search_section_title'
					),
				);
		}
			
		if($key == "search_page" || empty($key)){
			$settings = array_merge($settings,array(
				array(
					'title'    => __( 'Order search/listing page', 'wc-ost' ),
					'desc'     => '<br/>' . sprintf( __( 'The base page can also be used for dispaly search form and search list .', 'wc-ost' ) ),
					'id'       => 'woocommerce_order_search_page_id',
					'type'     => 'single_select_page',
					'default'  => '',
					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'This sets the base page of your shop - this is where your product archive will be.', 'wc-ost' ),
				),
				array(
					'title'    => __( 'Select role', 'wc-ost' ),
					'desc'     => '<br/>' . sprintf( __( 'Select user role can view search page.', 'wc-ost' ) ),
					'id'       => 'woocommerce_order_search_allowd_role',
					'type'     => 'multiselect_text',
					'default'  => array('administrator'),
					'options' => $roles
				),

			));
		}
		
		if($key == "order_search_form" || empty($key)){
			$settings = array_merge($settings,array(
				array(
					'title'   => __( 'Select search form allowed keys', 'wc-ost' ),
					'desc'    => '',
					'id'      => 'woocommerce_search_allowed_keys',
					'css'     => 'min-width: 350px;',
					'default' => '',
					'type'    => 'multiselect_text',
					'options' => get_serach_fields('all')
				),
			));
		}
		if(empty($key)){
			$settings = array_merge($settings,array(
				'section_end' => array(
					 'type' => 'sectionend',
					 'id' => 'wc_settings_tab_order_search_section_end'
				)
			));
		}
		
		
        return apply_filters( 'wc_settings_tab_order_search_settings', $settings );
    }


	public static function generate_settings_html(){
		$html = '';
		$form_fields = self::get_settings();
		foreach ( $form_fields as $k => $v ) {

			if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) ) {
				$v['type'] = 'text'; // Default to "text" field type.
			}

			if ( method_exists( $this, 'generate_' . $v['type'] . '_html' ) ) {
				$html .= $this->{'generate_' . $v['type'] . '_html'}( $k, $v );
			} else {
				$html .= $this->{'generate_text_html'}( $k, $v );
			}
		}

		echo $html;
	}
	
	public static function process_admin_options() {
		$templates = array(
			'order-search'	=> 'wcos-order-search-page.php',
			'order-single'	=> 'wcos-order-single-page.php',
		);
		foreach($templates as $template=>$template_name){
			$local_file     = get_stylesheet_directory() . '/order_search/' . $template_name;
			$core_file      = PLUGIN_PATH .'/templates/'. $template_name;
			$template_file  = apply_filters( 'woocommerce_locate_core_template', $core_file, $template_name, PLUGIN_PATH .'/templates/' );

			if ( ! empty( $_REQUEST[$template."_code"] ) ) {
	
				$saved  = false;
				$file   = $local_file;
				$code   = stripslashes( $_REQUEST[$template."_code"] );
	
				if ( is_writeable( $file ) ) {
	
					$f = fopen( $file, 'w+' );
	
					if ( $f !== FALSE ) {
						fwrite( $f, $code );
						fclose( $f );
						$saved = true;
					}
				}
	
				if ( ! $saved ) {
					$redirect = add_query_arg( 'wc_error', urlencode( __( 'Could not write to template file.', 'wc-ost' ) ) );
					wp_redirect( $redirect );
					exit;
				}
			}
		}
	}
	
	public static function get_template_editer($template_key){
		
		
		$templates = array(
			'order-search-page'	=> 'wcos-order-search-page.php',
			'order-search-form'	=> 'wcos-order-search-form.php',
			'order-search-list'	=> 'wcos-order-search-list.php',
		);
		
		if(!isset($templates[$template_key]))
			return false;
			
		$template_name = $templates[$template_key];
		$local_file     = get_stylesheet_directory() . '/order_search/' . $template_name;
		$core_file      = PLUGIN_PATH .'/templates/'. $template_name;
		$template_file  = apply_filters( 'woocommerce_locate_core_template', $core_file, $template_name, PLUGIN_PATH .'/templates/' );
		
		$template = $template_key;

		
		if ( ! empty( $_GET['move_template'] ) && ( $template == esc_attr( basename( $_GET['move_template'] ) ) ) ) {
			if ( ! empty( $template_name ) ) {
				if (  wp_mkdir_p( dirname( $local_file  ) ) && ! file_exists( $local_file ) ) {
					// Locate template file
					$template_file	= apply_filters( 'woocommerce_order_search_locate_core_template', $core_file, $template_name, PLUGIN_PATH .'/templates/' );
					// Copy template file
					copy( $template_file, $local_file );
					//echo '<div class="updated fade"><p>' . __( 'Template file copied to theme.', 'wc-ost' ) . '</p></div>';
				}
			}
		}

		if ( ! empty( $_GET['delete_template'] ) && ( $template == esc_attr( basename( $_GET['delete_template'] ) ) ) ) {
			if ( ! empty($template_name) ) {
				if ( file_exists($local_file) ) {
					unlink( $local_file);
					//echo '<div class="updated fade"><p>' . __( 'Template file deleted from theme.', 'wc-ost' ) . '</p></div>';
				}
			}
		}
		?>
		<div id="template" class="template_<?php $template ?>">
         	<div class="template <?php echo $template; ?>">
             	<h4><?php echo ucfirst(str_replace('-', ' ',$template_key)); ?> template</h4>
                    <?php if ( file_exists( $local_file ) ) { ?>
                        <p>
                            <a href="#" class="button toggle_editor"></a>

                            <?php if ( is_writable( $local_file ) ) : ?>
                                <a href="<?php echo remove_query_arg( array( 'move_template', 'saved' ), add_query_arg( 'delete_template', $template ) ); ?>" class="delete_template button"><?php _e( 'Delete template file', 'woocommerce' ); ?></a>
                            <?php endif; ?>

                            <?php printf( __( 'This template has been overridden by your theme and can be found in: <code>%s</code>.', 'woocommerce' ), 'yourtheme/order_search/' . $template_name ); ?>
                        </p>

                        <div class="editor" style="display:none">
                            <textarea name="template_html_code"  class="code" cols="25" rows="20" <?php if ( ! is_writable( $local_file ) ) : ?>readonly="readonly" disabled="disabled"<?php else : ?>data-name="<?php echo $template . '_code'; ?>"<?php endif; ?>><?php echo file_get_contents( $local_file ); ?></textarea>
                        </div>

                    <?php } elseif ( file_exists( $template_file ) ) { ?>

                        <p>
                            <a href="#" class="button toggle_editor"></a>

                            <?php if ( ( is_dir( get_stylesheet_directory() . '/order_search/' ) && is_writable( get_stylesheet_directory() . '/order_search/' ) ) || is_writable( get_stylesheet_directory() ) ) { ?>
                                <a href="<?php echo remove_query_arg( array( 'delete_template', 'saved' ), add_query_arg( 'move_template', $template ) ); ?>" class="button"><?php _e( 'Copy file to theme', 'woocommerce' ); ?></a>
                            <?php } ?>

                            <?php printf( __( 'To override and edit this template copy <code>%s</code> to your theme folder: <code>%s</code>.', 'woocommerce' ), plugin_basename( $template_file ) , 'yourtheme/order_search/' . $template_name ); ?>
                        </p>

                        <div class="editor" style="display:none">
                            <textarea name="template_html_code" class="code" readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents( $template_file ); ?></textarea>
                        </div>

                    <?php } else { ?>

                        <p><?php _e( 'File was not found.', 'woocommerce' ); ?></p>

                    <?php } ?>

                </div>
                <?php
        ?>
        </div><?php
	}
	
	public static function admin_template_options() {?>
		<h3>Oreder Search Page</h3>
        <table class="form-table">
			<?php  WC_Admin_Settings::output_fields(self::get_settings("search_page")); ?>
		</table>
       	<?php self::get_template_editer('order-search-page');?>
        
        <hr /><h3>Oreder Search from</h3>
        <table class="form-table">
			<?php  WC_Admin_Settings::output_fields(self::get_settings("order_search_form")); ?>
		</table>
        <?php self::get_template_editer('order-search-form');?>
         
        <hr /><h3>Oreder Search list</h3>
        <table class="form-table">
			<?php  WC_Admin_Settings::output_fields(self::get_settings("order_search_list")); ?>
		</table>
        <?php self::get_template_editer('order-search-list');?>
        
        
		<?php
        wc_enqueue_js("
            var view = '" . esc_js( __( 'View template', 'woocommerce' ) ) . "';
            var hide = '" . esc_js( __( 'Hide template', 'woocommerce' ) ) . "';

            jQuery('a.toggle_editor').text( view ).toggle( function() {
                jQuery( this ).text( hide ).closest('.template').find('.editor').slideToggle();
                return false;
            }, function() {
                jQuery( this ).text( view ).closest('.template').find('.editor').slideToggle();
                return false;
            } );

            jQuery('a.delete_template').click(function(){
                var answer = confirm('" . esc_js( __( 'Are you sure you want to delete this template file?', 'woocommerce' ) ) . "');

                if (answer)
                    return true;

                return false;
            });

            jQuery('.editor textarea').change(function(){
                var name = jQuery(this).attr( 'data-name' );

                if ( name )
                    jQuery(this).attr( 'name', name );
            });
        ");
	}
}
WC_Settings_Order_Search::init();