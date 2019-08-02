<div class="clearfix"></div>

<form role="form" class="styled-form" id="moove_register_form" method="post" action="<?php the_permalink();?>">

  <div class="form-group">
    <label for="moove_name"><?php _e( 'First Name','moove' ); ?></label><br />
    <input type="text" id="moove_name" name="moove_name" placeholder="First name">
    <?php if (isset($data['errors']['error']) && isset($data['errors']['moove_name'])):?>
      <span class="error"><?php echo $data['errors']['moove_name'];?></span>
    <?php endif;?>
  </div>
  <br>

  <div class="form-group">
    <label for="surname"><?php _e( 'Last Name', 'moove' ); ?></label><br />
    <input type="text" id="surname" name="surname" placeholder="Surname">
    <?php if (isset($data['errors']['error']) && isset($data['errors']['surname'])):?>
      <span class="error"><?php echo $data['errors']['surname'];?></span>
    <?php endif;?>
  </div>
  <br>

  <div class="form-group">
    <label for="email"><?php _e( 'E-mail', 'moove' ); ?></label><br />
    <input type="email" id="email" name="email" placeholder="Email">
    <?php if (isset($data['errors']['error']) && isset($data['errors']['email'])):?>
      <span class="error"><?php echo $data['errors']['email'];?></span>
    <?php endif;?>
  </div>
  <br>

  <div class="form-group">
    <label for="pwd"><?php _e( 'Password', 'moove' ); ?></label><br />
    <input type="password" id="pwd" name="pwd" placeholder="Password">
    <?php if (isset($data['errors']['error']) && isset($data['errors']['pwd'])):?>
      <span class="error"><?php echo $data['errors']['pwd'];?></span>
    <?php endif;?>
  </div>
  <br>

  <div class="form-group">
    <label for="pwdc"><?php _e( 'Confirm password', 'moove' ); ?></label><br />
    <input type="password" id="pwdc" name="pwdc" placeholder="Confirm password">
    <span class="form-instruction"></span>
    <?php if (isset($data['errors']['error']) && isset($data['errors']['pwdc'])):?>
      <span class="error"><?php echo $data['errors']['pwdc'];?></span>
    <?php endif;?>
  </div>
  <br>

  <div class="clearfix"></div><br><br>
  <?php wp_nonce_field("moove_register_action", "moove_register");?>
  <button type="submit" name="registerButton" class="btn btn-default"><?php _e('Register','moove'); ?></button>

</form>
<br>
<div class="clearfix"></div>
