<div class="moove-donation-box-wrapper">
    <div class="moove-donation-box">
        <div class="notice-dismiss">Dismiss</div>
        <h3>Donations</h3>

        <p>
            If you enjoy using this plugin and find it useful, feel free to donate a small amount to show appreciation and help us continue improving and supporting this plugin for free. It will make our development team very happy! :)
        </p>

        <p>
            Click the 'Donate' button and you will be redirected to Paypal where you can make your donation. You don't need to have a Paypal account, you can make donation using your credit card.
        </p>

        <p>
            Many thanks.
        </p>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="M4EKTTCT4KSY6">
            <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
        </form>
        <script>
            jQuery(document).ready(function ($) {
                $('.moove-donation-box .notice-dismiss').on('click', function () {
                    $(this).closest('.moove-donation-box-wrapper').slideUp();
                });
            });
        </script>
    </div>
</div>

<div class="wrap moove-protection-plugin-wrap">
	<h1><?php _e('Global content protection','moove'); ?></h1>

    <?php
        $current_tab_protection = sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) );
        if( isset( $current_tab_protection )  && $current_tab_protection !== '' ) {
            $active_tab = esc_attr( $current_tab_protection );
        } else {
            $active_tab = "post_type_protection";
        } // end if

    ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=moove-protection&tab=post_type_protection" class="nav-tab <?php echo $active_tab == 'post_type_protection' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Post type protection','moove'); ?>
        </a>
        <a href="?page=moove-protection&tab=email_settings" class="nav-tab <?php echo $active_tab == 'email_settings' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Email settings','moove'); ?>
        </a>
         <a href="?page=moove-protection&tab=validation_settings" class="nav-tab <?php echo $active_tab == 'validation_settings' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Validation settings','moove'); ?>
        </a>
        <a href="?page=moove-protection&tab=protection_settings" class="nav-tab <?php echo $active_tab == 'protection_settings' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Protection settings','moove'); ?>
        </a>
        <a href="?page=moove-protection&tab=plugin_documentation" class="nav-tab <?php echo $active_tab == 'plugin_documentation' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Documentation','moove'); ?>
        </a>
    </h2>
    <div class="moove-form-container <?php echo $active_tab; ?>">
        <span class="moove-logo"></span>
        <?php
        if( $active_tab == 'post_type_protection' ) : ?>
            <form action="options.php" method="post" class="moove-protection-form">
                <?php
                settings_fields( 'moove_post_protection' );
                do_settings_sections( 'moove-protection' );
                submit_button();
                ?>
            </form>

        <?php elseif( $active_tab == 'email_settings' ): ?>
            <form action="options.php" method="post" class="moove-protection-form">
                <?php
                settings_fields( 'moove_protection_email' );
                do_settings_sections( 'moove-protection-email' );
                submit_button();
                ?>
            </form>

        <?php elseif( $active_tab == 'validation_settings' ): ?>
            <form action="options.php" method="post" class="moove-protection-form">
                <?php
                settings_fields( 'moove_protection_validation' );
                do_settings_sections( 'moove-protection-validation' );
                submit_button();
                ?>

            </form>
        <?php elseif( $active_tab == 'protection_settings' ): ?>
            <form action="options.php" method="post" class="moove-protection-form">
                <?php
                settings_fields( 'moove_protection_settings' );
                do_settings_sections( 'moove-protection-settings' );
                submit_button();
                ?>

            </form>
        <?php elseif( $active_tab == 'plugin_documentation' ): ?>
            <?php echo Moove_View::load('moove.admin.settings.documentation'); ?>
        <?php endif; ?>
    </div>
    <!-- moove-form-container -->
</div>
<!-- wrap -->