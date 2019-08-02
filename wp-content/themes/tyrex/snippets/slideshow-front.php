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


					<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slideshow-circle-meg.png" width="82" height="312" alt="slide artwork" class="circle-half slide-art hidden-xs hidden-sm"/> 
			                               
                    </div>
                    <div class="col-md-3 slide-text sbd">
					<div class="slide-center-vert">
						<div class="slide-text-center">
							<div class="article-title"><?php echo get_the_title( $post->ID ); ?></div>
							<div class='text-container'>
							   <?php echo get_the_excerpt( $post->ID ); ?>
							   <a href="<?php the_permalink(); ?>" class="read-more">Learn More <i class="fa fa-play"></i></a>
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
