<div class="clearfix"></div>
<div class="row">




              <footer id="footer" role="contentinfo" class="footer-main">
                <div class="container-fluid">
                <div class="row">
                  <? // FIRST COL  ; ?>
                 <?php /*?> <div class="col-sm-4 logo-footer"> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/tyrex-white.png" width="400" height="138" alt="Tyrex Group, LTD." class="img-responsive width-100 footer-logo" />
                    <div class="privacy">
                      <p class="script">Family of Technology Companies</p>
				  <? $url = home_url('/'); ?>
                      <p><a href="<?php echo $url; ?>privacy-policy">Privacy Policy</a></p>
                    </div>
                  </div>
                  <div class="col-sm-2 col-sm-push-6 hidden-sm hidden-xs lg-recognize">
                    <div class="footer-message">
                      <div class="recognize-good-1">A Proud Founding Sponsor of</div>
                      <?php 
						$attachment_id = 28; // attachment ID
						$alt_text = get_post_meta('28', '_wp_attachment_image_alt', true);
						$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
						if( $image_attributes ) { ?>
                      <img src="<?php echo $image_attributes[0]; ?>"  class="img-responsive" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
                      <?php } ?>
                    </div>
                  </div>
                  
                  <? // SECOND COL  ; ?>
                  <div class="links col-md-6 col-md-pull-2 col-sm-8 col-sm-pull-0">
                    <?	wp_nav_menu( array(
                                    'menu'              => 'footer',
                                    'theme_location'    => 'footer',
                                    'depth'             => 1,
                                    'container'         => 'div',
                                    //'container_class'   => 'nav-tabs-container',
                                    'menu_class'        => 'list-unstyled list-inline',
                                    'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                                    'walker'            => new wp_bootstrap_navwalker())
                                );?>
                  </div>
                  
                  <? // THIRD COL  ; ?>
                  <div class="social-media clearfix col-md-6 col-md-pull-2 col-sm-8 col-sm-pull-0">
			   		<div class="social-block"> 
						<a href="#"> <i class="fa fa-facebook-square"></i></a> 
						<a href="#"><i class="fa fa-google-plus-square"></i></a> 
						<a href="#"><i class="fa fa-linkedin-square"></i></a> </a> 
						<a href="#"><i class="fa fa-youtube-square"></i></a> 
					</div>
			   		<div class="social-block"> 
						<a class="subscribe" href="#"><strong>Subscribe</strong> To our E-mail Newsletter</a> 
					</div>
				</div>
                  
                  <? // FOUTH COL  ; ?>
                  <div class="address-main col-md-6 col-md-pull-2 col-sm-8 col-sm-pull-0">
                    <div class="contact-info" >
                      <div itemscope itemtype="http://schema.org/LocalBusiness">
                        <div itemprop="address" class="address" itemscope itemtype="http://schema.org/PostalAddress">
                          <div itemprop="streetAddress" class="street-address">12317 Technology Blvd., Ste. 100</div>
                          <div class="street-address"> <span itemprop="addressLocality">Austin</span>, <span itemprop="addressRegion">TX</span> <span itemprop="postalCode">78727</span> </div>
                        </div>
                        &bull;
                        <div itemprop="telephone" class="telephone">(512) 490-2294</div>
                      </div>
                    </div>
                  </div>
                  
                  
                  <div class="clearfix cf991"></div>
                  
                  
                  <div class="col-md-x footer-news hidden-lg hidden-md">
                    <div class="footer-message">
                      <div class="recognize-good-1">A Proud Founding Sponsor of</div>
                      <?php 
                                    $attachment_id = 28; // attachment ID
                                    $alt_text = get_post_meta('28', '_wp_attachment_image_alt', true);
                                    $image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
                                    if( $image_attributes ) { ?>
                      <img src="<?php echo $image_attributes[0]; ?>"  class="recognize-good-2" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $alt_text; ?>">
                      <?php } ?>
                    </div>
                  </div><?php */?>
			   </div>
                </div>
              </footer>
  
  
  
  
 </div>
<?php wp_footer(); ?>
</body></html>