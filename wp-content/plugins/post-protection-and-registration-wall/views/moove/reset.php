<?php if ( isset( $data['errors']['nonce'] ) && isset( $data['errors']['error'] ) ) :?>
  <div class="error">
    <?php echo $data['errors']['nonce'];?>
  </div>
<?php endif;?>
<?php if ( isset( $data['errors']['fail'] ) && isset( $data['errors']['error'] ) ):?>
  <div class="error">
    <?php echo $data['errors']['fail'];?>
  </div>
<?php endif;?>
<div class="moove-protection-login-container">
  <form action="<?php the_permalink();?>" method="post" id="PasswordResetForm" class="styled-form">

    <div class="form-group">
      <label for="password"><?php _e('Password','moove'); ?></label>
      <input type="password" id="reset-password" name="password" placeholder="Password">
      <?php if (isset($data['errors']['error']) && isset($data['errors']['password'])):?>
        <span class="error"><?php echo $data['errors']['password'];?></span>
      <?php endif;?>
    </div>
    <!-- form-group -->
    <br />
    <div class="form-group">
      <label for="password2"><?php _e('Confirm password','moove'); ?></label>
      <input type="password" id="reset-password2" name="password2" placeholder="Confirm password">
      <?php if (isset($data['errors']['error']) && isset($data['errors']['password2'])):?>
        <span class="error"><?php echo $data['errors']['password2'];?></span>
      <?php endif;?>
      <br />
    </div>
    <!-- form-group -->


    <input type="hidden" name="reset_token" value="<?php echo $data['token'];?>" />
    <?php wp_nonce_field("moove_reset_action", "moove_reset");?>
    <div class="clearfix"></div>
    <br />
    <button type="submit" name="resetButton"><?php _e('Reset my password','moove'); ?></button>
    <!-- col -->
  </form>
</div>
<!-- moove-protection-login-container -->