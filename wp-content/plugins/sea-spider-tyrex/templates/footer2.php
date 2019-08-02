
<div class="clearfix"></div>
<div class="row">
	<footer id="footer" role="contentinfo" class="footer-main">
		<div class="container-fluid">
			<div class="row">
				<? // FIRST COL  ; ?>
				<div class="col-md-3 footer-logo-col"> 
                
<table class="logo-table"><tbody><tr><td valign="middle"> 
  <div class="table-div">
  
                
                
                
					<?php if( get_field('website_main', 'option') ){ ?><a href="<?php the_field('website_main', 'option'); ?>"><?php } ?>
						<?php  $image = get_field('logo_small', 'option');
						if( !empty($image) ): ?>
							<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>"  class="img-responsive"/>
						<?php endif; ?>
					<?php if( get_field('website_main', 'option') ){ ?></a><?php } ?>
					<div class="privacy" style="margin-top:5px;">
						<p class="script <? the_field('code', 'option'); ?> tyr-only"><a href="http://www.tyrexmfg.com/">Family of Technology Companies</a></p>
						<p class="script not-tyr family"><a href="http://www.tyrexmfg.com/">A TyRex Technology Family Company</a></p>
						<?php if( get_field('privacy_page', 'option')  OR  get_field('terms_of_service', 'option')){ ?><p><?php } ?>
							<?php if( get_field('privacy_page', 'option') ){ ?><a href="<?php the_field('privacy_page', 'option'); ?>">Privacy Policy</a><?php } ?>
							<?php if( get_field('privacy_page', 'option')  &&  get_field('terms_of_service', 'option')){ ?><span>|</span><?php } ?>
							<?php if( get_field('terms_of_service', 'option') ){ ?><a href="<?php the_field('terms_of_service', 'option'); ?>">Terms of Service</a> <?php } ?>
						<?php if( get_field('privacy_page', 'option')  OR  get_field('terms_of_service', 'option')){ ?></p><?php } ?>
					</div>
  
  </div>
  </td>
  </tr>
  </tbody>
  </table>                    
                    
                    
                    
                    
                    
				</div>
				
				
				<div class="col-md-3 col-md-push-6 hidden-sm hidden-xs">
                
