<?php if( get_field('our_culture_slideshow') ): ?>
<?php $n = 0; ?>
<?php $n2 = 0; ?>

<div class="slideshow-featured">
    <div id="slideshow-01" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
        <ol class="carousel-indicators">
		<? while ( have_rows('our_culture_slideshow') ) : the_row(); ?>
            <li data-target="#slideshow-01" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
            <? $n++; ?>
        <?php endwhile; ?>
        </ol>
    
      <!-- Wrapper for slides -->
      <div class="carousel-inner">
		<? while ( have_rows('our_culture_slideshow') ) : the_row(); ?>
            <div class="item <? if($n2 == 0) { ?> active<? } ?>">
					<?php 
                    $image = get_sub_field('slide_image');
                    if( !empty($image) ): ?>
					<img src="<?php echo $image['url']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive"/>
                    <?php endif; ?>
                    
<?php /*?>                <div class="slide-text">
                	<h2><?php the_sub_field('slides_mini_title'); ?></h2>
                	<?php the_sub_field('slides_mini_text'); ?>
                </div>	<?php */?>			 
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
