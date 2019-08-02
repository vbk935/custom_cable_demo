<div class="panel panel-default address-panel">
						<div class="panel-body">
							<div class="contact-info" >
								<div itemscope itemtype="http://schema.org/LocalBusiness">
									<div itemprop="address" class="address" itemscope itemtype="http://schema.org/PostalAddress">
										<h2>Address</h2>
										<?php if( get_field('google_map_url', 'option') ){ ?>
										<a href="<?php the_field('google_map_url', 'option'); ?>">
											<?php } ?>
											<div itemprop="streetAddress" class="street-address clearfix">
												<i class="fa fa-map-marker"></i>
												<?php the_field('address_line_01', 'option'); ?>
												,
												<?php if( get_field('address_line_02', 'option') ){ ?>
												<?php the_field('address_line_02', 'option'); ?>
												,
												<?php } ?>
											</div>
											<div class="street-address">
												<span itemprop="addressLocality">
												<?php the_field('city', 'option'); ?>
												</span>, <span itemprop="addressRegion">
												<?php the_field('state', 'option'); ?>
												</span>
												<span itemprop="postalCode">
												<?php the_field('zip', 'option'); ?>
												</span>
											</div>
											<?php if( get_field('google_map_url', 'option') ){ ?>
										</a>
										<?php } ?>
									</div>
									<?php $phone = get_field('phone_number', 'option'); ?>
									<h2>Phone</h2>
									<div itemprop="telephone" class="telephone">
										<a href="tel:+5124902294"><i class="fa fa-mobile"></i>
											<?php echo $phone; ?></a>
									</div>
									<h2>Fax</h2>
									<div itemprop="fax" class="fax">
										<?php the_field('fax_number', 'option'); ?>
									</div>
								</div>
							</div>
							<h2>Social Media</h2>
							<div class="social-media">
								<?php include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/social-media.php'); ?>
							</div>
								<?php //if( have_rows('hours', 'option') ){ ?>
									<h2>Hours</h2>
									<strong>Monday - Friday</strong>: 8:00 AM to 5:00 PM
									<?php /*?><?php while ( have_rows('hours', 'option') ) : the_row();?>
										<strong><?php the_sub_field('day_hours', 'option'); ?>: </strong> <?php the_sub_field('opening_time', 'option'); ?> to <?php the_sub_field('closing_time', 'option'); ?><br />
								<?php endwhile;
								}  ?>					
							<?php */?>
						</div>
					</div>