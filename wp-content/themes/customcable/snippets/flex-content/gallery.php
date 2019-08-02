<?php $images = get_sub_field('gallery');
if( $images ): ?>
<div class="row">
    <div class="gallery" style="background: <?php the_sub_field('background_color');?>">
        <?php foreach( $images as $image ): ?>
	   		<div class="col-xs-6 col-md-3">
                <a href="<?php echo $image['url']; ?>" class="fancybox thumbnail" rel="prettyPhoto[pp_gal]" title="<?php echo $image['caption']; ?>">
                     <img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php echo $image['alt']; ?>" />
				 <div id="single-post-container"></div>
                </a>
			</div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
