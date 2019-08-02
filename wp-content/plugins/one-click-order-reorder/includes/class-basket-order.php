<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'Basket_Order' ) ) {
	class Basket_Order {

		/**
		 * Global variable for current user capabilities.
		 */
		public $selectedUsers 	= array();

		/**
		 * Defines constructor of the clas.
		 */
		function __construct() {

			/**
			 * Array of pages for basket.
			 */
			$this->basket_pages = apply_filters( 
				'ced_ocor_basket_pages', 
				array(
					'shop' 		=> __( 'Shop', 'domain' ),
					'detail' 	=> __( 'Product Detail', 'domain' ),
					'cart' 		=> __( 'Cart', 'domain' ),
					'account' 	=> __( 'My Account', 'domain' )
				) 
			);

			/**
			 * Admin initialization process calling.
			 */
			add_action( 'admin_init', array( $this, 'ced_ocor_initialize_settings' ) );

			/**
			 * Frontend initialization calling at wp_head
			 */
			add_action( 'wp_head', array( $this, 'ced_ocor_initialize_frontend' ) );

			/**
			 * Creates admin menu pages.
			 */
			add_action( 'admin_menu', array( $this, 'ced_ocor_add_settings_page' ) );

			/**
			 * Admin setting html generation.
			 */
			add_action( 'ced_ocor_general_settings_html_content', array( $this, 'ced_ocor_general_settings_html_content' ), 10 );

			/**
			 * Ajax process calling.
			 */
			add_action( 'wp_ajax_ced_ocor_save_general_setting', array( $this, 'ced_ocor_save_general_setting' ) );
			add_action( 'wp_ajax_ced_ocor_add_to_basket', array( $this, 'ced_ocor_add_to_basket' ) );
			add_action( 'wp_ajax_ced_ocor_remove_from_basket', array( $this, 'ced_ocor_remove_from_basket' ) );
			add_action( 'wp_ajax_ced_ocor_add_basket_items_to_cart', array( $this, 'ced_ocor_add_basket_items_to_cart' ) );
			add_action( 'wp_ajax_ced_ocor_get_basket_items', array( $this, 'ced_ocor_get_basket_items' ) );
			add_action( 'wp_ajax_ced_ocor_get_attchment_icon_info', array( &$this, 'ced_ocor_get_attchment_icon_info' ), 10, 2 );
			
			/**
			 * Invokes footer process.
			 */
			add_action( 'wp_footer', array( $this, 'ced_ocor_footer_stuffs' ) );

			/**
			 * Updates attchment content calling when a new icon is added.
			 */
			add_action( 'update_attached_file', array( &$this, 'ced_ocor_action_attachment_updated' ), 10, 2 );
		}

		/**
		 * Admin initalization.
		 *
		 * Sets global variable selectedUsers
		 */
		public function ced_ocor_initialize_settings() {
			$generalSettings = get_option( 'ced_ocor_general_settings', false );
			if ( !empty( $generalSettings ) ) {
				$this->selectedUsers 	= $generalSettings[ 'selectedUsers' ];
			}
		}

		/**
		 * Add to basket and Remove from basket setting invoking.
		 */
		public function ced_ocor_initialize_frontend() {
			if ( ! is_user_logged_in() ) {
				return;
			}

 			$current_user =	wp_get_current_user();
			if ( empty( $current_user ) ) {
				return;
			}
			$settings 		= get_option( 'ced_ocor_general_settings', false );
			if(is_array($settings[ 'selectedUsers' ]))
			$current_user->roles = array_values( $current_user->roles );

	

			$generalSettings = get_option( 'ced_ocor_general_settings', false );

			if (( $generalSettings[ 'basketEnable' ] == 'enable' ) && (is_array($settings[ 'selectedUsers' ]) && in_array( $current_user->roles[0], $settings[ 'selectedUsers' ] ))) {
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'ced_ocor_add_to_basket_button' ) );
				add_action( 'woocommerce_after_single_variation', array( $this, 'ced_ocor_add_to_basket_button' ) );
			}
			else if (( $generalSettings[ 'basketEnable' ] == 'enable' ) && (current_user_can( 'administrator' ))) {
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'ced_ocor_add_to_basket_button' ) );
				add_action( 'woocommerce_after_single_variation', array( $this, 'ced_ocor_add_to_basket_button' ) );


			}
		}

		/**
		 * Admin setting menu page invoking
		 */
		public function ced_ocor_add_settings_page() {
			if ( is_user_logged_in() ) {
				if ( current_user_can( 'administrator' ) ) {
					$parent_slug= 'woocommerce';
					$page_title = __( 'One Click Order Re-Order', CNG_TXTDOMAIN );
					$menu_title = __( 'One Click Order Re-Order', CNG_TXTDOMAIN );
					$capability = 'manage_woocommerce';
					$menu_slug 	= 'wc-ocor-settings';
					$callable 	= array( $this, 'ced_ocor_settings_html' );
					add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callable );
				}
			}
		}

		/**
		 * Admin setting html generation start.
		 */
		public function ced_ocor_settings_html() {?>
			<div class="ced_ocor_main_wrapper">
				<div id="ced_ocor_messages"></div>
				<div id="ced_ocor_empty_messages" class="ced_cng_hide">
					<div class="error notice notice-error is-dismissible">
						<p><?php _e( 'Please fill all the fields.', CNG_TXTDOMAIN ); ?></p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', CNG_TXTDOMAIN );?></span>
						</button>
					</div>
				</div>
				<h1><?php _e( "One Click Order Reorder Settings", CNG_TXTDOMAIN ); ?></h1>
				<div class="ced_ocor_contents_wrapper">
					<?php 
					if ( ! current_user_can( 'administrator' ) ) {
						wp_die( __( "Sorry to say, you're not allowed to see this page.", CNG_TXTDOMAIN ) );
						return;
					}


					/**
					 * Admin setting html generation invoking.
					 */
					do_action( 'ced_ocor_general_settings_html_content' );
					?>
				</div>

				<?php
				if(!session_id())
					session_start();
				if(!isset($_SESSION["ced_ocor_hide_email"])):
					$actual_link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					$urlvars = parse_url($actual_link);
					$url_params = $urlvars["query"];
				?>
				<div class="ced_ocor_email_image">
					<div class="ced_ocor_email_main_content">
						<div class="ced_ocor_cross_image">
							<a class="button-primary ced_ocor_cross_image" href="?<?php echo $url_params?>&ced_ocor_close=true"></a>
						</div>
						<input type="text" value="" class="ced_ocor_img_email_field" placeholder="<?php _e("enter your e-mail address","one-click-order-reorder")?>"/> 
						<a id="ced_ocor_send_email" href=""><?php _e("Know More","one-click-order-reorder")?></a>
						<p></p>
						<div class="hide"  id="ced_ocor_loader">	
							<img id="ced-ocor-loading-image" src="<?php echo plugins_url().'/one-click-order-reorder/assets/images/ajax-loader.gif'?>" >
						</div>
						<div class="ced_ocor_banner">
						<a target="_blank" href="https://cedcommerce.com/offers#woocommerce-offers"><img src="<?php echo plugins_url().'/one-click-order-reorder/assets/images/ebay.jpg'?>"></a>
						</div>
					</div>
				</div>
				<?php endif;?>
			</div>	
		<?php 
		}


		/**
		 * Admin setting html generation.
		 */
		public function ced_ocor_general_settings_html_content() {
			$generalSettings = get_option( 'ced_ocor_general_settings', false );
			$enable_same_order_btn 	= '';
			$disable_same_order_btn = '';
			$selectedUsers 			= array();
			$basketEnable 			= '';
			$basketDisable 			= '';
			$basketPages 			= array();
			$basketSectionClass 	= '';
			$atbBtnText 			= __( 'Add to basket', CNG_TXTDOMAIN );
			$rfbBtnText 			= __( 'Remove from basket', CNG_TXTDOMAIN );

			/**
			 * manage settings is set.
			 */
			if ( !empty( $generalSettings ) ) {
				if ( $generalSettings[ 'same_order_btn' ] == 1 ) {
					$enable_same_order_btn = 'checked';
				} else {
					$disable_same_order_btn = 'checked';
				}
				$selectedUsers 			= $generalSettings[ 'selectedUsers' ];
				$basketEnable 			= $generalSettings[ 'basketEnable' ] == 'enable' ? 'checked' : '';
				$basketDisable 			= $generalSettings[ 'basketEnable' ] == 'disable' ? 'checked' : '';
				$basketPages 			= $generalSettings[ 'basketPages' ];
				$basketSectionClass 	= $generalSettings[ 'basketEnable' ] == 'enable' ? '' : 'ced_cng_hide';
				$atbBtnText 			= $generalSettings[ 'atbBtnText' ] == '' ? __( 'Add to basket', CNG_TXTDOMAIN ) : $generalSettings[ 'atbBtnText' ];
				$rfbBtnText 			= $generalSettings[ 'rfbBtnText' ] == '' ? __( 'Remove from basket', CNG_TXTDOMAIN ) : $generalSettings[ 'rfbBtnText' ];
			}
			?>
			<div class="ced_ocor_general_settings_wrap">
				<div class="ced_ocor_row">
					<div class="ced_ocor_columns ced_ocor_label_wrap">
						<label for="ced_ocor_enable_same_order">
							<?php _e( 'Enable/Disable Place Same Order Button:', CNG_TXTDOMAIN ); ?>
						</label>
					</div>
					<div class="ced_ocor_columns ced_ocor_columns_content">
						<span>
							<input type="radio" name="ced_ocor_enable_same_order" class="ced_ocor_enable_same_order" value="1" <?php echo $enable_same_order_btn;?>>
							<?php _e( 'Enable', CNG_TXTDOMAIN ); ?>
						</span>
						<span>
							<input type="radio" name="ced_ocor_enable_same_order" class="ced_ocor_enable_same_order" value="0" <?php echo $disable_same_order_btn; ?>>
							<?php _e( 'Disable', CNG_TXTDOMAIN ); ?>
						</span>
					</div>
				</div>
				<div class="ced_ocor_row">
					<div class="ced_ocor_columns ced_ocor_label_wrap">
						<label>
							<?php _e( 'Enable/Disable Basket:', CNG_TXTDOMAIN ); ?>
						</label>
					</div>
					<div class="ced_ocor_columns ced_ocor_columns_content">
						<span>
							<input type="radio" name="ced_ocor_enabling_basket" class="ced_ocor_enabling_basket" value="enable" <?php echo $basketEnable;?>>
							<?php _e( 'Enable', CNG_TXTDOMAIN ); ?>
						</span>
						<span>
							<input type="radio" name="ced_ocor_enabling_basket" class="ced_ocor_enabling_basket" value="disable" <?php echo $basketDisable; ?>>
							<?php _e( 'Disable', CNG_TXTDOMAIN ); ?>
						</span>
					</div>
				</div>
				<div class="ced_ocor_row">
					<div class="ced_ocor_columns ced_ocor_label_wrap">
						<label>
							<?php _e( 'Basket add button text:', CNG_TXTDOMAIN ); ?>
						</label>
					</div>
					<div class="ced_ocor_columns ced_ocor_columns_content">
						<input type="text" id="ced_ocor_basket_btn_text" value="<?php echo $atbBtnText;?>">
					</div>
				</div>
				<div class="ced_ocor_row">
					<div class="ced_ocor_columns ced_ocor_label_wrap">
						<label>
							<?php _e( 'Basket remove button text:', CNG_TXTDOMAIN ); ?>
						</label>
					</div>
					<div class="ced_ocor_columns ced_ocor_columns_content">
						<input type="text" id="ced_ocor_basket_remove_btn_text" value="<?php echo $rfbBtnText;?>">
					</div>
				</div>
				<div class="ced_ocor_row">
					<div class="ced_ocor_columns ced_ocor_label_wrap">
						<label for="ced_ocor_icon_for_basket">
							<?php _e( 'Upload an icon image for basket icon:', CNG_TXTDOMAIN ); ?>
						</label>
					</div>
					<div id="ced_ocor_attachment_section" class="ced_ocor_columns ced_ocor_columns_content">
						<div class="ced_ocor_attachment_wrapper">
							<?php 
							$image = CEDCOMMERCE_CNG_ORDER_URL . 'assets/images/shopping-bag.png';
							$settings 		= get_option( 'ced_ocor_general_settings', false );
							
							if ( !empty( $settings[ 'iconUri' ] ) ) {
								$image = $settings[ 'iconUri' ];
							}
							?>
							<img src="<?php echo $image;?>" alt="<?php _e( 'Basket icon', CNG_TXTDOMAIN );?>">
							<input type="hidden" id="cec_ocor_saved_icon_url" value="<?php echo esc_url( $image );?>">
						</div>
						<div class="ced_ocor_attachment_action">
							<input type="button" id="ced_ocor_icon_for_basket" class="button" value="<?php _e( 'Upload', CNG_TXTDOMAIN ); ?>">
						</div>
					</div>
				</div>
				<div class="ced_ocor_row">
					<div id="ced_ocor_basket_section" class="<?php echo $basketSectionClass;?>">
						<!-- <div class="ced_ocor_row"> -->
							<div class="ced_ocor_columns ced_ocor_label_wrap">
								<label for="ced_ocor_enable_basket_for_users">
									<?php _e( 'Enable basket feature for:', CNG_TXTDOMAIN ); ?>
								</label>
							</div>
							<div class="ced_ocor_columns ced_ocor_columns_content">
								<?php 
								
								$roles = get_editable_roles();
								if ( ! empty( $roles ) ) {
									echo '<select name="ced_ocor_enable_basket_for_users" id="ced_ocor_enable_basket_for_users" class="ced_ocor_enable_basket_for" multiple="multiple" placeholder="'. __( 'Select user roles to show them basket feature.', CNG_TXTDOMAIN ) .'">';
										
										foreach ( $roles as $role => $caps ) {
											if ( $role == 'administrator' ) {
												continue;
											}

											$selected = '';
											if ( !empty( $selectedUsers ) ) {
												if ( in_array( $role, $selectedUsers ) ) {
													echo $selected = 'selected';
												}
											}

											echo '<option value="'. esc_attr( $role ) .'" '. esc_attr( $selected ) .'>'. $caps[ 'name' ] .'</option>';
										}

									echo  '</select>';
								}
								?>
							</div>
						<!-- </div> -->
						<!-- <div class="ced_ocor_row"> -->
							<div class="ced_ocor_columns ced_ocor_label_wrap">
								<label for="ced_ocor_enable_basket_for_pages">
									<?php _e( 'Choose pages to show the basket icon:', CNG_TXTDOMAIN ); ?>
								</label>
							</div>
							<div class="ced_ocor_columns ced_ocor_columns_content">
								<?php 
								if ( !empty( $this->basket_pages ) and is_array( $this->basket_pages ) ) {
									echo '<select id="ced_ocor_enable_basket_for_pages" class="ced_ocor_enable_basket_for" multiple="multiple" placeholder="'. __( 'Select pages to show the basket.', CNG_TXTDOMAIN ) .'">';
									
										foreach ( $this->basket_pages as $page => $pageName ) {
											if ( empty( $page ) or empty( $pageName ) ) {
												continue;
											}

											$select = '';
											if ( !empty( $basketPages ) ) {
												if ( in_array( $page, $basketPages ) ) {
													$select = 'selected';
												}
											}

											echo '<option value="'. esc_attr( $page ) .'" '. esc_attr( $select ) .'>'. esc_html( $pageName ) .'</option>';
										}

									echo '</select>';
								}?>
							</div>
						<!-- </div> -->
					</div>
				</div>
				<div class="ced_ocor_row">
					<input id="ced_ocor_save_general_setting" type="button" class="button button-primary" value="Save Changes">
					<span class="spinner"></span>
				</div>
			</div>
		<?php 
		}

		/**
		 * Save general setting options.
		 */
		public function ced_ocor_save_general_setting() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'nonce_check' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$same_order_btn = isset( $_POST[ 'same_order' ] ) ? $_POST[ 'same_order' ] : 1;
			$selectedUsers 	= isset( $_POST[ 'selectedUser' ] ) ? $_POST[ 'selectedUser' ] : array();
			$basketEnable 	= isset( $_POST[ 'basketEnable' ] ) ? $_POST[ 'basketEnable' ] : 'enable';
			$basketPages 	= isset( $_POST[ 'basketPages' ] ) ? $_POST[ 'basketPages' ] : array( 'shop', 'detail' );
			$atbBtnText 	= isset( $_POST[ 'atbBtnText' ] ) ? $_POST[ 'atbBtnText' ] : __( 'Add to basket', CNG_TXTDOMAIN );
			$rfbBtnText 	= isset( $_POST[ 'rfbBtnText' ] ) ? $_POST[ 'rfbBtnText' ] : __( 'Remove from basket', CNG_TXTDOMAIN );
			$iconUri 		= isset( $_POST[ 'icon_uri' ] ) ? $_POST[ 'icon_uri' ] : CEDCOMMERCE_CNG_ORDER_URL . 'assets/images/shopping-bag.png';

			$update_data = array(
				'same_order_btn'	=> $same_order_btn,
				'selectedUsers' 	=> $selectedUsers,
				'basketEnable' 		=> $basketEnable,
				'basketPages' 		=> $basketPages,
				'atbBtnText' 		=> $atbBtnText,
				'rfbBtnText' 		=> $rfbBtnText,
				'iconUri' 			=> $iconUri
			);

			$updated = update_option( 'ced_ocor_general_settings', $update_data );
			if ( $updated ) {
				wp_send_json_success( __( 'Settings successfully saved.', CNG_TXTDOMAIN ) );
				wp_die();
			} else {
				wp_send_json_error( __( "It seems you didn't change anything or something went wrong, please try again.", CNG_TXTDOMAIN ) );
				wp_die();
			}
		}

		/**
		 * Add to basket and Remove from basket action html generation.
		 */
		public function ced_ocor_add_to_basket_button() {
			global $product;

			$atbBtnText = __( 'Add to basket', CNG_TXTDOMAIN );
			$rfbBtnText = __( 'Remove from basket', CNG_TXTDOMAIN );
			$atbDisable = is_product() ? "disabled" : "";
			$rfbhidden 	= is_product() ? "ced_cng_hide" : "";


			$settings = get_option( 'ced_ocor_general_settings', false );

			if ( ! empty( $settings ) )
			{
				if ( array_key_exists( 'atbBtnText', $settings ) and ! empty( $settings[ 'atbBtnText' ] ) ) {
					$atbBtnText = $settings[ 'atbBtnText' ];
				}

				if ( array_key_exists( 'rfbBtnText', $settings ) and ! empty( $settings[ 'rfbBtnText' ] ) ) {
					$rfbBtnText = $settings[ 'rfbBtnText' ];
				}
			}

			if ( ! is_product() ) {
				if ( $product->get_type() == 'variable' ) {
					return;
				}
			}

			$oldBasket = get_user_meta( get_current_user_id(), 'ced_ocor_basket_info', true );
			
if(WC()->version<'3.0.0')
				{
			if ( is_product() ) {
				$rfbhidden = 'ced_cng_hide';
				$atbhidden = 'ced_cng_hide';
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->id, $oldBasket ) and $oldBasket[ $product->id ][ 'type' ] == 'variable' and !empty( $oldBasket[ $product->id ][ 'variations' ] ) ) {
						foreach ( $oldBasket[ $product->id ][ 'variations' ] as $var_id => $variation_array ) {
							if ( empty( $var_id ) ) {
								continue;
							}

							$rfb_html = '<p class="ced_ocor_basket">';
								$rfb_html .= sprintf(
									'<a rel="nofollow" class="ced_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s" data-variation_id="%s">%s</a>',
									esc_attr( esc_html( $rfbhidden ) ),
									esc_attr( esc_html( 'ced_ocor_rfb_btn_' . $product->id ) ),
									esc_attr( esc_html( $product->id ) ),
									esc_attr( esc_html( $product->get_sku() ) ),
									esc_attr( esc_html( get_current_user_id() ) ),
									esc_attr( esc_html( $product->get_type() ) ),
									esc_attr( esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) ) ),
									esc_attr( esc_html( $var_id ) ),
									esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) )
								);
							echo $rfb_html .= '</p>';
						}
					}

					$atb_html = '<p class="ced_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="ced_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'ced_ocor_atb_btn_' . $product->id ) ),
							esc_attr( esc_html( $product->id ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				} else {
					$atbhidden = 'ced_cng_hide';
					$atb_html = '<p class="ced_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="ced_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'ced_ocor_atb_btn_' . $product->id ) ),
							esc_attr( esc_html( $product->id ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				}
			} else {
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->id, $oldBasket ) ) {
						$rfb_html = '<p class="ced_ocor_basket">';
							$rfb_html .= sprintf(
								'<a rel="nofollow" class="ced_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
								esc_attr( esc_html( $rfbhidden ) ),
								esc_attr( esc_html( 'ced_ocor_rfb_btn_' . $product->id ) ),
								esc_attr( esc_html( $product->id ) ),
								esc_attr( esc_html( $product->get_sku() ) ),
								esc_attr( esc_html( get_current_user_id() ) ),
								esc_attr( esc_html( $product->get_type() ) ),
								esc_attr( esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) ) ),
								esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) )
							);
						echo $rfb_html .= '</p>';
						return;
					}
				}

				$atb_html = '<p class="ced_ocor_basket">';
					$atb_html .= sprintf(
						'<a rel="nofollow" class="ced_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
						esc_attr( esc_html( $atbDisable ) ),
						esc_attr( esc_html( 'ced_ocor_atb_btn_' . $product->id ) ),
						esc_attr( esc_html( $product->id ) ),
						esc_attr( esc_html( get_current_user_id() ) ),
						esc_attr( esc_html( $product->get_type() ) ),
						esc_attr( esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) ) ),
						esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) )
					);
				$atb_html .= '</p>';
				echo $atb_html;
			}
}			
else{

	if ( is_product() ) {
				$rfbhidden = 'ced_cng_hide';
				$atbhidden = 'ced_cng_hide';
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->get_id(), $oldBasket ) and $oldBasket[ $product->get_id() ][ 'type' ] == 'variable' and !empty( $oldBasket[ $product->get_id() ][ 'variations' ] ) ) {
						foreach ( $oldBasket[ $product->get_id() ][ 'variations' ] as $var_id => $variation_array ) {
							if ( empty( $var_id ) ) {
								continue;
							}

							$rfb_html = '<p class="ced_ocor_basket">';
								$rfb_html .= sprintf(
									'<a rel="nofollow" class="ced_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s" data-variation_id="%s">%s</a>',
									esc_attr( esc_html( $rfbhidden ) ),
									esc_attr( esc_html( 'ced_ocor_rfb_btn_' . $product->get_id() ) ),
									esc_attr( esc_html( $product->get_id() ) ),
									esc_attr( esc_html( $product->get_sku() ) ),
									esc_attr( esc_html( get_current_user_id() ) ),
									esc_attr( esc_html( $product->get_type() ) ),
									esc_attr( esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) ) ),
									esc_attr( esc_html( $var_id ) ),
									esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) )
								);
							echo $rfb_html .= '</p>';
						}
					}

					$atb_html = '<p class="ced_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="ced_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'ced_ocor_atb_btn_' . $product->get_id() ) ),
							esc_attr( esc_html( $product->get_id() ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				} else {
					$atbhidden = 'ced_cng_hide';
					$atb_html = '<p class="ced_ocor_basket">';
						$atb_html .= sprintf(
							'<a rel="nofollow" class="ced_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
							esc_attr( esc_html( $atbhidden ) ),
							esc_attr( esc_html( 'ced_ocor_atb_btn_' . $product->get_id() ) ),
							esc_attr( esc_html( $product->get_id() ) ),
							esc_attr( esc_html( get_current_user_id() ) ),
							esc_attr( esc_html( $product->get_type() ) ),
							esc_attr( esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) ) ),
							esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) )
						);
					$atb_html .= '</p>';
					echo $atb_html;
					return;
				}
			} else {
				if ( !empty( $oldBasket ) and is_array( $oldBasket ) ) {
					if ( array_key_exists( $product->get_id(), $oldBasket ) ) {
						$rfb_html = '<p class="ced_ocor_basket">';
							$rfb_html .= sprintf(
								'<a rel="nofollow" class="ced_ocor_rfb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-sku="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
								esc_attr( esc_html( $rfbhidden ) ),
								esc_attr( esc_html( 'ced_ocor_rfb_btn_' . $product->get_id() ) ),
								esc_attr( esc_html( $product->get_id() ) ),
								esc_attr( esc_html( $product->get_sku() ) ),
								esc_attr( esc_html( get_current_user_id() ) ),
								esc_attr( esc_html( $product->get_type() ) ),
								esc_attr( esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) ) ),
								esc_html( apply_filters( 'ced_ocor_rfb_button_text', $rfbBtnText ) )
							);
						echo $rfb_html .= '</p>';
						return;
					}
				}

				$atb_html = '<p class="ced_ocor_basket">';
					$atb_html .= sprintf(
						'<a rel="nofollow" class="ced_ocor_atb button %s" id="%s" href="javascript:void(0);" data-id="%s" data-user_id="%s" data-type="%s" title="%s">%s</a>',
						esc_attr( esc_html( $atbDisable ) ),
						esc_attr( esc_html( 'ced_ocor_atb_btn_' . $product->get_id() ) ),
						esc_attr( esc_html( $product->get_id() ) ),
						esc_attr( esc_html( get_current_user_id() ) ),
						esc_attr( esc_html( $product->get_type() ) ),
						esc_attr( esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) ) ),
						esc_html( apply_filters( 'ced_ocor_atb_button_text', $atbBtnText ) )
					);
				$atb_html .= '</p>';
				echo $atb_html;
			
		}





		}
}
		/**
		 * Add to basket feature.
		 */
		public function ced_ocor_add_to_basket() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$item_id 		= isset( $_POST[ 'item_id' ] ) ? $_POST[ 'item_id' ] : '';
			$user_id 		= isset( $_POST[ 'user_id' ] ) ? $_POST[ 'user_id' ] : '';
			$qty 			= isset( $_POST[ 'qty' ] ) ? $_POST[ 'qty' ] : 1;
			$item_type 		= isset( $_POST[ 'type' ] ) ? $_POST[ 'type' ] : '';
			$variation_id 	= isset( $_POST[ 'variation_id' ] ) ? $_POST[ 'variation_id' ] : '';
			if ( get_current_user_id() == $user_id ) {
				$updated = false;
				$basket = get_user_meta( $user_id, 'ced_ocor_basket_info', true );
				if ( empty( $basket ) ) {
					$basket[ $item_id ][ 'type' ] 	= $item_type;
					if ( $item_type == 'variable' and $variation_id != '' ) {
						$basket[ $item_id ][ 'variations' ][ $variation_id ] = array(
							'qty'			=> $qty,
							'variation_id'	=> $variation_id,
						);
					} else {
						$basket[ $item_id ][ 'qty' ] = $qty;
					}

					$updated = update_user_meta( $user_id, 'ced_ocor_basket_info', $basket );
				} else {
					$basket[ $item_id ][ 'type' ] = $item_type;
					if ( $item_type == 'variable' and $variation_id != '' ) {
						$basket[ $item_id ][ 'variations' ][ $variation_id ] = array(
							'qty'			=> $qty,
							'variation_id'	=> $variation_id,
						);
					} else {
						$basket[ $item_id ][ 'qty' ] = $qty;
					}
					$updated = update_user_meta( $user_id, 'ced_ocor_basket_info', $basket );
				}

				if ( $updated ) {
					wp_send_json_success( __( 'Added to basket.', CNG_TXTDOMAIN ) );
					wp_die();
				} else {
					wc_add_notice( __( "Couldn't added to basket.", CNG_TXTDOMAIN ), 'error' );
					wp_send_json_error( __( "Couldn't added to basket.", CNG_TXTDOMAIN ) );
					wp_die();
				}
			}
		}

		/**
		 * remove from basket feature.
		 */
		public function ced_ocor_remove_from_basket() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$item_id 		= isset( $_POST[ 'item_id' ] ) ? $_POST[ 'item_id' ] : '';
			$user_id 		= isset( $_POST[ 'user_id' ] ) ? $_POST[ 'user_id' ] : '';
			$type 			= isset( $_POST[ 'type' ] ) ? $_POST[ 'type' ] : '';
			$variation_id 	= isset( $_POST[ 'variation_id' ] ) ? $_POST[ 'variation_id' ] : '';
			if ( get_current_user_id() == $user_id ) {
				$updated = false;
				$basket = get_user_meta( $user_id, 'ced_ocor_basket_info', true );
				if ( ! empty( $basket ) ) {
					if ( $type == 'variable' ) {
						foreach ( $basket[ $item_id ][ 'variations' ] as $var_id => $variation_array ) {
							if ( empty( $var_id ) ) {
								continue;
							}

							if ( $variation_id != $var_id ) {
								continue;
							}

							unset( $basket[ $item_id ][ 'variations' ][$var_id] );
						}
						if ( empty( $basket[ $item_id ] ) ) {
							unset( $basket[ $item_id ] );
						}
					} else {
						unset( $basket[ $item_id ] );
					}
					
					$updated = update_user_meta( $user_id, 'ced_ocor_basket_info', $basket );
				}

				if ( $updated ) {
					wp_send_json_success( __( 'Removed from basket.', CNG_TXTDOMAIN ) );
					wp_die();
				} else {
					wc_add_notice( __( "Couldn't removed from basket.", CNG_TXTDOMAIN ) );
					wp_send_json_error( __( "Couldn't removed from basket.", CNG_TXTDOMAIN ) );
					wp_die();
				}
			}
		}

		/**
		 * Add icon to show basket.
		 */
		public function ced_ocor_footer_stuffs() {


			if ( ! is_user_logged_in() ) {
				return;
			}

			$settings 		= get_option( 'ced_ocor_general_settings', false );
			if ( empty( $settings ) ) {
				return;
			}

			if ( empty( $settings[ 'selectedUsers' ] ) and ! current_user_can( 'administrator' ) ) {
				return;
			}

			$current_user =	wp_get_current_user();
			if ( empty( $current_user ) ) {
				return;
			}

			$current_user->roles = array_values( $current_user->roles );
			if(is_array($settings[ 'selectedUsers' ]))
			if (! in_array( $current_user->roles[0], $settings[ 'selectedUsers' ] ) and ! current_user_can( 'administrator' ) ) {
				return;
			}

			if ( empty( $settings[ 'basketEnable' ] ) or $settings[ 'basketEnable' ] == 'disable' ) {
				return;
			}

			if ( empty( $settings[ 'basketPages' ] ) ) {
				return;
			}

			$found = false;
			foreach ( $settings[ 'basketPages' ] as $page ) {
				if ( !$page ) {
					continue;
				}

				switch ( $page ) {
					case 'detail':
						if ( is_product() ) {
							$found = true;
						}
						break;
					
					case 'shop':
						if ( is_shop() ) {
							$found = true;
						}
						break;
					
					case 'cart':
						if ( is_cart() ) {
							$found = true;
						}
						break;
					
					case 'account':
						if ( is_account_page() ) {
							$found = true;
						}
						break;
					
					default:
						if ( ! $found ) {
							return ;
						}
						break;
				}
				if ( $found ) {
					break;
				}
			}

			if ( ! $found ) {
				return ;
			}

			$basketTotal 	= 0;
			$basket 		= get_user_meta( get_current_user_id(), 'ced_ocor_basket_info', true );
			if ( ! empty( $basket ) ) {
				foreach ( $basket as $item_id => $item ) {
					if( empty( $item ) )
						continue;

					if ( $item[ 'type' ] == 'variable' ) {
						foreach ( $item[ 'variations' ] as $var_id ) {
							if ( empty( $var_id ) )
								continue;

							$basketTotal++;			
						}
					} else {
						$basketTotal++;
					}
				}
			}

			$image = CEDCOMMERCE_CNG_ORDER_URL . 'assets/images/shopping-bag.png';
			if ( !empty( $settings[ 'iconUri' ] ) ) {
				$image = $settings[ 'iconUri' ];
			}
			?>
			<a class="ced_ocor_floating_basket_wrapper">
				<img src="<?php echo $image;?>" alt="<?php _e( "Shopping Basket", CNG_TXTDOMAIN );?>" class="ced_ocor_shopping_basket">
				<span class="ced_ocor_basket_item_count" data-total="<?php echo $basketTotal;?>"><?php echo $basketTotal;?></span>
			</a>
		<?php 
		}

		/**
		 * Add basket items to cart.
		 */
		public function ced_ocor_add_basket_items_to_cart() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				exit( 'failed' );
			}

			$excluded_items = isset( $_POST[ 'excluded_items' ] ) ? $_POST[ 'excluded_items' ] : array();
			$quantities 	= isset( $_POST[ 'quantities' ] ) ? $_POST[ 'quantities' ] : array();

			$foundItems 	= array();
			$errorInAtc 	= false;
			$basketItemsIds = get_user_meta( get_current_user_id(), 'ced_ocor_basket_info', true );
			if ( empty( $basketItemsIds ) or ! is_array( $basketItemsIds ) ) {
				wp_send_json_error( __( "No items found in you basket.", CNG_TXTDOMAIN ) );
				wp_die();
			}

			if ( WC ()->cart->get_cart_contents_count() ) {
				WC ()->cart->empty_cart ();
			}

			foreach ( $basketItemsIds as $item_id => $item ) {
				if ( empty( $item_id ) or empty( $item ) ) {
					continue;
				}

				if ( in_array( $item_id, $excluded_items ) ) {
					continue;
				}

				$qty = $item[ 'qty' ];
				if ( !empty( $quantities ) ) {
					if ( array_key_exists( $item_id, $quantities ) ) {
						$qty = $quantities[ $item_id ];
					}
				}
				
				if ( $item[ 'type' ] == 'variable' ) {
					foreach ( $item[ 'variations' ] as $var_id => $variation_array ) {
						if ( empty( $var_id ) or empty( $variation_array ) ) {
							continue;
						}

						$productVariation 	= new WC_Product_Variation( $var_id );
						$all_variations 	=  $productVariation->get_variation_attributes();
						$item_qty 			= isset( $qty ) ? $qty : $variation_array[ 'qty' ];
						$added_to_cart 		= WC()->cart->add_to_cart( $item_id, $item_qty, $var_id, $all_variations );
					}
				} else {
					// Add to cart order products
					$added_to_cart = WC ()->cart->add_to_cart( $item_id, $qty );
				}
				if ( ! $added_to_cart ) {
					$errorInAtc = true;
				}
			}
			
			wp_send_json_success( __( 'Successfully added to cart.', CNG_TXTDOMAIN ) );
			wp_die();
		}


		/**
		 * Fetch all items in basket and their informations.
		 */
		public function ced_ocor_get_basket_items() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'ajax_nonce' );
			if ( !$check_ajax ) {
				wc_add_notice( __( "Ajax nonce couldn't match.", CNG_TXTDOMAIN ) );
				wp_send_json_error( __( "Ajax nonce couldn't match.", CNG_TXTDOMAIN ) );
				wp_die();
			}

			if ( ! is_user_logged_in() ) {
				wc_add_notice( __( "Please login first.", CNG_TXTDOMAIN ), 'error' );
				wp_send_json_error( __( "Sorry please login first.", CNG_TXTDOMAIN ) );
				wp_die();
			}

			$foundItems 	= array();
			$basketItemsIds = get_user_meta( get_current_user_id(), 'ced_ocor_basket_info', true );
			if ( empty( $basketItemsIds ) or ! is_array( $basketItemsIds ) ) {
				wc_add_notice( __( "No items found in you basket.", CNG_TXTDOMAIN ), 'error' );
				wp_send_json_error( __( "No items found in you basket.", CNG_TXTDOMAIN ) );
				wp_die();
			}

			foreach ( $basketItemsIds as $item_id => $item ) {
				if ( empty( $item_id ) or empty( $item ) ) {
					continue;
				}

				$product 	= wc_get_product( $item_id );
				if ( empty( $product ) ) {
					continue;
				}

				$key = $item_id;
				if ( $item[ 'type' ] == 'variable' ) {
					foreach ( $item[ 'variations' ] as $var_id => $variation_array ) {
						$key = "{$item_id}_{$var_id}";
						$productVariation 	= new WC_Product_Variation( $var_id );
						$foundItems[ $key ][ 'type' ] 			=  $item[ 'type' ];
						$foundItems[ $key ][ 'attributes' ] 	=  $productVariation->get_formatted_variation_attributes();
						$foundItems[ $key ][ 'item_id' ] 		= $item_id;
						$foundItems[ $key ][ 'title' ] 			= $product->post->post_title;
						$foundItems[ $key ][ 'image' ] 			= $product->get_image();
						$foundItems[ $key ][ 'availability' ] 	= $product->post->post_status != 'publish' ? 'not_availale' : 'available';
						$foundItems[ $key ][ 'qty' ] 			= $variation_array[ 'qty' ];
						$foundItems[ $key ][ 'stock' ] 			= $product->is_in_stock() ? "in_stock" : 'out_of_stock' ;
						$foundItems[ $key ][ 'permalink' ] 		= $product->get_permalink( $var_id );
						$foundItems[ $key ][ 'variations' ] 	= WC()->cart->get_item_data( $cart_item, true );
					}
				} else {
					$foundItems[ $key ][ 'type' ] 			=  $item[ 'type' ];
					$foundItems[ $key ][ 'item_id' ] 		= $item_id;
					$foundItems[ $key ][ 'title' ] 			= $product->post->post_title;
					$foundItems[ $key ][ 'image' ] 			= $product->get_image();
					$foundItems[ $key ][ 'availability' ] 	= $product->post->post_status != 'publish' ? 'not_availale' : 'available';
					$foundItems[ $key ][ 'qty' ] 			= $item[ 'qty' ];
					$foundItems[ $key ][ 'stock' ] 			= $product->is_in_stock() ? "in_stock" : 'out_of_stock' ;
					$foundItems[ $key ][ 'permalink' ] 		= $product->get_permalink();
				}
			}

			if( empty( $foundItems ) ) {
				wc_add_notice( __( 'No items found in your basket.', 'domain' ), 'error' );
				wp_send_json_error( __( 'No items found.', 'domain' ) );
				wp_die();
			}

			wp_send_json_success( $foundItems );
			wp_die();
		}

		/**
		 * Attchment data info save.
		 * @param  string $file       
		 * @param  int $attachment_id 
		 * @return string
		 */
		function ced_ocor_action_attachment_updated( $file, $attachment_id ) {
			if ( !$attachment_id or !$file ) {
				return;
			}

			$iconInfo = array( $attachment_id, $file );
			update_option( 'ced_ocor_attchment_icon', $iconInfo );
			return $file;
		}

		/**
		 * Fetch attchment info.
		 */
		public function ced_ocor_get_attchment_icon_info() {
			$check_ajax = check_ajax_referer( 'ced-cng-ajax-seurity-nonce', 'nonce_check' );
			if ( !$check_ajax ) {
				wp_send_json_error( __( "Ajax nonce couldn't match.", CNG_TXTDOMAIN ) );
				wp_die();
			}

			$iconInfo = get_option( 'ced_ocor_attchment_icon', false );
			if ( empty( $iconInfo ) ) {
				wp_send_json_error( __( 'No attachment info found', CNG_TXTDOMAIN ) );
				wp_die();
			}

			wp_send_json_success( $iconInfo );
			wp_die();
		}
	}

	/**
	 * create object instance for this class.
	 */
	$_GLOBALS[ 'Basket_Order' ] = new Basket_Order();
}