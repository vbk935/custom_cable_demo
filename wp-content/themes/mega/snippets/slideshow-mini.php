<?php if( get_field('slides_repeater') ): ?>
<?php $n = 0; ?>
<?php $n2 = 0; ?>
<div class="slideshow-mini">
    <div id="slideshow-01" class="carousel slide " data-ride="carousel">
<?php /*?>      <!-- Indicators -->
        <ol class="carousel-indicators">
		<? while ( have_rows('slides_repeater') ) : the_row(); ?>
            <li data-target="#slideshow-01" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
            <? $n++; ?>
        <?php endwhile; ?>
        </ol><?php */?>
    
      <!-- Wrapper for slides -->
      <div class="carousel-inner">
		<? while ( have_rows('slides_repeater') ) : the_row(); ?>
            <div class="item<? if($n2 == 1) { ?> active<? } ?>">
                <div class="slide-img-holder">
					<?php 
                    $image = get_sub_field('slides_mini_image');
                    if( !empty($image) ): 
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
                    	<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="img-responsive" />
                    <?php endif; ?>
                    
                </div>
                <div class="slide-text">
                	<h2><?php the_sub_field('slides_mini_title'); ?></h2>
                	<?php the_sub_field('slides_mini_text'); ?>
                </div>				 
            </div>		    
            <? $n2++; ?>
        <?php endwhile; ?>
      </div>
   
<?php /*?>      <!-- Controls -->
      <a class="left carousel-control" href="#slideshow-01" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
      </a>
      <a class="right carousel-control" href="#slideshow-01" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
      </a><?php */?>
    </div>
</div>    
<?php endif; ?>
