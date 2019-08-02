<?php 
/*
 * This is the page users will see logged out. 
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>
	<div class="lwa lwa-default"><?php //class must be here, and if this is a template, class name should be that of template directory ?>
        <legend>Please login/register to continue!</legend>
        <form class="lwa-form" action="<?php echo esc_attr(LoginWithAjax::$url_login); ?>" method="post">
            <div>
                <span class="lwa-status"></span>
                <table>
                    <tr class="lwa-username">
                        <td class="lwa-username-label">
                            <label><?php esc_html_e( 'Username','login-with-ajax' ) ?></label>
                        </td>
                        <br>
                        <td class="lwa-username-input">
                            <input type="text" name="log" />
                        </td>
                    </tr>
                    <tr class="lwa-password">
                        <td class="lwa-password-label">
                            <label><?php esc_html_e( 'Password','login-with-ajax' ) ?></label>
                        </td>
                        <br>
                        <td class="lwa-password-input">
                            <input type="password" name="pwd" />
                        </td>
                    </tr>
                    <tr class="lwa-submit">
                        <td class="lwa-submit-button">
                            <input type="submit" name="wp-submit" id="lwa_wp-submit" class="woocommerce-Button button" value="<?php esc_attr_e('Log In', 'login-with-ajax'); ?>" tabindex="100" />
                            <input type="hidden" name="lwa_profile_link" value="<?php echo esc_attr($lwa_data['profile_link']); ?>" />
                            <input type="hidden" name="login-with-ajax" value="login" />
                            <?php if( !empty($lwa_data['redirect']) ): ?>
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url($lwa_data['redirect']); ?>" />
                            <?php endif; ?>
                        </td>
                        <td class="lwa-submit-links">
                            <input name="rememberme" type="checkbox" class="lwa-rememberme" value="forever" /> <label><?php esc_html_e( 'Remember Me','login-with-ajax' ) ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a target="_blank" href="<?php echo home_url('my-account'); ?>" class="lwa-links-register"><?php esc_html_e("Don't have an account, Click to Register",'login-with-ajax') ?></a>
                        </td>
                    </tr>
                </table>                
            </div>
        </form>
	</div>