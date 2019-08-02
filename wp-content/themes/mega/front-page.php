<?php get_header(); ?>



<div class="row">
    
    <div class="main-carousel">
        <div id="carousel-home" class="carousel slide carousel-fade clearfix" data-ride="carousel">
            <? // Indicators ?>
            <ol class="carousel-indicators">
            <? $n = 0;
            $args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
            $myposts = get_posts( $args );
            foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
                <li data-target="#carousel-home" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
                <? $n++; ?>
            <?php endforeach; 
            wp_reset_postdata(); ?>
            </ol>
            <? // End Indicators ?>
            <? // Wrapper for slides ?>
            <div class="carousel-inner">
                <? $n2 = 1;
                $args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
                $myposts = get_posts( $args );
                foreach ( $myposts as $post ) : setup_postdata( $post );
                $the_title = get_the_title( $post->ID );    ?>     
                <div class="item<? if($n2 == 1) { ?> active<? } ?>">
                    <div class="col-md-9 slide-img-holder">
					<? if ( has_post_thumbnail() ) {
					   echo get_the_post_thumbnail( $post->ID, 'full', array( 'class' => 'img-responsive slide-img width-100' ) );
					} ?>				

					<? $group = get_field('group'); 
					switch ($group) {
						case "arl": ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-arl.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/> 
						<? break;
						case "dli": ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-dli.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/> 
						<? break;
						case "irx": ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-irx.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/>  
						<? break;
						case "meg": ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-meg.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/>
						<? break;
						case "sbr": ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-sbr.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/> 
						<? break;	
						case "sbd": ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-sbr.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/> 
						<? break;	
						default: ?>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-tyr.png" width="82" height="312" alt="slide artwork" class="right slide-art hidden-xs hidden-sm"/> 
						<? break;																							
					} ?>                                  
                    </div>
                    <div class="col-md-3 slide-text <?php the_field('group') ?>">
						<div class="slide-center-vert">
					<div class="slide-text-center">
				 		
					<?	
						$image = get_field('include_logo');
						// vars
						$url = $image['url'];
						$title = $image['title'];
						$alt = $image['alt'];
					
						// thumbnail
						$size = 'medium';
						$thumb = $image['sizes'][ $size ];
						$width = $image['sizes'][ $size . '-width' ];
						$height = $image['sizes'][ $size . '-height' ];				 
					?>				 
				 
				 	<?php if( get_field('include_logo') ) { ?>
						<div class="logo-container"><img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"  class="img-responsive center-block slider-logo <?php the_field('logo_shape') ?>"/></div>
					<? } else { ?>
						<h2><?php echo get_the_title( $post->ID ); ?></h2>
					<? } ?>
					<div class='text-container'>
                        <?php echo get_the_excerpt( $post->ID ); ?>
				    
				   <?php $ext = get_field('link_type'); ?>
				   <?php if( $ext == 'external') { ?>
					   <a href="<?php the_field('external');?>" target="_blank" class="read-more">Learn More <i class="fa fa-play"></i></a>
				    <?php } else { ?>
					   <a href="<?php the_field('internal');?><?php if( get_field('link_anchor') ){ ?>#<?php the_field('link_anchor');?><? } ?>" class="read-more">Learn More <i class="fa fa-play"></i></a>
				    <?php } ?>
				    </div>
				    </div>
				    </div>
                    </div>				 
                </div>		    
                <? $n2++; ?>
                <?php endforeach; 
                wp_reset_postdata();
                ?>      
            </div>
            <? // End  Wrapper for slides ?>        
            </div><? // End  carousel-home ?>
    </div>

</div>




	<div class="title">

		<div class="h2-back"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/homepage-h2-background.png" width="1170" height="125" alt="background" class="img-responsive"/></div>
		 
		<h2 class="home"><?php bloginfo( 'description' ); ?></h2>

	</div>
	<div class="clearfix"></div>
	
	
	
	   <a href="http://50.62.139.33/~megladonmfg/meg/?product=cable-2" class="btn read-more">Cable Config Tool</a> <span class="trx">
	   <a href="http://50.62.139.33/~megladonmfg/meg/?post_type=product" class="btn read-more">Shop main page</a> <span class="trx">
	
	
	<div class="">
		<a class="col-sm-4 lead-in" href="<?php the_field('box_1_link', 53); ?>" title="<?php the_field('box_1_title', 53); ?>">
			<?php $image = get_field('box_1_photo', 53);
            if( !empty($image) ): ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
            <?php endif; ?>            
			<h3><?php the_field('box_1_title', 53); ?></h3>
			<p><?php the_field('box_1_text', 53); ?></p>
		</a>
		<a class="col-sm-4 lead-in" href="<?php the_field('box_2_link', 53); ?>" title="<?php the_field('box_2_title', 53); ?>">
			<?php $image = get_field('box_2_photo', 53);
            if( !empty($image) ): ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
            <?php endif; ?> 
			<h3><?php the_field('box_2_title', 53); ?></h3>
			<p><?php the_field('box_2_text', 53); ?></p>
		</a>
		<a class="col-sm-4 lead-in" href="<?php the_field('box_3_link', 53); ?>" title="<?php the_field('box_3_title', 53); ?>">
			<?php $image = get_field('box_3_photo', 53);
            if( !empty($image) ): ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
            <?php endif; ?> 
			<h3><?php the_field('box_3_title', 53); ?></h3>
			<p><?php the_field('box_3_text', 53); ?></p>
		</a>
	</div>		


<?php get_footer(); ?>

