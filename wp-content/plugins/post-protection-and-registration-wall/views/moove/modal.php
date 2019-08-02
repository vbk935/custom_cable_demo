<div id="moove-protection-modal-free" class="moove-modal-dialog <?php echo $data['modal-free']; ?>">
  <div class="moove-protection-modal-content">
    <a href="" title="Close" class="close">x</a>
    <?php if ( empty( get_option( 'moove_protection-settings' )['Free-membershipmodal-content'] ) ) :
        echo Moove_View::load( 'moove.protected.free_membership' ) ;
    else:
        echo get_option( 'moove_protection-settings' )['Free-membershipmodal-content'];
    endif;?>
  </div>
  <!-- moove-protection-modal-content -->
</div>
<!-- moove-modal-dialog-free -->

<div id="moove-protection-modal-premium" class="moove-modal-dialog <?php echo $data['modal-premium']; ?>">
  <div class="moove-protection-modal-content">
    <a href="" title="Close" class="close">x</a>
    <?php if ( empty( get_option( 'moove_protection-settings' )['Premium-membershipmodal-content'] ) ) :
        echo Moove_View::load( 'moove.protected.premium_membership' ) ;
    else:
        echo get_option( 'moove_protection-settings' )['Premium-membershipmodal-content'];
    endif; ?>
  </div>
  <!-- moove-protection-modal-content -->
</div>
<!-- moove-modal-dialog-premium -->