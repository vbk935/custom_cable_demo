<div class="login-form-page">
  <div class="moove-protection-login-container">

    <?php if ( isset( $system_message ) && $system_message['type'] !== false) : ?>
    	<p class="msg msg-<?php echo $system_message['type']?>"><?php echo $system_message['msg']?></p>
    <?php endif;?>

    <div class="login-part">
      <h3><?php _e('Login with your email','moove'); ?></h3>
      <form role="form" class="login-form-ajax" id="moove-login-form">
        <div class="form-group ">
          <label for="email"><?php _e('E-mail address','moove'); ?></label>
          <input type="email" id="email" name="email" placeholder="Email">
        </div>
        <!-- form-group -->
        <br>
        <div class="form-group">
          <label for="password"><?php _e('Password','moove'); ?></label>
          <input type="password" id="password" name="password" placeholder="Password">
        </div>
        <!-- form-group -->
        <div class="form-control-box">
          <span class="remember-me">
            <input type="checkbox" name="remember" id="remember" class="css-checkbox">
            <label for="remember" class="css-label"><?php _e('Remember me','moove'); ?></label>
          </span>
          <!-- remember-me -->
          <span class="forgot-password">
            <a class="forgot-password" href="#" title="<?php _e('Lost Password','moove');?>"><?php _e('Lost Password','moove');?></a>
          </span>
          <!-- forgot-password -->
        </div>
        <!-- form-control-box -->
        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
        <input type="hidden" name="redirect_to" value="<?php echo isset($data['redirect_to'])?$data['redirect_to']:''?>">
        <button type="submit" class="btn btn-default">Login</button>
      </form>
      <!-- #moove-login-form -->
    </div>
    <!-- login-part -->

    <div class="reset-password-part">
      <h3><?php _e('Reset your password','moove'); ?></h3>
      <form role="form" class="styled-form passwordreset" id="moove-password-reset">
        <div class="form-group">
          <label for="lost-mail"><?php _e('E-mail address','moove'); ?></label>
          <input type="email" name="lost-mail" id="lost-mail" placeholder="Email">
        </div>
        <!-- form-group -->
        <p><?php _e('Enter the email address you used when you joined and we’ll send you instructions to reset your password.','moove'); ?></p>
        <button type="submit" class="btn btn-default request_new_password"><?php _e('request new password','moove'); ?></button>
        <a class="back-to-login" href="#" title="<?php _e('Back to login','moove');?>"><?php _e('Back to login','moove');?></a>
      </form>
      <!-- #moove-password-reset -->
      <div class="reset-confirm-part">
        <?php echo Moove_View::load('moove.reset-complete'); ?>
        <a class="back-to-login" href="#" title="<?php _e('Back to login','moove');?>"><?php _e('Back to login','moove');?></a>
      </div>
      <!-- reset-confirm -->
    </div>
    <!-- reset-password-part -->
  </div>
  <!-- moove-protection-login-container -->
</div>
<!-- login-form-page -->
