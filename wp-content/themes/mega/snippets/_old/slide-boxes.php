

<?php if ( !wp_is_mobile() ) { ?>
	<?php /*******************************************?>
			
			START DESKTOP
			
	<?php *******************************************/?>
	<?php $n = 1; 
	$posts = get_field('services');
	
	if( $posts ): ?>

	    <?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
		   <?php setup_postdata($post); ?>
		 		
				
				<a name="<?php echo $post->post_name;?>"></a>					

  
		   
		<? if($n&1) {
			$column1 = "col-md-4 col-md-push-8";
			$column2 = "col-md-8 col-md-pull-4";				
		} else { 
			$column1 = "col-md-4";
			$column2 = "col-md-8";										
		} ?>			   
		   
			<article id="post-<?php the_ID(); ?>"  class="clearfix slide-box <? the_field('background_style') ?>">

				<div class="container">
			

	
				<?php $tri = get_field('triangle');
				if( !empty($tri) ) { ?>
					<div class="row tri">
						<div class="<? echo $column1; ?>">
						</div>
						<div class="<? echo $column2; ?>">
							<img src="<?php echo $tri['url']; ?>"  alt="<?php echo $tri['alt']; ?>" class="img-responsive triangle"/>
						</div>
					</div>
				<? } ?>
	
	

	
				<div class="row">
					<div class="<? echo $column1; ?>">
						<div class="spacer"><? //echo $n; ?>
								<div class="thumb scroll">
										<div class="thumb-wrapper">
											<?php $image = get_field('services_normal');
											if( !empty($image) ): ?>
												<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
											<?php endif; ?>
											<div class="thumb-detail">
													<?php $image = get_field('services_hover');
													 if( !empty($image) ): ?>
														<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" /> 
													<?php endif; ?>
												
											</div><? //thumb detail ?>
										</div><? //thumb wrapper ?>
									</div><? //thumb scroll ?>
							</div><? //spacer ?>
						<div class="clearfix visible-sm visible-xs"></div>
					</div><? //col1 ?>
					<div class="<? echo $column2; ?>">
						
						

						
						
									
						<h2><? the_title(); ?></h2>
						<? the_content(); ?>
						
						
						
<?php /*?>						<?php if( have_rows('bulleted_list') ) { ?>
							<ul class="list-unstyled image-list">
								<? while ( have_rows('bulleted_list') ) : the_row(); ?>
									<li>
										<? $image = get_sub_field('bullet_image'); ?>
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="bullet" /> <? the_sub_field('bullet_text'); ?>
										</li>
								<? endwhile; ?>
							</ul>
						<? } ?>		<?php */?>				
														
						
						
						
						
					</div><? //col 2 ?>
				</div><? //row ?>
				
				<div class="row slide-box-footer">
					<div class="col-md-1 col-md-offset-11 col-xs-2 col-xs-offset-5 contact">
						<div class="row">
							<div class="popover-dismiss col-xs-6" style="white-space: nowrap;" data-placement="top" data-toggle="popover" title="Call us, we can help!" data-content="480-381-1154"><i class="fa fa-phone"></i></div>
							<a href="/contact-us/" class="col-xs-6"><i class="fa fa-envelope"></i></a>
						</div>
					</div>
				</div>
				
			</div><? //container ?>
			<?php $n++; ?>

			</article>
	    <?php endforeach; ?>
	    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
	<?php endif; ?>
	<?php /*******************************************?>
			
			END DESKTOP
			
	<?php *******************************************/?>
<?php } else { ?>

	<? $posts = get_field('services');
	
	if( $posts ): ?>

	    <?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
		   <?php setup_postdata($post); ?>
		   
		<article id="post-<?php the_ID(); ?>"  class="clearfix slide-box <? the_field('background_style') ?>">
			<div class="container">
				
				<?php $tri = get_field('triangle');
				if( !empty($tri) ) { ?>
					<div class="row tri">
						<div class="<? echo $column1; ?>">
						</div>
						<div class="<? echo $column2; ?>">
							<img src="<?php echo $tri['url']; ?>"  alt="<?php echo $tri['alt']; ?>" class="img-responsive triangle"/>
						</div>
					</div>
				<? } ?>
	
					
				
				<div class="row">
					<div class="col-sm-4">
						<?php $image = get_field('services_normal');
						if( !empty($image) ): ?>
							<div class="img-slide-box"><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" /></div>
						<?php endif; ?>
					
					</div><? //col1 ?>
					
					
					<div class="col-sm-8 text-left">
						<? the_content(); ?>
					</div><? //col 2 ?>
				</div><? //row ?>
				
				<div class="row slide-box-footer">
					<div class="col-sm-2 col-sm-offset-5 col-xs-3 col-xs-offset-5 contact">
						<div class="row">
							<div class="popover-dismiss col-xs-6" style="white-space: nowrap;" data-placement="top" data-toggle="popover" title="Call us, we can help!" data-content="480-381-1154"><i class="fa fa-phone"></i></div>
							<a href="/contact-us/" class="col-xs-6"><i class="fa fa-envelope"></i></a>
						</div>
					</div>
				</div>				
				
				
				
			</div><? //container ?>
	
		</article>
		
		
	    <?php endforeach; ?>
	    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
	<?php endif; ?>



<?php } ?>



	
	
	
	
<script>
	jQuery('[data-toggle="popover"]').popover({trigger: 'hover','placement': 'top'});	
</script>	
	


