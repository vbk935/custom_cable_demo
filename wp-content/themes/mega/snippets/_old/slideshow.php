<div class="news-carousel">
        <div id="carousel-home" class="carousel slide carousel-fade" data-ride="carousel">
        <? // Indicators ?>
        <ol class="carousel-indicators">
			<?
            $n = 0;
            $args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
            $myposts = get_posts( $args );
            foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
            <li data-target="#carousel-home" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
            <? $n++; ?>
            <?php endforeach; 
            wp_reset_postdata();
            ?>
        </ol>
        <? // End Indicators ?>
       <? // Wrapper for slides ?>
        <div class="carousel-inner">
			<?
            $n2 = 1;
            $args = array( 'posts_per_page' => -1, 'post_type'=> 'article' );
            $myposts = get_posts( $args );
            foreach ( $myposts as $post ) : setup_postdata( $post );
            $the_title = get_the_title( $post->ID );    ?>     
            <div class="item<? if($n2 == 1) { ?> active<? } ?>">
                <div class="slide-img-holder">
					<? if ( has_post_thumbnail() ) {
                        echo get_the_post_thumbnail( $post->ID, 'full', array( 'class' => 'img-responsive slide-img width-100' ) );
                    } ?>  
                </div>
                <div class="slide-text">
                	<h2><?php echo get_the_title( $post->ID ); ?></h2>
                	<?php echo get_the_excerpt( $post->ID ); ?>
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