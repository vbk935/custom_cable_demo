	<div id="carousel-home" class="carousel slide carousel-fade clearfix main-carousel" data-ride="carousel">



		<!-- Indicators -->
		<ol class="carousel-indicators carousel-large">
			<? $n = 0;
			$args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
			$myposts = get_posts( $args );
			foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
			<li data-target="#carousel-home" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
			<? $n++; ?>
			<?php endforeach;  wp_reset_postdata(); ?>
		</ol>





		<div class="carousel-inner">
			<? $n2 = 1;
			 $args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
			 $myposts = get_posts( $args );
			 foreach ( $myposts as $post ) : setup_postdata( $post );
			 $the_title = get_the_title( $post->ID );
			 //echo $the_title; ?>
			<? $code = get_field('code', 'option'); ?>

			 <?php $background_color = get_field('background_color') ?>
			<?php if ($code == "tyr") { $code = $background_color;  } ?>

			<div class="clearfix <?php echo $code;?> item<? if($n2 == 1) { ?> active<? } ?>">

				<div class="slide-img-container <? if ( has_post_thumbnail() ) { echo "thumbnail-box"; } ?>">
					<? if ( has_post_thumbnail() ) { echo get_the_post_thumbnail( $post->ID, 'full', array( 'class' => 'img-responsive' ) ); } ?>
					<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-<?php echo $code;?>.png" width="82" height="312" alt="slide artwork" class="circle-half slide-art hidden-xs hidden-sm hidden"/>
					
				</div>
				<div class="custom-link"><?php $custom_excerpt = get_the_excerpt(); echo htmlspecialchars_decode($custom_excerpt); ?></div>






					<?php $header = get_field('add_a_logo');
						if ($header == 'logo') {
							$logo = get_field('slideshow_logo');
							if( !empty($logo) ) {
								$url = $logo['url'];
								$title = $logo['title'];
								$alt = $logo['alt'];
								$caption = $logo['caption'];
								$size = 'thumbnail';
								$thumb = $logo['sizes'][ $size ];
								$width = $logo['sizes'][ $size . '-width' ];
								$height = $logo['sizes'][ $size . '-height' ];
							}
						}
							$text_header = get_field('text_header');
					?>
					<?php $add_a_link = get_field('add_a_link') ?>
						<?php $link_internal = get_field('link_internal') ?>
						<?php $external = get_field('external') ?>
						<?php $anchor = get_field('anchor') ?>

					<?php $company_color = get_field('company_color') ?>




<?php /***********************			SLIDE TEXT START            */?>
				<div class="clearfix slide-txt-container hidden">
					<div class="slide-center-vert">
						<div class="slide-text-center">



							<?php if ($header == 'logo') { ?>
									<div class="logo"><img src="<?php echo $url; ?>" alt="<?php echo $alt; ?>"
										width="<?php echo $width; ?>" height="<?php echo $height; ?>"
										class="img-responsive center-block slider-logo"/>
									</div>
							<? } ?>


							<div class="text">

							<?php if ($header != 'logo') { ?>
									<div class="article-title"><?php the_title(); ?></div>
							<? } ?>

						    <?php the_excerpt(  ); ?>
							<?php $linktype = get_field('add_a_link');  ?>

							<?php if( $linktype == 'external') { ?>
								<a href="<?php the_field('link_external');?>" target="_blank" class="read-more">Learn More <i class="fa fa-play"></i></a>
							<?php }?>
							<?php if( $linktype == 'internal') { ?>
								<?php $link = get_field('link_internal');?>
								<a href="<?php echo $link; ?>" class="read-more">Learn More <i class="fa fa-play"></i></a>
							<?php } ?>
							<?php if( $linktype == 'article') { ?>
								<a href="<?php the_permalink();?>" class="read-more">Learn More <i class="fa fa-play"></i></a>
							<?php } ?>
							<?php if( $linktype == 'anchor') { ?>
								<?php $link = get_field('link_internal');?>
								<?php
									$post_object = get_field('link_anchor');
									//var_dump($post_object);
									if( $post_object ):
										$post = $post_object;
										setup_postdata( $post );
										//$postslug = $post["post_name"];
										$postslug = $post->post_name;
										wp_reset_postdata();
									endif; ?>


								<?php //var_dump($anchor); ?>
								 <?php //$pid = get_the_ID(); ?>
								<?php //the_slug($pid); ?>

								<a href="<?php echo $link; ?>#<?php echo $postslug; ?>" class="read-more">Learn More <i class="fa fa-play"></i></a>
							<?php } ?>
							<?php if( $linktype == 'oldanchor') { ?>
								<?php $link = get_field('link_internal');?>
								<?php $old = get_field('old_panel');?>
								<a href="<?php echo $link; ?>#<?php echo $old; ?>" class="read-more">Learn More <i class="fa fa-play"></i></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
<?php /***********************			SLIDE TEXT END            */?>
		</div>
		<? $n2++; ?>
		<?php endforeach; wp_reset_postdata(); ?>
</div>

		<!-- Indicators -->
		<ol class="carousel-indicators carousel-small" style="display:none;">
			<? $n = 0;
			$args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
			$myposts = get_posts( $args );
			foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
			<li data-target="#carousel-home" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
			<? $n++; ?>
			<?php endforeach;  wp_reset_postdata(); ?>
		</ol>

</div>

<?php //} ?>