<table class="logo-table"><tbody><tr><td align="center" valign="middle"> 
  <div class="table-div">                
                
						<?php if( get_field('footer_url', 'option') ){ ?><a href="<?php the_field('footer_url', 'option'); ?>" target="_blank"><?php } ?>
							<div class="recognize-good-1"><?php the_field('footer_text_above', 'option'); ?></div>
								<?php  $image = get_field('footer_image', 'option');
								if( !empty($image) ): ?>
									<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>"  class="img-responsive"/>
								<?php endif; ?>
						<?php if( get_field('footer_url', 'option') ){ ?></a><?php } ?>
                        
   </div>
  </td>
  </tr>
  </tbody>
  </table>                          
                        
				</div>
				
				
				
				<? // SECOND COL  ; ?>
				<div class="links col-md-6 col-md-pull-3">
					<?	wp_nav_menu( array(
                                    'menu'              => 'footer',
                                    'theme_location'    => 'footer',
                                    'depth'             => 1,
                                    'container'         => 'div',
                                    'menu_class'        => 'list-unstyled list-inline',
                                    'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                                    'walker'            => new wp_bootstrap_navwalker())
                                );?>
				</div>
				
				
				<? // THIRD COL  ; ?>
				<div class="social-media clearfix col-md-6 col-md-pull-3">
					<?php if( get_field('subscribe', 'option')){ ?><div class="social-block"><?php } else { echo "<div class='soc-med¨'>"; } ?>
					<div class="follow">Follow Us At:</div>
					<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/social-media.php'); ?>

						<?php if( get_field('subscribe', 'option')){ ?>
						<a class="subscribe" href="<?php the_field('subscribe', 'option'); ?>"><strong>Subscribe</strong> To our E-mail Newsletter</a> 
						<?php } ?>
					</div>
				</div>
				
				
				
				<? // FOUTH COL  ; ?>
				<div class="address-main col-md-6 col-md-pull-3">
					<?php if( get_field('address_line_01', 'option') && get_field('city', 'option') && get_field('state', 'option') && get_field('zip', 'option') && get_field('phone_number', 'option') ){ ?>
					<div class="contact-info" >
						<div itemscope itemtype="http://schema.org/LocalBusiness">
							<div itemprop="address" class="address" itemscope itemtype="http://schema.org/PostalAddress">
								<?php if( get_field('google_map_url', 'option') ){ ?>
								<a href="<?php the_field('google_map_url', 'option'); ?>">
								<i class="fa fa-map-marker"></i>
								<?php } ?>
								<div itemprop="streetAddress" class="street-address">
									<?php the_field('address_line_01', 'option'); ?>,
									<?php if( get_field('address_line_02', 'option') ){ ?>
									<?php the_field('address_line_02', 'option'); ?>,
									<?php } ?>
								</div>
								<div class="street-address"> <span itemprop="addressLocality">
									<?php the_field('city', 'option'); ?>
									</span>, <span itemprop="addressRegion">
									<?php the_field('state', 'option'); ?>
									</span> <span itemprop="postalCode">
									<?php the_field('zip', 'option'); ?>
									</span> </div>
								<?php if( get_field('google_map_url', 'option') ){ ?>
								</a>
								<?php } ?>
							</div>
							
							<?php if( get_field('google_map_url', 'option') ){ ?>
								<span class="bullet">&bull;</span>
								<?php $number = get_field('phone_number', 'option'); ?>
								<?php $number = str_replace('(', '', $number); ?>
								<?php $number = str_replace(')', '', $number); ?>
								<?php $number = str_replace('-', '', $number); ?>
								<?php $number = str_replace(' ', '', $number); ?>
								<div itemprop="telephone" class="telephone">
									<a href="tel:+<?php echo $number; ?>">
										<i class="fa fa-mobile"></i>
										<?php the_field('phone_number', 'option'); ?>
									</a>
								</div>
							<?php } ?>
							
						</div>
					</div>
					<?php } ?>
				</div>
				
				
				
				<div class="clearfix cf991"></div>
				<div class="col-md-x footer-news hidden-lg hidden-md">
					<div class="footer-message"> 
						<a href="<?php the_field('footer_url', 'option'); ?>" target="_blank">
							<div class="recognize-good-1"><?php the_field('footer_text_above', 'option'); ?></div>
								<?php  $image = get_field('footer_image', 'option');
								if( !empty($image) ): ?>
									<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>"  class="img-responsive recgood"/>
								<?php endif; ?>
						</a> 
					</div>
				</div>
			</div>
		</div>
	</footer>
	
			<?php if( get_field('display_footer_logo_bar', 'option') ){ ?>
			<div class="sites-footer">
		    		<a class="tyrex" href="http://tyrexmfg.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/tyrex.jpg' ) . '" class="img-responsive" alt="Tyrex" > '; ?>
		    		</a>
		    		<a href="http://austinrl.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/arl.jpg' ) . '" class="img-responsive" alt="Austin Reliablity Labs" > '; ?>
		    		</a>
		    		<a href="http://www.dlinnovations.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/dli.jpg' ) . '" class="img-responsive" alt="DLI" > '; ?>
		    		</a>						    		
		    		<a href="http://www.irexmfg.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/irex.jpg' ) . '" class="img-responsive" alt="iRex" > '; ?>
		    		</a>	
		    		<a href="http://www.megladonmfg.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/megladon.jpg' ) . '" class="img-responsive" alt="Megladon" > '; ?>
		    		</a>					
		    		<a href="http://www.saberdata.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/saberdata.jpg' ) . '" class="img-responsive" alt="Saber Data" > '; ?>
		    		</a>					
		    		<a href="http://www.saberex.com/" target="_blank">
					<?php echo '<img src="' . plugins_url( 'sea-spider-tyrex/assets/images/footer/saberex.jpg' ) . '" class="img-responsive" alt="Saberex" > '; ?>
		    		</a>																								
			</div>
			<?php } ?>	
	
	
</div>
</div>
<?php if( get_field('copyright', 'option') ){ ?>
<div class="container">
	<div class="legal"> <a href="<?php the_field('copyright_link', 'option'); ?>">Copyright &copy;
		<?php $date = date("Y"); ?>
		<?php if($date != "2014"){ echo '2014 - '; } ?>
		<?php echo date("Y") ?>
		<?php the_field('copyright', 'option'); ?>
		</a> </div>
</div>
<?php } ?>
</div>
<?php wp_footer(); ?>
</body></html>